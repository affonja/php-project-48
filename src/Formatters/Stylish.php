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

    $offset = getOffset($depth);
    $action = $translate[$arr['action']];
    $key = $arr['key'];
    $value = $arr['value'] ?? null;

    if (isset($arr['children']) || is_array($value)) {
        $new_depth = $depth + 1;
        $new_str_start = "$offset  $action $key: {";
        $new_str_mid = is_array($value)
            ? nestedArrayToString($value, $depth)
            : format($arr['children'] ?? $value, $new_depth);
        $new_str_end = "$offset    }";
        return [$new_str_start, $new_str_mid, $new_str_end];
    } else {
        $value = toString($value);
        return ["$offset  $action $key: $value"];
    }
}

function getOffset(int $depth): string
{
    return str_repeat('    ', $depth);
}

function nestedArrayToString(array $nestedArray, int $depth): string
{
    $offset = getOffset($depth + 2);

    $formattedStrings = array_map(
        function ($key, $value) use ($depth, $offset) {
            return is_array($value)
                ? "$offset$key: {\n" . nestedArrayToString($value, $depth + 1) . "\n$offset}"
                : "$offset$key: $value";
        },
        array_keys($nestedArray),
        $nestedArray
    );

    return implode("\n", $formattedStrings);
}
