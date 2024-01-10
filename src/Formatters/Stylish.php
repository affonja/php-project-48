<?php

namespace Differ;

use function Functional\indexes_of;

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
