<?php

namespace Differ\Differ;

const ROOT_DIR = __DIR__ . '/../';
const FIXTURES_DIR = ROOT_DIR . 'tests/fixtures/';

function getFullPath(string $path): string|bool
{
    $is_absolute_path = isAbsolutePath($path);

    return $is_absolute_path
        ? (realpath($path) !== false ? realpath($path) : realpath(ROOT_DIR . $path))
        : (realpath(ROOT_DIR . $path) !== false ? realpath(ROOT_DIR . $path) : realpath(FIXTURES_DIR . $path));
}

function isAbsolutePath(string $path): bool
{
    return (bool)strspn($path, '/\\', 0, 1)
        || (mb_strlen($path) > 3 && ctype_alpha($path[0])
            && mb_substr($path, 1, 1) === ':'
            && (bool)strspn($path, '/\\', 2, 1)
        )
        || null !== parse_url($path, PHP_URL_SCHEME);
}

function getExtension(string $file): string
{
    return pathinfo($file)['extension'] ?? '';
}
