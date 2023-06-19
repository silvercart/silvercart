<?php

namespace SilverCart\Dev\Tasks;

use SilverCart\Dev\CLITask;
use SilverCart\Services\ResetStockService;
use SilverStripe\Control\Controller;

/**
 * Task to reset the stock of SilverCart products from the actually assigned value
 * to the dynamically calculated value by related StockItemEntries.
 *
 * @package SilverCart
 * @subpackage Dev\Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 19.02.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ResetStockTask extends Controller
{
    use CLITask;
    /**
     * Initializes the CLI arguments.
     * 
     * @return void
     */
    protected function init() : void
    {
        parent::init();
        $this->initArgs();
    }
    
    /**
     * Processes this task.
     *
     * @return void
     */
    public function index() : void
    {
        ResetStockService::singleton()
                ->setJob($this)
                ->run();
    }
}