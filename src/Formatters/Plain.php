<?php

namespace Differ;

function plain(array $diff, string $depth = ''): string
{
    return array_reduce(
        $diff,
        function ($str, $arr) use ($depth) {
            if (is_array($arr['value']) && $arr['act'] === ' ') {
                $depth = ($depth === '') ? $arr['key'] : "$depth.{$arr['key']}";
                $str .= plain($arr['value'], $depth);
            } else {
                $str .= getDiffString($arr['act'], $arr['key'], $arr['value'], $depth);
            }
            return $str;
        },
        ""
    );
}

function getDiffString(string $act, string $key, mixed $val, string $depth): string
{
    $key = ($depth === '') ? $key : "$depth.$key";
    $val = match (true) {
        is_array($val) => '[complex value]',
        is_bool($val) || is_null($val) => strtolower(toString($val)),
        default => "'$val'",
    };

    $translate = [
        'add' => "Property '$key' was added with value: $val\n",
        'rmv' => "Property '$key' was removed\n",
        'upd=' => '',
        'upd-' => "Property '$key' was updated. From $val to ",
        'upd+' => "$val\n",
    ];

    return $translate[$act];
}
