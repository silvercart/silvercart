<?php

namespace SilverCart\Model\Pages;

use SilverCart\Model\Pages\MetaNavigationHolder;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;

/**
 * Page for newsletter (un)subscription.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property bool   $UseDoubleOptIn             Use Double Opt In
 * @property string $OptInPageTitle             Opt In Page Title
 * @property string $ConfirmationFailureMessage Confirmation Failure Message
 * @property string $ConfirmationSuccessMessage Confirmation Success Message
 * @property string $AlreadyConfirmedMessage    Already Confirmed Message
 */
class NewsletterPage extends MetaNavigationHolder
{
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = [
        'UseDoubleOptIn'             => 'Boolean',
        'OptInPageTitle'             => 'Varchar',
        'ConfirmationFailureMessage' => 'HTMLText',
        'ConfirmationSuccessMessage' => 'HTMLText',
        'AlreadyConfirmedMessage'    => 'HTMLText'
    ];
    /**
     * default values for DB attributes
     *
     * @var array
     */
    private static $defaults = [
        'UseDoubleOptIn' => true,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartNewsletterPage';
    /**
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     * 
     * @var string
     */
    private static $icon_class = 'font-icon-p-mail';
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $useDoubleOptInField = CheckboxField::create('UseDoubleOptIn', $this->fieldLabel('UseDoubleOptIn'));
            $fields->insertAfter('MenuTitle', $useDoubleOptInField);

            $optInPageTitleField = TextField::create('OptInPageTitle', $this->fieldLabel('OptInPageTitle'));
            $confirmationFailureMessageTextField = HTMLEditorField::create('ConfirmationFailureMessage', $this->fieldLabel('FailureMessageText'), 20);
            $confirmationSuccessMessageTextField = HTMLEditorField::create('ConfirmationSuccessMessage', $this->fieldLabel('SuccessMessageText'), 20);
            $alreadyConfirmedMessageTextField    = HTMLEditorField::create('AlreadyConfirmedMessage',    $this->fieldLabel('AlreadyConfirmedMessageText'), 20);

            $fields->addFieldToTab('Root.Main', $optInPageTitleField);
            $fields->addFieldToTab('Root.Main', $confirmationFailureMessageTextField);
            $fields->addFieldToTab('Root.Main', $confirmationSuccessMessageTextField);
            $fields->addFieldToTab('Root.Main', $alreadyConfirmedMessageTextField);
        });
        return parent::getCMSFields();
    }
    
    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return array_merge(
                parent::fieldLabels($includerelations),
                [
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
                ]
        );
    }
}