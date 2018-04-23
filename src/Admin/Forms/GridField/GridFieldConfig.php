<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldEditButton;

/**
 * Similar to {@link GridFieldConfig}, but adds some static helper methods.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 23.04.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldConfig extends \SilverStripe\Forms\GridField\GridFieldConfig {

    /**
     * Converts a GridField into a read only one.
     *
     * @param GridField $gridField GridField to convert
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2018
     */
    public static function convert_to_readonly(GridField $gridField) {
        $gridFieldConfig = $gridField->getConfig();
        $gridFieldConfig->removeComponentsByType(GridFieldEditButton::class);
        $gridFieldConfig->removeComponentsByType(GridFieldDeleteAction::class);
        $gridFieldConfig->removeComponentsByType(GridFieldAddNewButton::class);
        $gridFieldConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
        $gridFieldConfig->removeComponentsByType(\SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter::class);
    }

}