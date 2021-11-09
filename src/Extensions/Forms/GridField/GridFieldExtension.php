<?php

namespace SilverCart\Extensions\Forms\GridField;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\DataObject;

/**
 * Extension for SilverStripe GridField.
 * 
 * @package SilverCart
 * @subpackage Extensions\Forms\GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.11.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldExtension extends Extension
{
    /**
     * Adds new row $classes for the given $record.
     * 
     * @param array      &$classes Classes to update
     * @param int        $total    Total records
     * @param int        $index    Current index
     * @param DataObject $record   Record
     * 
     * @return void
     */
    public function updateNewRowClasses(array &$classes, int $total, int $index, DataObject $record) : void
    {
        if ($record->hasMethod('getGridFieldRowClasses')) {
            $classes = array_merge($classes, $record->getGridFieldRowClasses());
        }
    }
}