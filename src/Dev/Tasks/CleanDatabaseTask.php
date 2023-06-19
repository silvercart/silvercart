<?php

namespace SilverCart\Dev\Tasks;

use SilverCart\Dev\CLITask;
use SilverCart\Services\CleanDatabaseService;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\BuildTask;

/**
 * Provides a task to remove no more needed objects out of the database.
 * 
 * @package SilverCart
 * @subpackage Dev_Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2018 pixeltricks GmbH
 * @since 05.09.2018
 * @license see license file in modules root directory
 */
class CleanDatabaseTask extends BuildTask
{
    use CLITask;
    /**
     * Object lifetime in days.
     *
     * @var int
     */
    private static $object_lifetime = 40;
    /**
     * Set a custom url segment (to follow dev/tasks/)
     *
     * @var string
     */
    private static $segment = 'sc-clean-database';
    /**
     * Shown in the overview on the {@link TaskRunner}.
     * HTML or CLI interface. Should be short and concise, no HTML allowed.
     * 
     * @var string
     */
    protected $title = 'Clean Shop Database Task';
    /**
     * Describe the implications the task has, and the changes it makes. Accepts 
     * HTML formatting.
     * 
     * @var string
     */
    protected $description = 'Task to remove no more needed objects (like anonymous customer data, empty shopping carts, ...) out of the SilverCart shop database.';
    
    /**
     * Runs this task.
     * 
     * @param HTTPRequest $request HTTP request
     * 
     * @return void
     */
    public function run($request) : void
    {
        CleanDatabaseService::singleton()
                ->setJob($this)
                ->run();
    }
}