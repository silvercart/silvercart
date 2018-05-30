<?php

namespace SilverCart\Admin\Dev;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Security\Permission;

/**
 * Provides a task to show the phpinfo() output.
 * 
 * @package SilverCart
 * @subpackage Admin_Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class PHPInfoTask extends BuildTask {

    /**
     * Set a custom url segment (to follow dev/tasks/)
     *
     * @var string
     */
    private static $segment = 'phpinfo';

    /**
     * Shown in the overview on the {@link TaskRunner}.
     * HTML or CLI interface. Should be short and concise, no HTML allowed.
     * 
     * @var string
     */
    protected $title = 'PHP Info Task';

    /**
     * Describe the implications the task has, and the changes it makes. Accepts 
     * HTML formatting.
     * 
     * @var string
     */
    protected $description = 'Task to show the PHP info output.';
    
    /**
     * Runs this task.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return void
     */
    public function run($request) {
        if (Permission::check('ADMIN')) {
            phpinfo();
        }
    }

}