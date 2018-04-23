<?php

namespace SilverCart\Model\Pages;

use Psr\SimpleCache\CacheInterface;
use SilverCart\Admin\Model\Config;
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
use SilverCart\Model\Pages\FrontPageController;
use SilverCart\Model\Pages\MetaNavigationHolder;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Pages\ProductGroupHolder;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Plugins\Plugin;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Widgets\Widget;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\CMS\Controllers\ModelAsController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Convert;
use SilverStripe\ErrorPage\ErrorPage;
use SilverStripe\ErrorPage\ErrorPageController;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Queries\SQLSelect;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use Translatable;

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
        i18n::config()->merge('default_locale', Translatable::get_current_locale());
        i18n::set_locale(Translatable::get_current_locale());
        parent::__construct($dataRecord);
    }
    
    /**
     * On before init.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.10.2014
     */
    public function loadJSRequirements() {
        if (Tools::isIsolatedEnvironment()) {
            return;
        }

        Requirements::set_write_js_to_body(true);
        Requirements::javascript('https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
        Requirements::javascript('https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js');
        Requirements::javascript('silverstripe/admin:client/dist/js/i18n.js');
        
        $jsFiles = array(
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
        );
        if (Widget::$use_anything_slider) {
            $jsFiles = array_merge(
                    $jsFiles,
                    array(
                        'silvercart/silvercart:client/javascript/anythingslider/js/jquery.anythingslider.min.js',
                        'silvercart/silvercart:client/javascript/anythingslider/js/jquery.anythingslider.fx.min.js',
                        'silvercart/silvercart:client/javascript/anythingslider/js/jquery.easing.1.2.js',
                    )
            );
        }
        $this->extend('updatedJSRequirements', $jsFiles);
        
        Requirements::combine_files(
            'm.js.js',
            $jsFiles
        );
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
        Requirements::themedCSS('client/css/color_' . Config::getConfig()->ColorScheme);
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
    public function init() {
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
        
        $this->loadJSRequirements();
        
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
            get_class($this) != FrontPageController::class &&
            !get_class($this) instanceof CheckoutStepController &&
            !get_class($this) instanceof Security &&
            !is_subclass_of(get_class($this), CheckoutStepController::class)
        ) {
            Checkout::clear_session();
        }

        // Decorator can use this method to add custom forms and other stuff
        $this->extend('updateInit');

        Plugin::call($this, 'init', array($this));
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
        if ($this->getTranslations()) {
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
     * template method for breadcrumbs
     * show breadcrumbs for pages which show a DataObject determined via URL parameter ID
     * see _config.php
     *
     * @return string
     */
    public function getBreadcrumbs() {
        $page = SiteTree::get()->filter('URLSegment', $this->urlParams['URLSegment'])->first();

        return $this->ContextBreadcrumbs($page);
    }

    /**
     * pages with own url rewriting need their breadcrumbs created in a different way
     *
     * @param Controller $context        the current controller
     * @param int        $maxDepth       maximum levels
     * @param bool       $unlinked       link breadcrumbs elements
     * @param bool       $stopAtPageType ???
     * @param bool       $showHidden     show pages that will not show in menus
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     */
    public function ContextBreadcrumbs($context, $maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        $page = $context;
        $parts = array();

        // Get address type
        $address = DataObject::get_by_id($context->getSection(), $this->urlParams['ID']);
        $parts[] = $address->i18n_singular_name();

        $i = 0;
        while (
            $page
            && (!$maxDepth || sizeof($parts) < $maxDepth)
            && (!$stopAtPageType || $page->ClassName != $stopAtPageType)
        ) {
            if ($showHidden || $page->ShowInMenus || ($page->ID == $this->ID)) {
                if ($page->URLSegment == 'home') {
                    $hasHome = true;
                }
                if (($page->ID == $this->ID) || $unlinked) {
                    $parts[] = Convert::raw2xml($page->Title);
                } else {
                    $parts[] = ("<a href=\"" . $page->Link() . "\">" . Convert::raw2xml($page->Title) . "</a>");
                }
            }
            $page = $page->Parent;
        }

        return implode(" &raquo; ", array_reverse($parts));
    }
    
    /**
     * manipulates the parts the pages breadcrumbs if a product detail view is 
     * requested.
     *
     * @param int    $maxDepth       maximum depth level of shown pages in breadcrumbs
     * @param bool   $unlinked       true, if the breadcrumbs should be displayed without links
     * @param string $stopAtPageType name of pagetype to stop at
     * @param bool   $showHidden     true, if hidden pages should be displayed in breadcrumbs
     *
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Patrick Schneider <pschneider@pixeltricks.de>
     * @since 09.10.2012
     */
    public function BreadcrumbParts($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        $parts = new ArrayList();
        $page  = $this;

        while (
            $page
            && (!$maxDepth ||
                    $parts->count() < $maxDepth)
            && (!$stopAtPageType ||
                    $page->ClassName != $stopAtPageType)
        ) {
            if ($showHidden ||
                $page->ShowInMenus ||
                ($page->ID == $this->ID)) {
                
                if ($page->hasMethod('OriginalLink')) {
                    $link = $page->OriginalLink();
                } else {
                    $link = $page->Link();
                }

                if ($page->ID == $this->ID) {
                    $isActive = true;
                } else {
                    $isActive = false;
                }

                $parts->unshift(
                    new ArrayData(
                        array(
                            'MenuTitle' => $page->MenuTitle,
                            'Title'     => $page->Title,
                            'Link'      => $link,
                            'Parent'    => $page->Parent,
                            'IsActive'  => $isActive,
                        )
                    )
                );
            }
            $page = $page->Parent;
        }
        return $parts;
    }
    
    /**
     * returns the breadcrumbs as ArrayList for use in controls with product title
     * 
     * @param int    $maxDepth       maximum depth level of shown pages in breadcrumbs
     * @param bool   $unlinked       true, if the breadcrumbs should be displayed without links
     * @param string $stopAtPageType name of pagetype to stop at
     * @param bool   $showHidden     true, if hidden pages should be displayed in breadcrumbs
     *
     * @return ArrayList 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 09.10.2012
     */
    public function DropdownBreadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        return $this->BreadcrumbParts($maxDepth, $unlinked, $stopAtPageType, $showHidden);
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
     * This function is replacing the default SilverStripe Logout Form. This form is used to logout the customer and direct
     * the user to the startpage
     *
     * @return null
     *
     * @author Oliver Scheer <oscheer@pixeltricks.de>
     * @since 11.11.2010
     */
    public function logOut() {
        Security::logout(false);
        $frontPage = Tools::PageByIdentifierCode();
        $this->redirect($frontPage->RelativeLink());
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
                'ISO2'   => substr(Translatable::get_current_locale(), 3),
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
        $link = Director::baseURL() . 'Security/lostpassword/?locale=' . Translatable::get_current_locale();
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