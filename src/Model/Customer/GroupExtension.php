<?php

namespace SilverCart\Model\Customer;

use SilverCart\Admin\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Group;

/**
 * Decorates the Group class for additional functionality.
 *
 * @package SilverCart
 * @subpackage Model_Customer
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GroupExtension extends DataExtension {
   
    /**
     * extra attributes
     *
     * @var array
     */
    private static $db = array(
        'Pricetype' => 'Enum("---,gross,net","---")'
    );
    
     /**
     * extra relations
     *
     * @var array
     */
    private static $belongs_many_many = array(
        'PaymentMethods'  => PaymentMethod::class,
        'ShippingMethods' => ShippingMethod::class,
    );

    /**
     * Grant API access on this item.
     *
     * @var bool
     */
    private static $api_access = true;
    
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
        $fields->addFieldToTab('Root.Members', new TextField('Code', Group::singleton()->fieldLabel('Code')));
        if ($this->owner->ID) {
            $gridFieldConfig      = GridFieldConfig_RelationEditor::create();
            $shippingMethodsTable = new GridField('ShippingMethods', $this->owner->fieldLabel('ShippingMethods'), $this->owner->ShippingMethods(), $gridFieldConfig);
            $fields->findOrMakeTab('Root.ShippingMethod', $this->owner->fieldLabel('ShippingMethods'));
            $fields->addFieldToTab("Root.ShippingMethod", $shippingMethodsTable);
        }
        
        $enumValues = $this->owner->dbObject('Pricetype')->enumValues();
        $i18nSource = array();
        foreach ($enumValues as $value => $label) {
            $i18nSource[$value] = _t(Customer::class . '.' . strtoupper($label), $label);
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
                    'Pricetype'       => _t(GroupExtension::class . '.PRICETYPE', 'Pricetype'),
                    'PaymentMethods'  => PaymentMethod::singleton()->plural_name(),
                    'ShippingMethods' => ShippingMethod::singleton()->plural_name(),
                )
        );
    }
}
