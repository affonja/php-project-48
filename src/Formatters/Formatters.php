<?php

namespace Differ;

use Exception;

function getFormatter(string $formatName, $diff)
{
    if (!function_exists('Differ\\' . $formatName)) {
        throw new \Exception('Formatter not exist');
    }
    $formatFunc = "Differ\\$formatName";
    if ($formatName === 'stylish') {
        return "{\n{$formatFunc($diff)}}";
    }
    if ($formatName === 'plain') {
        return "{$formatFunc($diff)}";
    }
    if ($formatName === 'json') {
        return "[{$formatFunc($diff)}]";
    }
}
