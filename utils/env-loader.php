<?php

namespace App\Controller;

class DotEnvEnvironment
{
    public function load(string $path): void
    {
        $file = $path . '/.env';
        if (!file_exists($file)) {
            throw new \RuntimeException("Fichier .env non trouvé à : $file");
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, '#')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);

            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

(new DotEnvEnvironment)->load(__DIR__);