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
 * @subpackage Forms
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.08.2011
 * @license see license file in modules root directory
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductGroupPageSelectorsForm extends CustomHtmlForm {

    /**
     * Provides additional parameters for the cache key.
     *
     * @return mixed boolean false|string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.12.2012
     */
    public function getCacheKeyExtension() {
        $controller = Controller::curr();

        if ($controller &&
            $controller instanceof SilvercartProductGroupPage_Controller) {

            $extension = $controller->ID.'_'.
                         SilvercartProduct::defaultSort().'_'.
                         $controller->getProductsPerPageSetting().'_'.
                         $this->getTotalNumberOfProducts();

            return md5($extension);
        }

        return false;
    }

    /**
     * Returns the total number of products for the current controller.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.12.2012
     */
    public function getTotalNumberOfProducts() {
        $controller       = Controller::curr();
        $numberOfProducts = 0;

        if ($controller &&
            $controller instanceof SilvercartProductGroupPage_Controller) {

            $numberOfProducts = $controller->getTotalNumberOfProducts();
        }

        return $numberOfProducts;
    }

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
     * Returns the form fields for this form
     *
     * @param bool $withUpdate Call the method with decorator updates or not?
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.06.2012
     */
    public function getFormFields($withUpdate = true) {
        $productsPerPage = $this->controller->getProductsPerPageSetting();
        if ($productsPerPage == SilvercartConfig::getProductsPerPageUnlimitedNumber()) {
            $productsPerPage = 0;
        }
        
        $product                            = singleton('SilvercartProduct');
        $sortableFrontendFields             = $product->sortableFrontendFields();
        $sortableFrontendFieldValues        = array_keys($sortableFrontendFields);
        $sortableFrontendFieldValues        = array_flip($sortableFrontendFieldValues);
        if (!array_key_exists($product->getDefaultSort(), $sortableFrontendFieldValues)) {
            $sortableFrontendFieldValues[$product->getDefaultSort()] = 0;
        }
        $sortOrder                          = $sortableFrontendFieldValues[$product->getDefaultSort()];
        $sortableFrontendFieldsForDropdown  = array_values($sortableFrontendFields);
        asort($sortableFrontendFieldsForDropdown);
        
        $this->formFields = array(
            'productsPerPage' => array(
                'type'              => 'DropdownField',
                'title'             => _t('SilvercartProductGroupPageSelector.PRODUCTS_PER_PAGE'),
                'value'             => SilvercartConfig::getProductsPerPageOptions(),
                'selectedValue'     => $productsPerPage,
                'checkRequirements' => array(
                )
            ),
            'SortOrder' => array(
                'type'              => 'DropdownField',
                'title'             => _t('SilvercartProductGroupPageSelector.SORT_ORDER'),
                'value'             => $sortableFrontendFieldsForDropdown,
                'selectedValue'     => $sortOrder,
                'checkRequirements' => array(
                )
            ),
        );
        return parent::getFormFields($withUpdate = true);
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
        
        $product                        = singleton('SilvercartProduct');
        $sortableFrontendFields         = $product->sortableFrontendFields();
        $sortableFrontendFieldValues    = array_keys($sortableFrontendFields);
        $sortOrder                      = $sortableFrontendFieldValues[$data['SortOrder']];
        SilvercartProduct::setDefaultSort($sortOrder);
        
        if (!$member) {
            $member = SilvercartCustomer::createAnonymousCustomer();
        }
        
        if ($member) {
            $member->getSilvercartCustomerConfig()->productsPerPage = $data['productsPerPage'];
            $member->getSilvercartCustomerConfig()->write();
        }
        
        if (isset($formData['backLink'])) {
            $backLink = $formData['backLink'];
        }
        
        $this->controller->redirect($backLink, 302);
    }
}
