<?phpnamespace Differ;function plain(array $diff, $depth = '', string $lf = "\n"): string{    return array_reduce(        $diff,        function ($str, $arr) use ($lf, $depth) {            if (is_array($arr['val']) && $arr['z'] === ' ') {                $depth = ($depth === '') ? $arr['key'] : $depth . '.' . $arr['key'];                $str .= plain($arr['val'], $depth);            } else {//                $str .= "{$translate[$arr['z']]}";                $str .= getStr($arr['z'], $arr['key'], $arr['val'], $depth, $lf);            }            return $str;        },        ""    );}function getStr($z, $key, $val, $depth, $lf){    $key = ($depth === '') ? $key : "$depth.$key";    $val = is_array($val) ? '[complex value]' : $val;    $val = (is_bool($val) || is_null($val)) ?        strtolower(trim(var_export($val, true), "'")) :        ($val === '[complex value]' ? $val : "'$val'");    $translate = [        'a' => "Property '$key' was added with value: $val$lf",        'r' => "Property '$key' was removed$lf",        'u=' => '',        'u-' => "Property '$key' was updated. From $val to ",        'u+' => "$val$lf",    ];    $str = "{$translate[$z]}";    return $str;}