<?php

/**
 * shows and processes a registration form;
 * configuration of registration mails;
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 20.10.2010
 * @license LGPL
 */
class SilvercartRegistrationPage extends Page {

    public static $singular_name = "";
    public static $db = array(
        'ActivationMailSubject' => 'Varchar(255)',
        'ActivationMailMessage' => 'HTMLText'
    );
    public static $defaults = array(
    );

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
     * Return all fields of the backend
     *
     * @return FieldSet Fields of the CMS
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $activationMailSubjectField = new TextField('ActivationMailSubject', _t('SilvercartRegistrationPage.ACTIVATION_MAIL_SUBJECT', 'activation mail subject'));
        $activationMailTextField = new HtmlEditorField('ActivationMailMessage', _t('SilvercartRegistrationPage.ACTIVATION_MAIL_TEXT', 'activation mail text'), 20);
        $tabParam = "Root.Content." . _t('SilvercartRegistrationPage.ACTIVATION_MAIL', 'activation mail');
        $fields->addFieldToTab($tabParam, $activationMailSubjectField);
        $fields->addFieldToTab($tabParam, $activationMailTextField);

        return $fields;
    }

}

/**
 * Controller of this page type
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license LGPL
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
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
        $member = Member::currentUser();
        if ($member) {
            $member->logOut();
        }
        $this->registerCustomHtmlForm('SilvercartRegisterRegularCustomerForm', new SilvercartRegisterRegularCustomerForm($this));
        parent::init();
    }

}