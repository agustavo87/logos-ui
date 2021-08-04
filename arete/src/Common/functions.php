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

if (!function_exists(__NAMESPACE__ . '\var_dump_ret')) {
    /**
     * Returns a string representation of var_dump
     *
     * @param null $mixed
     *
     * @return string
     */
    function var_dump_ret($mixed = null): string
    {
        ob_start();
        var_dump($mixed);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}
if (!function_exists(__NAMESPACE__ . '\simplifyWord')) {

    /**
     * Simplify a word removing 'special characters', accents and spaces.
     *
     * @param string $word
     *
     * @return string
     */
    function simplifyWord(string $word): string
    {
        $word = str_replace(["'", ' '], '', $word);
        $word = strtolower($word);
        // remove accents
        $word = iconv('UTF-8', 'ASCII//TRANSLIT', $word);
        return $word;
    }
}
