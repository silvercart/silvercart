<?php

namespace SilverCart\Dev;

class DBMigration {
    
    /*
     * @todo
     * Move RegisterConfirmationPage::Content to RegistrationPage::WelcomeContent
     */
    
    private static $migration_map = array(
        'Address' => array(
            'has_one' => array(
                'SilvercartCountry' => 'Country',
            ),
        ),
        'Country' => array(
            'many_many' => array(
                'SilvercartPaymentMethods' => 'PaymentMethods',
            ),
        ),
        'CountryTranslation' => array(
            'has_one' => array(
                'SilvercartCountry' => 'Country',
            ),
        ),
        'Member' => array(
            'has_one' => array(
                'SilvercartShoppingCart'         => 'ShoppingCart',
                'SilvercartInvoiceAddress'       => 'InvoiceAddress',
                'SilvercartShippingAddress'      => 'ShippingAddress',
                'SilvercartCustomerConfig'       => 'CustomerConfig',
                'SilvercartShippingAddressInUse' => 'ShippingAddressInUse',
            ),
        ),
        'Order' => array(
            'has_one' => array(
                'SilvercartShippingAddress' => 'ShippingAddress',
                'SilvercartInvoiceAddress'  => 'InvoiceAddress',
                'SilvercartPaymentMethod'   => 'PaymentMethod',
                'SilvercartShippingMethod'  => 'ShippingMethod',
                'SilvercartOrderStatus'     => 'OrderStatus',
                'SilvercartShippingFee'     => 'ShippingFee',
            ),
        ),
        'OrderLog' => array(
            'has_one' => array(
                'SilvercartOrder' => 'Order',
            ),
        ),
        'OrderPosition' => array(
            'has_one' => array(
                'SilvercartOrder'   => 'Order',
                'SilvercartProduct' => 'Product',
            ),
        ),
        'OrderStatus' => array(
            'many_many' => array(
                'SilvercartShopEmails'  => 'ShopEmails'
            ),
        ),
        'OrderStatusTranslation' => array(
            'has_one' => array(
                'SilvercartOrderStatus' => 'OrderStatus'
            ),
        ),
        'ShoppingCart' => array(
            'many_many' => array(
                'SilvercartProducts' => 'Products'
            ),
        ),
        'ShoppingCartPosition' => array(
            'has_one' => array(
                'SilvercartProduct'      => 'Product',
                'SilvercartShoppingCart' => 'ShoppingCart'
            ),
        ),
        'HandlingCost' => array(
            'has_one' => array(
                'SilvercartTax'           => 'Tax',
                'SilvercartPaymentMethod' => 'PaymentMethod',
                'SilvercartZone'          => 'Zone',
            ),
        ),
        'PaymentMethod' => array(
            'has_one' => array(
                'SilvercartZone' => 'Zone',
            ),
            'many_many' => array(
                'SilvercartShippingMethods' => 'ShippingMethods',
            ),
        ),
        'AvailabilityStatusTranslation' => array(
            'has_one' => array(
                'SilvercartAvailabilityStatus' => 'AvailabilityStatus'
            ),
        ),
        'File' => array(
            'has_one' => array(
                'SilvercartProduct'         => 'Product',
                'SilvercartDownloadPage'    => 'DownloadPage',
            ),
        ),
        'FileTranslation' => array(
            'has_one' => array(
                'SilvercartFile' => 'File',
            ),
        ),
        'Image' => array(
            'has_one' => array(
                'SilvercartProduct'         => 'Product',
                'SilvercartPaymentMethod'   => 'PaymentMethod',
            ),
        ),
        'ImageTranslation' => array(
            'has_one' => array(
                'SilvercartImage' => 'Image',
            ),
        ),
        'ManufacturerTranslation' => array(
            'has_one' => array(
                'SilvercartManufacturer' => 'Manufacturer',
            ),
        ),
        'Product' => array(
            'has_one' => array(
                'SilvercartTax'                 => 'Tax',
                'SilvercartManufacturer'        => 'Manufacturer',
                'SilvercartProductGroup'        => 'ProductGroup',
                'SilvercartMasterProduct'       => 'MasterProduct',
                'SilvercartAvailabilityStatus'  => 'AvailabilityStatus',
                'SilvercartProductCondition'    => 'ProductCondition',
                'SilvercartQuantityUnit'        => 'QuantityUnit',
            ),
            'many_many' => array(
                'SilvercartProductGroupMirrorPages' => 'ProductGroupMirrorPages'
            ),
        ),
        'ProductTranslation' => array(
            'has_one' => array(
                'SilvercartProduct' => 'Product',
            ),
        ),
        'ProductConditionTranslation' => array(
            'has_one' => array(
                'SilvercartProductConditionTranslation' => 'ProductConditionTranslation',
            ),
        ),
        'QuantityUnitTranslation' => array(
            'has_one' => array(
                'SilvercartQuantityUnit' => 'QuantityUnit',
            ),
        ),
        'TaxTranslation' => array(
            'has_one' => array(
                'SilvercartTax' => 'Tax',
            ),
        ),
        'CarrierTranslation' => array(
            'has_one' => array(
                'SilvercartCarrier' => 'Carrier',
            ),
        ),
        'ShippingFee' => array(
            'has_one' => array(
                'SilvercartZone'           => 'Zone',
                'SilvercartShippingMethod' => 'ShippingMethod',
                'SilvercartTax'            => 'Tax',
            ),
        ),
        'ShippingMethod' => array(
            'has_one' => array(
                'SilvercartCarrier' => 'Carrier',
            ),
            'many_many' => array(
                'SilvercartZones'          => 'Zones',
                'SilvercartCustomerGroups' => 'CustomerGroups',
            ),
        ),
        'ShippingMethodTranslation' => array(
            'has_one' => array(
                'SilvercartShippingMethod' => 'ShippingMethod',
            ),
        ),
        'Zone' => array(
            'many_many' => array(
                'SilvercartCountries' => 'Countries',
                'SilvercartCarriers'  => 'Carriers',
            ),
        ),
        'ZoneTranslation' => array(
            'has_one' => array(
                'SilvercartZone' => 'Zone',
            ),
        ),
        'BargainProductsWidgetTranslation' => array(
            'has_one' => array(
                'SilvercartBargainProductsWidget' => 'BargainProductsWidget',
            ),
        ),
        'ContactMessage' => array(
            'has_one' => array(
                'SilvercartCountry' => 'Country',
            ),
        ),
        'ShopEmailTranslation' => array(
            'has_one' => array(
                'SilvercartShopEmail' => 'ShopEmail',
            ),
        ),
        'SiteConfig' => array(
            'has_one' => array(
                'SilvercartLogo' => 'ShopLogo',
            ),
        ),
    );
    
}
