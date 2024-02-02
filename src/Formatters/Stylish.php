<?php

namespace Differ\Formatters\Stylish;

use function Differ\Formatters\Formatters\toString;

function format(array $diff, int $depth = 0): string
{
    $result = array_reduce($diff, function ($acc, $arr) use ($depth) {
        return array_merge($acc, formatDiff($arr, $depth));
    }, []);

    if ($depth === 0) {
        return "{\n" . implode("\n", $result) . "\n}";
    }

    return implode("\n", $result);
}

function formatDiff(array $arr, int $depth): array
{
    $translate = [
        'add' => "+",
        'rmv' => '-',
        'upd=' => ' ',
        'upd-' => '-',
        'upd+' => '+',
        'nested' => ' '
    ];
    $offset = str_repeat('    ', $depth);
    if (isset($arr['children']) || is_array($arr['value'])) {
        $new_depth = $depth + 1;
        $new_str_start = "$offset  {$translate[$arr['action']]} {$arr['key']}: {";
        $new_str_mid = format($arr['children'] ?? $arr['value'], $new_depth);
        $new_str_end = "$offset    }";
        return [$new_str_start, $new_str_mid, $new_str_end];
    } else {
        $value = toString($arr['value']);
        return ["$offset  {$translate[$arr['action']]} {$arr['key']}: $value"];
    }
}
