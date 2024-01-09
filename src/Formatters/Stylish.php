<?php

namespace Differ;

function stylish(array $diff, int $depth = 0, string $lf = "\n"): string
{
    return array_reduce(
        $diff,
        function ($str, $arr) use ($lf, $depth) {
            $translate = [
                'a' => "+",
                'r' => '-',
                'u=' => ' ',
                'u-' => '-',
                'u+' => '+',
            ];
            $offset = str_repeat('    ', $depth);
            if (is_array($arr['val'])) {
                $depth++;
                $str .= "$offset  {$translate[$arr['z']]} {$arr['key']}: {{$lf}";
                $str .= stylish($arr['val'], $depth);
                $str .= "$offset    }$lf";
            } else {
                $arr['val'] = trim(var_export($arr['val'], true), "'");
                $str .= "$offset  {$translate[$arr['z']]} {$arr['key']}: {$arr['val']}$lf";
            }
            return $str;
        },
        ""
    );
}
