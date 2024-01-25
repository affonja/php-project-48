<?php

namespace Differ\Parsers;

use Exception;
use Symfony\Component\Yaml\Yaml;

use function Differ\FilePath\getExtension;

function parseFile(string $path): array
{
    $content = file_get_contents($path);
    if ($content === false) {
        throw new Exception('Unknown file');
    }

    $type = getExtension($path);
    if ($type === 'json') {
        return (array)json_decode($content, true);
    }
    if ($type === 'yml' || $type === 'yaml') {
        return (array)Yaml::parseFile($path);
    }
    throw new Exception('Unknown extension file');
}
