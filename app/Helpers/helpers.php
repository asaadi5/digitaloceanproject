<?php

if (! function_exists('num')) {
    /**
     * تنسيق الأرقام بفواصل عربية
     */
    function num($value, int $decimals = 0, string $thousands_sep = '٬', string $decimal_point = '.')
    {
        if (!is_numeric($value)) {
            return $value;
        }
        return number_format((float)$value, $decimals, $decimal_point, $thousands_sep);
    }
}
if (! function_exists('num_en')) {
    /**
     * تنسيق الأرقام بفواصل إنجليزية
     */
    function num_en($value, int $decimals = 0, string $thousands_sep = ',', string $decimal_point = '.')
    {
        if (!is_numeric($value)) {
            return $value;
        }
        return number_format((float)$value, $decimals, $decimal_point, $thousands_sep);
    }
}
