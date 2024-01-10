<?php

namespace Differ;

use Exception;

function getFormatter(string $formatName): callable
{
    if (!function_exists('Differ\\' . $formatName)) {
        throw new \Exception('Formatter not exist');
    }
    return 'Differ\\' . $formatName;
}
