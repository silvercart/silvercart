<?php

namespace SilverCart\Dev;

use DateTime;
use SilverCart\Dev\Tools;

/**
 * Provides methods for date tasks in SilverCart.
 *
 * @package SilverCart
 * @subpackage Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 25.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DateTools extends Tools
{
    /**
     * Expects an amount of business days (including Saturdays or not) and adds the
     * amount of missing Sundays and off days (by extensions) to be able to 
     * determine an expected date.
     *
     * @param int  $businessDays       Count of business days (including Saturdays)
     * @param bool $includingSaturdays Are Saturdays included in the amount of business days?
     * 
     * @return int
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.08.2019
     */
    public static function addOffDaysToBusinessDays(int $businessDays, bool $includingSaturdays = true) : int
    {
        $days = $businessDays;
        if (!$includingSaturdays) {
            $weekdays = ceil((strtotime("{$businessDays} weekdays") - time()) / (24*60*60));
            $days     = self::addOffDaysTo($weekdays, $includingSaturdays);
        } else {
            $days = self::getTotalDaysForBusinessDays($days, $includingSaturdays);
        }
        $lastWeekday = (int) date('N', time() + ($days * (24*60*60)));
        if (!$includingSaturdays
         && $lastWeekday === 6
        ) {
            $days++;
            $days++;
        } elseif ($lastWeekday == 7) {
            $days++;
            if (!$includingSaturdays) {
                $days++;
            }
        }
        return $days;
    }
    
    /**
     * Adds the off days to the given amount of days.
     * 
     * @param int  $days               Amount of days to add off days to
     * @param bool $includingSaturdays Include Saturdays?
     * 
     * @return int
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.08.2019
     */
    public static function addOffDaysTo(int $days, bool $includingSaturdays = true) : int
    {
        $originalDays = $days;
        $offWeekend   = [7];
        if (!$includingSaturdays) {
            $offWeekend[] = 6;
        }
        $offDays   = [];
        $days2     = $days * 2 >= 10 ? $days * 2 : 10;
        $until     = date('Y-m-d', strtotime("{$days2} days"));
        $totalDays = 0;
        self::singleton()->extend('updateAddOffDaysTo', $offDays, $until);
        if (!empty($offDays)) {
            $date = new DateTime(date('Y-m-d'));
            while ($days > 0) {
                $date->modify('+1 day');
                $totalDays++;
                if (in_array($date->format('Y-m-d'), $offDays)
                 && !in_array($date->format('N'), $offWeekend)
                ) {
                    continue;
                }
                if ($totalDays > $originalDays
                 && in_array($date->format('N'), $offWeekend)
                ) {
                    continue;
                }
                $days--;
            }
        } else {
            $totalDays = $days;
        }
        return $totalDays;
    }
    
    /**
     * Returns the total amount of days for the given amount of business $days.
     * 
     * @param int  $days               Amount of business days
     * @param bool $includingSaturdays Include Saturdays?
     * 
     * @return int
     */
    public static function getTotalDaysForBusinessDays(int $days, bool $includingSaturdays = true) : int
    {
        $businessDays = [1, 2, 3, 4, 5];
        if ($includingSaturdays) {
            $businessDays[] = 6;
        }
        $totalDays = 0;
        $date      = new DateTime(date('Y-m-d'));
        while ($days > 0) {
            $date->modify('+1 day');
            $totalDays++;
            if (!in_array($date->format('N'), $businessDays)) {
                continue;
            }
            $days--;
        }
        return self::addOffDaysTo($totalDays, $includingSaturdays);
    }
    
    /**
     * Returns the amount of business days until the given $date.
     *
     * @param string $date               Date to get business days for
     * @param bool   $includingSaturdays Handle Saturday as a business day?
     * 
     * @return int
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.10.2018
     */
    public static function getBusinessDaysUntil(string $date, bool $includingSaturdays = true) : int
    {
        $businessDays   = 0;
        $now            = time();
        $totalDays      = 0;
        $timeDifference = strtotime($date) - strtotime(date('Y-m-d 00:00:00'));
        $maxDayIndex    = '6';
        if ($timeDifference > 0) {
            $totalDays = ceil($timeDifference / (60*60*24));
        }
        if ($includingSaturdays) {
            $maxDayIndex = '7';
        }
        for ($day = 1; $day <= $totalDays; $day++) {
            if (date('N', $now + $day * (60*60*24)) !== $maxDayIndex) {
                $businessDays++;
            }
        }
        self::singleton()->extend('updateBusinessDaysUntil', $businessDays, $now, $date);
        return $businessDays;
    }
    
    /**
     * Returns the total amount of days until the given amount of $businessDays is
     * reached.
     * 
     * @param int $businessDays Amount of business days to get total days for
     * 
     * @return int
     */
    public static function getTotalDayCountFor(int $businessDays) : int
    {
        $totalDays = $businessDays;
        if ($totalDays > 0) {
            $addedBusinessDays = 0;
            $time              = time();
            do {
                if (date('N', $time) == 6
                 || date('N', $time) == 7
                ) {
                    $totalDays++;
                } else {
                    $addedBusinessDays++;
                }
                $time += 24*60*60;
            } while ($addedBusinessDays < $totalDays);
        }
        return $totalDays;
    }
}