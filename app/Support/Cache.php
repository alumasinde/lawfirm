<?php
declare(strict_types=1);
namespace App\Support;

final class Cache
{
    private const DIRECTORY = '/storage/cache';

    public static function remember(string $key, int $ttlSeconds, callable $resolver): mixed
    {
        $ttlSeconds = max(1, $ttlSeconds);
        $file = self::file($key);
        $cached = self::read($file);

        if ($cached !== null && $cached['expires_at'] >= time()) {
            return $cached['value'];
        }

        $value = $resolver();
        self::write($file, ['key' => $key, 'expires_at' => time() + $ttlSeconds, 'value' => $value]);

        return $value;
    }

    public static function forgetPrefix(string $prefix): void
    {
        foreach (glob(self::directory() . '/*.cache') ?: [] as $file) {
            $payload = self::read($file, false);

            if (is_array($payload) && str_starts_with((string) ($payload['key'] ?? ''), $prefix)) {
                @unlink($file);
            }
        }
    }

    private static function read(string $file, bool $validate = true): ?array
    {
        if (!is_file($file)) return null;
        $raw = @file_get_contents($file);
        if ($raw === false) return null;

        try {
            $payload = unserialize($raw, ['allowed_classes' => false]);
        } catch (\Throwable) {
            return null;
        }

        if (!is_array($payload)) return null;
        if ($validate && (!isset($payload['expires_at']) || !array_key_exists('value', $payload))) return null;

        return $payload;
    }

    private static function write(string $file, array $payload): void
    {
        $directory = self::directory();

        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) return;

        try {
            $temporary = $file . '.' . bin2hex(random_bytes(4)) . '.tmp';
        } catch (\Throwable) {
            $temporary = $file . '.' . uniqid('', true) . '.tmp';
        }

        if (@file_put_contents($temporary, serialize($payload), LOCK_EX) === false) return;
        @rename($temporary, $file);
    }

    private static function directory(): string
    {
        return BASE_PATH . self::DIRECTORY;
    }

    private static function file(string $key): string
    {
        return self::directory() . '/' . hash('sha256', $key) . '.cache';
    }
}
