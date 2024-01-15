<?php

namespace Differ;

function stylish(array $diff, int $depth = 0): string
{
    return array_reduce(
        $diff,
        function ($str, $arr) use ($depth) {
            $translate = [
                'add' => "+",
                'rmv' => '-',
                'upd=' => ' ',
                'upd-' => '-',
                'upd+' => '+',
                ' ' => ' '
            ];
            $offset = str_repeat('    ', $depth);
            if (is_array($arr['value'])) {
                $depth++;
                $str .= "$offset  {$translate[$arr['act']]} {$arr['key']}: {\n";
                $str .= stylish($arr['value'], $depth);
                $str .= "$offset    }\n";
            } else {
                $arr['value'] = toString($arr['value']);
                $str .= "$offset  {$translate[$arr['act']]} {$arr['key']}: {$arr['value']}\n";
            }
            return $str;
        },
        ""
    );
}
