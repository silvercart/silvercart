<?php

namespace SilverCart\Dev\Tasks;

use SilverStripe\Control\CliController;

/**
 * Basic task functionallity to handle cli args.
 *
 * @package SilverCart
 * @subpackage Dev_Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 30.08.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 * @deprecated since version 4.1
 */
class Task extends CliController
{
    use \SilverCart\Dev\CLITask;
    
    /**
     * Init
     *
     * @return mixed
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    protected function init()
    {
        $result = parent::init();
        $this->initArgs();
        return $result;
    }
}