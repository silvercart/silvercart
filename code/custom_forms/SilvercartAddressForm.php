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
 * Customer form for adding an address.
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 09.10.2012
 * @license see license file in modules root directory
 */
class SilvercartAddressForm extends CustomHtmlForm {
    
    /**
     * Default cache key extension for address forms
     * 
     * @return string
     */
    public function getCacheKeyExtension() {
        if (empty($this->cacheKeyExtension)) {
            $cacheKeyExtensionString    = '';
            $countryMap                 = SilvercartCountry::getPrioritiveDropdownMap();
            foreach ($countryMap as $id => $title) {
                $cacheKeyExtensionString .= $id . ':' . $title . ';';
            }
            $this->cacheKeyExtension = md5($cacheKeyExtensionString) . sha1($cacheKeyExtensionString);
        }

        return $this->cacheKeyExtension;
    }
    
    /**
     * Adds the javascript to handle packstations if enabled
     * 
     * @param Controller $controller  Controller
     * @param array      $params      Params
     * @param array      $preferences Preferences
     * @param bool       $barebone    Barebone?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.10.2012
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {
        parent::__construct($controller, $params, $preferences, $barebone);
        if (SilvercartConfig::enablePackstation()) {
            $this->controller->addJavascriptOnloadSnippet(
                array(
                    sprintf(
                            'initAddressForm(%s);',
                            $this->FormName()
                    ),
                )
            );
        }
    }
    
    /**
     * Returns the form fields
     * 
     * @param bool $withUpdate Execute update method of decorators?
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.10.2012
     */
    public function getFormFields($withUpdate = true) {
        if (empty($this->formFields)) {
            $address            = singleton('SilvercartAddress');
            $this->formFields   = array(
                'TaxIdNumber' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('TaxIdNumber'),
                    'maxLength' => 30
                ),
                'Company' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('Company'),
                    'maxLength' => 50
                ),
                'Salutation' => array(
                    'type' => 'DropdownField',
                    'title' => $address->fieldLabel('Salutation'),
                    'value' => array(
                        ''      => _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'),
                        "Frau"  => _t('SilvercartAddress.MISSES'),
                        "Herr"  => _t('SilvercartAddress.MISTER')
                    ),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'FirstName' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('FirstName'),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'Surname' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('Surname'),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'Addition' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('Addition'),
                ),
                'Street' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('Street'),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'StreetNumber' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('StreetNumber'),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'Postcode' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('Postcode'),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'City' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('City'),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'PhoneAreaCode' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('PhoneAreaCode'),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'Phone' => array(
                    'type'      => 'TextField',
                    'title'     => $address->fieldLabel('Phone'),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    )
                ),
                'Fax' => array(
                    'type'  => 'TextField',
                    'title' => $address->fieldLabel('Fax'),
                ),
                'Country' => array(
                    'type'              => 'DropdownField',
                    'title'             => $address->fieldLabel('SilvercartCountry'),
                    'value'             => SilvercartCountry::getPrioritiveDropdownMap(true, _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE')),
                    'checkRequirements' => array(
                        'isFilledIn' => true
                    ),
                )
            );
            if (SilvercartConfig::enablePackstation()) {
                $this->formFields = array_merge(
                    $this->formFields,
                    array(
                        'IsPackstation' => array(
                            'type'          => 'OptionsetField',
                            'title'         => $address->fieldLabel('AddressType'),
                            'selectedValue' => '0',
                            'value' => array(
                                '0' => $address->fieldLabel('UseAbsoluteAddress'),
                                '1' => $address->fieldLabel('UsePackstation'),
                            ),
                            'checkRequirements' => array(
                                'isFilledIn'        => true
                            ),
                        ),
                        'PostNumber' => array(
                            'type'              => 'TextField',
                            'title'             => $address->fieldLabel('PostNumber'),
                            'checkRequirements' => array(
                                'isFilledIn'        => true
                            ),
                        ),
                        'Packstation' => array(
                            'type'              => 'TextField',
                            'title'             => $address->fieldLabel('Packstation'),
                            'checkRequirements' => array(
                                'isFilledIn'        => true
                            ),
                        ),
                    )
                );
            } else {
                $this->formFields = array_merge(
                    $this->formFields,
                    array(
                        'IsPackstation' => array(
                            'type'  => 'HiddenField',
                            'title' => $address->fieldLabel('AddressType'),
                            'value' => '0',
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
     * @since 09.10.2012
     */
    public function preferences() {
        $this->CancelLink   = $this->controller->Link();
        $this->preferences  = array(
            'submitButtonTitle'  => _t('SilvercartPage.SAVE', 'save'),
            'markRequiredFields' => true
        );
        parent::preferences();
        return $this->preferences;
    }

    /**
     * Dynamically enables and disables the validation of some context dependant
     * fields
     *
     * @param SS_HTTPRequest $data submit data
     * @param Form           $form form object
     *
     * @return ViewableData
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.10.2012
     */
    public function submit($data, $form) {
        $formData = $this->getFormData($data);
        if (array_key_exists('IsPackstation', $formData)) {
            if ($formData['IsPackstation'] == '0') {
                $this->deactivateValidationFor('PostNumber');
                $this->deactivateValidationFor('Packstation');
            } else {
                $this->deactivateValidationFor('Street');
                $this->deactivateValidationFor('StreetNumber');
            }
        } elseif (array_key_exists('Shipping_IsPackstation', $formData)) {
            if ($formData['Shipping_IsPackstation'] == '0') {
                $this->deactivateValidationFor('Shipping_PostNumber');
                $this->deactivateValidationFor('Shipping_Packstation');
            } else {
                $this->deactivateValidationFor('Shipping_Street');
                $this->deactivateValidationFor('Shipping_StreetNumber');
            }
        }

        return parent::submit($data, $form);
    }

    /**
     * Indicates wether business customers should be enabled.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.03.2012
     */
    public function EnableBusinessCustomers() {
        return SilvercartConfig::enableBusinessCustomers();
    }

    /**
     * Indicates wether business customers should be enabled.
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.10.2012
     */
    public function EnablePackstation() {
        return SilvercartConfig::enablePackstation();
    }
    
}
