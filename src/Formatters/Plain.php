<?php

namespace Differ;

function plain(array $diff, $depth = '', string $lf = "\n"): string
{
    return array_reduce(
        $diff,
        function ($str, $arr) use ($lf, $depth) {
            if (is_array($arr['value']) && $arr['act'] === ' ') {
                $depth = ($depth === '') ? $arr['key'] : $depth . '.' . $arr['key'];
                $str .= plain($arr['value'], $depth);
            } else {
                $str .= getStr($arr['act'], $arr['key'], $arr['value'], $depth, $lf);
            }
            return $str;
        },
        ""
    );
}

function getStr($act, $key, $val, $depth, $lf)
{
    $key = ($depth === '') ? $key : "$depth.$key";
    $val = is_array($val) ? '[complex value]' : $val;
    $val = (is_bool($val) || is_null($val)) ?
        strtolower(trim(var_export($val, true), "'")) :
        ($val === '[complex value]' ? $val : "'$val'");
    $translate = [
        'add' => "Property '$key' was added with value: $val$lf",
        'rmv' => "Property '$key' was removed$lf",
        'upd=' => '',
        'upd-' => "Property '$key' was updated. From $val to ",
        'upd+' => "$val$lf",
    ];

    return $translate[$act];
}
