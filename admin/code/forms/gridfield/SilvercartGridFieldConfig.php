<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms_GridField
 */

/**
 * Similar to {@link GridFieldConfig}, but adds some static helper methods.
 *
 * @package Silvercart
 * @subpackage Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldConfig extends GridFieldConfig {

    /**
     * Converts a GridField into a read only one.
     *
     * @param GridField $gridField GridField to convert
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.03.2013
     */
    public static function convertToReadonly(GridField $gridField) {
        $gridFieldConfig = $gridField->getConfig();
        $gridFieldConfig->removeComponentsByType('GridFieldEditButton');
        $gridFieldConfig->removeComponentsByType('GridFieldDeleteAction');
        $gridFieldConfig->removeComponentsByType('GridFieldAddNewButton');
        $gridFieldConfig->removeComponentsByType('GridFieldAddExistingAutocompleter');
    }

}

/**
 * Similar to {@link GridFieldConfig_RecordEditor}, but uses
 * SilvercartGridFieldDataColumns instead of GridFieldDataColumns.
 *
 * @package Silvercart
 * @subpackage Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 26.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldConfig_RecordEditor extends GridFieldConfig_RecordEditor {

    /**
     * Loads the components, sets default properties.
     *
     * @param int $itemsPerPage How many items per page should show up
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.03.2013
     */
    public function __construct($itemsPerPage = null) {

        $this->addComponent(new GridFieldButtonRow('before'));
        $this->addComponent(new GridFieldAddNewButton('buttons-before-left'));
        $this->addComponent(new GridFieldToolbarHeader());
        $this->addComponent($sort = new GridFieldSortableHeader());
        $this->addComponent($filter = new GridFieldFilterHeader());
        $this->addComponent(new SilvercartGridFieldDataColumns());
        $this->addComponent(new GridFieldEditButton());
        $this->addComponent(new GridFieldDeleteAction());
        $this->addComponent($pagination = new GridFieldPaginator($itemsPerPage));
        $this->addComponent(new GridFieldDetailForm());

        $sort->setThrowExceptionOnBadDataType(false);
        $filter->setThrowExceptionOnBadDataType(false);
        $pagination->setThrowExceptionOnBadDataType(false);
    }

}

/**
 * Similar to {@link GridFieldConfig_RelationEditor}, but uses
 * SilvercartGridFieldAddExistingAutocompleter instead of
 * GridFieldAddExistingAutocompleter.
 *
 * @package Silvercart
 * @subpackage Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 12.02.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldConfig_RelationEditor extends GridFieldConfig {

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
        $this->addComponent(new GridFieldAddNewButton('buttons-before-left'));
        $this->addComponent(new SilvercartGridFieldAddExistingAutocompleter('buttons-before-left'));
        $this->addComponent(new GridFieldToolbarHeader());
        $this->addComponent($sort = new GridFieldSortableHeader());
        $this->addComponent($filter = new GridFieldFilterHeader());
        $this->addComponent(new SilvercartGridFieldDataColumns());
        $this->addComponent(new GridFieldEditButton());
        $this->addComponent(new GridFieldDeleteAction(true));
        $this->addComponent($pagination = new GridFieldPaginator($itemsPerPage));
        $this->addComponent(new GridFieldDetailForm());

        $sort->setThrowExceptionOnBadDataType(false);
        $filter->setThrowExceptionOnBadDataType(false);
        $pagination->setThrowExceptionOnBadDataType(false);
    }

}

/**
 * Similar to {@link SilvercartGridFieldConfig_RelationEditor}, but without
 * SilvercartGridFieldAddExistingAutocompleter.
 *
 * @package Silvercart
 * @subpackage Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 12.02.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldConfig_ExclusiveRelationEditor extends GridFieldConfig {

    /**
     * Loads the components, sets default properties.
     * Is useful for exclusive relations.
     *
     * @param int $itemsPerPage How many items per page should show up
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function __construct($itemsPerPage = null) {

        $this->addComponent(new GridFieldButtonRow('before'));
        $this->addComponent(new GridFieldAddNewButton('buttons-before-left'));
        $this->addComponent(new GridFieldToolbarHeader());
        $this->addComponent($sort = new GridFieldSortableHeader());
        $this->addComponent($filter = new GridFieldFilterHeader());
        $this->addComponent(new SilvercartGridFieldDataColumns());
        $this->addComponent(new GridFieldEditButton());
        $this->addComponent(new GridFieldDeleteAction());
        $this->addComponent($pagination = new GridFieldPaginator($itemsPerPage));
        $this->addComponent(new GridFieldDetailForm());

        $sort->setThrowExceptionOnBadDataType(false);
        $filter->setThrowExceptionOnBadDataType(false);
        $pagination->setThrowExceptionOnBadDataType(false);
    }

}

/**
 * Won't add any component to edit the context fields entries.
 *
 * @package Silvercart
 * @subpackage Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldConfig_Readonly extends GridFieldConfig {

    /**
     * Loads the components, sets default properties.
     *
     * @param int $itemsPerPage How many items per page should show up
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.03.2013
     */
    public function __construct($itemsPerPage = null) {
        $this->addComponent($sort = new GridFieldSortableHeader());
        $this->addComponent($filter = new GridFieldFilterHeader());
        $this->addComponent(new SilvercartGridFieldDataColumns());
        $this->addComponent($pagination = new GridFieldPaginator($itemsPerPage));
        $this->addComponent(new GridFieldDetailForm());

        $sort->setThrowExceptionOnBadDataType(false);
        $filter->setThrowExceptionOnBadDataType(false);
        $pagination->setThrowExceptionOnBadDataType(false);
    }

}

/**
 * Similar to {@link GridFieldConfig_RelationEditor}, but allows add buttons
 * only when there's no related component already.
 *
 * @package Silvercart
 * @subpackage Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 12.02.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldConfig_HasOnlyOneEditor extends GridFieldConfig {

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
        $this->addComponent(new SilvercartGridFieldDataColumns());
        $this->addComponent(new GridFieldEditButton());
        $this->addComponent(new GridFieldDeleteAction(true));
        $this->addComponent(new GridFieldDetailForm());
    }

    /**
     * Enables the add buttons for relations.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since  2013-05-08
     */
    public function enableAddSection() {
        $autoCompleter = new SilvercartGridFieldAddExistingAutocompleter('buttons-before-left');

        $this->addComponent(new GridFieldAddNewButton('buttons-before-left'));
        $this->addComponent($autoCompleter);
    }
}
