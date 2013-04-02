<?php

/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_Components
 */

/**
 * Component to add a action for duplication DataObjects to a GridField.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_Components
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 14.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldDuplicateAction implements GridField_ColumnProvider, GridField_ActionProvider {

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
        return array(
            'class' => 'col-buttons'
        );
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
            return array(
                'title' => ''
            );
        }
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
        $content = '';
        if ($record->canCreate()) {
            $field = GridField_FormAction::create($gridField, 'DuplicateRecord' . $record->ID, false, "duplicaterecord", array('RecordID' => $record->ID))
                    ->addExtraClass('gridfield-button-duplicate')
                    ->setAttribute('title', _t('GridAction.Duplicate', "Duplicate"))
                    ->setAttribute('data-icon', 'addpage')
                    ->setDescription(_t('GridAction.DUPLICATE_DESCRIPTION', 'Duplicate'));
            $content = $field->Field();
        }
        return $content;
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
                throw new ValidationException(_t('GridFieldAction_Duplicate.DuplicatePermissionsFailure', "No duplicate permissions"), 0);
            }
            $item->duplicate();
        }
    }

}
