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
 * form definition
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
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
     * The context product
     *
     * @var SilvercartProduct
     */
    protected $product = null;

    /**
     * Returns the Cache Key for the current step
     *
     * @return string
     */
    public function getCacheKeyExtension() {
        if (empty($this->cacheKeyExtension)) {
            $product                    = $this->getProduct();
            $cacheKeyExtension          = $product->ID . '_' . $product->LastEditedForCache . '_' . $product->getQuantityInCart();
            $this->cacheKeyExtension    = md5($cacheKeyExtension);
        }

        return $this->cacheKeyExtension;
    }

    /**
     * Preferences
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.03.2013
     */
    public function preferences() {
        $numberOfDecimalPlaces = false;

        if ($this->getProduct()->isInCart()) {
            $this->preferences['submitButtonTitle'] = _t('SilvercartProduct.CHANGE_QUANTITY_CART');
        } else {
            $this->preferences['submitButtonTitle'] = _t('SilvercartProduct.ADD_TO_CART');
        }
        $this->preferences['doJsValidationScrolling'] = false;

        $this->formFields['productQuantity']['title'] = _t('SilvercartProduct.QUANTITY');
        $this->setCustomParameter('backLink', Controller::curr()->Link());

        // Get maxlength for quantity field
        $quantityFieldMaxLength = strlen((string) SilvercartConfig::addToCartMaxQuantity());

        if ($quantityFieldMaxLength == 0) {
            $quantityFieldMaxLength = 1;
        }

        if (array_key_exists('productID', $this->customParameters)) {
            $silvercartProduct = $this->getProduct();
            if ($silvercartProduct instanceof SilvercartProduct) {
                $numberOfDecimalPlaces = $silvercartProduct->SilvercartQuantityUnit()->numberOfDecimalPlaces;
            }
        }

        if ($numberOfDecimalPlaces !== false &&
            $numberOfDecimalPlaces > 0) {

            if (array_key_exists('isNumbersOnly', $this->formFields['productQuantity']['checkRequirements'])) {
                unset($this->formFields['productQuantity']['checkRequirements']['isNumbersOnly']);
            }

            $this->formFields['productQuantity']['checkRequirements']['isDecimalNumber'] = $numberOfDecimalPlaces;
            $this->formFields['productQuantity']['maxLength'] = $quantityFieldMaxLength + 1 + $numberOfDecimalPlaces;
        } else {
            $this->formFields['productQuantity']['maxLength'] = $quantityFieldMaxLength;
        }
    }
    
    /**
     * Fills in the field values
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.03.2013
     */
    protected function fillInFieldValues() {
        parent::fillInFieldValues();
        if ($this->getProduct()->isInCart()) {
            $this->formFields['productQuantity']['value'] = $this->getProduct()->getQuantityInCart();
        }
    }
    
    /**
     * Returns the product in context of this form
     * 
     * @return SilvercartProduct
     */
    public function getProduct() {
        if (is_null($this->product)) {
            $this->product = DataObject::get_by_id('SilvercartProduct', $this->customParameters['productID']);
        }
        return $this->product;
    }
}
