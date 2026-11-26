<?php

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        return $value;
    }
}

if (!function_exists('cleanLetters')) {
    function cleanLetters(string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', $value) ?? '';
        $value = preg_replace('/[^\p{L} ]/u', '', $value) ?? '';
        $value = preg_replace('/ {2,}/u', ' ', $value) ?? '';
        return trim($value);
    }
}
