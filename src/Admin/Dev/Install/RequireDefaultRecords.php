<?php

namespace SilverCart\Admin\Dev\Install;

use ReflectionClass;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Order\NumberRange;
use SilverCart\Model\Order\OrderStatus;
use SilverCart\Model\Product\Manufacturer;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\ProductTranslation;
use SilverCart\Model\Product\Tax;
use SilverCart\Model\Product\TaxTranslation;
use SilverCart\Model\Pages\AddressHolder;
use SilverCart\Model\Pages\CartPage;
use SilverCart\Model\Pages\CheckoutStep;
use SilverCart\Model\Pages\ContactFormPage;
use SilverCart\Model\Pages\CustomerDataPage;
use SilverCart\Model\Pages\FrontPage;
use SilverCart\Model\Pages\MetaNavigationHolder;
use SilverCart\Model\Pages\MetaNavigationPage;
use SilverCart\Model\Pages\MyAccountHolder;
use SilverCart\Model\Pages\NewsletterPage;
use SilverCart\Model\Pages\OrderDetailPage;
use SilverCart\Model\Pages\OrderHolder;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Pages\PaymentNotification;
use SilverCart\Model\Pages\PaymentMethodsPage;
use SilverCart\Model\Pages\ProductGroupHolder;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Pages\RegistrationPage;
use SilverCart\Model\Pages\RevocationFormPage;
use SilverCart\Model\Pages\SearchResultsPage;
use SilverCart\Model\Pages\ShippingFeesPage;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Product\AvailabilityStatus;
use SilverCart\Model\Shipment\Carrier;
use SilverCart\Model\Shipment\CarrierTranslation;
use SilverCart\Model\Shipment\ShippingFee;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverCart\Model\Shipment\ShippingMethodTranslation;
use SilverCart\Model\Shipment\Zone;
use SilverCart\Model\Shipment\ZoneTranslation;
use SilverCart\Model\Widgets\ImageSliderImage;
use SilverCart\Model\Widgets\ImageSliderImageTranslation;
use SilverCart\Model\Widgets\ImageSliderWidget;
use SilverCart\Model\Widgets\LoginWidget;
use SilverCart\Model\Widgets\ProductGroupItemsWidget;
use SilverCart\Model\Widgets\ShoppingCartWidget;
use SilverCart\Model\Widgets\SlidorionProductGroupWidget;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\RedirectorPage;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Director;
use SilverStripe\Control\Email\Email;
use SilverStripe\ErrorPage\ErrorPage;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\Security\Group;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Widgets\Model\WidgetArea;
use Translatable;
use WidgetSets\Model\WidgetSet;

/**
 * Collects all default records to avoid redundant code when it comes to relations.
 * You do not need to search for other default records, they are all here.
 * 
 * @package SilverCart
 * @subpackage Admin_Dev_Install
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class RequireDefaultRecords {
    
    use \SilverStripe\Core\Extensible;
    use \SilverStripe\Core\Injector\Injectable;

    /**
     * If set to true the next /dev/build/ will add test data to the database.
     *
     * @var boolean
     */
    protected static $enableTestData = false;
    
    /**
     * Translation locale
     *
     * @var string 
     */
    protected $translationLocale = null;
    
    /**
     * Returns the translation locale
     *
     * @return string 
     */
    public function getTranslationLocale() {
        return $this->translationLocale;
    }

    /**
     * Sets the translation locale
     *
     * @param string $translationLocale Translation locale
     * 
     * @return void
     */
    public function setTranslationLocale($translationLocale) {
        $this->translationLocale = $translationLocale;
    }
    
    /**
     * Creates the default groups used in SilverCart
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012 
     */
    public function createDefaultGroups() {
        // Create an own group for anonymous customers
        $anonymousGroup = Group::get()->filter('Code', 'anonymous')->first();
        if (!$anonymousGroup) {
            $anonymousGroup             = new Group();
            $anonymousGroup->Title      = _t(Customer::class . '.ANONYMOUSCUSTOMER', 'anonymous customer');
            $anonymousGroup->Code       = "anonymous";
            $anonymousGroup->Pricetype  = "gross";
            $anonymousGroup->write();
        }

        // Create an own group for b2b customers
        $B2Bgroup = Group::get()->filter('Code', 'b2b')->first();
        if (!$B2Bgroup) {
            $B2Bgroup               = new Group();
            $B2Bgroup->Title        = _t(Customer::class . '.BUSINESSCUSTOMER', 'business customer');
            $B2Bgroup->Code         = "b2b";
            $B2Bgroup->Pricetype    = "net";
            $B2Bgroup->write();
        }

        //create a group for b2c customers
        $B2Cgroup = Group::get()->filter('Code', 'b2c')->first();
        if (!$B2Cgroup) {
            $B2Cgroup               = new Group();
            $B2Cgroup->Title        = _t(Customer::class . '.REGULARCUSTOMER', 'regular customer');
            $B2Cgroup->Code         = "b2c";
            $B2Cgroup->Pricetype    = "gross";
            $B2Cgroup->write();
        }
    }
    
    /**
     * Creates the default Config if not exists
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.12.2015
     */
    public function createDefaultConfig() {
        $config = Config::getConfig();
        if ($config instanceof SiteConfig &&
            is_null($config->DefaultCurrency)) {
            $config->DefaultCurrency = 'EUR';
            $email = Email::config()->get('admin_email');
            if ($email != '') {
                $config->EmailSender          = $email;
                $config->DefaultMailRecipient = $email;
            }
            $config->write();
        }
    }
    
    /**
     * Creates the default OrderStatus if not exists
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.10.2012
     */
    public function createDefaultOrderStatus() {
        // create order status
        $defaultStatusEntries = array(
            'new' => array(
                'en_US' => 'New',
                'en_GB' => 'New',
                'de_DE' => 'Neu',
            ),
            'canceled' => array(
                'en_US' => 'Canceled',
                'en_GB' => 'Cancelled',
                'de_DE' => 'Storniert',
            ),
            'pending' => array(
                'en_US' => 'Waiting for payment',
                'en_GB' => 'Waiting for payment',
                'de_DE' => 'Auf Zahlungseingang wird gewartet',
            ),
            'payed' => array(
                'en_US' => 'Payed',
                'en_GB' => 'Payed',
                'de_DE' => 'Bezahlt',
            ),
            'shipped' => array(
                'en_US' => 'Order shipped',
                'en_GB' => 'Order shipped',
                'de_DE' => 'Versendet',
            ),
            'inwork' => array(
                'en_US' => 'In work',
                'en_GB' => 'In work',
                'de_DE' => 'In Arbeit',
            ),
        );
        $locales        = array('de_DE', 'en_GB', 'en_US');
        $fallbackLocale = false;

        if (!in_array(Translatable::get_current_locale(), $locales)) {
            $locales[]      = Translatable::get_current_locale();
            $fallbackLocale = Translatable::get_current_locale();
        }

        if ($fallbackLocale !== false) {
            $defaultStatusEntries['pending'][$fallbackLocale] = $defaultStatusEntries['pending']['en_US'];
            $defaultStatusEntries['payed'][$fallbackLocale]   = $defaultStatusEntries['payed']['en_US'];
            $defaultStatusEntries['shipped'][$fallbackLocale] = $defaultStatusEntries['shipped']['en_US'];
            $defaultStatusEntries['inwork'][$fallbackLocale]  = $defaultStatusEntries['inwork']['en_US'];
        }
        $this->createDefaultTranslatableDataObject($defaultStatusEntries, OrderStatus::class);
    }
    
    /**
     * Creates the default AvailabilityStatus if not exists
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function createDefaultAvailabilityStatus() {
        $defaults = array(
            'available'     => array(
                'en_US' => 'available',
                'en_GB' => 'available',
                'de_DE' => 'verfügbar'
            ),
            'not-available' => array(
                'en_US' => 'unavailable',
                'en_GB' => 'unavailable',
                'de_DE' => 'nicht verfügbar'
            )
        );
        $locales        = array('de_DE', 'en_GB', 'en_US');
        $fallbackLocale = false;

        if (!in_array(Translatable::get_current_locale(), $locales)) {
            $locales[]      = Translatable::get_current_locale();
            $fallbackLocale = Translatable::get_current_locale();
        }

        if ($fallbackLocale !== false) {
            $defaults['available'][$fallbackLocale]     = $defaults['available']['en_US'];
            $defaults['not-available'][$fallbackLocale] = $defaults['not-available']['en_US'];
        }
        $this->createDefaultTranslatableDataObject($defaults, AvailabilityStatus::class);
    }
    
    /**
     * Creates a translatable DataObject by the given entries and for the current 
     * locale.
     *
     * @param array  $translatableDataObjectEntries         Entries to create
     * @param string $translatableDataObjectName            Name of DataObject to create entries for
     * @param string $translatableDataObjectTranslationName Name of DataObjectTranslation to create entries for (if not default)
     * @param string $translatableDataObjectRelationName    Name of relation name DataObjectTranslation -> DataObject (if not default)
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.09.2013
     */
    public function createDefaultTranslatableDataObject($translatableDataObjectEntries, $translatableDataObjectName, $translatableDataObjectTranslationName = '', $translatableDataObjectRelationName = '') {
        $translatableDataObjectTable  = Tools::get_table_name($translatableDataObjectName);
        if (empty($translatableDataObjectTranslationName)) {
            $translatableDataObjectTranslationName = $translatableDataObjectName . 'Translation';
        }
        if (empty($translatableDataObjectRelationName)) {
            $reflection = new ReflectionClass($translatableDataObjectName);
            $translatableDataObjectRelationName = $reflection->getShortName() . 'ID';
        }
        $translationLocale = $this->getTranslationLocale();
        foreach ($translatableDataObjectEntries as $code => $languages) {
            $obj = DataObject::get_one(
                $translatableDataObjectName,
                sprintf(
                        "\"Code\" = '%s'",
                        $code
                ),
                true,
                "ID"
            );
            if (!$obj) {
                $obj = new $translatableDataObjectName();
                $obj->Code = $code;
                $obj->write();
            }
            if (!is_null($translationLocale) &&
                !array_key_exists($translationLocale, $languages) &&
                array_key_exists('en_US', $languages)) {
                $languages[$translationLocale] = $languages['en_US'];
            }
            foreach ($languages as $locale => $title) {
                if (empty($locale)) {
                    continue;
                }
                $objTranslation = $translatableDataObjectTranslationName::get()->filter(array('Locale'=> $locale, $translatableDataObjectRelationName => $obj->ID))->first();
                if (!$objTranslation) {
                    $objTranslation = new $translatableDataObjectTranslationName();
                    $objTranslation->Locale                                = $locale;
                    $objTranslation->{$translatableDataObjectRelationName} = $obj->ID;
                    $objTranslation->Title                                 = $title;
                    $objTranslation->write();
                } elseif (empty($objTranslation->Title)) {
                    $objTranslation->Title                                 = $title;
                    $objTranslation->write();
                }
            }
        }
    }
    
    /**
     * Creates the default NumberRanges if not exists
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function createDefaultNumberRanges() {
        // create number ranges
        $orderNumbers = NumberRange::get()->filter('Identifier', 'OrderNumber')->first();
        if (!$orderNumbers) {
            $orderNumbers = new NumberRange();
            $orderNumbers->Identifier = 'OrderNumber';
            $orderNumbers->Title = _t(NumberRange::class . '.ORDERNUMBER', 'Ordernumber');
            $orderNumbers->write();
        }
        $customerNumbers = NumberRange::get()->filter('Identifier', 'CustomerNumber')->first();
        if (!$customerNumbers) {
            $customerNumbers = new NumberRange();
            $customerNumbers->Identifier = 'CustomerNumber';
            $customerNumbers->Title = _t(NumberRange::class . '.CUSTOMERNUMBER', 'Customernumber');
            $customerNumbers->write();
        }
    }
    
    /**
     * Creates the default SiteTree if not exists
     * 
     * @return \SilverCart\Model\Pages\FrontPage
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function createDefaultSiteTree() {
        $rootPage = Page::get()->filter('IdentifierCode', 'SilvercartCartPage')->first();
        if (!$rootPage) {
            //create a silvercart front page (parent of all other SilverCart pages
            $rootPage                   = new FrontPage();
            $rootPage->IdentifierCode   = "SilvercartFrontPage";
            $rootPage->Title            = 'SilverCart';
            $rootPage->MenuTitle        = "SilverCart";
            if (SiteTree::get_by_link('home')) {
                $rootPage->URLSegment   = 'webshop';
            } else {
                $rootPage->URLSegment   = 'home';
            }
            $rootPage->ShowInMenus      = false;
            $rootPage->ShowInSearch     = false;
            $rootPage->CanViewType      = "Anyone";
            $rootPage->Content          = _t(FrontPage::class . '.DEFAULT_CONTENT', '<h2>Welcome to <strong>SilverCart</strong> Webshop!</h2>');
            $rootPage->write();
            $rootPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

            //create a silvercart product group holder as a child af the silvercart root
            $productGroupHolder                     = new ProductGroupHolder();
            $productGroupHolder->Title              = _t(ProductGroupHolder::class . '.DEFAULT_TITLE',      'product groups');
            $productGroupHolder->URLSegment         = _t(ProductGroupHolder::class . '.DEFAULT_URLSEGMENT', 'productgroups');
            $productGroupHolder->ParentID           = $rootPage->ID;
            $productGroupHolder->IdentifierCode     = "SilvercartProductGroupHolder";
            $productGroupHolder->InheritFromParent  = false;
            $productGroupHolder->UseAsRootForMainNavigation = true;
            $productGroupHolder->write();
            $productGroupHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

            //create a cart page
            $cartPage                       = new CartPage();
            $cartPage->Title                = _t(CartPage::class . '.DEFAULT_TITLE', 'Cart');
            $cartPage->URLSegment           = _t(CartPage::class . '.DEFAULT_URLSEGMENT', 'cart');
            $cartPage->ShowInMenus          = true;
            $cartPage->ShowInSearch         = false;
            $cartPage->IdentifierCode       = "SilvercartCartPage";
            $cartPage->ParentID             = $rootPage->ID;
            $cartPage->InheritFromParent    = false;
            $cartPage->write();
            $cartPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

            //create a silvercart checkout step (checkout) as achild of the silvercart root
            $checkoutStep                       = new CheckoutStep();
            $checkoutStep->Title                = _t(CheckoutStep::class . '.DEFAULT_TITLE', 'Checkout');
            $checkoutStep->URLSegment           = _t(CheckoutStep::class . '.DEFAULT_URLSEGMENT', 'checkout');
            $checkoutStep->ShowInMenus          = true;
            $checkoutStep->ShowInSearch         = true;
            $checkoutStep->basename             = 'CheckoutFormStep';
            $checkoutStep->showCancelLink       = true;
            $checkoutStep->cancelPageID         = $cartPage->ID;
            $checkoutStep->ParentID             = $rootPage->ID;
            $checkoutStep->IdentifierCode       = "SilvercartCheckoutStep";
            $checkoutStep->InheritFromParent    = false;
            $checkoutStep->write();
            $checkoutStep->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

            //create a payment notification page as a child of the silvercart root
            $paymentNotification                    = new PaymentNotification();
            $paymentNotification->Title             = _t(PaymentNotification::class . '.DEFAULT_TITLE', 'payment notification');
            $paymentNotification->URLSegment        = _t(PaymentNotification::class . '.DEFAULT_URLSEGMENT', 'payment-notification');
            $paymentNotification->ShowInMenus       = 0;
            $paymentNotification->ShowInSearch      = 0;
            $paymentNotification->ParentID          = $rootPage->ID;
            $paymentNotification->IdentifierCode    = "SilvercartPaymentNotification";
            $paymentNotification->InheritFromParent = false;
            $paymentNotification->write();
            $paymentNotification->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
            DB::alteration_message('PaymentNotification Page created', 'created');

            //create a silvercart registration page as a child of silvercart root
            $registrationPage                       = new RegistrationPage();
            $registrationPage->Title                = _t(RegistrationPage::class . '.DEFAULT_TITLE', 'registration page');
            $registrationPage->URLSegment           = _t(RegistrationPage::class . '.DEFAULT_URLSEGMENT', 'registration');
            $registrationPage->ShowInMenus          = false;
            $registrationPage->ShowInSearch         = true;
            $registrationPage->ParentID             = $rootPage->ID;
            $registrationPage->IdentifierCode       = "SilvercartRegistrationPage";
            $registrationPage->InheritFromParent    = false;
            $registrationPage->WelcomeContent       = _t(RegistrationPage::class . '.DEFAULT_WELCOME_CONTENT', ' ');
            $registrationPage->write();
            $registrationPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

            //create a silvercart search results page as a child of the silvercart root
            $searchResultsPage                      = new SearchResultsPage();
            $searchResultsPage->Title               = _t(SearchResultsPage::class . '.DEFAULT_TITLE', 'search results');
            $searchResultsPage->URLSegment          = _t(SearchResultsPage::class . '.DEFAULT_URLSEGMENT', 'search-results');
            $searchResultsPage->ShowInMenus         = false;
            $searchResultsPage->ShowInSearch        = false;
            $searchResultsPage->ParentID            = $rootPage->ID;
            $searchResultsPage->IdentifierCode      = "SilvercartSearchResultsPage";
            $searchResultsPage->InheritFromParent   = false;
            $searchResultsPage->write();
            $searchResultsPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
            
            $this->createDefaultSiteTreeCMSSection($rootPage);
        }
        return $rootPage;
    }
    
    /**
     * Creates the default SiteTree CMS section if not exists
     * 
     * @param \SilverCart\Model\Pages\FrontPage $rootPage SiteTree root page
     * 
     * @return \SilverCart\Model\Pages\FrontPage
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.03.2016
     */
    public function createDefaultSiteTreeCMSSection($rootPage) {

        $legalNavigationHolder                    = new MetaNavigationHolder();
        $legalNavigationHolder->Title             = _t(MetaNavigationHolder::class . '.DEFAULT_TITLE_LEGAL', 'Legal');
        $legalNavigationHolder->URLSegment        = _t(MetaNavigationHolder::class . '.DEFAULT_URLSEGMENT_LEGAL', 'legal');
        $legalNavigationHolder->ShowInMenus       = 0;
        $legalNavigationHolder->IdentifierCode    = "SilvercartMetaNavigationHolderLegal";
        $legalNavigationHolder->ParentID          = $rootPage->ID;
        $legalNavigationHolder->InheritFromParent = false;
        $legalNavigationHolder->write();
        $legalNavigationHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $serviceNavigationHolder                    = new MetaNavigationHolder();
        $serviceNavigationHolder->Title             = _t(MetaNavigationHolder::class . '.DEFAULT_TITLE_SERVICE', 'Service');
        $serviceNavigationHolder->URLSegment        = _t(MetaNavigationHolder::class . '.DEFAULT_URLSEGMENT_SERVICE', 'service');
        $serviceNavigationHolder->ShowInMenus       = 0;
        $serviceNavigationHolder->IdentifierCode    = "SilvercartMetaNavigationHolderService";
        $serviceNavigationHolder->ParentID          = $rootPage->ID;
        $serviceNavigationHolder->InheritFromParent = false;
        $serviceNavigationHolder->write();
        $serviceNavigationHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $aboutNavigationHolder                    = new MetaNavigationHolder();
        $aboutNavigationHolder->Title             = _t(MetaNavigationHolder::class . '.DEFAULT_TITLE_ABOUT', 'About us');
        $aboutNavigationHolder->URLSegment        = _t(MetaNavigationHolder::class . '.DEFAULT_URLSEGMENT_ABOUT', 'about-us');
        $aboutNavigationHolder->ShowInMenus       = 0;
        $aboutNavigationHolder->IdentifierCode    = "SilvercartMetaNavigationHolderAbout";
        $aboutNavigationHolder->ParentID          = $rootPage->ID;
        $aboutNavigationHolder->InheritFromParent = false;
        $aboutNavigationHolder->write();
        $aboutNavigationHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $shopNavigationHolder                    = new MetaNavigationHolder();
        $shopNavigationHolder->Title             = _t(MetaNavigationHolder::class . '.DEFAULT_TITLE_SHOP', 'Shopsystem');
        $shopNavigationHolder->URLSegment        = _t(MetaNavigationHolder::class . '.DEFAULT_URLSEGMENT_SHOP', 'shop-system');
        $shopNavigationHolder->ShowInMenus       = 0;
        $shopNavigationHolder->IdentifierCode    = "SilvercartMetaNavigationHolderShop";
        $shopNavigationHolder->ParentID          = $rootPage->ID;
        $shopNavigationHolder->InheritFromParent = false;
        $shopNavigationHolder->write();
        $shopNavigationHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        // Sub pages of legal node
        $termsOfServicePage                 = new MetaNavigationPage();
        $termsOfServicePage->Title          = _t(MetaNavigationPage::class . '.DEFAULT_TITLE_TERMS', 'terms of service');
        $termsOfServicePage->URLSegment     = _t(MetaNavigationPage::class . '.DEFAULT_URLSEGMENT_TERMS', 'terms-of-service');
        $termsOfServicePage->ShowInMenus    = 1;
        $termsOfServicePage->ParentID       = $legalNavigationHolder->ID;
        $termsOfServicePage->IdentifierCode = "TermsOfServicePage";
        $termsOfServicePage->write();
        $termsOfServicePage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $revocationInstructionPage                  = new RedirectorPage();
        $revocationInstructionPage->RedirectionType = 'Internal';
        $revocationInstructionPage->LinkToID        = $termsOfServicePage->ID;
        $revocationInstructionPage->Title           = _t(MetaNavigationPage::class . '.DEFAULT_TITLE_REVOCATION', 'revocation instruction');
        $revocationInstructionPage->URLSegment      = _t(MetaNavigationPage::class . '.DEFAULT_URLSEGMENT_REVOCATION', 'revocation-instruction');
        $revocationInstructionPage->ShowInMenus     = 1;
        $revocationInstructionPage->ParentID        = $legalNavigationHolder->ID;
        $revocationInstructionPage->IdentifierCode  = "SilvercartRevocationInstructionPage";
        $revocationInstructionPage->write();
        $revocationInstructionPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $revocationPage                 = new RevocationFormPage();
        $revocationPage->Title          = _t(RevocationFormPage::class . '.DEFAULT_TITLE', 'Revocation');
        $revocationPage->URLSegment     = _t(RevocationFormPage::class . '.DEFAULT_URLSEGMENT', 'Revocation');
        $revocationPage->ShowInMenus    = 1;
        $revocationPage->IdentifierCode = "SilvercartRevocationFormPage";
        $revocationPage->ParentID       = $legalNavigationHolder->ID;
        $revocationPage->write();
        $revocationPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $dataPrivacyStatementPage                 = new MetaNavigationPage();
        $dataPrivacyStatementPage->Title          = _t(MetaNavigationPage::class . '.DEFAULT_TITLE_PRIVACY', 'Data privacy statement');
        $dataPrivacyStatementPage->URLSegment     = _t(MetaNavigationPage::class . '.DEFAULT_URLSEGMENT_PRIVACY', 'data-privacy-statement');
        $dataPrivacyStatementPage->ShowInMenus    = 1;
        $dataPrivacyStatementPage->IdentifierCode = "DataPrivacyStatementPage";
        $dataPrivacyStatementPage->ParentID       = $legalNavigationHolder->ID;
        $dataPrivacyStatementPage->write();
        $dataPrivacyStatementPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        // Sub pages of service node
        $this->createDefaultSiteTreeMyAccountSection($serviceNavigationHolder);
        
        $paymentMethodsPage                 = new PaymentMethodsPage();
        $paymentMethodsPage->Title          = _t(PaymentMethodsPage::class . '.DEFAULT_TITLE', 'Payment methods');
        $paymentMethodsPage->URLSegment     = _t(PaymentMethodsPage::class . '.DEFAULT_URLSEGMENT', 'payment-methods');
        $paymentMethodsPage->ShowInMenus    = 1;
        $paymentMethodsPage->ParentID       = $serviceNavigationHolder->ID;
        $paymentMethodsPage->IdentifierCode = "SilvercartPaymentMethodsPage";
        $paymentMethodsPage->write();
        $paymentMethodsPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $shippingFeesPage                 = new ShippingFeesPage();
        $shippingFeesPage->Title          = _t(ShippingFeesPage::class . '.DEFAULT_TITLE', 'shipping fees');
        $shippingFeesPage->URLSegment     = _t(ShippingFeesPage::class . '.DEFAULT_URLSEGMENT', 'shipping-fees');
        $shippingFeesPage->ShowInMenus    = 1;
        $shippingFeesPage->ParentID       = $serviceNavigationHolder->ID;
        $shippingFeesPage->IdentifierCode = "SilvercartShippingFeesPage";
        $shippingFeesPage->write();
        $shippingFeesPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $newsletterPage                 = new NewsletterPage();
        $newsletterPage->Title          = _t(NewsletterPage::class . '.DEFAULT_TITLE', 'Newsletter');
        $newsletterPage->URLSegment     = _t(NewsletterPage::class . '.DEFAULT_URLSEGMENT', 'newsletter');
        $newsletterPage->ShowInMenus    = true;
        $newsletterPage->ShowInSearch   = true;
        $newsletterPage->ParentID       = $serviceNavigationHolder->ID;
        $newsletterPage->IdentifierCode = "SilvercartNewsletterPage";
        $newsletterPage->OptInPageTitle             = NewsletterPage::singleton()->fieldLabel('DefaultOptInPageTitle');
        $newsletterPage->ConfirmationFailureMessage = NewsletterPage::singleton()->fieldLabel('DefaultConfirmationFailureMessage');
        $newsletterPage->ConfirmationSuccessMessage = NewsletterPage::singleton()->fieldLabel('DefaultConfirmationSuccessMessage');
        $newsletterPage->AlreadyConfirmedMessage    = NewsletterPage::singleton()->fieldLabel('DefaultAlreadyConfirmedMessage');
        $newsletterPage->write();
        $newsletterPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);


        // Sub pages of about node
        $imprintPage                 = new MetaNavigationPage();
        $imprintPage->Title          = _t(MetaNavigationPage::class . '.DEFAULT_TITLE_IMPRINT', 'imprint');
        $imprintPage->URLSegment     = _t(MetaNavigationPage::class . '.DEFAULT_URLSEGMENT_IMPRINT', 'imprint');
        $imprintPage->ShowInMenus    = 1;
        $imprintPage->ParentID       = $aboutNavigationHolder->ID;
        $imprintPage->IdentifierCode = "ImprintPage";
        $imprintPage->write();
        $imprintPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        
        $contactPage                  = new ContactFormPage();
        $contactPage->Title           = _t(ContactFormPage::class . '.DEFAULT_TITLE', 'contact');
        $contactPage->ResponseContent = _t(ContactFormPage::class . '.DEFAULT_RESPONSE_CONTENT', 'Many thanks for Your message. Your request will be answered as soon as possible.');
        $contactPage->URLSegment      = _t(ContactFormPage::class . '.DEFAULT_URLSEGMENT', 'contact');
        $contactPage->ShowInMenus     = 1;
        $contactPage->IdentifierCode  = "SilvercartContactFormPage";
        $contactPage->ParentID        = $aboutNavigationHolder->ID;
        $contactPage->write();
        $contactPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        // Sub pages of shop node
        $silvercartDePage                  = new RedirectorPage();
        $silvercartDePage->RedirectionType = 'External';
        $silvercartDePage->ExternalURL     = 'http://www.silvercart.de';
        $silvercartDePage->Title           = 'silvercart.de';
        $silvercartDePage->URLSegment      = 'silvercart-de';
        $silvercartDePage->ShowInMenus     = 1;
        $silvercartDePage->ParentID        = $shopNavigationHolder->ID;
        $silvercartDePage->write();
        $silvercartDePage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        
        $silvercartOrgPage                  = new RedirectorPage();
        $silvercartOrgPage->RedirectionType = 'External';
        $silvercartOrgPage->ExternalURL     = 'http://www.silvercart.org';
        $silvercartOrgPage->Title           = 'silvercart.org';
        $silvercartOrgPage->URLSegment      = 'silvercart-org';
        $silvercartOrgPage->ShowInMenus     = 1;
        $silvercartOrgPage->ParentID        = $shopNavigationHolder->ID;
        $silvercartOrgPage->write();
        $silvercartOrgPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
    }
    
    /**
     * Creates the "My Account" section of SilverCart.
     * 
     * @param SiteTree $parentPage Parent page of "My Account" section
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.03.2016
     */
    public function createDefaultSiteTreeMyAccountSection(SiteTree $parentPage) {
        $myAccountHolder                    = new MyAccountHolder();
        $myAccountHolder->Title             = _t(MyAccountHolder::class . '.DEFAULT_TITLE', 'my account');
        $myAccountHolder->URLSegment        = _t(MyAccountHolder::class . '.DEFAULT_URLSEGMENT', 'my-account');
        $myAccountHolder->ShowInMenus       = true;
        $myAccountHolder->ShowInSearch      = false;
        $myAccountHolder->ParentID          = $parentPage->ID;
        $myAccountHolder->IdentifierCode    = "SilvercartMyAccountHolder";
        $myAccountHolder->InheritFromParent = false;
        $myAccountHolder->write();
        $myAccountHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        //create a silvercart data page as a child of silvercart my account holder
        $dataPage                   = new CustomerDataPage();
        $dataPage->Title            = _t(CustomerDataPage::class . '.DEFAULT_TITLE', 'my data');
        $dataPage->URLSegment       = _t(CustomerDataPage::class . '.DEFAULT_URLSEGMENT', 'my-data');
        $dataPage->ShowInMenus      = true;
        $dataPage->ShowInSearch     = false;
        $dataPage->CanViewType      = "Inherit";
        $dataPage->ParentID         = $myAccountHolder->ID;
        $dataPage->IdentifierCode   = "SilvercartCustomerDataPage";
        $dataPage->write();
        $dataPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        //create a silvercart order holder as a child of silvercart my account holder
        $orderHolder                    = new OrderHolder();
        $orderHolder->Title             = _t(OrderHolder::class . '.DEFAULT_TITLE', 'my orders');
        $orderHolder->URLSegment        = _t(OrderHolder::class . '.DEFAULT_URLSEGMENT', 'my-orders');
        $orderHolder->ShowInMenus       = true;
        $orderHolder->ShowInSearch      = false;
        $orderHolder->CanViewType       = "Inherit";
        $orderHolder->ParentID          = $myAccountHolder->ID;
        $orderHolder->IdentifierCode    = "SilvercartOrderHolder";
        $orderHolder->write();
        $orderHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        //create an order detail page as a child of the order holder
        $orderDetailPage                    = new OrderDetailPage();
        $orderDetailPage->Title             = _t(OrderDetailPage::class . '.DEFAULT_TITLE', 'order details');
        $orderDetailPage->URLSegment        = _t(OrderDetailPage::class . '.DEFAULT_URLSEGMENT', 'order-details');
        $orderDetailPage->ShowInMenus       = false;
        $orderDetailPage->ShowInSearch      = false;
        $orderDetailPage->CanViewType       = "Inherit";
        $orderDetailPage->ParentID          = $orderHolder->ID;
        $orderDetailPage->IdentifierCode    = "SilvercartOrderDetailPage";
        $orderDetailPage->write();
        $orderDetailPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        //create a silvercart address holder as a child of silvercart my account holder
        $addressHolder                  = new AddressHolder();
        $addressHolder->Title           = _t(AddressHolder::class . '.DEFAULT_TITLE', 'address overview');
        $addressHolder->URLSegment      = _t(AddressHolder::class . '.DEFAULT_URLSEGMENT', 'address-overview');
        $addressHolder->ShowInMenus     = true;
        $addressHolder->ShowInSearch    = false;
        $addressHolder->CanViewType     = "Inherit";
        $addressHolder->ParentID        = $myAccountHolder->ID;
        $addressHolder->IdentifierCode  = "SilvercartAddressHolder";
        $addressHolder->write();
        $addressHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
    }
    
    /**
     * Re-renders the ErrorPage templates
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.06.2012
     */
    public function rerenderErrorPages() {
        $errorPages = ErrorPage::get();
        if ($errorPages->exists()) {
            Config::$forceLoadingOfDefaultLayout = true;
            foreach ($errorPages as $errorPage) {
                $errorPage->doPublish();
            }
            Config::$forceLoadingOfDefaultLayout = false;
        }
    }
    
    /**
     * Increases the SilverCart version if necessary.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2013
     */
    public function increaseSilvercartVersion() {
        $defaults       = SiteConfig::config()->get('defaults');
        $minorVersion   = $defaults['SilvercartMinorVersion'];
        $config         = Config::getConfig();
        if ($config->SilvercartMinorVersion != $minorVersion) {
            $config->SilvercartMinorVersion = $minorVersion;
            $config->write();
        }
    }
    
    /**
     * Creates the default records.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.10.2017
     */
    public static function require_default_records() {
        self::singleton()->requireDefaultRecords();
    }

    /**
     * create default records.
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.02.2013
     */
    public function requireDefaultRecords() {
        if (!Country::get()->exists()) {
            require_once(__DIR__ . '/RequireDefaultCountries.php');
            list($lang,$iso) = explode('_', i18n::get_locale());
            $country = Country::get()->filter('ISO2', $iso)->first();
            if ($country instanceof Country &&
                $country->exists()) {
                $country->Active = true;
                $country->write();
            }
        }
        // create groups
        $this->createDefaultGroups();
        // create config
        $this->createDefaultConfig();
        // create order status
        $this->createDefaultOrderStatus();
        // create availability status
        $this->createDefaultAvailabilityStatus();
        // create number ranges
        $this->createDefaultNumberRanges();
        // and now the whole site tree
        $rootPage = $this->createDefaultSiteTree();
        // rewrite error page templates
        $this->rerenderErrorPages();
        // increase SilverCart version if necessary
        $this->increaseSilvercartVersion();

        $this->extend('updateDefaultRecords', $rootPage);

        self::createTestConfiguration();
        self::createTestData();
        
        $defaultTax = Tax::get()->filter('isDefault', 1)->first();
        if (!($defaultTax instanceof Tax) ||
            !$defaultTax->exists()) {
            $defaultTax = Tax::get()->first();
            if ($defaultTax instanceof Tax &&
                $defaultTax->exists()) {
                $defaultTax->isDefault = true;
                $defaultTax->write();
            }
            
        }
    }
    
    /**
     * Will create a translation of all pages of the SiteTree for the defined
     * translationLocale
     *
     * @param int $parentID ID of the parent to get pages for
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.09.2013
     */
    public function translateSiteTree($parentID = 0) {
        $translatableFieldTypes = array(
            'varchar',
            'htmltext',
            'text',
        );
        
        $translationLocale = $this->getTranslationLocale();
        $pages = SiteTree::get()->filter("ParentID", $parentID);
        if ($pages->exists()) {
            foreach ($pages as $page) {
                if (!is_null($translationLocale) &&
                    !$page->getTranslation($translationLocale)) {
                    Versioned::set_reading_mode('Stage.Stage');
                    $translation = $page->createTranslation($translationLocale);
                    $translationDbFields = (array)\SilverStripe\Core\Config\Config::inst()->get(get_class($translation), 'db');
                    foreach ($translationDbFields as $name => $type) {
                        $isTranslatable = false;
                        foreach ($translatableFieldTypes as $translatableFieldType) {
                            if (strpos(strtolower($type), $translatableFieldType) === 0) {
                                $isTranslatable = true;
                                break;
                            }
                        }
                        if ($isTranslatable) {
                            i18n::set_locale($translationLocale);
                            $translation->{$name} = _t($translation->ClassName . '.DEFAULT_' . strtoupper($name), _t($translation->IdentifierCode . '.DEFAULT_' . strtoupper($name), $translation->{$name}));
                        }
                    }
                    $translation->write();
                    
                    /*
                     * transfer the existing widget sets to the translation
                     */
                    if ($page->WidgetSetSidebar()) {
                        foreach ($page->WidgetSetSidebar() as $widgetSetSidebar) {
                            $translation->WidgetSetSidebar()->add($widgetSetSidebar);
                        }
                    }
                    if ($page->WidgetSetContent()) {
                       foreach ($page->WidgetSetContent() as $widgetSetContent) {
                            $translation->WidgetSetContent()->add($widgetSetContent);
                        } 
                    }
                    
                    
                }
                $this->translateSiteTree($page->ID);
            }
        }
    }
    
    /**
     * Will publish all pages of the SiteTree for the defined translationLocale
     *
     * @param int $parentID ID of the parent to get pages for
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.05.2012
     */
    public function publishSiteTree($parentID = 0) {
        $translationLocale = $this->getTranslationLocale();
        Translatable::disable_locale_filter();
        Versioned::set_reading_mode('Stage.Stage');
        $pages = SiteTree::get()->filter(array("ParentID" => $parentID, "Locale" => $translationLocale));
        if ($pages->exists()) {
            foreach ($pages as $page) {
                $page->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
                $this->publishSiteTree($page->ID);
            }
        }
    }
    
    /**
     * Static accessor to trigger SiteTree translation
     *
     * @param string $locale Locale to translate SiteTree for
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.05.2012
     */
    public static function doTranslateSiteTree($locale) {
        $obj = new RequireDefaultRecords();
        $obj->requireDefaultTranslations($locale);
    }
    
    /**
     * Static accessor to trigger SiteTree publishing
     *
     * @param string $locale Locale to publish SiteTree for
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.05.2012
     */
    public static function doPublishSiteTree($locale) {
        $obj = new RequireDefaultRecords();
        $obj->setTranslationLocale($locale);
        $obj->publishSiteTree();
    }

    /**
     * create default records dependent on the given locale.
     * 
     * @param string $locale The locale to get records for
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function requireDefaultTranslations($locale) {
        $originalLocale = i18n::get_locale();
        i18n::set_locale($locale);
        $this->setTranslationLocale($locale);
        // create order status
        $this->createDefaultOrderStatus();
        // create availability status
        $this->createDefaultAvailabilityStatus();
        // and now the whole site tree
        $this->translateSiteTree();
        i18n::set_locale($originalLocale);
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
        self::$enableTestData = true;
    }

    /**
     * determine weather test data is enabled or not
     *
     * @return bool is test data enabled?
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.3.2011
     */
    public static function isEnabledTestData() {
        if (self::$enableTestData === true) {
            return true;
        } else {
            return false;
        }
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
        self::$enableTestData = false;
    }

    /**
     * creates test data on /dev/build or by adding test data in ModelAdmin.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.07.2011
     */
    public static function createTestData() {
        if (self::$enableTestData === true) {
            if (SiteTree::get_by_link(_t(RequireDefaultRecords::class . '.PRODUCTGROUPPAYMENT_URLSEGMENT', 'payment-modules'))) {
                // test data already created
                return false;
            }
            self::createTestTaxRates();
            // get ProductGroupHolder and tax rate
            $silvercartProductGroupHolder = ProductGroupHolder::get()->first();
            $taxRateID = Tax::get()->filter('Rate', '19')->first()->ID;

            //create a manufacturer
            $manufacturer = new Manufacturer();
            $manufacturer->Title = 'pixeltricks GmbH';
            $manufacturer->URL = 'http://www.pixeltricks.de/';
            $manufacturer->write();
            
            //create product groups
            $productGroupPayment = new ProductGroupPage();
            $productGroupPayment->Title = _t(RequireDefaultRecords::class . '.PRODUCTGROUPPAYMENT_TITLE', 'Payment Modules');
            $productGroupPayment->URLSegment = _t(RequireDefaultRecords::class . '.PRODUCTGROUPPAYMENT_URLSEGMENT', 'payment-modules');
            $productGroupPayment->Content = _t(RequireDefaultRecords::class . '.PRODUCTGROUP_CONTENT', '<div class="silvercart-message highlighted info32"><p><strong>Please note:</strong></p><p>These modules are available for free. Prices are for demo purposes only.</p></div>');
            $productGroupPayment->IdentifierCode = 'SilvercartProductGroupPayment';
            $productGroupPayment->ParentID = $silvercartProductGroupHolder->ID;
            $productGroupPayment->ShowInMenus = true;
            $productGroupPayment->ShowInSearch = true;
            $productGroupPayment->Sort = 1;
            $productGroupPayment->write();
            $productGroupPayment->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
            
            $productGroupMarketing = new ProductGroupPage();
            $productGroupMarketing->Title = _t(RequireDefaultRecords::class . '.PRODUCTGROUPMARKETING_TITLE', 'Marketing Modules');
            $productGroupMarketing->URLSegment = _t(RequireDefaultRecords::class . '.PRODUCTGROUPMARKETING_URLSEGMENT', 'marketing-modules');
            $productGroupMarketing->Content = _t(RequireDefaultRecords::class . '.PRODUCTGROUP_CONTENT', '<div class="silvercart-message highlighted info32"><p><strong>Please note:</strong></p><p>These modules are available for free. Prices are for demo purposes only.</p></div>');
            $productGroupMarketing->IdentifierCode = 'SilvercartproductGroupMarketing';
            $productGroupMarketing->ParentID = $silvercartProductGroupHolder->ID;
            $productGroupMarketing->ShowInMenus = true;
            $productGroupMarketing->ShowInSearch = true;
            $productGroupMarketing->Sort = 2;
            $productGroupMarketing->write();
            $productGroupMarketing->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
            
            $productGroupOthers = new ProductGroupPage();
            $productGroupOthers->Title = _t(RequireDefaultRecords::class . '.PRODUCTGROUPOTHERS_TITLE', 'Other Modules');
            $productGroupOthers->URLSegment = _t(RequireDefaultRecords::class . '.PRODUCTGROUPOTHERS_URLSEGMENT', 'other-modules');
            $productGroupOthers->Content = _t(RequireDefaultRecords::class . '.PRODUCTGROUP_CONTENT', '<div class="silvercart-message highlighted info32"><p><strong>Please note:</strong></p><p>These modules are available for free. Prices are for demo purposes only.</p></div>');
            $productGroupOthers->IdentifierCode = 'SilvercartproductGroupOthers';
            $productGroupOthers->ParentID = $silvercartProductGroupHolder->ID;
            $productGroupOthers->ShowInMenus = true;
            $productGroupOthers->ShowInSearch = true;
            $productGroupOthers->Sort = 3;
            $productGroupOthers->write();
            $productGroupOthers->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
            
            // Define products
            $products = array(
                array(
                    'en_US'                     => array(
                        'Title'            => 'Paypal',
                        'ShortDescription' => 'The world' . "'" . 's most loved way to pay and get paid.',
                        'LongDescription'  => 'PayPal works behind the scenes to help protect you and your customers. Your customers will love the speed of PayPal streamlined checkout experience. And you will love the sales boost PayPal can deliver. PayPal is ideal for selling overseas. You can accept payments in 22 currencies from 190 countries and markets worldwide. Source: www.paypal.com',
                        'MetaDescription'  => 'The world' . "'" . 's most loved way to pay and get paid.',
                        'MetaKeywords'     => 'SilverCart, modules, PayPal, payment',
                        'MetaTitle'        => 'Paypal'
                    ),
                    'en_GB' => array(
                        'Title'            => 'Paypal',
                        'ShortDescription' => 'The world' . "'" . 's most loved way to pay and get paid.',
                        'LongDescription'  => 'PayPal works behind the scenes to help protect you and your customers. Your customers will love the speed of PayPal streamlined checkout experience. And you will love the sales boost PayPal can deliver. PayPal is ideal for selling overseas. You can accept payments in 22 currencies from 190 countries and markets worldwide. Source: www.paypal.com',
                        'MetaDescription'  => 'The world' . "'" . 's most loved way to pay and get paid.',
                        'MetaKeywords'     => 'SilverCart, modules, PayPal, payment',
                        'MetaTitle'        => 'Paypal'
                    ),
                    'de_DE' => array(
                        'Title'            => 'Paypal',
                        'ShortDescription' => 'PayPal ist sicherererer. Für Daten, für Einkäufe - Für alles',
                        'LongDescription'  => 'PayPal für Ihren Shop Sie haben einen Online-Shop und fragen sich, warum Sie PayPal anbieten sollen? Ganz einfach: Ihre Kunden bezahlen mit nur zwei Klicks. Sie schließen den Kauf zufrieden ab, kommen gerne wieder - und Sie steigern Ihren Umsatz! Das kann PayPal für Sie tun – und mehr!',
                        'MetaDescription'  => 'PayPal ist sicherererer. Für Daten, für Einkäufe - Für alles',
                        'MetaKeywords'     => 'SilverCart, Modul, PayPal, Zahlart',
                        'MetaTitle'        => 'Paypal'
                    ),
                    'PriceGrossAmount'          => 9.99,
                    'PriceGrossCurrency'        => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PriceNetAmount'            => 9.99 / 119 * 100,
                    'PriceNetCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'MSRPriceAmount'            => 9.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PurchasePriceAmount'       => 9.99,
                    'PurchasePriceCurrency'     => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'Weight'                    => 250,
                    'StockQuantity'             => 5,
                    'ProductNumberShop'         => '10001',
                    'ProductNumberManufacturer' => 'SC_Mod_100',
                    'ProductGroupID'            => $productGroupPayment->ID,
                    'productImage'              => 'logopaypal.jpg',
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'iPayment',
                        'ShortDescription' => 'iPayment is one of the largest providers of credit and debit card-based payment processing services in the country, processing more than $30 billion in credit and debit card volume annually.',
                        'LongDescription'  => '<p>Receive best in class service no matter what size your business is, with iPayment. We’re committed to making your business more successful by delivering credit and debit card-based payment processing services that are customized to suit your needs.</p><ul><li>Major credit cards: MasterCard®, Visa®, American Express®, Discover® and JCB®</li><li>PIN-secured and signature debit cards</li><li>Gift and loyalty cards</li><li>Petroleum services</li><li>Paper and electronic check services</li><li>Cash advance funding program</li></ul><p><small>Source: www.ipaymentinc.com/</small></p>',
                        'MetaDescription'  => 'iPayment is one of the largest providers of credit and debit card-based payment processing services in the country, processing more than $30 billion in credit and debit card volume annually.',
                        'MetaKeywords'     => 'SilverCart, modules, iPayment, payment',
                        'MetaTitle'        => 'iPayment'
                    ),
                    'en_GB' => array(
                        'Title'            => 'iPayment',
                        'ShortDescription' => 'iPayment is one of the largest providers of credit and debit card-based payment processing services in the country, processing more than $30 billion in credit and debit card volume annually.',
                        'LongDescription'  => '<p>Receive best in class service no matter what size your business is, with iPayment. We’re committed to making your business more successful by delivering credit and debit card-based payment processing services that are customized to suit your needs.</p><ul><li>Major credit cards: MasterCard®, Visa®, American Express®, Discover® and JCB®</li><li>PIN-secured and signature debit cards</li><li>Gift and loyalty cards</li><li>Petroleum services</li><li>Paper and electronic check services</li><li>Cash advance funding program</li></ul><p><small>Source: www.ipaymentinc.com/</small></p>',
                        'MetaDescription'  => 'iPayment is one of the largest providers of credit and debit card-based payment processing services in the country, processing more than $30 billion in credit and debit card volume annually.',
                        'MetaKeywords'     => 'SilverCart, modules, iPayment, payment',
                        'MetaTitle'        => 'iPayment'
                    ),
                    'de_DE' => array(
                        'Title'            => 'iPayment',
                        'ShortDescription' => 'iPayment unterstützt Ihren Geschäftserfolg im Internet, indem es Ihren Kunden die sichere Bezahlung per Kreditkarte, internetbasiertem elektronischen Lastschriftverfahren und weiteren Zahlungsmedien ermöglicht.',
                        'LongDescription'  => 'ipayment unterstützt Ihren Geschäftserfolg im Internet, indem es Ihren Kunden die sichere Bezahlung per Kreditkarte, internetbasiertem elektronischen Lastschriftverfahren und weiteren Zahlungsmedien ermöglicht. Je nach genutztem Zahlungsanbieter können Sie Ihren Kunden über ipayment die Bezahlung mit folgenden Zahlungsmedien anbieten: Visa MasterCard Maestro American Express JCB Diners Club Visa Electron Solo Internetbasiertes Elektronisches Lastschriftverfahren (ELV) paysafecard Das Unternehmen, über das Sie Ihre Onlinezahlungen abwickeln möchten, können Sie dabei selbst auswählen - ipayment verfügt über Schnittstellen zu den wichtigsten Zahlungsanbietern. Sie schließen den Akzeptanzvertrag mit dem Anbieter Ihrer Wahl - ipayment sorgt für die reibungslose und sichere Abwicklung! Dazu nimmt ipayment die Zahlungsvorgänge direkt aus Ihrem System auf und verarbeitet sie im Hochleistungsrechenzentrum von 1&1 in Karlsruhe. Selbstverständlich erfüllt ipayment dabei die Zertifizierungsanforderungen gemäß dem PCI DSS (Payment Card Industry Data Security Standard). ',
                        'MetaDescription'  => 'iPayment unterstützt Ihren Geschäftserfolg im Internet, indem es Ihren Kunden die sichere Bezahlung per Kreditkarte, internetbasiertem elektronischen Lastschriftverfahren und weiteren Zahlungsmedien ermöglicht.',
                        'MetaKeywords'     => 'SilverCart, Module, iPayment, Zahlart',
                        'MetaTitle'        => 'iPayment'
                    ),
                    'PriceGrossAmount'          => 18.99,
                    'PriceGrossCurrency'        => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PriceNetAmount'            => 18.99 / 119 * 100,
                    'PriceNetCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'MSRPriceAmount'            => 18.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PurchasePriceAmount'       => 18.99,
                    'PurchasePriceCurrency'     => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'Weight'                    => 260,
                    'StockQuantity'             => 3,
                    'ProductNumberShop'         => '10002',
                    'ProductNumberManufacturer' => 'SC_Mod_101',
                    'ProductGroupID'            => $productGroupPayment->ID,
                    'productImage'              => 'logoipayment.gif',
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'Saferpay',
                        'ShortDescription' => 'Saferpay has set the standard for e-payment solutions in German-speaking Europe.',
                        'LongDescription'  => '<h3>Saferpay e-payment solutions for professionals and beginners</h3><p>Saferpay integrates all popular payment means in your Web shop through a single interface. This makes it easy to make adaptations and upgrades. What’s more, Saferpay enables the secure online processing of written and phone orders.</p><h3>More payment means – more turnover!</h3><p>Boost your turnover by offering a variety of payment means! With Saferpay you can offer your customers all popular payment means through a single interface, flexibly, easily & securely! You can accept all popular credit cards and debit cards with Saferpay and can activate new payment means at any time or deactivate existing ones and thus can flexibly react to your e-commerce requirements.</p><h3>More profit with security!</h3><p>SIX Card Solutions offers you comprehensive solutions from a single source to handle cashless, electronic payment processing as a merchant in e-commerce or in the phone/mail-order business as securely and conveniently as possible. The e-payment solution supports all current security standards. Increase confidence among your customers!</p>',
                        'MetaDescription'  => 'Saferpay has set the standard for e-payment solutions in German-speaking Europe.',
                        'MetaKeywords'     => 'SilverCart, modules, Saferpay, payment',
                        'MetaTitle'        => 'Saferpay'
                    ),
                    'en_GB' => array(
                        'Title'            => 'Saferpay',
                        'ShortDescription' => 'Saferpay has set the standard for e-payment solutions in German-speaking Europe.',
                        'LongDescription'  => '<h3>Saferpay e-payment solutions for professionals and beginners</h3><p>Saferpay integrates all popular payment means in your Web shop through a single interface. This makes it easy to make adaptations and upgrades. What’s more, Saferpay enables the secure online processing of written and phone orders.</p><h3>More payment means – more turnover!</h3><p>Boost your turnover by offering a variety of payment means! With Saferpay you can offer your customers all popular payment means through a single interface, flexibly, easily & securely! You can accept all popular credit cards and debit cards with Saferpay and can activate new payment means at any time or deactivate existing ones and thus can flexibly react to your e-commerce requirements.</p><h3>More profit with security!</h3><p>SIX Card Solutions offers you comprehensive solutions from a single source to handle cashless, electronic payment processing as a merchant in e-commerce or in the phone/mail-order business as securely and conveniently as possible. The e-payment solution supports all current security standards. Increase confidence among your customers!</p>',
                        'MetaDescription'  => 'Saferpay has set the standard for e-payment solutions in German-speaking Europe.',
                        'MetaKeywords'     => 'SilverCart, modules, Saferpay, payment',
                        'MetaTitle'        => 'Saferpay'
                    ),
                    'de_DE' => array(
                        'Title'            => 'Saferpay',
                        'ShortDescription' => 'Saferpay hat im deutschsprachigen Europa den Standard für E-Payment-Lösungen gesetzt und steht damit als Synonym für "sicheres Bezahlen im Internet."',
                        'LongDescription'  => '<h3>Saferpay E-Payment-Lösungen für Profis und Einsteiger</h3><p>Saferpay hat im deutschsprachigen Europa den Standard für E-Payment-Lösungen gesetzt und steht damit als Synonym für "sicheres Bezahlen im Internet." Dank Saferpay müssen sich Online-Händler wie Karteninhaber über die Sicherheit beim Einkaufen im Internet keine Sorgen mehr machen. Händler kennen und schätzen das sichere Bezahlen im Internet über Saferpay weltweit.</p><p>Saferpay integriert alle gängigen Zahlungsmittel in Ihren Webshop - über eine einzige Schnittstelle. Dadurch sind Anpassungen und Erweiterungen problemlos umsetzbar. Darüber hinaus ermöglicht Saferpay die sichere Onlineabwicklung von schriftlichen und telefonischen Bestellungen.</p><h3>Mehr Zahlungsmittel – mehr Umsatz!</h3><p>Steigern Sie Ihren Umsatz durch das Angebot einer Vielzahl an Zahlungsmitteln! Mit Saferpay bieten Sie Ihren Kunden alle gängigen Zahlungsmittel über eine einzige Schnittstelle – flexibel, einfach & sicher! Mit Saferpay können Sie alle gängigen Kreditkarten und Debitkarten akzeptieren. Sie können jederzeit neue Zahlungsmittel aufschalten oder bestehende wieder abschalten und somit flexibel auf die Bedürfnisse im E-Commerce reagieren.</p><h3>Mit Sicherheit mehr Gewinn!</h3><p>Um die bargeldlose, elektronische Zahlungsabwicklung für Sie als Händler im E-Commerce oder Phone-/Mail-Order Business so sicher und bequem wie möglich zu machen, bietet die SIX Card Solutions Ihnen als Händler Komplettlösungen aus einer Hand. Die E-Payment-Lösung unterstützt alle heutigen Sicherheitsstandards. Stärken Sie das Vertrauen Ihrer Kunden !</p>',
                        'MetaDescription'  => 'Saferpay hat im deutschsprachigen Europa den Standard für E-Payment-Lösungen gesetzt und steht damit als Synonym für "sicheres Bezahlen im Internet."',
                        'MetaKeywords'     => 'SilverCart, Module, Saferpay, Zahlart',
                        'MetaTitle'        => 'Saferpay'
                    ),
                    'PriceGrossAmount'          => 36.99,
                    'PriceGrossCurrency'        => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PriceNetAmount'            => 36.99 / 119 * 100,
                    'PriceNetCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'MSRPriceAmount'            => 36.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PurchasePriceAmount'       => 36.99,
                    'PurchasePriceCurrency'     => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'Weight'                    => 270,
                    'StockQuantity'             => 12,
                    'ProductNumberShop'         => '10003',
                    'ProductNumberManufacturer' => 'SC_Mod_102',
                    'ProductGroupID'            => $productGroupPayment->ID,
                    'productImage'              => 'logosaferpay.jpg',
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'Prepayment',
                        'ShortDescription' => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic.',
                        'LongDescription'  => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic. This module provides beside prepayment also payment via invoice.',
                        'MetaDescription'  => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic.',
                        'MetaKeywords'     => 'SilverCart, modules, Prepayment, payment',
                        'MetaTitle'        => 'Prepayment'
                    ),
                    'en_GB' => array(
                        'Title'            => 'Prepayment',
                        'ShortDescription' => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic.',
                        'LongDescription'  => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic. This module provides beside prepayment also payment via invoice.',
                        'MetaDescription'  => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic.',
                        'MetaKeywords'     => 'SilverCart, modules, Prepayment, payment',
                        'MetaTitle'        => 'Prepayment'
                    ),
                    'de_DE' => array(
                        'Title'            => 'Vorkasse',
                        'ShortDescription' => 'Flexibles Zahlungs-Modul für alle Zahlungsarten, die keine automatisierte Logik erfordern.',
                        'LongDescription'  => 'Flexibles Zahlungs-Modul für alle Zahlungsarten, die keine automatisierte Logik erfordern. Dieses Modul bietet neben der Vorkasse auch Rechnung als Zahlungsart.',
                        'MetaDescription'  => 'Flexibles Zahlungs-Modul für alle Zahlungsarten, die keine automatisierte Logik erfordern.',
                        'MetaKeywords'     => 'SilverCart, Module, Prepayment, Zahlart',
                        'MetaTitle'        => 'Vorkasse'
                    ),
                    'PriceGrossAmount'          => 27.99,
                    'PriceGrossCurrency'        => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PriceNetAmount'            => 27.99 / 119 * 100,
                    'PriceNetCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'MSRPriceAmount'            => 27.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PurchasePriceAmount'       => 27.99,
                    'PurchasePriceCurrency'     => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'Weight'                    => 290,
                    'StockQuantity'             => 9,
                    'ProductNumberShop'         => '10004',
                    'ProductNumberManufacturer' => 'SC_Mod_103',
                    'ProductGroupID'            => $productGroupPayment->ID,
                    'productImage'              => 'logoprepayment.png',
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'Cross selling',
                        'ShortDescription' => 'Cross selling is a practice of suggesting related products or services to a customer who is considering buying something.',
                        'LongDescription'  => 'It is a practice of suggesting related products or services to a customer who is considering buying something. Encourage established customers to buy different but related products. Getting a computer buyer to purchase a printer, for example. Source: www.procopytips.com',
                        'MetaDescription'  => 'Cross selling is a practice of suggesting related products or services to a customer who is considering buying something.',
                        'MetaKeywords'     => 'SilverCart, module, Cross selling, marketing',
                        'MetaTitle'        => 'Cross selling'
                    ),
                    'en_GB' => array(
                        'Title'            => 'Cross selling',
                        'ShortDescription' => 'Cross selling is a practice of suggesting related products or services to a customer who is considering buying something.',
                        'LongDescription'  => 'It is a practice of suggesting related products or services to a customer who is considering buying something. Encourage established customers to buy different but related products. Getting a computer buyer to purchase a printer, for example. Source: www.procopytips.com',
                        'MetaDescription'  => 'Cross selling is a practice of suggesting related products or services to a customer who is considering buying something.',
                        'MetaKeywords'     => 'SilverCart, module, Cross selling, marketing',
                        'MetaTitle'        => 'Cross selling'
                    ),
                    'de_DE' => array(
                        'Title'            => 'Cross-Selling',
                        'ShortDescription' => 'Kreuzverkauf bezeichnet im Marketing den Verkauf von sich ergänzenden Produkten oder Dienstleistungen.',
                        'LongDescription'  => 'Verkaufs- bzw. Marketinginstrument, bei dem Informationen über bereits existierende Kunden oder über bekanntes Konsumentenverhalten genutzt wird, um zusätzliche Käufe anderer Produkte zu begünstigen. Quelle: www.desig-n.de ',
                        'MetaDescription'  => 'Kreuzverkauf bezeichnet im Marketing den Verkauf von sich ergänzenden Produkten oder Dienstleistungen.',
                        'MetaKeywords'     => 'SilverCart, Modul, Cross-Selling, Marketing',
                        'MetaTitle'        => 'Cross-Selling'
                    ),
                    'PriceGrossAmount'          => 12.99,
                    'PriceGrossCurrency'        => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PriceNetAmount'            => 12.99 / 119 * 100,
                    'PriceNetCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'MSRPriceAmount'            => 12.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PurchasePriceAmount'       => 12.99,
                    'PurchasePriceCurrency'     => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'Weight'                    => 145,
                    'StockQuantity'             => 26,
                    'ProductNumberShop'         => '10006',
                    'ProductNumberManufacturer' => 'SC_Mod_104',
                    'ProductGroupID'            => $productGroupMarketing->ID,
                    'productImage'              => 'logocrossselling.png',
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'eKomi',
                        'ShortDescription' => 'Increase sales with eKomi’s trusted independent customer review system!',
                        'LongDescription'  => 'eKomi – The Feedback Company, helps companies through their web-based social SaaS technology with authentic and valuable reviews from customers and helps increasing the customer satisfaction and sales. Generate valuable customer reviews with eKomi' . "'" . 's intelligent, easy to install software and increase sales, trust and customer loyalty. <small>Source: www.ekomi.co.uk</small>',
                        'MetaDescription'  => 'Increase sales with eKomi’s trusted independent customer review system!',
                        'MetaKeywords'     => 'SilverCart, module, Ekomi, marketing',
                        'MetaTitle'        => 'eKomi'
                    ),
                    'en_GB' => array(
                        'Title'            => 'eKomi',
                        'ShortDescription' => 'Increase sales with eKomi’s trusted independent customer review system!',
                        'LongDescription'  => 'eKomi – The Feedback Company, helps companies through their web-based social SaaS technology with authentic and valuable reviews from customers and helps increasing the customer satisfaction and sales. Generate valuable customer reviews with eKomi' . "'" . 's intelligent, easy to install software and increase sales, trust and customer loyalty. <small>Source: www.ekomi.co.uk</small>',
                        'MetaDescription'  => 'Increase sales with eKomi’s trusted independent customer review system!',
                        'MetaKeywords'     => 'SilverCart, module, Ekomi, marketing',
                        'MetaTitle'        => 'eKomi'
                    ),
                    'de_DE' => array(
                        'Title'            => 'eKomi',
                        'ShortDescription' => 'Mehr Umsatz und Vertrauen durch unabhängige Kunden- und Produktbewertungen!',
                        'LongDescription'  => 'Beginnen Sie noch heute, durch intelligente Kundenbefragung authentisches und wertvolles Kundenfeedback zu gewinnen und damit Ihre Kundenzufriedenheit und Ihren Umsatz zu steigern. ',
                        'MetaDescription'  => 'Mehr Umsatz und Vertrauen durch unabhängige Kunden- und Produktbewertungen!',
                        'MetaKeywords'     => 'SilverCart, Modul, Ekomi, Marketing',
                        'MetaTitle'        => 'eKomi'
                    ),
                    'Title'                     => _t(RequireDefaultRecords::class . '.PRODUCTMARKETINGEKOMI_TITLE', 'eKomi'),
                    'PriceGrossAmount'          => 32.99,
                    'PriceGrossCurrency'        => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PriceNetAmount'            => 32.99 / 119 * 100,
                    'PriceNetCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'MSRPriceAmount'            => 32.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PurchasePriceAmount'       => 32.99,
                    'PurchasePriceCurrency'     => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'ShortDescription'          => _t(RequireDefaultRecords::class . '.PRODUCTMARKETINGEKOMI_SHORTDESC', 'Increase sales with eKomi’s trusted independent customer review system!'),
                    'LongDescription'           => _t(RequireDefaultRecords::class . '.PRODUCTMARKETINGEKOMI_LONGDESC', 'eKomi – The Feedback Company, helps companies through their web-based social SaaS technology with authentic and valuable reviews from customers and helps increasing the customer satisfaction and sales. Generate valuable customer reviews with eKomi' . "'" . 's intelligent, easy to install software and increase sales, trust and customer loyalty. <small>Source: www.ekomi.co.uk</small>'),
                    'MetaDescription'           => _t(RequireDefaultRecords::class . '.PRODUCTMARKETINGEKOMI_SHORTDESC', 'Increase sales with eKomi’s trusted independent customer review system!'),
                    'MetaTitle'                 => _t(RequireDefaultRecords::class . '.PRODUCTMARKETINGEKOMI_TITLE', 'eKomi'),
                    'MetaKeywords'              => _t(RequireDefaultRecords::class . '.PRODUCTMARKETINGEKOMI_KEYWORDS', 'SilverCart, module, Ekomi, marketing'),
                    'Weight'                    => 345,
                    'StockQuantity'             => 146,
                    'ProductNumberShop'         => '10007',
                    'ProductNumberManufacturer' => 'SC_Mod_105',
                    'ProductGroupID'            => $productGroupMarketing->ID,
                    'productImage'              => 'logoekomi.jpg',
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'Protected Shops',
                        'ShortDescription' => 'Make your online shop more secure! Try the Protected Shops quality rating system to boost your sales!',
                        'LongDescription'  => 'In the online business you will be confronted with unmanageable specifications which can be very expensive if you breach the conditions. Protected Shops offers a quality rating system to boost your sales. 67% of customers trust in a indepented shop ratings. Use the Vote connect interface of Protected Shops to integrate the quality rating system provided by Protected Shops into SilverCart.',
                        'MetaDescription'  => 'Make your online shop more secure! Try the Protected Shops quality rating system to boost your sales!',
                        'MetaKeywords'     => 'SilverCart, modules, ProtectedShops, marketing',
                        'MetaTitle'        => 'Protected Shops'
                    ),
                    'en_GB' => array(
                        'Title'            => 'Protected Shops',
                        'ShortDescription' => 'Make your online shop more secure! Try the Protected Shops quality rating system to boost your sales!',
                        'LongDescription'  => 'In the online business you will be confronted with unmanageable specifications which can be very expensive if you breach the conditions. Protected Shops offers a quality rating system to boost your sales. 67% of customers trust in a indepented shop ratings. Use the Vote connect interface of Protected Shops to integrate the quality rating system provided by Protected Shops into SilverCart.',
                        'MetaDescription'  => 'Make your online shop more secure! Try the Protected Shops quality rating system to boost your sales!',
                        'MetaKeywords'     => 'SilverCart, modules, ProtectedShops, marketing',
                        'MetaTitle'        => 'Protected Shops'
                    ),
                    'de_DE' => array(
                        'Title'            => 'Protected Shops',
                        'ShortDescription' => 'Machen Sie Ihr Online-Business sicherer! Wer im Internet handelt, kann seinen Umsatz durch das Protected Shops Bewertungssystem steigern. ',
                        'LongDescription'  => 'Wer im Internet handelt, ist mit einer unüberschaubaren Menge rechtlicher Vorgaben konfrontiert, die bei Nichteinhaltung zu einem teuren Unterfangen werden können. Gerade von Konkurrenten, die ihren Mitbewerb durch teuere Abmahnungen zu schädigen versuchen, geht für Ihr Unternehmen eine große Gefahr aus. Wer im Internet handelt, kann seinen Umsatz durch das Protected Shops Bewertungssystem steigern. 67% der Online Käufer vertrauen auf Online-Konsumentenbewertungen (Quelle: www.nielsen.com vom 24.07.2009). Mit unserer Vote Connect Schnittstelle integrieren Sie das Protected Shops Kundenbewertungssystem in Ihren Shop. ',
                        'MetaDescription'  => 'Machen Sie Ihr Online-Business sicherer! Wer im Internet handelt, kann seinen Umsatz durch das Protected Shops Bewertungssystem steigern. ',
                        'MetaKeywords'     => 'SilverCart, Module, ProtectedShops, Marketing',
                        'MetaTitle'        => 'Protected Shops'
                    ),
                    'PriceGrossAmount'          => 49.99,
                    'PriceGrossCurrency'        => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PriceNetAmount'            => 49.99 / 119 * 100,
                    'PriceNetCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'MSRPriceAmount'            => 49.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PurchasePriceAmount'       => 49.99,
                    'PurchasePriceCurrency'     => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'Weight'                    => 75,
                    'StockQuantity'             => 101,
                    'ProductNumberShop'         => '10008',
                    'ProductNumberManufacturer' => 'SC_Mod_106',
                    'ProductGroupID'            => $productGroupMarketing->ID,
                    'productImage'              => 'logoprotectedshops.jpg',
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'DHL',
                        'ShortDescription' => 'Packet interface for the shipping provider DHL (EasyLog)',
                        'LongDescription'  => 'Packet interface for the shipping provider DHL. Interface to export ordernumbers into Easylog and import tracking numbers back into SilverCart.',
                        'MetaDescription'  => 'Packet interface for the shipping provider DHL (EasyLog)',
                        'MetaKeywords'     => 'SilverCart, modules, shipping, DHL',
                        'MetaTitle'        => 'DHL'
                    ),
                    'en_GB' => array(
                        'Title'            => 'DHL',
                        'ShortDescription' => 'Packet interface for the shipping provider DHL (EasyLog)',
                        'LongDescription'  => 'Packet interface for the shipping provider DHL. Interface to export ordernumbers into Easylog and import tracking numbers back into SilverCart.',
                        'MetaDescription'  => 'Packet interface for the shipping provider DHL (EasyLog)',
                        'MetaKeywords'     => 'SilverCart, modules, shipping, DHL',
                        'MetaTitle'        => 'DHL'
                    ),
                    'de_DE' => array(
                        'Title'            => 'DHL',
                        'ShortDescription' => 'Paketschnittstelle zum Versandanbieter DHL (Easylog)',
                        'LongDescription'  => 'Paketschnittstelle zum Versandanbieter DHL für den Export von Bestellungen nach Easylog und den Import von Sendungsnachverfolgungsnummern in SilverCart.',
                        'MetaDescription'  => 'Paketschnittstelle zum Versandanbieter DHL (Easylog)',
                        'MetaKeywords'     => 'SilverCart, Module, Versand, DHL',
                        'MetaTitle'        => 'DHL'
                    ),
                    'PriceGrossAmount'          => 27.99,
                    'PriceGrossCurrency'        => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PriceNetAmount'            => 27.99 / 119 * 100,
                    'PriceNetCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'MSRPriceAmount'            => 27.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PurchasePriceAmount'       => 27.99,
                    'PurchasePriceCurrency'     => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'Weight'                    => 95,
                    'StockQuantity'             => 12,
                    'ProductNumberShop'         => '10009',
                    'ProductNumberManufacturer' => 'SC_Mod_107',
                    'ProductGroupID'            => $productGroupOthers->ID,
                    'productImage'              => 'logodhl.jpg',
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'PDF Invoice',
                        'ShortDescription' => 'Automatically generate PDF invoices',
                        'LongDescription'  => 'Automatically generated purchase order as PDF file.',
                        'MetaDescription'  => 'Automatically generate PDF invoices',
                        'MetaKeywords'     => 'SilverCart, modules, PDF invoice',
                        'MetaTitle'        => 'PDF Invoice'
                    ),
                    'en_GB' => array(
                        'Title'            => 'PDF Invoice',
                        'ShortDescription' => 'Automatically generate PDF invoices',
                        'LongDescription'  => 'Automatically generated purchase order as PDF file.',
                        'MetaDescription'  => 'Automatically generate PDF invoices',
                        'MetaKeywords'     => 'SilverCart, modules, PDF invoice',
                        'MetaTitle'        => 'PDF Invoice'
                    ),
                    'de_DE' => array(
                        'Title'            => 'PDF-Rechnung',
                        'ShortDescription' => 'Automatische Generierung von PDF-Rechnungen',
                        'LongDescription'  => 'Erstellt automatisiert PDF-Rechnungen bei Bestellungen.',
                        'MetaDescription'  => 'Automatische Generierung von PDF-Rechnungen',
                        'MetaKeywords'     => 'SilverCart, Module, PDF-Rechnung',
                        'MetaTitle'        => 'PDF-Rechnung'
                    ),
                    'PriceGrossAmount'          => 18.99,
                    'PriceGrossCurrency'        => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PriceNetAmount'            => 18.99 / 119 * 100,
                    'PriceNetCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'MSRPriceAmount'            => 18.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PurchasePriceAmount'       => 18.99,
                    'PurchasePriceCurrency'     => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'Weight'                    => 173,
                    'StockQuantity'             => 14,
                    'ProductNumberShop'         => '10011',
                    'ProductNumberManufacturer' => 'SC_Mod_109',
                    'ProductGroupID'            => $productGroupOthers->ID,
                    'productImage'              => 'logopdfinvoice.jpg',
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'Vouchers',
                        'ShortDescription' => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'LongDescription'  => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'MetaDescription'  => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'MetaKeywords'     => 'SilverCart, modules, vouchers',
                        'MetaTitle'        => 'Vouchers'
                    ),
                    'en_GB' => array(
                        'Title'            => 'Vouchers',
                        'ShortDescription' => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'LongDescription'  => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'MetaDescription'  => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'MetaKeywords'     => 'SilverCart, modules, vouchers',
                        'MetaTitle'        => 'Vouchers'
                    ),
                    'de_DE' => array(
                        'Title'            => 'Gutscheine',
                        'ShortDescription' => 'Gutscheinerstellung mit prozentualem oder absolutem Rabatt sowie Warengutscheinen.',
                        'LongDescription'  => 'Gutscheinerstellung mit prozentualem oder absolutem Rabatt sowie Warengutscheinen.',
                        'MetaDescription'  => 'Gutscheinerstellung mit prozentualem oder absolutem Rabatt sowie Warengutscheinen.',
                        'MetaKeywords'     => 'SilverCart, Module, Gutscheine',
                        'MetaTitle'        => 'Gutscheine'
                    ),
                    'PriceGrossAmount'          => 32.99,
                    'PriceGrossCurrency'        => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PriceNetAmount'            => 32.99 / 119 * 100,
                    'PriceNetCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'MSRPriceAmount'            => 32.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'PurchasePriceAmount'       => 32.99,
                    'PurchasePriceCurrency'     => _t(RequireDefaultRecords::class . '.CURRENCY', 'EUR'),
                    'Weight'                    => 373,
                    'StockQuantity'             => 24,
                    'ProductNumberShop'         => '10012',
                    'ProductNumberManufacturer' => 'SC_Mod_110',
                    'ProductGroupID'            => $productGroupOthers->ID,
                    'productImage'              => 'logovouchers.png',
                )
            );
            
            // Create folder for product images
            $exampleDataDir = Director::baseFolder().'/assets/test-images/';
            $imageFolder = new Folder();
            $imageFolder->Name = 'test-images';
            $imageFolder->write();
            
            if (!file_exists($exampleDataDir)) {
                mkdir($exampleDataDir);
            }
            
            $locales        = array('de_DE', 'en_GB', 'en_US');
            $fallbackLocale = false;

            if (!in_array(Translatable::get_current_locale(), $locales)) {
                $locales[]      = Translatable::get_current_locale();
                $fallbackLocale = Translatable::get_current_locale();
            }

            // Create products
            foreach ($products as $product) {
                $productItem                            = new Product();
                $productItem->TaxID                     = $taxRateID;
                $productItem->ManufacturerID            = $manufacturer->ID;
                $productItem->Weight                    = $product['Weight'];
                $productItem->StockQuantity             = $product['StockQuantity'];
                $productItem->ProductNumberShop         = $product['ProductNumberShop'];
                $productItem->ProductNumberManufacturer = $product['ProductNumberManufacturer'];
                $productItem->ProductGroupID            = $product['ProductGroupID'];
                $productItem->PriceGrossAmount          = $product['PriceGrossAmount'];
                $productItem->PriceGrossCurrency        = $product['PriceGrossCurrency'];
                $productItem->PriceNetAmount            = $product['PriceNetAmount'];
                $productItem->PriceNetCurrency          = $product['PriceNetCurrency'];
                $productItem->MSRPriceAmount            = $product['MSRPriceAmount'];
                $productItem->MSRPriceCurrency          = $product['MSRPriceCurrency'];
                $productItem->PurchasePriceAmount       = $product['PurchasePriceAmount'];
                $productItem->PurchasePriceCurrency     = $product['PurchasePriceCurrency'];
                $productItem->write();

                if ($fallbackLocale !== false) {
                    $product[$fallbackLocale] = $product['en_US'];
                }
                
                //create the language objects for the locales
                foreach ($locales as $locale) {
                   /*
                    * We need to check if a language object exists alredy because
                    * a hook of Product defaultly creates one.
                    */
                    $language = ProductTranslation::get()->filter(array(
                        'ProductID' => $productItem->ID,
                        'Locale' => $locale,
                    ))->first();
                    if (!$language) {
                        $language = new ProductTranslation();
                        $language->Locale = $locale;
                    }
                    $language->ProductID = $productItem->ID;

                    if (array_key_exists($locale, $product)) {
                        foreach ($product[$locale] as $attribute => $value) {
                            $language->{$attribute} = $value;
                        }
                    }
                    $language->write();
                }
                
                // Add product image
                if (array_key_exists('productImage', $product)) {
                    copy(
                        SILVERCART_IMG_PATH . DIRECTORY_SEPARATOR . 'exampledata'  . DIRECTORY_SEPARATOR . $product['productImage'],
                        $exampleDataDir.$product['productImage']
                    );

                    $productImage = new Image();
                    $productImage->Name = $product['productImage'];
                    $productImage->setFilename('test-images/' . $product['productImage']);
                    $productImage->ParentID = $imageFolder->ID;
                    $productImage->write();

                    $silvercartImage = new \SilverCart\Model\Product\Image();
                    $silvercartImage->ProductID = $productItem->ID;
                    $silvercartImage->ImageID = $productImage->ID;
                    $silvercartImage->write();
                }
            }
            
            // create widget sets
            $widgetSetFrontPageContentArea = new WidgetArea();
            $widgetSetFrontPageContentArea->write();
            
            $widgetSetFrontPageContent = new WidgetSet();
            $widgetSetFrontPageContent->setField('Title', _t(RequireDefaultRecords::class . '.WIDGETSET_FRONTPAGE_CONTENT_TITLE', 'Frontpage content area'));
            $widgetSetFrontPageContent->setField('WidgetAreaID', $widgetSetFrontPageContentArea->ID);
            $widgetSetFrontPageContent->write();
            
            $widgetSetFrontPageSidebarArea = new WidgetArea();
            $widgetSetFrontPageSidebarArea->write();
            
            $widgetSetFrontPageSidebar = new WidgetSet();
            $widgetSetFrontPageSidebar->setField('Title', _t(RequireDefaultRecords::class . '.WIDGETSET_FRONTPAGE_SIDEBAR_TITLE', 'Frontpage sidebar area'));
            $widgetSetFrontPageSidebar->setField('WidgetAreaID', $widgetSetFrontPageSidebarArea->ID);
            $widgetSetFrontPageSidebar->write();
            
            $widgetSetProductGroupPagesSidebarArea = new WidgetArea();
            $widgetSetProductGroupPagesSidebarArea->write();
            
            $widgetSetProductGroupPagesSidebar = new WidgetSet();
            $widgetSetProductGroupPagesSidebar->setField('Title', _t(RequireDefaultRecords::class . '.WIDGETSET_PRODUCTGROUPPAGES_SIDEBAR_TITLE', 'product group pages sidebar area'));
            $widgetSetProductGroupPagesSidebar->setField('WidgetAreaID', $widgetSetProductGroupPagesSidebarArea->ID);
            $widgetSetProductGroupPagesSidebar->write();
            
            // Attribute widget sets to pages
            $frontPage = Tools::PageByIdentifierCode('SilvercartFrontPage');
            
            if ($frontPage) {
                $frontPage->WidgetSetContent()->add($widgetSetFrontPageContent);
                $frontPage->WidgetSetSidebar()->add($widgetSetFrontPageSidebar);
            }
            
            $productGroupHolderPage = Tools::PageByIdentifierCode('SilvercartProductGroupHolder');
            
            if ($productGroupHolderPage) {
                $productGroupHolderPage->WidgetSetSidebar()->add($widgetSetProductGroupPagesSidebar);
            }
            
            // Create Widgets
            $widgetFrontPageContent1 = new ProductGroupItemsWidget();
            $widgetFrontPageContent1->setField('FrontTitle', _t(RequireDefaultRecords::class . '.WIDGETSET_FRONTPAGE_CONTENT1_TITLE', 'Payment Modules'));
            $widgetFrontPageContent1->setField('FrontContent', _t(RequireDefaultRecords::class . '.WIDGETSET_FRONTPAGE_CONTENT1_CONTENT', '<p>Explore all the payment modules for SilverCart!</p>'));
            $widgetFrontPageContent1->setField('numberOfProductsToShow', 4);
            $widgetFrontPageContent1->setField('ProductGroupPageID', $productGroupPayment->ID);
            $widgetFrontPageContent1->setField('GroupView', 'tile');
            $widgetFrontPageContent1->setField('isContentView', 1);
            $widgetFrontPageContent1->setField('useSlider', 0);
            $widgetFrontPageContent1->setField('buildArrows', 0);
            $widgetFrontPageContent1->setField('buildNavigation', 1);
            $widgetFrontPageContent1->setField('buildStartStop', 0);
            $widgetFrontPageContent1->setField('slideDelay', 6000);
            $widgetFrontPageContent1->setField('transitionEffect', 'fade');
            $widgetFrontPageContent1->setField('Sort', 2);
            $widgetFrontPageContent1->write();

            $widgetSetFrontPageContentArea->Widgets()->add($widgetFrontPageContent1);
            
            $widgetFrontPageContent2 = new ProductGroupItemsWidget();
            $widgetFrontPageContent2->setField('FrontTitle', _t(RequireDefaultRecords::class . '.WIDGETSET_FRONTPAGE_CONTENT2_TITLE', 'Other Modules'));
            $widgetFrontPageContent2->setField('FrontContent', _t(RequireDefaultRecords::class . '.WIDGETSET_FRONTPAGE_CONTENT2_CONTENT', '<p>There are modules for nearly every use case available for SilverCart.</p>'));
            $widgetFrontPageContent2->setField('numberOfProductsToShow', 1);
            $widgetFrontPageContent2->setField('numberOfProductsToFetch', 4);
            $widgetFrontPageContent2->setField('ProductGroupPageID', $productGroupOthers->ID);
            $widgetFrontPageContent2->setField('GroupView', 'list');
            $widgetFrontPageContent2->setField('isContentView', 1);
            $widgetFrontPageContent2->setField('useSlider', 1);
            $widgetFrontPageContent2->setField('buildArrows', 0);
            $widgetFrontPageContent2->setField('buildNavigation', 1);
            $widgetFrontPageContent2->setField('buildStartStop', 0);
            $widgetFrontPageContent2->setField('slideDelay', 6000);
            $widgetFrontPageContent2->setField('transitionEffect', 'horizontalSlide');
            $widgetFrontPageContent2->setField('Sort', 3);
            $widgetFrontPageContent2->write();

            $widgetSetFrontPageContentArea->Widgets()->add($widgetFrontPageContent2);
            
            $widgetFrontPageContent3 = new ImageSliderWidget();
            $widgetFrontPageContent3->setField('buildArrows', 0);
            $widgetFrontPageContent3->setField('buildNavigation', 1);
            $widgetFrontPageContent3->setField('buildStartStop', 0);
            $widgetFrontPageContent3->setField('slideDelay', 10000);
            $widgetFrontPageContent3->setField('transitionEffect', 'fade');
            $widgetFrontPageContent3->setField('Sort', 0);
            $widgetFrontPageContent3->write();

            $widgetSetFrontPageContentArea->Widgets()->add($widgetFrontPageContent3);
            
            copy(
                SILVERCART_IMG_PATH . DIRECTORY_SEPARATOR . 'exampledata'  . DIRECTORY_SEPARATOR . 'silvercart_teaser.jpg',
                $exampleDataDir.'/silvercart_teaser.jpg'
            );
            $teaserImage = new Image();
            $teaserImage->setFilename('test-images/silvercart_teaser.jpg');
            $teaserImage->ParentID = $imageFolder->ID;
            $teaserImage->write();
            
            $slideImage = new ImageSliderImage();
            //$slideImage->setField('Title',   'SilverCart Teaser');
            $slideImage->setField('ImageID', $teaserImage->ID);
            $slideImage->write();
            $sliderImageTranslations = array(
                'en_GB' => 'SilverCart Teaser',
                'en_US' => 'SilverCart Teaser',
                'de_DE' => 'SilverCart Teaser'
            );

            if ($fallbackLocale !== false) {
                $sliderImageTranslations[$fallbackLocale] = $sliderImageTranslations['en_US'];
            }

            foreach ($sliderImageTranslations as $locale => $translation) {
                $translationObj = ImageSliderImageTranslation::get()->filter('Locale', $locale)->first();
                if (!$translationObj) {
                    $translationObj = new ImageSliderImageTranslation();
                    $translationObj->Locale = $locale;
                    $translationObj->ImageSliderImageID = $slideImage->ID;
                }
                $translationObj->Title = $translation;
                $translationObj->write();
            }
            
            $widgetFrontPageContent3->slideImages()->add($slideImage);

            $widgetFrontPageSidebar1 = new ProductGroupItemsWidget();
            $widgetFrontPageSidebar1->setField('numberOfProductsToShow', 3);
            $widgetFrontPageSidebar1->setField('ProductGroupPageID', $productGroupMarketing->ID);
            $widgetFrontPageSidebar1->setField('useSlider', 0);
            $widgetFrontPageSidebar1->setField('GroupView', 'list');
            $widgetFrontPageSidebar1->setField('isContentView', 0);
            $widgetFrontPageSidebar1->setField('buildArrows', 0);
            $widgetFrontPageSidebar1->setField('buildNavigation', 1);
            $widgetFrontPageSidebar1->setField('buildStartStop', 0);
            $widgetFrontPageSidebar1->setField('slideDelay', 4000);
            $widgetFrontPageSidebar1->setField('transitionEffect', 'horizontalSlide');
            $widgetFrontPageSidebar1->setField('Sort', 0);
            $widgetFrontPageSidebar1->write();

            $widgetSetFrontPageSidebarArea->Widgets()->add($widgetFrontPageSidebar1);
            
            $widgetFrontPageSidebar2 = new ShoppingCartWidget();
            $widgetFrontPageSidebar2->setField('Sort', 1);
            $widgetFrontPageSidebar2->write();

            $widgetSetFrontPageSidebarArea->Widgets()->add($widgetFrontPageSidebar2);
            
            $widgetFrontPageSidebar3 = new LoginWidget();
            $widgetFrontPageSidebar3->setField('Sort', 2);
            $widgetFrontPageSidebar3->write();

            $widgetSetFrontPageSidebarArea->Widgets()->add($widgetFrontPageSidebar3);
            
            // product group page widgets
            
            $widgetProductGroupPageSidebar1 = new ProductGroupItemsWidget();
            $widgetProductGroupPageSidebar1->setField('numberOfProductsToShow', 3);
            $widgetProductGroupPageSidebar1->setField('ProductGroupPageID', $productGroupMarketing->ID);
            $widgetProductGroupPageSidebar1->setField('useSlider', 0);
            $widgetProductGroupPageSidebar1->setField('GroupView', 'list');
            $widgetProductGroupPageSidebar1->setField('isContentView', 0);
            $widgetProductGroupPageSidebar1->setField('buildArrows', 0);
            $widgetProductGroupPageSidebar1->setField('buildNavigation', 1);
            $widgetProductGroupPageSidebar1->setField('buildStartStop', 0);
            $widgetProductGroupPageSidebar1->setField('slideDelay', 4000);
            $widgetProductGroupPageSidebar1->setField('transitionEffect', 'horizontalSlide');
            $widgetProductGroupPageSidebar1->setField('Sort', 0);
            $widgetProductGroupPageSidebar1->write();

            $widgetSetProductGroupPagesSidebarArea->Widgets()->add($widgetProductGroupPageSidebar1);
            
            $widgetProductGroupPageSidebar2 = new ShoppingCartWidget();
            $widgetProductGroupPageSidebar2->setField('Sort', 1);
            $widgetProductGroupPageSidebar2->write();

            $widgetSetProductGroupPagesSidebarArea->Widgets()->add($widgetProductGroupPageSidebar2);
            
            $widgetProductGroupPageSidebar3 = new LoginWidget();
            $widgetProductGroupPageSidebar3->setField('Sort', 2);
            $widgetProductGroupPageSidebar3->write();

            $widgetSetProductGroupPagesSidebarArea->Widgets()->add($widgetProductGroupPageSidebar3);
            
            //self::createTestDataSlidorion($widgetSetFrontPageContentArea);
            
            return true;
        }
    }
    
    /**
     * Creates all pages and widgets for the slidorion widgets.
     *
     * @param WidgetSet $widgetSetFrontPageContentArea The widgetset content area
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.06.2014
     */
    public static function createTestDataSlidorion($widgetSetFrontPageContentArea) {
        // Create Widget
        $widgetSlidorion = new SlidorionProductGroupWidget();
        $widgetSlidorion->setField('Sort', 1);
        $widgetSlidorion->setField('Title', _t(RequireDefaultRecords::class . '.SLIDORION_TITLE', 'Advantages of SilverCart'));
        $widgetSlidorion->write();

        $widgetSetFrontPageContentArea->Widgets()->add($widgetSlidorion);
    }

    /**
     * creates test configuration data on /dev/build or by adding test
     * configuration in ModelAdmin.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.07.2011
     */
    public static function createTestConfiguration() {
        if (self::$enableTestData === true) {
            //create a carrier and an associated zone and shipping method
            $carrier = Carrier::get()->first();
            if (!$carrier) {
                self::createTestTaxRates();
                
                $carrier = new Carrier();
                $carrier->Title = 'DHL';
                $carrier->FullTitle = 'DHL International GmbH';
                $carrier->write();
                $carrierTranslations = array(
                    'en_GB' => array(
                        'Title' => 'DHL',
                        'FullTitle' => 'DHL International GmbH'
                    ),
                    'en_US' => array(
                        'Title' => 'DHL',
                        'FullTitle' => 'DHL International GmbH'
                    ),
                    'de_DE' => array(
                        'Title' => 'DHL',
                        'FullTitle' => 'DHL International GmbH'
                    )
                );
            
                $locales        = array('de_DE', 'en_GB', 'en_US');
                $fallbackLocale = false;

                if (!in_array(Translatable::get_current_locale(), $locales)) {
                    $locales[]      = Translatable::get_current_locale();
                    $fallbackLocale = Translatable::get_current_locale();
                }

                if ($fallbackLocale !== false) {
                    $carrierTranslations[$fallbackLocale] = $carrierTranslations['en_US'];
                }

                foreach ($carrierTranslations as $locale => $attributes) {
                    $languageObj = CarrierTranslation::get()->filter(array(
                        'CarrierID' => $carrier->ID,
                        'Locale' => $locale,
                    ))->first();
                    if (!$languageObj) {
                        $languageObj = new CarrierTranslation();
                        $languageObj->Locale = $locale;
                        $languageObj->CarrierID = $carrier->ID;
                    }
                    foreach ($attributes as $attribute => $value) {
                        $languageObj->{$attribute} = $value;
                    }
                    $languageObj->write();
                    
                }
                //relate carrier to zones
                $zoneDomestic = Zone::get()->first();
                if (!$zoneDomestic) {
                    $zones = array(
                        array(
                            'en_GB' => 'Domestic',
                            'en_US' => 'Domestic',
                            'de_DE' => 'Inland'
                        ),
                        array(
                            'en_GB' => 'EU',
                            'en_US' => 'European Union',
                            'de_DE' => 'EU'
                        )
                    );

                    $locales        = array('de_DE', 'en_GB', 'en_US');
                    $fallbackLocale = false;

                    if (!in_array(Translatable::get_current_locale(), $locales)) {
                        $locales[]      = Translatable::get_current_locale();
                        $fallbackLocale = Translatable::get_current_locale();
                    }

                    if ($fallbackLocale !== false) {
                        $zones[0][$fallbackLocale] = $zones[0]['en_US'];
                        $zones[1][$fallbackLocale] = $zones[1]['en_US'];
                    }
                    
                    foreach ($zones as $zone) {
                        $zoneObj = new Zone();
                        $zoneObj->write();
                        $zoneObj->Carriers()->add($carrier);
                        $zoneObj->write();
                        foreach ($zone as $locale => $title) {
                            $zoneTranslation = ZoneTranslation::get()->filter(array(
                                'ZoneID' => $zoneObj->ID,
                                'Locale' => $locale,
                            ))->first();
                            if (!$zoneTranslation) {
                                $zoneTranslation = new ZoneTranslation();
                                $zoneTranslation->ZoneID = $zoneObj->ID;
                                $zoneTranslation->Locale = $locale;
                            }
                            $zoneTranslation->Title = $title;
                            $zoneTranslation->write();
                        }
                    }
                    
                }
                //Retrieve the active country if exists
                $country = Country::get()->filter('Active', '1')->first();
                if (!$country) {
                    //Retrieve the country dynamically depending on the installation language
                    $installationLanguage = i18n::get_locale();
                    $ISO2 = substr($installationLanguage, -2);
                    $country = Country::get()->filter('ISO2', $ISO2)->first();
                    if (!$country) {
                        $country = new Country();
                        $country->Title = 'Testcountry';
                        $country->ISO2 = $ISO2;
                        $country->ISO3 = $ISO2;
                    }
                    $country->Active = true;
                    $country->write();
                }
                
                $zoneDomestic = Zone::get()->byID(1);
                $zoneDomestic->Countries()->add($country);
                
                if (class_exists('SilverCart\\Prepayment\\Model\\PaymentPrepayment')) {
                    // create if not exists, activate and relate payment method
                    $paymentMethod = \SilverCart\Prepayment\Model\PaymentPrepayment::get()->first();
                    if (!$paymentMethod) {
                        $paymentMethodHandler = new PaymentMethod();
                        $paymentMethodHandler->requireDefaultRecords();
                    }
                    $paymentMethod = \SilverCart\Prepayment\Model\PaymentPrepayment::get()->first();
                    $paymentMethod->isActive = true;
                    $orderStatusPending = OrderStatus::get()->filter('Code', 'pending')->first();
                    if ($orderStatusPending) {
                        $paymentMethod->orderStatus = $orderStatusPending->Code;
                    }
                    $paymentMethod->write();
                    $country->PaymentMethods()->add($paymentMethod);
                }

                // create a shipping method
                $shippingMethod = ShippingMethod::get()->first();
                if (!$shippingMethod) {
                    $shippingMethod = new ShippingMethod();
                    //relate shipping method to carrier
                    $shippingMethod->CarrierID = $carrier->ID;
                }
                $shippingMethod->isActive = 1;
                $shippingMethod->write();
                $shippingMethod->Zones()->add($zoneDomestic);
                
                
                //create the language objects for the shipping method
                $shippingMethodTranslations = array(
                    'de_DE' => 'Paket',
                    'en_GB' => 'Package',
                    'en_US' => 'Package'
                );

                $locales        = array('de_DE', 'en_GB', 'en_US');
                $fallbackLocale = false;

                if (!in_array(Translatable::get_current_locale(), $locales)) {
                    $locales[]      = Translatable::get_current_locale();
                    $fallbackLocale = Translatable::get_current_locale();
                }

                if ($fallbackLocale !== false) {
                    $shippingMethodTranslations[$fallbackLocale] = $shippingMethodTranslations['en_US'];
                }

                foreach ($shippingMethodTranslations as $locale => $title) {
                    $shippingMethodTranslation = ShippingMethodTranslation::get()->filter(array(
                        'Locale' => $locale,
                        'ShippingMethodID' => $shippingMethod->ID,
                    ))->first();
                    if (!$shippingMethodTranslation) {
                        $shippingMethodTranslation = new ShippingMethodTranslation();
                        $shippingMethodTranslation->Locale = $locale;
                        $shippingMethodTranslation->ShippingMethodID = $shippingMethod->ID;
                    }
                    $shippingMethodTranslation->Title = $title;
                    $shippingMethodTranslation->write();
                }

                // create a shipping fee and relate it to zone, tax and shipping method
                $shippingFee = ShippingFee::get()->first();
                if (!$shippingFee) {
                    $shippingFee = new ShippingFee();
                    $shippingFee->MaximumWeight = '100000';
                    $shippingFee->UnlimitedWeight = true;
                    $shippingFee->Price = new DBMoney();
                    $shippingFee->Price->setAmount('3.9');
                    $shippingFee->Price->setCurrency('EUR');
                }
                $shippingFee->ShippingMethodID = $shippingMethod->ID;
                $shippingFee->ZoneID = $zoneDomestic->ID;
                $higherTaxRate = Tax::get()->filter('Rate', '19')->first();
                $shippingFee->TaxID = $higherTaxRate->ID;
                $shippingFee->write();
                
                return true;
            }
        }
        return false;
    }

    /**
     * creates test tax rates on /dev/build or creating test data in ModelAdmin.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.07.2011
     */
    public static function createTestTaxRates() {
        if (self::$enableTestData === true) {
            // create two standard german tax rates if no tax rate exists
            $taxRate = Tax::get()->first();

            if (!$taxRate) {
                $taxrates = array(
                    '19' => array(
                        'en_US' => '19%',
                        'en_GB' => '19%',
                        'de_DE' => '19%'
                    ),
                    '7' => array(
                        'en_US' => '7%',
                        'en_GB' => '7%',
                        'de_DE' => '7%'
                    )
                );

                $locales        = array('de_DE', 'en_GB', 'en_US');
                $fallbackLocale = false;

                if (!in_array(Translatable::get_current_locale(), $locales)) {
                    $locales[]      = Translatable::get_current_locale();
                    $fallbackLocale = Translatable::get_current_locale();
                }

                if ($fallbackLocale !== false) {
                    $taxrates[0][$fallbackLocale] = $taxrates[0]['en_US'];
                    $taxrates[1][$fallbackLocale] = $taxrates[1]['en_US'];
                }
                
                foreach ($taxrates as $taxrate => $languages) {
                    $rateObj = new Tax();
                    $rateObj->Rate = $taxrate;
                    $rateObj->write();
                    foreach ($languages as $locale => $title) {
                        $rateTranslation = TaxTranslation::get()->filter(array(
                            'Locale' => $locale,
                            'TaxID' => $rateObj->ID,
                        ))->first();
                        if (!$rateTranslation) {
                            $rateTranslation = new TaxTranslation();
                            $rateTranslation->Locale = $locale;
                            $rateTranslation->TaxID = $rateObj->ID;
                        }
                        $rateTranslation->Title = $title;
                        $rateTranslation->write();
                    }
                }
            }
        }
    }
}
