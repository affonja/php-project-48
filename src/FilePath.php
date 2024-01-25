<?php

namespace App\FilePath;

use Symfony\Component\Filesystem\Filesystem;

const ROOT_DIR = __DIR__ . '/../';

function getFullPath(string $path): string|bool
{
    $filesystem = new Filesystem();
    $is_absolute_path = $filesystem->isAbsolutePath($path);

    return $is_absolute_path ? realpath($path) : realpath(ROOT_DIR . $path);
}

function getExtension(string $file): string
{
    return pathinfo($file)['extension'] ?? '';
}
