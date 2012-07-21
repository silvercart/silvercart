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
 * @subpackage Tasks
 */

/**
 * Basic task functionallity to handle cli args
 *
 * @package Silvercart
 * @subpackage Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 13.07.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartTask extends ScheduledTask {
    
    /**
     * Default CLI arguments
     *
     * @var array
     */
    public static $cli_args = array();
    
    /**
     * List of occured errors
     *
     * @var array
     */
    protected static $errors = array();

    /**
     * Init
     *
     * @return mixed
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function init() {
        $result = parent::init();
        foreach ($_REQUEST as $name => $value) {
            if ($name == 'args') {
                if (is_array($value)) {
                    foreach ($value as $index => $singleArg) {
                        if (is_numeric($index) &&
                            !array_key_exists($singleArg, $_REQUEST)) {
                            $this->setCliArg($singleArg, true);
                        }
                    }
                }
            } else {
                $this->setCliArg($name, $value);
            }
        }
        return $result;
    }
    
    /**
     * Simulates a login by setting the session param for the logged in user ID
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function loginSimulation() {
        Session::set("loggedInAs", 1);
    }
    
    /**
     * Prints the errors
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function printErrors() {
        $occuredErrors = self::get_errors();
        if (count($occuredErrors) > 0) {
            $tab    = "\t";
            $errors = PHP_EOL;
            if (count($occuredErrors) == 1) {
                $errors .= 'An error occured:' . PHP_EOL;
            } else {
                $errors .= 'Some errors occured:' . PHP_EOL;
            }
            foreach ($occuredErrors as $occuredError) {
                $errors   .= $tab . "\033[41m" . $occuredError . "\033[0m" . PHP_EOL;   
            }
            $errors   .= PHP_EOL;
            print $errors;
        }
    }

    /**
     * Sets the CLI argument with the given name
     *
     * @param string $name  Name of argument
     * @param mixed  $value Value of argument
     * 
     * @return void
     */
    public function setCliArg($name, $value) {
        self::$cli_args[$name] = $value;
    }
    
    /**
     * Returns the CLI argument with the given name
     *
     * @param string $name Name of argument
     * 
     * @return mixed 
     */
    public function getCliArg($name) {
        $arg = null;
        if (array_key_exists($name, self::$cli_args)) {
            $arg = self::$cli_args[$name];
        }
        return $arg;
    }
    
    /**
     * Adds an error to the errors list
     *
     * @param string $error Error to add
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public static function add_error($error) {
        self::$errors[] = $error;
    }
    
    /**
     * Returns the errors
     *
     * @return array
     */
    public static function get_errors() {
        return self::$errors;
    }
    
    /**
     * Sets the errors
     *
     * @param array $errors Errors
     * 
     * @return void
     */
    public static function set_errors($errors) {
        self::$errors = $errors;
    }
    
}