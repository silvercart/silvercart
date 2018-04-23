<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverCart\Admin\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;

/**
 * Similar to {@link SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor}, but allows add buttons
 * only when there's no related component already.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldConfig_HasOnlyOneEditor extends GridFieldConfig {

    /**
     * Loads the components, sets default properties.
     *
     * @param int $itemsPerPage How many items per page should show up
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.02.2013
     */
    public function __construct($itemsPerPage = null) {
        $this->addComponent(new GridFieldButtonRow('before'));
        $this->addComponent(new GridFieldDataColumns());
        $this->addComponent(new GridFieldEditButton());
        $this->addComponent(new GridFieldDeleteAction(true));
        $this->addComponent(new GridFieldDetailForm());
    }

    /**
     * Enables the add buttons for relations.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.09.2017
     */
    public function enableAddSection() {
        $autoCompleter = new GridFieldAddExistingAutocompleter('buttons-before-left');

        $this->addComponent(new GridFieldAddNewButton('buttons-before-left'));
        $this->addComponent($autoCompleter);
    }
}