<?php
/**
 * Copyright 2012 pixeltricks GmbH
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
 * class MyAdmin_RecordController extends SilvercartManyManyOrderField_RecordController {
 *     ...
 * }
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 10.03.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartManyManyOrderField extends DropdownField {

    /**
     * Contains the name of the manyManyClass
     *
     * @var DataObject
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    public $manyManyClass;

    /**
     * Contains the name of the belongsManyManyClass.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    public $belongsManyManyClass;

    /**
     * Contains the name of the relation on the manyManyClass.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    public $relationName;

    /**
     * Contains the edit link for the BelongsManyMany object.
     * 
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    public $relationEditLink;

    /**
     * Contains the tab for the BelongsManyMany object's edit mask
     * that should be opened initially.
     * 
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    public $relationEditTabToOpen;

    /**
     * Creates a new SilvercartManyManyOrder field.
     *
     * @param DataObject $manyManyClass The source class object
     * @param string     $relationName  The name of the relation
     * @param string     $name          The field name
     * @param string     $title         The field title
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    public function __construct($manyManyClass, $relationName, $name, $title = '') {
        parent::__construct($name, $title, null, null, null);

        $this->manyManyClass        = $manyManyClass;
        $this->relationName         = $relationName;

        $manyManyClassRelation = Object::get_static($manyManyClass->class, 'many_many');

        $this->belongsManyManyClass = $manyManyClassRelation[$relationName];
    }

    /**
     * Returns the select field.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    public function FieldHolder() {
        $source             = $this->getSource();
        $availableItemIdx   = 0;
        $selectedItemIdx    = 0;
        $output             = '';
        $manyManyClass      = $this->manyManyClass;
        $relationName       = $this->relationName;
        $availableItems     = array();
        $selectedItems      = array();
        $templateVars       = array(
            'ID'                   => $this->id(),
            'extraClass'           => $this->extraClass(),
            'available_items'      => array(),
            'selected_items'       => array(),
            'relationName'         => $relationName,
            'manyManyClass'        => $manyManyClass->class,
            'belongsManyManyClass' => $this->belongsManyManyClass,
            'AbsUrl'               => Director::absoluteBaseURL()
        );

        // --------------------------------------------------------------------
        // Fill selected field list
        // --------------------------------------------------------------------
        $attributedItems = $manyManyClass->$relationName();

        if ($attributedItems) {
            $attributedItems->sort('SortOrder', 'ASC');
            foreach ($attributedItems as $attributedItem) {
                $selectedItems['item_'.$selectedItemIdx] = new ArrayData(
                    array(
                        'value' => $attributedItem->ID,
                        'label' => $attributedItem->name
                    )
                );
                $selectedItemIdx++;
            }
        }

        // --------------------------------------------------------------------
        // Fill available field list
        // --------------------------------------------------------------------
        if (is_array($source)) {
            foreach ($source as $item) {
                if ($attributedItems->find('ID', $item[0])) {
                    continue;
                }

                $availableItems['item_'.$availableItemIdx] = new ArrayData(
                    array(
                        'value'             => $item[0],
                        'label'             => $item[1]
                    )
                );
                $availableItemIdx++;
            }
        }

        $templateVars['available_items'] = new ArrayList($availableItems);
        $templateVars['selected_items']  = new ArrayList($selectedItems);
        $output                          = $this->customise($templateVars)->renderWith('SilvercartManyManyOrderField');

        return $output;
    }

    /**
     * Returns the edit link for the relation.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    public function getRelationEditLink() {
        return $this->relationEditLink;
    }

    /**
     * Returns the tab for the edit panel that shall be opened initially.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    public function getRelationEditTabToOpen() {
        return $this->relationEditTabToOpen;
    }

    /**
     * Returns the source items
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.05.2012
     */
    public function getSource() {
        $itemArray = array();
        $items     = DataObject::get(
            $this->belongsManyManyClass
        );

        if ($items) {
            foreach ($items as $item) {
                $itemArray[] = array(
                    $item->ID,
                    $item->name,
                );
            }
        }

        return $itemArray;
    }

    /**
     * Sets the edit link for the relation.
     *
     * @param string $link The link
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    public function setRelationEditLink($link) {
        $this->relationEditLink = $link;
    }

    /**
     * Sets the edit tab that shall be opened initially.
     *
     * @param string $tab The tab name (e.g. "Root_Main").
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    public function setRelationEditTabToOpen($tab) {
        $this->relationEditTabToOpen = $tab;
    }
}

/**
 * The SilvercartManyManyOrderField_RecordController record controller.
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
 * class MyAdmin_RecordController extends SilvercartManyManyOrderField_RecordController {
 *     ...
 * }
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 10.03.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartManyManyOrderField_RecordController extends ModelAdmin_RecordController {

    /**
     * Adds the abillity to execute additional actions to the model admin's
     * action handling.
     *
     * @param SS_HTTPRequest $request The request
     *
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    public function handleAction(SS_HTTPRequest $request) {
        $vars = $request->requestVars();

        if (array_key_exists('doAttributeItems', $vars)) {
            return $this->doAttributeItems($vars, $request);
        } elseif (array_key_exists('doRemoveItems', $vars)) {
            return $this->doRemoveItems($vars, $request);
        } elseif (array_key_exists('doMoveUpItems', $vars)) {
            return $this->doMoveUpItems($vars, $request);
        } elseif (array_key_exists('doMoveDownItems', $vars)) {
            return $this->doMoveDownItems($vars, $request);
        } elseif (array_key_exists('doEditItem', $vars)) {
            return $this->doEditItem($vars, $request);
        } else {
            return parent::handleAction($request);
        }
    }

    /**
     * We edit the item here.
     *
     * @param array          $vars    The request parameters as associative array
     * @param SS_HTTPRequest $request The HTTP request
     *
     * @return mixed
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    public function doEditItem($vars, SS_HTTPRequest $request) {
        // Behaviour switched on ajax.
        if (Director::is_ajax()) {
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
     * @since 10.03.2012
     */
    public function doAttributeItems($vars, SS_HTTPRequest $request) {
        if (array_key_exists('availableItems', $vars) &&
            array_key_exists('ID', $vars)) {

            $manyManyClassName        = $vars['manyManyClass'];
            $belongsManyManyClassName = $vars['belongsManyManyClass'];
            $relationName             = $vars['relationName'];

            $manyManyClass = DataObject::get_by_id($manyManyClassName, $this->currentRecord->ID);
            $lastPosition  = $manyManyClass->$relationName()->Count();

            foreach ($vars['availableItems'] as $silvercartProductVariantAttributeID) {
                $belongsManyManyObject = DataObject::get_by_id($belongsManyManyClassName, $silvercartProductVariantAttributeID);
                $manyManyClass->$relationName()->add($belongsManyManyObject, array('SortOrder' => $lastPosition));
                $lastPosition++;
            }
        }

        // Behaviour switched on ajax.
        if (Director::is_ajax()) {
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
     * @since 10.03.2012
     */
    public function doRemoveItems($vars, SS_HTTPRequest $request) {
        if (array_key_exists('selectedItems', $vars) &&
            array_key_exists('ID', $vars)) {

            $manyManyClassName        = $vars['manyManyClass'];
            $belongsManyManyClassName = $vars['belongsManyManyClass'];
            $relationName             = $vars['relationName'];

            $manyManyClassObj = DataObject::get_by_id($manyManyClassName, $this->currentRecord->ID);
            $relationInfo     = $manyManyClassObj->$relationName()->getComponentInfo();

            foreach ($vars['selectedItems'] as $silvercartProductVariantAttributeID) {
                $belongsManyManyObject = $manyManyClassObj->$relationName()->find('ID', $silvercartProductVariantAttributeID);

                if ($belongsManyManyObject) {
                    $manyManyClassObj->$relationName()->remove($belongsManyManyObject);

                    $removedSortOrder = $belongsManyManyObject->SortOrder;

                    foreach ($manyManyClassObj->$relationName() as $relationField) {
                        if ($relationField->SortOrder > $removedSortOrder) {
                            $query = DB::query(
                                sprintf(
                                    "
                                    UPDATE
                                        %s
                                    SET
                                        SortOrder = SortOrder - 1
                                    WHERE
                                        %sID = %d AND
                                        %sID = %d
                                    ",
                                    $relationInfo['tableName'],
                                    $manyManyClassObj->class,
                                    $manyManyClassObj->ID,
                                    $relationField->class,
                                    $relationField->ID
                                )
                            );
                        }
                    }
                }
            }
        }

        // Behaviour switched on ajax.
        if (Director::is_ajax()) {
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
     * @since 10.03.2012
     */
    public function doMoveUpItems($vars, SS_HTTPRequest $request) {
        if (array_key_exists('selectedItems', $vars) &&
            array_key_exists('ID', $vars)) {

            $itemsToMove              = array();
            $manyManyClassName        = $vars['manyManyClass'];
            $belongsManyManyClassName = $vars['belongsManyManyClass'];
            $relationName             = $vars['relationName'];

            $manyManyClass = DataObject::get_by_id($manyManyClassName, $this->currentRecord->ID);
            $lastPosition  = $manyManyClass->$relationName()->Count();

            foreach ($vars['selectedItems'] as $belongsManyManyId) {
                $itemToMove = $manyManyClass->$relationName()->find('ID', $belongsManyManyId);

                if ($itemToMove) {
                    if ($itemToMove->SortOrder <= $lastPosition) {
                        $itemsToMove['sort_'.str_pad($itemToMove->SortOrder, 10, '0', STR_PAD_LEFT)] = $itemToMove;
                    }
                }
            }

            ksort($itemsToMove);

            foreach ($itemsToMove as $sortOrder => $itemToMove) {
                $moveFromPosition = $itemToMove->SortOrder;
                $moveToPosition   = $moveFromPosition - 1;

                $this->changePositions($manyManyClass, $itemToMove, $relationName, $moveFromPosition, $moveToPosition);
            }
        }

        // Behaviour switched on ajax.
        if (Director::is_ajax()) {
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
     * @since 10.03.2012
     */
    public function doMoveDownItems($vars, SS_HTTPRequest $request) {
        if (array_key_exists('selectedItems', $vars) &&
            array_key_exists('ID', $vars)) {

            $itemsToMove              = array();
            $manyManyClassName        = $vars['manyManyClass'];
            $belongsManyManyClassName = $vars['belongsManyManyClass'];
            $relationName             = $vars['relationName'];

            $manyManyClass = DataObject::get_by_id($manyManyClassName, $this->currentRecord->ID);
            $lastPosition  = $manyManyClass->$relationName()->Count();

            foreach ($vars['selectedItems'] as $belongsManyManyId) {
                $itemToMove = $manyManyClass->$relationName()->find('ID', $belongsManyManyId);

                if ($itemToMove) {
                    if ($itemToMove->SortOrder < $lastPosition) {
                        $itemsToMove['sort_'.str_pad($itemToMove->SortOrder, 10, '0', STR_PAD_LEFT)] = $itemToMove;
                    }
                }
            }

            ksort($itemsToMove);

            foreach ($itemsToMove as $sortOrder => $itemToMove) {
                $moveFromPosition = $itemToMove->SortOrder;
                $moveToPosition   = $moveFromPosition + 1;

                $this->changePositions($manyManyClass, $itemToMove, $relationName, $moveFromPosition, $moveToPosition);
            }
        }

        // Behaviour switched on ajax.
        if (Director::is_ajax()) {
            return $this->edit($request);
        } else {
            Director::redirectBack();
        }
    }

    /**
     * Changes positions of two DataObjects.
     *
     * @param DataObject $manyManyClassObj The ManyMany DataObject
     * @param DataObject $itemToMove       The BelongsManyMany DataObject to move
     * @param string     $relationName     The name of the relation from manyManyClassObj to itemToMove
     * @param int        $moveFromPosition Position to move the field from
     * @param int        $moveToPosition   Position to move the field to
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.03.2012
     */
    protected function changePositions(DataObject $manyManyClassObj, DataObject $itemToMove, $relationName, $moveFromPosition, $moveToPosition) {
        if (!$manyManyClassObj ||
            !$itemToMove) {

            return false;
        }

        $changerObject = $manyManyClassObj->$relationName()->find('SortOrder', $moveToPosition);

        if ($changerObject) {
            $relationInfo = $manyManyClassObj->$relationName()->getComponentInfo();

            $query = DB::query(
                sprintf(
                    "
                    UPDATE
                        %s
                    SET
                        SortOrder = %d
                    WHERE
                        %sID = %d AND
                        %sID = %d
                    ",
                    $relationInfo['tableName'],
                    $moveFromPosition,
                    $manyManyClassObj->class,
                    $manyManyClassObj->ID,
                    $changerObject->class,
                    $changerObject->ID
                )
            );
            $query = DB::query(
                sprintf(
                    "
                    UPDATE
                        %s
                    SET
                        SortOrder = %d
                    WHERE
                        %sID = %d AND
                        %sID = %d
                    ",
                    $relationInfo['tableName'],
                    $moveToPosition,
                    $manyManyClassObj->class,
                    $manyManyClassObj->ID,
                    $itemToMove->class,
                    $itemToMove->ID
                )
            );
        }
    }
}

