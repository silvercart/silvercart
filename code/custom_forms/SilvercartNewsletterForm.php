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
 * @subpackage Forms
 */

/**
 * The form for subscribing to or unsubscribing from the newsletter.
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 22.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartNewsletterForm extends CustomHtmlForm {

    /**
     * Form field definitions.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.03.2011
     */
    protected $formFields = array(
        'Salutation' => array(
            'type'  => 'DropdownField',
            'title' => 'Anrede',
            'value' => array(
                ''      => 'Bitte wählen',
                'Frau'  => 'Frau',
                'Herr'  => 'Herr'
            ),
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'FirstName' => array(
            'type'  => 'TextField',
            'title' => 'Vorname',
            'checkRequirements' => array(
                'isFilledIn'    => true,
                'hasMinLength'  => 3
            )
        ),
        'Surname' => array(
            'type'  => 'TextField',
            'title' => 'Nachname',
            'checkRequirements' => array(
                'isFilledIn'    => true,
                'hasMinLength'  => 3
            )
        ),
        'Email' => array(
            'type'  => 'TextField',
            'title' => 'Email Adresse',
            'value' => '',
            'checkRequirements' => array(
                'isFilledIn'        => true,
                'isEmailAddress'    => true
            )
        ),
        'NewsletterAction' => array(
            'type'          => 'OptionsetField',
            'title'         => 'Was wollen Sie tun',
            'selectedValue' => '1',
            'value' => array(
                '1' => 'Ich möchte den Newsletter erhalten',
                '2' => 'Ich möchte den Newsletter abbestellen'
            ),
            'checkRequirements' => array(
                'isFilledIn'        => true
            )
        )
    );
    /**
     * Form settings.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.03.2011
     */
    protected $preferences = array(
        'submitButtonTitle'         => 'Abschicken',
        'doJsValidationScrolling'   => false
    );

    /**
     * Here we insert the translations of the field labels.
     * 
     * Registered users don't have to insert their data, they only get the
     * option to subscribe to or unsubscribe from the newsletter.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 22.03.2011
     */
    protected function fillInFieldValues() {
        $member = SilvercartCustomerRole::currentRegisteredCustomer();

        $this->clearSessionMessages();

        // Set translations
        $this->formFields['Salutation']['value'] = array(
            ''      => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'),
            "Frau"  => _t('SilvercartAddress.MISSIS'),
            "Herr"  => _t('SilvercartAddress.MISTER')
        );
        $this->formFields['FirstName']['title']             = _t('SilvercartAddress.FIRSTNAME', 'firstname');
        $this->formFields['Surname']['title']               = _t('SilvercartAddress.SURNAME');
        $this->formFields['Email']['title']                 = _t('SilvercartAddress.EMAIL', 'email address');
        $this->formFields['NewsletterAction']['title']      = _t('SilvercartNewsletterForm.ACTIONFIELD_TITLE');
        $this->formFields['NewsletterAction']['value']['1'] = _t('SilvercartNewsletterForm.ACTIONFIELD_SUBSCRIBE');
        $this->formFields['NewsletterAction']['value']['2'] = _t('SilvercartNewsletterForm.ACTIONFIELD_UNSUBSCRIBE');
        $this->preferences['submitButtonTitle']             = _t('SilvercartPage.SUBMIT_MESSAGE', 'submit message');

        // Fill in field values for registered customers and set them to readonly.
        if ($member) {
            $this->formFields['Salutation']['checkRequirements']    = array();
            $this->formFields['Salutation']['type']                 = 'ReadonlyField';
            $this->formFields['Salutation']['value']                = $member->Salutation;
            $this->formFields['FirstName']['checkRequirements']     = array();
            $this->formFields['FirstName']['type']                  = 'ReadonlyField';
            $this->formFields['FirstName']['value']                 = $member->FirstName;
            $this->formFields['Surname']['checkRequirements']       = array();
            $this->formFields['Surname']['type']                    = 'ReadonlyField';
            $this->formFields['Surname']['value']                   = $member->Surname;
            $this->formFields['Email']['checkRequirements']         = array();
            $this->formFields['Email']['type']                      = 'ReadonlyField';
            $this->formFields['Email']['value']                     = $member->Email;

            // Remove action field according to newsletter status of the customer
            if ($member->SubscribedToNewsletter) {
                $this->formFields['NewsletterAction']['value'] = array(
                    '2' => _t('SilvercartNewsletterForm.ACTIONFIELD_UNSUBSCRIBE')
                );
                $this->formFields['NewsletterAction']['selectedValue'] = '2';
                $this->formFields['NewsletterAction']['title'] = _t('SilvercartNewsletter.SUBSCRIBED').' - '.$this->formFields['NewsletterAction']['title'];
            } else {
                $this->formFields['NewsletterAction']['value'] = array(
                    '1' => _t('SilvercartNewsletterForm.ACTIONFIELD_SUBSCRIBE')
                );
                $this->formFields['NewsletterAction']['selectedValue'] = '1';
                $this->formFields['NewsletterAction']['title'] = _t('SilvercartNewsletter.UNSUBSCRIBED').' - '.$this->formFields['NewsletterAction']['title'];
            }
        }
    }

    /**
     * We save the data of the user here.
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 22.03.2011
     */
    protected function submitSuccess($data, $form, $formData) {
        $member = SilvercartCustomerRole::currentRegisteredCustomer();

        if ($member) {
            // ----------------------------------------------------------------
            // For registered and logged in customers all we have to do is set
            // the respective field in the customer object.
            // ----------------------------------------------------------------
            switch ($formData['NewsletterAction']) {
                case '1':
                    $member->SubscribedToNewsletter = true;
                    $this->setSessionMessage(
                        sprintf(
                            _t('SilvercartNewsletterStatus.SUBSCRIBED_SUCCESSFULLY'),
                            $formData['Email']
                        )
                    );
                    break;
                case '2':
                default:
                    $this->setSessionMessage(
                        sprintf(
                            _t('SilvercartNewsletterStatus.UNSUBSCRIBED_SUCCESSFULLY'),
                            $formData['Email']
                        )
                    );
                    $member->SubscribedToNewsletter = false;
            }
            $member->write();
        } else {
            // ----------------------------------------------------------------
            // For unregistered customers we have to add / remove them from
            // the datastore for unregistered newsletter recipients.
            //
            // If the given email address belongs to a registered customer we
            // should not do anything but ask the user to log in first.
            // ----------------------------------------------------------------
            $checkForRegularCustomer = DataObject::get_one(
                'SilvercartRegularCustomer',
                sprintf(
                    "Email = '%s'",
                    $formData['Email']
                )
            );

            if ($checkForRegularCustomer) {
                $this->setSessionMessage(
                    sprintf(
                        _t('SilvercartNewsletterStatus.REGULAR_CUSTOMER_WITH_SAME_EMAIL_EXISTS'),
                        $formData['Email'],
                        '/Security/Login/?BackURL='.$this->controller->PageByIdentifierCode('SilvercartNewsletterPage')->Link()
                    )
                );
            } else {
                $checkForAnonymousRecipient = DataObject::get_one(
                    'SilvercartAnonymousNewsletterRecipient',
                    sprintf(
                        "Email = '%s'",
                        $formData['Email']
                    )
                );

                if ($formData['NewsletterAction'] == '1') {
                    // --------------------------------------------------------
                    // Subscribe to newsletter.
                    // If the user is already subscribed we display a
                    // message accordingly.
                    // --------------------------------------------------------
                    if ($checkForAnonymousRecipient) {
                        $this->setSessionMessage(
                            sprintf(
                                _t('SilvercartNewsletterStatus.ALREADY_SUBSCRIBED'),
                                $formData['Email']
                            )
                        );
                    } else {
                        $this->addToAnonymousRecipients($formData);
                        SilvercartAnonymousNewsletterRecipient::add($formData['Surname'], $formData['FirstName'], $formData['Surname'], $formData['Email']);
                        $this->setSessionMessage(
                            sprintf(
                                _t('SilvercartNewsletterStatus.SUBSCRIBED_SUCCESSFULLY'),
                                $formData['Email']
                            )
                        );
                    }
                } else {
                    // --------------------------------------------------------
                    // Unsubscribe from newsletter.
                    // If no email address exists we display a message
                    // accordingly.
                    // --------------------------------------------------------
                    if ($checkForAnonymousRecipient) {
                        $this->removeFromAnonymousRecipients($formData);
                        SilvercartAnonymousNewsletterRecipient::removeByEmailAddress($formData['Email']);
                        $this->setSessionMessage(
                            sprintf(
                                _t('SilvercartNewsletterStatus.UNSUBSCRIBED_SUCCESSFULLY'),
                                $formData['Email']
                            )
                        );
                    } else {
                        $this->setSessionMessage(
                            sprintf(
                                _t('SilvercartNewsletterStatus.NO_EMAIL_FOUND'),
                                $formData['Email']
                            )
                        );
                    }
                }
            }
        }

        $redirectLink = '/';
        $responsePage = SilvercartPage_Controller::PageByIdentifierCode("SilvercartNewsletterResponsePage");

        if ($responsePage) {
            $redirectLink = $responsePage->RelativeLink();
        }

        Director::redirect($redirectLink);
    }

    /**
     * Set a session message that can be recalled on the status page.
     *
     * @param string $message The message to store
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 22.03.2011
     */
    public function setSessionMessage($message) {
        $status = Session::get('SilvercartNewsletterStatus');

        // Initialise session data structure
        if (!$status ||
            !is_array($status)) {
            
            $status = array(
                'messages' => array()
            );
        } else {
            if (!isset($status['messages']) ||
                !is_array($status['messages'])) {

                $status['messages'] = array();
            }
        }

        // Add message and save to session
        $status['messages'][] = array(
            'message' => $message
        );
        
        Session::set('SilvercartNewsletterStatus', $status);
    }

    /**
     * Clear all session messages that could be recalled on the status page.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 22.03.2011
     */
    public function clearSessionMessages() {
        Session::clear('SilvercartNewsletterStatus');
    }
}