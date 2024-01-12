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
            if (is_array($arr['value'])) {
                $template = "{\"act\":\"{$translate[$arr['act']]}\",\"key\":\"{$arr['key']}\",\"value\":[";
                $str .= ($str === '') ? $template : ",$template";
                $str .= json($arr['value']);
                $str .= "]}";
            } else {
                $arr['value'] = trim(var_export($arr['value'], true), "'");
                $template = "{\"act\":\"{$translate[$arr['act']]}\",\"key\":\"{$arr['key']}\",\"value\":\"{$arr['value']}\"}";
                $str .= ($str === '') ? $template : ",$template";
            }
            return $str;
        },
        ""
    );
}
