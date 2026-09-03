<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AdminMediaRepository;
use InvalidArgumentException;

final class AdminMediaService
{
    private const IMAGE_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    public function __construct(
        private readonly AdminMediaRepository $repository,
        private readonly int $maxBytes = 10485760
    ) {
    }

    public function list(int $page, string $search): array
    {
        $result = $this->repository->paginate(max(1, $page), 24, trim($search));

        return [
            ...$result,
            'page' => max(1, $page),
            'per_page' => 24,
            'pages' => max(1, (int) ceil($result['total'] / 24)),
            'search' => trim($search),
        ];
    }

    public function options(): array
    {
        return $this->repository->options();
    }

    public function upload(array $file, string $altText = ''): int
    {
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($error !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException($this->uploadError($error));
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');

        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            throw new InvalidArgumentException('The uploaded file could not be verified.');
        }

        $size = (int) ($file['size'] ?? 0);

        if ($size < 1 || $size > $this->maxBytes) {
            throw new InvalidArgumentException('Images must be smaller than ' . (int) ceil($this->maxBytes / 1048576) . ' MB.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($tmpName);

        if (!isset(self::IMAGE_MIMES[$mime])) {
            throw new InvalidArgumentException('Only JPG, PNG, WEBP and GIF images are allowed.');
        }

        $dimensions = @getimagesize($tmpName);

        if ($dimensions === false) {
            throw new InvalidArgumentException('The uploaded file is not a valid image.');
        }

        $extension = self::IMAGE_MIMES[$mime];
        $folder = date('Y/m');
        $relativeDirectory = '/uploads/media/' . $folder;
        $directory = BASE_PATH . '/public' . $relativeDirectory;

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new InvalidArgumentException('The media storage directory could not be created.');
        }

        $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $directory . '/' . $storedName;

        if (!move_uploaded_file($tmpName, $destination)) {
            throw new InvalidArgumentException('The image could not be stored.');
        }

        try {
            return $this->repository->insert([
                'disk' => 'public',
                'path' => $relativeDirectory . '/' . $storedName,
                'filename' => $this->cleanFilename((string) ($file['name'] ?? $storedName)),
                'mime_type' => $mime,
                'size_bytes' => $size,
                'width' => (int) $dimensions[0],
                'height' => (int) $dimensions[1],
                'alt_text' => trim($altText) !== '' ? trim($altText) : null,
            ]);
        } catch (\Throwable $exception) {
            @unlink($destination);
            throw $exception;
        }
    }

    public function delete(int $id): void
    {
        $media = $this->repository->find($id);

        if ($media === null) {
            throw new InvalidArgumentException('Media item not found.');
        }

        $usage = $this->repository->usages($id);

        if ($usage !== []) {
            $names = array_map(
                static fn (array $item): string => $item['table'] . ' (' . $item['count'] . ')',
                $usage
            );

            throw new InvalidArgumentException('This media item is still in use by: ' . implode(', ', $names) . '.');
        }

        $this->repository->delete($id);

        $path = BASE_PATH . '/public' . (string) $media['path'];

        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function cleanFilename(string $name): string
    {
        $name = trim(basename(str_replace('\\', '/', $name)));

        return $name !== '' ? mb_substr($name, 0, 255) : 'image';
    }

    private function uploadError(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The image is larger than the allowed upload size.',
            UPLOAD_ERR_PARTIAL => 'The image upload was incomplete. Please try again.',
            UPLOAD_ERR_NO_FILE => 'Choose an image to upload.',
            default => 'The image could not be uploaded.',
        };
    }
}
