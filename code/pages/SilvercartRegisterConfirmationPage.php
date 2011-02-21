<?php
/*
 * Copyright 2010, 2011 pixeltricks GmbH
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
 */

/**
 * Confirmation page for Closed-Opt-In
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 20.10.2010
 */
class SilvercartRegisterConfirmationPage extends Page {

    public static $singular_name = "register confirmation page";
    public static $db = array(
        'ConfirmationFailureMessage' => 'HTMLText',
        'ConfirmationSuccessMessage' => 'HTMLText',
        'AlreadyConfirmedMessage' => 'HTMLText'
    );

    /**
     * Return all fields of the backend
     *
     * @return FieldSet Fields of the CMS
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $confirmationFailureMessageTextField = new HtmlEditorField('ConfirmationFailureMessage', _t('SilvercartRegisterConfirmationPage.FAILURE_MESSAGE_TEXT', 'failure message'), 20);
        $confirmationSuccessMessageTextField = new HtmlEditorField('ConfirmationSuccessMessage', _t('SilvercartRegisterConfirmationPage.SUCCESS_MESSAGE_TEXT', 'success message'), 20);
        $alreadyConfirmedMessageTextField = new HtmlEditorField('AlreadyConfirmedMessage', _t('SilvercartRegisterConfirmationPage.ALREADY_REGISTERES_MESSAGE_TEXT', 'message: user already registered'), 20);

        $fields->addFieldToTab('Root.Content.Main', $confirmationFailureMessageTextField);
        $fields->addFieldToTab('Root.Content.Main', $confirmationSuccessMessageTextField);
        $fields->addFieldToTab('Root.Content.Main', $alreadyConfirmedMessageTextField);

        return $fields;
    }
}

/**
 * Controller of this page type
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartRegisterConfirmationPage_Controller extends Page_Controller {

    /**
     * statments to be  executed on initialisation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.10.2010
     * @return void
     */
    public function init() {
        parent::init();
    }

    /**
     * Final step for customer registration
     *
     * @return array message for the template
     * @author Sascha Köhler <skoehler@pixeltricks.de>
     * @since 20.10.2010
     */
    public function doConfirmation() {
        $statusMessage = $this->ConfirmationFailureMessage;

        if (isset($_GET['h'])) {
            $hash = Convert::raw2sql(urldecode(mysql_real_escape_string($_GET['h'])));

            if ($hash) {
                $customer = DataObject::get_one(
                    'Member',
                    'ConfirmationHash LIKE \''.$hash.'\''
                );

                // Dem Kunde wird eine endgueltige Bestaetigungsmail geschickt.
                if ($customer) {
                    if ($customer->OptInStatus == 1) {
                        $statusMessage = $this->AlreadyConfirmedMessage;
                    } else {
                        $customer->setField('ConfirmationDate', date('Y-m-d H:i:s', mktime()));
                        $customer->setField('OptInStatus', true);
                        $customer->write();

                        // Remove customer from intermediate group
                        $customerGroup = DataObject::get_one(
                                        'Group',
                                        "code LIKE 'b2c-optin'"
                        );
                        $customer->Groups()->remove($customerGroup);

                        // Add customer to group with confirmed members
                        $customerGroup = DataObject::get_one(
                                        'Group',
                                        "code LIKE 'b2c'"
                        );
                        $customer->Groups()->add($customerGroup);
                        $customer->logIn();

                        $this->sendConfirmationMail($customer);
                        $statusMessage = $this->ConfirmationSuccessMessage;
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
    public function sendConfirmationMail($customer) {
        SilvercartShopEmail::send(
            'RegistrationConfirmation',
            $customer->Email,
            array(
                'FirstName' => $customer->FirstName,
                'Surname'   => $customer->Surname,
                'Email'     => $customer->Email
            )
        );
    }
}
