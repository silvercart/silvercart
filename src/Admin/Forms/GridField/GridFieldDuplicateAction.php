<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_ColumnProvider;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\View\ArrayData;
use SilverStripe\View\SSViewer;

/**
 * Component to add an action for duplication DataObjects to a GridField.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField_Components
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldDuplicateAction implements GridField_ColumnProvider, GridField_ActionProvider {

    /**
     * Add a column 'Actions'
     * 
     * @param GridField $gridField GridField to augment columns for
     * @param array     &$columns  Columns to augment
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function augmentColumns($gridField, &$columns) {
        if (!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    /**
     * Return any special attributes that will be used for FormField::createTag()
     *
     * @param GridField  $gridField  GridField to get column attributes for
     * @param DataObject $record     Record to get attributes for
     * @param string     $columnName Name of column to get attributes for
     * 
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName) {
        return array('class' => 'grid-field__col-compact');
    }

    /**
     * Add the title 
     * 
     * @param GridField $gridField  GridField to get column meta data for
     * @param string    $columnName Name of column to get meta data for
     * 
     * @return array
     */
    public function getColumnMetadata($gridField, $columnName) {
        if ($columnName == 'Actions') {
            return array('title' => '');
        }
        return [];
    }

    /**
     * Which columns are handled by this component
     * 
     * @param GridField $gridField GridField to get handled columns for
     * 
     * @return array
     */
    public function getColumnsHandled($gridField) {
        return array('Actions');
    }

    /**
     * Which GridField actions are this component handling
     *
     * @param GridField $gridField GridField to get actions for
     * 
     * @return array 
     */
    public function getActions($gridField) {
        return array('duplicaterecord');
    }

    /**
     * Returns the columns content for the given GridField, record and column 
     * name.
     *
     * @param GridField  $gridField  GridField to get content for
     * @param DataObject $record     Record to get content for
     * @param string     $columnName Column name to get content for
     * 
     * @return string - the HTML for the column 
     */
    public function getColumnContent($gridField, $record, $columnName) {
        $field = GridField_FormAction::create(
            $gridField,
            'DuplicateRecord'.$record->ID,
            false,
            "duplicaterecord",
            array('RecordID' => $record->ID)
        )
            ->addExtraClass('gridfield-button-duplicate btn--icon-md font-icon-plus btn--no-text grid-field__icon-action')
            ->setAttribute('title', _t(GridFieldDuplicateAction::class . '.Duplicate', "Duplicate"))
            ->setDescription(_t(GridFieldDuplicateAction::class . '.DUPLICATE_DESCRIPTION', "Duplicates this object"));
        return $field->Field();
    }

    /**
     * Handle the actions and apply any changes to the GridField
     *
     * @param GridField $gridField  GridField to handle action for
     * @param string    $actionName Action name
     * @param array     $arguments  Arguments to handle
     * @param array     $data       Form data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
        if ($actionName == 'duplicaterecord') {
            $item = $gridField->getList()->byID($arguments['RecordID']);
            if (!$item) {
                return;
            }
            if (!$item->canCreate()) {
                throw new ValidationException(_t(GridFieldDuplicateAction::class . '.DuplicatePermissionsFailure', "No duplicate permissions"), 0);
            }
            $item->duplicate();
        }
    }

}
