<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Forms\GridField\GridFieldBatchController;
use SilverCart\Admin\Forms\GridField\GridFieldQuickAccessController;
use SilverCart\Dev\Tools;
use SilverStripe\Admin\ModelAdmin as SilverStripeModelAdmin;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Member;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;
use function singleton;

/**
 * ModelAdmin extension for SilverCart.
 * Provides some special functions for SilverCarts admin area.
 * 
 * @package SilverCart
 * @subpackage Admin\Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class ModelAdmin extends SilverStripeModelAdmin
{
    /**
     * Allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'handleBatchCallback',
    ];
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
     * The default CSV export delimiter character.
     * 
     * @var string
     */
    private static $csv_export_delimiter = ',';
    /**
     * The default CSV export enclosure character.
     * 
     * @var string
     */
    private static $csv_export_enclosure = '"';
    /**
     * Determines whether the CSV export file is generated with a header line.
     * 
     * @var string
     */
    private static $csv_export_has_header = true;
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
     * @return void
     */
    protected function init()
    {
        parent::init();
        $this->extend('updateInit');
    }
    
    /**
     * Allows user code to hook into ModelAdmin::init() prior to updateInit 
     * being called on extensions.
     *
     * @param callable $callback The callback to execute
     * 
     * @return void
     */
    protected function beforeUpdateInit($callback)
    {
        $this->beforeExtending('updateInit', $callback);
    }
    
    /**
     * Allows user code to hook into ModelAdmin::getEditForm() prior to 
     * updateEditForm being called on extensions.
     *
     * @param callable $callback The callback to execute
     * 
     * @return void
     */
    protected function beforeUpdateEditForm($callback)
    {
        $this->beforeExtending('updateEditForm', $callback);
    }

    /**
     * title in the top bar of the CMS
     *
     * @return string 
     */
    public function SectionTitle()
    {
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
    public function getEditForm($id = null, $fields = null) : Form
    {
        $this->beforeUpdateEditForm(function(Form $form) {
            $grid           = $this->getGridFieldFor($form);
            $config         = $this->getGridFieldConfigFor($form);
            $sortable_field = $this->stat('sortable_field');
            $model          = singleton($this->modelClass);
            if ($model->hasField($sortable_field)) {
                if (class_exists(GridFieldOrderableRows::class)
                 && !empty($sortable_field)
                ) {
                    $config->addComponent(new GridFieldOrderableRows($sortable_field));
                } elseif (class_exists(GridFieldSortableRows::class)
                 && !empty($sortable_field)
                ) {
                    $config->addComponent(new GridFieldSortableRows($sortable_field));
                }
            }
            if (GridFieldBatchController::hasBatchActionsFor($this->modelClass)) {
                $config->addComponent(new GridFieldBatchController($this->modelClass, 'buttons-before-left'));
            }
            if ($model->hasMethod('getQuickAccessFields')) {
                $config->addComponent(new GridFieldQuickAccessController());
            }
            if ($model->hasMethod('getGridFieldDescription')) {
                $grid->setDescription($model->getGridFieldDescription());
            }
            $exportButton = $config->getComponentByType(GridFieldExportButton::class);
            if ($exportButton instanceof GridFieldExportButton) {
                $exportButton->setCsvSeparator($this->config()->csv_export_delimiter);
                $exportButton->setCsvEnclosure($this->config()->csv_export_enclosure);
                $exportButton->setCsvHasHeader($this->config()->csv_export_has_header);
            }
        });
        return parent::getEditForm($id, $fields);
    }
    
    /**
     * Adds the possibility to update the tabs by decorator.
     * 
     * @return ArrayList
     */
    protected function getManagedModelTabs() : ArrayList
    {
        $forms = parent::getManagedModelTabs();
        $this->extend('updateManagedModelTabs', $forms);
        return $forms;
    }
    
    /**
     * Returns the CSS class to use for the SearchForms collapse state.
     * 
     * @return string
     */
    public function SearchFormCollapseClass() : string
    {
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
     */
    public function handleBatchCallback(HTTPRequest $request)
    {
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
    public function getGridFieldFor(Form $form) : GridField
    {
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
    public function getGridFieldConfigFor(Form $form) : GridFieldConfig
    {
        if (is_null($this->gridFieldConfig)) {
            $this->gridFieldConfig = $this->getGridFieldFor($form)->getConfig();
        }
        return $this->gridFieldConfig;
    }
    
    /**
     * Workaround to hide this class in CMS menu.
     * 
     * @param Member $member Member
     * 
     * @return bool
     */
    public function canView($member = null) : bool
    {
        if (get_class($this) === ModelAdmin::class) {
            return false;
        }
        return parent::canView($member);
    }
}