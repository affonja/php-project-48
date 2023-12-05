<?php

namespace Differ;

use function Functional\flatten;

function genDiff(string $path1, string $path2): string
{
    $path1 = getFullPath($path1);
    $path2 = getFullPath($path2);

    if (!file_exists($path1) || (!file_exists($path2))) {
        throw new \Exception('File not exist');
    }
    $file1 = (array)json_decode(file_get_contents($path1));
    $file2 = (array)json_decode(file_get_contents($path2));

    $keys = array_merge(array_keys($file1), array_keys($file2));
    sort($keys);
    $keys = array_unique($keys);


    $diff = array_map(
        function ($key) use ($file2, $file1) {
            if (array_key_exists($key, $file1) && !array_key_exists($key, $file2)) {
                $result[] = "- $key: " . boolToString($file1[$key]);
            } elseif (!array_key_exists($key, $file1) && array_key_exists($key, $file2)) {
                $result[] = "+ $key: " . boolToString($file2[$key]);
            } elseif (array_key_exists($key, $file1) && array_key_exists($key, $file2)) {
                if ($file1[$key] === $file2[$key]) {
                    $result[] = "  $key: " . boolToString($file1[$key]);
                } else {
                    $result[] = "- $key: " . boolToString($file1[$key]);
                    $result[] = "+ $key: " . boolToString($file2[$key]);
                }
            }
            return $result ?? [];
        },
        $keys
    );
    $diff = flatten($diff);

    return implode(PHP_EOL, $diff);
}


function boolToString($val): string|int
{
    return is_bool($val) ? ($val === true ? 'true' : 'false') : $val;
}
