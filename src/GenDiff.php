<?php

namespace Differ\Differ;

use Exception;

function genDiff(string $path1, string $path2, $formatName = 'stylish'): string
{
    $path1 = getFullPath($path1);
    $path2 = getFullPath($path2);

    if (!file_exists($path1) || (!file_exists($path2))) {
        throw new Exception("File $path1 or $path2 not exist");
    }
    $file1 = parseFile($path1);
    $file2 = parseFile($path2);

    $diff = iter($file1, $file2);
    return getFormatter($formatName, $diff);
}

function iter(array $arr1, array $arr2): array
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
                $acc = getDiffIter($value1, $value2, $key, ' ', $acc);
            } elseif (!array_key_exists($key, $arr1) || $value1 === $value2) {
                $action = ($value1 === $value2) ? 'upd=' : 'add';
                $acc = getDiffIter($value2, $value2, $key, $action, $acc);
            } elseif (!array_key_exists($key, $arr2)) {
                $acc = getDiffIter($value1, $value1, $key, 'rmv', $acc);
            } else {
                $acc = getDiffIter($value1, $value1, $key, 'upd-', $acc);
                $acc = getDiffIter($value2, $value2, $key, 'upd+', $acc);
            }

            return $acc;
        },
        []
    );
}

function getDiffIter(mixed $value1, mixed $value2, string $key, string $act, array $acc): array
{
    if (is_array($value1)) {
        $acc[] = [
            'act' => $act,
            'key' => $key,
            'value' => iter($value1, $value2)
        ];
    } else {
        $acc[] = [
            'act' => $act,
            'key' => $key,
            'value' => $value1
        ];
    }

    return $acc;
}
