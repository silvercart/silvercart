<?php

namespace SilverCart\Dev\Tasks;

use SilverCart\Dev\CLITask;
use SilverCart\Services\AssignProductKeywordsService;
use SilverStripe\Control\Controller;

/**
 * Task to prime a SilverCart based sites cache.
 *
 * @package SilverCart
 * @subpackage Dev\Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class AssignProductKeywordsTask extends Controller
{
    use CLITask;
    
    /**
     * Processes this task.
     *
     * @return void
     */
    public function index() : void
    {
        AssignProductKeywordsService::singleton()
                ->setJob($this)
                ->run();
    }
}