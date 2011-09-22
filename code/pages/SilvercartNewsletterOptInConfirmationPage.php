<?php
/**
 * Copyright 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
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
 * @copyright Pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 25.08.2011
 */
class SilvercartNewsletterOptInConfirmationPage extends Page {

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public function singular_name() {
        if (_t('SilvercartNewsletterOptInConfirmationPage.SINGULARNAME')) {
            return _t('SilvercartNewsletterOptInConfirmationPage.SINGULARNAME');
        } else {
            return parent::singular_name();
        } 
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public function plural_name() {
        if (_t('SilvercartNewsletterOptInConfirmationPage.PLURALNAME')) {
            return _t('SilvercartNewsletterOptInConfirmationPage.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
    }
    
    /**
     * Attributes
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
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
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public static $icon = "silvercart/images/page_icons/registration_confirmation";

    /**
     * Return all fields of the backend
     *
     * @return FieldSet Fields of the CMS
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $confirmationFailureMessageTextField = new HtmlEditorField('ConfirmationFailureMessage', _t('SilvercartNewsletterOptInConfirmationPage.FAILURE_MESSAGE_TEXT'), 20);
        $confirmationSuccessMessageTextField = new HtmlEditorField('ConfirmationSuccessMessage', _t('SilvercartNewsletterOptInConfirmationPage.SUCCESS_MESSAGE_TEXT'), 20);
        $alreadyConfirmedMessageTextField    = new HtmlEditorField('AlreadyConfirmedMessage',    _t('SilvercartNewsletterOptInConfirmationPage.ALREADY_CONFIRMED_MESSAGE_TEXT'), 20);

        $fields->addFieldToTab('Root.Content.Main', $confirmationFailureMessageTextField);
        $fields->addFieldToTab('Root.Content.Main', $confirmationSuccessMessageTextField);
        $fields->addFieldToTab('Root.Content.Main', $alreadyConfirmedMessageTextField);

        return $fields;
    }
}

/**
 * Confirmation page for newsletter opt-in confirmation.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 25.08.2011
 */
class SilvercartNewsletterOptInConfirmationPage_Controller extends Page_Controller {

    /**
     * Final step for the newsletter opt-in.
     *
     * @return array message for the template
     * 
     * @author Sascha Köhler <skoehler@pixeltricks.de>
     * @since 25.08.2011
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
                    $customer = DataObject::get_one(
                        'Member',
                        sprintf(
                            "NewsletterConfirmationHash = '%s'",
                            $hash
                        )
                    );
                    
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
                            $customer->setField('NewsletterOptInStatus', true);
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
     * @param Customer $customer Das Kundenobjekt
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
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
