<?php

namespace Differ;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $path): array
{
    $type = getExtension($path);
    if ($type === 'json') {
        return (array)json_decode(file_get_contents($path));
    }
    if ($type === 'yml' || $type === 'yaml') {
        return Yaml::parseFile($path);
    }

    throw new \Exception('Unknown extension file');
}
