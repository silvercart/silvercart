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
 * form definition
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright 2013 pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.10.2010
 * @license see license file in modules root directory
 */
class SilvercartProductAddCartForm extends CustomHtmlForm {

    /**
     * Don't enable Security token for this type of form because we'll run
     * into caching problems when using it.
     * 
     * @var boolean
     */
    protected $securityTokenEnabled = false;
    
    /**
     * field configuration
     *
     * @var array
     */
    protected $formFields = array(
        'productQuantity' => array(
            'type' => 'TextField',
            'title' => 'Anzahl',
            'value' => '1',
            'maxLength' => 3,
            'checkRequirements' => array(
                'isFilledIn'      => true,
                'isNumbersOnly'   => true
            )
        )
    );
    
    /**
     * Custom form action to use for this form
     *
     * @var string
     */
    protected $customHtmlFormAction = 'addToCart';

    /**
     * Returns the Cache Key for the current step
     *
     * @return string
     */
    public function getCacheKeyExtension() {
        if (empty($this->cacheKeyExtension)) {
            $silvercartProduct       = DataObject::get_by_id('SilvercartProduct', $this->customParameters['productID']);
            $cacheKeyExtension       = $silvercartProduct->ID . '_' . $silvercartProduct->LastEditedForCache;
            $this->cacheKeyExtension = md5($cacheKeyExtension);
        }

        return $this->cacheKeyExtension;
    }

    /**
     * Preferences
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.2012
     */
    public function preferences() {
        $numberOfDecimalPlaces = false;

        $this->preferences['submitButtonTitle']       = _t('SilvercartProduct.ADD_TO_CART');
        $this->preferences['doJsValidationScrolling'] = false;

        $this->formFields['productQuantity']['title'] = _t('SilvercartProduct.QUANTITY');

        if (array_key_exists('productID', $this->customParameters)) {
            $silvercartProduct = DataObject::get_by_id('SilvercartProduct', $this->customParameters['productID']);

            if ($silvercartProduct) {
                $numberOfDecimalPlaces = $silvercartProduct->SilvercartQuantityUnit()->numberOfDecimalPlaces;
            }
        }

        if ($numberOfDecimalPlaces !== false &&
            $numberOfDecimalPlaces > 0) {

            if (array_key_exists('isNumbersOnly', $this->formFields['productQuantity']['checkRequirements'])) {
                unset($this->formFields['productQuantity']['checkRequirements']['isNumbersOnly']);
            }

            $this->formFields['productQuantity']['checkRequirements']['isDecimalNumber'] = $numberOfDecimalPlaces;
            $this->formFields['productQuantity']['maxLength'] = 3 + 1 + $numberOfDecimalPlaces;
        }
    }

    /**
     * executed if there are no validation errors on submit
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @return void
     * @since 23.10.2010
     */
    protected function submitSuccess($data, $form, $formData) {
        $backLink = $this->controller->Link();

        if (SilvercartConfig::getRedirectToCartAfterAddToCartAction()) {
            $backLink = SilvercartPage_Controller::PageByIdentifierCodeLink('SilvercartCartPage');
        } else if (isset($formData['backLink'])) {
            $backLink = $formData['backLink'];
        }

        // Preserve back link if available
        if (array_key_exists('_REDIRECT_BACK_URL', $formData)) {
            if (strpos('?', $backLink) === -1) {
                $backLink .= '?';
            } else {
                $backLink .= '&';
            }

            $backLink .= '_REDIRECT_BACK_URL='.$formData['_REDIRECT_BACK_URL'];
        }

        SilvercartShoppingCart::addProduct($formData);
        $this->controller->redirect($backLink, 302);
    }

}
