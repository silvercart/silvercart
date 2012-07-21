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
     * Template to use for this field
     * 
     * @var $template string
     */
    protected $template = "SilvercartEditableTableListField";
    
    /**
     * List of available batch actions
     *
     * @var DataObjectSet
     */
    protected $batchActions = null;

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
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.07.2012
     */
    public function FieldHolder() {
        Requirements::themedCSS('SilvercartEditableTableListField');
        return parent::fieldHolder();
    }
    
    /**
     * Returns the batch actions
     *
     * @return DataObjectSet
     */
    public function getBatchActions() {
        return $this->batchActions;
    }

    /**
     * Sets the batch actions
     *
     * @param DataObjectSet $batchActions Batch actions
     * 
     * @return void
     */
    public function setBatchActions($batchActions) {
        $this->batchActions = $batchActions;
    }

    /**
     * Sets the batch actions
     *
     * @param DataObjectSet $batchAction Batch action to add
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.07.2012
     */
    public function addBatchAction($batchAction) {
        if (is_null($this->batchActions)) {
            $this->batchActions = new DataObjectSet();
        }
        if (!$this->batchActions->find('action', $batchAction['action'])) {
            $this->batchActions->push(new DataObject($batchAction));
        }
    }

    /**
     * Adds some batchs actions
     *
     * @param DataObjectSet $batchActions Batch actions to add
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
}