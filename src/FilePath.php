<?php

namespace Differ\Differ;

define("ROOT_DIR", __DIR__ . '/../');
define("FIXTURES_DIR", ROOT_DIR . '/tests/fixtures/');
function getFullPath(string $path): string
{
    $is_absolute_path = getTypePath($path);
    $dirname = pathinfo($path)['dirname'];
    print_r("is_absolute_path - $is_absolute_path \n");
    print_r("dirname - $dirname \n");
    if (!$is_absolute_path) {
        $path = ($dirname === '.') ? FIXTURES_DIR . $path : ROOT_DIR . '/' . $path;
        print_r("abspath $path\n");
        return realpath($path);
    }
    if (stripos($dirname, realpath(ROOT_DIR)) === false && ($path[0] === '/' || $path[0] === '\\')) {
        $path = ROOT_DIR . $path;
        print_r("relpath $path\n");
        return realpath($path);
    }

    return realpath($path);
}

function getTypePath(string $path): bool
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
