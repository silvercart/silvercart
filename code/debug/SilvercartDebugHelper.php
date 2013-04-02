<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Debug
 */

/**
 * Provides some debug helper methods
 *
 * @package Silvercart
 * @subpackage Debug
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 22.03.2012
 * @license see license file in modules root directory
 */
class SilvercartDebugHelper {
    
    /**
     * Start time of the timer
     *
     * @var float
     */
    public static $starttime = null;
    
    /**
     * Counter of print outputs
     *
     * @var int
     */
    public static $printCounter = 0;

    /**
     * Starts the timer to debug some processing durations
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.03.2012
     */
    public static function startTimer() {
        self::setStarttime(microtime(true));
    }

    /**
     * Clears the timer
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.03.2012
     */
    public static function clearTimer() {
        self::setStarttime(null);
    }
    
    /**
     * Returns the current time difference. Timer has to be started with
     * self::startTimer().
     * If $print is set to true or not passed, the time difference will be 
     * printed to default output.
     *
     * @param bool $print Print time difference to output?
     * 
     * @return float 
     */
    public static function getTimeDifference($print = true) {
        $timeDifference = microtime(true) - self::getStarttime();
        if ($print) {
            self::printString($timeDifference, false);
        }
        return $timeDifference;
    }
    
    /**
     * Prints the given string to default output.
     *
     * @param string $string      String to print
     * @param bool   $withCounter Print counter?
     * 
     * @return void 
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function printString($string, $withCounter = true) {
        if ($withCounter) {
            print '#' . ++self::$printCounter . ': ';
        }
        print $string;
        print "<br/>" . PHP_EOL;
    }
    
    /**
     * Exports the given var to default output
     *
     * @param mixed $var Var to export
     * 
     * @return void 
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public static function varExport($var) {
        self::printString(var_export($var, true));
    }

    /**
     * Prints the current time difference to default output. Timer has to be 
     * started with self::startTimer().
     *
     * @param string $label Label to print with time difference
     * 
     * @return void 
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.03.2012
     */
    public static function printTimeDifference($label) {
        self::printString($label);
        self::getTimeDifference();
        self::printString("<hr/>", false);
    }
    
    /**
     * Checks whether the timer is already started
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function timerIsStarted() {
        $timerIsStarted = false;
        if (!is_null(self::$starttime)) {
            $timerIsStarted = true;
        }
        return $timerIsStarted;
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // DEFAULT ACCESSORS
    ////////////////////////////////////////////////////////////////////////////
    
    /**
     * Sets the start time
     *
     * @param float $starttime Start time
     * 
     * @return void
     */
    public static function setStarttime($starttime) {
        self::$starttime = $starttime;
    }
    
    /**
     * Returns the start time
     *
     * @return float
     */
    public static function getStarttime() {
        return self::$starttime;
    }
}