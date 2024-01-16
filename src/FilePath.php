<?php

namespace Differ\Differ;

define("ROOT_DIR", __DIR__ . '/../');
define("FIXTURES_DIR", ROOT_DIR . 'tests/fixtures/');

function getFullPath(string $path): string|bool
{
    $is_absolute_path = getTypePath($path);

    if ($is_absolute_path) {
        return realpath($path) ?: realpath(ROOT_DIR . $path);
    }
    return realpath(ROOT_DIR . $path) ?: realpath(FIXTURES_DIR . $path);
}

function getTypePath(string $path): bool|int
{
    $a = strspn($path, '/\\', 0, 1);
    $b = strlen($path) > 3 && ctype_alpha($path[0]);
    $c = substr($path, 1, 1) === ':';
    $d = strspn($path, '/\\', 2, 1);

    return $a || ($b && $c && $d);
}

function getExtension(string $file): string
{
    return pathinfo($file)['extension'] ?? '';
}
