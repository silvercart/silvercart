<?php

namespace SilverCart\Model\Customer;

use SilverCart\Admin\Forms\AlertInfoField;
use SilverCart\Admin\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Group;
use function _t;

/**
 * Decorates the Group class for additional functionality.
 *
 * @package SilverCart
 * @subpackage Model_Customer
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property Group $owner Owner
 */
class GroupExtension extends DataExtension
{
    /**
     * DB attributes
     *
     * @var string[]
     */
    private static array $db = [
        'Pricetype'      => 'Enum("---,gross,net","---")',
        'HidePrices'     => 'Boolean',
        'HidePricesInfo' => 'HTMLText',
    ];
    /**
     * Belongs many many relations
     *
     * @var string[]
     */
    private static array $belongs_many_many = [
        'PaymentMethods'       => PaymentMethod::class . '.ShowOnlyForGroups',
        'HiddenPaymentMethods' => PaymentMethod::class . '.ShowNotForGroups',
        'ShippingMethods'      => ShippingMethod::class . '.CustomerGroups',
    ];
    /**
     * Grant API access on this item.
     *
     * @var bool
     */
    private static bool $api_access = true;
    
    /**
     * Adds or removes GUI elements for the backend editing mask.
     *
     * @param FieldList $fields The original FieldList
     *
     * @return void
     */
    public function updateCMSFields(FieldList $fields) : void
    {
        $fields->addFieldToTab('Root.Members', TextField::create('Code', Group::singleton()->fieldLabel('Code')));
        if ($this->owner->exists()) {
            $fields->findOrMakeTab('Root.ShippingMethods', $this->owner->fieldLabel('ShippingMethods'));
            $fields->addFieldToTab("Root.ShippingMethods", AlertInfoField::create('ShippingMethodsInfo', $this->owner->fieldLabel('ShippingMethodsInfoContent'), $this->owner->fieldLabel('ShippingMethodsInfoTitle')));
            $fields->addFieldToTab("Root.ShippingMethods", GridField::create('ShippingMethods', $this->owner->fieldLabel('ShippingMethods'), $this->owner->ShippingMethods(), GridFieldConfig_RelationEditor::create()));
        }
        $enumValues = $this->owner->dbObject('Pricetype')->enumValues();
        $i18nSource = [];
        foreach ($enumValues as $value => $label) {
            $i18nSource[$value] = _t(Customer::class . '.' . strtoupper($label), $label);
        }
        $pricetypeField      = DropdownField::create('Pricetype', $this->owner->fieldLabel('Pricetype'), $i18nSource, $this->owner->Pricetype);
        $hidePricesField     = CheckboxField::create('HidePrices', $this->owner->fieldLabel('HidePrices'), $this->owner->HidePrices)->setDescription($this->owner->fieldLabel('HidePricesDesc'));
        $hidePricesInfoField = HTMLEditorField::create('HidePricesInfo', $this->owner->fieldLabel('HidePricesInfo'), $this->owner->HidePricesInfo)->setRightTitle($this->owner->fieldLabel('HidePricesInfoDesc'))->setRows(3);
        $fields->addFieldToTab("Root.Members", $pricetypeField, 'Members');
        $fields->addFieldToTab("Root.Members", $hidePricesField, 'Members');
        $fields->addFieldToTab("Root.Members", $hidePricesInfoField, 'Members');
    }
    
    /**
     * Updates the field labels
     *
     * @param array &$labels The original labels
     *
     * @return void
     */
    public function updateFieldLabels(&$labels) : void
    {
        $labels = array_merge($labels, [
            'HidePrices'                 => _t(GroupExtension::class . '.HidePrices', 'Hide prices'),
            'HidePricesDesc'             => _t(GroupExtension::class . '.HidePricesDesc', 'If selected, customers belonging to this group won\'t see any product prices.'),
            'HidePricesInfo'             => _t(GroupExtension::class . '.HidePricesInfo', 'Information text when hiding prices'),
            'HidePricesInfoDesc'         => _t(GroupExtension::class . '.HidePricesInfoDesc', 'This optional information text will be displayed if the "Hide rpeices" option is set.'),
            'Pricetype'                  => _t(GroupExtension::class . '.PRICETYPE', 'Pricetype'),
            'PaymentMethods'             => PaymentMethod::singleton()->plural_name(),
            'HiddenPaymentMethods'       => _t(GroupExtension::class . '.HiddenPaymentMethods', 'Hidden Payment Methods'),
            'ShippingMethods'            => ShippingMethod::singleton()->plural_name(),
            'ShippingMethodsInfoContent' => _t(GroupExtension::class . '.ShippingMethodsInfoContent', 'The shipping methods listed below are bound to this customer group and can only be chosen by customers belonging to this group. Shipping methods can be related to multiple customer groups, so this customer group might not be the only group with the permission to use the listed shipping methods.'),
            'ShippingMethodsInfoTitle'   => _t(GroupExtension::class . '.ShippingMethodsInfoTitle', 'Shipping methods bound to this customer group.'),
        ]);
    }
}
