<?php

namespace Differ;

//define("ROOT_DIR", $_SERVER["PWD"]);
define("ROOT_DIR", __DIR__ . '/../');
define("FIXTURES_DIR", ROOT_DIR . '/tests/fixtures/');
function getFullPath(string $path): string
{
    $is_absolute_path = getTypePath($path);
    if ($is_absolute_path) {
        if (stripos(pathinfo($path)['dirname'], realpath(ROOT_DIR)) === false) {
            if ($path[0] === '/' || $path[0] === '\\') {
                $path = ROOT_DIR . $path;
            }
        }
    } else {
        if (pathinfo($path)['dirname'] === '.') {
            $path = FIXTURES_DIR . $path;
        } else {
            $path = ROOT_DIR . '/' . $path;
        }
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
