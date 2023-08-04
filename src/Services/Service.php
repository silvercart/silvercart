<?php

namespace SilverCart\Services;

use ReflectionClass;
use SilverCart\Dev\CLITask;
use SilverCart\Dev\Tools;
use SilverStripe\Control\CliController;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Dev\Debug;
use Symbiote\QueuedJobs\Services\AbstractQueuedJob;

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
     * Context job
     * 
     * @var BuildTask|CliController|AbstractQueuedJob|null
     */
    protected BuildTask|CliController|AbstractQueuedJob|null $job = null;

    /**
     * Returns the context job.
     * 
     * @return BuildTask|CliController|AbstractQueuedJob|null
     */
    public function getJob() : BuildTask|CliController|AbstractQueuedJob|null
    {
        return $this->job;
    }

    /**
     * Set the context job.
     * 
     * @param BuildTask|CliController|AbstractQueuedJob $job Job
     * 
     * @return Service
     */
    public function setJob(BuildTask|CliController|AbstractQueuedJob $job) : Service
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
        if ($job instanceof AbstractQueuedJob) {
            $job->addMessage($message, $severity);
        } elseif (in_array(CLITask::class, (array) class_uses($job))
               || (method_exists($job, 'printInfo')
                && method_exists($job, 'printError')
                && method_exists($job, 'printString'))
        ) {
            switch ($severity) {
                case 'INFO':
                    $job->printInfo($message);
                    break;
                case 'ERROR':
                    $job->printError($message);
                    break;
                case 'PROGRESS':
                    $job->printProgressInfo($message);
                    break;
                default:
                    $job->printString($job, '33', $severity);
            }
        } else {
            Debug::message("{$severity}: {$message}");
        }
        if ($this->config()->enable_file_logging) {
            
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
     * @param string $type The type to log
     * @param string $text The text to log
     *
     * @return void
     */
    public function Log(string $type, string $text) : void
    {
        $logFileName = $this->getLogFileName();
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
    public function getXofY($x, $y) : string
    {
        $padded = str_pad($x, strlen($y), " ", STR_PAD_LEFT);
        return "{$padded}/{$y}";
    }
}