<?php

namespace SilverCart\Admin\Model;

use SilverCart\Admin\Dev\Install\RequireDefaultRecords;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\View\GroupView\GroupViewHandler;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Manifest\VersionProvider;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DatabaseAdmin;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Member;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;

/**
 * The class SilverCart\Admin\Model\Config is the Handler for central configurations of
 * SilverCart.
 * Configuration parameter which are defined as a value of $db must have a static
 * getter named like the attribute (without a 'get' in front of it).
 * If a 'get' is put in front of the methods name, it will cause a crash in case
 * of misconfiguration, because SilverStripes 'magic' getter will be overwritten
 * and called in backend by SilverStripes default logic. This will trigger an
 * configuration error, when no configuration is given.
 *
 * @package SilverCart
 * @subpackage Admin_Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 25.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Config
{
    use Extensible;
    
    const PRICE_TYPE_GROSS           = 'gross';
    const PRICE_TYPE_NET             = 'net';
    const WEIGHT_UNIT_GRAM           = 'g';
    const WEIGHT_UNIT_KILOGRAM       = 'kg';
    const WEIGHT_UNIT_POUND          = 'lbs';
    const DIMENSION_UNIT_CENTIMETER  = 'cm';
    const DIMENSION_UNIT_INCH        = 'in';
    const ENUMERATION_WEIGHT_UNIT    = 'Enum("'
            . self::WEIGHT_UNIT_GRAM . ','
            . self::WEIGHT_UNIT_POUND
            . '","' . self::WEIGHT_UNIT_GRAM . '")';
    const ENUMERATION_DIMENSION_UNIT = 'Enum("'
            . self::DIMENSION_UNIT_CENTIMETER . ','
            . self::DIMENSION_UNIT_INCH
            . '","' . self::DIMENSION_UNIT_CENTIMETER . '")';
    
    /**
     * Contains the possible values for products per page selectors for
     * storefront users.
     * 
     * This array is directly used for <option> Tags:
     *  'value' => 'Title': <option value="value">Title</option>
     * 
     * Use the array key 0 if you want to offer an option to show all products.
     *
     * @var array
     */
    public static $productsPerPageOptions = array(
        '18' => '18',
        '30' => '30',
        '60' => '60',
        '90' => '90',
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
    public static $defaultLayoutLoaded  = false;

    /**
     * The configuration fields should have a static attribute to set after its
     * first call (to prevent redundant logic).
     */
    public static $addToCartMaxQuantity                  = null;
    public static $defaultCurrency                       = null;
    public static $defaultCurrencySymbol                 = null;
    public static $defaultPricetype                      = null;
    public static $emailSender                           = null;
    public static $emailSenderName                       = null;
    public static $enableBusinessCustomers               = null;
    public static $enablePackstation                     = null;
    public static $globalEmailRecipient                  = null;
    public static $priceType                             = null;
    public static $config                                = null;
    public static $enableSSL                             = null;
    public static $minimumOrderValue                     = null;
    public static $freeOfShippingCostsFrom               = null;
    public static $useFreeOfShippingCostsFrom            = null;
    public static $useMinimumOrderValue                  = null;
    public static $productsPerPage                       = null;
    public static $silvercartVersion                     = null;
    public static $silvercartMinorVersion                = null;
    public static $silvercartFullVersion                 = null;
    public static $enableStockManagement                 = null;
    public static $isStockManagementOverbookable         = null;
    public static $redirectToCartAfterAddToCart          = null;
    public static $redirectToCheckoutWhenInCart          = null;
    public static $demandBirthdayDateOnRegistration      = null;
    public static $useMinimumAgeToOrder                  = null;
    public static $minimumAgeToOrder                     = null;
    public static $useDefaultLanguageAsFallback          = null;
    public static $forceLoadingOfDefaultLayout           = false;
    public static $productDescriptionFieldForCart        = null;
    public static $useProductDescriptionFieldForCart     = true;
    public static $useStrictSearchRelevance              = false;
    public static $defaultMailRecipient                  = null;
    public static $defaultMailOrderNotificationRecipient = null;
    public static $defaultMailRevocationRecipient        = null;
    public static $defaultMailRegistrationRecipient      = null;
    public static $defaultContactMessageRecipient        = null;
    public static $userAgentBlacklist                    = null;
    public static $skipPaymentStepIfUnique               = null;
    public static $skipShippingStepIfUnique              = null;
    public static $invoiceAddressIsAlwaysShippingAddress = null;
    public static $displayWeightsInKilogram              = null;
    public static $showTaxAndDutyHint                    = false;
    public static $colorScheme                           = null;
    public static $silvercartLogo                        = null;

    /**
     * This method checks the required configuration. If there is any missing
     * configuration, an error will be displayed.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.12.2015
     */
    public static function Check()
    {
        if (is_null(self::$required_configuration_fields)) {
            return true;
        }
        if (empty(self::$required_configuration_fields)) {
            return true;
        }
        if (is_array(self::$required_configuration_fields)) {
            $config = self::getConfig();
            foreach (self::$required_configuration_fields as $requiredField) {
                if (empty($requiredField)
                    || is_null($requiredField)
                ) {
                    continue;
                }

                if ($config->hasMethod('check' . $requiredField)) {
                    $method = 'check' . $requiredField;
                    $result = $config->$method();

                    if ($result['status'] === false) {
                        $errorMessage = $result['message'];
                        self::triggerError($errorMessage);
                    }
                } elseif (empty($config->$requiredField)) {
                    $errorMessage = _t(Config::class . '.ERROR_MESSAGE',
                            'Required configuration for "{field}" is missing. Please <a href="{baseurl}/admin/settings/">log in</a> and choose "SilverCart Configuration -> general configuration" to edit the missing field.',
                            [
                                'field'   => _t(Config::class . '.' . strtoupper($requiredField), $requiredField),
                                'baseurl' => Director::baseURL()
                            ]
                    );
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
    public static function DefaultCurrency()
    {
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
    public static function DefaultCurrencySymbol()
    {
        if (is_null(self::$defaultCurrencySymbol)) {

            $zend_currency               = new Zend_Currency(null, i18n::config()->get('default_locale'));
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
    public static function DefaultPriceType()
    {
        if (is_null(self::$defaultPricetype)
            || empty(self::$defaultPricetype)
        ) {
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
    public static function DemandBirthdayDateOnRegistration()
    {
        if (is_null(self::$demandBirthdayDateOnRegistration)) {
            self::$demandBirthdayDateOnRegistration = (bool) self::getConfig()->demandBirthdayDateOnRegistration;
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
    public static function UseMinimumAgeToOrder()
    {
        if (is_null(self::$useMinimumAgeToOrder)) {
            self::$useMinimumAgeToOrder = (bool) self::getConfig()->UseMinimumAgeToOrder;
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
    public static function MinimumAgeToOrder()
    {
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
    public static function MinimumAgeToOrderError()
    {
        $error = _t(Config::class . '.MinimumAgeToOrderError',
                'The minimum age to order is {age} years.',
                [
                    'age' => self::MinimumAgeToOrder(),
                ]
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
    public static function CheckMinimumAgeToOrder($birthdate)
    {
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
    public static function EmailSender()
    {
        if (is_null(self::$emailSender)) {
            self::$emailSender = self::getConfig()->EmailSender;
        }
        return iconv("UTF-8", "ISO-8859-1", self::$emailSender);
    }

    /**
     * Returns the configured email sender.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.06.2018
     */
    public static function EmailSenderName()
    {
        if (is_null(self::$emailSenderName)) {
            self::$emailSenderName = self::getConfig()->EmailSenderName;
        }
        return self::$emailSenderName;
    }

    /**
     * Returns if SSL should be used.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.04.2011
     */
    public static function EnableSSL()
    {
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
    public static function EnableStockManagement()
    {
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
    public static function isStockManagementOverbookable()
    {
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
    public static function MinimumOrderValue()
    {
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
    public static function UseFreeOfShippingCostsFrom()
    {
        if (is_null(self::$useFreeOfShippingCostsFrom)) {
            self::$useFreeOfShippingCostsFrom = self::getConfig()->useFreeOfShippingCostsFrom;
        }
        return self::$useFreeOfShippingCostsFrom;
    }

    /**
     * Returns the free of shipping costs from value if specified.
     * 
     * @param Country $shippingCountry Shipping country
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.07.2013
     */
    public static function FreeOfShippingCostsFrom($shippingCountry = null)
    {
        self::$freeOfShippingCostsFrom = self::getConfig()->freeOfShippingCostsFrom;
        if (!($shippingCountry instanceof Country)
            && Controller::curr()->hasMethod('getCombinedStepData')
        ) {
            $checkoutData = Controller::curr()->getCombinedStepData();
            if (array_key_exists('Shipping_Country', $checkoutData)) {
                $shippingCountryID = $checkoutData['Shipping_Country'];
                $shippingCountry   = Country::get()->byID($shippingCountryID);
            }
        }
        if ($shippingCountry
            && !is_null($shippingCountry->freeOfShippingCostsFrom->getAmount())
            && is_numeric($shippingCountry->freeOfShippingCostsFrom->getAmount())
        ) {
            $shippingCountry->freeOfShippingCostsFrom->getAmount();
            self::$freeOfShippingCostsFrom = $shippingCountry->freeOfShippingCostsFrom;
        }
        return self::$freeOfShippingCostsFrom;
    }

    /**
     * Returns the SilverCart version.
     *
     * @return string
     */
    public static function SilverCartVersion() : string
    {
        if (is_null(self::$silvercartVersion)) {
            $defaults                = SiteConfig::config()->defaults;
            self::$silvercartVersion = $defaults['SilvercartVersion'];
        }
        return (string) self::$silvercartVersion;
    }

    /**
     * Returns the SilverCart minor version.
     *
     * @return string
     */
    public static function SilverCartMinorVersion() : string
    {
        if (is_null(self::$silvercartMinorVersion)) {
            $defaults                     = SiteConfig::config()->defaults;
            self::$silvercartMinorVersion = $defaults['SilvercartMinorVersion'];
        }
        return (string) self::$silvercartMinorVersion;
    }

    /**
     * Returns the full SilverCart version number.
     *
     * @return string
     */
    public static function SilverCartFullVersion() : string
    {
        if (is_null(self::$silvercartFullVersion)) {
            $versionProvider = new VersionProvider();
            $moduleName      = 'silvercart/silvercart';
            $lockModules     = $versionProvider->getModuleVersionFromComposer([$moduleName]);
            if (empty($lockModules)) {
                $version      = self::SilverCartVersion();
                $minorVersion = self::SilverCartMinorVersion();
                self::$silvercartFullVersion = "{$version}.{$minorVersion}";
            } else {
                self::$silvercartFullVersion = $lockModules[$moduleName];
            }
        }
        return (string) self::$silvercartFullVersion;
    }

    /**
     * Returns if the minimum order value shall be used.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.06.2011
     */
    public static function UseMinimumOrderValue()
    {
        if (is_null(self::$useMinimumOrderValue)) {
            self::$useMinimumOrderValue = (bool) self::getConfig()->useMinimumOrderValue;
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
    public static function UserAgentBlacklist()
    {
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
    public static function GlobalEmailRecipient()
    {
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
    public static function ProductsPerPage()
    {
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
    public static function ProductGroupsPerPage()
    {
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
    public static function DisplayedPaginationPages()
    {
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
    public static function productDescriptionFieldForCart()
    {
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
    public static function useProductDescriptionFieldForCart()
    {
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
    public static function useStrictSearchRelevance()
    {
        if (is_null(self::$useStrictSearchRelevance)) {
            self::$useStrictSearchRelevance = self::getConfig()->useStrictSearchRelevance;
        }
        return self::$useStrictSearchRelevance;
    }

    /**
     * Returns the default mail recipient
     * 
     * @return string email address
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.10.2012
     */
    public static function DefaultMailRecipient()
    {
        if (is_null(self::$defaultMailRecipient)) {
            self::$defaultMailRecipient = self::getConfig()->DefaultMailRecipient;
            if (empty(self::$defaultMailRecipient)) {
                self::$defaultMailRecipient = Email::config()->get('admin_email');
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
    public static function DefaultMailOrderNotificationRecipient()
    {
        if (is_null(self::$defaultMailOrderNotificationRecipient)) {
            self::$defaultMailOrderNotificationRecipient = self::getConfig()->DefaultMailOrderNotificationRecipient;
            if (empty(self::$defaultMailOrderNotificationRecipient)) {
                self::$defaultMailOrderNotificationRecipient = self::DefaultMailRecipient();
            }
        }
        return self::$defaultMailOrderNotificationRecipient;
    }

    /**
     * Returns the default mail revocation recipient
     * 
     * @return string email address
     */
    public static function DefaultMailRevocationRecipient() : string
    {
        if (is_null(self::$defaultMailRevocationRecipient)) {
            self::$defaultMailRevocationRecipient = self::getConfig()->DefaultMailRevocationRecipient;
            if (empty(self::$defaultMailRevocationRecipient)) {
                self::$defaultMailRevocationRecipient = self::DefaultMailOrderNotificationRecipient();
            }
        }
        return (string) self::$defaultMailRevocationRecipient;
    }
    
    /**
     * Returns the default mail registration recipient
     * 
     * @return string email address
     */
    public static function DefaultMailRegistrationRecipient()
    {
        if (is_null(self::$defaultMailRegistrationRecipient)) {
            self::$defaultMailRegistrationRecipient = self::getConfig()->DefaultMailRegistrationRecipient;
            if (empty(self::$defaultMailRegistrationRecipient)) {
                self::$defaultMailRegistrationRecipient = self::DefaultMailOrderNotificationRecipient();
            }
        }
        return (string) self::$defaultMailRegistrationRecipient;
    }

    /**
     * Returns the default contact message recipient
     * 
     * @return string email address
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.10.2012
     */
    public static function DefaultContactMessageRecipient()
    {
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
    public static function SkipPaymentStepIfUnique()
    {
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
    public static function SkipShippingStepIfUnique()
    {
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
    public static function InvoiceAddressIsAlwaysShippingAddress()
    {
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
    public static function DisplayWeightsInKilogram()
    {
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
    public static function ShowTaxAndDutyHint()
    {
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.04.2018
     */
    public static function Pricetype()
    {
        if (is_null(self::$priceType)) {
            $member       = Customer::currentUser();
            $configObject = self::getConfig();

            if ($member instanceof Member
                && $member->exists()
            ) {
                foreach ($member->Groups() as $group) {
                    if (!empty($group->Pricetype)
                        && $group->Pricetype != '---'
                    ) {
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
            $configObject->extend('updatePriceType', self::$priceType, $member);
        }
        return self::$priceType;
    }

    /**
     * Returns the current ColorScheme.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.02.2016
     */
    public static function ColorScheme()
    {
        if (is_null(self::$colorScheme)) {
            self::$colorScheme = self::getConfig()->ColorScheme;
        }
        return self::$colorScheme;
    }

    /**
     * Returns the current SilverCart logo.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.02.2016
     */
    public static function ShopLogo()
    {
        if (is_null(self::$silvercartLogo)) {
            self::$silvercartLogo = self::getConfig()->ShopLogo();
        }
        return self::$silvercartLogo;
    }

    /**
     * Returns the SiteConfig or triggers an error if not existent.
     *
     * @return SiteConfig
     */
    public static function getConfig()
    {
        if (is_null(self::$config)) {
            if (self::configTableExists()) {
                self::$config = SiteConfig::current_site_config();
            } else {
                self::$config = SiteConfig::singleton();
            }
            if (!self::$config) {
                if (Tools::isIsolatedEnvironment()) {
                    return false;
                }
                $errorMessage = _t(Config::class . '.ERROR_NO_CONFIG', 'Shop Configuration is missing! Please <a href="/dev/build/?flush=all">run a database build</a>. ');
                self::triggerError($errorMessage);
            }
        }
        return self::$config;
    }

    /**
     * Returns whether dev/build is currently running.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.08.2018
     */
    public static function devBuildIsRunning()
    {
        $devBuildIsRunning = false;
        $ctrl              = Controller::curr();
        if ($ctrl instanceof DatabaseAdmin
            && $ctrl->getRequest()->param('Action') === 'build'
        ) {
            $devBuildIsRunning = true;
        }
        return $devBuildIsRunning;
    }

    /**
     * Returns whether the SiteConfig table exists.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.08.2018
     */
    public static function configTableExists()
    {
        $tables      = DB::get_schema()->tableList();
        $tableExists = false;
        if (is_array($tables)
            && in_array('SiteConfig', $tables)
        ) {
            $tableExists = true;
        }
        return $tableExists;
    }

    /**
     * Returns all hidden registered menus for the storeadmin.
     * 
     * @return array
     */
    public static function getHiddenRegisteredMenus()
    {
        return self::$hiddenRegisteredMenus;
    }

    /**
     * Returns all registered menus for the storeadmin.
     * 
     * @return array
     */
    public static function getRegisteredMenus()
    {
        return self::$registeredMenus;
    }

    /**
     * Returns the Non-CMS menu identifiers.
     * 
     * @return array
     */
    public static function getMenuNonCmsIdentifiers()
    {
        return self::$menuNonCmsIdentifiers;
    }

    /**
     * Returns the default no-image visualisation.
     * 
     * @return \SilverStripe\Assets\Image
     */
    public static function getNoImage()
    {
        $configObject = self::getConfig();

        return $configObject->SilvercartNoImage();
    }

    /**
     * Returns the standard product condition.
     * 
     * @return \SilverCart\Model\Product\ProductCondition
     */
    public static function getStandardProductCondition()
    {
        $configObject = self::getConfig();

        return $configObject->StandardProductCondition();
    }

    /**
     * Alias for RedirectToCartAfterAddToCart.
     * 
     * @return bool
     */
    public static function getRedirectToCartAfterAddToCartAction()
    {
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
    public static function RedirectToCartAfterAddToCart()
    {
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
    public static function RedirectToCheckoutWhenInCart()
    {
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
    public static function getProductsPerPageDefault()
    {
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
    public static function setProductsPerPageOptions(array $productsPerPageOptions, $includeAllProductsOption = false)
    {
        if (is_array($productsPerPageOptions)) {
            self::$productsPerPageOptions = $productsPerPageOptions;
            if ($includeAllProductsOption) {
                self::$productsPerPageOptions['0'] = _t(Config::class . '.PRODUCTSPERPAGE_ALL', 'Show all');
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
    public static function getProductsPerPageOptions()
    {

        if (array_key_exists('0', self::$productsPerPageOptions)) {
            self::$productsPerPageOptions['0'] = _t(Config::class . '.PRODUCTSPERPAGE_ALL', 'Show all');
        }

        return self::$productsPerPageOptions;
    }

    /**
     * Returns an ArrayList with values for products per page, e.g.
     * <pre>
     * <select>
     * <% loop $SiteConfig.ProductsPerPageOptionsForTemplate %>
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
    public static function getProductsPerPageOptionsForTemplate()
    {
        if (is_null(self::$productsPerPageOptionsForTemplate)) {
            self::$productsPerPageOptionsForTemplate = new ArrayList();
            $options                                 = self::getProductsPerPageOptions();
            foreach ($options as $option => $value) {
                self::$productsPerPageOptionsForTemplate->push(
                        new ArrayData([
                            'Option' => $option,
                            'Value'  => $value,
                        ])
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
    public static function getProductsPerPageUnlimitedNumber()
    {
        return self::$productsPerPageUnlimitedNumber;
    }

    /**
     * Diplays an error rendered with SilverCart's error template.
     *
     * @param string $errorMessage the error message to display
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2014
     */
    public static function triggerError($errorMessage)
    {
        if (Tools::isIsolatedEnvironment()) {
            $output = $errorMessage;
        } else {
            $elements = [
                'ErrorMessage' => Tools::string2html($errorMessage),
            ];
            $output   = Controller::curr()->customise($elements)->renderWith([
                'SilverCart/Model/Pages/ErrorPage',
                'Page',
            ]);
        }
        print $output;
        exit();
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
    public static function disableDefaultLayout()
    {
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
    public static function DefaultLayoutEnabled()
    {
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
    public static function DefaultLayoutLoaded()
    {
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
    public static function setDefaultLayoutLoaded($loaded)
    {
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
    public static function setMenuNonCmsIdentifier($identifier)
    {
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
    public static function enableTestData()
    {
        RequireDefaultRecords::enableTestData();
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
    public static function disableTestData()
    {
        RequireDefaultRecords::disableTestData();
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
    public static function addGroupView($groupView)
    {
        GroupViewHandler::addGroupView($groupView);
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
    public static function addGroupHolderView($groupHolderView)
    {
        GroupViewHandler::addGroupHolderView($groupHolderView);
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
    public static function removeGroupView($groupView)
    {
        GroupViewHandler::removeGroupView($groupView);
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
    public static function registerMenu($code, $menuTitle)
    {
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
    public static function registerHiddenMenu($code)
    {
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
    public static function removeGroupHolderView($groupHolderView)
    {
        GroupViewHandler::removeGroupHolderView($groupHolderView);
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
    public static function addToCartMaxQuantity()
    {
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
    public static function enableBusinessCustomers()
    {
        if (is_null(self::$enableBusinessCustomers)) {
            self::$enableBusinessCustomers = (bool) self::getConfig()->enableBusinessCustomers;
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
    public static function enablePackstation()
    {
        if (is_null(self::$enablePackstation)) {
            self::$enablePackstation = (bool) self::getConfig()->enablePackstation;
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
    public static function setDefaultGroupView($defaultGroupView = null)
    {
        GroupViewHandler::setDefaultGroupView($defaultGroupView);
    }

    /**
     * set the group view to use by default for product group lists
     *
     * @param string $defaultGroupHolderView the class name of the group view to use by default
     *
     * @return void
     */
    public static function setDefaultGroupHolderView($defaultGroupHolderView = null)
    {
        GroupViewHandler::setDefaultGroupHolderView($defaultGroupHolderView);
    }

    /**
     * Checks if the installation is complete. We assume a complete
     * installation if the Member table has the field "ShoppingCartID"
     * that is decorated via "Customer".
     * 
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.06.2012
     * @deprecated use Tools::isInstallationCompleted() instead
     */
    public static function isInstallationCompleted()
    {
        return Tools::isInstallationCompleted();
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
    public static function isValidUrl($url, $conectionTimeout = 5)
    {
        $curl     = curl_init($url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $conectionTimeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5); //The maximum number of seconds to allow cURL functions to execute.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($httpcode >= 200
            && $httpcode < 300
        ) {
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
    public static function isUserAgentBlacklisted($userAgent)
    {
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.04.2018
     */
    public static function Log($context, $text, $filename = 'default')
    {
        Tools::Log($context, $text, $filename);
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
    public static function DefaultLanguage()
    {
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
    public static function Locale()
    {
        if (self::getConfig() === false) {
            return i18n::config()->get('default_locale');
        }
        return self::getConfig()->DefaultLocale;
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
    public static function useDefaultLanguageAsFallback()
    {
        if (is_null(self::$useDefaultLanguageAsFallback)) {
            if (!self::getConfig() === false) {
                self::$useDefaultLanguageAsFallback = self::getConfig()->useDefaultLanguageAsFallback;
            }
        }
        return self::$useDefaultLanguageAsFallback;
    }
}