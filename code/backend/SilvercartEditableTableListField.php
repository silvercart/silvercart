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
 * @subpackage Backend
 */

/**
 * Special table list field to execute batch actions
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 10.07.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartEditableTableListField extends TableListField {

    /**
     * @var $itemClass string Class name for each item/row
     */
    public $itemClass = 'SilvercartEditableTableListField_Item';

    /**
     * Template to use for this field
     * 
     * @var $template string
     */
    protected $template = "SilvercartEditableTableListField";
    
    /**
     * List of available batch actions
     *
     * @var ArrayList
     */
    protected $batchActions = null;
    
    /**
     * Call back method name to get the quick access data for a record.
     * The return data type of the call back method should be string or
     * Fieldset
     *
     * @var string 
     */
    protected $quickAccessCallBack = null;
    
    /**
     * The name of the template to use for the quick access fields
     *
     * @var string 
     */
    protected $quickAccessFieldsTemplate = 'SilvercartEditableTableListFieldQuickAccess';

    /**
     * Constructor
     *
     * @param string $name         Name of the field
     * @param string $sourceClass  Source class to use
     * @param array  $fieldList    Field list to use
     * @param string $sourceFilter Filter to use
     * @param string $sourceSort   Sort to use
     * @param string $sourceJoin   Join to use
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.07.2012
     */
    public function __construct($name, $sourceClass, $fieldList = null, $sourceFilter = null, $sourceSort = null, $sourceJoin = null) {
        parent::__construct($name, $sourceClass, $fieldList, $sourceFilter, $sourceSort, $sourceJoin);
        $this->Markable = true;
        $this->addSelectOptions(
                array(
                    'all'       => _t('Silvercart.MARK_ALL'),
                    'none'      => _t('Silvercart.UNMARK_ALL'),
                )
        );
    }
    
    /**
     * Returns the link of the current controller
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.07.2012
     */
    public function ControllerLink() {
        $controllerLink = Director::baseURL();
        if (empty($controllerLink)) {
            $controllerLink = '/';
        }
        $controllerLink .= Controller::curr()->Link();
        return $controllerLink;
    }
    
    /**
     * Adds custom requirements and returns the field holder
     * 
     * @param array $properties Properties
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.01.2013
     */
    public function FieldHolder($properties = array()) {
        Requirements::themedCSS('SilvercartEditableTableListField');
        return parent::FieldHolder($properties);
    }
    
    /**
     * Returns the batch actions
     *
     * @return ArrayList
     */
    public function getBatchActions() {
        return $this->batchActions;
    }

    /**
     * Sets the batch actions
     *
     * @param ArrayList $batchActions Batch actions
     * 
     * @return void
     */
    public function setBatchActions($batchActions) {
        $this->batchActions = $batchActions;
    }

    /**
     * Sets the batch actions
     *
     * @param ArrayList $batchAction Batch action to add
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.07.2012
     */
    public function addBatchAction($batchAction) {
        if (is_null($this->batchActions)) {
            $this->batchActions = new ArrayList();
        }
        if (!$this->batchActions->find('action', $batchAction['action'])) {
            $this->batchActions->push(new DataObject($batchAction));
        }
    }

    /**
     * Adds some batchs actions
     *
     * @param ArrayList $batchActions Batch actions to add
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.07.2012
     */
    public function addBatchActions($batchActions) {
        foreach ($batchActions as $batchAction) {
            $this->addBatchAction($batchAction);
        }
    }

    /**
     * Get the IDs of the selected items, in a has_many or many_many relation
     * 
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.07.2012
     */
    public function selectedItemIDs() {
        $itemIDs = array();
        if (array_key_exists($this->name, $_POST) &&
            is_array($_POST[$this->name]) &&
            array_key_exists($this->htmlListField, $_POST[$this->name]) &&
            is_array($_POST[$this->name][$this->htmlListField]) &&
            count($_POST[$this->name][$this->htmlListField]) > 0) {
            $itemIDs = $_POST[$this->name][$this->htmlListField];
        }
        return $itemIDs;
    }
    
    /**
     * Adds some extra markup to the template
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.07.2012
     */
    public function ExtraData() {
        $list       = implode(',', $this->selectedItemIDs());
        $inputId    = $this->id() . '_CheckedFields';
        return <<<HTML
        <input id="$inputId" name="{$this->name}[selected]" type="hidden" value="$list"/>
HTML;
    }
    
    /**
     * Returns the total count of items
     * 
     * @return int
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.09.2012
     */
    public function TotalCount() {
        $countColumn = sprintf(
                "DISTINCT \"%s\".\"ID\"",
                $this->sourceClass
        );
        
        if (is_null($this->totalCount)) {
            if ($this->customSourceItems) {
                $this->totalCount = $this->customSourceItems->Count();
            } else {
                $this->totalCount = $this->getQuery()->unlimitedRowCount($countColumn);
            }
        }

        return $this->totalCount;
    }
    
    /**
     * Returns the quick access call back method name
     * 
     * @return string
     */
    public function getQuickAccessCallBack() {
        return $this->quickAccessCallBack;
    }

    /**
     * Sets the quick access call back method name
     * 
     * @param string $quickAccessCallBack Quick access call back method name
     * 
     * @return void
     */
    public function setQuickAccessCallBack($quickAccessCallBack) {
        $this->quickAccessCallBack = $quickAccessCallBack;
    }

    /**
     * Returns the quick access fields template name
     * 
     * @return string
     */
    public function getQuickAccessFieldsTemplate() {
        return $this->quickAccessFieldsTemplate;
    }

    /**
     * Sets the quick access fields template name
     * 
     * @param string $quickAccessFieldsTemplate Quick access fields template name
     * 
     * @return void
     */
    public function setQuickAccessFieldsTemplate($quickAccessFieldsTemplate) {
        $this->quickAccessFieldsTemplate = $quickAccessFieldsTemplate;
    }
    
    /**
     * Returns whether to use the quick access feature for this fields records
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.09.2012
     */
    public function UseQuickAccess() {
        $useQuickAccess = false;
        $sourceObject   = singleton($this->sourceClass);
        if (is_null($this->quickAccessCallBack)) {
            $this->setQuickAccessCallBack('getQuickAccessFields');
        }
        if ($sourceObject->hasMethod($this->quickAccessCallBack)) {
            $useQuickAccess = true;
        }
        return $useQuickAccess;
    }
}

/**
 * Item class to use with SilvercartEditableTableListField
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 24.09.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartEditableTableListField_Item extends TableListField_Item {
    
    /**
     * Returns whether to use the quick access feature for this record
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.09.2012
     */
    public function UseQuickAccess() {
        return $this->Parent()->UseQuickAccess();
    }
    
    /**
     * Returns the quick access data for the template
     * 
     * @return string
     */
    public function getQuickAccessData() {
        $quickAccessData = null;
        if ($this->UseQuickAccess()) {
            $quickAccessCallBackData = $this->item->{$this->Parent()->quickAccessCallBack}();
            if ($quickAccessCallBackData instanceof FieldSet) {
                $quickAccessData = $quickAccessCallBackData->renderWith($this->Parent()->getQuickAccessFieldsTemplate());
            } else {
                $quickAccessData = $quickAccessCallBackData;
            }
        }
        return $quickAccessData;
    }
    
}