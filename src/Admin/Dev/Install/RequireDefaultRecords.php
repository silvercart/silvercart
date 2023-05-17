<?php

namespace SilverCart\Admin\Dev\Install;

use Broarm\CookieConsent\CookieConsent;
use Broarm\CookieConsent\Model\CookiePolicyPage;
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
use SilverCart\Model\Payment\PaymentStatus;
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
class RequireDefaultRecords
{
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
    public function getTranslationLocale()
    {
        return $this->translationLocale;
    }

    /**
     * Sets the translation locale
     *
     * @param string $translationLocale Translation locale
     * 
     * @return void
     */
    public function setTranslationLocale($translationLocale)
    {
        $this->translationLocale = $translationLocale;
    }
    
    /**
     * Creates the default groups used in SilverCart
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2018
     */
    public function createDefaultGroups()
    {
        $defaultGroups = [
            [
                'Title'     => _t(Customer::class . '.ANONYMOUSCUSTOMER', 'anonymous customer'),
                'Code'      => Customer::GROUP_CODE_ANONYMOUS,
                'Pricetype' => "gross",
            ],
            [
                'Title'     => _t(Customer::class . '.BUSINESSCUSTOMER', 'business customer'),
                'Code'      => Customer::GROUP_CODE_B2B,
                'Pricetype' => "net",
            ],
            [
                'Title'     => _t(Customer::class . '.REGULARCUSTOMER', 'regular customer'),
                'Code'      => Customer::GROUP_CODE_B2C,
                'Pricetype' => "gross",
            ],
        ];
        foreach ($defaultGroups as $groupData) {
            $group = Group::get()->filter('Code', $groupData['Code'])->first();
            if (!($group instanceof Group)
             || !$group->exists()
            ) {
                Group::create($groupData)
                        ->write();
            }
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
    public function createDefaultConfig()
    {
        $config = Config::getConfig();
        if ($config instanceof SiteConfig
         && $config->exists()
         && is_null($config->DefaultCurrency)
        ) {
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
     * Adds the current locale to the i18n data if not added by default.
     * en_US will be used as fallback locale.
     * 
     * @param array &$defaults Defaults to check and update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2018
     */
    protected function addCurrentLocaleEntryIfNotExists(&$defaults)
    {
        $locales = ['de_DE', 'en_GB', 'en_US'];
        $locale  = Tools::current_locale();

        if (!in_array($locale, $locales)
         && $locale !== false
        ) {
            foreach ($defaults as $idCode => $i18n) {
                $defaults[$idCode][$locale] = $i18n['en_US'];
            }
        }
    }
    
    /**
     * Creates the default OrderStatus if not exists
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function createDefaultOrderStatus()
    {
        OrderStatus::singleton()->requireDefaultRecords();
    }
    
    /**
     * Creates the default PaymentStatus if not exists
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2018
     */
    public function createDefaultPaymentStatus()
    {
        PaymentStatus::singleton()->requireDefaultRecords();
    }
    
    /**
     * Creates the default AvailabilityStatus if not exists
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function createDefaultAvailabilityStatus()
    {
        $defaults = [
            'available'     => ['en_US' => 'available',   'en_GB' => 'available',   'de_DE' => 'verfügbar'],
            'not-available' => ['en_US' => 'unavailable', 'en_GB' => 'unavailable', 'de_DE' => 'nicht verfügbar'],
        ];
        $this->addCurrentLocaleEntryIfNotExists($defaults);
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
    public function createDefaultTranslatableDataObject($translatableDataObjectEntries, $translatableDataObjectName, $translatableDataObjectTranslationName = '', $translatableDataObjectRelationName = '')
    {
        if (empty($translatableDataObjectTranslationName)) {
            $translatableDataObjectTranslationName = $translatableDataObjectName . 'Translation';
        }
        if (empty($translatableDataObjectRelationName)) {
            $reflection = new ReflectionClass($translatableDataObjectName);
            $translatableDataObjectRelationName = $reflection->getShortName() . 'ID';
        }
        $translationLocale = $this->getTranslationLocale();
        foreach ($translatableDataObjectEntries as $code => $languages) {
            $obj = DataObject::get($translatableDataObjectName)
                    ->filter('Code', $code)
                    ->first();
            if (!$obj) {
                $obj = new $translatableDataObjectName();
                $obj->Code = $code;
                $obj->write();
            }
            if (!is_null($translationLocale)
             && !array_key_exists($translationLocale, $languages)
             && array_key_exists('en_US', $languages)
            ) {
                $languages[$translationLocale] = $languages['en_US'];
            }
            foreach ($languages as $locale => $title) {
                if (empty($locale)) {
                    continue;
                }
                $objTranslation = DataObject::get($translatableDataObjectTranslationName)
                        ->filter([
                            'Locale'                            => $locale,
                            $translatableDataObjectRelationName => $obj->ID
                        ])
                        ->first();
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
     * @since 05.09.2018
     */
    public function createDefaultNumberRanges()
    {
        $defaults = [
            [
                'Identifier' => 'OrderNumber',
                'Title'      => _t(NumberRange::class . '.ORDERNUMBER', 'Ordernumber'),
            ],
            [
                'Identifier' => 'CustomerNumber',
                'Title'      => _t(NumberRange::class . '.CUSTOMERNUMBER', 'Customernumber'),
            ],
        ];
        foreach ($defaults as $defaultData) {
            $object = NumberRange::get()->filter('Identifier', $defaultData['Identifier'])->first();
            if (!($object instanceof NumberRange)
             || !$object->exists()
            ) {
                NumberRange::create($defaultData)
                        ->write();
            }
        }
    }
    
    /**
     * Creates the default SiteTree if not exists
     * 
     * @return \SilverCart\Model\Pages\FrontPage
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2018
     */
    public function createDefaultSiteTree()
    {
        $rootPage = Page::get()->filter('IdentifierCode', Page::IDENTIFIER_FRONT_PAGE)->first();
        if (!$rootPage) {
            //create a silvercart front page (parent of all other SilverCart pages)
            $rootPage                 = FrontPage::create();
            $rootPage->IdentifierCode = Page::IDENTIFIER_FRONT_PAGE;
            $rootPage->Title          = 'SilverCart';
            $rootPage->URLSegment     = SiteTree::get_by_link('home') ? 'shop' : 'home';
            $rootPage->ShowInMenus    = false;
            $rootPage->ShowInSearch   = false;
            $rootPage->CanViewType    = 'Anyone';
            $rootPage->Content        = _t(FrontPage::class . '.DEFAULT_CONTENT', '<h2>Welcome to <strong>SilverCart</strong> Webshop!</h2>');
            $rootPage->write();
            $rootPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

            //create a silvercart product group holder as a child af the silvercart root
            $productGroupHolder                             = ProductGroupHolder::create();
            $productGroupHolder->Title                      = _t(ProductGroupHolder::class . '.DEFAULT_TITLE', 'Products');
            $productGroupHolder->URLSegment                 = _t(ProductGroupHolder::class . '.DEFAULT_URLSEGMENT', 'products');
            $productGroupHolder->ParentID                   = $rootPage->ID;
            $productGroupHolder->IdentifierCode             = Page::IDENTIFIER_PRODUCT_GROUP_HOLDER;
            $productGroupHolder->InheritFromParent          = false;
            $productGroupHolder->UseAsRootForMainNavigation = true;
            $productGroupHolder->write();
            $productGroupHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

            //create a cart page
            $cartPage                    = CartPage::create();
            $cartPage->Title             = _t(CartPage::class . '.DEFAULT_TITLE', 'Cart');
            $cartPage->URLSegment        = _t(CartPage::class . '.DEFAULT_URLSEGMENT', 'cart');
            $cartPage->ShowInSearch      = false;
            $cartPage->IdentifierCode    = Page::IDENTIFIER_CART_PAGE;
            $cartPage->ParentID          = $rootPage->ID;
            $cartPage->InheritFromParent = false;
            $cartPage->write();
            $cartPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

            //create a silvercart checkout step (checkout) as achild of the silvercart root
            $checkoutStep                       = CheckoutStep::create();
            $checkoutStep->Title                = _t(CheckoutStep::class . '.DEFAULT_TITLE', 'Checkout');
            $checkoutStep->URLSegment           = _t(CheckoutStep::class . '.DEFAULT_URLSEGMENT', 'checkout');
            $checkoutStep->ShowInSearch         = false;
            $checkoutStep->ParentID             = $rootPage->ID;
            $checkoutStep->IdentifierCode       = Page::IDENTIFIER_CHECKOUT_PAGE;
            $checkoutStep->InheritFromParent    = false;
            $checkoutStep->write();
            $checkoutStep->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

            //create a payment notification page as a child of the silvercart root
            $paymentNotification                    = PaymentNotification::create();
            $paymentNotification->Title             = _t(PaymentNotification::class . '.DEFAULT_TITLE', 'payment notification');
            $paymentNotification->URLSegment        = _t(PaymentNotification::class . '.DEFAULT_URLSEGMENT', 'payment-notification');
            $paymentNotification->ShowInMenus       = false;
            $paymentNotification->ShowInSearch      = false;
            $paymentNotification->ParentID          = $rootPage->ID;
            $paymentNotification->IdentifierCode    = "SilvercartPaymentNotification";
            $paymentNotification->InheritFromParent = false;
            $paymentNotification->write();
            $paymentNotification->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

            //create a silvercart registration page as a child of silvercart root
            $registrationPage                       = RegistrationPage::create();
            $registrationPage->Title                = _t(RegistrationPage::class . '.DEFAULT_TITLE', 'registration page');
            $registrationPage->URLSegment           = _t(RegistrationPage::class . '.DEFAULT_URLSEGMENT', 'registration');
            $registrationPage->ShowInMenus          = false;
            $registrationPage->ShowInSearch         = false;
            $registrationPage->ParentID             = $rootPage->ID;
            $registrationPage->IdentifierCode       = Page::IDENTIFIER_REGISTRATION_PAGE;
            $registrationPage->InheritFromParent    = false;
            $registrationPage->write();
            $registrationPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

            //create a silvercart search results page as a child of the silvercart root
            $searchResultsPage                      = SearchResultsPage::create();
            $searchResultsPage->Title               = _t(SearchResultsPage::class . '.DEFAULT_TITLE', 'search results');
            $searchResultsPage->URLSegment          = _t(SearchResultsPage::class . '.DEFAULT_URLSEGMENT', 'search-results');
            $searchResultsPage->ShowInMenus         = false;
            $searchResultsPage->ShowInSearch        = false;
            $searchResultsPage->ParentID            = $rootPage->ID;
            $searchResultsPage->IdentifierCode      = Page::IDENTIFIER_SEARCH_RESULTS_PAGE;
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
    public function createDefaultSiteTreeCMSSection($rootPage)
    {
        $legalNavigationHolder                    = MetaNavigationHolder::create();
        $legalNavigationHolder->Title             = _t(MetaNavigationHolder::class . '.DEFAULT_TITLE_LEGAL', 'Legal');
        $legalNavigationHolder->URLSegment        = _t(MetaNavigationHolder::class . '.DEFAULT_URLSEGMENT_LEGAL', 'legal');
        $legalNavigationHolder->ShowInMenus       = false;
        $legalNavigationHolder->IdentifierCode    = Page::IDENTIFIER_META_LEGAL_HOLDER;
        $legalNavigationHolder->ParentID          = $rootPage->ID;
        $legalNavigationHolder->InheritFromParent = false;
        $legalNavigationHolder->write();
        $legalNavigationHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $serviceNavigationHolder                    = MetaNavigationHolder::create();
        $serviceNavigationHolder->Title             = _t(MetaNavigationHolder::class . '.DEFAULT_TITLE_SERVICE', 'Service');
        $serviceNavigationHolder->URLSegment        = _t(MetaNavigationHolder::class . '.DEFAULT_URLSEGMENT_SERVICE', 'service');
        $serviceNavigationHolder->ShowInMenus       = false;
        $serviceNavigationHolder->IdentifierCode    = Page::IDENTIFIER_META_SERVICE_HOLDER;
        $serviceNavigationHolder->ParentID          = $rootPage->ID;
        $serviceNavigationHolder->InheritFromParent = false;
        $serviceNavigationHolder->write();
        $serviceNavigationHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $aboutNavigationHolder                    = MetaNavigationHolder::create();
        $aboutNavigationHolder->Title             = _t(MetaNavigationHolder::class . '.DEFAULT_TITLE_ABOUT', 'About us');
        $aboutNavigationHolder->URLSegment        = _t(MetaNavigationHolder::class . '.DEFAULT_URLSEGMENT_ABOUT', 'about-us');
        $aboutNavigationHolder->ShowInMenus       = false;
        $aboutNavigationHolder->IdentifierCode    = Page::IDENTIFIER_META_ABOUT_HOLDER;
        $aboutNavigationHolder->ParentID          = $rootPage->ID;
        $aboutNavigationHolder->InheritFromParent = false;
        $aboutNavigationHolder->write();
        $aboutNavigationHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $shopNavigationHolder                    = MetaNavigationHolder::create();
        $shopNavigationHolder->Title             = _t(MetaNavigationHolder::class . '.DEFAULT_TITLE_SHOP', 'Shopsystem');
        $shopNavigationHolder->URLSegment        = _t(MetaNavigationHolder::class . '.DEFAULT_URLSEGMENT_SHOP', 'shop-system');
        $shopNavigationHolder->ShowInMenus       = false;
        $shopNavigationHolder->IdentifierCode    = Page::IDENTIFIER_META_SHOP_HOLDER;
        $shopNavigationHolder->ParentID          = $rootPage->ID;
        $shopNavigationHolder->InheritFromParent = false;
        $shopNavigationHolder->write();
        $shopNavigationHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        // Sub pages of legal node
        $termsOfServicePage                 = MetaNavigationPage::create();
        $termsOfServicePage->Title          = _t(MetaNavigationPage::class . '.DEFAULT_TITLE_TERMS', 'terms of service');
        $termsOfServicePage->URLSegment     = _t(MetaNavigationPage::class . '.DEFAULT_URLSEGMENT_TERMS', 'terms-of-service');
        $termsOfServicePage->ParentID       = $legalNavigationHolder->ID;
        $termsOfServicePage->IdentifierCode = Page::IDENTIFIER_TERMS_OF_SERVICE_PAGE;
        $termsOfServicePage->write();
        $termsOfServicePage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $revocationInstructionPage                  = RedirectorPage::create();
        $revocationInstructionPage->RedirectionType = 'Internal';
        $revocationInstructionPage->LinkToID        = $termsOfServicePage->ID;
        $revocationInstructionPage->Title           = _t(MetaNavigationPage::class . '.DEFAULT_TITLE_REVOCATION', 'revocation instruction');
        $revocationInstructionPage->URLSegment      = _t(MetaNavigationPage::class . '.DEFAULT_URLSEGMENT_REVOCATION', 'revocation-instruction');
        $revocationInstructionPage->ParentID        = $legalNavigationHolder->ID;
        $revocationInstructionPage->IdentifierCode  = Page::IDENTIFIER_REVOCATION_INSTRUCTION_PAGE;
        $revocationInstructionPage->write();
        $revocationInstructionPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $revocationPage                 = RevocationFormPage::create();
        $revocationPage->Title          = _t(RevocationFormPage::class . '.DEFAULT_TITLE', 'Revocation');
        $revocationPage->URLSegment     = _t(RevocationFormPage::class . '.DEFAULT_URLSEGMENT', 'Revocation');
        $revocationPage->IdentifierCode = Page::IDENTIFIER_REVOCATION_FORM_PAGE;
        $revocationPage->ParentID       = $legalNavigationHolder->ID;
        $revocationPage->write();
        $revocationPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $dataPrivacyStatementPage                 = MetaNavigationPage::create();
        $dataPrivacyStatementPage->Title          = _t(MetaNavigationPage::class . '.DEFAULT_TITLE_PRIVACY', 'Data privacy statement');
        $dataPrivacyStatementPage->URLSegment     = _t(MetaNavigationPage::class . '.DEFAULT_URLSEGMENT_PRIVACY', 'data-privacy-statement');
        $dataPrivacyStatementPage->IdentifierCode = Page::IDENTIFIER_DATA_PRIVACY_PAGE;
        $dataPrivacyStatementPage->ParentID       = $legalNavigationHolder->ID;
        $dataPrivacyStatementPage->write();
        $dataPrivacyStatementPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        
        
        if (!CookieConsent::config()->create_default_pages
         && !CookiePolicyPage::get()->exists()
        ) {
            $cookiePolicyPage           = CookiePolicyPage::create();
            $cookiePolicyPage->ParentID = $legalNavigationHolder->ID;
            $cookiePolicyPage->write();
            $cookiePolicyPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
            $cookiePolicyPage->flushCache();
            DB::alteration_message('Cookie Policy page created', 'created');
        }

        // Sub pages of service node
        $this->createDefaultSiteTreeMyAccountSection($serviceNavigationHolder);
        
        $paymentMethodsPage                 = PaymentMethodsPage::create();
        $paymentMethodsPage->Title          = _t(PaymentMethodsPage::class . '.DEFAULT_TITLE', 'Payment methods');
        $paymentMethodsPage->URLSegment     = _t(PaymentMethodsPage::class . '.DEFAULT_URLSEGMENT', 'payment-methods');
        $paymentMethodsPage->ParentID       = $serviceNavigationHolder->ID;
        $paymentMethodsPage->IdentifierCode = Page::IDENTIFIER_PAYMENT_METHODS_PAGE;
        $paymentMethodsPage->write();
        $paymentMethodsPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $shippingFeesPage                 = ShippingFeesPage::create();
        $shippingFeesPage->Title          = _t(ShippingFeesPage::class . '.DEFAULT_TITLE', 'shipping fees');
        $shippingFeesPage->URLSegment     = _t(ShippingFeesPage::class . '.DEFAULT_URLSEGMENT', 'shipping-fees');
        $shippingFeesPage->ParentID       = $serviceNavigationHolder->ID;
        $shippingFeesPage->IdentifierCode = Page::IDENTIFIER_SHIPPING_FEES_PAGE;
        $shippingFeesPage->write();
        $shippingFeesPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        $newsletterPage                 = NewsletterPage::create();
        $newsletterPage->Title          = _t(NewsletterPage::class . '.DEFAULT_TITLE', 'Newsletter');
        $newsletterPage->URLSegment     = _t(NewsletterPage::class . '.DEFAULT_URLSEGMENT', 'newsletter');
        $newsletterPage->ParentID       = $serviceNavigationHolder->ID;
        $newsletterPage->IdentifierCode = Page::IDENTIFIER_NEWSLETTER_PAGE;
        $newsletterPage->OptInPageTitle             = NewsletterPage::singleton()->fieldLabel('DefaultOptInPageTitle');
        $newsletterPage->ConfirmationFailureMessage = NewsletterPage::singleton()->fieldLabel('DefaultConfirmationFailureMessage');
        $newsletterPage->ConfirmationSuccessMessage = NewsletterPage::singleton()->fieldLabel('DefaultConfirmationSuccessMessage');
        $newsletterPage->AlreadyConfirmedMessage    = NewsletterPage::singleton()->fieldLabel('DefaultAlreadyConfirmedMessage');
        $newsletterPage->write();
        $newsletterPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        // Sub pages of about node
        $imprintPage                 = MetaNavigationPage::create();
        $imprintPage->Title          = _t(MetaNavigationPage::class . '.DEFAULT_TITLE_IMPRINT', 'imprint');
        $imprintPage->URLSegment     = _t(MetaNavigationPage::class . '.DEFAULT_URLSEGMENT_IMPRINT', 'imprint');
        $imprintPage->ParentID       = $aboutNavigationHolder->ID;
        $imprintPage->IdentifierCode = Page::IDENTIFIER_IMPRINT_PAGE;
        $imprintPage->write();
        $imprintPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        
        $contactPage                  = ContactFormPage::create();
        $contactPage->Title           = _t(ContactFormPage::class . '.DEFAULT_TITLE', 'contact');
        $contactPage->ResponseContent = _t(ContactFormPage::class . '.DEFAULT_RESPONSE_CONTENT', 'Many thanks for Your message. Your request will be answered as soon as possible.');
        $contactPage->URLSegment      = _t(ContactFormPage::class . '.DEFAULT_URLSEGMENT', 'contact');
        $contactPage->IdentifierCode  = Page::IDENTIFIER_CONTACT_FORM_PAGE;
        $contactPage->ParentID        = $aboutNavigationHolder->ID;
        $contactPage->write();
        $contactPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        // Sub pages of shop node
        $silvercartDePage                  = RedirectorPage::create();
        $silvercartDePage->RedirectionType = 'External';
        $silvercartDePage->ExternalURL     = 'http://www.silvercart.de';
        $silvercartDePage->Title           = 'silvercart.de';
        $silvercartDePage->URLSegment      = 'silvercart-de';
        $silvercartDePage->ParentID        = $shopNavigationHolder->ID;
        $silvercartDePage->write();
        $silvercartDePage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        
        $silvercartOrgPage                  = RedirectorPage::create();
        $silvercartOrgPage->RedirectionType = 'External';
        $silvercartOrgPage->ExternalURL     = 'http://www.silvercart.org';
        $silvercartOrgPage->Title           = 'silvercart.org';
        $silvercartOrgPage->URLSegment      = 'silvercart-org';
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
    public function createDefaultSiteTreeMyAccountSection(SiteTree $parentPage)
    {
        $myAccountHolder                    = MyAccountHolder::create();
        $myAccountHolder->Title             = _t(MyAccountHolder::class . '.DEFAULT_TITLE', 'my account');
        $myAccountHolder->URLSegment        = _t(MyAccountHolder::class . '.DEFAULT_URLSEGMENT', 'my-account');
        $myAccountHolder->ShowInSearch      = false;
        $myAccountHolder->ParentID          = $parentPage->ID;
        $myAccountHolder->IdentifierCode    = Page::IDENTIFIER_MY_ACCOUNT_HOLDER;
        $myAccountHolder->InheritFromParent = false;
        $myAccountHolder->write();
        $myAccountHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        //create a silvercart data page as a child of silvercart my account holder
        $dataPage                   = CustomerDataPage::create();
        $dataPage->Title            = _t(CustomerDataPage::class . '.DEFAULT_TITLE', 'my data');
        $dataPage->URLSegment       = _t(CustomerDataPage::class . '.DEFAULT_URLSEGMENT', 'my-data');
        $dataPage->ShowInSearch     = false;
        $dataPage->CanViewType      = "Inherit";
        $dataPage->ParentID         = $myAccountHolder->ID;
        $dataPage->IdentifierCode   = Page::IDENTIFIER_CUSTOMER_DATA_PAGE;
        $dataPage->write();
        $dataPage->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        //create a silvercart order holder as a child of silvercart my account holder
        $orderHolder                    = OrderHolder::create();
        $orderHolder->Title             = _t(OrderHolder::class . '.DEFAULT_TITLE', 'my orders');
        $orderHolder->URLSegment        = _t(OrderHolder::class . '.DEFAULT_URLSEGMENT', 'my-orders');
        $orderHolder->ShowInSearch      = false;
        $orderHolder->CanViewType       = "Inherit";
        $orderHolder->ParentID          = $myAccountHolder->ID;
        $orderHolder->IdentifierCode    = Page::IDENTIFIER_ORDER_HOLDER;
        $orderHolder->write();
        $orderHolder->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);

        //create a silvercart address holder as a child of silvercart my account holder
        $addressHolder                  = AddressHolder::create();
        $addressHolder->Title           = _t(AddressHolder::class . '.DEFAULT_TITLE', 'address overview');
        $addressHolder->URLSegment      = _t(AddressHolder::class . '.DEFAULT_URLSEGMENT', 'address-overview');
        $addressHolder->ShowInSearch    = false;
        $addressHolder->CanViewType     = "Inherit";
        $addressHolder->ParentID        = $myAccountHolder->ID;
        $addressHolder->IdentifierCode  = Page::IDENTIFIER_ADDRESS_HOLDER;
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
    public function rerenderErrorPages()
    {
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
     */
    public function increaseSilverCartVersion() : void
    {
        $defaults = SiteConfig::config()->defaults;
        $config   = Config::getConfig();
        $config->SilvercartVersion      = $defaults['SilvercartVersion'];
        $config->SilvercartMinorVersion = $defaults['SilvercartMinorVersion'];
        $config->write();
    }
    
    /**
     * Creates the default records.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.10.2017
     */
    public static function require_default_records()
    {
        self::singleton()->requireDefaultRecords();
    }
    
    /**
     * Creates the default countries.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.05.2018
     */
    public static function require_default_countries()
    {
        if (!Country::get()->exists()) {
            require_once(__DIR__ . '/RequireDefaultCountries.php');
            list($lang,$iso) = explode('_', i18n::get_locale());
            $country = Country::get()->filter('ISO2', $iso)->first();
            if ($country instanceof Country
             && $country->exists()
            ) {
                $country->Active = true;
                $country->write();
            }
        }
    }

    /**
     * create default records.
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.02.2013
     */
    public function requireDefaultRecords()
    {
        self::require_default_countries();
        $this->createDefaultGroups();
        $this->createDefaultConfig();
        $this->createDefaultOrderStatus();
        $this->createDefaultPaymentStatus();
        $this->createDefaultAvailabilityStatus();
        $this->createDefaultNumberRanges();
        $rootPage = $this->createDefaultSiteTree();
        $this->rerenderErrorPages();
        $this->increaseSilverCartVersion();

        $this->extend('updateDefaultRecords', $rootPage);

        self::createTestConfiguration();
        self::createTestData();
        
        $defaultTax = Tax::get()->filter('isDefault', 1)->first();
        if (!($defaultTax instanceof Tax)
         || !$defaultTax->exists()
        ) {
            $defaultTax = Tax::get()->first();
            if ($defaultTax instanceof Tax
             && $defaultTax->exists()
            ) {
                $defaultTax->isDefault = true;
                $defaultTax->write();
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
    public function publishSiteTree($parentID = 0)
    {
        $translationLocale = $this->getTranslationLocale();
        Versioned::set_reading_mode('Stage.Stage');
        $pages = SiteTree::get()->filter(["ParentID" => $parentID, "Locale" => $translationLocale]);
        if ($pages->exists()) {
            foreach ($pages as $page) {
                $page->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
                $this->publishSiteTree($page->ID);
            }
        }
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
    public static function doPublishSiteTree($locale)
    {
        $obj = RequireDefaultRecords::create();
        $obj->setTranslationLocale($locale);
        $obj->publishSiteTree();
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
        self::$enableTestData = true;
    }

    /**
     * determine weather test data is enabled or not
     *
     * @return bool is test data enabled?
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.3.2011
     */
    public static function isEnabledTestData()
    {
        return self::$enableTestData === true;
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
            $productGroupHolder = ProductGroupHolder::get()->first();
            $taxRateID          = Tax::get()->filter('Rate', '19')->first()->ID;

            //create a manufacturer
            $manufacturer = Manufacturer::create();
            $manufacturer->Title = 'pixeltricks GmbH';
            $manufacturer->URL = 'http://www.pixeltricks.de/';
            $manufacturer->write();
            
            //create product groups
            $productGroupPayment = ProductGroupPage::create();
            $productGroupPayment->Title = _t(RequireDefaultRecords::class . '.PRODUCTGROUPPAYMENT_TITLE', 'Payment Modules');
            $productGroupPayment->URLSegment = _t(RequireDefaultRecords::class . '.PRODUCTGROUPPAYMENT_URLSEGMENT', 'payment-modules');
            $productGroupPayment->Content = _t(RequireDefaultRecords::class . '.PRODUCTGROUP_CONTENT', '<div class="alert alert-warning"><strong><span class="fa fa-info-circle"></span> Please note:</strong><br/>These modules are available for free. Prices are for demo purposes only.</div>');
            $productGroupPayment->IdentifierCode = 'SilvercartProductGroupPayment';
            $productGroupPayment->ParentID = $productGroupHolder->ID;
            $productGroupPayment->Sort = 1;
            $productGroupPayment->InheritFromParent = true;
            $productGroupPayment->write();
            $productGroupPayment->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
            
            $productGroupMarketing = ProductGroupPage::create();
            $productGroupMarketing->Title = _t(RequireDefaultRecords::class . '.PRODUCTGROUPMARKETING_TITLE', 'Marketing Modules');
            $productGroupMarketing->URLSegment = _t(RequireDefaultRecords::class . '.PRODUCTGROUPMARKETING_URLSEGMENT', 'marketing-modules');
            $productGroupMarketing->Content = _t(RequireDefaultRecords::class . '.PRODUCTGROUP_CONTENT', '<div class="alert alert-warning"><strong><span class="fa fa-info-circle"></span> Please note:</strong><br/>These modules are available for free. Prices are for demo purposes only.</div>');
            $productGroupMarketing->IdentifierCode = 'SilvercartproductGroupMarketing';
            $productGroupMarketing->ParentID = $productGroupHolder->ID;
            $productGroupMarketing->Sort = 2;
            $productGroupMarketing->InheritFromParent = true;
            $productGroupMarketing->write();
            $productGroupMarketing->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
            
            $productGroupOthers = ProductGroupPage::create();
            $productGroupOthers->Title = _t(RequireDefaultRecords::class . '.PRODUCTGROUPOTHERS_TITLE', 'Other Modules');
            $productGroupOthers->URLSegment = _t(RequireDefaultRecords::class . '.PRODUCTGROUPOTHERS_URLSEGMENT', 'other-modules');
            $productGroupOthers->Content = _t(RequireDefaultRecords::class . '.PRODUCTGROUP_CONTENT', '<div class="alert alert-warning"><strong><span class="fa fa-info-circle"></span> Please note:</strong><br/>These modules are available for free. Prices are for demo purposes only.</div>');
            $productGroupOthers->IdentifierCode = 'SilvercartproductGroupOthers';
            $productGroupOthers->ParentID = $productGroupHolder->ID;
            $productGroupOthers->Sort = 3;
            $productGroupOthers->InheritFromParent = true;
            $productGroupOthers->write();
            $productGroupOthers->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
            
            // Define products
            $products = [
                [
                    'en_US' => [
                        'Title'            => 'Paypal',
                        'ShortDescription' => 'The world' . "'" . 's most loved way to pay and get paid.',
                        'LongDescription'  => 'PayPal works behind the scenes to help protect you and your customers. Your customers will love the speed of PayPal streamlined checkout experience. And you will love the sales boost PayPal can deliver. PayPal is ideal for selling overseas. You can accept payments in 22 currencies from 190 countries and markets worldwide. Source: www.paypal.com',
                        'MetaDescription'  => 'The world' . "'" . 's most loved way to pay and get paid.',
                        'MetaTitle'        => 'Paypal'
                    ],
                    'en_GB' => [
                        'Title'            => 'Paypal',
                        'ShortDescription' => 'The world' . "'" . 's most loved way to pay and get paid.',
                        'LongDescription'  => 'PayPal works behind the scenes to help protect you and your customers. Your customers will love the speed of PayPal streamlined checkout experience. And you will love the sales boost PayPal can deliver. PayPal is ideal for selling overseas. You can accept payments in 22 currencies from 190 countries and markets worldwide. Source: www.paypal.com',
                        'MetaDescription'  => 'The world' . "'" . 's most loved way to pay and get paid.',
                        'MetaTitle'        => 'Paypal'
                    ],
                    'de_DE' => [
                        'Title'            => 'Paypal',
                        'ShortDescription' => 'PayPal ist sicherererer. Für Daten, für Einkäufe - Für alles',
                        'LongDescription'  => 'PayPal für Ihren Shop Sie haben einen Online-Shop und fragen sich, warum Sie PayPal anbieten sollen? Ganz einfach: Ihre Kunden bezahlen mit nur zwei Klicks. Sie schließen den Kauf zufrieden ab, kommen gerne wieder - und Sie steigern Ihren Umsatz! Das kann PayPal für Sie tun – und mehr!',
                        'MetaDescription'  => 'PayPal ist sicherererer. Für Daten, für Einkäufe - Für alles',
                        'MetaTitle'        => 'Paypal'
                    ],
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
                ],
                [
                    'en_US' => [
                        'Title'            => 'iPayment',
                        'ShortDescription' => 'iPayment is one of the largest providers of credit and debit card-based payment processing services in the country, processing more than $30 billion in credit and debit card volume annually.',
                        'LongDescription'  => '<p>Receive best in class service no matter what size your business is, with iPayment. We’re committed to making your business more successful by delivering credit and debit card-based payment processing services that are customized to suit your needs.</p><ul><li>Major credit cards: MasterCard®, Visa®, American Express®, Discover® and JCB®</li><li>PIN-secured and signature debit cards</li><li>Gift and loyalty cards</li><li>Petroleum services</li><li>Paper and electronic check services</li><li>Cash advance funding program</li></ul><p><small>Source: www.ipaymentinc.com/</small></p>',
                        'MetaDescription'  => 'iPayment is one of the largest providers of credit and debit card-based payment processing services in the country, processing more than $30 billion in credit and debit card volume annually.',
                        'MetaTitle'        => 'iPayment'
                    ],
                    'en_GB' => [
                        'Title'            => 'iPayment',
                        'ShortDescription' => 'iPayment is one of the largest providers of credit and debit card-based payment processing services in the country, processing more than $30 billion in credit and debit card volume annually.',
                        'LongDescription'  => '<p>Receive best in class service no matter what size your business is, with iPayment. We’re committed to making your business more successful by delivering credit and debit card-based payment processing services that are customized to suit your needs.</p><ul><li>Major credit cards: MasterCard®, Visa®, American Express®, Discover® and JCB®</li><li>PIN-secured and signature debit cards</li><li>Gift and loyalty cards</li><li>Petroleum services</li><li>Paper and electronic check services</li><li>Cash advance funding program</li></ul><p><small>Source: www.ipaymentinc.com/</small></p>',
                        'MetaDescription'  => 'iPayment is one of the largest providers of credit and debit card-based payment processing services in the country, processing more than $30 billion in credit and debit card volume annually.',
                        'MetaTitle'        => 'iPayment'
                    ],
                    'de_DE' => [
                        'Title'            => 'iPayment',
                        'ShortDescription' => 'iPayment unterstützt Ihren Geschäftserfolg im Internet, indem es Ihren Kunden die sichere Bezahlung per Kreditkarte, internetbasiertem elektronischen Lastschriftverfahren und weiteren Zahlungsmedien ermöglicht.',
                        'LongDescription'  => 'ipayment unterstützt Ihren Geschäftserfolg im Internet, indem es Ihren Kunden die sichere Bezahlung per Kreditkarte, internetbasiertem elektronischen Lastschriftverfahren und weiteren Zahlungsmedien ermöglicht. Je nach genutztem Zahlungsanbieter können Sie Ihren Kunden über ipayment die Bezahlung mit folgenden Zahlungsmedien anbieten: Visa MasterCard Maestro American Express JCB Diners Club Visa Electron Solo Internetbasiertes Elektronisches Lastschriftverfahren (ELV) paysafecard Das Unternehmen, über das Sie Ihre Onlinezahlungen abwickeln möchten, können Sie dabei selbst auswählen - ipayment verfügt über Schnittstellen zu den wichtigsten Zahlungsanbietern. Sie schließen den Akzeptanzvertrag mit dem Anbieter Ihrer Wahl - ipayment sorgt für die reibungslose und sichere Abwicklung! Dazu nimmt ipayment die Zahlungsvorgänge direkt aus Ihrem System auf und verarbeitet sie im Hochleistungsrechenzentrum von 1&1 in Karlsruhe. Selbstverständlich erfüllt ipayment dabei die Zertifizierungsanforderungen gemäß dem PCI DSS (Payment Card Industry Data Security Standard). ',
                        'MetaDescription'  => 'iPayment unterstützt Ihren Geschäftserfolg im Internet, indem es Ihren Kunden die sichere Bezahlung per Kreditkarte, internetbasiertem elektronischen Lastschriftverfahren und weiteren Zahlungsmedien ermöglicht.',
                        'MetaTitle'        => 'iPayment'
                    ],
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
                ],
                [
                    'en_US' => [
                        'Title'            => 'Saferpay',
                        'ShortDescription' => 'Saferpay has set the standard for e-payment solutions in German-speaking Europe.',
                        'LongDescription'  => '<h3>Saferpay e-payment solutions for professionals and beginners</h3><p>Saferpay integrates all popular payment means in your Web shop through a single interface. This makes it easy to make adaptations and upgrades. What’s more, Saferpay enables the secure online processing of written and phone orders.</p><h3>More payment means – more turnover!</h3><p>Boost your turnover by offering a variety of payment means! With Saferpay you can offer your customers all popular payment means through a single interface, flexibly, easily & securely! You can accept all popular credit cards and debit cards with Saferpay and can activate new payment means at any time or deactivate existing ones and thus can flexibly react to your e-commerce requirements.</p><h3>More profit with security!</h3><p>SIX Card Solutions offers you comprehensive solutions from a single source to handle cashless, electronic payment processing as a merchant in e-commerce or in the phone/mail-order business as securely and conveniently as possible. The e-payment solution supports all current security standards. Increase confidence among your customers!</p>',
                        'MetaDescription'  => 'Saferpay has set the standard for e-payment solutions in German-speaking Europe.',
                        'MetaTitle'        => 'Saferpay'
                    ],
                    'en_GB' => [
                        'Title'            => 'Saferpay',
                        'ShortDescription' => 'Saferpay has set the standard for e-payment solutions in German-speaking Europe.',
                        'LongDescription'  => '<h3>Saferpay e-payment solutions for professionals and beginners</h3><p>Saferpay integrates all popular payment means in your Web shop through a single interface. This makes it easy to make adaptations and upgrades. What’s more, Saferpay enables the secure online processing of written and phone orders.</p><h3>More payment means – more turnover!</h3><p>Boost your turnover by offering a variety of payment means! With Saferpay you can offer your customers all popular payment means through a single interface, flexibly, easily & securely! You can accept all popular credit cards and debit cards with Saferpay and can activate new payment means at any time or deactivate existing ones and thus can flexibly react to your e-commerce requirements.</p><h3>More profit with security!</h3><p>SIX Card Solutions offers you comprehensive solutions from a single source to handle cashless, electronic payment processing as a merchant in e-commerce or in the phone/mail-order business as securely and conveniently as possible. The e-payment solution supports all current security standards. Increase confidence among your customers!</p>',
                        'MetaDescription'  => 'Saferpay has set the standard for e-payment solutions in German-speaking Europe.',
                        'MetaTitle'        => 'Saferpay'
                    ],
                    'de_DE' => [
                        'Title'            => 'Saferpay',
                        'ShortDescription' => 'Saferpay hat im deutschsprachigen Europa den Standard für E-Payment-Lösungen gesetzt und steht damit als Synonym für "sicheres Bezahlen im Internet."',
                        'LongDescription'  => '<h3>Saferpay E-Payment-Lösungen für Profis und Einsteiger</h3><p>Saferpay hat im deutschsprachigen Europa den Standard für E-Payment-Lösungen gesetzt und steht damit als Synonym für "sicheres Bezahlen im Internet." Dank Saferpay müssen sich Online-Händler wie Karteninhaber über die Sicherheit beim Einkaufen im Internet keine Sorgen mehr machen. Händler kennen und schätzen das sichere Bezahlen im Internet über Saferpay weltweit.</p><p>Saferpay integriert alle gängigen Zahlungsmittel in Ihren Webshop - über eine einzige Schnittstelle. Dadurch sind Anpassungen und Erweiterungen problemlos umsetzbar. Darüber hinaus ermöglicht Saferpay die sichere Onlineabwicklung von schriftlichen und telefonischen Bestellungen.</p><h3>Mehr Zahlungsmittel – mehr Umsatz!</h3><p>Steigern Sie Ihren Umsatz durch das Angebot einer Vielzahl an Zahlungsmitteln! Mit Saferpay bieten Sie Ihren Kunden alle gängigen Zahlungsmittel über eine einzige Schnittstelle – flexibel, einfach & sicher! Mit Saferpay können Sie alle gängigen Kreditkarten und Debitkarten akzeptieren. Sie können jederzeit neue Zahlungsmittel aufschalten oder bestehende wieder abschalten und somit flexibel auf die Bedürfnisse im E-Commerce reagieren.</p><h3>Mit Sicherheit mehr Gewinn!</h3><p>Um die bargeldlose, elektronische Zahlungsabwicklung für Sie als Händler im E-Commerce oder Phone-/Mail-Order Business so sicher und bequem wie möglich zu machen, bietet die SIX Card Solutions Ihnen als Händler Komplettlösungen aus einer Hand. Die E-Payment-Lösung unterstützt alle heutigen Sicherheitsstandards. Stärken Sie das Vertrauen Ihrer Kunden !</p>',
                        'MetaDescription'  => 'Saferpay hat im deutschsprachigen Europa den Standard für E-Payment-Lösungen gesetzt und steht damit als Synonym für "sicheres Bezahlen im Internet."',
                        'MetaTitle'        => 'Saferpay'
                    ],
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
                ],
                [
                    'en_US' => [
                        'Title'            => 'Prepayment',
                        'ShortDescription' => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic.',
                        'LongDescription'  => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic. This module provides beside prepayment also payment via invoice.',
                        'MetaDescription'  => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic.',
                        'MetaTitle'        => 'Prepayment'
                    ],
                    'en_GB' => [
                        'Title'            => 'Prepayment',
                        'ShortDescription' => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic.',
                        'LongDescription'  => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic. This module provides beside prepayment also payment via invoice.',
                        'MetaDescription'  => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic.',
                        'MetaTitle'        => 'Prepayment'
                    ],
                    'de_DE' => [
                        'Title'            => 'Vorkasse',
                        'ShortDescription' => 'Flexibles Zahlungs-Modul für alle Zahlungsarten, die keine automatisierte Logik erfordern.',
                        'LongDescription'  => 'Flexibles Zahlungs-Modul für alle Zahlungsarten, die keine automatisierte Logik erfordern. Dieses Modul bietet neben der Vorkasse auch Rechnung als Zahlungsart.',
                        'MetaDescription'  => 'Flexibles Zahlungs-Modul für alle Zahlungsarten, die keine automatisierte Logik erfordern.',
                        'MetaTitle'        => 'Vorkasse'
                    ],
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
                ],
                [
                    'en_US' => [
                        'Title'            => 'Cross selling',
                        'ShortDescription' => 'Cross selling is a practice of suggesting related products or services to a customer who is considering buying something.',
                        'LongDescription'  => 'It is a practice of suggesting related products or services to a customer who is considering buying something. Encourage established customers to buy different but related products. Getting a computer buyer to purchase a printer, for example. Source: www.procopytips.com',
                        'MetaDescription'  => 'Cross selling is a practice of suggesting related products or services to a customer who is considering buying something.',
                        'MetaTitle'        => 'Cross selling'
                    ],
                    'en_GB' => [
                        'Title'            => 'Cross selling',
                        'ShortDescription' => 'Cross selling is a practice of suggesting related products or services to a customer who is considering buying something.',
                        'LongDescription'  => 'It is a practice of suggesting related products or services to a customer who is considering buying something. Encourage established customers to buy different but related products. Getting a computer buyer to purchase a printer, for example. Source: www.procopytips.com',
                        'MetaDescription'  => 'Cross selling is a practice of suggesting related products or services to a customer who is considering buying something.',
                        'MetaTitle'        => 'Cross selling'
                    ],
                    'de_DE' => [
                        'Title'            => 'Cross-Selling',
                        'ShortDescription' => 'Kreuzverkauf bezeichnet im Marketing den Verkauf von sich ergänzenden Produkten oder Dienstleistungen.',
                        'LongDescription'  => 'Verkaufs- bzw. Marketinginstrument, bei dem Informationen über bereits existierende Kunden oder über bekanntes Konsumentenverhalten genutzt wird, um zusätzliche Käufe anderer Produkte zu begünstigen. Quelle: www.desig-n.de ',
                        'MetaDescription'  => 'Kreuzverkauf bezeichnet im Marketing den Verkauf von sich ergänzenden Produkten oder Dienstleistungen.',
                        'MetaTitle'        => 'Cross-Selling'
                    ],
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
                ],
                [
                    'en_US' => [
                        'Title'            => 'eKomi',
                        'ShortDescription' => 'Increase sales with eKomi’s trusted independent customer review system!',
                        'LongDescription'  => 'eKomi – The Feedback Company, helps companies through their web-based social SaaS technology with authentic and valuable reviews from customers and helps increasing the customer satisfaction and sales. Generate valuable customer reviews with eKomi' . "'" . 's intelligent, easy to install software and increase sales, trust and customer loyalty. <small>Source: www.ekomi.co.uk</small>',
                        'MetaDescription'  => 'Increase sales with eKomi’s trusted independent customer review system!',
                        'MetaTitle'        => 'eKomi'
                    ],
                    'en_GB' => [
                        'Title'            => 'eKomi',
                        'ShortDescription' => 'Increase sales with eKomi’s trusted independent customer review system!',
                        'LongDescription'  => 'eKomi – The Feedback Company, helps companies through their web-based social SaaS technology with authentic and valuable reviews from customers and helps increasing the customer satisfaction and sales. Generate valuable customer reviews with eKomi' . "'" . 's intelligent, easy to install software and increase sales, trust and customer loyalty. <small>Source: www.ekomi.co.uk</small>',
                        'MetaDescription'  => 'Increase sales with eKomi’s trusted independent customer review system!',
                        'MetaTitle'        => 'eKomi'
                    ],
                    'de_DE' => [
                        'Title'            => 'eKomi',
                        'ShortDescription' => 'Mehr Umsatz und Vertrauen durch unabhängige Kunden- und Produktbewertungen!',
                        'LongDescription'  => 'Beginnen Sie noch heute, durch intelligente Kundenbefragung authentisches und wertvolles Kundenfeedback zu gewinnen und damit Ihre Kundenzufriedenheit und Ihren Umsatz zu steigern. ',
                        'MetaDescription'  => 'Mehr Umsatz und Vertrauen durch unabhängige Kunden- und Produktbewertungen!',
                        'MetaTitle'        => 'eKomi'
                    ],
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
                    'Weight'                    => 345,
                    'StockQuantity'             => 146,
                    'ProductNumberShop'         => '10007',
                    'ProductNumberManufacturer' => 'SC_Mod_105',
                    'ProductGroupID'            => $productGroupMarketing->ID,
                    'productImage'              => 'logoekomi.jpg',
                ],
                [
                    'en_US' => [
                        'Title'            => 'Protected Shops',
                        'ShortDescription' => 'Make your online shop more secure! Try the Protected Shops quality rating system to boost your sales!',
                        'LongDescription'  => 'In the online business you will be confronted with unmanageable specifications which can be very expensive if you breach the conditions. Protected Shops offers a quality rating system to boost your sales. 67% of customers trust in a indepented shop ratings. Use the Vote connect interface of Protected Shops to integrate the quality rating system provided by Protected Shops into SilverCart.',
                        'MetaDescription'  => 'Make your online shop more secure! Try the Protected Shops quality rating system to boost your sales!',
                        'MetaTitle'        => 'Protected Shops'
                    ],
                    'en_GB' => [
                        'Title'            => 'Protected Shops',
                        'ShortDescription' => 'Make your online shop more secure! Try the Protected Shops quality rating system to boost your sales!',
                        'LongDescription'  => 'In the online business you will be confronted with unmanageable specifications which can be very expensive if you breach the conditions. Protected Shops offers a quality rating system to boost your sales. 67% of customers trust in a indepented shop ratings. Use the Vote connect interface of Protected Shops to integrate the quality rating system provided by Protected Shops into SilverCart.',
                        'MetaDescription'  => 'Make your online shop more secure! Try the Protected Shops quality rating system to boost your sales!',
                        'MetaTitle'        => 'Protected Shops'
                    ],
                    'de_DE' => [
                        'Title'            => 'Protected Shops',
                        'ShortDescription' => 'Machen Sie Ihr Online-Business sicherer! Wer im Internet handelt, kann seinen Umsatz durch das Protected Shops Bewertungssystem steigern. ',
                        'LongDescription'  => 'Wer im Internet handelt, ist mit einer unüberschaubaren Menge rechtlicher Vorgaben konfrontiert, die bei Nichteinhaltung zu einem teuren Unterfangen werden können. Gerade von Konkurrenten, die ihren Mitbewerb durch teuere Abmahnungen zu schädigen versuchen, geht für Ihr Unternehmen eine große Gefahr aus. Wer im Internet handelt, kann seinen Umsatz durch das Protected Shops Bewertungssystem steigern. 67% der Online Käufer vertrauen auf Online-Konsumentenbewertungen (Quelle: www.nielsen.com vom 24.07.2009). Mit unserer Vote Connect Schnittstelle integrieren Sie das Protected Shops Kundenbewertungssystem in Ihren Shop. ',
                        'MetaDescription'  => 'Machen Sie Ihr Online-Business sicherer! Wer im Internet handelt, kann seinen Umsatz durch das Protected Shops Bewertungssystem steigern. ',
                        'MetaTitle'        => 'Protected Shops'
                    ],
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
                ],
                [
                    'en_US' => [
                        'Title'            => 'DHL',
                        'ShortDescription' => 'Packet interface for the shipping provider DHL (EasyLog)',
                        'LongDescription'  => 'Packet interface for the shipping provider DHL. Interface to export ordernumbers into Easylog and import tracking numbers back into SilverCart.',
                        'MetaDescription'  => 'Packet interface for the shipping provider DHL (EasyLog)',
                        'MetaTitle'        => 'DHL'
                    ],
                    'en_GB' => [
                        'Title'            => 'DHL',
                        'ShortDescription' => 'Packet interface for the shipping provider DHL (EasyLog)',
                        'LongDescription'  => 'Packet interface for the shipping provider DHL. Interface to export ordernumbers into Easylog and import tracking numbers back into SilverCart.',
                        'MetaDescription'  => 'Packet interface for the shipping provider DHL (EasyLog)',
                        'MetaTitle'        => 'DHL'
                    ],
                    'de_DE' => [
                        'Title'            => 'DHL',
                        'ShortDescription' => 'Paketschnittstelle zum Versandanbieter DHL (Easylog)',
                        'LongDescription'  => 'Paketschnittstelle zum Versandanbieter DHL für den Export von Bestellungen nach Easylog und den Import von Sendungsnachverfolgungsnummern in SilverCart.',
                        'MetaDescription'  => 'Paketschnittstelle zum Versandanbieter DHL (Easylog)',
                        'MetaTitle'        => 'DHL'
                    ],
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
                ],
                [
                    'en_US' => [
                        'Title'            => 'PDF Invoice',
                        'ShortDescription' => 'Automatically generate PDF invoices',
                        'LongDescription'  => 'Automatically generated purchase order as PDF file.',
                        'MetaDescription'  => 'Automatically generate PDF invoices',
                        'MetaTitle'        => 'PDF Invoice'
                    ],
                    'en_GB' => [
                        'Title'            => 'PDF Invoice',
                        'ShortDescription' => 'Automatically generate PDF invoices',
                        'LongDescription'  => 'Automatically generated purchase order as PDF file.',
                        'MetaDescription'  => 'Automatically generate PDF invoices',
                        'MetaTitle'        => 'PDF Invoice'
                    ],
                    'de_DE' => [
                        'Title'            => 'PDF-Rechnung',
                        'ShortDescription' => 'Automatische Generierung von PDF-Rechnungen',
                        'LongDescription'  => 'Erstellt automatisiert PDF-Rechnungen bei Bestellungen.',
                        'MetaDescription'  => 'Automatische Generierung von PDF-Rechnungen',
                        'MetaTitle'        => 'PDF-Rechnung'
                    ],
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
                ],
                [
                    'en_US' => [
                        'Title'            => 'Vouchers',
                        'ShortDescription' => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'LongDescription'  => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'MetaDescription'  => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'MetaTitle'        => 'Vouchers'
                    ],
                    'en_GB' => [
                        'Title'            => 'Vouchers',
                        'ShortDescription' => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'LongDescription'  => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'MetaDescription'  => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'MetaTitle'        => 'Vouchers'
                    ],
                    'de_DE' => [
                        'Title'            => 'Gutscheine',
                        'ShortDescription' => 'Gutscheinerstellung mit prozentualem oder absolutem Rabatt sowie Warengutscheinen.',
                        'LongDescription'  => 'Gutscheinerstellung mit prozentualem oder absolutem Rabatt sowie Warengutscheinen.',
                        'MetaDescription'  => 'Gutscheinerstellung mit prozentualem oder absolutem Rabatt sowie Warengutscheinen.',
                        'MetaTitle'        => 'Gutscheine'
                    ],
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
                ]
            ];
            
            // Create folder for product images
            $exampleDataDir = Director::publicFolder() . '/assets/test-images/';
            $imageFolder = Folder::create();
            $imageFolder->Name = 'test-images';
            $imageFolder->write();
            
            if (!file_exists((string) $exampleDataDir)) {
                mkdir($exampleDataDir);
            }
            
            $locales        = ['de_DE', 'en_GB', 'en_US'];
            $fallbackLocale = false;

            if (!in_array(Tools::current_locale(), $locales)) {
                $locales[]      = Tools::current_locale();
                $fallbackLocale = Tools::current_locale();
            }

            // Create products
            foreach ($products as $product) {
                $productItem                            = Product::create();
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
                    $language = ProductTranslation::get()->filter([
                        'ProductID' => $productItem->ID,
                        'Locale' => $locale,
                    ])->first();
                    if (!$language) {
                        $language = ProductTranslation::create();
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
                    $filePath   = SILVERCART_IMG_PATH . DIRECTORY_SEPARATOR . 'exampledata'  . DIRECTORY_SEPARATOR . $product['productImage'];
                    $fileHash   = sha1_file($filePath);
                    $hashDir    = substr($fileHash, 0, 10);
                    $targetPath = $exampleDataDir . $hashDir;
                    if (!file_exists((string) $targetPath)) {
                        mkdir($targetPath);
                    }
                    copy(
                        $filePath,
                        $targetPath . DIRECTORY_SEPARATOR . $product['productImage']
                    );

                    $productImage = Image::create();
                    $productImage->Name         = $product['productImage'];
                    $productImage->FileFilename = 'test-images/' . $product['productImage'];
                    $productImage->FileHash     = $fileHash;
                    $productImage->ParentID     = $imageFolder->ID;
                    $productImage->write();
                    $productImage->doPublish();

                    $silvercartImage = \SilverCart\Model\Product\Image::create();
                    $silvercartImage->ProductID = $productItem->ID;
                    $silvercartImage->ImageID = $productImage->ID;
                    $silvercartImage->write();
                }
            }
            
            // create widget sets
            $widgetSetFrontPageContentArea = WidgetArea::create();
            $widgetSetFrontPageContentArea->write();
            
            $widgetSetFrontPageContent = WidgetSet::create();
            $widgetSetFrontPageContent->setField('Title', _t(RequireDefaultRecords::class . '.WIDGETSET_FRONTPAGE_CONTENT_TITLE', 'Frontpage content area'));
            $widgetSetFrontPageContent->setField('WidgetAreaID', $widgetSetFrontPageContentArea->ID);
            $widgetSetFrontPageContent->write();
            
            $widgetSetFrontPageSidebarArea = WidgetArea::create();
            $widgetSetFrontPageSidebarArea->write();
            
            $widgetSetFrontPageSidebar = WidgetSet::create();
            $widgetSetFrontPageSidebar->setField('Title', _t(RequireDefaultRecords::class . '.WIDGETSET_FRONTPAGE_SIDEBAR_TITLE', 'Frontpage sidebar area'));
            $widgetSetFrontPageSidebar->setField('WidgetAreaID', $widgetSetFrontPageSidebarArea->ID);
            $widgetSetFrontPageSidebar->write();
            
            $widgetSetProductGroupPagesSidebarArea = WidgetArea::create();
            $widgetSetProductGroupPagesSidebarArea->write();
            
            $widgetSetProductGroupPagesSidebar = WidgetSet::create();
            $widgetSetProductGroupPagesSidebar->setField('Title', _t(RequireDefaultRecords::class . '.WIDGETSET_PRODUCTGROUPPAGES_SIDEBAR_TITLE', 'product group pages sidebar area'));
            $widgetSetProductGroupPagesSidebar->setField('WidgetAreaID', $widgetSetProductGroupPagesSidebarArea->ID);
            $widgetSetProductGroupPagesSidebar->write();
            
            // Attribute widget sets to pages
            $frontPage = Tools::PageByIdentifierCode(Page::IDENTIFIER_FRONT_PAGE);
            
            if ($frontPage) {
                $frontPage->WidgetSetContent()->add($widgetSetFrontPageContent);
                $frontPage->WidgetSetSidebar()->add($widgetSetFrontPageSidebar);
            }
            
            $productGroupHolderPage = Tools::PageByIdentifierCode(Page::IDENTIFIER_PRODUCT_GROUP_HOLDER);
            
            if ($productGroupHolderPage) {
                $productGroupHolderPage->WidgetSetSidebar()->add($widgetSetProductGroupPagesSidebar);
            }
            
            // Create Widgets
            $widgetFrontPageContent1 = ProductGroupItemsWidget::create();
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
            $widgetFrontPageContent1->doPublish();
            
            $widgetFrontPageContent2 = ProductGroupItemsWidget::create();
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
            $widgetFrontPageContent2->doPublish();
            
            $widgetFrontPageContent3 = ImageSliderWidget::create();
            $widgetFrontPageContent3->setField('buildArrows', 0);
            $widgetFrontPageContent3->setField('buildNavigation', 1);
            $widgetFrontPageContent3->setField('buildStartStop', 0);
            $widgetFrontPageContent3->setField('slideDelay', 10000);
            $widgetFrontPageContent3->setField('transitionEffect', 'fade');
            $widgetFrontPageContent3->setField('Sort', 1);
            $widgetFrontPageContent3->write();

            $widgetSetFrontPageContentArea->Widgets()->add($widgetFrontPageContent3);
            
            $filePath   = SILVERCART_IMG_PATH . DIRECTORY_SEPARATOR . 'exampledata'  . DIRECTORY_SEPARATOR . 'silvercart_teaser.jpg';
            $fileHash   = sha1_file($filePath);
            $hashDir    = substr($fileHash, 0, 10);
            $targetPath = $exampleDataDir . $hashDir;
            if (!file_exists((string) $targetPath)) {
                mkdir($targetPath);
            }
            copy(
                $filePath,
                $targetPath . DIRECTORY_SEPARATOR . 'silvercart_teaser.jpg'
            );

            $teaserImage = Image::create();
            $teaserImage->Name         = 'silvercart_teaser.jpg';
            $teaserImage->FileFilename = 'test-images/' . 'silvercart_teaser.jpg';
            $teaserImage->FileHash     = $fileHash;
            $teaserImage->ParentID     = $imageFolder->ID;
            $teaserImage->write();
            $teaserImage->doPublish();

            $slideImage = ImageSliderImage::create();
            $slideImage->setField('ImageID', $teaserImage->ID);
            $slideImage->write();
            $sliderImageTranslations = [
                'en_GB' => 'SilverCart Teaser',
                'en_US' => 'SilverCart Teaser',
                'de_DE' => 'SilverCart Teaser'
            ];

            if ($fallbackLocale !== false) {
                $sliderImageTranslations[$fallbackLocale] = $sliderImageTranslations['en_US'];
            }

            foreach ($sliderImageTranslations as $locale => $translation) {
                $translationObj = ImageSliderImageTranslation::get()->filter('Locale', $locale)->first();
                if (!$translationObj) {
                    $translationObj = ImageSliderImageTranslation::create();
                    $translationObj->Locale = $locale;
                    $translationObj->ImageSliderImageID = $slideImage->ID;
                }
                $translationObj->Title = $translation;
                $translationObj->write();
            }
            
            $widgetFrontPageContent3->slideImages()->add($slideImage);
            $widgetFrontPageContent3->doPublish();

            $widgetFrontPageSidebar1 = ProductGroupItemsWidget::create();
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
            $widgetFrontPageSidebar1->doPublish();
            
            $widgetFrontPageSidebar2 = ShoppingCartWidget::create();
            $widgetFrontPageSidebar2->setField('Sort', 1);
            $widgetFrontPageSidebar2->write();
            $widgetSetFrontPageSidebarArea->Widgets()->add($widgetFrontPageSidebar2);
            $widgetFrontPageSidebar2->doPublish();
            
            $widgetFrontPageSidebar3 = LoginWidget::create();
            $widgetFrontPageSidebar3->setField('Sort', 2);
            $widgetFrontPageSidebar3->write();
            $widgetSetFrontPageSidebarArea->Widgets()->add($widgetFrontPageSidebar3);
            $widgetFrontPageSidebar3->doPublish();
            
            // product group page widgets
            $widgetProductGroupPageSidebar1 = ProductGroupItemsWidget::create();
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
            $widgetProductGroupPageSidebar1->doPublish();

            $widgetProductGroupPageSidebar2 = ShoppingCartWidget::create();
            $widgetProductGroupPageSidebar2->setField('Sort', 1);
            $widgetProductGroupPageSidebar2->write();
            $widgetSetProductGroupPagesSidebarArea->Widgets()->add($widgetProductGroupPageSidebar2);
            $widgetProductGroupPageSidebar2->doPublish();

            $widgetProductGroupPageSidebar3 = LoginWidget::create();
            $widgetProductGroupPageSidebar3->setField('Sort', 2);
            $widgetProductGroupPageSidebar3->write();
            $widgetSetProductGroupPagesSidebarArea->Widgets()->add($widgetProductGroupPageSidebar3);
            $widgetProductGroupPageSidebar3->doPublish();
            
            return true;
        }
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
                
                $carrier = Carrier::create();
                $carrier->Title = 'DHL';
                $carrier->FullTitle = 'DHL International GmbH';
                $carrier->write();
                $carrierTranslations = [
                    'en_GB' => [
                        'Title' => 'DHL',
                        'FullTitle' => 'DHL International GmbH'
                    ],
                    'en_US' => [
                        'Title' => 'DHL',
                        'FullTitle' => 'DHL International GmbH'
                    ],
                    'de_DE' => [
                        'Title' => 'DHL',
                        'FullTitle' => 'DHL International GmbH'
                    ]
                ];
            
                $locales        = ['de_DE', 'en_GB', 'en_US'];
                $fallbackLocale = false;

                if (!in_array(Tools::current_locale(), $locales)) {
                    $locales[]      = Tools::current_locale();
                    $fallbackLocale = Tools::current_locale();
                }

                if ($fallbackLocale !== false) {
                    $carrierTranslations[$fallbackLocale] = $carrierTranslations['en_US'];
                }

                foreach ($carrierTranslations as $locale => $attributes) {
                    $languageObj = CarrierTranslation::get()->filter([
                        'CarrierID' => $carrier->ID,
                        'Locale' => $locale,
                    ])->first();
                    if (!$languageObj) {
                        $languageObj = CarrierTranslation::create();
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
                    $zones = [
                        [
                            'en_GB' => 'Domestic',
                            'en_US' => 'Domestic',
                            'de_DE' => 'Inland'
                        ],
                        [
                            'en_GB' => 'EU',
                            'en_US' => 'European Union',
                            'de_DE' => 'EU'
                        ],
                    ];

                    $locales        = ['de_DE', 'en_GB', 'en_US'];
                    $fallbackLocale = false;

                    if (!in_array(Tools::current_locale(), $locales)) {
                        $locales[]      = Tools::current_locale();
                        $fallbackLocale = Tools::current_locale();
                    }

                    if ($fallbackLocale !== false) {
                        $zones[0][$fallbackLocale] = $zones[0]['en_US'];
                        $zones[1][$fallbackLocale] = $zones[1]['en_US'];
                    }
                    
                    foreach ($zones as $zone) {
                        $zoneObj = Zone::create();
                        $zoneObj->write();
                        $zoneObj->Carriers()->add($carrier);
                        $zoneObj->write();
                        foreach ($zone as $locale => $title) {
                            $zoneTranslation = ZoneTranslation::get()->filter([
                                'ZoneID' => $zoneObj->ID,
                                'Locale' => $locale,
                            ])->first();
                            if (!$zoneTranslation) {
                                $zoneTranslation = ZoneTranslation::create();
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
                        $country = Country::create();
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
                        $paymentMethodHandler = PaymentMethod::create();
                        $paymentMethodHandler->requireDefaultRecords();
                    }
                    $paymentMethod = \SilverCart\Prepayment\Model\PaymentPrepayment::get()->first();
                    $paymentMethod->isActive = true;
                    $paymentStatusOpen = PaymentStatus::get()->filter('Code', 'open')->first();
                    if ($paymentStatusOpen) {
                        $paymentMethod->PaymentStatusID = $paymentStatusOpen->ID;
                    }
                    $paymentMethod->write();
                    $country->PaymentMethods()->add($paymentMethod);
                }

                // create a shipping method
                $shippingMethod = ShippingMethod::get()->first();
                if (!$shippingMethod) {
                    $shippingMethod = ShippingMethod::create();
                    //relate shipping method to carrier
                    $shippingMethod->CarrierID = $carrier->ID;
                }
                $shippingMethod->isActive = 1;
                $shippingMethod->write();
                $shippingMethod->Zones()->add($zoneDomestic);
                
                
                //create the language objects for the shipping method
                $shippingMethodTranslations = [
                    'de_DE' => 'Paket',
                    'en_GB' => 'Package',
                    'en_US' => 'Package'
                ];

                $locales        = ['de_DE', 'en_GB', 'en_US'];
                $fallbackLocale = false;

                if (!in_array(Tools::current_locale(), $locales)) {
                    $locales[]      = Tools::current_locale();
                    $fallbackLocale = Tools::current_locale();
                }

                if ($fallbackLocale !== false) {
                    $shippingMethodTranslations[$fallbackLocale] = $shippingMethodTranslations['en_US'];
                }

                foreach ($shippingMethodTranslations as $locale => $title) {
                    $shippingMethodTranslation = ShippingMethodTranslation::get()->filter([
                        'Locale' => $locale,
                        'ShippingMethodID' => $shippingMethod->ID,
                    ])->first();
                    if (!$shippingMethodTranslation) {
                        $shippingMethodTranslation = ShippingMethodTranslation::create();
                        $shippingMethodTranslation->Locale = $locale;
                        $shippingMethodTranslation->ShippingMethodID = $shippingMethod->ID;
                    }
                    $shippingMethodTranslation->Title = $title;
                    $shippingMethodTranslation->write();
                }

                // create a shipping fee and relate it to zone, tax and shipping method
                $shippingFee = ShippingFee::get()->first();
                if (!$shippingFee) {
                    $shippingFee = ShippingFee::create();
                    $shippingFee->MaximumWeight = '100000';
                    $shippingFee->UnlimitedWeight = true;
                    $shippingFee->Price = DBMoney::create();
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
                $taxrates = [
                    '19' => [
                        'en_US' => '19%',
                        'en_GB' => '19%',
                        'de_DE' => '19%'
                    ],
                    '7' => [
                        'en_US' => '7%',
                        'en_GB' => '7%',
                        'de_DE' => '7%'
                    ],
                ];

                $locales        = ['de_DE', 'en_GB', 'en_US'];
                $fallbackLocale = false;

                if (!in_array(Tools::current_locale(), $locales)) {
                    $locales[]      = Tools::current_locale();
                    $fallbackLocale = Tools::current_locale();
                }

                if ($fallbackLocale !== false) {
                    $taxrates[0][$fallbackLocale] = $taxrates[0]['en_US'];
                    $taxrates[1][$fallbackLocale] = $taxrates[1]['en_US'];
                }
                
                foreach ($taxrates as $taxrate => $languages) {
                    $rateObj = Tax::create();
                    $rateObj->Rate = $taxrate;
                    $rateObj->write();
                    foreach ($languages as $locale => $title) {
                        $rateTranslation = TaxTranslation::get()->filter([
                            'Locale' => $locale,
                            'TaxID' => $rateObj->ID,
                        ])->first();
                        if (!$rateTranslation) {
                            $rateTranslation = TaxTranslation::create();
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