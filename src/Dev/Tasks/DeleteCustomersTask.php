<?php

namespace SilverCart\Dev\Tasks;

use SilverCart\Services\DeleteCustomersService;

/**
 * Task to delete customer accounts.
 * 
 * @package SilverCart
 * @subpackage Dev\Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.07.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DeleteCustomersTask extends Task
{
    /**
     * Processes this task.
     *
     * @return void
     */
    public function process() : void
    {
        DeleteCustomersService::singleton()
                ->setJob($this)
                ->run();
    }
}