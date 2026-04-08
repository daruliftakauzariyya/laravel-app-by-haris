<?php

namespace App\Helpers;

use App\Models\SiteSetting;
use Carbon\Carbon;

class HijriHelper
{
    public static function now($format = 'd F Y')
    {
        $adjustment = (int) SiteSetting::get('hijri_adjustment', 0);
        $date = Carbon::now()->addDays($adjustment);
        return self::toHijri($date, $format);
    }

    public static function toHijri($date, $format = 'd F Y')
    {
        $months = [
            1 => "Muharram", 2 => "Safar", 3 => "Rabi' al-Awwal", 4 => "Rabi' al-Thani",
            5 => "Jumada al-Awwal", 6 => "Jumada al-Thani", 7 => "Rajab", 8 => "Sha'ban",
            9 => "Ramadan", 10 => "Shawwal", 11 => "Dhu al-Qi'dah", 12 => "Dhu al-Hijjah"
        ];

        $jd = (int) self::gregorianToJd($date->year, $date->month, $date->day);
        $l = $jd - 1948440 + 10632;
        $n = (int)(($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = (int)((10985 - $l) / 5316) * (int)((50 * $l) / 17719) + (int)($l / 5670) * (int)((43 * $l) / 15238);
        $l = $l - (int)((30 - $j) / 15) * (int)((17719 * $j) / 50) - (int)($j / 16) * (int)((15238 * $j) / 43) + 29;
        $m = (int)((24 * $l) / 709);
        $d = $l - (int)((709 * $m) / 24);
        $y = 30 * $n + $j - 30;

        $result = $format;
        $result = str_replace('d', sprintf("%02d", $d), $result);
        $result = str_replace('j', $d, $result);
        $result = str_replace('F', $months[$m], $result);
        $result = str_replace('Y', $y, $result);
        return $result;
    }

    private static function gregorianToJd($year, $month, $day)
    {
        if ($month < 3) { $year--; $month += 12; }
        $a = floor($year / 100);
        $b = 2 - $a + floor($a / 4);
        return floor(365.25 * ($year + 4716)) + floor(30.6001 * ($month + 1)) + $day + $b - 1524.5;
    }
}
