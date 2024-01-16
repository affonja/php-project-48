<?php

namespace Differ\Differ;

function stylish(array $diff, int $depth = 0): array
{
    return array_reduce(
        $diff,
        function ($acc, $arr) use ($depth) {
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
                $new_depth = $depth + 1;
                $new_str_start = ["$offset  {$translate[$arr['act']]} {$arr['key']}: {\n"];
                $new_str_mid = stylish($arr['value'], $new_depth);
                $new_str_end = ["$offset    }\n"];
                $new_str = array_merge($new_str_start, $new_str_mid, $new_str_end);
            } else {
                $value = (!is_null($arr['value'])) ?
                    toString($arr['value']) :
                    strtolower(toString($arr['value']));
                $new_str = ["$offset  {$translate[$arr['act']]} {$arr['key']}: $value\n"];
            }
            return array_merge($acc, $new_str);
        },
        []
    );
}
