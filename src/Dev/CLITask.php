<?php

namespace SilverCart\Dev;

use ReflectionClass;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Dev\DebugTools;
use SilverCart\Model\Product\Product;
use SilverCart\Model\ShopEmail;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config as SSConfig;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\FieldType\DBText;
use SilverStripe\View\ArrayData;
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
    public static $CLI_COLOR_BLACK          = "30";
    public static $CLI_COLOR_RED            = "31";
    public static $CLI_COLOR_GREEN          = "32";
    public static $CLI_COLOR_YELLOW         = "33";
    public static $CLI_COLOR_BLUE           = "34";
    public static $CLI_COLOR_MAGENTA        = "35";
    public static $CLI_COLOR_CYAN           = "36";
    public static $CLI_COLOR_WHITE          = "37";
    public static $CLI_COLOR_CHANGE_BLACK   = "\033[30m";
    public static $CLI_COLOR_CHANGE_RED     = "\033[31m";
    public static $CLI_COLOR_CHANGE_GREEN   = "\033[32m";
    public static $CLI_COLOR_CHANGE_YELLOW  = "\033[33m";
    public static $CLI_COLOR_CHANGE_BLUE    = "\033[34m";
    public static $CLI_COLOR_CHANGE_MAGENTA = "\033[35m";
    public static $CLI_COLOR_CHANGE_CYAN    = "\033[36m";
    public static $CLI_COLOR_CHANGE_WHITE   = "\033[37m";
    
    public static $CLI_EMAIL_INFO_TYPE_INFO    = 'info';
    public static $CLI_EMAIL_INFO_TYPE_ERROR   = 'error';
    public static $CLI_EMAIL_INFO_TYPE_WARNING = 'warning';
    
    public static $RUNNING_ACTION_FILE_PREFIX = 'cli-running-action';

    /**
     * Help docs.
     *
     * @var array
     */
    protected static $default_help_docs = [
        'help' => "The action help is showing this help text.",
    ];
    /**
     * Default CLI arguments
     *
     * @var array
     */
    public static $cli_args = [];
    /**
     * Optional log file name.
     *
     * @var string
     */
    protected static $log_file_name = null;
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
    protected static $tmpFolder = null;
    /**
     * List of infos to send by email.
     *
     * @var array
     */
    protected $emailInfos = [];
    /**
     * Contains the length of the last progress string printed by $this->printProgressInfo()
     *
     * @var int
     */
    protected $lastProgressInfoLength = 0;
    /**
     * List of action callbacks to finish before self::exitIfRunningAction() is
     * calling exit().
     *
     * @var callable[]
     */
    protected $finishRunningActionsOnBeforeExit = [];
    
    /**
     * Returns a list of running actions.
     * 
     * @return array
     */
    public static function getRunningActions() : array
    {
        $actions   = [];
        $tmpFolder = self::getTmpFolder();
        if (is_dir($tmpFolder)) {
            $handle = opendir($tmpFolder);
            while (false !== ($entry = readdir($handle))) {
                if (strpos($entry, self::$RUNNING_ACTION_FILE_PREFIX) !== 0) {
                    continue;
                }
                $args               = ArrayList::create();
                $filepath           = "{$tmpFolder}/{$entry}";
                $classnameAndAction = substr($entry, strlen(self::$RUNNING_ACTION_FILE_PREFIX) + 1);
                $classnameAndActionAndArgs = substr($entry, strlen(self::$RUNNING_ACTION_FILE_PREFIX) + 1);
                if (strpos($classnameAndActionAndArgs, '______') !== false) {
                    list(
                        $classnameAndAction,
                        $argsString
                    ) = explode('______', $classnameAndActionAndArgs);
                    $argsPairs = explode('___', $argsString);
                    foreach ($argsPairs as $argsPair) {
                        list($argName, $argValue) = explode('=', $argsPair);
                        $args->push(ArrayData::create([
                            'Name'  => $argName,
                            'Value' => $argValue,
                        ]));
                    }
                } else {
                    $classnameAndAction = $classnameAndActionAndArgs;
                }
                $parts              = explode('-', $classnameAndAction);
                $action             = array_pop($parts);
                $taskName           = 'Unknown';
                $classname          = implode('\\', $parts);
                $description        = 'No description available.';
                $expectedRuntime    = [
                    60 * 5,
                    60 * 10,
                ];
                if (class_exists($classname)) {
                    $reflection       = new ReflectionClass($classname);
                    $taskName         = $reflection->getShortName();
                    $helpDocs         = (array) SSConfig::inst()->get($classname, 'help_docs');
                    $expectedRuntimes = (array) SSConfig::inst()->get($classname, 'expected_runtimes');
                    if (array_key_exists($action, $helpDocs)) {
                        $description = $helpDocs[$action];
                    }
                    if (array_key_exists($action, $expectedRuntimes)) {
                        $expectedRuntime = $expectedRuntimes[$action];
                    }
                }
                $greenRuntimePeak  = array_shift($expectedRuntime);
                $yellowRuntimePeak = array_shift($expectedRuntime);
                $seconds           = time() - filectime($filepath);
                $hours             = floor($seconds / 3600);
                $mins              = floor($seconds / 60 % 60);
                $secs              = floor($seconds % 60);
                if ($hours > 24) {
                    $days     = floor($hours / 24);
                    $dayLabel = $days === 1 ? _t(Product::class . '.DAY', 'Day') : _t(Product::class . '.DAYS', 'Days');
                    $hours    = floor($hours % 24);
                    $runtimeH = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                    $runtime  = "{$days} {$dayLabel}, {$runtimeH}";
                } else {
                    $runtime = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                }
                $runtimeStatus = 'green';
                if ($seconds > $yellowRuntimePeak) {
                    $runtimeStatus = 'red';
                } elseif ($seconds > $greenRuntimePeak) {
                    $runtimeStatus = 'yellow';
                }
                $actions[] = [
                    'ID'               => md5($entry) . sha1($entry),
                    'Filename'         => $entry,
                    'Filepath'         => $filepath,
                    'Task'             => $taskName,
                    'Action'           => $action,
                    'Namespace'        => $classname,
                    'Description'      => DBText::create()->setValue($description),
                    'ModificationTime' => DBDatetime::create()->setValue(date('Y-m-d H:i:s', filemtime($filepath))),
                    'ChangeTime'       => DBDatetime::create()->setValue(date('Y-m-d H:i:s', filectime($filepath))),
                    'AccessTime'       => DBDatetime::create()->setValue(date('Y-m-d H:i:s', fileatime($filepath))),
                    'Runtime'          => $runtime,
                    'RuntimeStatus'    => $runtimeStatus,
                    'Args'             => $args,
                ];
            }
            closedir($handle);
        }
        return $actions;
    }
    
    /**
     * Returns the file name (including path) for the running $action of the given 
     * $task.
     * 
     * @param string $task   Task to get file name for
     * @param string $action Action to get file name for
     * 
     * @return string
     */
    protected static function getRunningTaskActionFilename(string $task, string $action) : string
    {
        $tmpFolder = self::getTmpFolder();
        $class     = str_replace('\\', '-', $task);
        $prefix    = self::$RUNNING_ACTION_FILE_PREFIX;
        $file      = "{$tmpFolder}/{$prefix}-{$class}-{$action}";
        $args      = [];
        foreach (self::$cli_args as $name => $value) {
            $args[] = "{$name}={$value}";
        }
        if (count($args) > 0) {
            $argsPart = implode('___', $args);
            $file    .= "______{$argsPart}";
        }
        return $file;
    }
    
    /**
     * Removes the running $action file for the given $task.
     * 
     * @param string $task   Task to remove file for
     * @param string $action Action to remove file for
     * 
     * @return void
     */
    public static function removeTaskActionFile(string $task, string $action) : void
    {
        $filename = self::getRunningTaskActionFilename($task, $action);
        self::removeActionFilename($filename);
    }
    
    /**
     * Removes the file with the given $filename.
     * 
     * @param string $filename Filename to remove
     * 
     * @return void
     */
    public static function removeActionFilename(string $filename) : void
    {
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    
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
     * Returns the help docs.
     * 
     * @return array
     */
    protected function getHelpDocs() : array
    {
        $helpDocs = $this->config()->get('help_docs');
        if (is_array($helpDocs)) {
            $helpDocs = array_merge(self::$default_help_docs, $helpDocs);
        } else {
            $helpDocs = [];
        }
        return $helpDocs;
    }
    
    /**
     * Will show the help.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.10.2018
     */
    public function help() : void
    {
        $docs = $this->getHelpDocs();
        if (empty($docs)) {
            return;
        }
        $reflection    = new ReflectionClass($this);
        $taskName      = $reflection->getShortName();
        $actions       = $this->config()->uninherited('allowed_actions');
        $cwd           = getcwd();
        $changeMagenta = self::$CLI_COLOR_CHANGE_MAGENTA;
        $changeYellow  = self::$CLI_COLOR_CHANGE_YELLOW;
        $this->printInfo("");
        $this->printInfo("Information about {$taskName}:", self::$CLI_COLOR_CYAN);
        $this->printInfo("-----------------------------------------", self::$CLI_COLOR_CYAN);
        $this->printInfo("The {$taskName} provides the following actions:");
        $this->printInfo("");
        foreach ($actions as $action) {
            if (!array_key_exists($action, $docs)) {
                $docs[$action] = "";
            }
            $this->printInfo("  {$action}", self::$CLI_COLOR_MAGENTA);
            foreach (explode(PHP_EOL, $docs[$action]) as $line) {
                $this->printInfo($line);
            }
            $this->printInfo("");
        }
        $urlSegment = (string) $this->config()->url_segment;
        if (empty($urlSegment)) {
            $request = $this->getRequest();
            /* @var $request \SilverStripe\Control\HTTPRequest */
            $parts   = explode('/', $request->getURL());
            $cAction = array_pop($parts);
            if ($cAction !== $request->param('Action')) {
                $parts[] = $cAction;
            }
            $urlSegment = implode('/', $parts);
        }
        $this->printInfo("");
        $this->printInfo("");
        $this->printInfo("Usage of {$taskName}:", self::$CLI_COLOR_CYAN);
        $this->printInfo("--------------------------------", self::$CLI_COLOR_CYAN);
        $this->printInfo("");
        foreach ($actions as $action) {
            $this->printInfo("Calling the action {$changeMagenta}{$action}{$changeYellow}:");
            $this->printInfo("");
            $this->printInfo("\t" . "# cd {$cwd}", self::$CLI_COLOR_WHITE);
            $this->printInfo("\t" . "# php vendor/silverstripe/framework/cli-script.php {$urlSegment}/{$action}", self::$CLI_COLOR_WHITE);
            $this->printInfo("\t" . "-- or if sake is installed --");
            $this->printInfo("\t" . "# cd {$cwd}", self::$CLI_COLOR_WHITE);
            $this->printInfo("\t" . "# sake {$urlSegment}/{$action}", self::$CLI_COLOR_WHITE);
            $this->printInfo("");
        }
        $this->printInfo("");
        $this->printInfo("");
        $this->printInfo("{$taskName} Support:", self::$CLI_COLOR_CYAN);
        $this->printInfo("-------------------------------", self::$CLI_COLOR_CYAN);
        $this->printInfo("You have problems with or questions about the {$taskName}?");
        $this->printInfo("Feel free to text me at sdiel@pixeltricks.de.");
        $this->printInfo("");
        $this->printInfo("");
    }
    
    /**
     * Will show the help.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.05.2020
     */
    public function helpNoAction() : void
    {
        $errorMessage  = " No action given. Please choose an action to use this task properly.   ";
        $errorMessage2 = " See the help text below for further information how to use this task. ";
        $paddedString  = str_pad("", strlen($errorMessage));
        $this->printInfo("");
        $this->printInfo("An error occured:");
        $this->printError($paddedString);
        $this->printError($errorMessage);
        $this->printError($errorMessage2);
        $this->printError($paddedString);
        $this->printInfo("");
        $this->help();
    }
    
    /**
     * Returns the file name (including path) for the running action.
     * 
     * @param string $action Action to get file name for
     * 
     * @return string
     */
    protected function getRunningActionFilename(string $action) : string
    {
        return self::getRunningTaskActionFilename(get_class($this), $action);
    }

    /**
     * Will exit the currently running program if the requested $action is already 
     * running. If $markAsStarted is set to true, the $action will automatically
     * be marked as started if not running yet. If $printInfoMessage is set to false,
     * nothing will be printed to the output.
     * 
     * @param string $action           Action to check
     * @param bool   $markAsStarted    Mark as started if not running yet
     * @param bool   $printInfoMessage Print info message if the action is running
     * 
     * @return void
     */
    protected function exitIfRunningAction(string $action, bool $markAsStarted = true, bool $printInfoMessage = true) : void
    {
        if ($this->isRunningAction($action, $markAsStarted)) {
            if ($this->getCliArg('force-run') === '1') {
                $this->printError("");
                $this->printError("!!! CAUTION !!!");
                $this->printError("It seems that this action is already running. Forcing this action to run anyway might result in unexpected behavior.");
                $this->printError("");
                $filename  = $this->getRunningActionFilename($action);
                file_put_contents($filename, time());
                return;
            } elseif ($printInfoMessage) {
                $this->printInfo("!!! Action {$action} is already running, quit.", self::$CLI_COLOR_YELLOW);
            }
            foreach ($this->finishRunningActionsOnBeforeExit as $callback) {
                $callback();
            }
            exit();
        }
    }

    /**
     * Adds the given $action and $task combination to the callback list.
     * 
     * @param string     $action Action to finish
     * @param Controller $task   Task context
     * 
     * @return void
     */
    public function finishRunningActionOnBeforeExit(string $action, Controller $task) : void
    {
        $this->finishRunningActionsOnBeforeExit[] = function() use($action, $task) {
            $task->finishAction($action);
        };
    }
    
    /**
     * Checks whether the requested $action is already running. If $markAsStarted 
     * is set to true, the $action will automatically be marked as started if not 
     * running yet.
     * 
     * @param string $action        Action to check
     * @param bool   $markAsStarted Mark as started if not running yet
     * 
     * @return bool
     */
    public function isRunningAction(string $action, bool $markAsStarted = true) : bool
    {
        $isRunning = false;
        $filename  = $this->getRunningActionFilename($action);
        if (file_exists($filename)) {
            $isRunning = true;
        } elseif ($markAsStarted) {
            file_put_contents($filename, time());
        }
        return $isRunning;
    }

    /**
     * Will mark the requested $action as finished by removing the file marker.
     * 
     * @param string $action Action to finish
     * 
     * @return void
     */
    public function finishAction(string $action) : void
    {
        $filename = $this->getRunningActionFilename($action);
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    
    /**
     * Returns the start time for the requested $action (if running).
     * 
     * @param string $action Action to get start time for
     * 
     * @return int
     */
    public function getRunningActionStartTime(string $action) : int
    {
        $starttime = 0;
        $filename  = $this->getRunningActionFilename($action);
        if (file_exists($filename)) {
            $starttime = file_get_contents($filename);
        }
        return (int) $starttime;
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
            $progressLength = mb_strlen($progress);
            if ($this->lastProgressInfoLength > $progressLength) {
                $spaces = '';
                for ($x = 0; $x < $this->lastProgressInfoLength; $x++) {
                    $spaces .= ' ';
                }
                if (!empty($spaces)) {
                    print "\t\033[33m{$spaces}\033[0m\r";
                }
            }
            $this->lastProgressInfoLength = $progressLength;
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
     * Prints the current time as start time and returns the timestamp.
     * 
     * @param string $color Color code
     * 
     * @return int
     */
    public function printStartTime(string $color = '33') : int
    {
        $startTime     = time();
        $startTimeNice = date('Y-m-d H:i:s', $startTime);
        $this->printInfo("Start time: {$startTimeNice}", $color);
        return (int) $startTime;
    }
    
    /**
     * Prints end time information to the output.
     * 
     * @param int $startTime Start timestamp
     * 
     * @return void
     */
    public function printEndTime(int $startTime) : void
    {
        $endTime       = time();
        $startDate     = date('Y-m-d', $startTime);
        $endDate       = date('Y-m-d', $endTime);
        $startTimeNice = date('Y-m-d H:i:s', $startTime);
        $endTimeNice   = $startDate !== $endDate ? date('Y-m-d H:i:s', $endTime) : date('H:i:s', $endTime);
        $timeZoneDiff  = date('I') === '1' ? (int) date('Z') - 3600 : (int) date('Z');
        $durationNice  = date('H:i:s', ($endTime - $startTime) - $timeZoneDiff);
        $this->printInfo("");
        $this->printInfo("done after {$durationNice} ({$startTimeNice} - {$endTimeNice})");
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
    public static function getTmpFolder()
    {
        if (self::$tmpFolder === null) {
            self::setTmpFolder(Director::baseFolder() . '/silverstripe-cache/tmp');
            if (!is_dir(self::$tmpFolder)) {
                mkdir(self::$tmpFolder, 0777, true);
            }
        }
        return self::$tmpFolder;
    }

    /**
     * Sets the folder for temporary files
     * 
     * @param string $tmpFolder Folder for temporary files
     * 
     * @return void
     */
    public static function setTmpFolder(string $tmpFolder) : void
    {
        self::$tmpFolder = $tmpFolder;
    }
    
    /**
     * Adds the given prefix to the log file name (separated with a ".").
     * If $force is not set to true, the $prefix won't be added if there already
     * is an added prefix.
     * 
     * @param string $prefix Prefix to add
     * @param bool   $force  Force adding the prefix?
     * 
     * @return void
     */
    protected function setLogFileNamePrefix(string $prefix, bool $force = false) : void
    {
        if (is_null(static::$log_file_name)) {
            $reflection            = new ReflectionClass($this);
            static::$log_file_name = $reflection->getShortName();
        }
        if (strpos(static::$log_file_name, '-') === false) {
            $last  = static::$log_file_name;
        } elseif ($force) {
            $parts = explode('-', static::$log_file_name);
            $last  = array_pop($parts);
        }
        static::$log_file_name = "{$prefix}-{$last}";
    }
    
    /**
     * Adds the given suffix to the log file name (separated with a ".").
     * If $force is not set to true, the $suffix won't be added if there already
     * is an added suffix.
     * 
     * @param string $suffix Suffix to add
     * @param bool   $force  Force adding the suffix?
     * 
     * @return void
     */
    protected function setLogFileNameSuffix(string $suffix, bool $force = false) : void
    {
        if (strpos(static::$log_file_name, '.') === false) {
            static::$log_file_name .= ".{$suffix}";
        } elseif ($force) {
            $parts = explode('.', static::$log_file_name);
            $first = array_shift($parts);
            static::$log_file_name = "{$first}.{$suffix}";
        }
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
     * Returns a padded X of Y string.
     * 
     * @param int $x X (current index)
     * @param int $y Y (total quantity)
     * 
     * @return string
     */
    public function getXofY($x, $y)
    {
        $padded = str_pad($x, strlen($y), " ", STR_PAD_LEFT);
        return "{$padded}/{$y}";
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

    /**
     * Adds an information to send by email after the task is done.
     * 
     * @param string $info Info to send
     * @param string $type Type of info
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.09.2018
     */
    public function addEmailInfo($info, $type = null)
    {
        if (is_null($type)) {
            $type = self::$CLI_EMAIL_INFO_TYPE_INFO;
        }
        if (!array_key_exists($type, $this->emailInfos)) {
            $this->emailInfos[$type] = [];
        }
        $this->emailInfos[$type][] = $info;
    }

    /**
     * Adds an error message to send by email after the task is done.
     * 
     * @param string $error Error to send
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.09.2018
     */
    public function addEmailError($error)
    {
        $this->addEmailInfo($error, self::$CLI_EMAIL_INFO_TYPE_ERROR);
    }

    /**
     * Adds an warning message to send by email after the task is done.
     * 
     * @param string $warning Warning to send
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.09.2018
     */
    public function addEmailWarning($warning)
    {
        $this->addEmailInfo($warning, self::$CLI_EMAIL_INFO_TYPE_WARNING);
    }
    
    /**
     * Send the information email to the given $recipient.
     * If no $recipient is given, Config::DefaultMailRecipient() will be used as
     * default.
     * 
     * @param string $recipient Email recipient
     * 
     * @return void
     */
    public function sendEmail($recipient = null)
    {
        if (empty($this->emailInfos)) {
            return;
        }
        if (is_null($recipient)) {
            $recipient = Config::DefaultMailRecipient();
        }
        $class = self::class;
        $lines = [
            "Hey there!",
            "",
            "It's me, {$class}.",
            "I just finished my work and figured I should inform you about the following messages.",
            "",
        ];
        foreach ($this->emailInfos as $type => $messages) {
            $lines[] = "Messages of type {$type}:";
            foreach ($messages as $message) {
                $lines[] = " • {$message}";
            }
            $lines[] = "";
        }
        $lines[] = "Sincerely yours,";
        $lines[] = "Your SilverCart Ecommerce System";
        $subject = "SilverCart CLI task information";
        $content = implode("<br/>" . PHP_EOL, $lines);
        ShopEmail::send_email($recipient, $subject, $content);
        $this->emailInfos = [];
    }
}