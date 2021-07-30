<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_ColumnProvider;
use SilverStripe\Forms\GridField\GridField_FormAction;

/**
 * Component to add an action for sending the order confirmation mail.
 *
 * @package SilverCart
 * @subpackage Admin\Forms\GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.07.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldResendOrderConfirmationAction implements GridField_ColumnProvider, GridField_ActionProvider
{
    /**
     * Add a column 'Actions'
     * 
     * @param GridField $gridField GridField to augment columns for
     * @param array     &$columns  Columns to augment
     * 
     * @return void
     */
    public function augmentColumns($gridField, &$columns) : void
    {
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
    public function getColumnAttributes($gridField, $record, $columnName) : array
    {
        return ['class' => 'grid-field__col-compact'];
    }

    /**
     * Add the title 
     * 
     * @param GridField $gridField  GridField to get column meta data for
     * @param string    $columnName Name of column to get meta data for
     * 
     * @return array
     */
    public function getColumnMetadata($gridField, $columnName) : array
    {
        if ($columnName === 'Actions') {
            return ['title' => ''];
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
    public function getColumnsHandled($gridField) : array
    {
        return ['Actions'];
    }

    /**
     * Which GridField actions are this component handling
     *
     * @param GridField $gridField GridField to get actions for
     * 
     * @return array 
     */
    public function getActions($gridField) : array
    {
        return ['resendorderconfirmation'];
    }

    /**
     * Returns the columns content for the given GridField, record and column 
     * name.
     *
     * @param GridField  $gridField  GridField to get content for
     * @param DataObject $record     Record to get content for
     * @param string     $columnName Column name to get content for
     * 
     * @return string|\SilverStripe\ORM\FieldType\DBHTMLText - the HTML for the column 
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        $field = GridField_FormAction::create(
            $gridField,
            "ResendOrderConfirmation{$record->ID}",
            false,
            "resendorderconfirmation",
            ['RecordID' => $record->ID]
        )
            ->addExtraClass('gridfield-button-resendorderconfirmation btn--icon-md font-icon-p-mail btn--no-text grid-field__icon-action')
            ->setAttribute('title', $record->fieldLabel('ResendOrderConfirmation'))
            ->setDescription($record->fieldLabel('ResendOrderConfirmationDesc'));
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
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data) : void
    {
        if ($actionName == 'resendorderconfirmation') {
            $item = $gridField->getList()->byID($arguments['RecordID']);
            if (!$item) {
                return;
            }
            $item->sendConfirmationMail(false);
        }
    }
}
