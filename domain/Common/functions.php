<?php

declare(strict_types=1);

namespace Arete\Common;

if (!function_exists(__NAMESPACE__ . '\testFunction')) {
    function testFunction()
    {
        return true;
    }
}


if (!function_exists(__NAMESPACE__ . '\array_filter_keys')) {
    /**
     * Filter an associative array by its $keys
     * @param array $array
     * @param array $keys
     *
     * @return array
     */
    function array_filter_keys(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }
}
