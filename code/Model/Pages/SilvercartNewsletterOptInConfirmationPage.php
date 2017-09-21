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
 * Confirmation page for newsletter opt-in confirmation.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 * @since 25.08.2011
 */
class SilvercartNewsletterOptInConfirmationPage extends Page {
    
    /**
     * Attributes
     *
     * @var array
     */
    public static $db = array(
        'ConfirmationFailureMessage' => 'HTMLText',
        'ConfirmationSuccessMessage' => 'HTMLText',
        'AlreadyConfirmedMessage'    => 'HTMLText'
    );
    
    /**
     * The icon to use for this page in the storeadmin sitetree.
     *
     * @var string
     */
    public static $icon = "silvercart/img/page_icons/metanavigation_page";
    
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
     * Return all fields of the backend
     *
     * @return FieldList Fields of the CMS
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $confirmationFailureMessageTextField = new HtmlEditorField('ConfirmationFailureMessage', _t('SilvercartNewsletterOptInConfirmationPage.FAILURE_MESSAGE_TEXT'), 20);
        $confirmationSuccessMessageTextField = new HtmlEditorField('ConfirmationSuccessMessage', _t('SilvercartNewsletterOptInConfirmationPage.SUCCESS_MESSAGE_TEXT'), 20);
        $alreadyConfirmedMessageTextField    = new HtmlEditorField('AlreadyConfirmedMessage',    _t('SilvercartNewsletterOptInConfirmationPage.ALREADY_CONFIRMED_MESSAGE_TEXT'), 20);

        $fields->addFieldToTab('Root.Main', $confirmationFailureMessageTextField);
        $fields->addFieldToTab('Root.Main', $confirmationSuccessMessageTextField);
        $fields->addFieldToTab('Root.Main', $alreadyConfirmedMessageTextField);

        return $fields;
    }
}

/**
 * Confirmation page for newsletter opt-in confirmation.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 * @since 25.08.2011
 */
class SilvercartNewsletterOptInConfirmationPage_Controller extends Page_Controller {

    /**
     * Final step for the newsletter opt-in.
     *
     * @return array message for the template
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.06.2014
     */
    public function doConfirmation() {
        $statusMessage      = $this->ConfirmationFailureMessage;
        $anonymousCustomer  = true;

        if (isset($_GET['h'])) {
            $hash = Convert::raw2sql(urldecode($_GET['h']));

            if ($hash) {
                $recipient = SilvercartAnonymousNewsletterRecipient::getByHash($hash);
                
                if ($recipient) {
                    $customer = $recipient;
                } else {
                    $customer = Member::get()->filter('NewsletterConfirmationHash', $hash)->first();
                    
                    if ($customer) {
                        $anonymousCustomer = false;
                    }
                }
                
                if ($customer) {
                    if ($customer->NewsletterOptInStatus) {
                        $statusMessage = $this->AlreadyConfirmedMessage;
                    } else {
                        if ($anonymousCustomer) {
                            SilvercartAnonymousNewsletterRecipient::doConfirmationByHash($hash);
                        } else {
                            $customer->NewsletterOptInStatus  = true;
                            $customer->SubscribedToNewsletter = true;
                            $customer->write();
                        }
                        $statusMessage = $this->ConfirmationSuccessMessage;
                        
                        $this->sendConfirmationMail(
                            $customer->Salutation,
                            $customer->FirstName,
                            $customer->Surname,
                            $customer->Email
                        );
                    }
                }
            }
        }

        return $this->customise(array(
            'message' => $statusMessage
        ));
    }

    /**
     * Send confirmation mail to customer
     *
     * @param Customer $salutation Das Kundenobjekt
     * @param string   $firstName  ...
     * @param string   $surName    ...
     * @param string   $email      ...
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.10.2010
     */
    public function sendConfirmationMail($salutation, $firstName, $surName, $email) {
        SilvercartShopEmail::send(
            'NewsletterOptInConfirmation',
            $email,
            array(
                'Salutation' => $salutation,
                'FirstName'  => $firstName,
                'Surname'    => $surName,
                'Email'      => $email
            )
        );
    }
}
