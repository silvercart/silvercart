<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms
 */

/**
 * a contact form of the CustomHTMLForms modul
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright pixeltricks GmbH
 * @author Roland Lehmann <rlehmann@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 04.06.2014
 * @license see license file in modules root directory
 */
class SilvercartContactForm extends CustomHtmlForm {

    /**
     * Indicates whether to exclude this form from caching or not
     *
     * @var bool
     */
    protected $excludeFromCache = true;

    /**
     * Returns the form fields
     * 
     * @param bool $withUpdate Execute update method of decorators?
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function getFormFields($withUpdate = true) {
        if (empty($this->formFields)) {
            if (Member::currentUserID() > 0) {
                $member = SilvercartCustomer::currentUser();
            } else {
                $member = singleton('Member');
            }
            $address            = singleton('SilvercartAddress');
            $this->formFields   = array(
                'Salutation' => array(
                    'type'              => 'DropdownField',
                    'title'             => $address->fieldLabel('Salutation'),
                    'selectedValue'     => $member->Salutation,
                    'value'             => array(
                        ''      => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'),
                        "Frau"  => _t('SilvercartAddress.MISSES'),
                        "Herr"  => _t('SilvercartAddress.MISTER')
                    ),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'FirstName' => array(
                    'type'              => 'TextField',
                    'title'             => $address->fieldLabel('FirstName'),
                    'value'             => $member->FirstName,
                    'checkRequirements' => array(
                        'isFilledIn'   => true,
                        'hasMinLength' => 3
                    )
                ),
                'Surname' => array(
                    'type'              => 'TextField',
                    'title'             => $address->fieldLabel('Surname'),
                    'value'             => $member->Surname,
                    'checkRequirements' => array(
                        'isFilledIn'   => true,
                        'hasMinLength' => 3
                    )
                ),
                'Email' => array(
                    'type'              => 'TextField',
                    'title'             => $member->fieldLabel('Email'),
                    'value'             => $member->Email,
                    'checkRequirements' => array(
                        'isFilledIn'     => true,
                        'isEmailAddress' => true
                    )
                ),
                'Message' => array(
                    'type'              => 'TextareaField',
                    'title'             => _t('SilvercartPage.MESSAGE', 'message'),
                    'checkRequirements' => array(
                        'isFilledIn'   => true,
                        'hasMinLength' => 3
                    )
                )
            );

            if ($this->EnablePhoneNumber()) {
                $requirements = array();
                if ($this->PhoneNumberIsRequired()) {
                    $requirements = array(
                        'isFilledIn'        => true
                    );
                }
                $this->formFields = array_merge(
                    $this->formFields,
                    array(
                        'Phone' => array(
                            'type'              => 'TextField',
                            'title'             => $address->fieldLabel('Phone'),
                            'checkRequirements' => $requirements,
                        ),
                    )
                );
            }
        }
        return parent::getFormFields($withUpdate);
    }
    
    /**
     * Sets the preferences for this form
     * 
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.06.2014
     */
    public function preferences() {
        $this->preferences  = array(
            'submitButtonTitle'  => _t('SilvercartPage.SUBMIT_MESSAGE', 'submit message'),
            'markRequiredFields' => true,
        );
        parent::preferences();
        return $this->preferences;
    }

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.10.2010
     * @return void
     */
    protected function submitSuccess($data, $form, $formData) {

        $formData['Message'] = str_replace('\r\n', "\n", $formData['Message']);

        $contactMessage = new SilvercartContactMessage();
        $contactMessage->update($formData);
        $contactMessage->write();
        $contactMessage->send();
        /*
         * redirect a user to the page type for the response or to the root
         */
        $contactFormResponsePage = SilvercartPage_Controller::PageByIdentifierCode("SilvercartContactFormResponsePage");
        $this->controller->redirect($contactFormResponsePage->RelativeLink());
    }
    
    /**
     * Returns whether to enable the phone number field.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.06.2014
     */
    public function EnablePhoneNumber() {
        return $this->Controller()->EnablePhoneNumber;
    }

    /**
     * Returns whether to set the phone number field as a required one.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.06.2014
     */
    public function PhoneNumberIsRequired() {
        return $this->Controller()->PhoneNumberIsRequired;
    }
}
