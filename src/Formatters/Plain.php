<?php

namespace Differ\Formatters\Plain;

use function Differ\Formatters\Formatters\toString;

function plain(array $diff, string $depth = ''): string
{
    $formatDiff = function ($arr) use (&$formatDiff, $depth) {
        $key = ($depth === '') ? $arr['key'] : "$depth.{$arr['key']}";
        if (is_array($arr['value']) && $arr['action'] === 'nested') {
            $str_depth = ($depth === '') ? $arr['key'] : "$depth.{$arr['key']}";
            return [plain($arr['value'], $str_depth)];
        } else {
            return [getDiffString($arr['action'], $key, $arr['value'])];
        }
    };

    $result = array_reduce(array_map($formatDiff, $diff), fn($acc, $item) => array_merge($acc, $item), []);
    if ($depth === '') {
        return trim(implode($result));
    }

    return implode($result);
}

function getDiffString(string $action, string $key, mixed $val): string
{
    $new_val = match (true) {
        is_array($val) => '[complex value]',
        is_int($val) => toString($val),
        is_bool($val) || is_null($val) => strtolower(toString($val)),
        default => "'$val'",
    };

    $translate = [
        'add' => "Property '$key' was added with value: $new_val\n",
        'rmv' => "Property '$key' was removed\n",
        'upd=' => '',
        'upd-' => "Property '$key' was updated. From $new_val to ",
        'upd+' => "$new_val\n",
    ];

    return $translate[$action];
}
