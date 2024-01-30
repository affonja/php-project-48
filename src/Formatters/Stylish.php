<?php

namespace Differ\Formatters\Stylish;

use function Differ\Formatters\Formatters\toString;

function format(array $diff, int $depth = 0): string
{
    $translate = [
        'add' => "+",
        'rmv' => '-',
        'upd=' => ' ',
        'upd-' => '-',
        'upd+' => '+',
        'nested' => ' '
    ];

    $formatDiff = function ($arr) use ($translate, $depth) {
        $offset = str_repeat('    ', $depth);
        if (is_array($arr['value'])) {
            $new_depth = $depth + 1;
            $new_str_start = "$offset  {$translate[$arr['action']]} {$arr['key']}: {";
            $new_str_mid = format($arr['value'], $new_depth);
            $new_str_end = "$offset    }";
            return [$new_str_start, $new_str_mid, $new_str_end];
        } else {
            $value = (!is_null($arr['value'])) ? toString($arr['value']) : strtolower(toString($arr['value']));
            return ["$offset  {$translate[$arr['action']]} {$arr['key']}: $value"];
        }
    };

    $result = array_reduce($diff, function ($acc, $arr) use ($formatDiff) {
        return array_merge($acc, $formatDiff($arr));
    }, []);

    if ($depth === 0) {
        return "{\n" . implode("\n", $result) . "\n}";
    }

    return implode("\n", $result);
}
