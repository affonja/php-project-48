<?php

namespace App\Gendiff;

use Exception;

use function App\FilePath\getFullPath;
use function App\Formatters\getFormatter;
use function App\Parsers\parseFile;

function genDiff(string $path1, string $path2, string $formatName = 'stylish'): string
{
    $file1 = getParsedData($path1);
    $file2 = getParsedData($path2);

    $diff = iter($file1, $file2);
    return getFormatter($formatName, $diff);
}

function getParsedData(string $path): array
{
    $full_path = is_bool(getFullPath($path)) ? '' : getFullPath($path);

    if (!file_exists($full_path)) {
        throw new Exception("File not exist");
    }
    return parseFile($full_path);
}

function iter(array $arr1, array $arr2): array
{
    $keys = array_merge(array_keys($arr1), array_keys($arr2));
    $sorted_keys = \Functional\sort($keys, fn($left, $right) => strcmp($left, $right));
    $unical_keys = array_unique($sorted_keys);

    return array_reduce(
        $unical_keys,
        function ($acc, $key) use ($arr1, $arr2) {
            $value1 = $arr1[$key] ?? null;
            $value2 = $arr2[$key] ?? null;

            if (is_array($value1) && is_array($value2)) {
                $new_acc = getDiffIter($value1, $value2, $key, ' ');
            } elseif (!array_key_exists($key, $arr1) || $value1 === $value2) {
                $action = ($value1 === $value2) ? 'upd=' : 'add';
                $new_acc = getDiffIter($value2, $value2, $key, $action);
            } elseif (!array_key_exists($key, $arr2)) {
                $new_acc = getDiffIter($value1, $value1, $key, 'rmv');
            } else {
                $new_acc = array_merge(
                    getDiffIter($value1, $value1, $key, 'upd-'),
                    getDiffIter($value2, $value2, $key, 'upd+')
                );
            }

            return array_merge($acc, $new_acc);
        },
        []
    );
}

function getDiffIter(mixed $value1, mixed $value2, string $key, string $act): array
{
    if (is_array($value1)) {
        $new_acc = [
            [
                'act' => $act,
                'key' => $key,
                'value' => iter($value1, (array)$value2)
            ]
        ];
    } else {
        $new_acc = [
            [
                'act' => $act,
                'key' => $key,
                'value' => $value1
            ]
        ];
    }

    return $new_acc;
}
