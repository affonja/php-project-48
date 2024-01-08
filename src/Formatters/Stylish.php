<?php

namespace Differ;

function stylish(array $diff, int $depth = 0, string $lf = "\n"): string
{
    return array_reduce(
        $diff,
        function ($str, $arr) use ($lf, $depth) {
            $offset = str_repeat('    ', $depth);
            if (is_array($arr['val'])) {
                $depth++;
                $str .= stylish($arr['val'], $depth);
                $str .= "$offset    }$lf";
            } else {
                $str .= "$offset  {$arr['z']} {$arr['key']}: {$arr['val']}$lf";
            }
            return $str;
        },
        ""
    );
}
