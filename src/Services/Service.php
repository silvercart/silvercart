<?php

namespace SilverCart\Services;

use ReflectionClass;
use SilverCart\Dev\CLITask;
use SilverStripe\Control\CliController;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Dev\Debug;
use Symbiote\QueuedJobs\Services\AbstractQueuedJob;
use const BASE_PATH;

class Service
{
    use Injectable;
    use Configurable;
    use Extensible;
    /**
     * Set to true to log the message output into a logfile.
     * 
     * @var bool
     */
    private static bool $enable_file_logging = false;
    /**
     * Log file name.
     * 
     * @var string
     */
    protected static string|null $log_file_name = null;
    /**
     * Context job
     * 
     * @var BuildTask|CLITask|CliController|AbstractQueuedJob|null
     */
    protected BuildTask|CLITask|CliController|AbstractQueuedJob|null $job = null;
    /**
     * Prefix to set before every message.
     * 
     * @var string
     */
    protected string $messagePrefix = '';

    /**
     * Returns the context job.
     * 
     * @return BuildTask|CLITask|CliController|AbstractQueuedJob|null
     */
    public function getJob() : BuildTask|CLITask|CliController|AbstractQueuedJob|null
    {
        return $this->job;
    }

    /**
     * Set the context job.
     * 
     * @param BuildTask|CLITask|CliController|AbstractQueuedJob $job Job
     * 
     * @return Service
     */
    public function setJob(BuildTask|CLITask|CliController|AbstractQueuedJob $job) : Service
    {
        $this->job = $job;
        return $this;
    }
    
    /**
     * Sets the current step.
     * 
     * @param int $step Step
     * 
     * @return void
     */
    public function setCurrentStep(int $step) : void
    {
        $job = $this->getJob();
        if (method_exists($job, 'setCurrentStep')) {
            $job->setCurrentStep($step);
        }
    }
    
    /**
     * Returns the message prefix to use.
     * 
     * @return string
     */
    public function getMessagePrefix() : string
    {
        return $this->messagePrefix;
    }

    /**
     * Sets the message prefix.
     * 
     * @param string $messagePrefix Message prefix
     * 
     * @return Service
     */
    public function setMessagePrefix(string $messagePrefix) : Service
    {
        $this->messagePrefix = $messagePrefix;
        return $this;
    }
    
    /**
     * Adds a $message to the given $job.
     * 
     * @param string $message  Message
     * @param string $severity Severity
     * 
     * @return void
     */
    public function addMessage(string $message, string $severity = 'INFO') : void
    {
        $job = $this->getJob();
        if ($job instanceof AbstractQueuedJob
         || ($job !== null
          && method_exists($job, 'addMessage'))
        ) {
            $job->addMessage("{$this->messagePrefix}{$message}", $severity);
        } elseif (in_array(CLITask::class, (array) class_uses($job))
               || (method_exists($job, 'printInfo')
                && method_exists($job, 'printError')
                && method_exists($job, 'printString'))
        ) {
            switch ($severity) {
                case 'INFO':
                    $job->printInfo("{$this->messagePrefix}{$message}");
                    break;
                case 'ERROR':
                    $job->printError("{$this->messagePrefix}{$message}");
                    break;
                case 'PROGRESS':
                    $job->printProgressInfo("{$this->messagePrefix}{$message}");
                    break;
                default:
                    $job->printString("{$this->messagePrefix}{$message}", '33', $severity);
            }
        } else {
            Debug::message("{$this->messagePrefix}{$severity}: {$message}", false);
        }
        if ($this->config()->enable_file_logging) {
            $this->logMessage($message, $severity);
        }
    }
    
    /**
     * Adds a $message to the given $job.
     * 
     * @param string $message Message
     * 
     * @return void
     */
    public function addProgressMessage(string $message) : void
    {
        $this->addMessage($message, 'PROGRESS');
    }
    
    /**
     * Updates the job's total steps.
     * 
     * @param int $totalSteps Total step count
     * 
     * @return void
     */
    public function updateTotalSteps(int $totalSteps) : void
    {
        $job = $this->getJob();
        if (method_exists($job, 'updateTotalSteps')
         || (method_exists($job, 'hasMethod')
          && $job->hasMethod('updateTotalSteps'))
        ) {
            $job->updateTotalSteps($totalSteps);
        }
    }
    
    /**
     * Appends the job's total steps.
     * 
     * @param int $totalSteps Total step count
     * 
     * @return void
     */
    public function appendTotalSteps(int $totalSteps) : void
    {
        $job = $this->getJob();
        if (method_exists($job, 'updateTotalSteps')
         || (method_exists($job, 'hasMethod')
          && $job->hasMethod('updateTotalSteps'))
        ) {
            $job->updateTotalSteps($job->getTotalSteps() + $totalSteps);
        }
    }
    
    /**
     * Increases the job's steps processed count.
     * 
     * @return void
     */
    public function increaseStepsProcessed() : void
    {
        $job = $this->getJob();
        if (method_exists($job, 'increaseStepsProcessed')
         || (method_exists($job, 'hasMethod')
          && $job->hasMethod('increaseStepsProcessed'))
        ) {
            $job->increaseStepsProcessed();
        }
    }
    
    /**
     * Returns the log file name.
     * 
     * @return string
     */
    protected function getLogFileName() : string
    {
        if (static::$log_file_name === null) {
            $reflection            = new ReflectionClass($this);
            $name                  = str_replace(['/', '\\'], '-', $reflection->getName());
            static::$log_file_name = "Service.{$name}";
        }
        return (string) static::$log_file_name;
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
        $logFileName = $this->getLogFileName();
        if (strpos($logFileName, '-') === false) {
            $last  = $logFileName;
        } elseif ($force) {
            $parts = explode('-', $logFileName);
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
        $logFileName = $this->getLogFileName();
        if (strpos($logFileName, '.') === false) {
            static::$log_file_name .= ".{$suffix}";
        } elseif ($force) {
            $parts = explode('.', $logFileName);
            $first = array_shift($parts);
            static::$log_file_name = "{$first}.{$suffix}";
        }
    }

    /**
     * Writes a log entry
     *
     * @param string $message  Message
     * @param string $severity Severity
     *
     * @return void
     */
    public function logMessage(string $message, string $severity = 'INFO') : void
    {
        $basePath = BASE_PATH;
        $sep      = DIRECTORY_SEPARATOR;
        $logPath  = "{$basePath}{$sep}..{$sep}log";
        if (!is_dir($logPath)) {
            $logPath = "{$basePath}{$sep}log";
            if (!is_dir($logPath)) {
                mkdir($logPath);
            }
        }
        $logFileName = $this->getLogFileName();
        $datetime    = date('Y-m-d H:i:s');
        file_put_contents("{$logPath}{$sep}{$logFileName}", "{$datetime} - {$severity}: {$this->messagePrefix}{$message}" . PHP_EOL, FILE_APPEND);
    }
    
    /**
     * Returns a padded X of Y string.
     * 
     * @param int $x X (current index)
     * @param int $y Y (total quantity)
     * 
     * @return string
     */
    public function getXofY($x, $y) : string
    {
        $padded = str_pad($x, strlen($y), " ", STR_PAD_LEFT);
        return "{$padded}/{$y}";
    }
    
    
    /**
     * Prints the given error.
     * 
     * @param string $error Error to print
     * 
     * @return void
     */
    public function printError($error)
    {
        $this->addMessage($error, 'ERROR');
    }
    
    /**
     * Prints the given info.
     * 
     * @param string $info  Info to print
     * 
     * @return void
     */
    public function printInfo(string $info) : void
    {
        $this->addMessage($info);
    }

    /**
     * Prints the given progess info.
     * 
     * @param string $progress Progress to print
     * 
     * @return void
     */
    public function printProgressInfo(string $progress) : void
    {
        $this->addProgressMessage($progress);
    }
}