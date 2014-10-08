<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * shows and processes a registration form;
 * configuration of registration mails;
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 20.10.2010
 * @license see license file in modules root directory
 */
class SilvercartRegistrationPage extends Page {
    
    /**
     * attributes
     *
     * @var array
     */
    private static $db = array(
        'ActivationMailSubject' => 'Varchar(255)',
        'ActivationMailMessage' => 'HTMLText'
    );
    
    /**
     * default values
     *
     * @var array
     */
    private static $defaults = array();
    
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    private static $icon = "silvercart/images/page_icons/metanavigation_page";

    /**
     * Constructor
     *
     * @param array|null $record      This will be null for a new database record.  Alternatively, you can pass an array of
     *                                field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                don't have their defaults set.
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 2.2.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$defaults = array(
            'ActivationMailSubject' => _t('SilvercartRegistrationPage.YOUR_REGISTRATION', 'your registration'),
            'ActivationMailMessage' => _t('SilvercartRegistrationPage.CUSTOMER_SALUTATION', 'Dear customer\,')
            );
        parent::__construct($record, $isSingleton);
    }
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),             array(
                    'ActivationMailSubject' => _t('SilvercartRegistrationPage.ACTIVATION_MAIL_SUBJECT'),
                    'ActivationMailMessage' => _t('SilvercartRegistrationPage.ACTIVATION_MAIL_TEXT'),
                    'ActivationMailTab'     => _t('SilvercartRegistrationPage.ACTIVATION_MAIL_TAB')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    

    /**
     * Return all fields of the backend
     *
     * @return FieldList Fields of the CMS
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $activationMailSubjectField = new TextField('ActivationMailSubject', $this->fieldLabel('ActivationMailSubject'));
        $activationMailTextField = new HtmlEditorField('ActivationMailMessage', $this->fieldLabel('ActivationMailMessage'), 20);
        $tabParam = "Root." . $this->fieldLabel('ActivationMailTab');
        $fields->addFieldToTab($tabParam, $activationMailSubjectField);
        $fields->addFieldToTab($tabParam, $activationMailTextField);

        return $fields;
    }
}

/**
 * Controller of this page type
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license see license file in modules root directory
 * @since 19.10.2010
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartRegistrationPage_Controller extends Page_Controller {

    /**
     * initialisation of the form object
     * logged in members get logged out
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de> Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     * @return void
     */
    public function init() {
        if (SilvercartConfig::EnableSSL()) {
            Director::forceSSL();
        }
        
        $this->registerCustomHtmlForm('SilvercartRegisterRegularCustomerForm', new SilvercartRegisterRegularCustomerForm($this));
        parent::init();
    }
    
    /**
     * Returns whether the registration context is in checkout.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.05.2014
     */
    public function IsInCheckout() {
        $isInCheckout = false;
        if (array_key_exists('backlink', $_GET) &&
            !empty($_GET['backlink'])) {
            $backlink       = Convert::raw2sql($_GET['backlink']);
            $backlinkedPage = Page::get_by_link($backlink);
            if ($backlinkedPage instanceof SilvercartCheckoutStep) {
                $isInCheckout = true;
            }
        }
        return $isInCheckout;
    }

    /**
     * Returns the current form instance object
     *
     * @return CustomHtmlForm
     */
    public function getCheckoutFormInstance() {
        $checkoutStepPage = SilvercartTools::PageByIdentifierCode('SilvercartCheckoutStep');
        $checkoutStepPageController = ModelAsController::controller_for($checkoutStepPage);
        $checkoutStepPageController->handleRequest($this->getRequest());
        return new SilvercartCheckoutFormStep2($checkoutStepPageController);
    }
    
}
