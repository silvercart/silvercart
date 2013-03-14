<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
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
 * @copyright 2010 pixeltricks GmbH
 * @since 23.02.2011
 * @license LGPL
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
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public static $productsPerPageOptions = array(
        '4'         => '4',
        '8'         => '8',
        '20'        => '20',
        '50'        => '50',
        '100'       => '100'
        //'0'         => 'All' // Activate this only for shops with small product counts
    );
    
    /**
     * The default setting for the CustomerConfig option 'productsPerPage'.
     *
     * @var int
     */
    public static $productsPerPageDefault = 20;
    
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
        'minimumOrderValue'                     => 'Money',
        'useMinimumOrderValue'                  => 'Boolean(0)',
        'disregardMinimumOrderValue'            => 'Boolean(0)',
        'freeOfShippingCostsFrom'               => 'Money',
        'useFreeOfShippingCostsFrom'            => 'Boolean(0)',
        'enableBusinessCustomers'               => 'Boolean(0)',
        'enablePackstation'                     => 'Boolean(0)',
        'enableStockManagement'                 => 'Boolean(0)',
        'isStockManagementOverbookable'         => 'Boolean(0)',
        'redirectToCartAfterAddToCart'          => 'Boolean(0)',
        'SkipPaymentStepIfUnique'               => 'Boolean(0)',
        'SkipShippingStepIfUnique'              => 'Boolean(0)',
        'redirectToCartAfterAddToCart'          => 'Boolean(0)',
        'DisplayWeightsInKilogram'              => 'Boolean(1)',
        'demandBirthdayDateOnRegistration'      => 'Boolean(0)',
        'addToCartMaxQuantity'                  => 'Int(999)',
        'Locale'                                => 'DBLocale',
        'useDefaultLanguageAsFallback'          => 'Boolean(1)',
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
        'StandardProductCondition'  => 'SilvercartProductCondition'
    );
    
    /**
     * Defaults for empty fields.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion'             => '1.3',
        'SilvercartMinorVersion'        => '6',
        'SilvercartUpdateVersion'       => '9',
        'DefaultPriceType'              => 'gross',
        'GeoNamesActive'                => false,
        'GeoNamesAPI'                   => 'http://api.geonames.org/',
        'productsPerPage'               => 20,
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
    public static $disregardMinimumOrderValue               = null;
    public static $useMinimumOrderValue                     = null;
    public static $productsPerPage                          = null;
    public static $silvercartVersion                        = null;
    public static $silvercartMinorVersion                   = null;
    public static $silvercartFullVersion                    = null;
    public static $enableStockManagement                    = null;
    public static $isStockManagementOverbookable            = null;
    public static $redirectToCartAfterAddToCart             = null;
    public static $demandBirthdayDateOnRegistration         = null;
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
    public static $displayWeightsInKilogram                 = null;

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
     * Add notes to the CMS fields.
     *
     * @param array $params custom params
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public function getCMSFields($params = array()) {
        $defaultCMSFields = parent::getCMSFields(
                array_merge(
                        $params,
                        array(
                            'fieldClasses' => array(
                                'minimumOrderValue'         => 'SilvercartMoneyField',
                                'freeOfShippingCostsFrom'   => 'SilvercartMoneyField',
                            ),
                        )
                )
        );
        // Remove not required fields
        $defaultCMSFields->removeByName('SilvercartVersion');
        $defaultCMSFields->removeByName('SilvercartMinorVersion');
        $defaultCMSFields->removeByName('SilvercartUpdateVersion');
        $defaultCMSFields->removeByName('DefaultCurrency');
        $defaultCMSFields->removeByName('useMinimumOrderValue');
        $defaultCMSFields->removeByName('productsPerPage');
        $defaultCMSFields->removeByName('productGroupsPerPage');
        $defaultCMSFields->removeByName('SilvercartNoImage');
        $defaultCMSFields->removeByName('enableSSL');
        $defaultCMSFields->removeByName('enableStockManagement');
        $defaultCMSFields->removeByName('isStockManagementOverbookable');
        $defaultCMSFields->removeByName('StandardProductCondition');
        $defaultCMSFields->removeByName('redirectToCartAfterAddToCart');
        $defaultCMSFields->removeByName('disregardMinimumOrderValue');
        $defaultCMSFields->removeByName('productDescriptionFieldForCart');
        $defaultCMSFields->removeByName('useProductDescriptionFieldForCart');
        $defaultCMSFields->removeByName('useStrictSearchRelevance');

        //Make the field DefaultLanguage a Dropdown
        $defaultCMSFields->removeByName('Locale');
        $defaultLanguageDropdown = SilvercartLanguageHelper::prepareLanguageDropdownField($this, 'SiteTree');
        $defaultLanguageDropdown->setTitle(_t('SilvercartConfig.DEFAULT_LANGUAGE'));
        $defaultCMSFields->push($defaultLanguageDropdown);
        
        // Building the general tab structure
        $CMSFields = new FieldSet(
            $rootTab = new TabSet(
                'Root',
                $generalTab = new TabSet(
                    'General',
                    _t('SilvercartConfig.GENERAL'),
                    $tabGeneralMain     = new Tab('Main',       _t('SilvercartConfig.GENERAL_MAIN')),
                    $tabGeneralTestData = new Tab('TestData',   _t('SilvercartConfig.GENERAL_TEST_DATA')),
                    $tabPricesMain      = new Tab('Prices',     _t('SilvercartPrice.PLURALNAME')),
                    $tabLayoutMain      = new Tab('Layout',     _t('SilvercartConfig.LAYOUT')),
                    $tabServerMain      = new Tab('Server',     _t('SilvercartConfig.SERVER')),
                    $tabStockMain       = new Tab('Stock',      _t('SilvercartConfig.STOCK')),
                    $tabCheckoutMain    = new Tab('Checkout',   _t('SilvercartPage.CHECKOUT')),
                    $tabCleanMain       = new Tab('Clean',      _t('SilvercartConfig.CLEAN'))
                ),
                $interfacesTab = new TabSet(
                    'Interfaces',
                    _t('SilvercartConfig.INTERFACES'),
                    $tabInterfacesGeoNames  = new Tab('GeoNames',   _t('SilvercartConfig.INTERFACES_GEONAMES'))
                )
            )
        );

        // General Form Fields right here

        $CMSFields->addFieldsToTab('Root.General.Main', $defaultCMSFields->dataFields());
        $CMSFields->addFieldToTab('Root.General.Main', new LabelField('ForEmailSender', _t('SilvercartConfig.EMAILSENDER_INFO')), 'GlobalEmailRecipient');
        $productConditionMap = SilvercartProductCondition::getDropdownFieldOptionSet();
        $CMSFields->addFieldToTab('Root.General.Main', new DropdownField(
            'StandardProductConditionID',
            _t('SilvercartProductCondition.USE_AS_STANDARD_CONDITION'),
            $productConditionMap,
            $this->StandardProductConditionID,
            null,
            _t('SilvercartProductCondition.PLEASECHOOSE')
        ));
        $productDescriptionFieldMap = array();

        foreach (singleton('SilvercartConfig')->dbObject('productDescriptionFieldForCart')->enumValues() as $productDescriptionField) {
            $productDescriptionFieldMap[$productDescriptionField] = singleton('SilvercartProduct')->fieldLabel($productDescriptionField);
        }

        $CMSFields->addFieldToTab('Root.General.Main', new CheckboxField(
            'useProductDescriptionFieldForCart',
            _t('SilvercartConfig.USE_PRODUCT_DESCRIPTION_FIELD_FOR_CART'),
            $this->useProductDescriptionFieldForCart
        ));
        $CMSFields->addFieldToTab('Root.General.Main', new DropdownField(
            'productDescriptionFieldForCart',
            _t('SilvercartConfig.PRODUCT_DESCRIPTION_FIELD_FOR_CART'),
            $productDescriptionFieldMap
        ));
        
        $CMSFields->addFieldToTab('Root.General.Main', new CheckboxField(
                'useStrictSearchRelevance',
                _t('SilvercartConfig.USE_STRICT_SEARCH_RELEVANCE'), 
                $this->useStrictSearchRelevance
        ));
        
     
        /*
         * Root.General.Prices tab
         */
        $CMSFields->addFieldToTab('Root.General.Prices', new TextField('DefaultCurrency', _t('SilvercartConfig.DEFAULTCURRENCY')));
        
        // configure the fields for pricetype configuration
        $CMSFields->addFieldToTab(
            'Root.General.Prices',
            new LiteralField(
                'PricetypesTitle',
                sprintf(
                    '<h3>%s</h3>',
                    _t('SilvercartConfig.PRICETYPES_HEADLINE')
                )
            )
        );
        
        $originalSource = $CMSFields->dataFieldByName('DefaultPriceType')->getSource();
        $i18nSource     = array();
        foreach ($originalSource as $value => $label) {
            $i18nSource[$value] = _t('SilvercartCustomer.' . strtoupper($label), $label);
        }
        $CMSFields->addFieldToTab('Root.General.Prices', new DropdownField('DefaultPriceType', $this->fieldLabel('DefaultPriceType'), $i18nSource));
        
        /*
         * Root.General.Layout tab
         */
        $CMSFields->addFieldToTab('Root.General.Layout', new TextField('productsPerPage', _t('SilvercartConfig.PRODUCTSPERPAGE')));
        $CMSFields->addFieldToTab('Root.General.Layout', new TextField('productGroupsPerPage', _t('SilvercartConfig.PRODUCTGROUPSPERPAGE')));
        $CMSFields->addFieldToTab('Root.General.Layout', new FileIFrameField('SilvercartNoImage', _t('SilvercartConfig.DEFAULT_IMAGE')));
        $CMSFields->addFieldToTab('Root.General.Layout', new TextField('displayedPaginationPages', _t('SilvercartConfig.DISPLAYEDPAGINATION')));
        $source = array(
            'Tabbed' => _t('SilvercartConfig.TABBED'),
            'Flat' => _t('SilvercartConfig.FLAT')
        );
        
        /*
         * Root.General.Server tab
         */
        $CMSFields->addFieldToTab('Root.General.Server',
            $defaultCMSFields->dataFieldByName('userAgentBlacklist')
        );
        
        /*
         * Root.General.Stock tab
         */
        $CMSFields->addFieldToTab('Root.General.Stock', new CheckboxField('enableStockManagement', _t('SilvercartConfig.ENABLESTOCKMANAGEMENT')));
        $CMSFields->addFieldToTab('Root.General.Stock', new CheckboxField('isStockManagementOverbookable', _t('SilvercartConfig.QUANTITY_OVERBOOKABLE')));
        
        /*
         * Root.General.Checkout tab
         */
        $basicCheckoutTab = new Tab('basicCheckoutTab', _t('SilvercartConfig.BASICCHECKOUT'));
        $minimumOrderValueTab = new Tab('minimumOrderValueTab', _t('SilvercartConfig.MINIMUMORDERVALUE'));
        $freeOfShippingCostsTab = new Tab('freeOfShippingCostsTab', _t('SilvercartConfig.FREEOFSHIPPINGCOSTSTAB'));
        $tabCheckoutMain->push(
            $tabCheckoutMainTabSet = new TabSet('tabsetCheckout')
        );
        $tabCheckoutMainTabSet->push($basicCheckoutTab);
        $tabCheckoutMainTabSet->push($minimumOrderValueTab);
        $tabCheckoutMainTabSet->push($freeOfShippingCostsTab);

        $basicCheckoutTab->push(new CheckboxField('enableSSL', _t('SilvercartConfig.ENABLESSL')));
        $basicCheckoutTab->push(new CheckboxField('redirectToCartAfterAddToCart', _t('SilvercartConfig.REDIRECTTOCARTAFTERADDTOCART')));
        $basicCheckoutTab->push(new CheckboxField('SkipShippingStepIfUnique', $this->fieldLabel('SkipShippingStepIfUnique')));
        $basicCheckoutTab->push(new CheckboxField('SkipPaymentStepIfUnique', $this->fieldLabel('SkipPaymentStepIfUnique')));
        $basicCheckoutTab->push(new CheckboxField('DisplayWeightsInKilogram', $this->fieldLabel('DisplayWeightsInKilogram')));

        $minimumOrderValueTab->push(new CheckboxField('useMinimumOrderValue', _t('SilvercartConfig.USEMINIMUMORDERVALUE')));
        $minimumOrderValueTab->push(new CheckboxField('disregardMinimumOrderValue', _t('SilvercartConfig.DISREGARD_MINIMUM_ORDER_VALUE')));
        $minimumOrderValueTab->push($defaultCMSFields->dataFieldByName('minimumOrderValue'));

        $freeOfShippingCostsTab->push(new CheckboxField('useFreeOfShippingCostsFrom', _t('SilvercartConfig.USEFREEOFSHIPPINGCOSTSFROM')));
        $freeOfShippingCostsTab->push($defaultCMSFields->dataFieldByName('freeOfShippingCostsFrom'));
        
        // FormFields for Test Data right here        
        $addExampleData = new FormAction('addExampleData', _t('SilvercartConfig.ADD_EXAMPLE_DATA', 'Add Example Data'));
        $addExampleData->setRightTitle(_t('SilvercartConfig.ADD_EXAMPLE_DATA_DESCRIPTION'));
        $CMSFields->addFieldToTab('Root.General.TestData', $addExampleData);
        
        $spacer = new LiteralField('Spacer', '<br/><hr/><br/>');
        $CMSFields->addFieldToTab('Root.General.TestData', $spacer);
        
        $addExampleConfig = new FormAction('addExampleConfig', _t('SilvercartConfig.ADD_EXAMPLE_CONFIGURATION', 'Add Example Configuration'));
        $addExampleConfig->setRightTitle(_t('SilvercartConfig.ADD_EXAMPLE_CONFIGURATION_DESCRIPTION'));
        $CMSFields->addFieldToTab('Root.General.TestData', $addExampleConfig);
        
        // FormFields for cleaning tab right here
        $cleanDataBaseStartIndex = new TextField('cleanDataBaseStartIndex', _t('SilvercartConfig.CLEAN_DATABASE_START_INDEX'), 0);
        $cleanDataBase = new FormAction('cleanDataBase', _t('SilvercartConfig.CLEAN_DATABASE', 'Clean database'));
        $cleanDataBase->setRightTitle(_t('SilvercartConfig.CLEAN_DATABASE_DESCRIPTION'));
        $CMSFields->addFieldToTab('Root.General.Clean', $cleanDataBaseStartIndex);
        $CMSFields->addFieldToTab('Root.General.Clean', $cleanDataBase);
        
        // FormFields for Interfaces right here
        // GeoNames
        $geoNamesDescriptionValue = '';
        $geoNamesDescriptionValue = _t('SilvercartConfig.GEONAMES_DESCRIPTION');
        $geoNamesDescription = new LiteralField('GeoNamesDescription', $geoNamesDescriptionValue);
        $CMSFields->addFieldToTab('Root.Interfaces.GeoNames', $geoNamesDescription);
        $CMSFields->addFieldToTab('Root.Interfaces.GeoNames', $CMSFields->dataFieldByName('GeoNamesActive'));
        $CMSFields->addFieldToTab('Root.Interfaces.GeoNames', $CMSFields->dataFieldByName('GeoNamesUserName'));
        $CMSFields->addFieldToTab('Root.Interfaces.GeoNames', $CMSFields->dataFieldByName('GeoNamesAPI'));
        $this->extend('updateCMSFields', $CMSFields);
        return $CMSFields;
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
                    'disregardMinimumOrderValue'            => _t('SilvercartConfig.DISREGARDMINIMUMORDERVALUE', 'Allow orders disregarding the minimum order value'),
                    'useMinimumOrderValue'                  => _t('SilvercartConfig.USEMINIMUMORDERVALUE', 'Use minimum order value'),
                    'freeOfShippingCostsFrom'               => _t('SilvercartConfig.FREEOFSHIPPINGCOSTSFROM'),
                    'productsPerPage'                       => _t('SilvercartConfig.PRODUCTSPERPAGE', 'Products per page'),
                    'productGroupsPerPage'                  => _t('SilvercartConfig.PRODUCTGROUPSPERPAGE', 'Product groups per page'),
                    'isStockManagementOverbookable'         => _t('SilvercartConfig.QUANTITY_OVERBOOKABLE', 'Is the stock quantity of a product generally overbookable?'),
                    'demandBirthdayDateOnRegistration'      => _t('SilvercartConfig.DEMAND_BIRTHDAY_DATE_ON_REGISTRATION', 'Demand birthday date on registration?'),
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
                    'SkipPaymentStepIfUnique'               => _t('SilvercartConfig.SKIP_PAYMENT_STEP_IF_UNIQUE'),
                    'SkipShippingStepIfUnique'              => _t('SilvercartConfig.SKIP_SHIPPING_STEP_IF_UNIQUE'),
                    'DisplayWeightsInKilogram'              => _t('SilvercartConfig.DISPLAY_WEIGHTS_IN_KILOGRAM'),
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
        if (DataObject::get_one('SilvercartConfig')) {
            if (DataObject::get_one('SilvercartConfig')->ID !== $this->ID) {
                // is there is an existent SilvercartConfig, do not write another.
                $this->record = array();
            }
        }
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
        }
        return self::$defaultCurrency;
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
     * @copyright 2011 pixeltricks GmbH
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
     * @copyright 2011 pixeltricks GmbH
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
     * @copyright 2011 pixeltricks GmbH
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
     * @copyright 2011 pixeltricks GmbH
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
     * @copyright 2012 pixeltricks GmbH
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2012 pixeltricks GmbH
     * @since 13.03.2012
     */
    public static function FreeOfShippingCostsFrom($shippingCountry = null) {
        if (is_null(self::$freeOfShippingCostsFrom)) {
            self::$freeOfShippingCostsFrom = self::getConfig()->freeOfShippingCostsFrom;
        }
        if (!($shippingCountry instanceof SilvercartCountry) &&
            Controller::curr()->hasMethod('getCombinedStepData')) {
            $checkoutData       = Controller::curr()->getCombinedStepData();
            if (array_key_exists('Shipping_Country', $checkoutData)) {
                $shippingCountryID  = $checkoutData['Shipping_Country'];
                $shippingCountry    = DataObject::get_by_id(
                        'SilvercartCountry',
                        $shippingCountryID
                );
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
     * Returns the minimum order value if specified
     *
     * @return mixed float|bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 09.06.2011
     */
    public static function DisregardMinimumOrderValue() {
        if (is_null(self::$disregardMinimumOrderValue)) {
            self::$disregardMinimumOrderValue = self::getConfig()->disregardMinimumOrderValue;
        }
        return self::$disregardMinimumOrderValue;
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
     * @copyright 2011 pixeltricks GmbH
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
     * @return bool
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
     * @return bool
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
     * @return bool
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
     * determins weather a customer gets prices shown gross or net dependent on
     * customer's invoice address or class
     *
     * @return string returns "gross" or "net"
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public static function Pricetype() {
        $member             = Member::currentUser();
        $configObject       = self::getConfig();

        $silvercartPluginResult = SilvercartPlugin::call(
            $configObject,
            'overwritePricetype',
            array()
        );

        if (!empty($silvercartPluginResult)) {
            return $silvercartPluginResult;
        }

        if ($member) {
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
        return self::$priceType;
    }

    /**
     * Returns the SilvercartConfig or triggers an error if not existent.
     *
     * @return SilvercartConfig
     */
    public static function getConfig() {
        if (is_null(self::$config)) {
            self::$config = DataObject::get_one('SilvercartConfig');
            if (!self::$config) {
                if (array_key_exists('QUERY_STRING', $_SERVER) && (strpos($_SERVER['QUERY_STRING'], 'dev/tests') !== false || strpos($_SERVER['QUERY_STRING'], 'dev/build') !== false)) {
                    return false;
                }
                $errorMessage = _t('SilvercartConfig.ERROR_NO_CONFIG', 'SilvercartConfig is missing! Please <a href="/admin/silvercart-config/">log in</a> and choose "SilverCart Configuration -> general configuration" to add the general configuration. ');
                self::triggerError($errorMessage);
            }
        }
        return self::$config;
    }

    /**
     * Returns all registered menus for the storeadmin.
     * 
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.01.2012
     */
    public static function getRegisteredMenus() {
        return self::$registeredMenus;
    }

    /**
     * Returns the Non-CMS menu identifiers.
     * 
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.02.2012
     */
    public static function getMenuNonCmsIdentifiers() {
        return self::$menuNonCmsIdentifiers;
    }

    /**
     * Returns the default no-image visualisation.
     * 
     * @return mixed Image|bool false
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.06.2011
     */
    public static function getNoImage() {
        $configObject = self::getConfig();
        
        return $configObject->SilvercartNoImage();
    }
    
    /**
     * Returns the standard product condition.
     * 
     * @return mixed SilvercartProductCondition|bool false
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.08.2011
     */
    public static function getStandardProductCondition() {
        $configObject = self::getConfig();
        
        return $configObject->StandardProductCondition();
    }
    
    /**
     * Returns the standard product condition.
     * 
     * @return mixed SilvercartProductCondition|bool false
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public static function getRedirectToCartAfterAddToCartAction() {
        if (is_null(self::$redirectToCartAfterAddToCart)) {
            self::$redirectToCartAfterAddToCart = self::getConfig()->redirectToCartAfterAddToCart;
        }
        return self::$redirectToCartAfterAddToCart;
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
     * @since 21.03.2011
     */
    public static function triggerError($errorMessage) {
        $elements = array(
            'ErrorMessage' => $errorMessage,
        );
        $output = Controller::curr()->customise($elements)->renderWith(
                        array(
                            'SilvercartErrorPage',
                            'Page'
                        )
        );
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
        $query = "
                SELECT
                    *
                FROM
                    `SilvercartCountry`
                WHERE
                    `Active`=1
                ";
        $items = singleton('SilvercartCountry')->buildDataObjectSet(DB::query($query));
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
     * @copyright 2010 pixeltricks GmbH
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
            self::$useDefaultLanguageAsFallback = self::getConfig()->useDefaultLanguageAsFallback;
        }
        return self::$useDefaultLanguageAsFallback;
    }
}
