<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @since 01.07.2011
 * @license see license file in modules root directory
 */
class SilvercartGroupExtension extends DataExtension {
   
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
     * Grant API access on this item.
     *
     * @var bool
     */
    public static $api_access = true;
    
    /**
     * Adds or removes GUI elements for the backend editing mask.
     *
     * @param FieldList $fields The original FieldList
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.03.2014
     */
    public function updateCMSFields(FieldList $fields) {
        $fields->addFieldToTab('Root.Members', new TextField('Code', _t('Group.CODE')));
        if ($this->owner->ID) {
            $gridFieldConfig      = SilvercartGridFieldConfig_RelationEditor::create();
            $shippingMethodsTable = new GridField('SilvercartShippingMethods', $this->owner->fieldLabel('SilvercartShippingMethods'), $this->owner->SilvercartShippingMethods(), $gridFieldConfig);
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
                    'Pricetype'                 => _t('SilvercartGroupExtension.PRICETYPE'),
                    'SilvercartPaymentMethods'  => _t('SilvercartPaymentMethod.PLURALNAME'),
                    'SilvercartShippingMethods' => _t('SilvercartShippingMethod.PLURALNAME'),
                )
        );
    }
}
