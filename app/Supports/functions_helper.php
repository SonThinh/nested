<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

if (! function_exists('getBreakTimeTotal')) {
    /**
     * @param array $breakTimeArray
     * @return mixed
     */
    function getBreakTimeTotal(array $breakTimeArray)
    {
        $breakTimeTotalArray = [];
        foreach (array_filter($breakTimeArray) as $breakTime) {
            $endTime = CarbonParse($breakTime['end_time']);
            $startTime = CarbonParse($breakTime['start_time']);
            $breakTimeTotalArray [] = $endTime->diffInMinutes($startTime);
        }

        return array_reduce($breakTimeTotalArray, function ($carry, $value) {
            return $carry + $value;
        });
    }
}

if (! function_exists('CarbonParse')) {
    /**
     * @param $time
     * @return \Carbon\Carbon
     */
    function CarbonParse($time): Carbon
    {
        return Carbon::parse($time);
    }
}

if (! function_exists('ChangeMinutesToHour')) {
    /**
     * @param $minutes
     * @return string
     */
    function ChangeMinutesToHour($minutes): string
    {
        return intdiv($minutes, 60).':'.($minutes % 60);
    }
}

if (! function_exists('generateUniqueCode')) {
    /**
     * @return string
     */
    function generateUniqueCode(): string
    {
        $uniqueCode = '';
        while (! $uniqueCode) {
            $uniqueCode = Str::random(8);
            if (User::query()->where('unique_code', $uniqueCode)->exists()) {
                $uniqueCode = '';
            }
        }

        return $uniqueCode;
    }
}
if (! function_exists('convertUploadedFileToHumanReadable')) {
    /**
     * @param int $size
     * @param int $precision
     * @return float|int
     */
    function convertUploadedFileToHumanReadable(int $size, int $precision = 3)
    {
        if ($size > 0) {
            $base = log($size) / log(1024);

            //$suffixes = [' bytes', ' KB', ' MB', ' GB', ' TB'];
            //return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
            return round(pow(1024, $base - floor($base)), $precision);
        }

        return $size;
    }
}