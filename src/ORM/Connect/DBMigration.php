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
     * Returns the current DB field value of $fieldName by the given $dataObject 
     * context.
     * 
     * @param DataObject $dataObject Data object context
     * @param string     $fieldName  DB field name
     * 
     * @return string
     */
    public static function get_field_value_and_remove_field(DataObject $dataObject, string $fieldName) : string
    {
        $value = '';
        if (!$dataObject->exists()) {
            return $value;
        }
        $schema    = DB::get_schema();
        $tableName = $dataObject->getSchema()->tableName($dataObject->ClassName);
        if ($tableName === null) {
            return $value;
        }
        if ($schema->hasField($tableName, $fieldName)) {
            $result = DB::query("SELECT {$fieldName} FROM \"{$tableName}\" WHERE ID = {$dataObject->ID}");
            DB::alteration_message("Extracted field value {$tableName}.{$fieldName} [#{$dataObject->ID}]", "changed");
            $value = $result->first()[$fieldName];
            DB::query("ALTER TABLE \"{$tableName}\" DROP COLUMN {$fieldName}");
            DB::alteration_message("Dropped field {$tableName}.{$fieldName}.", "deleted");
        }
        return (string) $value;
    }

    /**
     * Moves the values of the fields defined in $renameFieldMap from $sourceObject
     * to $targetObject.
     * The source DB fields in $sourceObject will be removed from DB.
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
     * @param DataObject $sourceObject   Source data object
     * @param DataObject $targetObject   Target data object
     * @param array      $renameFieldMap The field map (old name => new name)
     * 
     * @return void
     */
    public static function move_fields(DataObject $sourceObject, DataObject $targetObject, array $renameFieldMap) : void
    {
        $schema    = DB::get_schema();
        $sourceTableName = $sourceObject->getSchema()->tableName($sourceObject->ClassName);
        $targetTableName = $targetObject->getSchema()->tableName($targetObject->ClassName);
        if ($sourceTableName === null
         || $targetTableName === null
        ) {
            return;
        }
        foreach ($renameFieldMap as $oldFieldName => $newFieldName) {
            if ($schema->hasField($sourceTableName, $oldFieldName)
             && $schema->hasField($targetTableName, $newFieldName)
            ) {
                DB::query("UPDATE \"{$targetTableName}\" SET {$newFieldName} = {$oldFieldName}");
                DB::alteration_message("Updated field {$targetTableName}.{$newFieldName} with the value of {$sourceTableName}.{$oldFieldName}", "changed");
                DB::query("ALTER TABLE \"{$sourceTableName}\" DROP COLUMN {$oldFieldName}");
                DB::alteration_message("Dropped field {$sourceTableName}.{$oldFieldName} to make room for the new replacement field {$targetTableName}.{$newFieldName}.", "deleted");
            }
        }
    }
    
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
    public static function rename_fields(DataObject $dataObject, array $renameFieldMap) : void
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
    
    /**
     * Removes database columns for the given $dataObject like defined in 
     * $removeFields.
     * 
     * <code>
     * // expected format for $removeFields
     * $removeFields = [
     *     'FieldName1',
     *     'FieldName2',
     *     'FieldName3',
     *     'FieldName4',
     * ];
     * </code>
     * 
     * @param DataObject $dataObject   DataObject context to rename fields for
     * @param array      $removeFields List of field names to remove.
     * 
     * @return void
     */
    public static function remove_fields(DataObject $dataObject, array $removeFields) : void
    {
        $schema    = DB::get_schema();
        $tableName = $dataObject->getSchema()->tableName($dataObject->ClassName);
        if (is_null($tableName)) {
            return;
        }
        foreach ($removeFields as $fieldName) {
            if ($schema->hasField($tableName, $fieldName)) {
                DB::query("ALTER TABLE \"{$tableName}\" DROP COLUMN {$fieldName}");
                DB::alteration_message("Dropped field {$tableName}.{$fieldName}.", "deleted");
            }
        }
    }
    
    /**
     * Returns whether the given $dataObject has the given $fieldName in DB.
     * 
     * @param DataObject $dataObject DataObject
     * @param string     $fieldName  Field name
     * 
     * @return bool
     */
    public static function has_field(DataObject $dataObject, string $fieldName) : bool
    {
        $has       = false;
        $schema    = DB::get_schema();
        $tableName = $dataObject->getSchema()->tableName($dataObject->ClassName);
        if ($tableName !== null
         && $schema->hasField($tableName, $fieldName)
        ) {
            $has = true;
        }
        return $has;
    }
}