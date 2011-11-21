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
     * Singular name for the backend..
     *
     * @var string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2011
     */
    public static $singular_name = "General configuration";
    
    /**
     * Plural name for the backend..
     *
     * @var string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2011
     */
    public static $plural_name = "General configurations";
    
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
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public static $productsPerPageDefault = 20;
    
    /**
     * Used as SQL limit number for unlimited products per page.
     *
     * @var int
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public static $productsPerPageUnlimitedNumber = 999999;
    
    /**
     * Attributes.
     *
     * @var array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2011
     */
    public static $db = array(
        'SilvercartVersion'                 => 'VarChar(16)',
        'SilvercartUpdateVersion'           => 'VarChar(16)',
        'DefaultCurrency'                   => 'VarChar(16)',
        'EmailSender'                       => 'VarChar(255)',
        'GlobalEmailRecipient'              => 'VarChar(255)',
        'PricetypeAnonymousCustomers'       => 'VarChar(6)',
        'PricetypeRegularCustomers'         => 'VarChar(6)',
        'PricetypeBusinessCustomers'        => 'VarChar(6)',
        'PricetypeAdmins'                   => 'VarChar(6)',
        'enableSSL'                         => 'Boolean(0)',
        'productsPerPage'                   => 'Int',
        'productGroupsPerPage'              => 'Int',
        'displayTypeOfProductAdmin'         => 'Enum("Flat,Tabbed","Tabbed")',
        'minimumOrderValue'                 => 'Money',
        'useMinimumOrderValue'              => 'Boolean(0)',
        'disregardMinimumOrderValue'        => 'Boolean(0)',
        'useApacheSolrSearch'               => 'Boolean(0)',
        'apacheSolrUrl'                     => 'VarChar(255)',
        'apacheSolrPort'                    => 'Int',
        'enableStockManagement'             => 'Boolean(0)',
        'isStockManagementOverbookable'     => 'Boolean(0)',
        'redirectToCartAfterAddToCart'      => 'Boolean(0)',
        'demandBirthdayDateOnRegistration'  => 'Boolean(0)',
        'addToCartMaxQuantity'              => 'Int(999)',
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
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.06.2011
     */
    public static $has_one = array(
        'SilvercartNoImage'         => 'Image',
        'StandardProductCondition'  => 'SilvercartProductCondition'
    );
    
    /**
     * Defaults for empty fields.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 11.07.2011
     */
    public static $defaults = array(
        'SilvercartVersion'             => '1.2',
        'SilvercartUpdateVersion'       => '1',
        'PricetypeAnonymousCustomers'   => 'gross',
        'PricetypeRegularCustomers'     => 'gross',
        'PricetypeBusinessCustomers'    => 'net',
        'PricetypeAdmins'               => 'net',
        'GeoNamesActive'                => false,
        'GeoNamesAPI'                   => 'http://api.geonames.org/',
        'productsPerPage'               => 20,
        'productGroupsPerPage'          => 6,
        'apacheSolrUrl'                 => '/solr',
        'apacheSolrPort'                => '8983',
        'addToCartMaxQuantity'          => 999
    );
    /**
     * Define all required configuration fields in this array. The given fields
     * will be handled in self::Check().
     *
     * @var array
     */
    public static $required_configuration_fields = array(
        'EmailSender',
        'PricetypeAnonymousCustomers',
        'PricetypeRegularCustomers',
        'PricetypeBusinessCustomers',
        'PricetypeAdmins',
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
    public static $addToCartMaxQuantity             = null;
    public static $apacheSolrPort                   = null;
    public static $apacheSolrUrl                    = null;
    public static $defaultCurrency                  = null;
    public static $emailSender                      = null;
    public static $globalEmailRecipient             = null;
    public static $priceType                        = null;
    public static $config                           = null;
    public static $enableSSL                        = null;
    public static $minimumOrderValue                = null;
    public static $disregardMinimumOrderValue       = null;
    public static $useMinimumOrderValue             = null;
    public static $productsPerPage                  = null;
    public static $silvercartVersion                = null;
    public static $useApacheSolrSearch              = null;
    public static $enableStockManagement            = null;
    public static $isStockManagementOverbookable    = null;
    public static $redirectToCartAfterAddToCart     = null;
    public static $demandBirthdayDateOnRegistration = null;

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function singular_name() {
        if (_t('SilvercartConfig.SINGULARNAME')) {
            return _t('SilvercartConfig.SINGULARNAME');
        } else {
            return parent::singular_name();
        } 
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011 
     */
    public function plural_name() {
        if (_t('SilvercartConfig.PLURALNAME')) {
            return _t('SilvercartConfig.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
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
    public function getCMSFields($params = null) {
        $defaultCMSFields = parent::getCMSFields($params);
        // Remove not required fields
        $defaultCMSFields->removeByName('SilvercartVersion');
        $defaultCMSFields->removeByName('SilvercartUpdateVersion');
        $defaultCMSFields->removeByName('DefaultCurrency');
        $defaultCMSFields->removeByName('minimumOrderValue');
        $defaultCMSFields->removeByName('useMinimumOrderValue');
        $defaultCMSFields->removeByName('productsPerPage');
        $defaultCMSFields->removeByName('productGroupsPerPage');
        $defaultCMSFields->removeByName('SilvercartNoImage');
        $defaultCMSFields->removeByName('useApacheSolrSearch');
        $defaultCMSFields->removeByName('apacheSolrUrl');
        $defaultCMSFields->removeByName('apacheSolrPort');
        $defaultCMSFields->removeByName('enableSSL');
        $defaultCMSFields->removeByName('enableStockManagement');
        $defaultCMSFields->removeByName('isStockManagementOverbookable');
        $defaultCMSFields->removeByName('StandardProductCondition');
        $defaultCMSFields->removeByName('redirectToCartAfterAddToCart');
        $defaultCMSFields->removeByName('disregardMinimumOrderValue');

        // Building the general tab structure
        $CMSFields = new FieldSet(
            $rootTab = new TabSet(
                'Root',
                $generalTab = new TabSet(
                    'General',
                    $tabGeneralMain = new Tab('Main'),
                    $tabGeneralTestData = new Tab('TestData'),
                    $tabPricesMain = new Tab('Prices'),
                    $tabLayoutMain = new Tab('Layout'),
                    $tabServerMain = new Tab('Server'),
                    $tabStockMain = new Tab('Stock'),
                    $tabCkechoutMain = new Tab('Checkout'),
                    $tabCleanMain = new Tab('Clean')
                ),
                $interfacesTab = new TabSet(
                    'Interfaces',
                    $tabInterfacesGeoNames  = new Tab('GeoNames')
                )
            )
        );

        // General Form Fields right here
        $generalTab->setTitle(_t('SilvercartConfig.GENERAL'));
        
        // General Main
        $tabGeneralMain->setTitle(_t('SilvercartConfig.GENERAL_MAIN'));
        $tabGeneralTestData->setTitle(_t('SilvercartConfig.GENERAL_TEST_DATA'));
        $tabPricesMain->setTitle(_t('SilvercartPrice.PLURALNAME'));
        $tabLayoutMain->setTitle(_t('SilvercartConfig.LAYOUT'));
        $tabServerMain->setTitle(_t('SilvercartConfig.SERVER'));
        $tabStockMain->setTitle(_t('SilvercartConfig.STOCK'));
        $tabCkechoutMain->setTitle(_t('SilvercartPage.CHECKOUT'));
        $tabCleanMain->setTitle(_t('SilvercartConfig.CLEAN'));

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
        $pricetypes = array(
            'PricetypeAnonymousCustomers' => _t('SilvercartConfig.PRICETYPE_ANONYMOUS', 'Pricetype anonymous customers'),
            'PricetypeRegularCustomers' => _t('SilvercartConfig.PRICETYPE_REGULAR', 'Pricetype regular customers'),
            'PricetypeBusinessCustomers' => _t('SilvercartConfig.PRICETYPE_BUSINESS', 'Pricetype business customers'),
            'PricetypeAdmins' => _t('SilvercartConfig.PRICETYPE_ADMINS', 'Pricetype administrators')
        );
        $pricetypeDropdownValues = array(
            'gross' => _t('SilvercartCustomer.GROSS'),
            'net' => _t('SilvercartCustomer.NET')
        );
        foreach ($pricetypes as $name => $title) {
            $CMSFields->removeByName($name);
            $CMSFields->addFieldToTab('Root.General.Prices', new DropdownField($name, $title, $pricetypeDropdownValues));
        }
        
        /*
         * Root.General.Layout tab
         */
        $CMSFields->addFieldToTab('Root.General.Layout', new TextField('productsPerPage', _t('SilvercartConfig.PRODUCTSPERPAGE')));
        $CMSFields->addFieldToTab('Root.General.Layout', new TextField('productGroupsPerPage', _t('SilvercartConfig.PRODUCTGROUPSPERPAGE')));
        $CMSFields->addFieldToTab('Root.General.Layout', new FileIFrameField('SilvercartNoImage', _t('SilvercartConfig.DEFAULT_IMAGE')));
        $source = array(
            'Tabbed' => _t('SilvercartConfig.TABBED'),
            'Flat' => _t('SilvercartConfig.FLAT')
        );
        $displayTypeOfProductAdminDropdown = new DropdownField('displayTypeOfProductAdmin', _t('SilvercartConfig.DISPLAY_TYPE_OF_PRODUCT_ADMIN', 'Display type of product administration'), $source, $this->displayTypeOfProductAdmin);
        $CMSFields->addFieldToTab('Root.General.Layout', $displayTypeOfProductAdminDropdown);
        
        /*
         * Root.General.Server tab
         */
        $CMSFields->addFieldToTab('Root.General.Server', new LiteralField('ApacheSolrTitle', '<h3>Apache Solr Search</h3>'));
        $CMSFields->addFieldToTab('Root.General.Server', new CheckboxField('useApacheSolrSearch', _t('SilvercartConfig.USE_APACHE_SOLR_SEARCH')));
        $CMSFields->addFieldToTab('Root.General.Server', new TextField('apacheSolrUrl', _t('SilvercartConfig.APACHE_SOLR_URL')));
        $CMSFields->addFieldToTab('Root.General.Server', new TextField('apacheSolrPort', _t('SilvercartConfig.APACHE_SOLR_PORT')));
        
        /*
         * Root.General.Stock tab
         */
        $CMSFields->addFieldToTab('Root.General.Stock', new CheckboxField('enableStockManagement', _t('SilvercartConfig.ENABLESTOCKMANAGEMENT')));
        $CMSFields->addFieldToTab('Root.General.Stock', new CheckboxField('isStockManagementOverbookable', _t('SilvercartConfig.QUANTITY_OVERBOOKABLE')));
        
        /*
         * Root.General.Checkout tab
         */
        $CMSFields->addFieldToTab('Root.General.Checkout', new CheckboxField('enableSSL', _t('SilvercartConfig.ENABLESSL')));
        $CMSFields->addFieldToTab('Root.General.Checkout', new CheckboxField('redirectToCartAfterAddToCart', _t('SilvercartConfig.REDIRECTTOCARTAFTERADDTOCART')));
        $CMSFields->addFieldToTab(
            'Root.General.Checkout',
            new LiteralField(
                'MinimumOrderValueTitle',
                sprintf(
                    '<h3>%s</h3>',
                    _t('SilvercartConfig.MINIMUMORDERVALUE_HEADLINE')
                )
            )
        );
        $CMSFields->addFieldToTab('Root.General.Checkout', new CheckboxField('useMinimumOrderValue', _t('SilvercartConfig.USEMINIMUMORDERVALUE')));
        $CMSFields->addFieldToTab('Root.General.Checkout', new CheckboxField('disregardMinimumOrderValue', _t('SilvercartConfig.DISREGARD_MINIMUM_ORDER_VALUE')));
        $CMSFields->addFieldToTab('Root.General.Checkout', new MoneyField('minimumOrderValue', _t('SilvercartConfig.MINIMUMORDERVALUE')));
        
        
        
        // FormFields for Test Data right here
        $tabGeneralTestData->setTitle(_t('SilvercartConfig.GENERAL_TEST_DATA'));
        
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
        $interfacesTab->setTitle(_t('SilvercartConfig.INTERFACES'));
        // GeoNames
        $tabInterfacesGeoNames->setTitle(_t('SilvercartConfig.INTERFACES_GEONAMES'));

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
        $fieldLabels = parent::fieldLabels($includerelations);
        $fieldLabels['addToCartMaxQuantity']                = _t('SilvercartConfig.ADDTOCARTMAXQUANTITY', 'Maximum allowed quantity of a single product in the shopping cart');
        $fieldLabels['DefaultCurrency']                     = _t('SilvercartConfig.DEFAULTCURRENCY', 'Default currency');
        $fieldLabels['EmailSender']                         = _t('SilvercartConfig.EMAILSENDER', 'Email sender');
        $fieldLabels['GlobalEmailRecipient']                = _t('SilvercartConfig.GLOBALEMAILRECIPIENT', 'Global email recipient');
        $fieldLabels['enableSSL']                           = _t('SilvercartConfig.ENABLESSL', 'Enable SSL');
        $fieldLabels['enableStockManagement']               = _t('SilvercartConfig.ENABLESTOCKMANAGEMENT', 'enable stock management');
        $fieldLabels['minimumOrderValue']                   = _t('SilvercartConfig.MINIMUMORDERVALUE', 'Minimum order value');
        $fieldLabels['disregardMinimumOrderValue']          = _t('SilvercartConfig.DISREGARDMINIMUMORDERVALUE', 'Allow orders disregarding the minimum order value');
        $fieldLabels['useMinimumOrderValue']                = _t('SilvercartConfig.USEMINIMUMORDERVALUE', 'Use minimum order value');
        $fieldLabels['productsPerPage']                     = _t('SilvercartConfig.PRODUCTSPERPAGE', 'Products per page');
        $fieldLabels['productGroupsPerPage']                = _t('SilvercartConfig.PRODUCTGROUPSPERPAGE', 'Product groups per page');
        $fieldLabels['useApacheSolrSearch']                 = _t('SilvercartConfig.USE_APACHE_SOLR_SEARCH', 'Use Apache Solr search');
        $fieldLabels['apacheSolrPort']                      = _t('SilvercartConfig.APACHE_SOLR_PORT', 'Apache Solr port');
        $fieldLabels['apacheSolrUrl']                       = _t('SilvercartConfig.APACHE_SOLR_URL', 'Apache Solr url');
        $fieldLabels['isStockManagementOverbookable']       = _t('SilvercartConfig.QUANTITY_OVERBOOKABLE', 'Is the stock quantity of a product generally overbookable?');
        $fieldLabels['demandBirthdayDateOnRegistration']    = _t('SilvercartConfig.DEMAND_BIRTHDAY_DATE_ON_REGISTRATION', 'Demand birthday date on registration?');
        $fieldLabels['GeoNamesActive']                      = _t('SilvercartConfig.GEONAMES_ACTIVE');
        $fieldLabels['GeoNamesUserName']                    = _t('SilvercartConfig.GEONAMES_USERNAME');
        $fieldLabels['GeoNamesAPI']                         = _t('SilvercartConfig.GEONAMES_API');
        return $fieldLabels;
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
        $summaryFields['DefaultCurrency'] = _t('SilvercartConfig.DEFAULTCURRENCY', 'Default currency');
        $summaryFields['EmailSender'] = _t('SilvercartConfig.EMAILSENDER', 'Email sender');
        $summaryFields['GlobalEmailRecipient'] = _t('SilvercartConfig.GLOBALEMAILRECIPIENT', 'Global email recipient');
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
                    $errorMessage = sprintf(_t('SilvercartConfig.ERROR_MESSAGE', 'Required configuration for "%s" is missing. Please <a href="%s/admin/silvercart-configuration/">log in</a> and choose "SilverCart Configuration -> general configuration" to edit the missing field.'), _t('SilvercartConfig.' . strtoupper($requiredField), $requiredField), Director::baseURL());
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
        return self::$emailSender;
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
     * Returns if the minimum order value shall be used.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 09.06.2011
     */
    public static function UseMinimumOrderValue() {
        if (is_null(self::$useMinimumOrderValue)) {
            self::$useMinimumOrderValue = self::getConfig()->useMinimumOrderValue;
        }
        return self::$useMinimumOrderValue;
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
     * determins weather a customer gets prices shown gross or net dependent on
     * customer's class
     *
     * @return string returns "gross" or "net"
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.3.2011
     */
    public static function Pricetype() {
        $member         = Member::currentUser();
        $configObject   = self::getConfig();

        if ($member) {
            if ($member->Groups()->find('Code', 'anonymous')) {
                self::$priceType = $configObject->PricetypeAnonymousCustomers;
            } else if ($member->Groups()->find('Code', 'b2b')) {
                self::$priceType = $configObject->PricetypeBusinessCustomers;
            } else if ($member->Groups()->find('Code', 'b2c')) {
                self::$priceType = $configObject->PricetypeRegularCustomers;
            } else {
                self::$priceType = $configObject->PricetypeAnonymousCustomers;
            }
        } else {
            self::$priceType = $configObject->PricetypeAnonymousCustomers;
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
                $errorMessage = _t('SilvercartConfig.ERROR_NO_CONFIG', 'SilvercartConfig is missing! Please <a href="/admin/silvercart-configuration/">log in</a> and choose "SilverCart Configuration -> general configuration" to add the general configuration. ');
                self::triggerError($errorMessage);
            }
        }
        return self::$config;
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
        if (DataObject::get_one('SilvercartCountry', "`Active`=1")) {
            $hasActiveCountries = true;
        }
        return array(
            'status'    => $hasActiveCountries,
            'message'   => sprintf(_t('SilvercartConfig.ERROR_MESSAGE_NO_ACTIVATED_COUNTRY', 'No activated country found. Please <a href="%s/admin/silvercart-configuration/">log in</a> and choose "SilverCart Configuration -> countries" to activate a country.'), Director::baseURL())
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
     * Returns the Apache Solr url.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.07.2011
     */
    public static function apacheSolrUrl() {
        if (is_null(self::$apacheSolrUrl)) {
            self::$apacheSolrUrl = self::getConfig()->apacheSolrUrl;
        }
        return self::$apacheSolrUrl;
    }
    
    /**
     * Returns the Apache Solr port.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.07.2011
     */
    public static function apacheSolrPort() {
        if (is_null(self::$apacheSolrPort)) {
            self::$apacheSolrPort = self::getConfig()->apacheSolrPort;
        }
        return self::$apacheSolrPort;
    }
    
    /**
     * Returns whether the Apache Solr search should be used or not.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.07.2011
     */
    public static function UseApacheSolrSearch() {
        if (is_null(self::$useApacheSolrSearch)) {
            self::$useApacheSolrSearch = self::getConfig()->useApacheSolrSearch;
        }
        return self::$useApacheSolrSearch;
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
     * Returns the display type of product administration
     *
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.09.2011
     */
    public static function DisplayTypeOfProductAdmin() {
        return self::getConfig()->displayTypeOfProductAdmin;
    }
    
    /**
     * Returns whether the display type of product administration is tabbed or not
     *
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.09.2011
     */
    public static function DisplayTypeOfProductAdminTabbed() {
        $displayTypeOfProductAdminTabbed = false;
        if (self::getConfig()->displayTypeOfProductAdmin == 'Tabbed' ) {
            $displayTypeOfProductAdminTabbed = true;
        }
        return $displayTypeOfProductAdminTabbed;
    }
    
    /**
     * Returns whether the display type of product administration is flat or not
     *
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.09.2011
     */
    public static function DisplayTypeOfProductAdminFlat() {
        $displayTypeOfProductAdminFlat = false;
        if (self::getConfig()->displayTypeOfProductAdmin == 'Flat' ) {
            $displayTypeOfProductAdminFlat = true;
        }
        return $displayTypeOfProductAdminFlat;
    }

    /**
     * Checks if the installation is complete. We assume a complete
     * installation if the Member table has the field "SilvercartShoppingCartID"
     * that is decorated via "SilvercartCustomer".
     * 
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.05.2011
     */
    public static function isInstallationCompleted() {
        $installationComplete   = false;
        
        if ((array_key_exists('SCRIPT_NAME', $_SERVER) && strpos($_SERVER['SCRIPT_NAME'], 'install.php') !== false) ||
            (array_key_exists('QUERY_STRING', $_SERVER) && strpos($_SERVER['QUERY_STRING'], 'successfullyinstalled') !== false) ||
            (array_key_exists('QUERY_STRING', $_SERVER) && strpos($_SERVER['QUERY_STRING'], 'deleteinstallfiles') !== false) ||
            (array_key_exists('REQUEST_URI', $_SERVER) && strpos($_SERVER['REQUEST_URI'], 'successfullyinstalled') !== false) ||
            (array_key_exists('REQUEST_URI', $_SERVER) && strpos($_SERVER['REQUEST_URI'], 'deleteinstallfiles') !== false)) {
            $installationComplete = false;
        } else {
            $memberFieldList        = array();
            $queryRes               = DB::query("SHOW TABLES");
            if ($queryRes->numRecords() > 0) {
                $queryRes               = DB::query("SHOW COLUMNS FROM Member");

                foreach ($queryRes as $key => $value) {
                    $memberFieldList[] = $value['Field'];
                }

                if (in_array('SilvercartShoppingCartID', $memberFieldList)) {
                    $installationComplete = true;
                }
            }
        }
        
        return $installationComplete;
    }
}
