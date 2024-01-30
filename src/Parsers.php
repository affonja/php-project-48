<?php

namespace Differ\Parsers;

use Exception;
use Symfony\Component\Yaml\Yaml;

use function Differ\FilePath\getExtension;

function parseFile(string $content, string $type): array
{
    return match ($type) {
        'json' => json_decode($content, true),
        'yaml', 'yml' => Yaml::parse($content),
        default => throw new Exception('Unknown extension file')
    };
}
