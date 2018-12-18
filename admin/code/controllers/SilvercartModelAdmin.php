<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @license see license file in modules root directory
 */
class SilvercartModelAdmin extends ModelAdmin {
    
    /**
     * Allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = array(
        'handleBatchCallback',
    );

    /**
     * Menu icon
     *
     * @var string
     */
    private static $menu_icon = 'silvercart/img/glyphicons-halflings.png';
    
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
     * If this is set to true the ModelAdmins SearchForm will be collapsed on
     * load.
     *
     * @var bool
     */
    protected static $search_form_is_collapsed = true;

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
        
        if (class_exists('GridFieldOrderableRows')
         && !empty($sortable_field)
        ) {
            $this->getGridFieldConfig($form)->addComponent(new GridFieldOrderableRows($sortable_field));
        } elseif (class_exists('GridFieldSortableRows')
         && !empty($sortable_field)
        ) {
            $this->getGridFieldConfig($form)->addComponent(new GridFieldSortableRows($sortable_field));
        }
        if (SilvercartGridFieldBatchController::hasBatchActionsFor($this->sanitiseClassName($this->modelClass))) {
            $this->getGridFieldConfig($form)->addComponent(new SilvercartGridFieldBatchController($this->sanitiseClassName($this->modelClass)));
        }
        if (singleton($this->modelClass)->hasMethod('getQuickAccessFields')) {
            $this->getGridFieldConfig($form)->addComponent(new SilvercartGridFieldQuickAccessController());
        }
        
        $this->extend('updateSilvercartEditForm', $form);
        
        return $form;
    }
    
    /**
     * Returns the CSS class to use for the SearchForms collapse state.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.03.2014
     */
    public function SearchFormCollapseClass() {
        $collapseClass = '';
        if (self::$search_form_is_collapsed) {
            $collapseClass = 'collapsed';
        }
        return $collapseClass;
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
 * @license see license file in modules root directory
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
 * @license see license file in modules root directory
 */
interface SilvercartModelAdmin_ExclusiveRelationInterface {
    
}