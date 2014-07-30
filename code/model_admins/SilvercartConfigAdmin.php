<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage ModelAdmins
 */

/**
 * ModelAdmin for SilvercartConfig.
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 16.01.2012
 * @license see license file in modules root directory
 */
class SilvercartConfigAdmin extends SilvercartLeftAndMain {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'config';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSortIndex = 10;

    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-config';

    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Configuration';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'SilvercartConfig' => array(
            'title'                    => 'SilvercartConfig',
            'enableFirstEntryAutoLoad' => true
        ),
    );

    /**
     * List of managed models with disabled creation and import form
     *
     * @var array
     */
    public static $disable_creation_and_import_for = array(
        'SilvercartConfig',
    );

    /**
     * List of managed models with resetted search form
     *
     * @var array
     */
    public static $reset_search_form_for = array(
        'SilvercartConfig',
    );
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @param bool $skipUpdateInit Set to true to skip the parents updateInit extension
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.02.2013
     */
    public function init($skipUpdateInit = false) {
        parent::init($skipUpdateInit);

        Requirements::css(CMS_DIR . '/css/WidgetAreaEditor.css');
        Requirements::javascript(CMS_DIR . '/javascript/WidgetAreaEditor.js');

        $this->extend('updateInit');
    }
    
    /**
     * Builds and returns the edit form
     * 
     * @param int       $id     Not used. Available because of inheritance.
     * @param FieldList $fields Not used. Available because of inheritance.
     * 
     * @return Form
     */
    public function getEditForm($id = null, $fields = null) {
        $config     = SilvercartConfig::getConfig();
        $fields     = $config->getCMSFields();
        $actions    = $config->getCMSActions();
        
        $form = new Form($this, 'EditForm', $fields, $actions);
        $form->addExtraClass('root-form');
        $form->addExtraClass('cms-edit-form center cms-tabset');
        // don't add data-pjax-fragment=CurrentForm, its added in the content template instead

        if ($form->Fields()->hasTabset()) {
            $form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');
        }
        $form->setHTMLID('Form_EditForm');
        $form->loadDataFrom($config);
        $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));

        // Use <button> to allow full jQuery UI styling
        $actionFields = $actions->dataFields();
        if ($actionFields) {
            foreach ($actionFields as $action) {
                $action->setUseButtonTag(true);
            }
        }

        $this->extend('updateEditForm', $form);

        return $form;
    }

    /**
     * Save the current sites {@link SiteConfig} into the database
     *
     * @param array $data Data to save
     * @param Form  $form Form to extract data from
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.02.2013
     */
    public function save_scconfig($data, $form) {
        $config = SilvercartConfig::getConfig();
        $form->saveInto($config);
        $config->write();

        $this->response->addHeader('X-Status', rawurlencode(_t('LeftAndMain.SAVEDUP', 'Saved.')));
        return $this->getResponseNegotiator()->respond($this->request);
    }
    
    /**
     * Adds example data to SilverCart when triggered in ModelAdmin.
     *
     * @return SS_HTTPResponse 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.02.2013
     */
    public function add_example_data() {
        SilvercartConfig::enableTestData();
        $result = SilvercartRequireDefaultRecords::createTestData();
        if ($result) {
            $responseText   = _t('SilvercartConfig.ADDED_EXAMPLE_DATA');
        } else {
            $responseText   = _t('SilvercartConfig.EXAMPLE_DATA_ALREADY_ADDED');
        }
        $this->response->addHeader('X-Status', rawurlencode($responseText));
        return $this->getResponseNegotiator()->respond($this->request);
    }
    
    /**
     * Adds example configuration to SilverCart when triggered in ModelAdmin.
     *
     * @return SS_HTTPResponse 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function add_example_config() {
        SilvercartConfig::enableTestData();
        $result = SilvercartRequireDefaultRecords::createTestConfiguration();
        if ($result) {
            $responseText   = _t('SilvercartConfig.ADDED_EXAMPLE_CONFIGURATION');
        } else {
            $responseText   = _t('SilvercartConfig.EXAMPLE_CONFIGURATION_ALREADY_ADDED');
        }
        $this->response->addHeader('X-Status', rawurlencode($responseText));
        return $this->getResponseNegotiator()->respond($this->request);
    }
    
}
