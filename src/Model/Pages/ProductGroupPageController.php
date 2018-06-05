<?php

namespace SilverCart\Model\Pages;

use ReflectionClass;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Customer;
use SilverCart\Forms\ProductGroupPageSelectorsForm;
use SilverCart\Model\Pages\ProductGroupHolder;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Product\Manufacturer;
use SilverCart\Model\Product\Product;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\ORM\SS_List;
use SilverStripe\Security\Member;
use SilverStripe\View\ArrayData;
use SilverStripe\View\SSViewer;

/**
 * ProductGroupPage Controller class.
 * This controller handles the actions for product group views and product detail
 * views.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupPageController extends \PageController {

    /**
     * Contains the total number of products for this page.
     *
     * @var int
     */
    protected $totalNumberOfProducts = 0;

    /**
     * Contains a list of all registered filter plugins.
     *
     * @var array
     */
    public static $registeredFilterPlugins = [];
    
    /**
     * Contains a DataList of products for this page or null. Used for
     * caching.
     *
     * @var mixed null|ArrayList
     */
    protected $groupProducts = [];

    /**
     * Contains the Product object that is used for the detail view
     * or null. Used for caching.
     *
     * @var mixed Product
     */
    protected $detailViewProduct = null;

    /**
     * Contains filters for the SQL query that retrieves the products for this
     * page.
     *
     * @var array
     */
    protected $listFilters = [];
    
    /**
     * Used for offset calculation of the SQL query that retrieves the
     * products for this page.
     *
     * @var int
     */
    protected $SQL_start = 0;
    
    /**
     * Contains the output of all WidgetSets of the parent page
     *
     * @var array
     */
    protected $widgetOutput = [];

    /**
     * Makes widgets of parent pages load when subpages don't have any attributed.
     *
     * @var boolean
     */
    public $forceLoadOfWidgets = true;
    
    /**
     * Contains the viewable children of this page for caching purposes.
     *
     * @var mixed null|PaginatedList
     */
    protected $viewableChildren = null;
    
    /**
     * Product detail view parameters
     *
     * @var array
     */
    protected $productDetailViewParams = [];
    
    /**
     * Current SQL offset
     *
     * @var int 
     */
    protected $sqlOffsets = [];
    
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'detail',
        'chsffopt',
        'chpppopt',
        'ProductGroupPageSelectorsForm',
    ];
    
    /**
     * Detail product to show
     *
     * @var Product
     */
    protected $product = null;
    
    /**
     * Sortable frontend fields as ArrayList.
     *
     * @var ArrayList
     */
    protected $sortableFrontendFields = null;
    
    /**
     * Current sortable frontend field label.
     *
     * @var string
     */
    protected $currentSortableFrontendFieldLabel = null;

    /**
     * Indicates wether a filter plugin can be registered for the current view.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.08.2011
     */
    public function canRegisterFilterPlugin() {
        if ($this->isProductDetailView()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Returns the cache key for the product group page list view.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.10.2011
     */
    public function CacheKeyProductGroupPageControls() {
        return implode(
            '_',
            array(
                $this->ID,
                $this->SQL_start,
                $this->getProductsPerPageSetting()
            )
        );
    }
    
    /**
     * Registers an object as a filter plugin. Before getting the result set
     * the method 'filter' is called on the plugin. It has to return an array
     * with filters to deploy on the query.
     * 
     * @param string $plugin Name of the filter plugin
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public static function registerFilterPlugin($plugin) {
        $reflectionClass = new ReflectionClass($plugin);
        
        if ($reflectionClass->hasMethod('filter')) {
            self::$registeredFilterPlugins[] = new $plugin();
        }
    }

    /**
     * execute these statements on object call
     *
     * @param bool $skip When set to true, the init routine will be skipped
     * 
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2013
     */
    protected function init($skip = false) {
        parent::init();
        if (!$skip) {
            if (isset($_GET['start'])) {
                $this->SQL_start = (int)$_GET['start'];
            }

            // there must be two way to initialize this controller:
            if ($this->isProductDetailView()) {
                // a product detail view is requested
                if (!$this->getDetailViewProduct()->isActive) {
                    $this->redirect($this->PageByIdentifierCodeLink());
                }
            } else {
                Tools::Session()->set("SilverCart.ProductGroupPageID", $this->ID);
                Tools::saveSession();
            }
        }
    }
    
    /**
     * Returns the ProductGroupPageSelectorsForm.
     * 
     * @return ProductGroupPageSelectorsForm
     */
    public function ProductGroupPageSelectorsForm() {
        $form = new ProductGroupPageSelectorsForm($this);
        return $form;
    }

    /**
     * Returns the total number of products for the current controller.
     *
     * @return int
     */
    public function getTotalNumberOfProducts() {
        return $this->totalNumberOfProducts;
    }

    /**
     * Set the total number of products for the current controller.
     *
     * @param int $numberOfProducts The number of products to set
     * 
     * @return void
     */
    public function setTotalNumberOfProducts($numberOfProducts) {
        $this->totalNumberOfProducts = $numberOfProducts;
    }

    /**
     * Adds the given number to the total number of products for the
     * current controller.
     *
     * @param int $numberOfProducts The number of products to set
     * 
     * @return void
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.12.2012
     */
    public function addTotalNumberOfProducts($numberOfProducts) {
        $this->totalNumberOfProducts += $numberOfProducts;
    }

    /**
     * Uses the children of MyAccountHolder to render a subnavigation
     * with the SilverCart/Model/Pages/Includes/SubNavigation.ss template.
     * 
     * @param string $identifierCode param only added because it exists on parent::getSubNavigation
     *                               to avoid strict notice
     *
     * @return string
     */
    public function getSubNavigation($identifierCode = 'SilvercartProductGroupHolder') {
        $cachekey = 'SilverCart_Model_Pages_Includes_SubNavigation'.$this->ID;
        $cache    = Injector::inst()->get(CacheInterface::class . '.ProductGroupPageController_getSubNavigation');
        $result   = $cache->get($cachekey);

        if ($result) {
            $output = unserialize($result);
        } else {
            $menuElements = $this->getTopProductGroup($this)->Children();
            
            $extendedOutput = $this->extend('getSubNavigation', $menuElements);
        
            if (empty ($extendedOutput)) {
                $elements = array(
                    'SubElements' => $menuElements,
                );
                $output = $this->customise($elements)->renderWith(
                    array(
                        'SilverCart/Model/Pages/Includes/SubNavigation',
                    )
                );
            } else {
                $output = $extendedOutput[0];
            }
            
            $cache->set(serialize($output));
        }
        
        return Tools::string2html($output);
    }

    /**
     * returns the top product group (first product group under ProductGroupHolder)
     *
     * @param ProductGroupPage $productGroup product group
     *
     * @return ProductGroupPage
     */
    public function getTopProductGroup($productGroup = false) {
        if (!$productGroup) {
            $productGroup = $this;
        }
        if ($productGroup->Parent()->ClassName == ProductGroupHolder::class ||
            $productGroup->ParentID == 0) {
            return $productGroup;
        }
        return $this->getTopProductGroup($productGroup->Parent());
    }

    /**
     * returns the original page link. This is needed by the breadcrumbs. When
     * a product detail view is requested, the default method self::Link() will
     * return a modified link to the products detail view. This controller handles
     * both (product group views and product detail views), so a product detail
     * view won't have a related parent to show in breadcrumbs. The controller
     * itself will be the parent, so there must be two different links for one
     * controller.
     *
     * @param string $action Action to call.
     *
     * @return string
     *
     * @see self::Link()
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.02.2011
     */
    public function OriginalLink($action = null) {
        return $this->data()->OriginalLink($action);
    }
    
    /**
     * Returns the offset of the current page for pagination.
     * 
     * @return int
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.06.2011
     */
    public function CurrentOffset() {
        if (!isset($_GET['start']) ||
            !is_numeric($_GET['start']) ||
            (int)$_GET['start'] < 1) {


            if (isset($_GET['offset'])) {
                $productsPerPage = $this->getProductsPerPageSetting();
                
                // --------------------------------------------------------
                // Use offset for getting the current item rage
                // --------------------------------------------------------
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
                // --------------------------------------------------------
                // Use item number for getting the current item range
                // --------------------------------------------------------
                $SQL_start = 0;
            }
        } else {
            $SQL_start = (int) $_GET['start'];
        }
        
        return $SQL_start;
    }
    
    /**
     * All products of this group forced (independent of DoNotShowProducts setting)
     * 
     * @param int    $numberOfProducts The number of products to return
     * @param string $sort             An SQL sort statement
     * @param bool   $disableLimit     Disables the product limitation
     * 
     * @return DataList all products of this group or FALSE
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.06.2012
     */
    public function getProductsForced($numberOfProducts = false, $sort = false, $disableLimit = false) {
        return $this->getProducts($numberOfProducts, $sort, $disableLimit, true);
    }

    /**
     * All products of this group
     * 
     * @param int    $numberOfProducts The number of products to return
     * @param string $sort             An SQL sort statement
     * @param bool   $disableLimit     Disables the product limitation
     * @param bool   $force            Forces to get the products
     * 
     * @return DataList|false all products of this group
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2014
     */
    public function getProducts($numberOfProducts = false, $sort = false, $disableLimit = false, $force = false) {
        $hashKey = md5($numberOfProducts . '_' . $sort . '_' . $disableLimit . Tools::current_locale());
        if ($this->data()->DoNotShowProducts &&
            !$force) {
            $this->groupProducts[$hashKey] = new ArrayList();
        } elseif (!array_key_exists($hashKey, $this->groupProducts) || $force) {
            $SQL_start       = $this->getSqlOffset($numberOfProducts);
            $productsPerPage = $this->getProductsPerPageSetting();
            $products        = null;
            $this->extend('overwriteGetProducts', $products, $numberOfProducts, $productsPerPage, $SQL_start, $sort);

            if (!is_null($products)) {
                $this->groupProducts[$hashKey] = $products;
            } else {
                $this->listFilters = [];
                $filter            = '';
                
                // ----------------------------------------------------------------
                // Get products that have this group set as mirror group
                // ----------------------------------------------------------------

                if ($numberOfProducts !== false) {
                    $productsPerPage = (int) $numberOfProducts;
                }
                
                $translations               = Tools::get_translations($this);
                $translationProductGroupIDs = array(
                    $this->ID,
                );

                if ($translations &&
                    $translations->count() > 0) {
                    foreach ($translations as $translation) {
                        $translationProductGroupIDs[] = $translation->ID;
                    }
                }
                $translationProductGroupIDList  = implode(',', $translationProductGroupIDs);

                $mirroredProductIdList  = '';
                $mirroredProductIDs     = $this->getMirroredProductIDs();

                foreach ($mirroredProductIDs as $mirroredProductID) {
                    $mirroredProductIdList .= sprintf(
                        "'%s',",
                        $mirroredProductID
                    );
                }

                if (!empty($mirroredProductIdList)) {
                    $mirroredProductIdList = substr($mirroredProductIdList, 0, -1);
                }

                // ----------------------------------------------------------------
                // Get products that have this group set as main group
                // ----------------------------------------------------------------
                if ($this->isFilteredByManufacturer()) {
                    $manufacturer = Manufacturer::getByUrlSegment($this->urlParams['ID']);
                    if ($manufacturer instanceof Manufacturer &&
                        $manufacturer->exists()) {
                        $this->addListFilter('ManufacturerID', $manufacturer->ID);
                    }
                }

                if (empty($mirroredProductIdList)) {
                    $this->listFilters['original'] = sprintf(
                        "ProductGroupID IN (%s)",
                        $translationProductGroupIDList
                    );
                } else {
                    $this->listFilters['original'] = sprintf(
                        "(ProductGroupID IN (%s) OR
                        \"%s\".\"ID\" IN (%s))",
                        $translationProductGroupIDList,
                        Tools::get_table_name(Product::class),
                        $mirroredProductIdList
                    );
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

                foreach ($this->listFilters as $listFilterIdentifier => $listFilter) {
                    $filter .= ' ' . $listFilter;
                }
                $this->extend('updateGetProductsFilter', $filter);
               
                if (!$sort) {
                    $sort = Product::defaultSort();
                    $this->extend('updateGetProductsSort', $sort);
                }
                
                $groupProducts = Product::getProducts($filter, $sort);
                $this->extend('onAfterGetProducts', $groupProducts);
                $this->groupProducts[$hashKey] = $groupProducts;
                $this->totalNumberOfProducts   = $groupProducts->count();
            }

            // Inject additional methods into the ArrayList
            if ($this->groupProducts[$hashKey]) {
                $this->groupProducts[$hashKey]->HasMorePagesThan = $this->HasMorePagesThan;
            }
        }
        
        return $this->groupProducts[$hashKey];
    }

    /**
     * Returns the cache key parts for this product group
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2018
     */
    public function CacheKeyParts() {
        $cacheKeyParts = $this->data()->CacheKeyParts();
        $this->extend('updateCacheKeyParts', $cacheKeyParts);
        return $cacheKeyParts;
    }
    
    /**
     * Returns the cache key for this product group
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2018
     */
    public function CacheKey() {
        $cacheKey = $this->data()->CacheKey();
        $this->extend('updateCacheKey', $cacheKey);
        return $cacheKey;

    }

    /**
     * Returns the products (all or by the given hash key)
     *
     * @param string $hashKey Hash key to get products for
     * 
     * @return array 
     */
    public function getGroupProducts($hashKey = null) {
        if (is_null($hashKey)) {
            $groupProducts = $this->groupProducts;
        } elseif (array_key_exists($hashKey, $this->groupProducts)) {
            $groupProducts = $this->groupProducts[$hashKey];
        } else {
            $groupProducts = [];
        }
        return $groupProducts;
    }
    
    /**
     * Sets the products (all or by the given hash key)
     *
     * @param array  $groupProducts Products to set
     * @param string $hashKey       Hash key to set products for
     * 
     * @return void 
     */
    public function setGroupProducts($groupProducts, $hashKey = null) {
        if (is_null($hashKey)) {
            $this->groupProducts = $groupProducts;
        } else {
            $this->groupProducts[$hashKey] = $groupProducts;
        }
    }

    /**
     * All products of this group
     * 
     * @param int    $numberOfProducts The number of products to return
     * @param string $addFilter        Optional filter to add
     * 
     * @return DataList all products of this group or FALSE
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.10.2013
     */
    public function getRandomProducts($numberOfProducts, $addFilter = null) {
        $listFilters = [];
        $filter      = '';

        // ----------------------------------------------------------------
        // Get products that have this group set as mirror group
        // ----------------------------------------------------------------
        
        $mirroredProductIdList  = '';
        $mirroredProductIDs     = $this->getMirroredProductIDs();

        foreach ($mirroredProductIDs as $mirroredProductID) {
            $mirroredProductIdList .= sprintf(
                "'%s',",
                $mirroredProductID
            );
        }

        if (!empty($mirroredProductIdList)) {
            $mirroredProductIdList = substr($mirroredProductIdList, 0, -1);
        }

        // ----------------------------------------------------------------
        // Get products that have this group set as main group
        // ----------------------------------------------------------------
        if ($this->isFilteredByManufacturer()) {
            $manufacturer = Manufacturer::getByUrlSegment($this->urlParams['ID']);
            if ($manufacturer) {
                $this->addListFilter('ManufacturerID', $manufacturer->ID);
            }
        }

        if (empty($mirroredProductIdList)) {
            $listFilters['original'] = sprintf(
                "ProductGroupID = '%s'",
                $this->ID
            );
        } else {
            $listFilters['original'] = sprintf(
                "(ProductGroupID = '%s' OR
                  \"%s\".\"ID\" IN (%s))",
                $this->ID,
                Tools::get_table_name(Product::class),
                $mirroredProductIdList
            );
        }

        foreach ($listFilters as $listFilterIdentifier => $listFilter) {
            $filter .= ' ' . $listFilter;
        }
        
        if (!is_null($addFilter)) {
            $filter .= ' AND ' . $addFilter;
        }

        $sort = 'RAND()';

        $products = Product::getProducts($filter, $sort, null, $numberOfProducts);
        
        return $products;
    }
    
    /**
     * Returns the number of products per page according to where it is set.
     * Highest priority has the customer's configuration setting if available.
     * Next comes the shop owners setting for this page; if that's not
     * configured we use the global setting from Config.
     *
     * @return int
     */
    public function getProductsPerPageSetting() {
        $member          = Customer::currentUser();
        $productsPerPage = self::getProductsPerPage();
        if (is_null($productsPerPage)) {
            if ($member &&
                $member->getCustomerConfig() &&
                $member->getCustomerConfig()->productsPerPage !== null &&
                array_key_exists($member->getCustomerConfig()->productsPerPage, Config::$productsPerPageOptions)) {

                $productsPerPage = $member->getCustomerConfig()->productsPerPage;
            } else if ($this->productsPerPage) {
                $productsPerPage = $this->productsPerPage;
            } else {
                $productsPerPage = Config::ProductsPerPage();
            }
        }
        if ($productsPerPage == 0) {
            $productsPerPage = Config::getProductsPerPageUnlimitedNumber();
        }
        
        return $productsPerPage;
    }

    /**
     * Sets the products per page count.
     *
     * @param int $count Count of products to show in a list.
     * 
     * @return void
     */
    public static function setProductsPerPage($count) {
        if (array_key_exists($count, Config::$productsPerPageOptions)) {
            Tools::Session()->set('SilvercartProductGroup.productsPerPage', $count);
            Tools::saveSession();
        }
    }

    /**
     * Returns the products per page count.
     * 
     * @return int
     */
    public static function getProductsPerPage() {
        return Tools::Session()->get('SilvercartProductGroup.productsPerPage');
    }

    /**
     * Returns the sortable frontend fields as ArrayList.
     * 
     * @return ArrayList
     */
    public function getSortableFrontendFields() {
        if (is_null($this->sortableFrontendFields)) {
            $this->sortableFrontendFields = new ArrayList();
            $product                      = Product::singleton();
            $sortableFrontendFields       = array_values($product->sortableFrontendFields());
            asort($sortableFrontendFields);
            
            foreach ($sortableFrontendFields as $option => $value) {
                $this->sortableFrontendFields->push(
                        new ArrayData(
                                array(
                                    'Option' => $option,
                                    'Value'  => $value,
                                )
                        )
                );
            }
            
            $sortableFrontendFieldValues = array_flip(array_keys($product->sortableFrontendFields()));
            if (!array_key_exists($product->getDefaultSort(), $sortableFrontendFieldValues)) {
                $sortableFrontendFieldValues[$product->getDefaultSort()] = 0;
            }
            $this->currentSortableFrontendFieldLabel = $sortableFrontendFields[$sortableFrontendFieldValues[$product->getDefaultSort()]];
        }
        return $this->sortableFrontendFields;
    }

    /**
     * Returns the current sortable frontend field label.
     * 
     * @return string
     */
    public function getCurrentSortableFrontendFieldLabel() {
        if (is_null($this->currentSortableFrontendFieldLabel)) {
            $this->getSortableFrontendFields();
        }
        return $this->currentSortableFrontendFieldLabel;
    }

    /**
     * Return the start value for the limit part of the sql query that
     * retrieves the product list for the current product group page.
     * 
     * @param int|bool $numberOfProducts The number of products to return
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2012
     */
    public function getSqlOffset($numberOfProducts = false) {
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
                if (!isset($_GET['start']) ||
                    !is_numeric($_GET['start']) ||
                    (int)$_GET['start'] < 1) {

                    if (isset($_GET['offset'])) {
                        // --------------------------------------------------------
                        // Use offset for getting the current item rage
                        // --------------------------------------------------------
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
                        // --------------------------------------------------------
                        // Use item number for getting the current item range
                        // --------------------------------------------------------
                        $SQL_start = 0;
                    }
                } else {
                    $SQL_start = (int) $_GET['start'];
                }
            }
            $this->sqlOffsets[$sqlOffsetKey] = $SQL_start;
        }
        return $this->sqlOffsets[$sqlOffsetKey];
    }
    
    /**
     * All viewable product groups of this group.
     *
     * @param int $numberOfProductGroups Number of product groups to display
     * 
     * @return PaginatedList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Ramon Kupper <rkupper@pixeltricks.de>
     * @since 04.01.2014
     */
    public function getViewableChildren($numberOfProductGroups = false) {
        if ($this->viewableChildren === null) {
            $viewableChildren = new ArrayList();
            
            foreach ($this->Children() as $child) {
                if ($child->hasProductsOrChildren()) {
                    $viewableChildren->push($child);
                }
            }
            if ($viewableChildren->count() > 0) {
                if ($numberOfProductGroups == false) {
                    if ($this->productGroupsPerPage) {
                        $pageLength = $this->productGroupsPerPage;
                    } else {
                        $pageLength = Config::ProductGroupsPerPage();
                    }
                } else {
                    $pageLength = $numberOfProductGroups;
                }

                $pageStart = $this->getSqlOffsetForProductGroups($numberOfProductGroups);

                $viewableChildrenPage = new PaginatedList($viewableChildren, $this->getRequest());
                $viewableChildrenPage->setPaginationGetVar('groupStart');
                $viewableChildrenPage->setPageStart($pageStart);
                $viewableChildrenPage->setPageLength($pageLength);
                
                $this->viewableChildren = $viewableChildrenPage;         
            } else {
                return false;
            }
        }
        return $this->viewableChildren;
    }
    
    /**
     * Indicates wether there are more viewable product groups than the given
     * number.
     *
     * @param int $nrOfViewableChildren The number to check against
     * 
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.11.2011
     */
    public function HasMoreViewableChildrenThan($nrOfViewableChildren) {
        if ($this->getViewableChildren()->count() > $nrOfViewableChildren) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Return the start value for the limit part of the sql query that
     * retrieves the product group list for the current product group page.
     * 
     * @param int|bool $numberOfProductGroups The number of product groups to return
     *
     * @return int
     */
    public function getSqlOffsetForProductGroups($numberOfProductGroups = false) {
        if ($this->productGroupsPerPage) {
            $productGroupsPerPage = $this->productGroupsPerPage;
        } else {
            $productGroupsPerPage = Config::ProductsPerPage();
        }

        if ($numberOfProductGroups !== false) {
            $productGroupsPerPage = (int) $numberOfProductGroups;
        }
            
        if (!isset($_GET['groupStart']) ||
            !is_numeric($_GET['groupStart']) ||
            (int)$_GET['groupStart'] < 1) {

            if (isset($_GET['groupOffset'])) {
                // --------------------------------------------------------
                // Use offset for getting the current item rage
                // --------------------------------------------------------
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
                // --------------------------------------------------------
                // Use item number for getting the current item range
                // --------------------------------------------------------
                $SQL_start = 0;
            }
        } else {
            $SQL_start = (int) $_GET['groupStart'];
        }
        
        return $SQL_start;
    }

    /**
     * Indicates wether the resultset of the product query returns more items
     * than the number given (defaults to 10).
     *
     * @param int $maxResults The number of results to check
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.04.2011
     */
    public function HasMorePagesThan($maxResults = 10) {
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
     * Indicates wether the resultset of the product query returns more
     * products than the number given (defaults to 10).
     * 
     * @param int $maxResults The maximum count of results
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public function HasMoreProductsThan($maxResults = 10) {
        $products = $this->getProducts();
        if ($products &&
            $products->count() > $maxResults) {
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Indicates wether the resultset of the product query returns less
     * products than the number given (defaults to 10).
     * 
     * @param int $maxResults The maximum count of results
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public function HasLessProductsThan($maxResults = 10) {
        $products = $this->getProducts();
        
        if ($products &&
            $products->count() < $maxResults) {
            return true;
        }
        
        return false;
    }

    /**
     * Returns $Content of the page. If it's empty and
     * the option is set to use the content of a parent page we try to find
     * the first parent page with content and deliver that.
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getPageContent() {
        $content = Tools::string2html('');
        if (!empty($this->Content) ||
            !$this->useContentFromParent) {
            $content = Tools::string2html($this->Content);
        } else {
            $page = $this;

            while ($page->ParentID > 0) {
                if (!empty($page->Content)) {
                    $content = Tools::string2html($page->Content);
                    break;
                }

                $page = SiteTree::get()->byID($page->ParentID);
            }
        }
        
        return $content;
    }
    
    /**
     * Getter for an products image.
     *
     * @return Image
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getProductImage() {
        return Product::image();
    }

    /**
     * Action to show a product detail page.
     * Returns the rendered detail page.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2013
     */
    public function detail(HTTPRequest $request) {
        $params     = $request->allParams();
        $productID  = $params['ID'];
        $product    = Product::get()->byID($productID);
        
        if (!($product instanceof Product) ||
            !$product->exists()) {
            return $this->httpError(404);
        }
        
        $productLink = $product->Link();
        $calledLink  = $request->getURL();

        if (strpos($calledLink, '/') != strpos($productLink, '/')) {
            if (strpos($productLink, '/') == 0) {
                $calledLink = '/' . $calledLink;
            } elseif (strpos($calledLink, '/') == 0) {
                $productLink = '/' . $productLink;
            }
        }

        if ($calledLink != $productLink) {
            Tools::redirectPermanentlyTo($productLink);
        }
        
        $this->setProduct($product);
        return $this->render();
    }
    
    /**
     * chsffopt stands for "CHange Sortable Frontend Field Option".
     * Changes the sort order type for product lists.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.11.2014
     */
    public function chsffopt(HTTPRequest $request) {
        $newOption                   = $request->param('ID');
        $product                     = Product::singleton();
        $sortableFrontendFields      = $product->sortableFrontendFields();
        $sortableFrontendFieldValues = array_keys($sortableFrontendFields);
        if (array_key_exists($newOption, $sortableFrontendFieldValues)) {
            $sortOrder = $sortableFrontendFieldValues[$newOption];
            Product::setDefaultSort($sortOrder);
        }
        $this->redirect($this->Link());
    }
    
    /**
     * chpppopt stands for "CHange Products Per Page Option".
     * Changes the quantity of products to display in a product lists.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.11.2014
     */
    public function chpppopt(HTTPRequest $request) {
        $member                      = Customer::currentUser();
        $newOption                   = $request->param('ID');
        $product                     = Product::singleton();
        $sortableFrontendFields      = $product->sortableFrontendFields();
        $sortableFrontendFieldValues = array_keys($sortableFrontendFields);
        if (array_key_exists($newOption, $sortableFrontendFieldValues)) {
            $sortOrder = $sortableFrontendFieldValues[$newOption];
            Product::setDefaultSort($sortOrder);
        }
        
        if ($member instanceof Member &&
            $member->exists()) {
            $member->getCustomerConfig()->productsPerPage = $newOption;
            $member->getCustomerConfig()->write();
        }
        self::setProductsPerPage($newOption);
        $this->redirect($this->Link());
    }
    
    /**
     * Returns the detail product to show
     * 
     * @return Product
     */
    public function getProduct() {
        return $this->product;
    }
    
    /**
     * Sets the detail product to show
     * 
     * @param Product $product The detail product to show
     * 
     * @return void
     */
    public function setProduct($product) {
        $this->product = $product;
    }
    
    /**
     * Workaround to be able to access the current HTTPRequest in self::getDetailViewProduct().
     * 
     * @param HTTPRequest $request HTTP request
     * 
     * @return HTTPResponse
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function handleRequest(HTTPRequest $request) {
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
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.05.2017
     */
    public function handleAction($request, $action) {
        if ($this->isProductDetailView()) {
            $this->urlParams['Action'] = (int) $this->urlParams['Action'];

            if (!empty($this->urlParams['OtherID'])) {
                $secondaryAction = $this->urlParams['OtherID'];
                if ($this->hasMethod($secondaryAction) &&
                    $this->hasAction($secondaryAction)) {

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
                    $calledLink = '/' . $calledLink;
                } elseif (strpos($calledLink, '/') == 0) {
                    $productLink = '/' . $productLink;
                }
            }
            
            if ($calledLink != $productLink) {
                Tools::redirectPermanentlyTo($productLink);
            }
            
            $this->setProduct($product);
            return $this->render();
        } elseif ($this->isFilteredByManufacturer()) {
            $url = str_replace($this->urlParams['Action'] . '/' . $this->urlParams['ID'], '', $_REQUEST['url']);
            $this->urlParams['Action'] = '';
            $this->urlParams['ID'] = '';
            $customRequest = new HTTPRequest('GET', $url, [], [], null);
            return parent::handleAction($customRequest, $action);
        }
        return parent::handleAction($request, $action);
    }
    
    /**
     * Overwrites checking for an existing action if a product detail view is called.
     * 
     * @param string $action Action to check
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.05.2017
     */
	public function hasAction($action) {
        $hasAction = parent::hasAction($action);
        if (!$hasAction &&
            $this->isProductDetailView()) {
            $hasAction = true;
        }
        return $hasAction;
    }
    
    /**
     * Overwrites access handling if a product detail view is called.
     * 
     * @param string $action Action to check access for
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.05.2017
     */
    public function checkAccessAction($action) {
        $hasAccess = parent::checkAccessAction($action);
        if (!$hasAccess &&
            $this->isProductDetailView()) {
            $hasAccess = true;
        }
        return $hasAccess;
    }

    /**
     * Return an SSViewer object to process the data
     * Manipulates the SSViewer in case of a product detail view.
     * 
     * @param string $action Action
     * 
     * @return SSViewer The viewer identified being the default handler for this Controller/Action combination
     */
    public function getViewer($action) {
        $viewer = parent::getViewer($action);
        if ($this->isProductDetailView()) {
            $templates = $viewer->templates();
            $viewer    = new SSViewer(
                    array(
                        'SilverCart\Model\Pages\ProductGroupPage_detail',
                        basename($templates['main'], '.ss')
                    )
            );
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
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.07.2012
     */
    public function customise($data) {
        if ($this->isProductDetailView()) {
            $data = array_merge(
                $data,
                $this->ProductDetailViewParams()
            );
        }
        $customisedData = parent::customise($data);
        return $customisedData;
    }

    /**
     * renders a product detail view template (if requested)
     *
     * @return string the redered template
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.07.2012
     */
    protected function ProductDetailView() {
        if ($this->isProductDetailView()) {
            $output = $this->customise([])->renderWith(array('SilverCart\Model\Pages\ProductGroupPage_detail','Page'));
            
            return $output;
        }
        return false;
    }

    /**
     * renders a product detail view template (if requested)
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.07.2012
     */
    protected function ProductDetailViewParams() {
        if ($this->isProductDetailView() &&
            empty($this->productDetailViewParams)) {
            $product                        = $this->getDetailViewProduct();
            $this->productDetailViewParams  = array(
                'getProduct'    => $product,
                'MetaTitle'     => $this->DetailViewProductMetaTitle(),
                'MetaTags'      => $this->DetailViewProductMetaTags(false),
            );
        }
        return $this->productDetailViewParams;
    }

    /**
     * checks whether the requested view is an product detail view or a product
     * group view.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.02.2011
     */
    public function isProductDetailView() {
        $isProductDetailView = false;
        if ($this->getDetailViewProduct() instanceof Product) {
            $isProductDetailView = true;
        }
        return $isProductDetailView;
    }

    /**
     * returns the chosen product when requesting a product detail view.
     *
     * @return Product
     */
    public function getDetailViewProduct() {
        if (!array_key_exists('Action', $this->urlParams) ||
            !is_numeric($this->urlParams['Action'])) {
            return null;
        }
        if (is_null($this->detailViewProduct)) {
            $this->detailViewProduct = Product::get()->byID(Convert::raw2sql($this->urlParams['Action']));
        }

        return $this->detailViewProduct;
    }
    
    /**
     * Returns the SQL filter statement for the current query.
     * 
     * @param string $excludeFilter The name of the filter to exclude
     *
     * @return string
     */
    public function getListFilters($excludeFilter = false) {
        $filter = '';
        
        foreach ($this->listFilters as $listFilterIdenfitier => $listFilter) {
            if ($listFilterIdenfitier != $excludeFilter) {
                $filter .= ' ' . $listFilter;
            }
        }
        
        return $filter;
    }

    /**
     * Because of a url rule defined for this page type in the _config.php, the function MetaTags does not work anymore.
     * This function overloads it and parses the meta data attributes of Product
     *
     * @param boolean $includeTitle should the title tag be parsed?
     *
     * @return string with all meta tags
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.06.2013
     */
    protected function DetailViewProductMetaTags($includeTitle = false) {
        $canonicalTag = '';
        if ($this->isProductDetailView()) {
            $product = $this->getDetailViewProduct();
            $this->MetaKeywords                 = $product->MetaKeywords;
            $this->MetaDescription              = $product->MetaDescription;
            $this->dataRecord->MetaKeywords     = $product->MetaKeywords;
            $this->dataRecord->MetaDescription  = $product->MetaDescription;
                    
            if ($product->IsMirroredView()) {
                $canonicalTag = sprintf(
                        '<link rel="canonical" href="%s"/>' . "\n",
                        $product->CanonicalLink()
                );
            }
        }
        $tags = parent::MetaTags($includeTitle);
        $tags .= $canonicalTag;
        return $tags;
    }

    /**
     * for SEO reasons this pages attribute MetaTitle gets overwritten with the products MetaTitle
     * Remember: search engines evaluate 64 characters of the MetaTitle only
     *
     * @return string|false the products MetaTitle
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.11.10
     */
    protected function DetailViewProductMetaTitle() {
        $product        = $this->getDetailViewProduct();
        $extendedOutput = $this->extend('overwriteDetailViewProductMetaTitle', $product);

        if (empty($extendedOutput)) {
            if ($product && $product->MetaTitle) {
                if ($product->Manufacturer()->ID > 0) {
                    return $product->MetaTitle ."/". $product->Manufacturer()->Title;
                }
                return $product->MetaTitle;
            } else {
                return false;
            }
        } else {
            return $extendedOutput[0];
        }
    }

    /**
     * Checks whether the product list should be filtered by manufacturer.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.01.2013
     */
    public function isFilteredByManufacturer() {
        if ($this->getRequest()) {
            $params = $this->getRequest()->allParams();

            if (is_array($params) &&
                    array_key_exists('Action', $params) &&
                    $params['Action'] == Manufacturer::get_filter_action() &&
                    !empty ($params['ID'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2011
     */
    public function addListFilter($property, $value, $comparison = '=', $operator = 'AND') {
        if ($comparison == 'IN') {
            $this->listFilters[] = " \"" . $property . "\" " . $comparison . " (" . $value . ")" . $operator;
        } else {
            $this->listFilters[] = " \"" . $property . "\" " . $comparison . " '" . $value . "'" . $operator;
        }
    }
    
    /**
     * Returns whether the current view is the first page of the product list or not
     *
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.06.2012
     */
    public function isFirstPage() {
        $isFirstPage = true;
        if ($this->getSqlOffset() > 0) {
            $isFirstPage = false;
        }
        return $isFirstPage;
    }
    
    /**
     * Returns injected products
     *
     * @param array $excludeWidgets Optional: array of widgets to exclude.
     *
     * @return ArrayList 
     */
    public function getInjectedProducts($excludeWidgets = []) {
        $injectedProducts = new ArrayList();
        if ($this->WidgetSetContent()->count() > 0) {
            foreach ($this->WidgetSetContent() as $widgetSet) {
                if ($widgetSet->WidgetArea()->Widgets()->count() > 0) {
                    foreach ($widgetSet->WidgetArea()->Widgets() as $widget) {
                        if (in_array(get_class($widget), $excludeWidgets)) {
                            continue;
                        }
                        $controllerClass = get_class($widget) . 'Controller';
                        if (method_exists($controllerClass, 'getProducts')) {
                            $controller = new $controllerClass($widget);
                            $products   = $controller->getProducts();
                            if ($products instanceof SS_List) {
                                $injectedProducts->merge($products);
                            }
                        }
                    }
                }
            }
        }
        return $injectedProducts;
    }

}