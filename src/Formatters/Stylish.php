<?php

namespace Differ;

function stylish(array $diff, int $depth = 0, string $lf = "\n"): string
{
    return array_reduce(
        $diff,
        function ($str, $arr) use ($lf, $depth) {
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
                $str .= "$offset  {$translate[$arr['act']]} {$arr['key']}: {{$lf}";
                $str .= stylish($arr['value'], $depth);
                $str .= "$offset    }$lf";
            } else {
                $arr['value'] = trim(var_export($arr['value'], true), "'");
                $str .= "$offset  {$translate[$arr['act']]} {$arr['key']}: {$arr['value']}$lf";
            }
            return $str;
        },
        ""
    );
}
