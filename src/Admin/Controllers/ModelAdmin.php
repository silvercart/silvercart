<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Dev\Tools;
use SilverCart\Admin\Forms\GridField\GridFieldBatchController;
use SilverCart\Admin\Forms\GridField\GridFieldQuickAccessController;
use SilverStripe\Control\HTTPRequest;

/**
 * ModelAdmin extension for SilverCart.
 * Provides some special functions for SilverCarts admin area.
 * 
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class ModelAdmin extends \SilverStripe\Admin\ModelAdmin {
    
    /**
     * Allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = array(
        'handleBatchCallback',
    );

    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart';

    /**
     * Menu icon
     *
     * @var string
     */
    private static $menu_icon = 'silvercart/silvercart:client/img/glyphicons-halflings.png';
    
    /**
     * Name of DB field to make records sortable by.
     *
     * @var string
     */
    private static $sortable_field = '';
    
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
    protected function init($skipUpdateInit = false) {
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.10.2017
     */
    public function SectionTitle() {
        $sectionTitle = parent::SectionTitle();
        if (class_exists($this->modelClass)) {
            $sectionTitle = Tools::plural_name_for(singleton($this->modelClass));
        }
        return $sectionTitle;
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
        
        if (class_exists('GridFieldSortableRows') &&
            !empty($sortable_field)) {
            $this->getGridFieldConfig($form)->addComponent(new GridFieldSortableRows($sortable_field));
        }
        if (GridFieldBatchController::hasBatchActionsFor($this->modelClass)) {
            $this->getGridFieldConfig($form)->addComponent(new GridFieldBatchController($this->modelClass));
        }
        if (singleton($this->modelClass)->hasMethod('getQuickAccessFields')) {
            $this->getGridFieldConfig($form)->addComponent(new GridFieldQuickAccessController());
        }
        
        $this->extend('updateEditForm', $form);
        
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
     * @param HTTPRequest $request Request to handle
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function handleBatchCallback(HTTPRequest $request) {
        $result = '';
        if (GridFieldBatchController::hasBatchActionsFor($this->modelClass)) {
            $result = GridFieldBatchController::handleBatchCallback($this->modelClass, $request);
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
    
    /**
     * Workaround to hide this class in CMS menu.
     * 
     * @param Member $member Member
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.10.2017
     */
    public function canView($member = null) {
        if (get_class($this) == ModelAdmin::class) {
            return false;
        }
        return parent::canView($member);
    }
}