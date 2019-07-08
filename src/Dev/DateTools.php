<?php

namespace SilverCart\Dev;

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
     * Expects an amount of business days (including Saturdays) and adds the
     * amount of missing Sundays to be able to determine an expected date.
     *
     * @param int $businessDays Count of business days (including Saturdays)
     * 
     * @return int
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.10.2018
     */
    public static function addSundaysToBusinessDays($businessDays) : int
    {
        $currentWeekDay = date('N');
        $sundaysPlain   = floor(($businessDays + $currentWeekDay) / 7);
        $sundaysTotal   = floor(($businessDays + $currentWeekDay + $sundaysPlain) / 7);
        return $businessDays + $sundaysTotal;
    }
    
    /**
     * Returns the amount of business days until the given $date.
     *
     * @param string $date Date to get business days for
     * 
     * @return int
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.10.2018
     */
    public static function getBusinessDaysUntil($date) : int
    {
        $businessDays   = 0;
        $now            = time();
        $totalDays      = 0;
        $timeDifference = strtotime($date) - strtotime(date('Y-m-d 00:00:00'));
        if ($timeDifference > 0) {
            $totalDays = ceil($timeDifference / (60*60*24));
        }
        for ($day = 1; $day <= $totalDays; $day++) {
            if (date('N', $now + $day * (60*60*24)) !== '7') {
                $businessDays++;
            }
        }
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