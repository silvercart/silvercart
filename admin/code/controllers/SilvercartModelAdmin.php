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
 * @subpackage Admin_Controllers
 */

/**
 * ModelAdmin extension for SilverCart.
 * Provides some special functions for SilverCarts admin area.
 * 
 * @package Silvercart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 19.02.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartModelAdmin extends ModelAdmin {
    
    /**
     * Name of DB field to make records sortable by.
     *
     * @var string
     */
    public static $sortable_field = '';
    
    /**
     * GridField of the edit form
     *
     * @var GridField
     */
    protected $gridField = null;
    
    /**
     * GridFieldConfig of the edit form
     *
     * @var GridFieldConfig
     */
    protected $gridFieldConfig = null;
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @param bool $skipUpdateInit Set to true to skip the parents updateInit extension
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.02.2013
     */
    public function init($skipUpdateInit = false) {
        parent::init();
        if (!$skipUpdateInit) {
            $this->extend('updateInit');
        }
    }

    /**
     * title in the top bar of the CMS
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.02.2013
     */
    public function SectionTitle() {
        return _t($this->sanitiseClassName($this->modelClass) . '.PLURALNAME');
    }
    
    /**
     * Builds and returns the edit form.
     * 
	 * @param int       $id     The current records ID. Won't be used for ModelAdmins.
	 * @param FieldList $fields Fields to use. Won't be used for ModelAdmins.
     * 
     * @return Form
     */
    public function getEditForm($id = null, $fields = null) {
        $form           = parent::getEditForm($id, $fields);
        $sortable_field = $this->stat('sortable_field');
        
        $this->getGridFieldConfig($form)
                ->removeComponentsByType('GridFieldDataColumns')
                ->removeComponentsByType('GridFieldEditButton')
                ->removeComponentsByType('GridFieldDeleteAction')
                ->addComponent(new SilvercartGridFieldDataColumns())
                ->addComponent(new GridFieldEditButton())
                ->addComponent(new GridFieldDeleteAction());
        
        if (class_exists('GridFieldSortableRows') &&
            !empty($sortable_field)) {
            $this->getGridFieldConfig($form)->addComponent(new GridFieldSortableRows($sortable_field));
        }
        if (SilvercartGridFieldBatchController::hasBatchActionsFor($this->sanitiseClassName($this->modelClass))) {
            $this->getGridFieldConfig($form)->addComponent(new SilvercartGridFieldBatchController($this->sanitiseClassName($this->modelClass)));
        }
        if (singleton($this->modelClass)->hasMethod('getQuickAccessFields')) {
            $this->getGridFieldConfig($form)->addComponent(new SilvercartGridFieldQuickAccessController());
        }
        return $form;
    }
    
    /**
     * Handles a batch action
     * 
     * @param SS_HTTPRequest $request Request to handle
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function handleBatchCallback(SS_HTTPRequest $request) {
        $result = '';
        if (SilvercartGridFieldBatchController::hasBatchActionsFor($this->sanitiseClassName($this->modelClass))) {
            $result = SilvercartGridFieldBatchController::handleBatchCallback($this->sanitiseClassName($this->modelClass), $request);
        }
        return $result;
    }

    /**
     * Returns the GridField of the given edit form
     * 
     * @param Form $form The edit form to get GridField for
     * 
     * @return GridField
     */
    public function getGridField($form) {
        if (is_null($this->gridField)) {
            $this->gridField = $form->Fields()->dataFieldByName($this->sanitiseClassName($this->modelClass));
        }
        return $this->gridField;
    }
    
    /**
     * Returns the GridFieldConfig of the given edit form
     * 
     * @param Form $form The edit form to get GridField for
     * 
     * @return GridFieldConfig
     */
    public function getGridFieldConfig($form) {
        if (is_null($this->gridFieldConfig)) {
            $this->gridFieldConfig = $this->getGridField($form)->getConfig();
        }
        return $this->gridFieldConfig;
    }
}

/**
 * This interface marks a DataObject to use SilvercartGridFieldConfig_Readonly 
 * in the CMS fields of a related one.
 * 
 * @package Silvercart
 * @subpackage Admin_Interfaces
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.03.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
interface SilvercartModelAdmin_ReadonlyInterface {
    
}

/**
 * This interface marks a DataObject to use 
 * SilvercartGridFieldConfig_ExclusiveRelationEditor in the CMS fields of a
 * related one.
 * 
 * @package Silvercart
 * @subpackage Admin_Interfaces
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.03.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
interface SilvercartModelAdmin_ExclusiveRelationInterface {
    
}