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
 * @license see license file in modules root directory
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
     * List of occured infos
     *
     * @var array
     */
    protected static $infos = array();
    
    /**
     * Folder for temporary files
     *
     * @var string
     */
    protected $tmpFolder = null;
    
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
        
        if (array_key_exists('argv', $_SERVER)) {
            $args = $_SERVER['argv'];
            if (is_array($args)) {
                array_shift($args);
                array_shift($args);
                foreach ($args as $arg) {
                    if (strpos($arg, '=') !== false) {
                        list($name, $value) = explode('=', $arg);
                    } else {
                        $name = $arg;
                        $value = true;
                    }
                    $this->setCliArg($name, $value);
                }
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
     * Returns whether the task is in silent mode or not
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.01.2013
     */
    public function isInSilentMode() {
        return $this->getCliArg('silentmode');
    }

    /**
     * Prints the errors
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.01.2013
     */
    public function printErrors() {
        $occuredErrors = self::get_errors();
        if (count($occuredErrors) > 0) {
            if (count($occuredErrors) == 1) {
                $this->printError('An error occured:');
            } else {
                $this->printError('Some errors occured:');
            }
            foreach ($occuredErrors as $occuredError) {
                $this->printError($occuredError);
            }
        }
    }
    
    /**
     * Prints the given error
     * 
     * @param string $error Error to print
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.01.2013
     */
    public function printError($error) {
        if (!$this->isInSilentMode()) {
            $tab        = "\t";
            $errorText  = $tab . "\033[41m" . $error . "\033[0m" . PHP_EOL;
            print $errorText;
        }
        $this->Log('ERROR', $error);
    }
    
    /**
     * Prints the infos
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.12.2012
     */
    public function printInfos() {
        $infos = self::get_infos();
        if (count($infos) > 0) {
            $this->printInfo('Caution:');
            foreach ($infos as $info) {
                $this->printInfo($info);
            }
        }
    }
    
    /**
     * Prints the given info
     * 
     * @param string $info Info to print
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.12.2012
     */
    public function printInfo($info) {
        if (!$this->isInSilentMode()) {
            $tab        = "\t";
            $infoText   = $tab . "\033[33m" . $info . "\033[0m" . PHP_EOL;
            print $infoText;
        }
        $this->Log('INFO', $info);
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
     * Returns the folder for temporary files.
     * If the folder does not exist yet, the folder will be created.
     * 
     * @return string
     */
    public function getTmpFolder() {
        if (is_null($this->tmpFolder)) {
            $this->setTmpFolder(Director::baseFolder() . '/silverstripe-cache/tmp');
            if (!is_dir($this->tmpFolder)) {
                mkdir($this->tmpFolder, 0777, true);
            }
        }
        return $this->tmpFolder;
    }

    /**
     * Sets the folder for temporary files
     * 
     * @param string $tmpFolder Folder for temporary files
     * 
     * @return void
     */
    public function setTmpFolder($tmpFolder) {
        $this->tmpFolder = $tmpFolder;
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
    
    /**
     * Adds an info to the infos list
     *
     * @param string $info Info to add
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public static function add_info($info) {
        self::$infos[] = $info;
    }
    
    /**
     * Returns the infos
     *
     * @return array
     */
    public static function get_infos() {
        return self::$infos;
    }
    
    /**
     * Sets the infos
     *
     * @param array $infos Infos
     * 
     * @return void
     */
    public static function set_infos($infos) {
        self::$infos = $infos;
    }

    /**
     * Writes a log entry
     *
     * @param string $type The type to log
     * @param string $text The text to log
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.01.2013
     */
    public function Log($type, $text) {
        SilvercartTools::Log($type, $text, get_class($this));
    }
    
    /**
     * Runs a command in background and returns its PID
     * 
     * @param string $command Command to run in background
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.02.2013
     */
    protected static function run_process_in_background($command) {
        $PID = shell_exec($command . " > /dev/null & echo $!");
        return trim($PID);
    }
    
    /**
     * Checks whether a process with the given PID is running
     * 
     * @param string $PID PID to look for a running process
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.02.2013
     */
    protected static function is_process_running($PID) {
        $processState = null;
        exec("ps $PID", $processState);
        return count($processState) >= 2;
    }
}