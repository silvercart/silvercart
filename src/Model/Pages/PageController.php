<?php

namespace SilverCart\Model\Pages;

use Psr\SimpleCache\CacheInterface;
use SilverCart\Admin\Model\Config;
use SilverCart\Admin\Model\CookiePolicyConfig;
use SilverCart\Checkout\Checkout;
use SilverCart\Dev\Tools;
use SilverCart\Forms\ChangeLanguageForm;
use SilverCart\Forms\QuickLoginForm;
use SilverCart\Forms\QuickSearchForm;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\OrderPosition;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Pages\CheckoutStep;
use SilverCart\Model\Pages\CheckoutStepController;
use SilverCart\Model\Pages\MetaNavigationHolder;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Pages\ProductGroupHolder;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Widgets\Widget;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\CMS\Controllers\ModelAsController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ErrorPage\ErrorPage;
use SilverStripe\ErrorPage\ErrorPageController;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Queries\SQLSelect;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SilverStripe\View\Requirements;

/**
 * Standard Controller
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PageController extends ContentController {

    /**
     * Prevents recurring rendering of this page's controller.
     *
     * @var array
     */
    public static $instanceMemorizer = array();

    /**
     * Contains HTML code from modules that shall be inserted on the Page.ss
     * template.
     *
     * @var array
     */
    protected static $moduleHtmlInjections = array();
    
    /**
     * Constructor.
     *
     * @param array $dataRecord Data record
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.10.2017
     */
    public function __construct($dataRecord = null) {
        i18n::config()->merge('default_locale', Tools::current_locale());
        i18n::set_locale(Tools::current_locale());
        parent::__construct($dataRecord);
    }
    
    /**
     * Loads all PHP side SilverCart JS requirements.
     * Additional JS files can still be loaded elsewhere.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.07.2018
     */
    public function RequireFullJavaScript() {
        if (Tools::isIsolatedEnvironment()) {
            return;
        }
        $this->extend('onBeforeRequireFullJavaScript');
        Requirements::set_write_js_to_body(true);
        Requirements::javascript('silvercart/silvercart:client/gdpr/jquery.1.9.1.min.js');
        Requirements::javascript('silvercart/silvercart:client/gdpr/jquery-ui.1.10.1.min.js');
        $this->RequireI18nJavaScript();
        $this->RequireCoreJavaScript();
        $this->RequireExtendedJavaScript();
        $this->RequireCookieBannerJavaScript();
        $this->extend('onAfterRequireFullJavaScript');
    }
    
    /**
     * Loads SilverStripe framework i18n.js and registers the SilverCart i18n JS
     * folder.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.07.2018
     */
    public function RequireI18nJavaScript() {
        if (Tools::isIsolatedEnvironment()) {
            return;
        }
        $this->extend('onBeforeRequireI18nJavaScript');
        Requirements::set_write_js_to_body(true);
        Requirements::javascript('silverstripe/admin:client/dist/js/i18n.js');
        Requirements::add_i18n_javascript('silvercart/silvercart:client/javascript/lang');
        $this->extend('onAfterRequireI18nJavaScript');
    }
    
    /**
     * Loads the SilverCart core (default) JS requirements.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.07.2018
     */
    public function RequireCoreJavaScript() {
        if (Tools::isIsolatedEnvironment()) {
            return;
        }
        $jsFilesBefore = [];
        $jsFilesWidget = [];
        $this->extend('onBeforeRequireCoreJavaScript', $jsFilesBefore);
        if (Widget::$use_anything_slider) {
            $jsFilesWidget = [
                'silvercart/silvercart:client/javascript/anythingslider/js/jquery.anythingslider.min.js',
                'silvercart/silvercart:client/javascript/anythingslider/js/jquery.anythingslider.fx.min.js',
                'silvercart/silvercart:client/javascript/anythingslider/js/jquery.easing.1.2.js',
            ];
        }
        $jsFilesCore = array_merge(
                $jsFilesBefore,
                [
                    'silvercart/silvercart:client/javascript/jquery.pixeltricks.tools.js',
                    'silvercart/silvercart:client/javascript/jquery.cookie.js',
                    'silvercart/silvercart:client/javascript/bootstrap.min.js',
                    'silvercart/silvercart:client/javascript/jquery.flexslider-min.js',
                    'silvercart/silvercart:client/javascript/jquery.cycle2.min.js',
                    'silvercart/silvercart:client/javascript/jquery.cycle2.carousel.min.js',
                    'silvercart/silvercart:client/javascript/jquery.cycle2.swipe.min.js',
                    'silvercart/silvercart:client/javascript/fancybox/jquery.fancybox.js',
                    'silvercart/silvercart:client/javascript/custom.js',
                    'silvercart/silvercart:client/javascript/silvercart.js',
                ],
                $jsFilesWidget
        );
        $this->extend('updateRequireCoreJavaScript', $jsFilesCore);
        
        if (count($jsFilesCore) > 0) {
            Requirements::combine_files(
                'sc.core.js',
                $jsFilesCore
            );
        }
        $this->extend('onAfterRequireCoreJavaScript', $jsFilesCore);
    }
    
    /**
     * Loads the SilverCart extended JS requirements.
     * Extended JS files are loaded by modules or custom project extensions
     * using the updateRequireExtendedJavaScript hook.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.07.2018
     */
    public function RequireExtendedJavaScript() {
        if (Tools::isIsolatedEnvironment()) {
            return;
        }
        $jsFilesExt = [];
        $this->extend('updateRequireExtendedJavaScript', $jsFilesExt);
        
        if (count($jsFilesExt) > 0) {
            Requirements::combine_files(
                'sc.ext.js',
                $jsFilesExt
            );
        }
    }
    
    /**
     * Loads the SilverCart cookie policy (banner) JS requirements.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.07.2018
     */
    public function RequireCookieBannerJavaScript() {
        if (Tools::isIsolatedEnvironment()) {
            return;
        }
        CookiePolicyConfig::load_requirements();
    }
    
    /**
     * Requires the color scheme CSS.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2014
     */
    public function RequireColorSchemeCSS() {
        if (!is_null(Config::getConfig()->ColorScheme)) {
            Requirements::themedCSS('client/css/color_' . Config::getConfig()->ColorScheme);
        }
    }
    
    /**
     * Returns custom HTML code to place within the <head> tag, injected by
     * extensions.
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.08.2018
     */
    public function HeadCustomHtmlContent()
    {
        $headCustomHtmlContent = '';
        $this->extend('updateHeadCustomHtmlContent', $headCustomHtmlContent);
        return Tools::string2html($headCustomHtmlContent);
    }
    
    /**
     * Returns custom HTML code to place right after the <body> tag, injected by
     * extensions.
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.08.2018
     */
    public function HeaderCustomHtmlContent()
    {
        $headerCustomHtmlContent = '';
        $this->extend('updateHeaderCustomHtmlContent', $headerCustomHtmlContent);
        return Tools::string2html($headerCustomHtmlContent);
    }
    
    /**
     * Returns custom HTML code to place right before the closing </body> tag, 
     * injected by extensions.
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.08.2018
     */
    public function FooterCustomHtmlContent()
    {
        $footerCustomHtmlContent = '';
        $this->extend('updateFooterCustomHtmlContent', $footerCustomHtmlContent);
        return Tools::string2html($footerCustomHtmlContent);
    }

    /**
     * standard page controller
     *
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    protected function init() {
        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            if (Config::isUserAgentBlacklisted($_SERVER['HTTP_USER_AGENT'])) {
                exit();
            }
        }

        if (array_key_exists($this->ID, self::$instanceMemorizer)) {
            parent::init();
            return true;
        }
        
        Tools::initSession();
        $controller = Controller::curr();
        
        if ($controller == $this || $controller->forceLoadOfWidgets) {
            $this->loadWidgetControllers();
        }
        
        $allParams = Controller::curr()->getRequest()->allParams();
        $customer  = Security::getCurrentUser();
        if (Controller::curr() instanceof Security &&
            array_key_exists('Action', $allParams) &&
            strtolower($allParams['Action']) == 'lostpassword' &&
            $customer instanceof Member) {
            $customer->logOut();
        }
        if (!($customer instanceof Member)) {
            Tools::Session()->set('loggedInAs', 0);
            Tools::saveSession();
        }

        // check the SilverCart configuration
        if (!Tools::isIsolatedEnvironment()) {
            Config::Check();
        }

        // Delete checkout session data if user is not in the checkout process.
        if (get_class($this) != CheckoutStep::class &&
            get_class($this) != CheckoutStepController::class &&
            get_class($this) != ErrorPageController::class &&
            get_class($this) != Security::class &&
            get_class($this) != CheckoutStepController::class &&
            get_class($this) != Security::class &&
            !is_subclass_of(get_class($this), CheckoutStepController::class)
        ) {
            Checkout::clear_session();
        }

        // Decorator can use this method to add custom forms and other stuff
        $this->extend('updateInit');
        
        self::$instanceMemorizer[$this->ID] = true;
        parent::init();
    }
    
    /**
     * Returns the ChangeLanguageForm.
     * 
     * @return ChangeLanguageForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2017
     */
    public function ChangeLanguageForm() {
        $translations = Tools::get_translations($this->data());
        if ($translations instanceof \SilverStripe\ORM\SS_List &&
            $translations->exists()) {
            $form = new ChangeLanguageForm($this);
        }
        return $form;
    }
    
    /**
     * Returns the error response for the given status code.
     * Workaround to include CSS requirements into response HTML code.
     * 
     * @param string $statusCode Status code
     * 
     * @return ContentController
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.05.2015
     */
    public static function error_response_for($statusCode) {
        $response  = null;
        $errorPage = ErrorPage::get()->filter(array(
            "ErrorCode" => $statusCode
        ))->first(); 

        if ($errorPage) {
            $response = ModelAsController::controller_for($errorPage)->handleRequest(new HTTPRequest('GET', ''));
        }
        
        return $response;
    }

    /**
     * Returns the protocol for the current page.
     *
     * @return string
     */
    public function getProtocol() {
        return Director::protocol();
    }

    /**
     * Returns HTML code that has been created by SilverCart modules.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-01-03
     */
    public function ModuleHtmlInjections() {
        $injections = '';

        foreach (self::$moduleHtmlInjections as $injectionId => $injectionCode) {
            $injections .= $injectionCode;
        }

        return $injections;
    }

    /**
     * Saves HTML code for injection on the Page.ss template.
     *
     * @param string $identifier The identifier for the injection
     * @param string $code       The code to inject
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-01-03
     */
    public static function injectHtmlCode($identifier, $code) {
        self::$moduleHtmlInjections[$identifier] = $code;
    }

    /**
     * Indicates wether the site is in live mode.
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.01.2012
     */
    public function isLive() {
        return Director::isLive();
    }
    
    /**
     * template function: returns customers orders
     * 
     * @param int $limit Limit
     *
     * @return DataList
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.10.10
     */
    public function CurrentMembersOrders($limit = null) {
        $customer = Security::getCurrentUser();
        if ($customer instanceof Member) {
            if ($limit) {
                $orders = Order::get()->filter('MemberID', $customer->ID)->limit($limit);
            } else {
                $orders = Order::get()->filter('MemberID', $customer->ID);
            }
            return $orders;
        }
    }
    
    /**
     * Returns the HTML Code of SilverCart errors and clears the error list.
     *
     * @return string
     * 
     * @author Sascha koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2013
     */
    public function ShopErrors() {
        $errorStr = '';
        
        $silvercartSessionErrors = Tools::Session()->get('Silvercart.errors');
        if (is_array($silvercartSessionErrors)) {
            foreach ($silvercartSessionErrors as $error) {
                $errorStr .= '<p>'.$error.'</p>';
            }
            Tools::Session()->set('Silvercart.errors', array());
            Tools::saveSession();
        }
        
        return $errorStr;
    }

    /**
     * Provide permissions
     * 
     * @return array configuration of API permissions
     * 
     * @author Sascha koehler <skoehler@pixeltricks.de>
     * @since 12.10.2010
     */
    public function providePermissions() {
        return array(
            'API_VIEW'   => Page::singleton()->fieldLabel('APIView'),
            'API_CREATE' => Page::singleton()->fieldLabel('APICreate'),
            'API_EDIT'   => Page::singleton()->fieldLabel('APIEdit'),
            'API_DELETE' => Page::singleton()->fieldLabel('APIDelete'),
        );
    }

    /**
     * Function similar to Security::getCurrentUser(); Determines if we deal with a
     * registered customer who has opted in. Returns the member object or false.
     *
     * @return mixed Member|boolean(false)
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2017
     */
    public function CurrentRegisteredCustomer() {
        return Customer::currentRegisteredCustomer();
    }
    
    /**
     * Returns the logout URL.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.08.2018
     */
    public function logoutURL()
    {
        return Security::logout_url();
    }

    /**
     * returns a single page by IdentifierCode
     * used to retrieve links dynamically
     *
     * @param string $identifierCode the classes name
     * 
     * @return SiteTree | false a single object of the site tree; without param the FrontPage will be returned
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public static function PageByIdentifierCode($identifierCode = "SilvercartFrontPage") {
        return Tools::PageByIdentifierCode($identifierCode);
    }

    /**
     * returns a page link by IdentifierCode
     *
     * @param string $identifierCode the DataObjects IdentifierCode
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public static function PageByIdentifierCodeLink($identifierCode = "SilvercartFrontPage") {
        return Tools::PageByIdentifierCodeLink($identifierCode);
    }

    /**
     * Uses the children of ProductGroupHolder to render a subnavigation
     * with the SilverCart/Model/Pages/Includes/SubNavigation.ss template. This is the default sub-
     * navigation.
     *
     * @param string $identifierCode The code of the parent page.
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getSubNavigation($identifierCode = 'SilvercartProductGroupHolder') {
        $output = '';
        $this->extend('updateSubNavigation', $output);
        if (empty($output)) {
            $items              = array();
            $productGroupPage   = Tools::PageByIdentifierCode($identifierCode);

            if ($productGroupPage) {
                foreach ($productGroupPage->Children() as $child) {
                    if ($child->hasmethod('hasProductsOrChildren') &&
                        $child->hasProductsOrChildren()) {
                        $items[] = $child;
                    }
                }
                $elements = array(
                    'SubElements' => new ArrayList($items),
                );
                $output = $this->customise($elements)->renderWith(
                    array(
                        'SilverCart/Model/Pages/Includes/SubNavigation',
                    )
                );
            }
        }
        return Tools::string2html($output);
    }

    /**
     * Returns whether the prices should be shown with the given type (gross/net).
     *
     * @param string $type Price type (gross/net) to check
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2017
     */
    public function showPrices($type) {
        $pricetype  = Config::Pricetype();
        $member     = Customer::currentUser();
        
        if ($member instanceof Member &&
            $member->doesNotHaveToPayTaxes()) {
            $pricetype = 'net';
        }
        
        if ($pricetype == $type) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns whether the prices should be shown gross (including taxes).
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2017
     */
    public function showPricesGross() {
        return $this->showPrices('gross');
    }

    /**
     * Returns whether the prices should be shown net (excluding taxes).
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2017
     */
    public function showPricesNet() {
        return $this->showPrices('net');
    }

    /**
     * Return the given number of topseller products as DataList.
     * 
     * We use caching here, so check the cache first if you don't get the
     * desired results.
     *
     * @param int $nrOfProducts The number of products to return
     *
     * @return mixed DataList|Boolean false
     */
    public function getTopsellerProducts($nrOfProducts = 5) {
        $cachekey = 'TopsellerProducts'.$nrOfProducts;
        $cache    = Injector::inst()->get(CacheInterface::class . '.PageController_getTopsellerProducts');
        $result   = $cache->get($cachekey);

        if ($result) {
            $topsellerProducts = unserialize($result);
        } else {
            $products      = array();
            $sqlSelect     = new SQLSelect();
            $positionTable = Tools::get_table_name(OrderPosition::class);
            $productTable  = Tools::get_table_name(Product::class);

            $sqlSelect->select = array(
                'SOP.ProductID',
                'SUM(SOP.Quantity) AS Quantity'
            );
            $sqlSelect->from = array(
                $positionTable . ' SOP',
                'LEFT JOIN ' . $productTable . ' SP on SP.ID = SOP.ProductID'
            );
            $sqlSelect->where = array(
                'SP.isActive = 1'
            );
            $sqlSelect->groupby = array(
                'SOP.ProductID'
            );
            $sqlSelect->orderby  = 'Quantity DESC';
            $sqlSelect->limit    = $nrOfProducts;

            $result = $sqlSelect->execute();

            foreach ($result as $row) {
                $products[] = Product::get()->byID($row['ProductID']);
            }
            
            $topsellerProducts = new DataList($products);
        }

        return $topsellerProducts;
    }

    /**
     * We load the special offers productgroup page here.
     *
     * @param string $groupIdentifier Identifier of the product group
     * @param int    $nrOfProducts    The number of products to return
     *
     * @return DataList
     */
    public function getProductGroupItems($groupIdentifier = 'SilvercartOffers', $nrOfProducts = 4) {
        $products = array();
        $productTable = Tools::get_table_name(Product::class);
        $pageTable    = Tools::get_table_name(Page::class);

        $records = DB::query(
            sprintf(
                "
                SELECT
                    ProductID
                FROM
                    (
                        SELECT
                            Product.ID AS ProductID
                        FROM
                            %s Product
                        LEFT JOIN
                            %s Page
                        ON
                            Page.ID = Product.ProductGroupID
                        WHERE
                            Page.IdentifierCode = '%s'
                    ) AS DirectRelations
                UNION SELECT
                    ProductID
                FROM
                    (
                        SELECT
                            P_PGMP.ProductID AS ProductID
                        FROM
                            %s_ProductGroupMirrorPages AS P_PGMP
                        LEFT JOIN
                            %s Page
                        ON
                            Page.ID = P_PGMP.ProductGroupPageID
                        WHERE
                            Page.IdentifierCode = '%s'
                    ) AS MirrorRelations
                GROUP BY
                    ProductID
                ORDER BY
                    RAND()
                LIMIT
                    %d
                ",
                $productTable,
                $pageTable,
                $groupIdentifier,
                $productTable,
                $pageTable,
                $groupIdentifier,
                $nrOfProducts
            )
        );

        foreach ($records as $record) {
            $product = Product::get()->byID($record['ProductID']);

            if ($product instanceof Product &&
                $product->exists()) {
                $products[] = $product;
            }
        }

        $productGroupItems = new ArrayList($products);

        return $productGroupItems;
    }
    
    /**
     * Returns the shoppingcart of the current user or false if there's no
     * member object registered.
     * 
     * @return ShoppingCart
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public function ShoppingCart() {
        $controller = Controller::curr();

        if (get_class($this) == get_class($controller) &&
            !Tools::isIsolatedEnvironment() &&
            !Tools::isBackendEnvironment()) {

            $member = Customer::currentUser();

            if (!$member) {
                return false;
            }

            return $member->getCart();
        } else {
            return false;
        }
    }
    
    /**
     * Alias for self::ShoppingCart().
     * 
     * @return ShoppingCart
     */
    public function getCart() {
        return $this->ShoppingCart();
    }
    
    /**
     * Builds an associative array of ProductGroups to use in GroupedDropDownFields.
     *
     * @param SiteTree $parent      Expects a ProductGroupHolder or a ProductGroupPage
     * @param boolean  $allChildren All children or only visible children?
     * @param boolean  $withParent  Add parent page to the list?
     *
     * @return array
     * 
     * @deprecated no uses found. remove before release.
     */
    public static function getRecursivePagesForGroupedDropdownAsArray($parent = null, $allChildren = false, $withParent = false) {
        $pages = array();
        
        if (is_null($parent)) {
            $pages['']  = '';
            $parent     = Tools::PageByIdentifierCode('SilverCartPageHolder');
        }
        
        if ($parent) {
            if ($withParent) {
                $pages[$parent->ID] = $parent->Title;
            }
            if ($allChildren) {
                $children = $parent->AllChildren();
            } else {
                $children = $parent->Children();
            }
            foreach ($children as $child) {
                $pages[$child->ID] = $child->Title;
                $subs                      = self::getRecursivePagesForGroupedDropdownAsArray($child);
                
                if (!empty ($subs)) {
                    $pages[_t(ProductGroupHolder::class . '.SUBGROUPS_OF','Subgroups of ') . $child->Title] = $subs;
                }
            }
        }
        return $pages;
    }
    
    /**
     * Returns all payment methods
     *
     * @return DataList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function PaymentMethods() {
        $paymentMethods = PaymentMethod::getAllowedPaymentMethodsFor($this->ShippingCountry(), ShoppingCart::singleton(), true);
        return $paymentMethods;
    }
    
    /**
     * Returns the current shipping country
     *
     * @return Country
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function ShippingCountry() {
        $customer           = Customer::currentUser();
        $shippingCountry    = null;
        if ($customer) {
            $shippingCountry = $customer->ShippingAddress()->Country();
        }
        if (is_null($shippingCountry) ||
            $shippingCountry->ID == 0) {
            $shippingCountry = Country::get()->filter(array(
                'ISO2'   => substr(Tools::current_locale(), 3),
                'Active' => 1,
            ))->first();
        }
        return $shippingCountry;
    }
    
    /**
     * Returns the footer columns.
     * 
     * @return DataList
     */
    public function getFooterColumns() {
        $metanavigationHolder = MetaNavigationHolder::get()->filter('ClassName', MetaNavigationHolder::class);
        return $metanavigationHolder;
    }
    
    /**
     * Returns the link to lost password form dependent on the current locale.
     * 
     * @return string
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.07.2014
     */
    public function LostPasswordLink() {
        $link = Director::baseURL() . 'Security/lostpassword/?locale=' . Tools::current_locale();
        return $link;
    }
    
    /**
     * Get the error message out of session and delete it (from session).
     *
     * @return string
     */
    public function getErrorMessage() {
        $errorMessage = Tools::Session()->get('Silvercart.errorMessage');
        Tools::Session()->clear('Silvercart.errorMessage');
        Tools::saveSession();
        return $errorMessage;
    }

    /**
     * Set the error message into the session.
     *
     * @param string $errorMessage Error message
     * 
     * @return void
     */
    public function setErrorMessage($errorMessage) {
        Tools::Session()->set('Silvercart.errorMessage', $errorMessage);
        Tools::saveSession();
    }
    
    /**
     * Get the success message out of session and delete it (from session).
     *
     * @return string
     */
    public function getSuccessMessage() {
        $successMessage = Tools::Session()->get('Silvercart.successMessage');
        Tools::Session()->clear('Silvercart.successMessage');
        Tools::saveSession();
        return $successMessage;
    }

    /**
     * Set the success message into the session.
     *
     * @param string $successMessage Success message
     * 
     * @return void
     */
    public function setSuccessMessage($successMessage) {
        Tools::Session()->set('Silvercart.successMessage', $successMessage);
        Tools::saveSession();
    }
    
    /**
     * Returns the QuickSearchForm.
     * 
     * @return QuickSearchForm
     */
    public function QuickSearchForm() {
        $form = new QuickSearchForm($this);
        return $form;
    }
    
    /**
     * Returns the QuickLoginForm.
     * 
     * @return QuickLoginForm
     */
    public function QuickLoginForm() {
        $form = new QuickLoginForm($this);
        return $form;
    }
    
}