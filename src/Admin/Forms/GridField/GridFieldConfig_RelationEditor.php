<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverCart\Admin\Forms\GridField\GridFieldAddExistingAutocompleter;

/**
 * Similar to {@link GridFieldConfig_RelationEditor}, but uses
 * SilverCart\Admin\Forms\GridField\GridFieldAddExistingAutocompleter instead of
 * SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldConfig_RelationEditor extends \SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor {

    /**
     * Loads the components, sets default properties.
     *
     * @param int $itemsPerPage How many items per page should show up
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2018
     */
    public function __construct($itemsPerPage = null) {
        parent::__construct($itemsPerPage);

        $this->removeComponentsByType(\SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter::class);
        $this->addComponent(new GridFieldAddExistingAutocompleter('buttons-before-right'));
    }

}
