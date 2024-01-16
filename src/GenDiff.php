<?php

namespace Differ\Differ;

use Exception;

function genDiff(string $path1, string $path2, $formatName = 'stylish'): string
{
    $full_path1 = getFullPath($path1);
    $full_path2 = getFullPath($path2);

    if (!file_exists($full_path1) || (!file_exists($full_path2))) {
        throw new Exception("File not exist");
    }
    $file1 = parseFile($full_path1);
    $file2 = parseFile($full_path2);

    $diff = iter($file1, $file2);
    return getFormatter($formatName, $diff);
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
                'value' => iter($value1, $value2)
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
