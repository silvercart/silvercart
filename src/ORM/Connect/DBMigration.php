<?php

namespace SilverCart\ORM\Connect;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;

/**
 * Provides some DB migration tools.
 * 
 * @package SilverCart
 * @subpackage ORM_Connect
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 07.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DBMigration
{
    /**
     * DB migration map.
     * 
     * @todo
     */
    private static $migration_map = [
        'Address' => [
            'has_one' => [
                'SilvercartCountry' => 'Country',
            ],
        ],
        'Country' => [
            'many_many' => [
                'SilvercartPaymentMethods' => 'PaymentMethods',
            ],
        ],
        'CountryTranslation' => [
            'has_one' => [
                'SilvercartCountry' => 'Country',
            ],
        ],
        'Member' => [
            'has_one' => [
                'SilvercartShoppingCart'    => 'ShoppingCart',
                'SilvercartInvoiceAddress'  => 'InvoiceAddress',
                'SilvercartShippingAddress' => 'ShippingAddress',
                'SilvercartCustomerConfig'  => 'CustomerConfig',
            ],
        ],
        'Order' => [
            'has_one' => [
                'SilvercartShippingAddress' => 'ShippingAddress',
                'SilvercartInvoiceAddress'  => 'InvoiceAddress',
                'SilvercartPaymentMethod'   => 'PaymentMethod',
                'SilvercartShippingMethod'  => 'ShippingMethod',
                'SilvercartOrderStatus'     => 'OrderStatus',
                'SilvercartShippingFee'     => 'ShippingFee',
            ],
        ],
        'OrderLog' => [
            'has_one' => [
                'SilvercartOrder' => 'Order',
            ],
        ],
        'OrderPosition' => [
            'has_one' => [
                'SilvercartOrder'   => 'Order',
                'SilvercartProduct' => 'Product',
            ],
        ],
        'OrderStatus' => [
            'many_many' => [
                'SilvercartShopEmails'  => 'ShopEmails'
            ],
        ],
        'OrderStatusTranslation' => [
            'has_one' => [
                'SilvercartOrderStatus' => 'OrderStatus'
            ],
        ],
        'ShoppingCart' => [
            'many_many' => [
                'SilvercartProducts' => 'Products'
            ],
        ],
        'ShoppingCartPosition' => [
            'has_one' => [
                'SilvercartProduct'      => 'Product',
                'SilvercartShoppingCart' => 'ShoppingCart'
            ],
        ],
        'HandlingCost' => [
            'has_one' => [
                'SilvercartTax'           => 'Tax',
                'SilvercartPaymentMethod' => 'PaymentMethod',
                'SilvercartZone'          => 'Zone',
            ],
        ],
        'PaymentMethod' => [
            'has_one' => [
                'SilvercartZone' => 'Zone',
            ],
            'many_many' => [
                'SilvercartShippingMethods' => 'ShippingMethods',
            ],
        ],
        'AvailabilityStatusTranslation' => [
            'has_one' => [
                'SilvercartAvailabilityStatus' => 'AvailabilityStatus'
            ],
        ],
        'File' => [
            'has_one' => [
                'SilvercartProduct'         => 'Product',
                'SilvercartDownloadPage'    => 'DownloadPage',
            ],
        ],
        'FileTranslation' => [
            'has_one' => [
                'SilvercartFile' => 'File',
            ],
        ],
        'Image' => [
            'has_one' => [
                'SilvercartProduct'         => 'Product',
                'SilvercartPaymentMethod'   => 'PaymentMethod',
            ],
        ],
        'ImageTranslation' => [
            'has_one' => [
                'SilvercartImage' => 'Image',
            ],
        ],
        'ManufacturerTranslation' => [
            'has_one' => [
                'SilvercartManufacturer' => 'Manufacturer',
            ],
        ],
        'Product' => [
            'has_one' => [
                'SilvercartTax'                 => 'Tax',
                'SilvercartManufacturer'        => 'Manufacturer',
                'SilvercartProductGroup'        => 'ProductGroup',
                'SilvercartMasterProduct'       => 'MasterProduct',
                'SilvercartAvailabilityStatus'  => 'AvailabilityStatus',
                'SilvercartProductCondition'    => 'ProductCondition',
                'SilvercartQuantityUnit'        => 'QuantityUnit',
            ],
            'many_many' => [
                'SilvercartProductGroupMirrorPages' => 'ProductGroupMirrorPages'
            ],
        ],
        'ProductTranslation' => [
            'has_one' => [
                'SilvercartProduct' => 'Product',
            ],
        ],
        'ProductConditionTranslation' => [
            'has_one' => [
                'SilvercartProductConditionTranslation' => 'ProductConditionTranslation',
            ],
        ],
        'QuantityUnitTranslation' => [
            'has_one' => [
                'SilvercartQuantityUnit' => 'QuantityUnit',
            ],
        ],
        'TaxTranslation' => [
            'has_one' => [
                'SilvercartTax' => 'Tax',
            ],
        ],
        'CarrierTranslation' => [
            'has_one' => [
                'SilvercartCarrier' => 'Carrier',
            ],
        ],
        'ShippingFee' => [
            'has_one' => [
                'SilvercartZone'           => 'Zone',
                'SilvercartShippingMethod' => 'ShippingMethod',
                'SilvercartTax'            => 'Tax',
            ],
        ],
        'ShippingMethod' => [
            'has_one' => [
                'SilvercartCarrier' => 'Carrier',
            ],
            'many_many' => [
                'SilvercartZones'          => 'Zones',
                'SilvercartCustomerGroups' => 'CustomerGroups',
            ],
        ],
        'ShippingMethodTranslation' => [
            'has_one' => [
                'SilvercartShippingMethod' => 'ShippingMethod',
            ],
        ],
        'Zone' => [
            'many_many' => [
                'SilvercartCountries' => 'Countries',
                'SilvercartCarriers'  => 'Carriers',
            ],
        ],
        'ZoneTranslation' => [
            'has_one' => [
                'SilvercartZone' => 'Zone',
            ],
        ],
        'BargainProductsWidgetTranslation' => [
            'has_one' => [
                'SilvercartBargainProductsWidget' => 'BargainProductsWidget',
            ],
        ],
        'ContactMessage' => [
            'has_one' => [
                'SilvercartCountry' => 'Country',
            ],
        ],
        'ShopEmailTranslation' => [
            'has_one' => [
                'SilvercartShopEmail' => 'ShopEmail',
            ],
        ],
        'SiteConfig' => [
            'has_one' => [
                'SilvercartLogo' => 'ShopLogo',
            ],
        ],
    ];
    
    /**
     * Renames database columns for the given $dataObject like defined in 
     * $renameFieldMap.
     * 
     * <code>
     * // expected format for $renameFieldMap
     * $renameFieldMap = [
     *     'OldFieldName1' => 'NewFieldName1',
     *     'OldFieldName2' => 'NewFieldName2',
     *     'OldFieldName3' => 'NewFieldName3',
     *     'OldFieldName4' => 'NewFieldName4',
     * ];
     * </code>
     * 
     * @param DataObject $dataObject     DataObject context to rename fields for
     * @param array      $renameFieldMap Map of old and new field names.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public static function rename_fields(DataObject $dataObject, array $renameFieldMap)
    {
        $schema    = DB::get_schema();
        $tableName = $dataObject->getSchema()->tableName($dataObject->ClassName);
        if (is_null($tableName)) {
            return;
        }
        foreach ($renameFieldMap as $oldFieldName => $newFieldName) {
            if ($schema->hasField($tableName, $oldFieldName)) {
                if ($schema->hasField($tableName, $newFieldName)) {
                    DB::query("UPDATE \"{$tableName}\" SET {$newFieldName} = {$oldFieldName}");
                    DB::alteration_message("Updated field {$tableName}.{$newFieldName} with the value of {$tableName}.{$oldFieldName}", "changed");
                    DB::query("ALTER TABLE \"{$tableName}\" DROP COLUMN {$oldFieldName}");
                    DB::alteration_message("Dropped field {$tableName}.{$oldFieldName} to make room for the new replacement field {$tableName}.{$newFieldName}.", "deleted");
                } else {
                    $schema->renameField($tableName, $oldFieldName, $newFieldName);
                    DB::alteration_message("Renamed field {$tableName}.{$oldFieldName} to {$tableName}.{$newFieldName}", "created");
                }
            }
        }
    }
}