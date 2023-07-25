<?php

namespace SilverCart\Model\Pages;

use PageController;
use ReflectionClass;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\ProductGroupPageSelectorsForm;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Product\Manufacturer;
use SilverCart\Model\Pages\ProductGroupSorting;
use SilverCart\Model\Product\Product;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\ORM\SS_List;
use SilverStripe\View\ArrayData;

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
class ProductGroupPageController extends PageController
{
    use ProductGroupSorting;
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
     * @var bool
     */
    public $forceLoadOfWidgets = true;
    /**
     * Contains the viewable children of this page for caching purposes.
     *
     * @var PaginatedList|null
     */
    protected $viewableChildren = null;

    /**
     * Indicates wether a filter plugin can be registered for the current view.
     *
     * @return bool
     */
    public function canRegisterFilterPlugin() : bool
    {
        return ! (bool) $this->isProductDetailView();
    }

    /**
     * Returns the cache key for the product group page list view.
     *
     * @return string
     */
    public function CacheKeyProductGroupPageControls() : string
    {
        return implode('_', [
            $this->ID,
            $this->SQL_start,
            $this->getProductsPerPageSetting()
        ]);
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
    protected function init(bool $skip = false) : void
    {
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
     * returns the top product group (first product group under ProductGroupHolder)
     *
     * @param ProductGroupPage $productGroup product group
     *
     * @return ProductGroupPage
     */
    public function getTopProductGroup(ProductGroupPage $productGroup = null) : ProductGroupPage
    {
        if (is_null($productGroup)) {
            $productGroup = $this;
        }
        if ($productGroup->Parent()->ClassName === ProductGroupHolder::class
         || $productGroup->ParentID == 0
        ) {
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
    public function OriginalLink(string $action = null) : string
    {
        return (string) $this->data()->OriginalLink($action);
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
    public function getProductsForced($numberOfProducts = false, $sort = false, $disableLimit = false)
    {
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
     * @return PaginatedList
     */
    public function getProducts($numberOfProducts = false, $sort = false, $disableLimit = false, $force = false) : PaginatedList
    {
        $hashKey = md5($numberOfProducts . '_' . $sort . '_' . $disableLimit . Tools::current_locale());

	    if ($this->data()->DoNotShowProducts
         && !$force
        ) {
            $this->groupProducts[$hashKey] = PaginatedList::create(ArrayList::create());
        } elseif (!array_key_exists($hashKey, $this->groupProducts)
               || $force
        ) {
            $SQL_start       = $this->getSqlOffset($numberOfProducts);
            $productsPerPage = $this->getProductsPerPageSetting();
            $products        = null;
            $this->extend('overwriteGetProducts', $products, $numberOfProducts, $productsPerPage, $SQL_start, $sort);
            if (!is_null($products)) {
                $this->groupProducts[$hashKey] = $products;
            } else {
                $filter = $this->getProductsFilter();
                if (!$sort) {
                    $sort = Product::defaultSort();
                    $this->extend('updateGetProductsSort', $sort);
                }
                $paginatedProducts = PaginatedList::create(Product::getProductsList($filter, $sort), $_GET);
                $paginatedProducts->setPageLength($this->getProductsPerPageSetting());
                $this->extend('onAfterGetProducts', $paginatedProducts);
                $this->groupProducts[$hashKey] = $paginatedProducts;
                $this->totalNumberOfProducts   = $paginatedProducts->count();
            }
            if ($this->groupProducts[$hashKey]) {
                $this->groupProducts[$hashKey]->HasMorePagesThan = $this->HasMorePagesThan;
            }
        }
        return $this->groupProducts[$hashKey];
    }

    /**
     * Returns the products (all or by the given hash key)
     *
     * @param string $hashKey Hash key to get products for
     *
     * @return array
     */
    public function getGroupProducts(string $hashKey = null)
    {
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
    public function setGroupProducts($groupProducts, $hashKey = null) : ProductGroupPageController
    {
        if (is_null($hashKey)) {
            $this->groupProducts = $groupProducts;
        } else {
            $this->groupProducts[$hashKey] = $groupProducts;
        }
        return $this;
    }

    /**
     * All products of this group
     *
     * @param int    $numberOfProducts The number of products to return
     * @param string $addFilter        Optional filter to add
     *
     * @return DataList
     */
    public function getRandomProducts(int $numberOfProducts, string $addFilter = null)
    {
        $listFilters           = [];
        $filter                = '';
        $sort                  = 'RAND()';
        $mirroredProductIdList = implode(',', $this->getMirroredProductIDs());
        if ($this->isFilteredByManufacturer()) {
            $manufacturer = Manufacturer::getByUrlSegment($this->urlParams['ID']);
            if ($manufacturer) {
                $this->addListFilter('ManufacturerID', $manufacturer->ID);
            }
        }
        if (empty($mirroredProductIdList)) {
            $listFilters['original'] = "ProductGroupID = '{$this->ID}'";
        } else {
            $pStageTable             = Product::singleton()->getStageTableName();
            $listFilters['original'] = "(ProductGroupID = '{$this->ID}'"
                                     . " OR {$pStageTable}.ID IN ({$mirroredProductIdList}))";
        }
        foreach ($listFilters as $listFilter) {
            $filter .= " {$listFilter}";
        }
        if (!is_null($addFilter)) {
            $filter .= " AND {$addFilter}";
        }
        return Product::getProducts($filter, $sort, null, $numberOfProducts);
    }

    /**
     * Returns the number of products per page according to where it is set.
     * Highest priority has the customer's configuration setting if available.
     * Next comes the shop owners setting for this page; if that's not
     * configured we use the global setting from Config.
     *
     * @return int
     */
    public function getProductsPerPageSetting()
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
    public static function setProductsPerPage(int $count)
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
    public static function getProductsPerPage() : ?int
    {
        return Tools::Session()->get('SilvercartProductGroup.productsPerPage');
    }

    /**
     * Returns the sortable frontend fields as ArrayList.
     *
     * @return ArrayList
     */
    public function getSortableFrontendFields() : ArrayList
    {
        if (is_null($this->sortableFrontendFields)) {
            $this->sortableFrontendFields = ArrayList::create();
            $product                      = Product::singleton();
            $sortableFrontendFields       = array_values($product->sortableFrontendFields());
            asort($sortableFrontendFields);
            foreach ($sortableFrontendFields as $option => $value) {
                $this->sortableFrontendFields->push(ArrayData::create([
                    'Option' => $option,
                    'Value'  => $value,
                ]));
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
    public function getCurrentSortableFrontendFieldLabel() : string
    {
        if (is_null($this->currentSortableFrontendFieldLabel)) {
            $this->getSortableFrontendFields();
        }
        return (string) $this->currentSortableFrontendFieldLabel;
    }

    /**
     * All viewable product groups of this group.
     *
     * @param int $numberOfProductGroups Number of product groups to display
     *
     * @return PaginatedList
     */
    public function getViewableChildren($numberOfProductGroups = false) : PaginatedList
    {
        if ($this->viewableChildren === null) {
            $viewableChildren = ArrayList::create();
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
                $pageStart            = $this->getSqlOffsetForProductGroups($numberOfProductGroups);
                $viewableChildrenPage = PaginatedList::create($viewableChildren, $this->getRequest());
                $viewableChildrenPage->setPaginationGetVar('groupStart');
                $viewableChildrenPage->setPageStart($pageStart);
                $viewableChildrenPage->setPageLength($pageLength);
                $this->viewableChildren = $viewableChildrenPage;
            } else {
                $this->viewableChildren = PaginatedList::create(ArrayList::create());
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
     * @return bool
     */
    public function HasMoreViewableChildrenThan(int $nrOfViewableChildren) : bool
    {
        return $this->getViewableChildren()->count() > $nrOfViewableChildren;
    }

    /**
     * Returns $Content of the page. If it's empty and
     * the option is set to use the content of a parent page we try to find
     * the first parent page with content and deliver that.
     *
     * @return DBHTMLText
     */
    public function getPageContent() : DBHTMLText
    {
        $content = Tools::string2html('');
        if (!empty($this->Content)
         || !$this->useContentFromParent
        ) {
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
        return $content->setProcessShortcodes(true);
    }

    /**
     * Action to show a product detail page.
     * Returns the rendered detail page.
     *
     * @param HTTPRequest $request Request
     *
     * @return DBHTMLText
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2013
     */
    public function detail(HTTPRequest $request) : DBHTMLText
    {
        $params     = $request->allParams();
        $productID  = $params['ID'];
        $product    = Product::get()->byID($productID);
        if (!($product instanceof Product)
         || !$product->exists()
        ) {
            $this->httpError(404);
        }
        $productLink = $product->Link();
        $calledLink  = $request->getURL();
        if (strpos($calledLink, '/') != strpos($productLink, '/')) {
            if (strpos($productLink, '/') == 0) {
                $calledLink = "/{$calledLink}";
            } elseif (strpos($calledLink, '/') == 0) {
                $productLink = "/{$productLink}";
            }
        }
        if ($calledLink != $productLink) {
            Tools::redirectPermanentlyTo($productLink);
        }
        $this->setProduct($product);
        return $this->render();
    }


    /**
     * Returns the detail product to show
     *
     * @return Product
     */
    public function getProduct() : ?Product
    {
        return $this->product;
    }

    /**
     * Sets the detail product to show
     *
     * @param Product $product The detail product to show
     *
     * @return ProductGroupPageController
     */
    public function setProduct(Product $product = null) : ProductGroupPageController
    {
        $this->product = $product;
        return $this;
    }

    /**
     * renders a product detail view template (if requested)
     *
     * @return string the redered template
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.07.2012
     */
    protected function ProductDetailView()
    {
        if ($this->isProductDetailView()) {
            $output = $this->customise([])->renderWith(['SilverCart\Model\Pages\ProductGroupPage_detail', 'Page']);
            return $output;
        }
        return false;
    }

    /**
     * Because of a url rule defined for this page type in the _config.php, the function MetaTags does not work anymore.
     * This function overloads it and parses the meta data attributes of Product
     *
     * @param boolean $includeTitle should the title tag be parsed?
     *
     * @return string
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.06.2013
     */
    protected function DetailViewProductMetaTags($includeTitle = false)
    {
        $canonicalTag = '';
        if ($this->isProductDetailView()) {
            $product = $this->getDetailViewProduct();
            $this->MetaDescription              = $product->MetaDescription;
            $this->dataRecord->MetaDescription  = $product->MetaDescription;
            if ($product->IsMirroredView()) {
                $canonicalTag = "<link rel=\"canonical\" href=\"{$product->CanonicalLink()}\"/>\n";
            }
        }
        $tags  = parent::MetaTags($includeTitle);
        $tags .= $canonicalTag;
        return $tags;
    }

    /**
     * for SEO reasons this pages attribute MetaTitle gets overwritten with the products MetaTitle
     * Remember: search engines evaluate 64 characters of the MetaTitle only
     *
     * @return string|false
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.11.10
     */
    protected function DetailViewProductMetaTitle()
    {
        $product        = $this->getDetailViewProduct();
        $extendedOutput = $this->extend('overwriteDetailViewProductMetaTitle', $product);
        if (empty($extendedOutput)) {
            if ($product
             && $product->MetaTitle
            ) {
                if ($product->Manufacturer()->ID > 0) {
                    return "{$product->MetaTitle}/{$product->Manufacturer()->Title}";
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
     * Returns injected products
     *
     * @param array $excludeWidgets Optional: array of widgets to exclude.
     *
     * @return ArrayList
     */
    public function getInjectedProducts(array $excludeWidgets = []) : ArrayList
    {
        $injectedProducts = ArrayList::create();
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
