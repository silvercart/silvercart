<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\MetaNavigationHolder;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;

/**
 * Page for newsletter (un)subscription.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class NewsletterPage extends MetaNavigationHolder {
    
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = array(
        'UseDoubleOptIn'             => 'Boolean',
        'OptInPageTitle'             => 'Varchar',
        'ConfirmationFailureMessage' => 'HTMLText',
        'ConfirmationSuccessMessage' => 'HTMLText',
        'AlreadyConfirmedMessage'    => 'HTMLText'
    );
    
    /**
     * default values for DB attributes
     *
     * @var array
     */
    private static $defaults = array(
        'UseDoubleOptIn' => true,
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartNewsletterPage';
    
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/metanavigation_page-file.gif";
    
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
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $useDoubleOptInField = new CheckboxField('UseDoubleOptIn', $this->fieldLabel('UseDoubleOptIn'));
        $fields->insertAfter('MenuTitle', $useDoubleOptInField);

        $optInPageTitleField = new TextField('OptInPageTitle', $this->fieldLabel('OptInPageTitle'));
        $confirmationFailureMessageTextField = new HTMLEditorField('ConfirmationFailureMessage', $this->fieldLabel('FailureMessageText'), 20);
        $confirmationSuccessMessageTextField = new HTMLEditorField('ConfirmationSuccessMessage', $this->fieldLabel('SuccessMessageText'), 20);
        $alreadyConfirmedMessageTextField    = new HTMLEditorField('AlreadyConfirmedMessage',    $this->fieldLabel('AlreadyConfirmedMessageText'), 20);

        $fields->addFieldToTab('Root.Main', $optInPageTitleField);
        $fields->addFieldToTab('Root.Main', $confirmationFailureMessageTextField);
        $fields->addFieldToTab('Root.Main', $confirmationSuccessMessageTextField);
        $fields->addFieldToTab('Root.Main', $alreadyConfirmedMessageTextField);
        
        return $fields;
    }
    
    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'UseDoubleOptIn'                    => _t(NewsletterPage::class . '.UseDoubleOptIn', 'Use double opt-in'),
                    'DefaultOptInPageTitle'             => _t(NewsletterPage::class . '.DefaultOptInPageTitle', 'Complete newsletter registration'),
                    'DefaultConfirmationFailureMessage' => _t(NewsletterPage::class . '.DefaultConfirmationFailureMessage', 'Your newsletter registration couldn\'t be completed.'),
                    'DefaultConfirmationSuccessMessage' => _t(NewsletterPage::class . '.DefaultConfirmationSuccessMessage', 'Your newsletter registration was successful! Hopefully our offers will be of good use to you.'),
                    'DefaultAlreadyConfirmedMessage'    => _t(NewsletterPage::class . '.DefaultAlreadyConfirmedMessage', 'Your newsletter registration has been completed already.'),
                    'DefaultEmailConfirmationSubject'   => _t(NewsletterPage::class . '.DefaultEmailConfirmationSubject', 'Complete newsletter registration'),
                    'OptInPageTitle'                    => _t(NewsletterPage::class . '.OptInPageTitle', 'Opt-in page title'),
                    'ConfirmationFailureMessage'        => _t(NewsletterPage::class . '.ConfirmationFailureMessage', 'Message for subscription failure'),
                    'ConfirmationSuccessMessage'        => _t(NewsletterPage::class . '.ConfirmationSuccessMessage', 'Message for subscription success'),
                    'AlreadyConfirmedMessage'           => _t(NewsletterPage::class . '.AlreadyConfirmedMessage', 'Message for already existing subscription'),
                    'FailureMessageText'                => _t(NewsletterPage::class . '.FailureMessageText', 'Failure message'),
                    'SuccessMessageText'                => _t(NewsletterPage::class . '.SuccessMessageText', 'Success message'),
                    'AlreadyConfirmedMessageText'       => _t(NewsletterPage::class . '.AlreadyConfirmedMessageText', 'Message: user completed opt-in already'),
                )
        );
    }
    
}