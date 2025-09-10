<?php

use Carbon\Carbon;

/**
 * تحويل بين الأرقام الغربية ↔ العربية-الهندية داخل النص
 */
if (! function_exists('ar_digits')) {
    function ar_digits(string $str, bool $toEastern = true): string {
        $western = ['0','1','2','3','4','5','6','7','8','9'];
        $eastern = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        return str_replace($toEastern ? $western : $eastern, $toEastern ? $eastern : $western, $str);
    }
}

/**
 * رقم عربي بفواصل عربية صحيحة (fallback لو ما في intl)
 * ملاحظة: الفاصلة العشرية العربية = "٫" والألفية = "٬"
 */
if (! function_exists('num')) {
    function num($value, int $decimals = 0): string {
        if (!is_numeric($value)) return (string)$value;

        // إن توفر intl نستخدمه
        if (class_exists(\NumberFormatter::class)) {
            $fmt = new \NumberFormatter('ar', \NumberFormatter::DECIMAL);
            $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, $decimals);
            return $fmt->format((float)$value);
        }

        // بديل بدون intl
        return number_format((float)$value, $decimals, '٫', '٬');
    }
}

/**
 * رقم إنجليزي بفواصل إنجليزية
 */
if (! function_exists('num_en')) {
    function num_en($value, int $decimals = 0): string {
        if (!is_numeric($value)) return (string)$value;

        if (class_exists(\NumberFormatter::class)) {
            $fmt = new \NumberFormatter('en', \NumberFormatter::DECIMAL);
            $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, $decimals);
            return $fmt->format((float)$value);
        }

        return number_format((float)$value, $decimals, '.', ',');
    }
}

/**
 * تنسيق عملة بالعربي (USD, SAR, ...). يحترم intl إن وجد.
 */
if (! function_exists('money_ar')) {
    function money_ar($amount, string $currency = 'USD', int $decimals = 0): string {
        if (!is_numeric($amount)) return (string)$amount;

        if (class_exists(\NumberFormatter::class)) {
            $fmt = new \NumberFormatter('ar', \NumberFormatter::CURRENCY);
            // بعض العملات تحتاج عدد منازل محدد
            $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, $decimals);
            return $fmt->formatCurrency((float)$amount, strtoupper($currency));
        }

        // بديل بسيط
        return num($amount, $decimals).' '.$currency;
    }
}

/**
 * التاريخ/الوقت بالعربي (مع خيار إظهار الوقت)
 */
if (! function_exists('ar_datetime')) {
    function ar_datetime($value, bool $withTime = true, bool $easternDigits = true): string {
        if (!$value) return '';
        $dt = $value instanceof Carbon ? $value : Carbon::parse($value);

        // إن توفر intl / locale مضبوط، isoFormat يعطي عربية فعلًا
        $out = $dt->locale('ar')->isoFormat($withTime ? 'LL, HH:mm' : 'LL');

        // تحويل الأرقام إلى هندية عربية إن رغبت
        return $easternDigits ? ar_digits($out, true) : $out;
    }
}

if (! function_exists('ar_date')) {
    function ar_date($value): string {
        if (!$value) return '';
        $dt = $value instanceof Carbon ? $value : Carbon::parse($value);

        // التاريخ فقط بصيغة عربية، بأرقام غربية (لا تحويل)
        return $dt->locale('ar')->isoFormat('LL');
    }


}
