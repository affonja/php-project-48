<?php

namespace Differ\Formatters\Json;

use function Differ\Formatters\Formatters\toString;

function format(array $diff): bool|string
{
    return json_encode($diff);
}
