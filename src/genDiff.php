<?php

namespace Differ;

use function Functional\flatten;

function genDiff($path1, $path2)
{
    if (!file_exists($path1) && (!file_exists($path2))) {
        throw new \Exception('Unknown file');
    }
    $file1 = (array)json_decode(file_get_contents($path1));
    $file2 = (array)json_decode(file_get_contents($path2));

    $keys = array_merge(array_keys($file1), array_keys($file2));
    sort($keys);
    $keys = array_unique($keys);

    $diff = array_map(
        function ($key) use ($file2, $file1) {
            if (array_key_exists($key, $file1) && !array_key_exists($key, $file2)) {
                $result[] = "- $key: $file1[$key]";
            } elseif (!array_key_exists($key, $file1) && array_key_exists($key, $file2)) {
                $result[] = "+ $key: $file2[$key]";
            } elseif (array_key_exists($key, $file1) && array_key_exists($key, $file2)) {
                if ($file1[$key] === $file2[$key]) {
                    $result[] = "  $key: $file1[$key]";
                } else {
                    $result[] = "- $key: $file1[$key]";
                    $result[] = "+ $key: $file2[$key]";
                }
            }
            return $result;
        }
        , $keys
    );
    $diff = flatten($diff);

//    return flatten($diff);
    return implode(PHP_EOL, $diff);
}

function validatePath($path)
{
    
}
