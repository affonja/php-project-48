<?php

namespace Differ;

use Exception;

function getFormatter(string $formatName, array $diff): string
{
    if (!function_exists('Differ\\' . $formatName)) {
        throw new Exception('Formatter not exist');
    }
    $formatFunc = "Differ\\$formatName";

    return match ($formatName) {
        'stylish' => "{\n{$formatFunc($diff)}}",
        'plain' => "{$formatFunc($diff)}",
        'json' => "[{$formatFunc($diff)}]",
        default => '',
    };
}

function toString(mixed $val): string
{
    return trim(var_export($val, true), "'");
}