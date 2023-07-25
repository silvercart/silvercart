<?php

namespace SilverCart\Model\Pages;

use ReflectionClass;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\ProductGroupPageSelectorsForm;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Product\Manufacturer;
use SilverCart\Model\Product\Product;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Security\Member;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ViewableData_Customised;


/**
 * Adds product group sorting features.
 * 
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @author Ionut Lipciuc
 * @since 26.06.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait ProductGroupSorting
{
    /**
     * List of allowed actions.
     *
     * @var string[]
     */
    private static array $allowed_actions = [
        'detail',
        'newproducts',
        'preorders',
        'chsffopt',
        'chpppopt',
        'ProductGroupPageSelectorsForm',
    ];
    /**
     * Contains a list of all registered filter plugins.
     *
     * @var array
     */
    public static array $registeredFilterPlugins = [];

    /**
     * Sets the products per page count.
     *
     * @param int $count Count of products to show in a list.
     *
     * @return void
     */
    public static function setProductsPerPage(int $count) : void
    {
        if (array_key_exists($count, Config::$productsPerPageOptions)) {
            Tools::Session()->set('SilvercartProductGroup.productsPerPage', $count);
            Tools::saveSession();
        }
    }

    /**
     * Returns the products per page count.
     *
     * @return int|null
     */
    public static function getProductsPerPage() : int|null
    {
        return Tools::Session()->get('SilvercartProductGroup.productsPerPage');
    }

    /**
     * Registers an object as a filter plugin. Before getting the result set
     * the method 'filter' is called on the plugin. It has to return an array
     * with filters to deploy on the query.
     *
     * @param string $plugin Name of the filter plugin
     *
     * @return void
     */
    public static function registerFilterPlugin(string $plugin) : void
    {
        $reflectionClass = new ReflectionClass($plugin);
        if ($reflectionClass->hasMethod('filter')) {
            self::$registeredFilterPlugins[] = new $plugin();
        }
    }
    
    /**
     * Contains the total number of products for this page.
     *
     * @var int
     */
    protected int $totalNumberOfProducts = 0;
    /**
     * Contains a DataList of products for this page or null. Used for
     * caching.
     *
     * @var PaginatedList[]
     */
    protected array $groupProducts = [];
    /**
     * Current SQL offset
     *
     * @var int[]
     */
    protected array $sqlOffsets = [];
    /**
     * Contains the Product object that is used for the detail view
     * or null. Used for caching.
     *
     * @var Product|null
     */
    protected Product|null $detailViewProduct = null;
    /**
     * Contains filters for the SQL query that retrieves the products for this
     * page.
     *
     * @var string[]
     */
    protected array $listFilters = [];
    /**
     * Product detail view parameters
     *
     * @var array
     */
    protected array $productDetailViewParams = [];

    /**
     * Detail product to show
     *
     * @var Product|null
     */
    protected Product|null $product = null;
    /**
     * Sortable frontend fields as ArrayList.
     *
     * @var ArrayList|null
     */
    protected ArrayList|null $sortableFrontendFields = null;

    /**
     * Current sortable frontend field label.
     *
     * @var string|null
     */
    protected string|null $currentSortableFrontendFieldLabel = null;

    /**
     * Returns the offset of the current page for pagination.
     *
     * @return int
     */
    public function CurrentOffset() : int
    {
        if (!isset($_GET['start'])
         || !is_numeric($_GET['start'])
         || (int)$_GET['start'] < 1
        ) {
            if (isset($_GET['offset'])) {
                $productsPerPage = $this->getProductsPerPageSetting();
                // Use offset for getting the current item rage
                $offset = (int) $_GET['offset'];
                if ($offset > 0) {
                    $offset -= 1;
                }
                // Prevent too high values
                if ($offset > 999999) {
                    $offset = 0;
                }
                $SQL_start = $offset * $productsPerPage;
            } else {
                // Use item number for getting the current item range
                $SQL_start = 0;
            }
        } else {
            $SQL_start = (int) $_GET['start'];
        }
        return (int) $SQL_start;
    }

    /**
     * checks whether the requested view is an product detail view or a product
     * group view.
     *
     * @return bool
     */
    public function isProductDetailView() : bool
    {
        return $this->getDetailViewProduct() instanceof Product;
    }

    /**
     * returns the chosen product when requesting a product detail view.
     *
     * @return Product|null
     */
    public function getDetailViewProduct() : Product|null
    {
        if (!array_key_exists('Action', $this->urlParams)
         || !is_numeric($this->urlParams['Action'])
        ) {
            return null;
        }
        if (is_null($this->detailViewProduct)) {
            $this->detailViewProduct = Product::get()->byID(Convert::raw2sql($this->urlParams['Action']));
        }
        return $this->detailViewProduct;
    }

    /**
     * chsffopt stands for "Change Sortable Frontend Field Option".
     * Changes the sort order type for product lists.
     *
     * @param HTTPRequest $request Request
     *
     * @return HTTPResponse
     */
    public function chsffopt(HTTPRequest $request) : HTTPResponse
    {
        $newOption                   = $request->param('ID');
        $product                     = Product::singleton();
        $sortableFrontendFields      = $product->sortableFrontendFields();
        $sortableFrontendFieldValues = array_keys($sortableFrontendFields);
        if (array_key_exists($newOption, $sortableFrontendFieldValues)) {
            $sortOrder = $sortableFrontendFieldValues[$newOption];
            Product::setDefaultSort($sortOrder);
        }
        return $this->redirect($this->Link());
    }

    /**
     * chpppopt stands for "CHange Products Per Page Option".
     * Changes the quantity of products to display in a product lists.
     *
     * @param HTTPRequest $request Request
     *
     * @return HTTPResponse
     */
    public function chpppopt(HTTPRequest $request) : HTTPResponse
    {
        $member                      = Customer::currentUser();
        $newOption                   = $request->param('ID');
        $product                     = Product::singleton();
        $sortableFrontendFields      = $product->sortableFrontendFields();
        $sortableFrontendFieldValues = array_keys($sortableFrontendFields);
        if (array_key_exists($newOption, $sortableFrontendFieldValues)) {
            $sortOrder = $sortableFrontendFieldValues[$newOption];
            Product::setDefaultSort($sortOrder);
        }
        if ($member instanceof Member
         && $member->exists()
        ) {
            $member->getCustomerConfig()->productsPerPage = $newOption;
            $member->getCustomerConfig()->write();
        }
        self::setProductsPerPage($newOption);
        return $this->redirect($this->Link());
    }

    /**
     * Workaround to be able to access the current HTTPRequest in self::getDetailViewProduct().
     *
     * @param HTTPRequest $request HTTP request
     *
     * @return HTTPResponse
     */
    public function handleRequest(HTTPRequest $request) : HTTPResponse
    {
        $allowed_actions = array_merge(
                $this->config()->get('allowed_actions'),
                [Manufacturer::get_filter_action()]
        );
        $this->config()->update('allowed_actions', $allowed_actions);
        $this->setRequest($request);
        return parent::handleRequest($request);
    }

    /**
     * handles the requested action.
     * If a product detail view is requested, the detail view template will be
     * rendered an displayed.
     *
     * @param HTTPRequest $request request data
     * @param string         $action  Action
     *
     * @return mixed
     */
    public function handleAction($request, $action)
    {
        if ($this->isProductDetailView()) {
            $this->urlParams['Action'] = (int) $this->urlParams['Action'];
            if (!empty($this->urlParams['OtherID'])) {
                $secondaryAction = $this->urlParams['OtherID'];
                if ($this->hasMethod($secondaryAction)
                 && $this->hasAction($secondaryAction)
                ) {
                    $result = $this->{$secondaryAction}($request);
                    if (is_array($result)) {
                        return $this->getViewer($this->action)->process($this->customise($result));
                    } else {
                        return $result;
                    }
                }
            }
            $product     = $this->getDetailViewProduct();
            $productLink = $product->Link();
            $calledLink  = $request->getURL();
            if (strpos($calledLink, '/') != strpos($productLink, '/')) {
                if (strpos($productLink, '/') == 0) {
                    $calledLink = "/{$calledLink}";
                } elseif (strpos($calledLink, '/') == 0) {
                    $productLink = "/{$productLink}";
                }
            }
            if ($calledLink != $productLink
             && Director::baseURL() . substr($calledLink, 1) != $productLink
            ) {
                Tools::redirectPermanentlyTo($productLink);
            }
            $this->setProduct($product);
            $this->extend('onBeforeRenderProductDetailView');
            return $this->render();
        } elseif ($this->isFilteredByManufacturer()) {
            $url                       = str_replace("{$this->urlParams['Action']}/{$this->urlParams['ID']}", '', $_REQUEST['url']);
            $this->urlParams['Action'] = '';
            $this->urlParams['ID']     = '';
            $customRequest             = HTTPRequest::create('GET', $url, [], [], null);
            return parent::handleAction($customRequest, $action);
        }
        return parent::handleAction($request, $action);
    }

    /**
     * Overwrites checking for an existing action if a product detail view is called.
     *
     * @param string $action Action to check
     *
     * @return bool
     */
    public function hasAction($action) : bool
    {
        $hasAction = parent::hasAction($action);
        if (!$hasAction
         && $this->isProductDetailView()
        ) {
            $hasAction = true;
        }
        return $hasAction;
    }

    /**
     * Overwrites access handling if a product detail view is called.
     *
     * @param string $action Action to check access for
     *
     * @return bool
     */
    public function checkAccessAction($action) : bool
    {
        $hasAccess = parent::checkAccessAction($action);
        if (!$hasAccess
         && $this->isProductDetailView()
        ) {
            $hasAccess = true;
        }
        return $hasAccess;
    }


    /**
     * Checks whether the product list filtered by any filter plugin or by
     * manufacturer.
     *
     * @return bool
     */
    public function isFiltered() : bool
    {
        return $this->isFilteredByPlugin()
            || $this->isFilteredByManufacturer();
    }

    /**
     * Checks whether the product list is filtered by any filter plugin.
     *
     * @return bool
     */
    public function isFilteredByPlugin() : bool
    {
        $isFilteredByPlugin = false;
        if (count(self::$registeredFilterPlugins) > 0) {
            foreach (self::$registeredFilterPlugins as $registeredPlugin) {
                $pluginFilters = $registeredPlugin->filter();
                if (is_array($pluginFilters)
                 && !empty($pluginFilters)
                ) {
                    $isFilteredByPlugin = true;
                    break;
                }
            }
        }
        return $isFilteredByPlugin;
    }

    /**
     * Checks whether the product list should be filtered by manufacturer.
     *
     * @return bool
     */
    public function isFilteredByManufacturer() : bool
    {
        if ($this->getRequest()) {
            $params = $this->getRequest()->allParams();
            if (is_array($params)
             && array_key_exists('Action', $params)
             && $params['Action'] == Manufacturer::get_filter_action()
             && !empty ($params['ID'])
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Adds a filter to filter the groups product list.
     *
     * @param string $property   The property to filter
     * @param string $value      The value of the property
     * @param string $comparison The comparison operator (default: '=')
     * @param string $operator   The logical operator (default: 'AND')
     *
     * @return void
     *
     * @example $productGroup->addListFilter('ManufacturerID','5');
     *          Will add the following filter: "AND \"ManufacturerID\" = '5'"
     * @example $productGroup->addListFilter('ManufacturerID','(5,6,7)','IN','OR');
     *          Will add the following filter: "OR \"ManufacturerID\" IN (5,6,7)"
     */
    public function addListFilter(string $property, string $value, string $comparison = '=', string $operator = 'AND') : ProductGroupPageController
    {
        if ($comparison === 'IN') {
            $this->listFilters[] = " \"{$property}\" {$comparison} ({$value}) {$operator}";
        } else {
            $this->listFilters[] = " \"{$property}\" {$comparison} '{$value}' {$operator}";
        }
        return $this;
    }

    /**
     * Returns whether the current view is the first page of the product list or not
     *
     * @return bool
     */
    public function isFirstPage() : bool
    {
        $isFirstPage = true;
        if ($this->getSqlOffset() > 0) {
            $isFirstPage = false;
        }
        return $isFirstPage;
    }

    /**
     * Returns the total number of products for the current controller.
     *
     * @return int
     */
    public function getTotalNumberOfProducts() : int
    {
        return (int) $this->totalNumberOfProducts;
    }

    /**
     * Indicates wether the resultset of the product query returns more
     * products than the number given (defaults to 10).
     *
     * @param int $maxResults The maximum count of results
     *
     * @return bool
     */
    public function HasMoreProductsThan(int $maxResults = 10) : bool
    {
        $products = $this->getProducts();
        return $products
            && $products->count() > $maxResults;
    }

    /**
     * Indicates wether the resultset of the product query returns less
     * products than the number given (defaults to 10).
     *
     * @param  int  $maxResults  The maximum count of results
     *
     * @return bool
     */
    public function HasLessProductsThan(int $maxResults = 10) : bool
    {
        $products = $this->getProducts();
        return $products
            && $products->count() < $maxResults;
    }

    /**
     * Indicates wether the resultset of the product query returns more items
     * than the number given (defaults to 10).
     *
     * @param int $maxResults The number of results to check
     *
     * @return bool
     */
    public function HasMorePagesThan(int $maxResults = 10) : bool
    {
        $products       = $this->getProducts();
        $items          = 0;
        $hasMoreResults = false;
        if ($products) {
            $items = $products->Pages()->count();
        }
        if ($items > $maxResults) {
            $hasMoreResults = true;
        }
        return $hasMoreResults;
    }


    /**
     * Return the start value for the limit part of the sql query that
     * retrieves the product group list for the current product group page.
     *
     * @param int|bool $numberOfProductGroups The number of product groups to return
     *
     * @return int
     */
    public function getSqlOffsetForProductGroups($numberOfProductGroups = false) : int
    {
        if ($this->productGroupsPerPage) {
            $productGroupsPerPage = $this->productGroupsPerPage;
        } else {
            $productGroupsPerPage = Config::ProductsPerPage();
        }
        if ($numberOfProductGroups !== false) {
            $productGroupsPerPage = (int) $numberOfProductGroups;
        }
        if (!isset($_GET['groupStart'])
         || !is_numeric($_GET['groupStart'])
         || (int)$_GET['groupStart'] < 1
        ) {
            if (isset($_GET['groupOffset'])) {
                // Use offset for getting the current item rage
                $offset = (int) $_GET['groupOffset'];
                if ($offset > 0) {
                    $offset -= 1;
                }
                // Prevent too high values
                if ($offset > 999999) {
                    $offset = 0;
                }
                $SQL_start = $offset * $productGroupsPerPage;
            } else {
                // Use item number for getting the current item range
                $SQL_start = 0;
            }
        } else {
            $SQL_start = (int) $_GET['groupStart'];
        }
        return (int) $SQL_start;
    }


    /**
     * Returns the cache key parts for this product group
     *
     * @return string[]
     */
    public function CacheKeyParts() : array
    {
        $cacheKeyParts = $this->data()->CacheKeyParts();
        $this->extend('updateCacheKeyParts', $cacheKeyParts);
        return (array) $cacheKeyParts;
    }

    /**
     * Returns the cache key for this product group
     *
     * @return string
     */
    public function CacheKey() : string
    {
        $cacheKey = $this->data()->CacheKey();
        $this->extend('updateCacheKey', $cacheKey);
        return (string) $cacheKey;

    }

    /**
     * Adds the given number to the total number of products for the
     * current controller.
     *
     * @param int $numberOfProducts The number of products to set
     *
     * @return ProductGroupPageController
     */
    public function addTotalNumberOfProducts(int $numberOfProducts) : ProductGroupPageController
    {
        $this->totalNumberOfProducts += $numberOfProducts;
        return $this;
    }

    /**
     * Set the total number of products for the current controller.
     *
     * @param int $numberOfProducts The number of products to set
     *
     * @return ProductGroupPageController
     */
    public function setTotalNumberOfProducts(int $numberOfProducts) : ProductGroupPageController
    {
        $this->totalNumberOfProducts = $numberOfProducts;
        return $this;
    }

    /**
     * Returns the ProductGroupPageSelectorsForm.
     *
     * @return ProductGroupPageSelectorsForm
     */
    public function ProductGroupPageSelectorsForm() : ProductGroupPageSelectorsForm
    {
        return ProductGroupPageSelectorsForm::create($this);
    }

    /**
     * Returns the SQL filter statement for the current query.
     *
     * @param string $excludeFilter The name of the filter to exclude
     *
     * @return string
     */
    public function getListFilters($excludeFilter = false) : string
    {
        $filter = '';
        foreach ($this->listFilters as $listFilterIdenfitier => $listFilter) {
            if ($listFilterIdenfitier != $excludeFilter) {
                $filter .= " {$listFilter}";
            }
        }
        return $filter;
    }

    /**
     * Returns the number of products per page according to where it is set.
     * Highest priority has the customer's configuration setting if available.
     * Next comes the shop owners setting for this page; if that's not
     * configured we use the global setting from Config.
     *
     * @return int
     */
    public function getProductsPerPageSetting() : int
    {
        $member          = Customer::currentUser();
        $productsPerPage = self::getProductsPerPage();
        if (is_null($productsPerPage)) {
            if ($member
             && $member->getCustomerConfig()
             && $member->getCustomerConfig()->productsPerPage !== null
             && array_key_exists($member->getCustomerConfig()->productsPerPage, Config::$productsPerPageOptions)
            ) {
                $productsPerPage = $member->getCustomerConfig()->productsPerPage;
            } elseif ($this->productsPerPage) {
                $productsPerPage = $this->productsPerPage;
            } else {
                $productsPerPage = Config::ProductsPerPage();
            }
        }
        if ((int) $productsPerPage === 0) {
            $productsPerPage = Config::getProductsPerPageUnlimitedNumber();
        }
        return (int) $productsPerPage;
    }

    /**
     * All products of this group
     *
     * @return string
     */
    public function getProductsFilter() : string
    {
        $this->listFilters          = [];
        $filter                     = '';
        $translations               = Tools::get_translations($this);
        $translationProductGroupIDs = [
            $this->ID,
        ];
        if ($translations
         && $translations->count() > 0
        ) {
            foreach ($translations as $translation) {
                $translationProductGroupIDs[] = $translation->ID;
            }
        }
        $translationProductGroupIDList = implode(',', $translationProductGroupIDs);
        $mirroredProductIdList         = '';
        if ($this->hasMethod('getMirroredProductIDs')) {
            implode(',', $this->getMirroredProductIDs());
        }
        if ($this->isFilteredByManufacturer()) {
            $manufacturer = Manufacturer::getByUrlSegment($this->urlParams['ID']);
            if ($manufacturer instanceof Manufacturer
             && $manufacturer->exists()
            ) {
                $this->addListFilter('ManufacturerID', $manufacturer->ID);
            }
        }
        if (empty($mirroredProductIdList)) {
            $this->listFilters['original'] = "ProductGroupID IN ({$translationProductGroupIDList})";
        } else {
            $pStageTable                   = Product::singleton()->getStageTableName();
            $this->listFilters['original'] = "(ProductGroupID IN ({$translationProductGroupIDList})"
                                           . " OR {$pStageTable}.ID IN ({$mirroredProductIdList}))";
        }
        if ($this->data()->config()->load_products_from_children) {
            $childrenFilter = $this->getProductsFromChildrenFilter();
            if (!empty($childrenFilter)) {
                $this->listFilters['original'] = "({$this->listFilters['original']} OR {$childrenFilter})";
            }
        }
        if (count(self::$registeredFilterPlugins) > 0) {
            foreach (self::$registeredFilterPlugins as $registeredPlugin) {
                $pluginFilters = $registeredPlugin->filter();
                if (is_array($pluginFilters)) {
                    $this->listFilters = array_merge(
                            $this->listFilters,
                            $pluginFilters
                    );
                }
            }
        }
        foreach ($this->listFilters as $listFilter) {
            $filter .= " {$listFilter}";
        }
        $this->extend('updateGetProductsFilter', $filter);
        return $filter;
    }
    
    /**
     * Return the start value for the limit part of the sql query that
     * retrieves the product list for the current product group page.
     *
     * @param  bool|int  $numberOfProducts  The number of products to return
     *
     * @return int
     * 7168
     */
    public function getSqlOffset(bool|int $numberOfProducts = false) : int
    {
        $sqlOffsetKey = $numberOfProducts;
        if ($numberOfProducts === false) {
            $sqlOffsetKey = 'false';
        }
        if (!array_key_exists($sqlOffsetKey, $this->sqlOffsets)) {
            $productsPerPage = $this->getProductsPerPageSetting();
            if ($numberOfProducts !== false) {
                $productsPerPage = (int) $numberOfProducts;
            }
            if ($productsPerPage === Config::getProductsPerPageUnlimitedNumber()) {
                $SQL_start = 0;
            } else {
                if (!isset($_GET['start'])
                 || !is_numeric($_GET['start'])
                 || (int)$_GET['start'] < 1
                ) {
                    if (isset($_GET['offset'])) {
                        // Use offset for getting the current item rage
                        $offset = (int) $_GET['offset'];
                        if ($offset > 0) {
                                $offset -= 1;
                        }
                        // Prevent too high values
                        if ($offset > 999999) {
                                $offset = 0;
                        }
                        $SQL_start = $offset * $productsPerPage;
                    } else {
                        // Use item number for getting the current item range
                        $SQL_start = 0;
                    }
                } else {
                    $SQL_start = (int) $_GET['start'];
                }
            }
            $this->sqlOffsets[$sqlOffsetKey] = $SQL_start;
        }
        return (int) $this->sqlOffsets[$sqlOffsetKey];
    }


    /**
     * Return an SSViewer object to process the data
     * Manipulates the SSViewer in case of a product detail view.
     *
     * @param string $action Action
     *
     * @return SSViewer The viewer identified being the default handler for this Controller/Action combination
     */
    public function getViewer($action)
    {
        $viewer = parent::getViewer($action);
        if ($this->isProductDetailView()) {
            $templates = $viewer->templates();
            $viewer    = SSViewer::create([
                    'SilverCart\Model\Pages\ProductGroupPage_detail',
                    basename($templates['main'], '.ss'),
            ]);
        }
        return $viewer;
    }


    /**
     * Merge some arbitrary data in with this object. This method returns a {@link ViewableData_Customised} instance
     * with references to both this and the new custom data.
     *
     * Note that any fields you specify will take precedence over the fields on this object.
     *
     * Adds custom product detail data when a product detail view is requested.
     *
     * @param array $data Customised data
     *
     * @return ViewableData_Customised
     */
    public function customise($data) : ViewableData_Customised
    {
        if ($this->isProductDetailView()) {
            $data = array_merge(
                    $data,
                    $this->ProductDetailViewParams()
            );
        }
        return parent::customise($data);
    }

    /**
     * renders a product detail view template (if requested)
     *
     * @return void
     */
    protected function ProductDetailViewParams()
    {
        if ($this->isProductDetailView()
         && empty($this->productDetailViewParams)
        ) {
            $product                       = $this->getDetailViewProduct();
            $this->productDetailViewParams = [
                    'getProduct' => $product,
                    'MetaTitle'  => $this->DetailViewProductMetaTitle(),
                    'MetaTags'   => $this->DetailViewProductMetaTags(false),
            ];
        }
        return $this->productDetailViewParams;
    }
}