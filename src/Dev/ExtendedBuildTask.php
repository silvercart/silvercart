<?php

namespace SilverCart\Dev;

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;

/**
 * Trait to add some enhanced features to any BuildTask.
 * 
 * @package SilverCart
 * @subpackage Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 15.08.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait ExtendedBuildTask
{
    /**
     * Handles an action called on this task.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.08.2019
     */
    protected function handleAction(HTTPRequest $request) : void
    {
        $allowedActions = $this->config()->allowed_actions;
        if (!is_array($allowedActions)) {
            $this->runDefault($request);
            exit();
        }
        $urlParts  = explode('/', $request->getURL());
        $urlParams = [];
        do {
            $param       = array_pop($urlParts);
            $urlParams[] = $param;
        } while ($param !== $this->config()->segment);
        array_pop($urlParams);
        $revParams = array_reverse($urlParams);
        $action    = array_shift($revParams);
        $id        = array_shift($revParams);
        $otherId   = array_shift($revParams);
        
        if (in_array($action, $allowedActions)) {
            $this->{$action}($request, $id, $otherId);
            exit();
        } elseif (empty($action)
               || $action === $this->config()->segment
        ) {
            $this->runDefault($request);
            exit();
        } else {
            $this->printMessage("Action {$action} is not allowed on this task.");
            exit();
        }
    }
    
    /**
     * Returns the link with the given $action of this task.
     * 
     * @param string $action Action
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.05.2019
     */
    public function Link(string $action = '') : string
    {
        $relativeURL = Controller::curr()->getRequest()->getURL();
        $absoluteURL = Director::absoluteURL($relativeURL);
        if (!empty($action)) {
            $absoluteURL .= DIRECTORY_SEPARATOR . $action;
        }
        return $absoluteURL;
    }
    
    /**
     * Returns the link with the given $action of this task.
     * 
     * @param string $action Action
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.05.2019
     */
    public function BaseLink(string $action = '') : string
    {
        $relativeURL = "dev/tasks/{$this->config()->segment}";
        $absoluteURL = Director::absoluteURL($relativeURL);
        if (!empty($action)) {
            $absoluteURL .= DIRECTORY_SEPARATOR . $action;
        }
        return $absoluteURL;
    }
    
    /**
     * Returns the line break string for the given environment context (CLI/browser).
     * 
     * @return string
     */
    public function getLineBreak() : string
    {
        $br = "\n";
        if (!Director::is_cli()) {
            $br = "<br/>{$br}";
        }
        return $br;
    }
    
    /**
     * Prints a line to the output.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.08.2019
     */
    public function printLine() : void
    {
        $line = "----------------------------------------------------------------------------{$this->getLineBreak()}";
        if (!Director::is_cli()) {
            $line = "<hr/>{$this->getLineBreak()}";
        }
        $this->printMessage($line);
    }
    
    /**
     * Prints the given $message to the output.
     * 
     * @param string $message Message
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.08.2019
     */
    public function printMessage(string $message) : void
    {
        print "{$message}{$this->getLineBreak()}";
    }
}