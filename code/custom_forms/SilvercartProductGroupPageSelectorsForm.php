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
 * A form that displays fields for manipulating the display of a
 * SilvercartProductGroupPage group view results.
 *
 * @package Silvercart
 * @subpacke Forms
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.08.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductGroupPageSelectorsForm extends CustomHtmlForm {
    
    /**
     * Form field definitions.
     *
     * @var array 
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public $formFields = array(
        'productsPerPage' => array(
            'type'              => 'DropdownField',
            'title'             => 'Products per page',
            'value'             => array(),
            'selectedValue'     => 0,
            'checkRequirements' => array(
            )
        )
    );
    
    /**
     * Set some field values and button labels.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public function preferences() {
        $this->preferences['submitButtonTitle']         = _t('SilvercartProductGroupPageSelector.OK');
        $this->preferences['doJsValidationScrolling']   = false;
    }
    
    /**
     * Fill form fields.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public function fillInFieldValues() {
        $this->formFields['productsPerPage']['title'] = _t('SilvercartProductGroupPageSelector.PRODUCTS_PER_PAGE');
        $this->formFields['productsPerPage']['value'] = SilvercartConfig::getProductsPerPageOptions();
        
        // Get the products per page setting
        $selectedValue = $this->controller->getProductsPerPageSetting();
        
        if ($selectedValue == SilvercartConfig::getProductsPerPageUnlimitedNumber()) {
            $selectedValue = 0;
        }
        
        $this->formFields['productsPerPage']['selectedValue'] = $selectedValue;
        
        parent::fillInFieldValues();
    }
    
    
    /**
     * We save the chosen value for the products per page in the customer's
     * configuration object here and redirect to the last view.
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 23.08.2011
     */
    public function submitSuccess($data, $form, $formData) {
        $backLink = $this->controller->Link();
        $member   = Member::currentUser();
        
        if (!$member) {
            $member = SilvercartCustomerRole::createAnonymousCustomer();
        }
        
        if ($member) {
            $member->getSilvercartCustomerConfig()->productsPerPage = $data['productsPerPage'];
            $member->getSilvercartCustomerConfig()->write();
        }
        
        if (isset($formData['backLink'])) {
            $backLink = $formData['backLink'];
        }
        
        Director::redirect($backLink, 302);
    }
}