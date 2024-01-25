<?php

namespace Differ\Formatters;

function plain(array $diff, string $depth = ''): array
{
    return array_reduce(
        $diff,
        function ($acc, $arr) use ($depth) {
            $key = ($depth === '') ? $arr['key'] : "$depth.{$arr['key']}";
            if (is_array($arr['value']) && $arr['act'] === ' ') {
                $str_depth = ($depth === '') ? $arr['key'] : "$depth.{$arr['key']}";
                $new_str = plain($arr['value'], $str_depth);
            } else {
                $new_str = [getDiffString($arr['act'], $key, $arr['value'])];
            }
            return array_merge($acc, $new_str);
        },
        []
    );
}

function getDiffString(string $act, string $key, mixed $val): string
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

    return $translate[$act];
}
