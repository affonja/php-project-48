<?php

namespace Differ\Differ;

const ROOT_DIR = __DIR__ . '/../';
const FIXTURES_DIR = ROOT_DIR . 'tests/fixtures/';

function getFullPath(string $path): string|bool
{
    $is_absolute_path = getTypePath($path);

    return $is_absolute_path
        ? (realpath($path) !== false ? realpath($path) : realpath(ROOT_DIR . $path))
        : (realpath(ROOT_DIR . $path) !== false ? realpath(ROOT_DIR . $path) : realpath(FIXTURES_DIR . $path));
}

function getTypePath(string $path): bool
{
    $a = (bool)strspn($path, '/\\', 0, 1);
    $b = (bool)strlen($path) > 3 && ctype_alpha($path[0]);
    $c = substr($path, 1, 1) === ':';
    $d = (bool)strspn($path, '/\\', 2, 1);

    return $a || ($b && $c && $d);
}

function getExtension(string $file): string
{
    return pathinfo($file)['extension'] ?? '';
}
