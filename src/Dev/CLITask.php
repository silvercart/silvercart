<?php

namespace SilverCart\Dev;

use SilverCart\Dev\Tools;
use SilverCart\Dev\DebugTools;
use SilverStripe\Control\Director;
use ZipArchive;

/**
 * Trait to add some enhanced CLI based features to any BildTask.
 * 
 * @package SilverCart
 * @subpackage Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 30.08.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait CLITask
{
    /**
     * Optional log file name.
     *
     * @var string
     */
    private static $log_file_name = null;
    /**
     * Default CLI arguments
     *
     * @var array
     */
    public static $cli_args = [];
    /**
     * List of occured errors
     *
     * @var array
     */
    protected static $errors = [];
    /**
     * List of occured infos
     *
     * @var array
     */
    protected static $infos = [];
    /**
     * Folder for temporary files
     *
     * @var string
     */
    protected $tmpFolder = null;
    
    /**
     * Initializes the given arguments.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2018
     */
    protected function initArgs() {
        if ($this->isRunningCLI()) {
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
        } else {
            foreach ($_GET as $name => $value) {
                $this->setCliArg($name, $value);
            }
        }
    }
    
    /**
     * Returns whether the current task is running in CLI.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public function isRunningCLI()
    {
        return in_array(PHP_SAPI, ["cli", "cgi", "cgi-fcgi"]);
    }
    
    /**
     * Returns the HTML color code for the given CLI color code.
     * 
     * @param string $cliColor CLI color code
     * 
     * @return string
     */
    public function getHTMLColor($cliColor)
    {
        $htmlColor  = "#000000";
        $htmlColors = [
            "30" => "#000000",
            "31" => "red",
            "32" => "green",
            "33" => "yellow",
            "34" => "blue",
            "35" => "magenta",
            "36" => "cyan",
            "37" => "#ffffff",
            "41" => "#ffffff",
        ];
        if (array_key_exists($cliColor, $htmlColors)) {
            $htmlColor = $htmlColors[$cliColor];
        }
        return $htmlColor;
    }
    
    /**
     * Returns the matching HTML color code to use as background color for the
     * given CLI color code.
     * 
     * @param string $cliColor CLI color code
     * 
     * @return string
     */
    public function getHTMLBgColor($cliColor)
    {
        $htmlColor  = "#ffffff";
        $htmlColors = [
            "30" => "#ffffff",
            "31" => "#ffffff",
            "32" => "#ffffff",
            "33" => "#000000",
            "34" => "#ffffff",
            "35" => "#ffffff",
            "36" => "#000000",
            "37" => "#000000",
            "41" => "red",
        ];
        if (array_key_exists($cliColor, $htmlColors)) {
            $htmlColor = $htmlColors[$cliColor];
        }
        return $htmlColor;
    }

    /**
     * Prints the errors
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public function printErrors()
    {
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
     * Prints the infos
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public function printInfos()
    {
        $infos = self::get_infos();
        if (count($infos) > 0) {
            $this->printInfo('Caution:');
            foreach ($infos as $info) {
                $this->printInfo($info);
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
     * @since 30.08.2018
     */
    public function printError($error)
    {
        $this->printString($error, '41', 'ERROR');
    }
    
    /**
     * Prints the given info
     * Colors:
     * <ul>
     *      <li>30 -> black</li>
     *      <li>31 -> red</li>
     *      <li>32 -> green</li>
     *      <li>33 -> yellow</li>
     *      <li>34 -> blue</li>
     *      <li>35 -> magenta</li>
     *      <li>36 -> cyan</li>
     *      <li>37 -> white</li>
     * </ul>
     * 
     * @param string $info  Info to print
     * @param string $color Color to use
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public function printInfo($info, $color = '33')
    {
        $this->printString($info, $color, 'INFO');
    }
    
    /**
     * Prints and logs the given string.
     * If silent mode is active, the string will only be logged.
     * Colors:
     * <ul>
     *      <li>30 -> black</li>
     *      <li>31 -> red</li>
     *      <li>32 -> green</li>
     *      <li>33 -> yellow</li>
     *      <li>34 -> blue</li>
     *      <li>35 -> magenta</li>
     *      <li>36 -> cyan</li>
     *      <li>37 -> white</li>
     * </ul>
     * 
     * @param string $info  Info to print
     * @param string $color Color to use
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public function printString($string, $color = '33', $logKey = '')
    {
        if (!$this->isInSilentMode()) {
            if ($this->isRunningCLI()) {
                $output = "\t\033[{$color}m{$string}\033[0m" . PHP_EOL;
            } else {
                $output = "<span style=\"color:{$this->getHTMLColor($color)};background-color:{$this->getHTMLBgColor($color)};display:block;\">{$string}</span>";
            }
            print $output;
        }
        $this->Log($logKey, $string);
    }
    
    /**
     * Prints the given progess info.
     * 
     * @param string $progress Progress to print
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public function printProgressInfo($progress)
    {
        if (!$this->isRunningCLI()) {
            return;
        }
        if (!$this->isInSilentMode()) {
            print "\t\033[33m{$progress}\033[0m\r";
        }
    }
    
    /**
     * Prints the progress percentage info
     * 
     * @param int $currentIndex Current index
     * @param int $total        Total iterations to run
     * @param int $level        Current depth level
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public function printProgressPercentageInfo($currentIndex, $total, $level = 1)
    {
        if (!$this->isRunningCLI()) {
            return;
        }
        $percentage          = $currentIndex / ($total / 100);
        $formattedPercentage = number_format($percentage, 2, ',', '.');
        $paddedPercentage    = str_pad($formattedPercentage, 6, ' ', STR_PAD_LEFT);
        $formattedIndex      = number_format($currentIndex, 0, ',', '.');
        $paddedIndex         = str_pad($formattedIndex, strlen($total), ' ', STR_PAD_LEFT);
        $formattedTotal      = number_format($total, 0, ',', '.');

        $tabs = "";
        for ($x = 0; $x < $level; $x++) {
            $tabs .= "\t";
        }
        
        $this->printProgressInfo("{$tabs}progress: {$paddedPercentage}%\t{$paddedIndex}/{$formattedTotal}");
    }
    
    /**
     * Prints the progress percentage info
     * 
     * @param int   $currentIndex Current index
     * @param int   $total        Total iterations to run
     * @param float $seconds      Current run time in seconds.
     * @param int   $level        Current depth level
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public function printProgressPercentageInfoWithTime($currentIndex, $total, $seconds, $level = 1)
    {
        if (!$this->isRunningCLI()) {
            return;
        }
        $percentage          = $currentIndex / ($total / 100);
        $formattedPercentage = number_format($percentage, 2, ',', '.');
        $paddedPercentage    = str_pad($formattedPercentage, 6, ' ', STR_PAD_LEFT);
        $formattedIndex      = number_format($currentIndex, 0, ',', '.');
        $paddedIndex         = str_pad($formattedIndex, strlen($total), ' ', STR_PAD_LEFT);
        $formattedTotal      = number_format($total, 0, ',', '.');

        $tabs = "";
        for ($x = 0; $x < $level; $x++) {
            $tabs .= "\t";
        }
        
        $paddedMinutes = str_pad(floor($seconds / 60),  2, '0', STR_PAD_LEFT);
        $paddedSeconds = str_pad(floor($seconds) % 60,  2, '0', STR_PAD_LEFT);
        $time          = "{$paddedMinutes}:{$paddedSeconds}";
        
        $this->printProgressInfo("{$tabs}time: {$time}\tprogress: {$paddedPercentage}%\t{$paddedIndex}/{$formattedTotal}");
    }
    
    /**
     * Prints the current memory usage.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public function printMemoryInfo()
    {
        if (!$this->isInSilentMode()) {
            $memUsage = 'Current Memory Usage: ' . DebugTools::getCurrentMemoryUsage() . ' (' . DebugTools::getCurrentMemoryUsage(true) . ')';
            if ($this->isRunningCLI()) {
                $output = "\t\033[35m{$memUsage}\033[0m" . PHP_EOL;
            } else {
                $output = "<span style=\"color: magenta;\">{$memUsage}</span><br/>";
            }
            print $output;
        }
    }
    
    /**
     * Prints the current memory usage as progress.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public function printMemoryProgressInfo()
    {
        if (!$this->isRunningCLI()) {
            return;
        }
        if (!$this->isInSilentMode()) {
            $memUsage   = 'Current Memory Usage: ' . DebugTools::getCurrentMemoryUsage() . ' (' . DebugTools::getCurrentMemoryUsage(true) . ')';
            print "\t\033[35m{$memUsage}\033[0m\r";
        }
    }
    
    /**
     * Simulates a login by setting the session param for the logged in user ID
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public function loginSimulation()
    {
        Tools::Session()->set("loggedInAs", 1);
    }
    
    /**
     * Returns whether the task is in silent mode or not
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public function isInSilentMode()
    {
        return $this->getCliArg('silentmode');
    }

    /**
     * Sets the CLI argument with the given name
     *
     * @param string $name  Name of argument
     * @param mixed  $value Value of argument
     * 
     * @return void
     */
    public function setCliArg($name, $value)
    {
        self::$cli_args[$name] = $value;
    }
    
    /**
     * Returns the CLI argument with the given name
     *
     * @param string $name Name of argument
     * 
     * @return mixed 
     */
    public function getCliArg($name)
    {
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
    public function getTmpFolder()
    {
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
    public function setTmpFolder($tmpFolder)
    {
        $this->tmpFolder = $tmpFolder;
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
     * @since 30.08.2018
     */
    public function Log($type, $text)
    {
        $logFileName = static::$log_file_name;
        if (is_null($logFileName)) {
            $logFileName = static::class;
        }
        Tools::Log($type, $text, $logFileName);
    }
    
    /**
     * Returns an array with all filepaths of the given file types in the given
     * directory.
     *
     * @param string $directory The directory to check.
     * @param string $extension The file extension to check for.
     *
     * @return array
     */
    protected function getFilesInDirectory($directory, $extension)
    {
        $matchingFiles = array();
        
        if ($directory) {
            $files = scandir($directory);

            if ($files) {
                if (strpos(strrev($directory), '/') !== 0) {
                    $directory .= '/';
                }
                foreach ($files as $fileName) {
                    $filePath = $directory . $fileName;

                    if (!is_file($filePath)) {
                        continue;
                    }

                    if (strtolower(substr($fileName, -(strlen($extension) + 1))) == '.' . strtolower($extension)
                        || $extension == '*'
                    ) {
                        $matchingFiles[] = $filePath;
                    }
                }
            }
        }

        return $matchingFiles;
    }
    
    /**
     * Extracts the given ZIP file into the given target folder.
     * 
     * @param string $sourceFile            Source file path.
     * @param string $targetFolder          Target folder path.
     * @param bool   $deleteAfterExtraction Set this to false to NOT delete the 
     *                                      given soource file after extraction.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    protected function extractZipFile($sourceFile, $targetFolder, $deleteAfterExtraction = true)
    {
        $this->printInfo('extracting file ' . basename($sourceFile));
        $zipArchive = new ZipArchive();
        if ($zipArchive->open($sourceFile)) {
            $zipArchive->extractTo($targetFolder);
            $zipArchive->close();
            unset($zipArchive);
        } else {
            unset($zipArchive);
        }
        if ($deleteAfterExtraction) {
            $this->printInfo('deleting ZIP file...');
            unlink($sourceFile);
        }
    }

    /**
     * Deletes all files with the given extension out of the given directory.
     * 
     * @param string $basepath  Bse path
     * @param string $extension File extension
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    protected function deleteFiles($basepath, $extension)
    {
        $importFiles  = $this->getFilesInDirectory($basepath, $extension);
        
        foreach ($importFiles as $importFile) {
            $this->printInfo('deleting file ' . $importFile);
            unlink($importFile);
        }
    }
    
    /**
     * Adds an error to the errors list
     *
     * @param string $error Error to add
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public static function add_error($error)
    {
        self::$errors[] = $error;
    }
    
    /**
     * Returns the errors
     *
     * @return array
     */
    public static function get_errors()
    {
        return self::$errors;
    }
    
    /**
     * Sets the errors
     *
     * @param array $errors Errors
     * 
     * @return void
     */
    public static function set_errors($errors)
    {
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
     * @since 30.08.2018
     */
    public static function add_info($info)
    {
        self::$infos[] = $info;
    }
    
    /**
     * Returns the infos
     *
     * @return array
     */
    public static function get_infos()
    {
        return self::$infos;
    }
    
    /**
     * Sets the infos
     *
     * @param array $infos Infos
     * 
     * @return void
     */
    public static function set_infos($infos)
    {
        self::$infos = $infos;
    }
    
    /**
     * Runs a command in background and returns its PID
     * 
     * @param string $command Command to run in background
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    protected static function run_process_in_background($command)
    {
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
     * @since 30.08.2018
     */
    protected static function is_process_running($PID)
    {
        $processState = null;
        exec("ps $PID", $processState);
        return count($processState) >= 2;
    }
}