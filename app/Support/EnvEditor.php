<?php

namespace App\Support;

class EnvEditor
{
    public function __construct(
        private readonly string $filePath,
    ) {
    }

    public function all(): array
    {
        if (!is_file($this->filePath)) {
            return [];
        }

        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES) ?: [];
        $pairs = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $position = strpos($line, '=');
            if ($position === false) {
                continue;
            }

            $key = trim(substr($line, 0, $position));
            $value = trim(substr($line, $position + 1));

            if ($key === '' || !preg_match('/^[A-Z0-9_]+$/', $key)) {
                continue;
            }

            $pairs[$key] = $this->unquote($value);
        }

        ksort($pairs);

        return $pairs;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $all = $this->all();

        return $all[$key] ?? $default;
    }

    public function setMany(array $pairs): void
    {
        if (!is_file($this->filePath)) {
            return;
        }

        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES) ?: [];
        $normalized = [];

        foreach ($pairs as $key => $value) {
            if (!is_string($key) || !preg_match('/^[A-Z0-9_]+$/', $key)) {
                continue;
            }

            $normalized[$key] = $value === null ? '' : (string) $value;
        }

        if (empty($normalized)) {
            return;
        }

        $found = [];

        foreach ($lines as &$line) {
            if (!is_string($line)) {
                continue;
            }

            $position = strpos($line, '=');
            if ($position === false) {
                continue;
            }

            $key = trim(substr($line, 0, $position));
            if (!array_key_exists($key, $normalized)) {
                continue;
            }

            $line = $key . '=' . $this->quote($normalized[$key]);
            $found[$key] = true;
        }
        unset($line);

        foreach ($normalized as $key => $value) {
            if (!isset($found[$key])) {
                $lines[] = $key . '=' . $this->quote($value);
            }
        }

        file_put_contents($this->filePath, implode(PHP_EOL, $lines) . PHP_EOL);
    }

    private function unquote(string $value): string
    {
        if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
            $trimmed = substr($value, 1, -1);

            return str_replace(['\\"', '\\\\'], ['"', '\\'], $trimmed);
        }

        return $value;
    }

    private function quote(string $value): string
    {
        if ($value === '') {
            return '""';
        }

        if (preg_match('/\s|#|"|=/', $value)) {
            return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $value) . '"';
        }

        return $value;
    }
}
