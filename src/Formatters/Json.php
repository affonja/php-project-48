<?php

namespace Differ\Formatters\Json;

use function Differ\Formatters\Formatters\toString;

function json(array $diff): bool|string
{
    $translate = [
        'add' => "+",
        'rmv' => '-',
        'upd=' => ' ',
        'upd-' => '-',
        'upd+' => '+',
        'nested' => ' '
    ];

    $formatDiff = function ($item) use (&$formatDiff, $translate) {
        $new_item['action'] = $translate[$item['action']];
        if (is_array($item['value'])) {
            $new_item['value'] = array_map($formatDiff, $item['value']);
        } else {
            $new_item['value'] = toString($item['value']);
        }
        return array_merge($item, $new_item);
    };

    $new_res = array_map($formatDiff, $diff);

    return json_encode($new_res);
}
