<?php

namespace SilverCart\Model\Pages;

use Page;
use SilverCart\Dev\SeoTools;
use SilverCart\Dev\Tools;
use SilverCart\Forms\FormFields\FieldGroup;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Product\Product;
use SilverCart\ORM\ExtensibleDataObject;
use SilverCart\View\GroupView\GroupViewHandler;
use SilverStripe\CMS\Model\RedirectorPage;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\Map;
use SilverStripe\ORM\PaginatedList;
use function _t;

/**
 * Page to display a group of products.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 *
 * @property int    $productGroupsPerPage          product Groups Per Page
 * @property string $DefaultGroupHolderView        Default Group Holder View
 * @property string $UseOnlyDefaultGroupHolderView Use Only Default Group Holder View
 * @property string $DefaultGroupView              Default Group View
 * @property string $UseOnlyDefaultGroupView       Use Only Default Group View
 * @property bool   $RedirectToProductGroup        Redirect To Product Group
 * @property bool   $DoNotShowProducts             Do Not Show Products
 *
 * @method SiteTree LinkTo() Returns the related page to link to.
 */
class ProductGroupHolder extends Page
{
    use ExtensibleDataObject;
    /**
     * Attributes.
     *
     * @var string[]
     */
    private static array $db = [
        'productGroupsPerPage'          => 'Int',
        'DefaultGroupHolderView'        => 'Varchar(255)',
        'UseOnlyDefaultGroupHolderView' => 'Enum("no,yes,inherit","inherit")',
        'DefaultGroupView'              => 'Varchar(255)',
        'UseOnlyDefaultGroupView'       => 'Enum("no,yes,inherit","inherit")',
        'RedirectToProductGroup'        => 'Boolean(0)',
        'DoNotShowProducts'             => 'Boolean(1)',
    ];
    /**
     * Has one relations.
     *
     * @var string[]
     */
    private static array $has_one = [
        'LinkTo' => SiteTree::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static string $table_name = 'SilvercartProductGroupHolder';
    /**
     * Allowed children in site tree
     *
     * @var string[]
     */
    private static array $allowed_children = [
        ProductGroupPage::class,
        RedirectorPage::class,
    ];
    /**
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     *
     * @var string
     */
    private static string $icon_class = 'font-icon-p-gallery';
    /**
     * Indicator to check whether getCMSFields is called
     *
     * @var bool
     */
    protected $getCMSFieldsIsCalled = false;
    /**
     * Cache key parts for this product group
     *
     * @var array
     */
    protected array|null $cacheKeyParts = null;
    /**
     * Cache key for this product group
     *
     * @var string
     */
    protected string|null$cacheKey = null;
    /**
     * Saves the result from $this->getProducts()
     *
     * @var array
     */
    protected array $cachedProducts = [];
    /**
     * Optional pagination context. If not set, the return value of getProducts() will be used.
     *
     * @var PaginatedList
     */
    protected PaginatedList|null $paginationContext = null;

    /**
     * Field labels for display in tables.
     *
     * @param bool $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        $this->beforeUpdateFieldLabels(function(&$labels) {
            $labels = array_merge($labels, [
                    'productGroupsPerPage'          => _t(ProductGroupPage::class . '.PRODUCTGROUPSPERPAGE', 'Product groups per page'),
                    'ProductsPerPageHint'           => _t(ProductGroupPage::class . '.PRODUCTSPERPAGEHINT', 'Set products or product groups per page to 0 (zero) to use the default setting.'),
                    'DefaultGroupHolderView'        => _t(ProductGroupPage::class . '.DEFAULTGROUPHOLDERVIEW', 'Default product group view'),
                    'DoNotShowProducts'             => _t(ProductGroupPage::class . '.DONOTSHOWPRODUCTS', 'do <strong>not</strong> show products of this group'),
                    'UseOnlyDefaultGroupHolderView' => _t(ProductGroupPage::class . '.USEONLYDEFAULTGROUPHOLDERVIEW', 'Allow only default view'),
                    'DefaultGroupView'              => _t(ProductGroupPage::class . '.DEFAULTGROUPVIEW', 'Default product view'),
                    'DefaultGroupViewInherit'       => _t(ProductGroupPage::class . '.DEFAULTGROUPVIEW_DEFAULT', 'Use view from parent pages'),
                    'UseOnlyDefaultGroupView'       => _t(ProductGroupPage::class . '.USEONLYDEFAULTGROUPVIEW', 'Allow only default view'),
                    'DisplaySettings'               => _t(ProductGroupPage::class . '.DisplaySettings', 'Display settings'),
                    'RedirectionSettings'           => _t(ProductGroupHolder::class . '.RedirectionSettings', 'Redirection'),
                    'RedirectToProductGroup'        => _t(ProductGroupHolder::class . '.RedirectToProductGroup', 'Redirect to a product group'),
                    'LinkTo'                        => _t(ProductGroupHolder::class . '.LinkTo', 'Product group to redirect to'),
                    'Yes'                           => Tools::field_label('Yes'),
                    'No'                            => Tools::field_label('No'),
            ]);
        });
        return parent::fieldLabels($includerelations);
    }

    /**
     * Return all fields of the backend.
     *
     * @return FieldList Fields of the CMS
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $useOnlydefaultGroupviewSource  = [
                'inherit' => $this->fieldLabel('DefaultGroupViewInherit'),
                'yes'     => $this->fieldLabel('Yes'),
                'no'      => $this->fieldLabel('No'),
            ];

            $doNotShowProductsField             = CheckboxField::create('DoNotShowProducts',        Tools::string2html($this->fieldLabel('DoNotShowProducts')));
            $defaultGroupViewField              = GroupViewHandler::getGroupViewDropdownField('DefaultGroupView', $this->fieldLabel('DefaultGroupView'), $this->DefaultGroupView, $this->fieldLabel('DefaultGroupViewInherit'));
            $useOnlyDefaultGroupViewField       = DropdownField::create('UseOnlyDefaultGroupView',  $this->fieldLabel('UseOnlyDefaultGroupView'), $useOnlydefaultGroupviewSource, $this->UseOnlyDefaultGroupView);
            $productGroupsPerPageField          = TextField::create('productGroupsPerPage',         $this->fieldLabel('productGroupsPerPage'));
            $defaultGroupHolderViewField        = GroupViewHandler::getGroupViewDropdownField('DefaultGroupHolderView', $this->fieldLabel('DefaultGroupHolderView'), $this->DefaultGroupHolderView, $this->fieldLabel('DefaultGroupView'));
            $useOnlyDefaultGroupHolderViewField = DropdownField::create('UseOnlyDefaultGroupHolderView',  $this->fieldLabel('UseOnlyDefaultGroupHolderView'), $useOnlydefaultGroupviewSource, $this->UseOnlyDefaultGroupHolderView);
            $fieldGroup                         = FieldGroup::create('FieldGroup', '', $fields);
            $redirectionFieldGroup              = FieldGroup::create('RedirectionFieldGroup', '', $fields);
            $redirectToProductGroupField        = CheckboxField::create('RedirectToProductGroup', $this->fieldLabel('RedirectToProductGroup'));
            $linkToField                        = TreeDropdownField::create('LinkToID', $this->fieldLabel('LinkTo'), SiteTree::class);

            $productGroupsPerPageField->setRightTitle($this->fieldLabel('ProductsPerPageHint'));

            $fieldGroup->pushAndBreak($doNotShowProductsField);
            $fieldGroup->push($defaultGroupViewField);
            $fieldGroup->push($useOnlyDefaultGroupViewField);
            $fieldGroup->breakAndPush($productGroupsPerPageField);
            $fieldGroup->breakAndPush($defaultGroupHolderViewField);
            $fieldGroup->push($useOnlyDefaultGroupHolderViewField);

            $redirectionFieldGroup->push($redirectToProductGroupField);
            if ($this->exists()) {
                $linkToField->setTreeBaseID($this->ID);
                $redirectionFieldGroup->breakAndPush($linkToField);
            }

            $displaySettingsToggle = ToggleCompositeField::create(
                    'DisplaySettingsToggle',
                    $this->fieldLabel('DisplaySettings'),
                    [
                        $fieldGroup,
                    ]
            )->setHeadingLevel(4)->setStartClosed(true);

            $redirectionSettingsToggle = ToggleCompositeField::create(
                    'RedirectionSettingsToggle',
                    $this->fieldLabel('RedirectionSettings'),
                    [
                        $redirectionFieldGroup,
                    ]
            )->setHeadingLevel(4)->setStartClosed(true);

            $fields->insertAfter($redirectionSettingsToggle, 'Content');
            $fields->insertAfter($displaySettingsToggle, 'Content');
        });
        $this->getCMSFieldsIsCalled = true;
        return parent::getCMSFields();
    }

    /**
     * Returns a dynamic meta description.
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $metaDescription = $this->getField('MetaDescription');
        if (!$this->getCMSFieldsIsCalled) {
            if (empty($metaDescription)) {
                $descriptionArray = [$this->Title];
                $children         = $this->Children();
                if ($children->count() > 0) {
                    $map = $children->map();
                    if ($map instanceof Map) {
                        $map = $map->toArray();
                    }
                    $descriptionArray = array_merge($descriptionArray, $map);
                }
                $metaDescription = SeoTools::extractMetaDescriptionOutOfArray($descriptionArray);
            }
            $this->extend('updateMetaDescription', $metaDescription);
        }
        return $metaDescription;
    }

    /**
     * Return the link that we should redirect to.
     * Only return a value if there is a legal redirection destination.
     *
     * @return void
     */
    public function redirectionLink()
    {
        $redirectionLink = false;
        if ($this->RedirectToProductGroup) {
            $linkTo = $this->LinkToID ? ProductGroupPage::get()->byID($this->LinkToID) : null;
            if ($linkTo instanceof ProductGroupPage
             && $linkTo->exists()
             && $linkTo->ID != $this->ID
            ) {
                $redirectionLink = $linkTo->Link();
            }
        }
        return $redirectionLink;
    }

    /**
     * Checks if ProductGroup has children or products.
     *
     * @return bool
     */
    public function hasProductsOrChildren() : bool
    {
        return count($this->Children()) > 0;
    }

    /**
     * Returns the cache key parts for this product group holder
     *
     * @return array
     */
    public function CacheKeyParts() : array
    {
        if (is_null($this->cacheKeyParts)) {
            $lastEditedChildID = 0;
            if ($this->Children()->Count() > 0) {
                $this->Children()->sort('LastEdited', 'DESC');
                $lastEditedChildID = $this->Children()->First()->ID;
            }
            $ctrl = Controller::curr();
            /* @var $ctrl ProductGroupHolderController */
            $cacheKeyParts = [
                i18n::get_locale(),
                $this->LastEdited,
                $ctrl->getSqlOffsetForProductGroups(),
                GroupViewHandler::getActiveGroupHolderView(),
                $lastEditedChildID,
                array_key_exists('start', $_GET) ? $_GET['start'] : 0,
                str_replace('-', '_', Tools::string2urlSegment(Product::defaultSort())),
            ];
            $this->extend('updateCacheKeyParts', $cacheKeyParts);
            $this->cacheKeyParts = $cacheKeyParts;
        }
        return (array) $this->cacheKeyParts;
    }

    /**
     * Returns the cache key for this product group holder
     *
     * @return string
     */
    public function CacheKey() : string
    {
        if (is_null($this->cacheKey)) {
            $cacheKey = implode('_', $this->CacheKeyParts());
            $this->extend('updateCacheKey', $cacheKey);
            $this->cacheKey = $cacheKey;
        }
        return (string) $this->cacheKey;
    }

    /**
     * Returns whether this is a ProductGroupHolder, so true..
     *
     * @return bool
     */
    public function IsProductGroupHolder() : bool
    {
        return true;
    }

    /**
     * Returns the current context controller.
     *
     * @return ProductGroupHolderController
     */
    public function getContextController() : ProductGroupHolderController
    {
        $ctrl = Controller::curr();
        if (!$ctrl instanceof ProductGroupHolder
         || $ctrl->data()->ID !== $this->ID
        ) {
            $ctrl = ProductGroupHolderController::create($this);
        }
        return $ctrl;
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
     */
    public function getProducts($numberOfProducts = false, $sort = false, $disableLimit = false, $force = false)
    {
        $cacheKey = md5("{$numberOfProducts}-{$sort}-{$disableLimit}-{$force}-" . Tools::current_locale());
        if (!array_key_exists($cacheKey, $this->cachedProducts)) {
            $this->cachedProducts[$cacheKey] = $this->getContextController()->getProducts($numberOfProducts, $sort, $disableLimit, $force);
        }
        return $this->cachedProducts[$cacheKey];
    }

    /**
     * Returns a string to display how many products on how many pages are found
     *
     * @return string
     */
    public function getProductsOnPagesString() : string
    {
        $productsOnPagesString = '';
        $products = $this->getPaginationContext();
        if ($products->exists()
         && $products->TotalPages() != 1
        ) {
            $singularOrPlural = 'PRODUCTS_ON_PAGES';
        } elseif ($products->exists()
               && $products->TotalPages() == 1
        ) {
            $singularOrPlural = 'PRODUCTS_ON_PAGE';
        } else {
            $singularOrPlural = 'PRODUCTS_ON_PAGES';
        }
        if ($products->count() > 0) {
            $productsOnPagesString = _t(ProductGroupPage::class . ".{$singularOrPlural}",
                    '{productcount} products on {pagecount} pages',
                    [
                        'productcount' => $products->count(),
                        'pagecount'    => $products->TotalPages(),
                    ]
            );
        }
        return (string) $productsOnPagesString;
    }

    /**
     * Returns the pagination context.
     *
     * @return PaginatedList
     */
    public function getPaginationContext() : PaginatedList
    {
        if (is_null($this->paginationContext)) {
            $this->paginationContext = $this->getProducts();
        }
        return $this->paginationContext;
    }

    /**
     * Sets the pagination context.
     *
     * @param PaginatedList $paginationContext Pagination context
     *
     * @return void
     */
    public function setPaginationContext(PaginatedList $paginationContext) : ProductGroupHolder
    {
        $this->paginationContext = $paginationContext;
        return $this;
    }
}
