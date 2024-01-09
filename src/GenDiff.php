<?php

namespace Differ;

use Exception;

use function Functional\flatten;
use function Differ\stylish;
use function Differ\plain;

function genDiff(string $path1, string $path2, $formatName): string
{
    $path1 = getFullPath($path1);
    $path2 = getFullPath($path2);

    if (!file_exists($path1) || (!file_exists($path2))) {
        throw new \Exception('File not exist');
    }
    $file1 = parseFile($path1);
    $file2 = parseFile($path2);

    $diff = iter($file1, $file2);
    $formatFunc = 'Differ\\' . $formatName;
    return "{$formatFunc($diff)}";
}

function iter(array $arr1, array $arr2)
{
    $keys = array_merge(array_keys($arr1), array_keys($arr2));
    sort($keys);
    $keys = array_unique($keys);

    return array_reduce(
        $keys,
        function ($acc, $key) use ($arr1, $arr2) {
            $value1 = $arr1[$key] ?? null;
            $value2 = $arr2[$key] ?? null;

            if (is_array($value1) && is_array($value2)) {
                $acc = getAcc($value1, $value2, $key, ' ', $acc);
            } elseif (!array_key_exists($key, $arr1) || $value1 === $value2) {
                $z = ($value1 === $value2) ? 'u=' : 'a';
                $acc = getAcc($value2, $value2, $key, $z, $acc);
            } elseif (!array_key_exists($key, $arr2) || $value1 === $value2) {
                $z = ($value1 === $value2) ? 'u=' : 'r';
                $acc = getAcc($value1, $value1, $key, $z, $acc);
            } elseif ($value1 !== $value2) {
                $acc = getAcc($value1, $value1, $key, 'u-', $acc);
                $acc = getAcc($value2, $value2, $key, 'u+', $acc);
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
            'val' => iter($value1, $value2)
        ];
//        $acc[] = ['val' => iter($value1, $value2)];
    } else {
        $acc[] = [
            'z' => $z,
            'key' => $key,
//            'val' => toString($value1)
            'val' => $value1
        ];
    }

    return $acc;
}

function toString(mixed $val): string
{
    return trim(var_export($val, true), "'");
}
