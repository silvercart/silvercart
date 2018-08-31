<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_ColumnProvider;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Versioned\Versioned;

/**
 * This class is a {@link GridField} component that adds a publish action for
 * versioned objects.
 * 
 * @package SilverCart
 * @subpackage Admin_Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 31.08.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldPublishAction implements GridField_ColumnProvider, GridField_ActionProvider
{
    /**
     * Add a column 'Publish'
     *
     * @param GridField $gridField GridField
     * @param array     $columns   Columns
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.08.2018
     */
    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    /**
     * Return any special attributes that will be used for FormField::create_tag()
     *
     * @param GridField  $gridField  GridField
     * @param DataObject $record     Record
     * @param string     $columnName Column name
     * 
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return ['class' => 'grid-field__col-compact'];
    }

    /**
     * Add the title
     *
     * @param GridField  $gridField  GridField
     * @param string     $columnName Column name
     * 
     * @return array
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        if ($columnName == 'Actions') {
            return ['title' => ''];
        }
    }

    /**
     * Which columns are handled by this component
     *
     * @param GridField $gridField GridField
     * 
     * @return array
     */
    public function getColumnsHandled($gridField)
    {
        return ['Actions'];
    }

    /**
     * Which GridField actions are this component handling
     *
     * @param GridField $gridField GridField
     * 
     * @return array
     */
    public function getActions($gridField)
    {
        return ['publishrecord'];
    }

    /**
     * Returns the column content.
     *
     * @param GridField  $gridField  GridField
     * @param DataObject $record     Record
     * @param string     $columnName Column name
     * 
     * @return string the HTML for the column
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if (!$record->hasExtension(Versioned::class)) {
            return null;
        }

        $field = GridField_FormAction::create(
            $gridField,
            'PublishRecord' . $record->ID,
            false,
            "publishrecord",
            ['RecordID' => $record->ID]
        )
            ->addExtraClass('gridfield-button-publish btn--icon-md font-icon-rocket btn--no-text grid-field__icon-action')
            ->setAttribute('title', _t(self::class . '.Publish', "Publish"))
            ->setDescription(_t(self::class . '.PublishDesc', 'Publish'));
        return $field->Field();
    }

    /**
     * Handle the actions and apply any changes to the GridField
     *
     * @param GridField $gridField  GridField
     * @param string    $actionName Action name
     * @param array     $arguments  Arguments
     * @param array     $data       Form data
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.08.2018
     * @throws ValidationException
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == 'publishrecord') {
            /** @var DataObject $item */
            $item = $gridField->getList()->byID($arguments['RecordID']);
            if (!$item) {
                return;
            }

            if (!$item->hasExtension(Versioned::class)) {
                throw new ValidationException(
                    _t(self::class . '.PublishFailure', "Can't publish a non-versioned object.")
                );
            }

            $item->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        }
    }
}
