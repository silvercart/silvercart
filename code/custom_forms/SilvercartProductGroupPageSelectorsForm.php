<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 * @package Silvercart
 * @subpackage Forms
 */

/**
 * A form that displays fields for manipulating the display of a
 * SilvercartProductGroupPage group view results.
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Sascha Koehler <skoehler@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 30.08.2013
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartProductGroupPageSelectorsForm extends CustomHtmlForm {

    /**
     * Provides additional parameters for the cache key.
     *
     * @return mixed boolean false|string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2013
     */
    public function getCacheKeyExtension() {
        $controller = Controller::curr();

        if ($controller &&
            $controller instanceof SilvercartProductGroupPage_Controller) {

            $product   = singleton('SilvercartProduct');
            $extension = $controller->ID . '_' . 
                         SilvercartProduct::defaultSort() . '_' . 
                         $controller->getProductsPerPageSetting() . '_' .
                         $product->getDefaultSort() . '_' .
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.11.2014
     */
    public function preferences() {
        $this->preferences['submitButtonTitle']         = _t('SilvercartProductGroupPageSelector.OK');
        $this->preferences['doJsValidationScrolling']   = false;
        parent::preferences();
    }
    
    /**
     * Returns the form fields for this form
     *
     * @param bool $withUpdate Call the method with decorator updates or not?
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2013
     */
    public function getFormFields($withUpdate = true) {
        if (!array_key_exists('SortOrder', $this->formFields)) {
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
                'SortOrder' => array(
                    'type'              => 'DropdownField',
                    'title'             => _t('SilvercartProductGroupPageSelector.SORT_ORDER'),
                    'value'             => $sortableFrontendFieldsForDropdown,
                    'selectedValue'     => $sortOrder,
                    'checkRequirements' => array(
                    )
                ),
            );
            $productsPerPageOptions = SilvercartConfig::getProductsPerPageOptions();
            if (!empty($productsPerPageOptions)) {
                $this->formFields['productsPerPage'] = array(
                    'type'              => 'DropdownField',
                    'title'             => _t('SilvercartProductGroupPageSelector.PRODUCTS_PER_PAGE'),
                    'value'             => SilvercartConfig::getProductsPerPageOptions(),
                    'selectedValue'     => $productsPerPage,
                    'checkRequirements' => array(
                    )
                );
            } else {
                $this->formFields['productsPerPage'] = array(
                    'type'              => 'HiddenField',
                    'value'             => SilvercartConfig::getProductsPerPageDefault(),
                );
            }
        }
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public function submitSuccess($data, $form, $formData) {
        $backLink = $this->controller->Link();
        $member   = SilvercartCustomer::currentUser();
        
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
    
    /**
     * Returns whether there are products per page options.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2013
     */
    public function hasProductsPerPageOptions() {
        $productsPerPageOptions = SilvercartConfig::getProductsPerPageOptions();
        return !empty($productsPerPageOptions);
    }
}
