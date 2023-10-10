<?php

namespace SilverCart\Extensions\Forms\GridField;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataObject;

/**
 * Extension for SilverStripe GridFieldDetailForm.
 * 
 * @package SilverCart
 * @subpackage Extensions\Forms\GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 10.10.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldDetailFormExtension extends Extension
{
    /**
     * Updates the ItemRequestClass.
     *
     * @param string      $class          Item class
     * @param GridField   $gridField      GridField
     * @param DataObject  $record         Record
     * @param Controller  $requestHandler Request handle
     * @param string|null $assignedClass  Assigned class
     * 
     * @return void
     */
    public function updateItemRequestClass(string $class, GridField $gridField, DataObject $record, Controller $requestHandler, string|null $assignedClass) : void
    {
        $record->extend('updateGridFieldDetailForm', $this->owner);
    }
}