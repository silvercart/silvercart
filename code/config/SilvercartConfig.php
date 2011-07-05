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
     * Attributes.
     *
     * @var array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2011
     */
    public static $db = array(
        'SilvercartVersion' => 'VarChar(16)',
        'SilvercartUpdateVersion' => 'VarChar(16)',
        'DefaultCurrency' => 'VarChar(16)',
        'EmailSender' => 'VarChar(255)',
        'GlobalEmailRecipient' => 'VarChar(255)',
        'PricetypeAnonymousCustomers' => 'VarChar(6)',
        'PricetypeRegularCustomers' => 'VarChar(6)',
        'PricetypeBusinessCustomers' => 'VarChar(6)',
        'PricetypeAdmins' => 'VarChar(6)',
        // Put DB definitions for interfaces here
        // Definitions for GeoNames
        'GeoNamesActive' => 'Boolean',
        'GeoNamesUserName' => 'VarChar(128)',
        'GeoNamesAPI' => 'VarChar(255)',
        'enableSSL' => 'Boolean(0)',
        'productsPerPage' => 'Int',
        'productGroupsPerPage' => 'Int',
        'minimumOrderValue' => 'Money',
        'useMinimumOrderValue' => 'Boolean(0)'
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
        'SilvercartNoImage' => 'Image'
    );
    
    public static $defaults = array(
        'SilvercartVersion' => '1.0',
        'SilvercartUpdateVersion' => '3',
        'PricetypeAnonymousCustomers' => 'gross',
        'PricetypeRegularCustomers' => 'gross',
        'PricetypeBusinessCustomers' => 'net',
        'PricetypeAdmins' => 'net',
        'GeoNamesActive' => false,
        'GeoNamesAPI' => 'http://api.geonames.org/',
        'productsPerPage' => 15,
        'productGroupsPerPage' => 6,
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
    /**
     * The configuration fields should have a static attribute to set after its
     * first call (to prevent redundant logic).
     */
    public static $defaultCurrency = null;
    public static $emailSender = null;
    public static $globalEmailRecipient = null;
    public static $priceType = null;
    public static $config = null;
    public static $enableSSL = null;
    public static $minimumOrderValue = null;
    public static $useMinimumOrderValue = null;
    public static $productsPerPage = null;

    /**
     * Constructor. We localize the static variables here.
     *
     * @param array|null $record      This will be null for a new database record.
     *                                  Alternatively, you can pass an array of
     *                                  field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                  don't have their defaults set.
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$singular_name = _t('SilvercartConfig.SINGULARNAME', 'General configuration');
        self::$plural_name = _t('SilvercartConfig.PLURALNAME', 'General configurations');
        parent::__construct($record, $isSingleton);
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

        // Building the general tab structure
        $CMSFields = new FieldSet(
            $rootTab = new TabSet(
                'Root',
                $generalTab = new TabSet(
                    'General',
                    $tabGeneralMain = new Tab('Main'),
                    $tabGeneralTestData = new Tab('TestData')
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

        $CMSFields->addFieldsToTab('Root.General.Main', $defaultCMSFields->dataFields());
        $CMSFields->addFieldToTab('Root.General.Main', new LabelField('ForEmailSender', _t('SilvercartConfig.EMAILSENDER_INFO')), 'GlobalEmailRecipient');
        // configure the fields for pricetype configuration
        $pricetypes = array(
            'PricetypeAnonymousCustomers' => _t('SilvercartConfig.PRICETYPE_ANONYMOUS', 'Pricetype anonymous customers'),
            'PricetypeRegularCustomers' => _t('SilvercartConfig.PRICETYPE_REGULAR', 'Pricetype regular customers'),
            'PricetypeBusinessCustomers' => _t('SilvercartConfig.PRICETYPE_BUSINESS', 'Pricetype business customers'),
            'PricetypeAdmins' => _t('SilvercartConfig.PRICETYPE_ADMINS', 'Pricetype administrators')
        );
        $pricetypeDropdownValues = array(
            'gross' => _t('SilvercartCustomerRole.GROSS'),
            'net' => _t('SilvercartCustomerRole.NET')
        );
        foreach ($pricetypes as $name => $title) {
            $CMSFields->removeByName($name);
            $CMSFields->addFieldToTab('Root.General.Main', new DropdownField($name, $title, $pricetypeDropdownValues));
        }

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
        
        // FormFields for Interfaces right here
        $interfacesTab->setTitle(_t('SilvercartConfig.INTERFACES'));
        // GeoNames
        $tabInterfacesGeoNames->setTitle(_t('SilvercartConfig.INTERFACES_GEONAMES'));

        $geoNamesDescriptionValue = '';
        $geoNamesDescriptionValue .= '<h3>Description</h3>';
        $geoNamesDescriptionValue .= '<p>GeoNames provides a detailed database of geo informations. It can be used to get up-to-date country informations (name, ISO2, ISO3, etc.).<br/>';
        $geoNamesDescriptionValue .= 'To use this feature, you have to create an account at <a href="http://www.geonames.org/" target="blank">http://www.geonames.org/</a>, confirm the registration and activate the webservice.<br/>';
        $geoNamesDescriptionValue .= 'Then set GeoNames to be active, put your username into the Username field and save the configuration right here.<br/>';
        $geoNamesDescriptionValue .= 'After that, SilverCart will sync your countries with the GeoNames database on every /dev/build, optionally in multiple languages.</p>';
        $geoNamesDescription = new LiteralField('GeoNamesDescription', $geoNamesDescriptionValue);
        $CMSFields->addFieldToTab('Root.Interfaces.GeoNames', $geoNamesDescription);
        $CMSFields->addFieldToTab('Root.Interfaces.GeoNames', $CMSFields->dataFieldByName('GeoNamesActive'));
        $CMSFields->addFieldToTab('Root.Interfaces.GeoNames', $CMSFields->dataFieldByName('GeoNamesUserName'));
        $CMSFields->addFieldToTab('Root.Interfaces.GeoNames', $CMSFields->dataFieldByName('GeoNamesAPI'));

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
        $fieldLabels['DefaultCurrency'] = _t('SilvercartConfig.DEFAULTCURRENCY', 'Default currency');
        $fieldLabels['EmailSender'] = _t('SilvercartConfig.EMAILSENDER', 'Email sender');
        $fieldLabels['GlobalEmailRecipient'] = _t('SilvercartConfig.GLOBALEMAILRECIPIENT', 'Global email recipient');
        $fieldLabels['enableSSL'] = _t('SilvercartConfig.ENABLESSL', 'Enable SSL');
        $fieldLabels['minimumOrderValue'] = _t('SilvercartConfig.MINIMUMORDERVALUE', 'Minimum order value');
        $fieldLabels['useMinimumOrderValue'] = _t('SilvercartConfig.USEMINIMUMORDERVALUE', 'Use minimum order value');
        $fieldLabels['productsPerPage'] = _t('SilvercartConfig.PRODUCTSPERPAGE', 'Products per page');
        $fieldLabels['productGroupsPerPage'] = _t('SilvercartConfig.PRODUCTGROUPSPERPAGE', 'Product groups per page');
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
     * determins weather a customer gets prices shown gross or net dependent on customer's class
     *
     * @return string returns "gross" or "net"
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.3.2011
     */
    public static function Pricetype() {
            $member = Member::currentUser();
            $configObject = self::getConfig();
            if ($member) {
                switch ($member->ClassName) {
                    case "SilvercartAnonymousCustomer":
                        self::$priceType = $configObject->PricetypeAnonymousCustomers;
                        break;
                    case "SilvercartRegularCustomer":
                        self::$priceType = $configObject->PricetypeRegularCustomers;
                        break;
                    case "SilvercartBusinessCustomer":
                        self::$priceType = $configObject->PricetypeBusinessCustomers;
                        break;
                    case "Member":
                        self::$priceType = $configObject->PricetypeAdmins;
                        break;
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
     * that is decorated via "SilvercartCustomerRole".
     * 
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.05.2011
     */
    public static function isInstallationCompleted() {
        $installationComplete   = false;
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
        
        return $installationComplete;
    }
}
