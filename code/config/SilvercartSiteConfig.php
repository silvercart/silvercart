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
 * This class is used to add a translation section to the original SiteConfig 
 * object in the cms section.
 *
 * @package Silvercart
 * @subpackage Config
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.12.2015
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartSiteConfig extends DataExtension {
    
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = array(
        'ShopName'         => 'Varchar(256)',
        'ShopStreet'       => 'Varchar(256)',
        'ShopStreetNumber' => 'Varchar(6)',
        'ShopPostcode'     => 'Varchar(32)',
        'ShopCity'         => 'Varchar(256)',
        'SilvercartVersion'                     => 'VarChar(16)',
        'SilvercartMinorVersion'                => 'VarChar(16)',
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
        'DefaultLocale'                         => 'DBLocale',
        'useDefaultLanguageAsFallback'          => 'Boolean(1)',
        'ShowTaxAndDutyHint'                    => 'Boolean(0)',
        'productDescriptionFieldForCart'        => 'Enum("ShortDescription,LongDescription","ShortDescription")',
        'useProductDescriptionFieldForCart'     => 'Boolean(1)',
        'useStrictSearchRelevance'              => 'Boolean(0)',
        'userAgentBlacklist'                    => 'Text',
        'ColorScheme'                           => 'Varchar(256)',
        'GoogleAnalyticsTrackingCode'   => 'Text',
        'GoogleConversionTrackingCode'  => 'Text',
        'GoogleWebmasterCode'           => 'Text',
        'PiwikTrackingCode'             => 'Text',
        'FacebookLink'                  => 'Text',
        'TwitterLink'                   => 'Text',
        'XingLink'                      => 'Text',
    );
    
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = array(
        'SilvercartLogo'            => 'Image',
        'SilvercartNoImage'         => 'Image',
        'StandardProductCondition'  => 'SilvercartProductCondition',
        'ShopCountry'               => 'SilvercartCountry',
    );
    
    /**
     * Defaults for empty fields.
     *
     * @var array
     */
    private static $defaults = array(
        'SilvercartVersion'             => '3.1',
        'SilvercartMinorVersion'        => '0',
        'DefaultPriceType'              => 'gross',
        'productsPerPage'               => 18,
        'productGroupsPerPage'          => 6,
        'displayedPaginationPages'      => 4,
        'addToCartMaxQuantity'          => 999,
        'DefaultLocale'                 => 'de_DE',
        'userAgentBlacklist'            => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
        'ColorScheme'                   => 'blue',
    );
    
    /**
     * Indicator to check whether getCMSFields is called
     *
     * @var boolean
     */
    protected $getCMSFieldsIsCalled = false;
    
    /**
     * Will hold the Locale of the SiteConfig object to be written.
     *
     * @var string
     */
    private static $duplicate_config_locale = null;
    
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
     * Updates the fields labels
     *
     * @param array &$labels Labels to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.12.2015
     */
    public function updateFieldLabels(&$labels) {
        $labels = array_merge(
                $labels,
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
                    'DefaultLocale'                         => _t('SilvercartConfig.DEFAULT_LANGUAGE'),
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
                    'addExampleDataDesc'                    => _t('SilvercartConfig.ADD_EXAMPLE_DATA_DESCRIPTION'),
                    'addExampleConfig'                      => _t('SilvercartConfig.ADD_EXAMPLE_CONFIGURATION'),
                    'addExampleConfigDesc'                  => _t('SilvercartConfig.ADD_EXAMPLE_CONFIGURATION_DESCRIPTION'),
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
                    
                    'GoogleAnalyticsTrackingCode'   => _t('SilvercartSiteConfig.GOOGLE_ANALYTICS_TRACKING_CODE'),
                    'GoogleConversionTrackingCode'  => _t('SilvercartSiteConfig.GOOGLE_CONVERSION_TRACKING_CODE'),
                    'GoogleWebmasterCode'           => _t('SilvercartSiteConfig.GOOGLE_WEBMASTER_CODE'),
                    'PiwikTrackingCode'             => _t('SilvercartSiteConfig.PIWIK_TRACKING_CODE'),
                    'FacebookLink'                  => _t('SilvercartSiteConfig.FACEBOOK_LINK'),
                    'TwitterLink'                   => _t('SilvercartSiteConfig.TWITTER_LINK'),
                    'XingLink'                      => _t('SilvercartSiteConfig.XING_LINK'),
                    'SeoTab'                        => _t('Silvercart.SEO'),
                    'SocialMediaTab'                => _t('Silvercart.SOCIALMEDIA'),
                    'TranslationsTab'               => _t('Silvercart.TRANSLATIONS'),
                    'CreateTransHeader'             => _t('Translatable.CREATE'),
                    'CreateTransDescription'        => _t('Translatable.CREATE_TRANSLATION_DESC'),
                    'NewTransLang'                  => _t('Translatable.NEWLANGUAGE'),
                    'createsitetreetranslation'     => _t('Translatable.CREATEBUTTON'),
                    'createsitetreetranslationDesc' => _t('Translatable.CREATEBUTTON_DESC'),
                    'publishsitetree'               => _t('Translatable.PUBLISHBUTTON'),
                    'ExistingTransHeader'           => _t('Translatable.EXISTING'),
                    'CurrentLocale'                 => _t('Translatable.CURRENTLOCALE'),

                    'SilvercartLogo'           => _t('SilvercartConfig.SilvercartLogo'),
                    'SilvercartLogoDesc'       => _t('SilvercartConfig.SilvercartLogoDesc'),
                    'ColorScheme'              => _t('SilvercartConfig.ColorScheme'),
                    'ColorSchemeTab'           => _t('SilvercartConfig.ColorSchemeTab'),
                    'ColorSchemeConfiguration' => _t('SilvercartConfig.ColorSchemeConfiguration'),
                )
        );
    }
    
    /**
     * Adds a translation section
     *
     * @param FieldList $fields The FieldList
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2013
     */
    public function updateCMSFields(FieldList $fields) {
        $this->getCMSFieldsIsCalled = true;
        $fields->findOrMakeTab('Root.SEO')          ->setTitle($this->owner->fieldLabel('SeoTab'));
        $fields->findOrMakeTab('Root.SocialMedia')  ->setTitle($this->owner->fieldLabel('SocialMediaTab'));
        $fields->findOrMakeTab('Root.Translations') ->setTitle($this->owner->fieldLabel('TranslationsTab'));
        
        $googleWebmasterCodeField           = new TextField('GoogleWebmasterCode',              $this->owner->fieldLabel('GoogleWebmasterCode'));
        $googleAnalyticsTrackingCodeField   = new TextareaField('GoogleAnalyticsTrackingCode',  $this->owner->fieldLabel('GoogleAnalyticsTrackingCode'));
        $googleConversionTrackingCodeField  = new TextareaField('GoogleConversionTrackingCode', $this->owner->fieldLabel('GoogleConversionTrackingCode'));
        $piwikTrackingCodeField             = new TextareaField('PiwikTrackingCode',            $this->owner->fieldLabel('PiwikTrackingCode'));
        
        $fields->addFieldToTab('Root.SEO', $googleWebmasterCodeField);
        $fields->addFieldToTab('Root.SEO', $googleAnalyticsTrackingCodeField);
        $fields->addFieldToTab('Root.SEO', $googleConversionTrackingCodeField);
        $fields->addFieldToTab('Root.SEO', $piwikTrackingCodeField);
        
        $facebookLinkField  = new TextField('FacebookLink',     $this->owner->fieldLabel('FacebookLink'));
        $twitterLinkField   = new TextField('TwitterLink',      $this->owner->fieldLabel('TwitterLink'));
        $xingLinkField      = new TextField('XingLink',         $this->owner->fieldLabel('XingLink'));
        
        $fields->addFieldToTab('Root.SocialMedia', $facebookLinkField);
        $fields->addFieldToTab('Root.SocialMedia', $twitterLinkField);
        $fields->addFieldToTab('Root.SocialMedia', $xingLinkField);
        
        $translatable = new Translatable();
        $translatable->setOwner($this->owner);
        $translatable->updateCMSFields($fields);
        
        $localeField    = new TextField('CurrentLocale',                        $this->owner->fieldLabel('CurrentLocale'),              i18n::get_locale_name($this->owner->Locale));
        $createButton   = new InlineFormAction('createsitetreetranslation',     $this->owner->fieldLabel('createsitetreetranslation'));
        $publishButton  = new InlineFormAction('publishsitetree',               $this->owner->fieldLabel('publishsitetree'));
        
        $localeField->setReadonly(true);
        $localeField->setDisabled(true);
        $createButton->setRightTitle($this->owner->fieldLabel('createsitetreetranslationDesc'));
        $createButton->includeDefaultJS(false);
        $createButton->addExtraClass('createTranslationButton');
        $publishButton->includeDefaultJS(false);
        $publishButton->addExtraClass('createTranslationButton');
        
        $fields->addFieldToTab('Root.Translations', $localeField,   'CreateTransHeader');
        $fields->addFieldToTab('Root.Translations', $createButton,  'createtranslation');
        $fields->addFieldToTab('Root.Translations', $publishButton, 'createtranslation');
        $fields->removeByName('createtranslation');
        
        $this->getCMSFieldsForSilvercart($fields);
    }
    
    /**
     * Builds and returns the CMS fields.
     *
     * @return FieldList the CMS tabs and fields
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.02.2013
     */
    public function getCMSFieldsForSilvercart(FieldList $fields) {
        $tab = $fields->findOrMakeTab('Root.Silvercart', 'SilverCart Shop');
        $tab->setTitle('SilverCart Shop');

        // Build general toggle group
        $generalConfigurationField = ToggleCompositeField::create(
                'GeneralConfiguration',
                $this->owner->fieldLabel('GeneralConfiguration'),
                array(
                    SilvercartLanguageHelper::prepareLanguageDropdownField($this->owner, 'SiteTree', 'DefaultLocale'),
                    new CheckboxField('useDefaultLanguageAsFallback',       $this->owner->fieldLabel('useDefaultLanguageAsFallback')),
                    new TextField('DefaultCurrency',                        $this->owner->fieldLabel('DefaultCurrency')),
                    new DropdownField('DefaultPriceType',                   $this->owner->fieldLabel('DefaultPriceType')),
                )
        )->setHeadingLevel(4)->setStartClosed(false);

        // Build email toggle group
        $emailConfigurationField = ToggleCompositeField::create(
                'EmailConfiguration',
                $this->owner->fieldLabel('EmailConfiguration'),
                array(
                    new TextField('EmailSender',                            $this->owner->fieldLabel('EmailSender')),
                    new TextField('GlobalEmailRecipient',                   $this->owner->fieldLabel('GlobalEmailRecipient')),
                    new TextField('DefaultMailRecipient',                   $this->owner->fieldLabel('DefaultMailRecipient')),
                    new TextField('DefaultMailOrderNotificationRecipient',  $this->owner->fieldLabel('DefaultMailOrderNotificationRecipient')),
                    new TextField('DefaultContactMessageRecipient',         $this->owner->fieldLabel('DefaultContactMessageRecipient'))
                )
        )->setHeadingLevel(4);

        // Build customer toggle group
        $customerConfigurationField = ToggleCompositeField::create(
                'CustomerConfiguration',
                $this->owner->fieldLabel('CustomerConfiguration'),
                array(
                    new CheckboxField('enableBusinessCustomers',            $this->owner->fieldLabel('enableBusinessCustomers')),
                    new CheckboxField('enablePackstation',                  $this->owner->fieldLabel('enablePackstation')),
                    new CheckboxField('demandBirthdayDateOnRegistration',   $this->owner->fieldLabel('demandBirthdayDateOnRegistration')),
                )
        )->setHeadingLevel(4);

        // Build product toggle group
        $productConfigurationField = ToggleCompositeField::create(
                'ProductConfiguration',
                $this->owner->fieldLabel('ProductConfiguration'),
                array(
                    new CheckboxField('enableStockManagement',              $this->owner->fieldLabel('enableStockManagement')),
                    new CheckboxField('isStockManagementOverbookable',      $this->owner->fieldLabel('isStockManagementOverbookable')),
                    new TextField('productsPerPage',                        $this->owner->fieldLabel('productsPerPage')),
                    new TextField('productGroupsPerPage',                   $this->owner->fieldLabel('productGroupsPerPage')),
                    new TextField('displayedPaginationPages',               $this->owner->fieldLabel('displayedPaginationPages')),
                    new UploadField('SilvercartNoImage',                    $this->owner->fieldLabel('SilvercartNoImage')),
                    new CheckboxField('useStrictSearchRelevance',           $this->owner->fieldLabel('useStrictSearchRelevance')),
                    new DropdownField('StandardProductConditionID',         $this->owner->fieldLabel('StandardProductConditionID')),
                )
        )->setHeadingLevel(4);

        // Build checkout toggle group
        $checkoutConfigurationField = ToggleCompositeField::create(
                'CheckoutConfiguration',
                $this->owner->fieldLabel('CheckoutConfiguration'),
                array(
                    new CheckboxField('enableSSL',                          $this->owner->fieldLabel('enableSSL')),
                    new CheckboxField('redirectToCartAfterAddToCart',       $this->owner->fieldLabel('redirectToCartAfterAddToCart')),
                    new CheckboxField('redirectToCheckoutWhenInCart',       $this->owner->fieldLabel('redirectToCheckoutWhenInCart')),
                    new CheckboxField('useProductDescriptionFieldForCart',  $this->owner->fieldLabel('useProductDescriptionFieldForCart')),
                    new DropdownField('productDescriptionFieldForCart',     $this->owner->fieldLabel('productDescriptionFieldForCart')),
                    new TextField('addToCartMaxQuantity',                   $this->owner->fieldLabel('addToCartMaxQuantity')),

                    new CheckboxField('useMinimumOrderValue',               $this->owner->fieldLabel('useMinimumOrderValue')),
                    new SilvercartMoneyField('minimumOrderValue',           $this->owner->fieldLabel('minimumOrderValue')),
                    new CheckboxField('disregardMinimumOrderValue',         $this->owner->fieldLabel('disregardMinimumOrderValue')),

                    new CheckboxField('useFreeOfShippingCostsFrom',         $this->owner->fieldLabel('useFreeOfShippingCostsFrom')),
                    new SilvercartMoneyField('freeOfShippingCostsFrom',     $this->owner->fieldLabel('freeOfShippingCostsFrom')),
                    
                    new CheckboxField('SkipShippingStepIfUnique',           $this->owner->fieldLabel('SkipShippingStepIfUnique')),
                    new CheckboxField('SkipPaymentStepIfUnique',            $this->owner->fieldLabel('SkipPaymentStepIfUnique')),
                    new CheckboxField('DisplayWeightsInKilogram',           $this->owner->fieldLabel('DisplayWeightsInKilogram')),
                    new CheckboxField('ShowTaxAndDutyHint',                 $this->owner->fieldLabel('ShowTaxAndDutyHint')),
                    
                    new CheckboxField('InvoiceAddressIsAlwaysShippingAddress', $this->owner->fieldLabel('InvoiceAddressIsAlwaysShippingAddress')),
                )
        )->setHeadingLevel(4);

        // Build shop data toggle group
        $shopDataConfigurationField = ToggleCompositeField::create(
                'ShopDataConfiguration',
                $this->owner->fieldLabel('ShopDataConfiguration'),
                array(
                    new TextField('ShopName',          $this->owner->fieldLabel('ShopName')),
                    new TextField('ShopStreet',        $this->owner->fieldLabel('ShopStreet')),
                    new TextField('ShopStreetNumber',  $this->owner->fieldLabel('ShopStreetNumber')),
                    new TextField('ShopPostcode',      $this->owner->fieldLabel('ShopPostcode')),
                    new TextField('ShopCity',          $this->owner->fieldLabel('ShopCity')),
                    new DropdownField('ShopCountryID', $this->owner->fieldLabel('ShopCountry'), SilvercartCountry::getPrioritiveDropdownMap()),
                )
        )->setHeadingLevel(4);

        // Build security toggle group
        $securityConfigurationField = ToggleCompositeField::create(
                'SecurityConfiguration',
                $this->owner->fieldLabel('SecurityConfiguration'),
                array(
                    new TextareaField('userAgentBlacklist',                 $this->owner->fieldLabel('userAgentBlacklist')),
                )
        )->setHeadingLevel(4);

        // Build example data toggle group
        $addExampleDataButton   = new InlineFormAction('add_example_data',   $this->owner->fieldLabel('addExampleData'));
        $addExampleConfigButton = new InlineFormAction('add_example_config', $this->owner->fieldLabel('addExampleConfig'));
        $exampleDataField       = ToggleCompositeField::create(
                'ExampleData',
                $this->owner->fieldLabel('addExampleData'),
                array(
                    $addExampleDataButton,
                    $addExampleConfigButton,
                )
        )->setHeadingLevel(4);
        
        $addExampleDataButton->setRightTitle($this->owner->fieldLabel('addExampleDataDesc'));
        $addExampleDataButton->includeDefaultJS(false);
        $addExampleDataButton->setAttribute('data-icon', 'addpage');
        $addExampleConfigButton->setRightTitle($this->owner->fieldLabel('addExampleConfigDesc'));
        $addExampleConfigButton->includeDefaultJS(false);
        $addExampleConfigButton->setAttribute('data-icon', 'addpage');

        // Add groups to Root.Main
        $fields->addFieldToTab('Root.Silvercart', $generalConfigurationField);
        $fields->addFieldToTab('Root.Silvercart', $emailConfigurationField);
        $fields->addFieldToTab('Root.Silvercart', $customerConfigurationField);
        $fields->addFieldToTab('Root.Silvercart', $productConfigurationField);
        $fields->addFieldToTab('Root.Silvercart', $checkoutConfigurationField);
        $fields->addFieldToTab('Root.Silvercart', $shopDataConfigurationField);
        $fields->addFieldToTab('Root.Silvercart', $securityConfigurationField);
        $fields->addFieldToTab('Root.Silvercart', $exampleDataField);

        // Modify field data
        $fields->dataFieldByName('DefaultLocale')                           ->setTitle($this->owner->fieldLabel('DefaultLocale'));

        $fields->dataFieldByName('EmailSender')                             ->setRightTitle($this->owner->fieldLabel('EmailSenderRightTitle'));
        $fields->dataFieldByName('GlobalEmailRecipient')                    ->setRightTitle($this->owner->fieldLabel('GlobalEmailRecipientRightTitle'));
        $fields->dataFieldByName('DefaultMailRecipient')                    ->setRightTitle($this->owner->fieldLabel('DefaultMailRecipientRightTitle'));
        $fields->dataFieldByName('DefaultMailOrderNotificationRecipient')   ->setRightTitle($this->owner->fieldLabel('DefaultMailOrderNotificationRecipientRightTitle'));
        $fields->dataFieldByName('DefaultContactMessageRecipient')          ->setRightTitle($this->owner->fieldLabel('DefaultContactMessageRecipientRightTitle'));
        $fields->dataFieldByName('userAgentBlacklist')                      ->setRightTitle($this->owner->fieldLabel('userAgentBlacklistRightTitle'));

        // Add i18n to DefaultPriceType source
        $i18nForDefaultPriceTypeField = array();
        foreach ($this->owner->dbObject('DefaultPriceType')->enumValues() as $value => $label) {
            $i18nForDefaultPriceTypeField[$value] = _t('SilvercartCustomer.' . strtoupper($label), $label);
        }
        $fields->dataFieldByName('DefaultPriceType')->setSource($i18nForDefaultPriceTypeField);

        // Add i18n to productDescriptionFieldForCart source
        $i18nForProductDescriptionField = array();
        foreach ($this->owner->dbObject('productDescriptionFieldForCart')->enumValues() as $productDescriptionField) {
            $i18nForProductDescriptionField[$productDescriptionField] = singleton('SilvercartProduct')->fieldLabel($productDescriptionField);
        }
        $fields->dataFieldByName('productDescriptionFieldForCart')->setSource($i18nForProductDescriptionField);

        $fields->dataFieldByName('StandardProductConditionID')->setEmptyString($this->owner->fieldLabel('StandardProductConditionEmptyString'));

        $this->getCMSFieldsForColorScheme($fields);
        
        return $fields;
    }
    
    /**
     * Adds the CMS fields for the ColorScheme setting.
     * 
     * @param FieldList $fields Fields to update
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.09.2014
     */
    public function getCMSFieldsForColorScheme(FieldList $fields) {
        $colorSchemePath = Director::baseFolder() . '/silvercart/css';
        if (is_dir($colorSchemePath)) {

            if ($handle = opendir($colorSchemePath)) {
                $colorSchemes = new ArrayList();
                while (false !== ($entry = readdir($handle))) {
                    if (substr($entry, -4) != '.css') {
                        continue;
                    }
                    if (substr($entry, 0, 6) != 'color_') {
                        continue;
                    }

                    $colorSchemeName = substr($entry, 6, -4);
                    $colorSchemeFile = $colorSchemePath . '/' . $entry;

                    $lines            = file($colorSchemeFile);
                    $backgroundColors = array();
                    $fontColors       = array();
                    foreach ($lines as $line) {
                        if (strpos(strtolower($line), 'background-color') !== false &&
                            preg_match('/#[a-z|A-Z|0-9]{3,6}/', $line, $matches)) {
                            $backgroundColors[$matches[0]] = new ArrayData(array('Color' => $matches[0]));
                        } elseif (strpos(strtolower(trim($line)), 'color') === 0 &&
                            preg_match('/#[a-z|A-Z|0-9]{3,6}/', $line, $matches)) {
                            $fontColors[$matches[0]] = new ArrayData(array('Color' => $matches[0]));
                        }
                    }

                    $colorSchemes->push(new ArrayData(
                            array(
                                'Name'             => $colorSchemeName,
                                'Title'            => _t('SilvercartConfig.ColorScheme_' . $colorSchemeName, ucfirst($colorSchemeName)),
                                'BackgroundColors' => new ArrayList($backgroundColors),
                                'FontColors'       => new ArrayList($fontColors),
                                'IsActive'         => $this->owner->ColorScheme == $colorSchemeName,
                            )
                    ));
                }
                closedir($handle);
            }
            
            $colorSchemes->sort('Title');

            $fields->removeByName('ColorScheme');
            
            $logoField = new UploadField('SilvercartLogo',   $this->owner->fieldLabel('SilvercartLogo'));
            $logoField->setDescription($this->owner->fieldLabel('SilvercartLogoDesc'));
            // Build color scheme toggle group
            $colorSchemeConfigurationField = ToggleCompositeField::create(
                    'ColorSchemeConfiguration',
                    $this->owner->fieldLabel('ColorSchemeConfiguration'),
                    array(
                        $logoField,
                        new LiteralField('ColorScheme', $this->owner->customise(array('ColorSchemes' => $colorSchemes))->renderWith('ColorSchemeField'))
                    )
            )->setHeadingLevel(4)->setStartClosed(true);
            $fields->addFieldToTab('Root.Silvercart', $colorSchemeConfigurationField);
        } else {
            $fields->removeByName('ColorScheme');
        }
    }
    
    /**
     * Sets the ColorScheme.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.02.2016
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        $request     = Controller::curr()->getRequest();
        $colorScheme = $request->postVar('ColorScheme');
        if (is_string($colorScheme)) {
            $this->owner->ColorScheme = $colorScheme;
        }
    }

    /**
     * Duplicates the SilverCart based config into the translations.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.12.2015
     */
    public function onAfterWrite() {
        parent::onAfterWrite();
        if (is_null(self::$duplicate_config_locale)) {
            self::$duplicate_config_locale = $this->owner->Locale;
            $changedFields = $this->owner->getChangedFields();
            $translations  = $this->owner->getTranslations();
            $dbAttributes  = array_keys(Config::inst()->get('SilvercartSiteConfig', 'db'));
            foreach ($translations as $translation) {
                foreach ($changedFields as $changedFieldName => $changedFieldData) {
                    if (!in_array($changedFieldName, $dbAttributes)) {
                        continue;
                    }
                    $translation->{$changedFieldName} = $changedFieldData['after'];
                }
                $translation->write();
            }
        }
    }
    
    /**
     * Restores the config parameters out of the old SilvercartConfig object.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.12.2015
     */
    public function requireDefaultRecords() {
        $result = DB::query('SHOW TABLES LIKE \'SilvercartConfig\'');
        if ($result->numRecords() > 0) {
            $config           = SilvercartConfig::getConfig();
            $skipFields       = array('ID', 'ClassName', 'Created', 'LastEdited');
            $silvercartConfig = DB::query('SELECT * FROM SilvercartConfig;');
            foreach ($silvercartConfig as $row) {
                foreach ($row as $fieldName => $fieldValue) {
                    if (in_array($fieldName, $skipFields)) {
                        continue;
                    } elseif ($fieldName == 'Locale') {
                        $config->DefaultLocale = $fieldValue;
                    } else {
                        $config->{$fieldName} = $fieldValue;
                    }
                }
                $config->write();
            }
            DB::query('DROP TABLE SilvercartConfig');
        }
        
    }
    
    /**
     * Returns whether to enable SSL.
     * 
     * @return bool
     */
    public function getEnableSSL() {
        $enableSSL = $this->owner->getField('EnableSSL');
        if (!$this->getCMSFieldsIsCalled) {
            $this->owner->extend('updateEnableSSL', $enableSSL);
        }
        return $enableSSL;
    }
    
    // Put SilvercartConfiguration::Check() Methods here
    
    /**
     * Checks, whether an activated country exists or not.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
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
    
}
