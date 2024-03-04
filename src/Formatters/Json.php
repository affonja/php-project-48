<?php

namespace Differ\Formatters\Json;

function format(array $diff): bool|string
{
    return json_encode($diff);
}
