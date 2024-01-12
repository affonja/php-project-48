<?php

namespace Differ;

function json(array $diff): string
{
    return array_reduce(
        $diff,
        function ($str, $arr) {
            $translate = [
                'add' => "+",
                'rmv' => '-',
                'upd=' => ' ',
                'upd-' => '-',
                'upd+' => '+',
                ' ' => ' '
            ];
            $template = '{"act":"%s","key":"%s","value":%s';

            if (is_array($arr['value'])) {
                $str .= formatTemplate($template, $str, [$translate[$arr['act']], $arr['key'], '[']);
                $str .= json($arr['value']) . "]}";
            } else {
                $arr['value'] = toString($arr['value']);
                $str .= formatTemplate($template, $str, [$translate[$arr['act']], $arr['key'], "\"{$arr['value']}\"}"]);
            }

            return $str;
        },
        ""
    );
}

function formatTemplate(string $template, string $str, array $values): string
{
    $template = $str === '' ? $template : ",$template";
    return sprintf($template, ...$values);
}
