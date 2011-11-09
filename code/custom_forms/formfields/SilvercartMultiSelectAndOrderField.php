<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * @subpackage FormFields
 */

/**
 * A formfield that displays two multiselect boxes. The left one contains a
 * pool of available items from the given DataObject ($sourceClass) that can be
 * transfered to the right field which contains the selected items. Those are
 * related to the given DataObject via a relation ($relationName).
 * The selected items can be removed and ordered.
 * 
 * You have to follow a naming convention for this field to work. If the
 * DataObject is called "MyDataObject" the relation to it's fields has to be
 * named "MyDataObjectFields"; the relation object's name must be
 * "MyDataObjectField".
 * 
 * For the actions (move up, move down, attribute, remove, etc) to work you
 * have to register this classes' recordController:
 * 
 * Register your record_controller in your ModelAdmin:
 * 
 * public static $managed_models = array(
 *     'MyDataObject' => array(
 *         'record_controller' => 'MyAdmin_RecordController'
 *     )
 * );
 * 
 * Extend the RecordController class with your ModelAdmin_RecordController
 * class that handles the DataObject in the storeadmin:
 * 
 * class MyAdmin_RecordController extends SilvercartMultiSelectAndOrderField_RecordController {
 *     ...
 * }
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 06.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartMultiSelectAndOrderField extends DropdownField {
    
    /**
     * Contains the data object to operate on
     * 
     * @var DataObject
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    protected $dataObj;
    
    /**
     * Contains the name of the relation.
     * 
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.08.2011
     */
    protected $relationName;
    
    /**
	 * Creates a new SilvercartMultiSelectAndOrder field.
	 * 
	 * @param DataObject   $sourceClass  The source class object
	 * @param string       $relationName The name of the relation
	 * @param string       $title        The field title
	 * @param array        $source       An map of the dropdown items
	 * @param string|array $value        You can pass an array of values or a single value like a drop down to be selected
	 * @param int          $size         Optional size of the select element
	 * @param boolean      $multiple     Indicates wether multiple entries can be selected
	 * @param form         $form         The parent form
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
	 */
    function __construct($sourceClass, $relationName, $name, $title = '', $source = array(), $value = '', $size = null, $multiple = false, $form = null) {
        parent::__construct($name, $title, $source, $value, $form);
        
        $this->dataObj      = $sourceClass;
        $this->relationName = $relationName;
    }
    
    /**
     * Returns the select field.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function FieldHolder() {
        $source             = $this->getSource();
        $availableItemIdx   = 0;
        $selectedItemIdx    = 0;
        $output             = '';
        $relationName       = $this->relationName;
        $availableItems     = array();
        $selectedItems      = array();
        $templateVars       = array(
            'ID'                => $this->id(),
            'extraClass'        => $this->extraClass(),
            'available_items'   => array(),
            'selected_items'    => array()
        );
        
        if (!$this->dataObj) {
            return $output;
        }
        
        // --------------------------------------------------------------------
        // Fill available field list
        // --------------------------------------------------------------------
        if (is_array($source)) {
            foreach ($source as $key => $value) {
                if (!$this->dataObj->$relationName()->find('name', $value)) {
                    $availableItems['item_'.$availableItemIdx] = new ArrayData(
                        array(
                            'value'             => $value,
                            'label'             => $value
                        )
                    );
                    $availableItemIdx++;
                }
            }
        }
        
        // --------------------------------------------------------------------
        // Fill selected field list
        // --------------------------------------------------------------------
        if ($this->dataObj) {
            $selectedRelationFields = $this->dataObj->$relationName();
            $selectedRelationFields->sort('sortOrder', 'ASC');

            foreach ($selectedRelationFields as $selectedRelationField) {
                $selectedItems['item_'.$selectedItemIdx] = new ArrayData(
                    array(
                        'value' => $selectedRelationField->name,
                        'label' => $selectedRelationField->name
                    )
                );
                $selectedItemIdx++;
            }

            $templateVars['available_items'] = new DataObjectSet($availableItems);
            $templateVars['selected_items']  = new DataObjectSet($selectedItems);
            $output                          = $this->customise($templateVars)->renderWith('SilvercartMultiSelectAndOrderField');
        }
        
        return $output;
    }
}

/**
 * The SilvercartMultiSelectAndOrderField_RecordController record controller.
 * 
 * Register your record_controller in your ModelAdmin:
 * 
 * public static $managed_models = array(
 *     'MyDataObject' => array(
 *         'record_controller' => 'MyAdmin_RecordController'
 *     )
 * );
 * 
 * Extend this class with your ModelAdmin_RecordController class that handles
 * the DataObject in the storeadmin:
 * 
 * class MyAdmin_RecordController extends SilvercartMultiSelectAndOrderField_RecordController {
 *     ...
 * }
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 13.08.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartMultiSelectAndOrderField_RecordController extends ModelAdmin_RecordController {
    
    /**
     * The name of the relation field that's connected to the DataObject we
     * operate on.
     *
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.08.2011
     */
    protected $relationFieldName;
    
    /**
     * The name of the relation on the DataObject to the field object.
     *
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.08.2011
     */
    protected $relationName;
    
    /**
     * Adds the abillity to execute additional actions to the model admin's
     * action handling.
     *
     * @param SS_HTTPRequest $request
     * 
     * @return mixed
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function handleAction(SS_HTTPRequest $request) {
        $vars = $request->requestVars();
        
        $this->relationFieldName  = $this->currentRecord->ClassName.'Field';
        $this->relationName       = $this->currentRecord->ClassName.'Fields';

        if (array_key_exists('doAttributeItems', $vars)) {
            return $this->doAttributeItems($vars, $request);
        } elseif (array_key_exists('doRemoveItems', $vars)) {
            return $this->doRemoveItems($vars, $request);
        } elseif (array_key_exists('doMoveUpItems', $vars)) {
            return $this->doMoveUpItems($vars, $request);
        } elseif (array_key_exists('doMoveDownItems', $vars)) {
            return $this->doMoveDownItems($vars, $request);
        } elseif (array_key_exists('doAddCallbackField', $vars)) {
            return $this->doAddCallbackField($vars, $request);
        } else {
            return parent::handleAction($request);
        }
    }
    
    /**
     * We save the CSV-Header definitions here.
     *
     * @param array          $data    The sent data
     * @param Form           $form    The current form object
     * @param SS_HTTPRequest $request The HTTP request
     * 
     * @return mixed
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public function doSave($data, $form, SS_HTTPRequest $request) {

        $dataObj = DataObject::get_by_id(
            $this->currentRecord->ClassName,
            Convert::raw2sql($data['ID'])
        );

        if ($dataObj) {
            foreach ($data as $httpFieldName => $fieldValue) {
                if (substr($httpFieldName, 0, strlen($this->relationName.'_')) == $this->relationName.'_') {
                    $fieldName = substr($httpFieldName, 32);

                    $relationFieldObj = DataObject::get_one(
                        $this->relationFieldName,
                        sprintf(
                            "%sID = %d AND name = '%s'",
                            $this->currentRecord->ClassName,
                            $dataObj->ID,
                            $fieldName
                        )
                    );

                    if ($relationFieldObj) {
                        $relationFieldObj->setField('headerTitle', Convert::raw2sql($fieldValue));
                        $relationFieldObj->write();
                    }
                }
            }
        }

        return parent::doSave($data, $form, $request);
    }

    /**
     * Attributes a callback field to the DataObject.
     * 
     * @param array          $vars    The request parameters as associative array
     * @param SS_HTTPRequest $request The HTTP request
     * 
     * @return html
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public function doAddCallbackField($vars, SS_HTTPRequest $request) {
        if (isset($vars['ID'])) {
            $relationName   = $this->relationName;
            $dataObj        = DataObject::get_by_id(
                $this->currentRecord->ClassName,
                Convert::raw2sql($vars['ID'])
            );

            if ($dataObj) {
                if (isset($vars['callbackField']) &&
                    !empty($vars['callbackField'])) {

                    $relationField = new $this->relationFieldName();
                    $relationField->setField('name', Convert::raw2sql($vars['callbackField']));
                    $relationField->setField($this->currentRecord->ClassName.'ID', $dataObj->ID);
                    $relationField->setField('sortOrder', $dataObj->$relationName()->Count() + 1);
                    $relationField->setField('isCallbackField', true);
                    $relationField->write();

                    $dataObj->$relationName()->push($relationField);
                    $dataObj->write();
                }
            }
        }

        // Behaviour switched on ajax.
        if(Director::is_ajax()) {
            return $this->edit($request);
        } else {
            Director::redirectBack();
        }
    }

    /**
     * Attributes fields to the DataObject.
     * 
     * @param array          $vars    The request parameters as associative array
     * @param SS_HTTPRequest $request The HTTP request
     * 
     * @return html
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function doAttributeItems($vars, SS_HTTPRequest $request) {

        if (isset($vars['ID'])) {
            $relationName = $this->relationName;
            $dataObj  = DataObject::get_by_id(
                $this->currentRecord->ClassName,
                Convert::raw2sql($vars['ID'])
            );

            if ($dataObj) {
                if (is_array($vars['availableItems'])) {
                    foreach ($vars['availableItems'] as $field) {

                        if (!$dataObj->$relationName()->find('name', $field)) {
                            $relationField = new $this->relationFieldName();
                            $relationField->setField('name', $field);
                            $relationField->setField($this->currentRecord->ClassName.'ID', $dataObj->ID);
                            $relationField->setField('sortOrder', $dataObj->$relationName()->Count() + 1);
                            $relationField->write();

                            $dataObj->$relationName()->push($relationField);
                            $dataObj->write();
                        }

                    }
                }
            }
        }

        // Behaviour switched on ajax.
        if(Director::is_ajax()) {
            return $this->edit($request);
        } else {
            Director::redirectBack();
        }
    }

    /**
     * Removes fields from the DataObject.
     * 
     * @param array          $vars    The request parameters as associative array
     * @param SS_HTTPRequest $request The HTTP request
     * 
     * @return html
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function doRemoveItems($vars, SS_HTTPRequest $request) {

        if (isset($vars['ID'])) {
            $relationName = $this->relationName;
            $dataObj  = DataObject::get_by_id(
                $this->currentRecord->ClassName,
                Convert::raw2sql($vars['ID'])
            );

            if ($dataObj) {
                if (is_array($vars['selectedItems'])) {
                    foreach ($vars['selectedItems'] as $field) {

                        $itemToDelete = $dataObj->$relationName()->find('name', $field);

                        if ($itemToDelete) {
                            $removedSortOrder = $itemToDelete->sortOrder;

                            foreach ($dataObj->$relationName() as $relationField) {
                                if ($relationField->sortOrder > $removedSortOrder) {
                                    $relationField->setField('sortOrder', $relationField->sortOrder - 1);
                                    $relationField->write();
                                }
                            }

                            $itemToDelete->delete();
                        }
                    }
                }
            }
        }

        // Behaviour switched on ajax.
        if(Director::is_ajax()) {
            return $this->edit($request);
        } else {
            Director::redirectBack();
        }
    }

    /**
     * Moves a field up in the sort hierarchy.
     * 
     * @param array          $vars    The request parameters as associative array
     * @param SS_HTTPRequest $request The HTTP request
     * 
     * @return html
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function doMoveUpItems($vars, SS_HTTPRequest $request) {

        $itemsToMove = array();

        if (isset($vars['ID'])) {
            $relationName = $this->relationName;
            $dataObj  = DataObject::get_by_id(
                $this->currentRecord->ClassName,
                Convert::raw2sql($vars['ID'])
            );

            if ($dataObj) {
                if (is_array($vars['selectedItems'])) {
                    foreach ($vars['selectedItems'] as $field) {

                        $itemToMove = $dataObj->$relationName()->find('name', $field);

                        if ($itemToMove) {
                            if ($itemToMove->sortOrder <= $dataObj->$relationName()->Count()) {
                                $itemsToMove['sort_'.str_pad($itemToMove->sortOrder, 10, '0', STR_PAD_LEFT)] = $itemToMove;
                            }
                        }
                    }
                }

                ksort($itemsToMove);

                foreach ($itemsToMove as $sortOrder => $itemToMove) {
                    $moveFromPosition = $itemToMove->sortOrder;
                    $moveToPosition   = $moveFromPosition - 1;

                    $this->changePositions($dataObj, $itemToMove, $moveFromPosition, $moveToPosition);
                }
            }
        }

        // Behaviour switched on ajax.
        if(Director::is_ajax()) {
            return $this->edit($request);
        } else {
            Director::redirectBack();
        }
    }

    /**
     * Moves a field down in the sort hierarchy.
     * 
     * @param array          $vars    The request parameters as associative array
     * @param SS_HTTPRequest $request The HTTP request
     * 
     * @return html
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function doMoveDownItems($vars, SS_HTTPRequest $request) {

        $itemsToMove = array();

        if (isset($vars['ID'])) {
            $relationName = $this->relationName;
            $dataObj  = DataObject::get_by_id(
                $this->currentRecord->ClassName,
                Convert::raw2sql($vars['ID'])
            );

            if ($dataObj) {
                if (is_array($vars['selectedItems'])) {
                    foreach ($vars['selectedItems'] as $field) {

                        $itemToMove = $dataObj->$relationName()->find('name', $field);

                        if ($itemToMove) {
                            if ($itemToMove->sortOrder < $dataObj->$relationName()->Count()) {
                                $itemsToMove['sort_'.str_pad($itemToMove->sortOrder, 10, '0', STR_PAD_LEFT)] = $itemToMove;
                            }
                        }
                    }
                }

                krsort($itemsToMove);

                foreach ($itemsToMove as $sortOrder => $itemToMove) {
                    $moveFromPosition = $itemToMove->sortOrder;
                    $moveToPosition   = $moveFromPosition + 1;

                    $this->changePositions($dataObj, $itemToMove, $moveFromPosition, $moveToPosition);
                }
            }
        }

        // Behaviour switched on ajax.
        if(Director::is_ajax()) {
            return $this->edit($request);
        } else {
            Director::redirectBack();
        }
    }

    /**
     * Changes positions of two DataObjects.
     * 
     * @param DataObject $dataObj          The DataObject object to which the fields are connected
     * @param DataObject $itemToMove       The item to move
     * @param int        $moveFromPosition Position to move the field from
     * @param int        $moveToPosition   Position to move the field to
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    protected function changePositions(DataObject $dataObj, DataObject $itemToMove, $moveFromPosition, $moveToPosition) {

        if (!$dataObj ||
            !$itemToMove) {

            return false;
        }

        $changerObject = DataObject::get_one(
            $this->relationFieldName,
            sprintf(
                "%sID = %d AND sortOrder = %d",
                $this->currentRecord->ClassName,
                $dataObj->ID,
                $moveToPosition
            )
        );

        if ($changerObject) {
            $changerObject->setField('sortOrder', $moveFromPosition);
            $changerObject->write();

            $itemToMove->setField('sortOrder', $moveToPosition);
            $itemToMove->write();
        }
    }
}
