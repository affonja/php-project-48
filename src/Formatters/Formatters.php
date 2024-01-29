<?php

namespace Differ\Formatters\Formatters;

use Exception;

function getFormatter(string $formatName, array $diff): string
{
    $formatFunc = "Differ\\Formatters\\$formatName\\$formatName";
    if (!function_exists($formatFunc)) {
        throw new Exception('Formatter not valid');
    }

    return $formatFunc($diff);
}

function toString(mixed $val): string
{
    return trim(var_export($val, true), "'");
}
