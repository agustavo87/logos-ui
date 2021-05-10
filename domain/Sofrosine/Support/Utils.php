<?php

namespace Arete\Sofrosine\Support;

class Utils
{
    /**
     * Returns an array of segments from a path
     *
     * @param string $path
     *
     * @return array
     */
    public static function segments(string $path): array
    {
        $segments = explode('/', $path);
        return array_values(array_filter($segments, function ($value) {
            return $value !== '';
        }));
    }

    /**
     * Trim each element of an array
     * 
     * @param string[] $array
     * @return string[]
     */
    public static function array_trim(array $array)
    {
        return array_map(fn($x) => trim($x), $array);
    }

    /**
     * Filter array and returns values ordered.
     *
     * @param string $path
     *
     * @return array
     */
    public static function filter(array $array, \Closure $callback): array
    {
        return array_values(array_filter($array, $callback));
    }

    /**
     * Evaluates if arrays keys are ordered ascending +1.
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isOrdered(array $array): bool
    {
        $isOrdered = true;
        $carry = null;
        array_walk($array, function ($value, $key) use (&$isOrdered, &$carry) {
            if ($carry === null) {
                $isOrdered = true;
            } elseif ($isOrdered) {
                $isOrdered = (int) $carry === (int) $key - 1;
            }
            $carry = $key;
            return;
        });
        return $isOrdered;
    }

    /**
     * Return the indexes of the elements of the array that fullfil a
     * condition.
     *
     * @param array $array
     * @param \Closure that returns if the key is saved.
     *
     * @return array
     */
    public static function indexesOf(array $array, \Closure $callback): array
    {
        $keys = [];
        array_walk($array, function ($value, $key) use (&$keys, $callback) {
            if ($callback($value, $key)) {
                $keys[] = $key;
            }
        });

        return $keys;
    }

    /**
     * Replace a segment in path.
     *
     * @param string $path
     * @param string $replacement
     * @param \Closure $callback that returns if the param is to be replaced.
     * 
     * @return string|null path replaced or null if no match found.
     */
    public static function replaceFirstSegment(
        string $path, 
        string $replacement, 
        \Closure $callback
    ): ?string {
        $path = trim($path);
        $segments = self::segments($path);
        $i = self::indexesOf($segments, $callback);
        if (!count($i)) return null;
        $segments[$i[0]] = $replacement;
        return '/'.implode('/', $segments);
    }

    /**
     * Return the path of a full $url. If invalid, return null. 
     * If no path, '/'.
     * 
     * @param string $url
     * @return string|null
     */
    public static function path($url): ?string
    {
        $url = Utils::validateURL($url);
        if (!$url) return null;
        $url = parse_url($url, PHP_URL_PATH);

        return $url ? $url : '/';
    }

    /**
     * Sanitizes and checks if the $url is valid. If so, returns
     * the sanitized url, null otherwise.
     * 
     * @param string $url 
     * @return string|null
     */
    public static function validateURL($url): ?string
    {
        $url = self::sanitizeURL($url);
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        return null;
    }

    public static function sanitizeURL($url)
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}
