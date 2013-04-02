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
 * @subpackage Customer
 */

/**
 * Decorates the Group class for additional functionality
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 01.07.2011
 * @license see license file in modules root directory
 */
class SilvercartGroupDecorator extends DataExtension {
   
    /**
     * extra attributes
     *
     * @var array
     */
    public static $db = array(
                'Pricetype' => 'Enum("---,gross,net","---")'
        );
    
     /**
     * extra relations
     *
     * @var array
     */
    public static $belongs_many_many = array(
                'SilvercartPaymentMethods'  => 'SilvercartPaymentMethod',
                'SilvercartShippingMethods' => 'SilvercartShippingMethod'
        );
    
    /**
     * Adds or removes GUI elements for the backend editing mask.
     *
     * @param FieldList $fields The original FieldList
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public function updateCMSFields(FieldList $fields) {
        $fields->addFieldToTab('Root.Members', new TextField('Code', _t('Group.CODE')));
        if ($this->owner->ID) {
            $shippingMethodsTable = new SilvercartManyManyComplexTableField(
                            $this->owner,
                            'SilvercartShippingMethods',
                            'SilvercartShippingMethod'
            );
            $shippingMethodsTable->pageSize = 50;
            $fields->findOrMakeTab('Root.SilvercartShippingMethod', $this->owner->fieldLabel('SilvercartShippingMethods'));
            $fields->addFieldToTab("Root.SilvercartShippingMethod", $shippingMethodsTable);
        }
        
        $enumValues = $this->owner->dbObject('Pricetype')->enumValues();
        $i18nSource = array();
        foreach ($enumValues as $value => $label) {
            $i18nSource[$value] = _t('SilvercartCustomer.' . strtoupper($label), $label);
        }
        $pricetypeField = new DropdownField(
                'Pricetype',
                $this->owner->fieldLabel('Pricetype'),
                $i18nSource,
                $this->owner->Pricetype
        );
        $fields->addFieldToTab("Root.Members", $pricetypeField, 'Members');
        
    }
    
    /**
     * Updates the field labels
     *
     * @param array &$labels The original labels
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public function updateFieldLabels(&$labels) {
        $labels = array_merge(
                $labels,
                array(
                    'Pricetype'                 => _t('SilvercartGroupDecorator.PRICETYPE'),
                    'SilvercartPaymentMethods'  => _t('SilvercartPaymentMethod.PLURALNAME'),
                    'SilvercartShippingMethods' => _t('SilvercartShippingMethod.PLURALNAME'),
                )
        );
    }
}
