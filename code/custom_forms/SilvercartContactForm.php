<?php
/**
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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
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
     * @since 04.06.2014
     */
    public function getFormFields($withUpdate = true) {
        if (empty($this->formFields)) {
            if (Member::currentUserID() > 0) {
                $member = Member::currentUser();
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
            
            if ($this->EnableStreet()) {
                $requirements = array();
                if ($this->StreetIsRequired()) {
                    $requirements = array(
                        'isFilledIn'        => true
                    );
                }
                $this->formFields = array_merge(
                    $this->formFields,
                    array(
                        'Street' => array(
                            'type'              => 'TextField',
                            'title'             => $address->fieldLabel('Street') . ' / ' . $address->fieldLabel('StreetNumber'),
                            'checkRequirements' => $requirements,
                        ),
                        'StreetNumber' => array(
                            'type'              => 'TextField',
                            'title'             => $address->fieldLabel('StreetNumber'),
                            'checkRequirements' => $requirements,
                        ),
                    )
                );
            }
            if ($this->EnableCity()) {
                $requirements = array();
                if ($this->CityIsRequired()) {
                    $requirements = array(
                        'isFilledIn'        => true
                    );
                }
                $this->formFields = array_merge(
                    $this->formFields,
                    array(
                        'Postcode' => array(
                            'type'              => 'TextField',
                            'title'             => $address->fieldLabel('Postcode'),
                            'checkRequirements' => $requirements,
                        ),
                        'City' => array(
                            'type'              => 'TextField',
                            'title'             => $address->fieldLabel('Postcode') . ' - ' . $address->fieldLabel('City'),
                            'checkRequirements' => $requirements,
                        ),
                    )
                );
            }
            if ($this->EnableCountry()) {
                $requirements = array();
                if ($this->CountryIsRequired()) {
                    $requirements = array(
                        'isFilledIn'        => true
                    );
                }
                $this->formFields = array_merge(
                    $this->formFields,
                    array(
                        'SilvercartCountryID' => array(
                            'type'              => 'DropdownField',
                            'title'             => $address->fieldLabel('SilvercartCountry'),
                            'value'             => SilvercartCountry::getPrioritiveDropdownMap(true, _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE')),
                            'checkRequirements' => $requirements,
                        ),
                    )
                );
            }
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
        Director::redirect($contactFormResponsePage->RelativeLink());
    }
    
    /**
     * Returns whether to enable the Street field.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.08.2014
     */
    public function EnableStreet() {
        return $this->Controller()->EnableStreet;
    }

    /**
     * Returns whether to set the Street field as a required one.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.08.2014
     */
    public function StreetIsRequired() {
        return $this->Controller()->StreetIsRequired;
    }
    
    /**
     * Returns whether to enable the City field.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.08.2014
     */
    public function EnableCity() {
        return $this->Controller()->EnableCity;
    }

    /**
     * Returns whether to set the City field as a required one.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.08.2014
     */
    public function CityIsRequired() {
        return $this->Controller()->CityIsRequired;
    }
    
    /**
     * Returns whether to enable the Country field.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.08.2014
     */
    public function EnableCountry() {
        return $this->Controller()->EnableCountry;
    }

    /**
     * Returns whether to set the Country field as a required one.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.08.2014
     */
    public function CountryIsRequired() {
        return $this->Controller()->CountryIsRequired;
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
