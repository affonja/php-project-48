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
        $new_action = $translate[$item['action']];
        if (is_array($item['value'])) {
            $new_value = array_map($formatDiff, $item['value']);
        } else {
            $new_value = toString($item['value']);
        }
        return array_merge($item, ['action' => $new_action, 'value' => $new_value]);
    };

    $result = array_map($formatDiff, $diff);

    return json_encode($result);
}
