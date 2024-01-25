<?php

namespace Differ\Formatters;

function json(array $diff): array
{
    return array_reduce(
        $diff,
        function ($acc, $arr) {
            $translate = [
                'add' => "+",
                'rmv' => '-',
                'upd=' => ' ',
                'upd-' => '-',
                'upd+' => '+',
                ' ' => ' '
            ];
            $template = $acc === [] ?
                '{"act":"%s","key":"%s","value":%s' :
                ',{"act":"%s","key":"%s","value":%s';

            if (is_array($arr['value'])) {
                $new_str = array_merge(
                    formatTemplate($template, [$translate[$arr['act']], $arr['key'], '[']),
                    json($arr['value']),
                    ["]}"]
                );
            } else {
                $value = toString($arr['value']);
                $new_str = formatTemplate(
                    $template,
                    [$translate[$arr['act']], $arr['key'], "\"$value\"}"]
                );
            }

            return array_merge($acc, $new_str);
        },
        []
    );
}

function formatTemplate(string $template, array $values): array
{
    return [sprintf($template, ...$values)];
}
