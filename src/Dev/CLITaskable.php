<?php

namespace SilverCart\Dev;

/**
 * Trait to add some CLITask context features (like logging or CLI output) to an 
 * object handled by a CLITask.
 * 
 * @package SilverCart
 * @subpackage Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 30.08.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait CLITaskable
{
    /**
     * Contextual CLITask object
     * 
     * @var object|null
     */
    protected $cliTask = null;
    
    /**
     * Sets the logger object.
     * 
     * @param object $cliTask CLITask to set
     * 
     * @return object
     */
    public function setCLITask(object $cliTask) : object
    {
        $this->cliTask = $cliTask;
        return $this;
    }
    
    /**
     * Returns the cliTask object.
     * 
     * @return object|null
     */
    public function getCLITask() : ?object
    {
        return $this->cliTask;
    }
    
    /**
     * Calls the given method with the given arguments on the current CLITask
     * context.
     * 
     * @return object
     */
    public function callCLITask() : object
    {
        $args   = func_get_args();
        $method = array_shift($args);
        if (is_object($this->cliTask)
         && (method_exists($this->cliTask, $method)
          || (method_exists($this->cliTask, 'hasMethod')
           && $this->cliTask->hasMethod($method)))
        ) {
            call_user_func_array([$this->cliTask, $method], $args);
        }
        return $this;
    }
}