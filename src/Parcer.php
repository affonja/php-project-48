<?php

namespace Differ\Differ;

use Exception;
use Symfony\Component\Yaml\Yaml;

function parseFile(string $path): array
{
    $content = file_get_contents($path);
    if ($content === false) {
        throw new Exception('Unknown file');
    }

    $type = getExtension($path);
    if ($type === 'json') {
        return json_decode($content, true);
    }
    if ($type === 'yml' || $type === 'yaml') {
        return Yaml::parseFile($path);
    }
    throw new Exception('Unknown extension file');
}
