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
 * @subpackage Order
 */

/**
 * Handles the configuration for the prefilled shopping carts mechanism.
 *
 * @package Silvercart
 * @subpackage Order
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 01.08.2011
 * @license see license file in modules root directory
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartInboundShoppingCartTransfer extends DataObject {
    
    /**
     * Attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.08.2011
     */
    public static $db = array(
        'Title'                             => 'VarChar(255)',
        'refererIdentifier'                 => 'VarChar(50)',
        'useSharedSecret'                   => 'Boolean(1)',
        'sharedSecret'                      => 'VarChar(255)',
        'sharedSecretIdentifier'            => 'VarChar(50)',
        'transferMethod'                    => "Enum('keyValue,combinedString','combinedString')",
        'combinedStringKey'                 => 'VarChar(50)',
        'combinedStringEntitySeparator'     => 'VarChar(20)',
        'combinedStringQuantitySeparator'   => 'VarChar(20',
        'keyValueProductIdentifier'         => 'VarChar(50)',
        'keyValueQuantityIdentifier'        => 'VarChar(50)',
        'productMatchingField'              => 'VarChar(255)'
    );
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.08.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title'                 => $this->fieldLabel('Title'),
            'refererIdentifier'     => $this->fieldLabel('refererIdentifier'),
            'useSharedSecret'       => $this->fieldLabel('useSharedSecret'),
            'transferMethod'        => $this->fieldLabel('transferMethod'),
            'productMatchingField'  => $this->fieldLabel('productMatchingField')
        );
        $this->extend('updateSummaryFields', $summaryFields);

        return $summaryFields;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     * 
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.08.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title'                             => _t('SilvercartInboundShoppingCartTransfer.TITLE'),
                'refererIdentifier'                 => _t('SilvercartInboundShoppingCartTransfer.REFERER_IDENTIFIER'),
                'useSharedSecret'                   => _t('SilvercartInboundShoppingCartTransfer.SHARED_SECRET_ACTIVATION'),
                'sharedSecret'                      => _t('SilvercartInboundShoppingCartTransfer.SHARED_SECRET'),
                'sharedSecretIdentifier'            => _t('SilvercartInboundShoppingCartTransfer.SHARED_SECRET_IDENTIFIER'),
                'transferMethod'                    => _t('SilvercartInboundShoppingCartTransfer.TRANSFER_METHOD'),
                'combinedStringKey'                 => _t('SilvercartInboundShoppingCartTransfer.COMBINED_STRING_KEY'),
                'combinedStringEntitySeparator'     => _t('SilvercartInboundShoppingCartTransfer.COMBINED_STRING_ENTITY_SEPARATOR'),
                'combinedStringQuantitySeparator'   => _t('SilvercartInboundShoppingCartTransfer.COMBINED_STRING_QUANTITY_SEPARATOR'),
                'keyValueProductIdentifier'         => _t('SilvercartInboundShoppingCartTransfer.KEY_VALUE_PRODUCT_IDENTIFIER'),
                'keyValueQuantityIdentifier'        => _t('SilvercartInboundShoppingCartTransfer.KEY_VALUE_QUANTITY_IDENTIFIER'),
                'productMatchingField'              => _t('SilvercartInboundShoppingCartTransfer.PRODUCT_MATCHING_FIELD')
            )
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        
        return $fieldLabels;
    }
}