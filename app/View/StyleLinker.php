<?php

declare(strict_types=1);

namespace App\View;

final class StyleLinker
{
    private array $config;

    public function __construct()
    {
        $path = BASE_PATH . '/config/design/active.ini';

        $config = parse_ini_file($path, true, INI_SCANNER_TYPED);

        if ($config === false) {
            throw new \RuntimeException('Unable to load design configuration.');
        }

        $this->config = $config;
    }

    public function render(): string
    {
        $version = $this->version();
        $output = $this->variables();

        foreach ($this->styles($version) as $style) {
            $href = '/css/' . rawurlencode($version) . '/' . rawurlencode($style);
            $output .= '<link rel="stylesheet" href="' . $href . '">';
        }

        return $output;
    }

    private function version(): string
    {
        $version = (string) ($this->config['application']['style_version'] ?? 'v1');

        if (!preg_match('/^v[0-9]+$/', $version)) {
            return 'v1';
        }

        if (!is_dir(BASE_PATH . '/public/css/' . $version)) {
            return 'v1';
        }

        return $version;
    }

    private function variables(): string
    {
        $variables = [];

        foreach (['brand', 'text', 'surface', 'layout', 'border', 'spacing'] as $section) {
            foreach ($this->config[$section] ?? [] as $key => $value) {
                $name = '--' . $section . '-' . str_replace('_', '-', $key);
                $variables[] = $name . ':' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
            }
        }

        return '<style>:root{' . implode(';', $variables) . ';}</style>';
    }

    private function styles(string $version): array
    {
        $manifest = BASE_PATH . '/public/css/' . $version . '/manifest.php';

        if (!is_file($manifest)) {
            return [];
        }

        $styles = require $manifest;

        return is_array($styles) ? $styles : [];
    }
}
