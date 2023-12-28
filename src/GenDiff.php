<?php

namespace Differ;

use Exception;

use function Functional\flatten;

function genDiff(string $path1, string $path2, $stylish = 'Differ\formatter'): string
{
    $path1 = getFullPath($path1);
    $path2 = getFullPath($path2);

    if (!file_exists($path1) || (!file_exists($path2))) {
        throw new \Exception('File not exist');
    }
    $file1 = parseFile($path1);
    $file2 = parseFile($path2);

    $diff = iter($file1, $file2);
    return "{" . PHP_EOL . "{$stylish($diff)}}";
}

function iter(array $arr1, array $arr2)
{
    $keys = array_merge(array_keys($arr1), array_keys($arr2));
    sort($keys);
    $keys = array_unique($keys);

    return array_reduce(
        $keys,
        function ($acc, $key) use ($arr1, $arr2) {
            if (array_key_exists($key, $arr1) && !array_key_exists($key, $arr2)) {
                $acc = getAcc($arr1[$key], $arr1[$key], $key, '-', $acc);
            }
            if (!array_key_exists($key, $arr1) && array_key_exists($key, $arr2)) {
                $acc = getAcc($arr2[$key], $arr2[$key], $key, '+', $acc);
            }

            if (array_key_exists($key, $arr1) && array_key_exists($key, $arr2)) {
                if (is_array($arr1[$key]) && is_array($arr2[$key])) {
                    $acc = getAcc($arr1[$key], $arr2[$key], $key, ' ', $acc);
                } else {
                    if ($arr1[$key] === $arr2[$key]) {
                        $acc = getAcc($arr1[$key], $arr1[$key], $key, ' ', $acc);
                    } else {
                        $acc = getAcc($arr1[$key], $arr1[$key], $key, '-', $acc);
                        $acc = getAcc($arr2[$key], $arr2[$key], $key, '+', $acc);
                    }
                }
            }

            return $acc;
        },
        []
    );
}

function getAcc(mixed $value1, mixed $value2, string $key, string $z, array $acc): array
{
    if (is_array($value1)) {
        $acc[] = [
            'z' => $z,
            'key' => $key,
            'val' => '{'
        ];
        $acc[] = ['val' => iter($value1, $value2)];
    } else {
        $acc[] = [
            'z' => $z,
            'key' => $key,
            'val' => toString($value1)
        ];
    }

    return $acc;
}

function toString(mixed $val): string
{
    return trim(var_export($val, true), "'");
}
