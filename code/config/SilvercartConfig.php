<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Config
 */

/**
 * The class SilvercartConfig is the Handler for central configurations of
 * SilverCart.
 * Configuration parameter which are defined as a value of $db must have a static
 * getter named like the attribute (without a 'get' in front of it).
 * If a 'get' is put in front of the methods name, it will cause a crash in case
 * of misconfiguration, because SilverStripes 'magic' getter will be overwritten
 * and called in backend by SilverStripes default logic. This will trigger an
 * configuration error, when no configuration is given.
 *
 * @package Silvercart
 * @subpackage Config
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 23.02.2011
 * @license see license file in modules root directory
 */
class SilvercartConfig extends DataObject {
    
    /**
     * Contains the possible values for products per page selectors for
     * storefront users.
     * 
     * This array is directly used for <option> Tags:
     *  'value' => 'Title': <option value="value">Title</option>
     *
     * @var array
     */
    public static $productsPerPageOptions = array(
        '18' => '18',
        '30' => '30',
        '60' => '60',
        '90' => '90',
        //'0'  => 'All' // Activate this only for shops with small product counts
    );
    
    /**
     * Contains the possible values for products per page selectors for
     * storefront users.
     *
     * @var ArrayList
     */
    public static $productsPerPageOptionsForTemplate = null;

    /**
     * The default setting for the CustomerConfig option 'productsPerPage'.
     *
     * @var int
     */
    public static $productsPerPageDefault = 18;
    
    /**
     * Used as SQL limit number for unlimited products per page.
     *
     * @var int
     */
    public static $productsPerPageUnlimitedNumber = 999999;

    /**
     * Contains all registered menus for the storeadmin.
     * 
     * @var array
     */
    public static $registeredMenus = array();

    /**
     * Contains all hidden registered menus for the storeadmin.
     * 
     * @var array
     */
    public static $hiddenRegisteredMenus = array();

    /**
     * Contains URL identifiers for Non-CMS menu items.
     * 
     * @var array
     */
    public static $menuNonCmsIdentifiers = array('silvercart');
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'ShopName'         => 'Varchar(256)',
        'ShopStreet'       => 'Varchar(256)',
        'ShopStreetNumber' => 'Varchar(6)',
        'ShopPostcode'     => 'Varchar(32)',
        'ShopCity'         => 'Varchar(256)',
        'SilvercartVersion'                     => 'VarChar(16)',
        'SilvercartMinorVersion'                => 'VarChar(16)',
        'SilvercartUpdateVersion'               => 'VarChar(16)',
        'DefaultCurrency'                       => 'VarChar(16)',
        'DefaultPriceType'                      => 'Enum("gross,net","gross")',
        'EmailSender'                           => 'VarChar(255)',
        'GlobalEmailRecipient'                  => 'VarChar(255)',
        'DefaultMailRecipient'                  => 'VarChar(255)',
        'DefaultMailOrderNotificationRecipient' => 'VarChar(255)',
        'DefaultContactMessageRecipient'        => 'VarChar(255)',
        'enableSSL'                             => 'Boolean(0)',
        'productsPerPage'                       => 'Int',
        'productGroupsPerPage'                  => 'Int',
        'displayedPaginationPages'              => 'Int',
        'minimumOrderValue'                     => 'SilvercartMoney',
        'useMinimumOrderValue'                  => 'Boolean(0)',
        'freeOfShippingCostsFrom'               => 'SilvercartMoney',
        'useFreeOfShippingCostsFrom'            => 'Boolean(0)',
        'enableBusinessCustomers'               => 'Boolean(0)',
        'enablePackstation'                     => 'Boolean(0)',
        'enableStockManagement'                 => 'Boolean(0)',
        'isStockManagementOverbookable'         => 'Boolean(0)',
        'SkipPaymentStepIfUnique'               => 'Boolean(0)',
        'SkipShippingStepIfUnique'              => 'Boolean(0)',
        'InvoiceAddressIsAlwaysShippingAddress' => 'Boolean(0)',
        'redirectToCartAfterAddToCart'          => 'Boolean(0)',
        'redirectToCheckoutWhenInCart'          => 'Boolean(0)',
        'DisplayWeightsInKilogram'              => 'Boolean(1)',
        'demandBirthdayDateOnRegistration'      => 'Boolean(0)',
        'UseMinimumAgeToOrder'                  => 'Boolean(0)',
        'MinimumAgeToOrder'                     => 'Varchar(3)',
        'addToCartMaxQuantity'                  => 'Int(999)',
        'Locale'                                => 'DBLocale',
        'useDefaultLanguageAsFallback'          => 'Boolean(1)',
        'ShowTaxAndDutyHint'                    => 'Boolean(0)',
        'productDescriptionFieldForCart'        => 'Enum("ShortDescription,LongDescription","ShortDescription")',
        'useProductDescriptionFieldForCart'     => 'Boolean(1)',
        'useStrictSearchRelevance'              => 'Boolean(0)',
        'userAgentBlacklist'                    => 'Text',
        // Put DB definitions for interfaces here
        // Definitions for GeoNames
        'GeoNamesActive'                => 'Boolean',
        'GeoNamesUserName'              => 'VarChar(128)',
        'GeoNamesAPI'                   => 'VarChar(255)',
    );
    
    /**
     * Has-one relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartNoImage'         => 'Image',
        'StandardProductCondition'  => 'SilvercartProductCondition',
        'ShopCountry'               => 'SilvercartCountry',
    );
    
    /**
     * Defaults for empty fields.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion'             => '3.1',
        'SilvercartMinorVersion'        => '0',
        'SilvercartUpdateVersion'       => '0',
        'DefaultPriceType'              => 'gross',
        'GeoNamesActive'                => false,
        'GeoNamesAPI'                   => 'http://api.geonames.org/',
        'productsPerPage'               => 18,
        'productGroupsPerPage'          => 6,
        'displayedPaginationPages'      => 4,
        'addToCartMaxQuantity'          => 999,
        'Locale'                        => 'de_DE',
        'userAgentBlacklist'            => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
    );

    /**
     * Define all required configuration fields in this array. The given fields
     * will be handled in self::Check().
     *
     * @var array
     */
    public static $required_configuration_fields = array(
        'EmailSender',
        'DefaultPriceType',
        'ActiveCountries',
    );

    /**
     * Put here all static attributes which have no db field.
     */
    public static $defaultLayoutEnabled = true;
    public static $defaultLayoutLoaded = false;
    /**
     * The configuration fields should have a static attribute to set after its
     * first call (to prevent redundant logic).
     */
    public static $addToCartMaxQuantity                     = null;
    public static $defaultCurrency                          = null;
    public static $defaultCurrencySymbol                    = null;
    public static $defaultPricetype                         = null;
    public static $emailSender                              = null;
    public static $enableBusinessCustomers                  = null;
    public static $enablePackstation                        = null;
    public static $globalEmailRecipient                     = null;
    public static $priceType                                = null;
    public static $config                                   = null;
    public static $enableSSL                                = null;
    public static $minimumOrderValue                        = null;
    public static $freeOfShippingCostsFrom                  = null;
    public static $useFreeOfShippingCostsFrom               = null;
    public static $useMinimumOrderValue                     = null;
    public static $productsPerPage                          = null;
    public static $silvercartVersion                        = null;
    public static $silvercartMinorVersion                   = null;
    public static $silvercartFullVersion                    = null;
    public static $enableStockManagement                    = null;
    public static $isStockManagementOverbookable            = null;
    public static $redirectToCartAfterAddToCart             = null;
    public static $redirectToCheckoutWhenInCart             = null;
    public static $demandBirthdayDateOnRegistration         = null;
    public static $useMinimumAgeToOrder                     = null;
    public static $minimumAgeToOrder                        = null;
    public static $useDefaultLanguageAsFallback             = null;
    public static $forceLoadingOfDefaultLayout              = false;
    public static $productDescriptionFieldForCart           = null;
    public static $useProductDescriptionFieldForCart        = true;
    public static $useStrictSearchRelevance                 = false;
    public static $defaultMailRecipient                     = null;
    public static $defaultMailOrderNotificationRecipient    = null;
    public static $defaultContactMessageRecipient           = null;
    public static $userAgentBlacklist                       = null;
    public static $skipPaymentStepIfUnique                  = null;
    public static $skipShippingStepIfUnique                 = null;
    public static $invoiceAddressIsAlwaysShippingAddress    = null;
    public static $displayWeightsInKilogram                 = null;
    public static $showTaxAndDutyHint                       = false;
    
    /**
     * Indicator to check whether getCMSFields is called
     *
     * @var boolean
     */
    protected $getCMSFieldsIsCalled = false;

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
     * There is only one config object which is created on installation.
     * This method disables creation of config objects in the modeladmin.
     * 
     * @param Member $member Member to check permission for
     *
     * @return false 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 09.02.2013
     */
    public function canCreate($member = null) {
        return false;
    }

    /**
     * Indicates that the config is translatable
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.03.2012
     */
    public function canTranslate() {
        return true;
    }

    /**
     * Builds and returns the CMS fields.
     *
     * @return FieldList the CMS tabs and fields
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.02.2013
     * @deprecated GeoNames should be outsourced into a module
     */
    public function getCMSFields() {
        $this->getCMSFieldsIsCalled = true;
        $fields = new FieldList(
                $rootTab = new TabSet("Root",
                    $tabMain = new Tab('Main')
                )
        );

        $tabMain->setTitle(_t('SiteTree.TABMAIN', "Main"));

        // Build general toggle group
        $generalConfigurationField = ToggleCompositeField::create(
                'GeneralConfiguration',
                $this->fieldLabel('GeneralConfiguration'),
                array(
                    SilvercartLanguageHelper::prepareLanguageDropdownField($this, 'SiteTree'),
                    new CheckboxField('useDefaultLanguageAsFallback',       $this->fieldLabel('useDefaultLanguageAsFallback')),
                    new TextField('DefaultCurrency',                        $this->fieldLabel('DefaultCurrency')),
                    new DropdownField('DefaultPriceType',                   $this->fieldLabel('DefaultPriceType')),
                )
        )->setHeadingLevel(4)->setStartClosed(false);

        // Build email toggle group
        $emailConfigurationField = ToggleCompositeField::create(
                'EmailConfiguration',
                $this->fieldLabel('EmailConfiguration'),
                array(
                    new TextField('EmailSender',                            $this->fieldLabel('EmailSender')),
                    new TextField('GlobalEmailRecipient',                   $this->fieldLabel('GlobalEmailRecipient')),
                    new TextField('DefaultMailRecipient',                   $this->fieldLabel('DefaultMailRecipient')),
                    new TextField('DefaultMailOrderNotificationRecipient',  $this->fieldLabel('DefaultMailOrderNotificationRecipient')),
                    new TextField('DefaultContactMessageRecipient',         $this->fieldLabel('DefaultContactMessageRecipient'))
                )
        )->setHeadingLevel(4);

        // Build customer toggle group
        $customerConfigurationField = ToggleCompositeField::create(
                'CustomerConfiguration',
                $this->fieldLabel('CustomerConfiguration'),
                array(
                    new CheckboxField('enableBusinessCustomers',            $this->fieldLabel('enableBusinessCustomers')),
                    new CheckboxField('enablePackstation',                  $this->fieldLabel('enablePackstation')),
                    new CheckboxField('demandBirthdayDateOnRegistration',   $this->fieldLabel('demandBirthdayDateOnRegistration')),
                )
        )->setHeadingLevel(4);

        // Build product toggle group
        $productConfigurationField = ToggleCompositeField::create(
                'ProductConfiguration',
                $this->fieldLabel('ProductConfiguration'),
                array(
                    new CheckboxField('enableStockManagement',              $this->fieldLabel('enableStockManagement')),
                    new CheckboxField('isStockManagementOverbookable',      $this->fieldLabel('isStockManagementOverbookable')),
                    new TextField('productsPerPage',                        $this->fieldLabel('productsPerPage')),
                    new TextField('productGroupsPerPage',                   $this->fieldLabel('productGroupsPerPage')),
                    new TextField('displayedPaginationPages',               $this->fieldLabel('displayedPaginationPages')),
                    new UploadField('SilvercartNoImage',                    $this->fieldLabel('SilvercartNoImage')),
                    new CheckboxField('useStrictSearchRelevance',           $this->fieldLabel('useStrictSearchRelevance')),
                    new DropdownField('StandardProductConditionID',         $this->fieldLabel('StandardProductConditionID')),
                )
        )->setHeadingLevel(4);

        // Build checkout toggle group
        $checkoutConfigurationField = ToggleCompositeField::create(
                'CheckoutConfiguration',
                $this->fieldLabel('CheckoutConfiguration'),
                array(
                    new CheckboxField('enableSSL',                          $this->fieldLabel('enableSSL')),
                    new CheckboxField('redirectToCartAfterAddToCart',       $this->fieldLabel('redirectToCartAfterAddToCart')),
                    new CheckboxField('redirectToCheckoutWhenInCart',       $this->fieldLabel('redirectToCheckoutWhenInCart')),
                    new CheckboxField('useProductDescriptionFieldForCart',  $this->fieldLabel('useProductDescriptionFieldForCart')),
                    new DropdownField('productDescriptionFieldForCart',     $this->fieldLabel('productDescriptionFieldForCart')),
                    new TextField('addToCartMaxQuantity',                   $this->fieldLabel('addToCartMaxQuantity')),

                    new CheckboxField('useMinimumOrderValue',               $this->fieldLabel('useMinimumOrderValue')),
                    new SilvercartMoneyField('minimumOrderValue',           $this->fieldLabel('minimumOrderValue')),
                    new CheckboxField('disregardMinimumOrderValue',         $this->fieldLabel('disregardMinimumOrderValue')),

                    new CheckboxField('useFreeOfShippingCostsFrom',         $this->fieldLabel('useFreeOfShippingCostsFrom')),
                    new SilvercartMoneyField('freeOfShippingCostsFrom',     $this->fieldLabel('freeOfShippingCostsFrom')),
                    
                    new CheckboxField('SkipShippingStepIfUnique',           $this->fieldLabel('SkipShippingStepIfUnique')),
                    new CheckboxField('SkipPaymentStepIfUnique',            $this->fieldLabel('SkipPaymentStepIfUnique')),
                    new CheckboxField('DisplayWeightsInKilogram',           $this->fieldLabel('DisplayWeightsInKilogram')),
                    new CheckboxField('ShowTaxAndDutyHint',                 $this->fieldLabel('ShowTaxAndDutyHint')),
                    
                    new CheckboxField('InvoiceAddressIsAlwaysShippingAddress', $this->fieldLabel('InvoiceAddressIsAlwaysShippingAddress')),
                )
        )->setHeadingLevel(4);

        // Build shop data toggle group
        $shopDataConfigurationField = ToggleCompositeField::create(
                'ShopDataConfiguration',
                $this->fieldLabel('ShopDataConfiguration'),
                array(
                    new TextField('ShopName',          $this->fieldLabel('ShopName')),
                    new TextField('ShopStreet',        $this->fieldLabel('ShopStreet')),
                    new TextField('ShopStreetNumber',  $this->fieldLabel('ShopStreetNumber')),
                    new TextField('ShopPostcode',      $this->fieldLabel('ShopPostcode')),
                    new TextField('ShopCity',          $this->fieldLabel('ShopCity')),
                    new DropdownField('ShopCountryID', $this->fieldLabel('ShopCountry'), SilvercartCountry::getPrioritiveDropdownMap()),
                )
        )->setHeadingLevel(4);

        // Build security toggle group
        $securityConfigurationField = ToggleCompositeField::create(
                'SecurityConfiguration',
                $this->fieldLabel('SecurityConfiguration'),
                array(
                    new TextareaField('userAgentBlacklist',                 $this->fieldLabel('userAgentBlacklist')),
                )
        )->setHeadingLevel(4);

        // Add groups to Root.Main
        $fields->addFieldToTab('Root.Main', $generalConfigurationField);
        $fields->addFieldToTab('Root.Main', $emailConfigurationField);
        $fields->addFieldToTab('Root.Main', $customerConfigurationField);
        $fields->addFieldToTab('Root.Main', $productConfigurationField);
        $fields->addFieldToTab('Root.Main', $checkoutConfigurationField);
        $fields->addFieldToTab('Root.Main', $shopDataConfigurationField);
        $fields->addFieldToTab('Root.Main', $securityConfigurationField);

        // Modify field data
        $fields->dataFieldByName('Locale')                                  ->setTitle($this->fieldLabel('Locale'));

        $fields->dataFieldByName('EmailSender')                             ->setRightTitle($this->fieldLabel('EmailSenderRightTitle'));
        $fields->dataFieldByName('GlobalEmailRecipient')                    ->setRightTitle($this->fieldLabel('GlobalEmailRecipientRightTitle'));
        $fields->dataFieldByName('DefaultMailRecipient')                    ->setRightTitle($this->fieldLabel('DefaultMailRecipientRightTitle'));
        $fields->dataFieldByName('DefaultMailOrderNotificationRecipient')   ->setRightTitle($this->fieldLabel('DefaultMailOrderNotificationRecipientRightTitle'));
        $fields->dataFieldByName('DefaultContactMessageRecipient')          ->setRightTitle($this->fieldLabel('DefaultContactMessageRecipientRightTitle'));
        $fields->dataFieldByName('userAgentBlacklist')                      ->setRightTitle($this->fieldLabel('userAgentBlacklistRightTitle'));

        // Add i18n to DefaultPriceType source
        $i18nForDefaultPriceTypeField = array();
        foreach ($this->dbObject('DefaultPriceType')->enumValues() as $value => $label) {
            $i18nForDefaultPriceTypeField[$value] = _t('SilvercartCustomer.' . strtoupper($label), $label);
        }
        $fields->dataFieldByName('DefaultPriceType')->setSource($i18nForDefaultPriceTypeField);

        // Add i18n to productDescriptionFieldForCart source
        $i18nForProductDescriptionField = array();
        foreach ($this->dbObject('productDescriptionFieldForCart')->enumValues() as $productDescriptionField) {
            $i18nForProductDescriptionField[$productDescriptionField] = singleton('SilvercartProduct')->fieldLabel($productDescriptionField);
        }
        $fields->dataFieldByName('productDescriptionFieldForCart')->setSource($i18nForProductDescriptionField);

        $fields->dataFieldByName('StandardProductConditionID')->setEmptyString($this->fieldLabel('StandardProductConditionEmptyString'));

        // Add GeoNames Tab
        $fields->fieldByName('Root')->push(new Tab('GeoNames',                      _t('SilvercartConfig.INTERFACES_GEONAMES')));
        $fields->addFieldToTab('Root.GeoNames', new LiteralField('GeoNamesDescription', _t('SilvercartConfig.GEONAMES_DESCRIPTION')));
        $fields->addFieldToTab('Root.GeoNames', new CheckboxField('GeoNamesActive',     $this->fieldLabel('GeoNamesActive')));
        $fields->addFieldToTab('Root.GeoNames', new TextField('GeoNamesUserName',       $this->fieldLabel('GeoNamesUserName')));
        $fields->addFieldToTab('Root.GeoNames', new TextField('GeoNamesAPI',            $this->fieldLabel('GeoNamesAPI')));

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    /**
     * Get the actions that are sent to the CMS. In
     * your extensions: updateEditFormActions($actions)
     *
     * @return Fieldset
     */
    public function getCMSActions() {
        if (Permission::check('ADMIN') || Permission::check('EDIT_SITECONFIG')) {
            $actions = new FieldList(
                FormAction::create(
                        'save_scconfig',
                        _t('CMSMain.SAVE','Save')
                    )->addExtraClass('ss-ui-action-constructive')->setAttribute('data-icon', 'accept')
            );
            
            
            $exampleDataActions = CompositeField::create()->setTag('fieldset')->addExtraClass('ss-ui-buttonset');
            $exampleDataActions->push(
                FormAction::create(
                        'add_example_data',
                        $this->fieldLabel('addExampleData')
                    )->setAttribute('data-icon', 'addpage')
            );
            $exampleDataActions->push(
                FormAction::create(
                        'add_example_config',
                        $this->fieldLabel('addExampleConfig')
                    )->setAttribute('data-icon', 'addpage')
            );
            
            $actions->push($exampleDataActions);
        } else {
            $actions = new FieldList();
        }

        $this->extend('updateCMSActions', $actions);

        return $actions;
    }

    /**
     * Sets the translations of SilvercartConfigs field labels.
     *
     * @param bool $includerelations Include relations or not
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'ShopData'                              => _t('SilvercartConfig.ShopData'),
                    'ShopName'                              => _t('SilvercartConfig.ShopName'),
                    'ShopStreet'                            => _t('SilvercartConfig.ShopStreet'),
                    'ShopStreetNumber'                      => _t('SilvercartConfig.ShopStreetNumber'),
                    'ShopPostcode'                          => _t('SilvercartConfig.ShopPostcode'),
                    'ShopCity'                              => _t('SilvercartConfig.ShopCity'),
                    'ShopCountry'                           => _t('SilvercartConfig.ShopCountry'),
                    'addToCartMaxQuantity'                  => _t('SilvercartConfig.ADDTOCARTMAXQUANTITY', 'Maximum allowed quantity of a single product in the shopping cart'),
                    'DefaultCurrency'                       => _t('SilvercartConfig.DEFAULTCURRENCY', 'Default currency'),
                    'DefaultPriceType'                      => _t('SilvercartConfig.DEFAULTPRICETYPE', 'Default price type'),
                    'EmailSender'                           => _t('SilvercartConfig.EMAILSENDER', 'Email sender'),
                    'GlobalEmailRecipient'                  => _t('SilvercartConfig.GLOBALEMAILRECIPIENT', 'Global email recipient'),
                    'enableBusinessCustomers'               => _t('SilvercartConfig.ENABLEBUSINESSCUSTOMERS', 'Enable business customers'),
                    'enablePackstation'                     => _t('SilvercartConfig.ENABLEPACKSTATION', 'Enable address input fields for PACKSTATION'),
                    'enableSSL'                             => _t('SilvercartConfig.ENABLESSL', 'Enable SSL'),
                    'enableStockManagement'                 => _t('SilvercartConfig.ENABLESTOCKMANAGEMENT', 'enable stock management'),
                    'minimumOrderValue'                     => _t('SilvercartConfig.MINIMUMORDERVALUE', 'Minimum order value'),
                    'useMinimumOrderValue'                  => _t('SilvercartConfig.USEMINIMUMORDERVALUE', 'Use minimum order value'),
                    'disregardMinimumOrderValue'            => _t('SilvercartConfig.DISREGARD_MINIMUM_ORDER_VALUE'),
                    'useFreeOfShippingCostsFrom'            => _t('SilvercartConfig.USEFREEOFSHIPPINGCOSTSFROM'),
                    'freeOfShippingCostsFrom'               => _t('SilvercartConfig.FREEOFSHIPPINGCOSTSFROM'),
                    'productsPerPage'                       => _t('SilvercartConfig.PRODUCTSPERPAGE', 'Products per page'),
                    'productGroupsPerPage'                  => _t('SilvercartConfig.PRODUCTGROUPSPERPAGE', 'Product groups per page'),
                    'isStockManagementOverbookable'         => _t('SilvercartConfig.QUANTITY_OVERBOOKABLE', 'Is the stock quantity of a product generally overbookable?'),
                    'demandBirthdayDateOnRegistration'      => _t('SilvercartConfig.DEMAND_BIRTHDAY_DATE_ON_REGISTRATION', 'Demand birthday date on registration?'),
                    'UseMinimumAgeToOrder'                  => _t('SilvercartConfig.UseMinimumAgeToOrder', 'Use minimum age to order?'),
                    'MinimumAgeToOrder'                     => _t('SilvercartConfig.MinimumAgeToOrder', 'Minimum age to order'),
                    'GeoNamesActive'                        => _t('SilvercartConfig.GEONAMES_ACTIVE'),
                    'GeoNamesUserName'                      => _t('SilvercartConfig.GEONAMES_USERNAME'),
                    'GeoNamesAPI'                           => _t('SilvercartConfig.GEONAMES_API'),
                    'Locale'                                => _t('SilvercartConfig.DEFAULT_LANGUAGE'),
                    'useDefaultLanguageAsFallback'          => _t('SilvercartConfig.USE_DEFAULT_LANGUAGE'),
                    'productDescriptionFieldForCart'        => _t('SilvercartConfig.PRODUCT_DESCRIPTION_FIELD_FOR_CART'),
                    'useProductDescriptionFieldForCart'     => _t('SilvercartConfig.USE_PRODUCT_DESCRIPTION_FIELD_FOR_CART'),
                    'useStrictSearchRelevance'              => _t('SilvercartConfig.USE_STRICT_SEARCH_RELEVANCE'),
                    'DefaultMailRecipient'                  => _t('SilvercartConfig.DEFAULT_MAIL_RECIPIENT'),
                    'DefaultMailOrderNotificationRecipient' => _t('SilvercartConfig.DEFAULT_MAIL_ORDER_NOTIFICATION_RECIPIENT'),
                    'DefaultContactMessageRecipient'        => _t('SilvercartConfig.DEFAULT_CONTACT_MESSAGE_RECIPIENT'),
                    'userAgentBlacklist'                    => _t('SilvercartConfig.USER_AGENT_BLACKLIST'),
                    'redirectToCartAfterAddToCart'          => _t('SilvercartConfig.REDIRECTTOCARTAFTERADDTOCART'),
                    'redirectToCheckoutWhenInCart'          => _t('SilvercartConfig.redirectToCheckoutWhenInCart'),
                    'addExampleData'                        => _t('SilvercartConfig.ADD_EXAMPLE_DATA'),
                    'addExampleConfig'                      => _t('SilvercartConfig.ADD_EXAMPLE_CONFIGURATION'),
                    'displayedPaginationPages'              => _t('SilvercartConfig.DISPLAYEDPAGINATION'),
                    'SilvercartNoImage'                     => _t('SilvercartConfig.DEFAULT_IMAGE'),
                    'StandardProductConditionID'            => _t('SilvercartProductCondition.USE_AS_STANDARD_CONDITION'),
                    'StandardProductConditionEmptyString'   => _t('SilvercartProductCondition.PLEASECHOOSE'),
                    'GeneralConfiguration'                  => _t('SilvercartConfig.GeneralConfiguration', 'General Configuration'),
                    'EmailConfiguration'                    => _t('SilvercartConfig.EmailConfiguration', 'Email Configuration'),
                    'CustomerConfiguration'                 => _t('SilvercartConfig.CustomerConfiguration', 'Customer Configuration'),
                    'ProductConfiguration'                  => _t('SilvercartConfig.ProductConfiguration', 'Product Configuration'),
                    'CheckoutConfiguration'                 => _t('SilvercartConfig.CheckoutConfiguration', 'Checkout Configuration'),
                    'ShopDataConfiguration'                 => _t('SilvercartConfig.ShopData', 'Shop Data Configuration'),
                    'SecurityConfiguration'                 => _t('SilvercartConfig.SecurityConfiguration', 'Security Configuration'),
                    'SkipPaymentStepIfUnique'               => _t('SilvercartConfig.SKIP_PAYMENT_STEP_IF_UNIQUE'),
                    'SkipShippingStepIfUnique'              => _t('SilvercartConfig.SKIP_SHIPPING_STEP_IF_UNIQUE'),
                    'InvoiceAddressIsAlwaysShippingAddress' => _t('SilvercartConfig.InvoiceAddressIsAlwaysShippingAddress'),
                    'DisplayWeightsInKilogram'              => _t('SilvercartConfig.DISPLAY_WEIGHTS_IN_KILOGRAM'),
                    'ShowTaxAndDutyHint'                    => _t('SilvercartConfig.ShowTaxAndDutyHint'),
                    
                    'EmailSenderRightTitle'                             => _t('SilvercartConfig.EMAILSENDER_INFO'),
                    'GlobalEmailRecipientRightTitle'                    => _t('SilvercartConfig.GLOBALEMAILRECIPIENT_INFO'),
                    'DefaultMailRecipientRightTitle'                    => _t('SilvercartConfig.DEFAULT_MAIL_RECIPIENT_INFO'),
                    'DefaultMailOrderNotificationRecipientRightTitle'   => _t('SilvercartConfig.DEFAULT_MAIL_ORDER_NOTIFICATION_RECIPIENT_INFO'),
                    'DefaultContactMessageRecipientRightTitle'          => _t('SilvercartConfig.DEFAULT_CONTACT_MESSAGE_RECIPIENT_INFO'),
                    'userAgentBlacklistRightTitle'                      => _t('SilvercartConfig.USER_AGENT_BLACKLIST_INFO'),
                )
        );
    }

    /**
     * Sets the translations of SilvercartConfigs summary fields.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public function summaryFields() {
        $summaryFields = parent::summaryFields();
        $summaryFields['DefaultCurrency'] = $this->fieldLabel('DefaultCurrency');
        $summaryFields['EmailSender'] = $this->fieldLabel('EmailSender');
        $summaryFields['GlobalEmailRecipient'] = $this->fieldLabel('GlobalEmailRecipient');
        return $summaryFields;
    }

    /**
     * disable all search fields.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.04.2011
     */
    public function searchableFields() {
        return array();
    }

    /**
     * Remove permission to delete for all members.
     *
     * @param Member $member Member
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.04.2011
     */
    public function canDelete($member = null) {
        return false;
    }

    /**
     * Checks whether there is an existing SilvercartConfig or not before writing.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        if (SilvercartConfig::get()->first()) {
            if (SilvercartConfig::get()->first()->ID !== $this->ID) {
                // is there is an existent SilvercartConfig, do not write another.
                $this->record = array();
            }
        }
    }
    
    /**
     * Returns whether to enable SSL.
     * 
     * @return bool
     */
    public function getEnableSSL() {
        $enableSSL = $this->getField('EnableSSL');
        if (!$this->getCMSFieldsIsCalled) {
            $this->extend('updateEnableSSL', $enableSSL);
        }
        return $enableSSL;
    }

    /**
     * This method checks the required configuration. If there is any missing
     * configuration, an error will be displayed.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public static function Check() {
        if (is_null(self::$required_configuration_fields)) {
            return true;
        }
        if (empty(self::$required_configuration_fields)) {
            return true;
        }
        if (is_array(self::$required_configuration_fields)) {
            $config = self::getConfig();
            foreach (self::$required_configuration_fields as $requiredField) {
                if (empty($requiredField) || is_null($requiredField)) {
                    continue;
                }
                
                if (method_exists('SilvercartConfig', 'check' . $requiredField)) {
                    $method = 'check' . $requiredField;
                    $result = $config->$method();
                    
                    if ($result['status'] === false) {
                        $errorMessage = $result['message'];
                        self::triggerError($errorMessage);
                    }
                } elseif (empty($config->$requiredField)) {
                    $errorMessage = sprintf(_t('SilvercartConfig.ERROR_MESSAGE', 'Required configuration for "%s" is missing. Please <a href="%s/admin/silvercart-config/">log in</a> and choose "SilverCart Configuration -> general configuration" to edit the missing field.'), _t('SilvercartConfig.' . strtoupper($requiredField), $requiredField), Director::baseURL());
                    self::triggerError($errorMessage);
                }
            }
        }
        return true;
    }

    /**
     * Returns the configured default currency.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public static function DefaultCurrency() {
        if (is_null(self::$defaultCurrency)) {
            self::$defaultCurrency = self::getConfig()->DefaultCurrency;
            self::getConfig()->extend('updateDefaultCurrency', self::$defaultCurrency);
        }
        return self::$defaultCurrency;
    }

    /**
     * Returns the configured default currency symbol.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.10.2013
     */
    public static function DefaultCurrencySymbol() {
        if (is_null(self::$defaultCurrencySymbol)) {
            
            $zend_currency = new Zend_Currency(null, i18n::default_locale());
            self::$defaultCurrencySymbol = $zend_currency->getSymbol(self::DefaultCurrency(), i18n::get_locale());
        }
        return self::$defaultCurrencySymbol;
    }

    /**
     * Returns the configured default price type.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public static function DefaultPriceType() {
        if (is_null(self::$defaultPricetype) ||
            empty(self::$defaultPricetype)) {
            self::$defaultPricetype = self::getConfig()->DefaultPriceType;
        }
        return self::$defaultPricetype;
    }
    
    /**
     * Indicates wether the birthday date should be demanded on registration.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.10.2011
     */
    public static function DemandBirthdayDateOnRegistration() {
        if (is_null(self::$demandBirthdayDateOnRegistration)) {
            self::$demandBirthdayDateOnRegistration = self::getConfig()->demandBirthdayDateOnRegistration;
        }
        return self::$demandBirthdayDateOnRegistration;
    }
    
    /**
     * Returns whether there is a minimum age to order.
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.01.2014
     */
    public static function UseMinimumAgeToOrder() {
        if (is_null(self::$useMinimumAgeToOrder)) {
            self::$useMinimumAgeToOrder = self::getConfig()->UseMinimumAgeToOrder;
        }
        return self::$useMinimumAgeToOrder;
    }
    
    /**
     * Returns the minimum age to order.
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.01.2014
     */
    public static function MinimumAgeToOrder() {
        if (is_null(self::$minimumAgeToOrder)) {
            self::$minimumAgeToOrder = self::getConfig()->MinimumAgeToOrder;
        }
        return self::$minimumAgeToOrder;
    }
    
    /**
     * Returns the minimum age to order.
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.01.2014
     */
    public static function MinimumAgeToOrderError() {
        $error = sprintf(
                _t('SilvercartConfig.MinimumAgeToOrderError'),
                self::MinimumAgeToOrder()
        );
        return $error;
    }
    
    /**
     * Checks whether the given birthdate is allowed to order.
     *
     * @param string $birthdate Birthdate in format 'yyyy-mm-dd'
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.01.2014
     */
    public static function CheckMinimumAgeToOrder($birthdate) {
        $ageIsOk       = true;
        $minimumAge    = self::MinimumAgeToOrder();
        $birthdayParts = explode('-', $birthdate);

        $age = (date("Y") - $birthdayParts[0]);
        if (date('md') < date('md', strtotime($birthdate))) {
            $age = $age - 1;
        }
        
        if ($age < $minimumAge) {
            $ageIsOk = false;
        }
        
        return $ageIsOk;
    }

    /**
     * Returns the configured email sender.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public static function EmailSender() {
        if (is_null(self::$emailSender)) {
            self::$emailSender = self::getConfig()->EmailSender;
        }
        return iconv("UTF-8", "ISO-8859-1", self::$emailSender);
    }

    /**
     * Returns if SSL should be used.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.04.2011
     */
    public static function EnableSSL() {
        if (is_null(self::$enableSSL)) {
            self::$enableSSL = self::getConfig()->enableSSL;
        }
        return self::$enableSSL;
    }
    
    /**
     * Returns if stock management is enabled
     * 
     * @return bool is stock management enabled? 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.7.2011
     */
    public static function EnableStockManagement() {
        if (is_null(self::$enableStockManagement)) {
            self::$enableStockManagement = self::getConfig()->enableStockManagement;
        }
        return self::$enableStockManagement;
    }
    
    
    
    /**
     * May a products stock quantity be below zero?
     * 
     * @return bool is stock management overbookable?
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.7.2011
     */
    public static function isStockManagementOverbookable() {
        if (is_null(self::$isStockManagementOverbookable)) {
            self::$isStockManagementOverbookable = self::getConfig()->isStockManagementOverbookable;
        }
        return self::$isStockManagementOverbookable;
    }

    /**
     * Returns the minimum order value if specified
     *
     * @return mixed float|bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.06.2011
     */
    public static function MinimumOrderValue() {
        if (is_null(self::$minimumOrderValue)) {
            self::$minimumOrderValue = self::getConfig()->minimumOrderValue;
        }
        return self::$minimumOrderValue;
    }

    /**
     * Returns if the free of shipping costs from setting should be used.
     *
     * @return Boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.03.2012
     */
    public static function UseFreeOfShippingCostsFrom() {
        if (is_null(self::$useFreeOfShippingCostsFrom)) {
            self::$useFreeOfShippingCostsFrom = self::getConfig()->useFreeOfShippingCostsFrom;
        }
        return self::$useFreeOfShippingCostsFrom;
    }

    /**
     * Returns the free of shipping costs from value if specified.
     * 
     * @param SilvercartCountry $shippingCountry Shipping country
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.07.2013
     */
    public static function FreeOfShippingCostsFrom($shippingCountry = null) {
        self::$freeOfShippingCostsFrom = self::getConfig()->freeOfShippingCostsFrom;
        if (!($shippingCountry instanceof SilvercartCountry) &&
            Controller::curr()->hasMethod('getCombinedStepData')) {
            $checkoutData       = Controller::curr()->getCombinedStepData();
            if (array_key_exists('Shipping_Country', $checkoutData)) {
                $shippingCountryID  = $checkoutData['Shipping_Country'];
                $shippingCountry    = SilvercartCountry::get()->byID($shippingCountryID);
            }
        }
        if ($shippingCountry &&
            !is_null($shippingCountry->freeOfShippingCostsFrom->getAmount()) &&
            is_numeric($shippingCountry->freeOfShippingCostsFrom->getAmount())) {
            $shippingCountry->freeOfShippingCostsFrom->getAmount();
            self::$freeOfShippingCostsFrom = $shippingCountry->freeOfShippingCostsFrom;
        }
        return self::$freeOfShippingCostsFrom;
    }

    /**
     * Returns the SilverCart version.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.10.2011
     */
    public static function SilvercartVersion() {
        if (is_null(self::$silvercartVersion)) {
            self::$silvercartVersion = self::$defaults['SilvercartVersion'];
        }
        return self::$silvercartVersion;
    }

    /**
     * Returns the SilverCart minor version.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.01.2013
     */
    public static function SilvercartMinorVersion() {
        if (is_null(self::$silvercartMinorVersion)) {
            self::$silvercartMinorVersion = self::$defaults['SilvercartMinorVersion'];
        }
        return self::$silvercartMinorVersion;
    }

    /**
     * Returns the full SilverCart version number.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.01.2013
     */
    public static function SilvercartFullVersion() {
        if (is_null(self::$silvercartFullVersion)) {
            $version        = self::SilvercartVersion();
            $minorVersion   = self::SilvercartMinorVersion();
            self::$silvercartFullVersion = $version;
            if (!is_null($minorVersion) &&
                !empty($minorVersion)) {
                self::$silvercartFullVersion .= '.' . $minorVersion;
            }
        }
        return self::$silvercartFullVersion;
    }
    
    /**
     * Returns if the minimum order value shall be used.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.06.2011
     */
    public static function UseMinimumOrderValue() {
        if (is_null(self::$useMinimumOrderValue)) {
            self::$useMinimumOrderValue = self::getConfig()->useMinimumOrderValue;
        }
        return self::$useMinimumOrderValue;
    }

    /**
     * Returns the user agent blacklist
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-01-04
     */
    public static function UserAgentBlacklist() {
        if (is_null(self::$userAgentBlacklist)) {
            self::$userAgentBlacklist = self::getConfig()->userAgentBlacklist;
        }
        return self::$userAgentBlacklist;
    }

    /**
     * Returns the configured default global email recipient.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public static function GlobalEmailRecipient() {
        if (is_null(self::$globalEmailRecipient)) {
            self::$globalEmailRecipient = self::getConfig()->GlobalEmailRecipient;
        }
        return self::$globalEmailRecipient;
    }
    
    /**
     * Returns the configured default setting that determines the default page
     * size for products.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.03.2011
     */
    public static function ProductsPerPage() {
        $silvercartConfig = self::getConfig();

        if ($silvercartConfig->hasField('productsPerPage')) {
            return $silvercartConfig->getField('productsPerPage');
        } else {
            return false;
        }
    }

    /**
     * Returns the configured default setting that determines the default page
     * size for product groups.
     *
     * @return int
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.07.2011
     */
    public static function ProductGroupsPerPage() {
        $silvercartConfig = self::getConfig();

        if ($silvercartConfig->hasField('productGroupsPerPage')) {
            return $silvercartConfig->getField('productGroupsPerPage');
        } else {
            return false;
        }
    }
    
    /**
     * returns the configurated setting for displayedPaginationPages
     * 
     * @return int | false
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 16.08.2012
     */
    public static function DisplayedPaginationPages() {
        $silvercartConfig = self::getConfig();
      
        if ($silvercartConfig->hasField('displayedPaginationPages')) {
            return $silvercartConfig->getField('displayedPaginationPages');
        } else {
            return false;
        }
    }

    /**
     * Returns product description field for shopping cart and order positions.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.07.2012
     */
    public static function productDescriptionFieldForCart() {
        $silvercartConfig = self::getConfig();

        if ($silvercartConfig->hasField('productDescriptionFieldForCart')) {
            return $silvercartConfig->getField('productDescriptionFieldForCart');
        } else {
            return false;
        }
    }

    /**
     * Returns product description field for shopping cart and order positions.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.07.2012
     */
    public static function useProductDescriptionFieldForCart() {
        $silvercartConfig = self::getConfig();

        if ($silvercartConfig->hasField('useProductDescriptionFieldForCart')) {
            return $silvercartConfig->getField('useProductDescriptionFieldForCart');
        } else {
            return false;
        }
    }
    
    /**
     * Returns whether to use strict search relevance or not
     * 
     * @return bool
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.10.2012
     */
    public static function useStrictSearchRelevance() {
        if (is_null(self::$useStrictSearchRelevance)) {
            self::$useStrictSearchRelevance = self::getConfig()->useStrictSearchRelevance;
        }
        return self::$useStrictSearchRelevance;
    }
    
    /**
     * Returns the default mail recipient
     * 
     *@return string email address
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.10.2012
     */
    public static function DefaultMailRecipient() {
        if (is_null(self::$defaultMailRecipient)) {
            self::$defaultMailRecipient = self::getConfig()->DefaultMailRecipient;
            if (empty(self::$defaultMailRecipient)) {
                self::$defaultMailRecipient = Email::getAdminEmail();
            }
        }
        return self::$defaultMailRecipient;
    }
    
    /**
     * Returns the default mail order notification recipient
     * 
     * @return string email address
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.10.2012
     */
    public static function DefaultMailOrderNotificationRecipient() {
        if (is_null(self::$defaultMailOrderNotificationRecipient)) {
            self::$defaultMailOrderNotificationRecipient = self::getConfig()->DefaultMailOrderNotificationRecipient;
            if (empty(self::$defaultMailOrderNotificationRecipient)) {
                self::$defaultMailOrderNotificationRecipient = self::DefaultMailRecipient();
            }
        }
        return self::$defaultMailOrderNotificationRecipient;
    }
    
    /**
     * Returns the default contact message recipient
     * 
     * @return string email address
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.10.2012
     */
    public static function DefaultContactMessageRecipient() {
        if (is_null(self::$defaultContactMessageRecipient)) {
            self::$defaultContactMessageRecipient = self::getConfig()->DefaultContactMessageRecipient;
            if (empty(self::$defaultContactMessageRecipient)) {
                self::$defaultContactMessageRecipient = self::DefaultMailRecipient();
            }
        }
        return self::$defaultContactMessageRecipient;
    }
    
    /**
     * Returns the SkipPaymentStepIfUnique property
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.03.2013
     */
    public static function SkipPaymentStepIfUnique() {
        if (is_null(self::$skipPaymentStepIfUnique)) {
            self::$skipPaymentStepIfUnique = self::getConfig()->SkipPaymentStepIfUnique;
        }
        return self::$skipPaymentStepIfUnique;
    }
    
    /**
     * Returns the SkipShippingStepIfUnique property
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.03.2013
     */
    public static function SkipShippingStepIfUnique() {
        if (is_null(self::$skipShippingStepIfUnique)) {
            self::$skipShippingStepIfUnique = self::getConfig()->SkipShippingStepIfUnique;
        }
        return self::$skipShippingStepIfUnique;
    }
    
    /**
     * Returns the InvoiceAddressIsAlwaysShippingAddress property
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.07.2014
     */
    public static function InvoiceAddressIsAlwaysShippingAddress() {
        if (is_null(self::$invoiceAddressIsAlwaysShippingAddress)) {
            self::$invoiceAddressIsAlwaysShippingAddress = self::getConfig()->InvoiceAddressIsAlwaysShippingAddress;
        }
        return self::$invoiceAddressIsAlwaysShippingAddress;
    }
    
    /**
     * Returns the DisplayWeightsInKilogram property
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.03.2013
     */
    public static function DisplayWeightsInKilogram() {
        if (is_null(self::$displayWeightsInKilogram)) {
            self::$displayWeightsInKilogram = self::getConfig()->DisplayWeightsInKilogram;
        }
        return self::$displayWeightsInKilogram;
    }
    
    /**
     * Returns whether to show tax and duty hint in checkout or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.04.2014
     */
    public static function ShowTaxAndDutyHint() {
        if (is_null(self::$showTaxAndDutyHint)) {
            self::$showTaxAndDutyHint = self::getConfig()->ShowTaxAndDutyHint;
        }
        return self::$showTaxAndDutyHint;
    }

    /**
     * determins weather a customer gets prices shown gross or net dependent on
     * customer's invoice address or class
     *
     * @return string returns "gross" or "net"
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public static function Pricetype() {
        if (is_null(self::$priceType)) {
            $member         = SilvercartCustomer::currentUser();
            $configObject   = self::getConfig();

            $silvercartPluginResult = SilvercartPlugin::call(
                $configObject,
                'overwritePricetype',
                array()
            );

            if (!empty($silvercartPluginResult)) {
                self::$priceType = $silvercartPluginResult;
            } elseif ($member) {
                foreach ($member->Groups() as $group) {
                    if (!empty($group->Pricetype) &&
                        $group->Pricetype != '---') {
                        self::$priceType = $group->Pricetype;
                        break;
                    }
                }
                if (is_null(self::$priceType)) {
                    self::$priceType = self::DefaultPriceType();
                }
            } else {
                self::$priceType = self::DefaultPriceType();
            }
        }
        return self::$priceType;
    }

    /**
     * Returns the SilvercartConfig or triggers an error if not existent.
     *
     * @return SilvercartConfig
     */
    public static function getConfig() {
        if (is_null(self::$config)) {
            self::$config = SilvercartConfig::get()->first();
            if (!self::$config) {
                if (SilvercartTools::isIsolatedEnvironment()) {
                    return false;
                }
                $errorMessage = _t('SilvercartConfig.ERROR_NO_CONFIG', 'SilvercartConfig is missing! Please <a href="/admin/silvercart-config/">log in</a> and choose "SilverCart Configuration -> general configuration" to add the general configuration. ');
                self::triggerError($errorMessage);
            }
        }
        return self::$config;
    }

    /**
     * Returns all hidden registered menus for the storeadmin.
     * 
     * @return array
     */
    public static function getHiddenRegisteredMenus() {
        return self::$hiddenRegisteredMenus;
    }

    /**
     * Returns all registered menus for the storeadmin.
     * 
     * @return array
     */
    public static function getRegisteredMenus() {
        return self::$registeredMenus;
    }

    /**
     * Returns the Non-CMS menu identifiers.
     * 
     * @return array
     */
    public static function getMenuNonCmsIdentifiers() {
        return self::$menuNonCmsIdentifiers;
    }

    /**
     * Returns the default no-image visualisation.
     * 
     * @return mixed Image|bool false
     */
    public static function getNoImage() {
        $configObject = self::getConfig();
        
        return $configObject->SilvercartNoImage();
    }
    
    /**
     * Returns the standard product condition.
     * 
     * @return mixed SilvercartProductCondition|bool false
     */
    public static function getStandardProductCondition() {
        $configObject = self::getConfig();
        
        return $configObject->StandardProductCondition();
    }
    
    /**
     * Alias for RedirectToCartAfterAddToCart.
     * 
     * @return bool
     */
    public static function getRedirectToCartAfterAddToCartAction() {
        return self::RedirectToCartAfterAddToCart();
    }
    
    /**
     * Returns whether to redirect to cart after adding a product into.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2014
     */
    public static function RedirectToCartAfterAddToCart() {
        if (is_null(self::$redirectToCartAfterAddToCart)) {
            self::$redirectToCartAfterAddToCart = self::getConfig()->redirectToCartAfterAddToCart;
        }
        return self::$redirectToCartAfterAddToCart;
    }
    
    /**
     * Returns whether to redirect to checkout after going to cart.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2014
     */
    public static function RedirectToCheckoutWhenInCart() {
        if (is_null(self::$redirectToCheckoutWhenInCart)) {
            self::$redirectToCheckoutWhenInCart = self::getConfig()->redirectToCheckoutWhenInCart;
        }
        return self::$redirectToCheckoutWhenInCart;
    }

    /**
     * Returns the default value for the CustomerConfig option 'productsPerPage'.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public static function getProductsPerPageDefault() {
        return self::$productsPerPageDefault;
    }    
    
    /**
     * used to set self::$productsPerPageOptions, set $includeAllProductsOption true if
     * 'All' should be included 
     * 
     * @param array $productsPerPageOptions   array with all options
     *                                          array(
     *                                              '5'  => '5',
     *                                              '10' => '10',
     *                                              ...
     *                                          )
     * @param bool  $includeAllProductsOption set if 'All' should be included
     * 
     * @return void
     */
    public static function setProductsPerPageOptions(array $productsPerPageOptions, $includeAllProductsOption = false) {
        if (is_array($productsPerPageOptions)) {
            self::$productsPerPageOptions = $productsPerPageOptions;
            if ($includeAllProductsOption) {
                self::$productsPerPageOptions['0'] = _t('SilvercartConfig.PRODUCTSPERPAGE_ALL');
            }
        }
    }

    
    /**
     * Returns an associative array with values for products per page, e.g.
     * array(
     *     '5'  => '5',
     *     '10' => '10',
     *     ...
     * )
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public static function getProductsPerPageOptions() {
        
        if (array_key_exists('0', self::$productsPerPageOptions)) {
            self::$productsPerPageOptions['0'] = _t('SilvercartConfig.PRODUCTSPERPAGE_ALL');
        }
        
        return self::$productsPerPageOptions;
    }
    
    /**
     * Returns an ArrayList with values for products per page, e.g.
     * <pre>
     * <select>
     * <% loop $SilvercartConfig.ProductsPerPageOptionsForTemplate %>
     *      <option value="{$Option}">{$Value}&lt;/option>
     * <% end_loop %>
     * &lt;/select>
     * </pre>
     * 
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.11.2014
     */
    public static function getProductsPerPageOptionsForTemplate() {
        if (is_null(self::$productsPerPageOptionsForTemplate)) {
            self::$productsPerPageOptionsForTemplate = new ArrayList();
            $options = self::getProductsPerPageOptions();
            foreach ($options as $option => $value) {
                self::$productsPerPageOptionsForTemplate->push(
                        new ArrayData(
                                array(
                                    'Option' => $option,
                                    'Value'  => $value,
                                )
                        )
                );
            }
        }
        return self::$productsPerPageOptionsForTemplate;
    }
    
    /**
     * Returns the number that is used as unlimited value for the products
     * per page setting.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public static function getProductsPerPageUnlimitedNumber() {
        return self::$productsPerPageUnlimitedNumber;
    }
    
    /**
     * Diplays an error rendered with Silvercart's error template.
     *
     * @param string $errorMessage the error message to display
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2014
     */
    public static function triggerError($errorMessage) {
        if (SilvercartTools::isIsolatedEnvironment()) {
            $output = $errorMessage;
        } else {
            $elements = array(
                'ErrorMessage' => $errorMessage,
            );
            $output = Controller::curr()->customise($elements)->renderWith(
                            array(
                                'SilvercartErrorPage',
                                'Page'
                            )
            );
        }
        print $output;
        exit();
    }
    
    // Put SilvercartConfiguration::Check() Methods here
    
    /**
     * Checks, whether an activated country exists or not.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.04.2011
     */
    public function checkActiveCountries() {
        $hasActiveCountries = false;
        /*
         * We have to bypass DataObject::get_one() because it would ignore active
         * countries without a translation of the current locale
         */
        $items = SilvercartCountry::get()->filter(array("Active" => 1));
        if ($items) {
            $hasActiveCountries = true;
        }
        return array(
            'status'    => $hasActiveCountries,
            'message'   => sprintf(_t('SilvercartConfig.ERROR_MESSAGE_NO_ACTIVATED_COUNTRY', 'No activated country found. Please <a href="%s/admin/silvercart-config/">log in</a> and choose "SilverCart Configuration -> countries" to activate a country.'), Director::baseURL())
        );
    }

    // Put foreign configurations here

    /**
     * Disables the base layout of SilverCart. This is important if the layout
     * stands in conflict with your projects default layout.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public static function disableDefaultLayout() {
        self::$defaultLayoutEnabled = false;
    }

    /**
     * Returns whether the base layout is enabled or not.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public static function DefaultLayoutEnabled() {
        return self::$defaultLayoutEnabled;
    }

    /**
     * Returns whether the base layout is loaded or not.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.09.2011
     */
    public static function DefaultLayoutLoaded() {
        return self::$defaultLayoutLoaded;
    }

    /**
     * Sets whether the base layout is loaded or not.
     *
     * @param bool $loaded indicator whether the layout is loaded or not
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.09.2011
     */
    public static function setDefaultLayoutLoaded($loaded) {
        self::$defaultLayoutLoaded = $loaded;
    }

    /**
     * Set a Non-CMS menu identifier.
     *
     * @param string $identifier The identifier
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.02.2012
     */
    public static function setMenuNonCmsIdentifier($identifier) {
        if (!in_array($identifier, self::$menuNonCmsIdentifiers)) {
            self::$menuNonCmsIdentifiers[] = $identifier;
        }
    }

    /**
     * enables the creation of test data on /dev/build
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2011
     */
    public static function enableTestData() {
        SilvercartRequireDefaultRecords::enableTestData();
    }

    /**
     * disables the creation of test data on /dev/build. This is set by default,
     * so you do not have to disable creation of test data if it was not enabled
     * before.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2011
     */
    public static function disableTestData() {
        SilvercartRequireDefaultRecords::disableTestData();
    }

    /**
     * adds a new group view type for product lists to the handler.
     *
     * @param string $groupView the class name of the group view to add
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public static function addGroupView($groupView) {
        SilvercartGroupViewHandler::addGroupView($groupView);
    }

    /**
     * adds a new group view type for product group lists to the handler.
     *
     * @param string $groupHolderView the class name of the group view to add
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public static function addGroupHolderView($groupHolderView) {
        SilvercartGroupViewHandler::addGroupHolderView($groupHolderView);
    }

    /**
     * removes a group view for product lists from the handler
     *
     * @param string $groupView the class name of the group view to remove
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public static function removeGroupView($groupView) {
        SilvercartGroupViewHandler::removeGroupView($groupView);
    }

    /**
     * Registers a menu.
     * 
     * @param string $code      The identifier code for this menu
     * @param string $menuTitle The menu title
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.01.2012
     */
    public static function registerMenu($code, $menuTitle) {
        if (!in_array($menuTitle, self::$registeredMenus)) {
            self::$registeredMenus[] = array(
                'code' => $code,
                'name' => $menuTitle
            );
        }
    }

    /**
     * Registers a menu.
     * 
     * @param string $code      The identifier code for this menu
     * @param string $menuTitle The menu title
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.01.2012
     */
    public static function registerHiddenMenu($code) {
        if (!in_array($code, self::$hiddenRegisteredMenus)) {
            self::$hiddenRegisteredMenus[] = $code;
        }
    }

    /**
     * removes a group view for product group lists from the handler
     *
     * @param string $groupHolderView the class name of the group view to remove
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public static function removeGroupHolderView($groupHolderView) {
        SilvercartGroupViewHandler::removeGroupHolderView($groupHolderView);
    }
    
    /**
     * Returns the maximum number of products that can be added to cart for one
     * product.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.11.2011
     */
    public static function addToCartMaxQuantity() {
        if (is_null(self::$addToCartMaxQuantity)) {
            self::$addToCartMaxQuantity = self::getConfig()->addToCartMaxQuantity;
        }
        return self::$addToCartMaxQuantity;
    }

    /**
     * Returns wether to enable business customers or not.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.12.2011
     */
    public static function enableBusinessCustomers() {
        if (is_null(self::$enableBusinessCustomers)) {
            self::$enableBusinessCustomers = self::getConfig()->enableBusinessCustomers;
        }
        return self::$enableBusinessCustomers;
    }
    
    /**
     * Returns wether to enable packstations or not.
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.10.2012
     */
    public static function enablePackstation() {
        if (is_null(self::$enablePackstation)) {
            self::$enablePackstation = self::getConfig()->enablePackstation;
        }
        return self::$enablePackstation;
    }

    /**
     * set the group view to use by default for product lists
     *
     * @param string $defaultGroupView the class name of the group view to use by default
     *
     * @return void
     */
    public static function setDefaultGroupView($defaultGroupView = null) {
        SilvercartGroupViewHandler::setDefaultGroupView($defaultGroupView);
    }

    /**
     * set the group view to use by default for product group lists
     *
     * @param string $defaultGroupHolderView the class name of the group view to use by default
     *
     * @return void
     */
    public static function setDefaultGroupHolderView($defaultGroupHolderView = null) {
        SilvercartGroupViewHandler::setDefaultGroupHolderView($defaultGroupHolderView);
    }

    /**
     * Checks if the installation is complete. We assume a complete
     * installation if the Member table has the field "SilvercartShoppingCartID"
     * that is decorated via "SilvercartCustomer".
     * 
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.06.2012
     * @deprecated use SilvercartTools::isInstallationCompleted() instead
     */
    public static function isInstallationCompleted() {
        return SilvercartTools::isInstallationCompleted();
    }
    
    /**
     * check if a url is reachable
     * This can be used to timeout SOAP connection
     * An http code between 200 and 299 is considered a valid connection.
     *
     * @param string  $url              the URL to check
     * @param integer $conectionTimeout connection timeout in seconds; if set to zero timeout is deactivated
     *
     * @return bool 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.11.2011
     */
    public static function isValidUrl($url, $conectionTimeout = 5) { 
        $curl = curl_init($url);  
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $conectionTimeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5); //The maximum number of seconds to allow cURL functions to execute.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
        curl_exec($curl);  
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
        curl_close($curl);  
        if ($httpcode >= 200 && $httpcode < 300) {  
            return true;  
        }  
        return false; 
    }

    /**
     * Returns whether the given UserAgent string is blacklisted.
     *
     * @param string $userAgent The UserAgent string
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-01-04
     */
    public static function isUserAgentBlacklisted($userAgent) {
        $isBlacklisted         = false;
        $blacklistedUserAgents = explode(PHP_EOL, self::UserAgentBlacklist());

        if (in_array($userAgent, $blacklistedUserAgents)) {
            $isBlacklisted = true;
        }

        return $isBlacklisted;
    }

    /**
     * writes a log entry
     *
     * @param string $context  the context for the log entry
     * @param string $text     the text for the log entry
     * @param string $filename filename to log into
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.11.2010
     */
    public static function Log($context, $text, $filename = 'default') {
        $path = Director::baseFolder() . '/silvercart/log/' . $filename . '.log';
        $text = sprintf(
                "%s - %s - %s" . PHP_EOL,
                date('Y-m-d H:i:s'),
                $context,
                $text
        );
        file_put_contents($path, $text, FILE_APPEND);
    }
    
    /**
     * getter for the default language
     * Returns a default locale for multilingual DataObjects
     *
     * @return string $locale a locale eg. "de_DE", "en_NZ", ...
     *                        Only locales from i18n::get_common_locales() are possible values.
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     */
    public static function DefaultLanguage() {
        return self::Locale();
    }
    
    /**
     * Returns the configs locale
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.03.2012
     */
    public static function Locale() {
        if (self::getConfig() === false) {
            return i18n::default_locale();
        }
        return self::getConfig()->Locale;
    }
    
    /**
     * Determin wether the default language should be used for multilingual DataObjects
     * in case a translation does not exist.
     *
     * @return bool 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     */
    public static function useDefaultLanguageAsFallback() {
        if (is_null(self::$useDefaultLanguageAsFallback)) {
            if (!self::getConfig() === false) {
                self::$useDefaultLanguageAsFallback = self::getConfig()->useDefaultLanguageAsFallback;
            }
        }
        return self::$useDefaultLanguageAsFallback;
    }
}
