<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

/**
 * shows and processes a registration form;
 * configuration of registration mails;
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class RegistrationPage extends \Page {

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartRegistrationPage';
    
    /**
     * attributes
     *
     * @var array
     */
    private static $db = array(
        'ActivationMailSubject' => 'Varchar(255)',
        'ActivationMailMessage' => 'HTMLText',
        'WelcomeContent' => 'HTMLText',
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
    private static $icon = "silvercart/silvercart:client/img/page_icons/metanavigation_page-file.gif";

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
            'ActivationMailSubject' => $this->fieldLabel('DefaultActivationMailSubject'),
            'ActivationMailMessage' => $this->fieldLabel('DefaultActivationMailMessage'),
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
        return Tools::singular_name_for($this);
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
        return Tools::plural_name_for($this); 
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
                parent::fieldLabels($includerelations),
                array(
                    'ActivationMailSubject'        => _t(RegistrationPage::class . '.ACTIVATION_MAIL_SUBJECT', 'Activation mail subject'),
                    'ActivationMailMessage'        => _t(RegistrationPage::class . '.ACTIVATION_MAIL_TEXT', 'Activation mail text'),
                    'ActivationMailTab'            => _t(RegistrationPage::class . '.ACTIVATION_MAIL_TAB', 'Activation Mail'),
                    'DefaultActivationMailSubject' => _t(RegistrationPage::class . '.YOUR_REGISTRATION', 'your registration'),
                    'DefaultActivationMailMessage' => _t(RegistrationPage::class . '.CUSTOMER_SALUTATION', 'Dear customer,'),
                    'WelcomeContent'               => _t(RegistrationPage::class . '.WelcomeContent', 'Content of the welcome page'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    

    /**
     * Return all fields of the backend
     *
     * @return FieldList Fields of the CMS
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $welcomeContentField = new HTMLEditorField('WelcomeContent', $this->fieldLabel('WelcomeContent'));
        $fields->addFieldToTab('Root.Main', $welcomeContentField);
        
        $activationMailSubjectField = new TextField('ActivationMailSubject', $this->fieldLabel('ActivationMailSubject'));
        $activationMailTextField = new HTMLEditorField('ActivationMailMessage', $this->fieldLabel('ActivationMailMessage'), 20);
        $tabParam = "Root." . $this->fieldLabel('ActivationMailTab');
        $fields->addFieldToTab($tabParam, $activationMailSubjectField);
        $fields->addFieldToTab($tabParam, $activationMailTextField);

        return $fields;
    }
}