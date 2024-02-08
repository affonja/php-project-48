<?php

namespace Differ\Differ;

use Exception;
use Symfony\Component\Yaml\Yaml;

use function Differ\FilePath\getExtension;
use function Differ\FilePath\getFullPath;
use function Differ\Formatters\Formatters\getFormatter;
use function Differ\Parsers\parseFile;

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

    $content = file_get_contents($full_path);
    if ($content === false) {
        throw new Exception('Unknown file');
    }

    $type = getExtension($path);

    return parseFile($content, $type);
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
                $value = getValue($value1, $value2);
                $new_acc = getDiffIter($value, $key, 'nested');
            } elseif (!array_key_exists($key, $arr2)) {
                $value = is_array($value1) ? $value1 : getValue($value1, $value1);
                $new_acc = getDiffIter($value, $key, 'rmv');
            } elseif (!array_key_exists($key, $arr1)) {
                $value = is_array($value2) ? $value2 : getValue($value2, $value2);
                $new_acc = getDiffIter($value, $key, 'add');
            } elseif ($value1 === $value2) {
                $value = getValue($value2, $value2);
                $new_acc = getDiffIter($value, $key, 'upd=');
            } else {
                $value_add = is_array($value1) ? $value1 : getValue($value1, $value1);
                $value_rmv = is_array($value2) ? $value2 : getValue($value2, $value2);
                $new_acc = array_merge(
                    getDiffIter($value_add, $key, 'upd-'),
                    getDiffIter($value_rmv, $key, 'upd+')
                );
            }

            return array_merge($acc, $new_acc);
        },
        []
    );
}

function getValue(mixed $value1, mixed $value2): mixed
{
    return (is_array($value1)) ? iter($value1, (array)$value2) : $value1;
}

function getDiffIter(mixed $value, string $key, string $action): array
{
    if ($action === 'nested') {
        return [
            [
                'action' => $action,
                'key' => $key,
                'children' => $value
            ]
        ];
    }
    return [
        [
            'action' => $action,
            'key' => $key,
            'value' => $value
        ]
    ];
}
