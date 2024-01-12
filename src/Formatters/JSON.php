<?php

namespace Differ;

function json(array $diff, string $lf = "\n"): string
{
    return array_reduce(
        $diff,
        function ($str, $arr) use ($lf) {
            $translate = [
                'add' => "+",
                'rmv' => '-',
                'upd=' => ' ',
                'upd-' => '-',
                'upd+' => '+',
                ' ' => ' '
            ];
            if (is_array($arr['val'])) {
                $template = "{\"z\":\"{$translate[$arr['z']]}\",\"key\":\"{$arr['key']}\",\"val\":[";
                $str .= ($str === '') ? $template : ",$template";
                $str .= json($arr['val']);
                $str .= "]}";
            } else {
                $arr['val'] = trim(var_export($arr['val'], true), "'");
                $template = "{\"z\":\"{$translate[$arr['z']]}\",\"key\":\"{$arr['key']}\",\"val\":\"{$arr['val']}\"}";
                $str .= ($str === '') ? $template : ",$template";
            }
            return $str;
        },
        ""
    );
}
