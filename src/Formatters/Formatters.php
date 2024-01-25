<?php

namespace Differ\Formatters;

use Exception;

function getFormatter(string $formatName, array $diff): string
{
    $formatFunc = "Differ\\Formatters\\$formatName";
    if (!function_exists($formatFunc)) {
        throw new Exception('Formatter not valid');
    }

    return match ($formatName) {
        'stylish' => "{\n" . implode($formatFunc($diff)) . "}",
        'plain' => trim(implode($formatFunc($diff))),
        'json' => "[" . implode($formatFunc($diff)) . "]",
        default => '',
    };
}

function toString(mixed $val): string
{
    return trim(var_export($val, true), "'");
}
