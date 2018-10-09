<?php

namespace SilverCart\Model\Pages;

use DateTime;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\SeoTools;
use SilverCart\Dev\Tools;
use SilverCart\Forms\FormFields\FieldGroup;
use SilverCart\Model\Pages\ProductGroupHolder;
use SilverCart\Model\Pages\ProductGroupPageController;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\ProductTranslation;
use SilverCart\View\GroupView\GroupViewHandler;
use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordViewer;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Map;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\ORM\FieldType\DBText;
use SilverStripe\ORM\Queries\SQLSelect;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\ArrayData;
use WidgetSets\Model\WidgetSet;

/**
 * Displays products with similar attributes.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupPage extends \Page
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartProductGroupPage';
    /**
     * Set allowed children for this page.
     *
     * @var array
     */
    private static $allowed_children = [
        ProductGroupPage::class,
    ];
    /**
     * Controls whether a page can be in the root of the site tree.
     * Note: Value might be cached, see {@link $allowed_chilren}.
     *
     * @var boolean
     */
    private static $can_be_root = false;
    /**
     * The icon for this page type in the backend sitetree.
     * 
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/product_group-file.gif";
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'productsPerPage'               => 'Int',
        'productGroupsPerPage'          => 'Int',
        'useContentFromParent'          => 'Boolean(0)',
        'DefaultGroupView'              => 'Varchar(255)',
        'UseOnlyDefaultGroupView'       => 'Enum("no,yes,inherit","inherit")',
        'DefaultGroupHolderView'        => 'Varchar(255)',
        'UseOnlyDefaultGroupHolderView' => 'Enum("no,yes,inherit","inherit")',
        'DoNotShowProducts'             => 'Boolean(0)',
        'LastEditedForCache'            => 'DBDatetime',
    ];
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = [
        'GroupPicture' => Image::class,
    ];
    /**
     * Has-many relationships.
     *
     * @var array
     */
    private static $has_many = [
        'Products' => Product::class,
    ];
    /**
     * Belongs-many-many relationships.
     *
     * @var array
     */
    private static $belongs_many_many = [
        'MirrorProducts' => Product::class,
    ];
    /**
     * Casting
     *
     * @var array
     */
    private static $casting = [
        'BreadcrumbTitle'       => 'Text',
        'ProductsOnPagesString' => 'HTMLText',
    ];
    /**
     * Delimiter character(s) to use as product group breadcrumb title separation.
     *
     * @var string
     */
    private static $breadcrumb_title_delimiter = ' > ';
    /**
     * Determines whether to show new products on a product group page or not.
     *
     * @var boolean
     */
    private static $show_new_products = true;
    /**
     * Determines whether to show pre-orderable products on a product group page or not.
     *
     * @var boolean
     */
    private static $show_preorderable_products = true;
    /**
     * Saves the result from $this->getProducts()
     *
     * @var array
     */
    protected $cachedProducts = [];
    /**
     * Contains all manufacturers of the products contained in this product
     * group page.
     *
     * @var boolean
     */
    protected $manufacturers = null;
    /**
     * Contains the number of all active Products for this page for
     * caching purposes.
     *
     * @var array
     */
    protected static $activeProducts = [];
    /**
     * Indicator to check whether getCMSFields is called
     *
     * @var boolean
     */
    protected $getCMSFieldsIsCalled = false;
    /**
     * Optional pagination context. If not set, the return value of getProducts() will be used.
     *
     * @var PaginatedList
     */
    protected $paginationContext = null;
    /**
     * Cache key parts for this product group
     * 
     * @var array 
     */
    protected $cacheKeyParts = null;
    /**
     * Cache key for this product group
     * 
     * @var string
     */
    protected $cacheKey = null;

    /**
     * Constructor. Extension to overwrite the groupimage's "alt"-tag with the
     * name of the productgroup.
     *
     * @param array $record      Array of field values. Normally this contructor is only used by the internal systems that get objects from the database.
     * @param bool  $isSingleton This this to true if this is a singleton() object, a stub for calling methods. Singletons don't have their defaults set.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.02.2011
     */
    public function  __construct($record = null, $isSingleton = false)
    {
        parent::__construct($record, $isSingleton);
        $this->drawCMSFields = true;

        if ($this->GroupPictureID > 0) {
            $this->GroupPicture()->Title = $this->Title;
        }
    }
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name()
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name()
    {
        return Tools::plural_name_for($this);
    }
    
    /**
     * Overwrites the function LinkingMode in SiteTree
     * Other than the default behavior current should be returned for the
     * product category defined via session. This is neccessary for products
     * that are mirrored into a category.
     * If the product category is not set in the session the method behaves like
     * the overwritten one.
     * 
     * @return string current, section or link; to be used in the template
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 29.6.2011
     */
    public function LinkingMode()
    {
        if (Tools::Session()->get("SilverCart.ProductGroupPageID")
         && Controller::curr() instanceof ProductGroupPageController
        ) {
            if ($this->ID == Tools::Session()->get("SilverCart.ProductGroupPageID")) {
                return 'current';
            }
        } elseif ($this->isCurrent()) {
            return "current";
        } elseif ($this->isSection()) {
            return 'section';
        } else {
            return 'link';
        }
    }

    /**
     * builds the ProductPages link according to its custom URL rewriting rule
     *
     * @param string $action Action to call. Will be ignored for product detail views.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.03.2015
     */
    public function Link($action = null)
    {
        $controller = Controller::curr();
        if ($controller->hasMethod('isProductDetailView')
         && $controller->isProductDetailView()
         && $controller->data()->ID === $this->ID
        ) {
            $product = $controller->getDetailViewProduct();
            $link    = $product->Link($this->Locale);
        } else {
            $link = parent::Link($action);
        }
        return $link;
    }
    
    /**
     * Returns the relative canonical link.
     * Adds the HTTP GET parameter 'start' if needed.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.06.2017
     */
    public function CanonicalLink()
    {
        $link = parent::CanonicalLink();
        if (!(Controller::curr()->hasMethod('isProductDetailView')
           && Controller::curr()->isProductDetailView())
        ) {
            if (array_key_exists('start', $_GET)) {
                $char = '?';
                if (strpos($link, $char) !== false) {
                    $char = '&';
                }
                if (strpos($link, '?start=') === false
                 && strpos($link, '&start=') === false
                 && (int) $_GET['start'] > 0
                ) {
                    $link .= $char . 'start=' . $_GET['start'];
                }
            }
        } elseif (Controller::curr()->isProductDetailView()) {
            $link = Controller::curr()->getDetailViewProduct()->CanonicalLink();
        }
        return $link;
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
    public function OriginalLink($action = null)
    {
        return parent::Link($action);
    }
    
    /**
     * Returns the back link
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.05.2014
     */
    public function BackLink()
    {
        if (Controller::curr()->getRequest()->requestVar('_REDIRECT_BACK_URL')) {
            $url = Controller::curr()->getRequest()->requestVar('_REDIRECT_BACK_URL');
        } elseif (Controller::curr()->getRequest()->getHeader('Referer')) {
            $url = Controller::curr()->getRequest()->getHeader('Referer');
        } else {
            $url = $this->OriginalLink();
        }
        if (!$this->isInternalUrl($url)
         || Director::makeRelative($url) == Director::makeRelative($this->Link())
        ) {
            $url = $this->OriginalLink();
        }
        return $url;
    }
    
    /**
     * Returns the back page
     *
     * @return SiteTree
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.05.2012
     */
    public function BackPage()
    {
        $url            = $this->BackLink();
        $relativeUrl    = Director::makeRelative($url);
        if (strpos($relativeUrl, '?') !== false) {
            $blankUrl   = substr($relativeUrl, 0, strpos($relativeUrl, '?'));
        } elseif (strpos($relativeUrl, '#') !== false) {
            $blankUrl   = substr($relativeUrl, 0, strpos($relativeUrl, '#'));
        } else {
            $blankUrl   = $relativeUrl;
        }
        $backPage = SiteTree::get_by_link($blankUrl);
        
        // If no backPage has been found we could come from a product detail
        // page. Try to get the product title then.
        if (!$backPage) {
            $urlElems = explode('/', $blankUrl);
            array_pop($urlElems);
            $productId = array_pop($urlElems);
            if (is_numeric($productId)) {
                $product = Product::get()->byID(Convert::raw2xml($productId));

                if ($product) {
                    $backPage = new DataObject();
                    $backPage->MenuTitle = $product->Title;
                }
            } else {
                $backPage = new DataObject();
                $backPage->MenuTitle = Page::singleton()->fieldLabel('PreviousPage');
            }
        }
        
        return $backPage;
    }
    
    /**
     * Returns whether the given url is an internal url
     * 
     * @param string $url URL to check
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.05.2012
     */
    public function isInternalUrl($url) {
        $isInternalUrl  = false;
        if (Director::is_absolute_url($url) &&
            strpos($url, $_SERVER['SERVER_NAME'])) {
            $isInternalUrl  = true;
        }
        return $isInternalUrl;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'productsPerPage'               => _t(ProductGroupPage::class . '.PRODUCTSPERPAGE', 'Products per page'),
                'ProductsPerPageHint'           => _t(ProductGroupPage::class . '.PRODUCTSPERPAGEHINT', 'Set products or product groups per page to 0 (zero) to use the default setting.'),
                'productGroupsPerPage'          => _t(ProductGroupPage::class . '.PRODUCTGROUPSPERPAGE', 'Product groups per page'),
                'useContentFromParent'          => _t(ProductGroupPage::class . '.USE_CONTENT_FROM_PARENT', 'Use content from parent pages'),
                'DefaultGroupView'              => _t(ProductGroupPage::class . '.DEFAULTGROUPVIEW', 'Default product list view'),
                'DefaultGroupViewInherit'       => _t(ProductGroupPage::class . '.DEFAULTGROUPVIEW_DEFAULT', 'Use view from parent pages'),
                'UseOnlyDefaultGroupView'       => _t(ProductGroupPage::class . '.USEONLYDEFAULTGROUPVIEW', 'Allow only default view'),
                'DefaultGroupHolderView'        => _t(ProductGroupPage::class . '.DEFAULTGROUPHOLDERVIEW', 'Default product group view'),
                'UseOnlyDefaultGroupHolderView' => _t(ProductGroupPage::class . '.USEONLYDEFAULTGROUPHOLDERVIEW', 'Allow only default view'),
                'DoNotShowProducts'             => _t(ProductGroupPage::class . '.DONOTSHOWPRODUCTS', 'do <strong>not</strong> show products of this group'),
                'DisplaySettings'               => _t(ProductGroupPage::class . '.DisplaySettings', 'Display Settings'),
                'NewProducts'                   => _t(ProductGroupPage::class . '.NewProducts', 'New Products'),
                'NewProductsLinkTitle'          => _t(ProductGroupPage::class . '.NewProductsLinkTitle', 'Show all new products'),
                'PreorderableProducts'          => _t(ProductGroupPage::class . '.PreorderableProducts', 'Pre-Orders'),
                'PreorderableProductsLinkTitle' => _t(ProductGroupPage::class . '.PreorderableProductsLinkTitle', 'Show all pre-orders'),
                'GroupPicture'                  => _t(ProductGroupPage::class . '.GROUP_PICTURE', 'Group picture'),
                'ManageProductsButton'          => _t(ProductGroupPage::class . '.MANAGE_PRODUCTS_BUTTON', 'Manage products'),
                'Yes'                           => Tools::field_label('Yes'),
                'No'                            => Tools::field_label('No'),
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Return all fields of the backend.
     *
     * @return FieldList Fields of the CMS
     */
    public function getCMSFields() {
        $this->getCMSFieldsIsCalled = true;
        $fields = parent::getCMSFields();
        if ($this->isArchived()) {
            return $fields;
        }
        
        $useOnlydefaultGroupviewSource  = array(
            'inherit'   => $this->fieldLabel('DefaultGroupViewInherit'),
            'yes'       => $this->fieldLabel('Yes'),
            'no'        => $this->fieldLabel('No'),
        );
        
        $useContentField                    = new CheckboxField('useContentFromParent',     $this->fieldLabel('useContentFromParent'));
        $doNotShowProductsField             = new CheckboxField('DoNotShowProducts',        Tools::string2html($this->fieldLabel('DoNotShowProducts')));
        $productsPerPageField               = new TextField('productsPerPage',              $this->fieldLabel('productsPerPage'));
        $defaultGroupViewField              = GroupViewHandler::getGroupViewDropdownField('DefaultGroupView', $this->fieldLabel('DefaultGroupView'), $this->DefaultGroupView, $this->fieldLabel('DefaultGroupViewInherit'));
        $useOnlyDefaultGroupViewField       = new DropdownField('UseOnlyDefaultGroupView',  $this->fieldLabel('UseOnlyDefaultGroupView'), $useOnlydefaultGroupviewSource, $this->UseOnlyDefaultGroupView);
        $productGroupsPerPageField          = new TextField('productGroupsPerPage',         $this->fieldLabel('productGroupsPerPage'));
        $defaultGroupHolderViewField        = GroupViewHandler::getGroupViewDropdownField('DefaultGroupHolderView', $this->fieldLabel('DefaultGroupHolderView'), $this->DefaultGroupHolderView, $this->fieldLabel('DefaultGroupViewInherit'));
        $useOnlyDefaultGroupHolderViewField = new DropdownField('UseOnlyDefaultGroupHolderView',  $this->fieldLabel('UseOnlyDefaultGroupHolderView'), $useOnlydefaultGroupviewSource, $this->UseOnlyDefaultGroupHolderView);
        $fieldGroup                         = new FieldGroup('FieldGroup', '', $fields);
        
        $productsPerPageField->setRightTitle($this->fieldLabel('ProductsPerPageHint'));
        $productGroupsPerPageField->setRightTitle($this->fieldLabel('ProductsPerPageHint'));
        
        $fieldGroup->push(          $useContentField);
        $fieldGroup->breakAndPush(  $doNotShowProductsField);
        $fieldGroup->breakAndPush(  $productsPerPageField);
        $fieldGroup->breakAndPush(  $defaultGroupViewField);
        $fieldGroup->push(          $useOnlyDefaultGroupViewField);
        $fieldGroup->breakAndPush(  $productGroupsPerPageField);
        $fieldGroup->breakAndPush(  $defaultGroupHolderViewField);
        $fieldGroup->push(          $useOnlyDefaultGroupHolderViewField);
        
        $displaySettingsToggle = ToggleCompositeField::create(
                'DisplaySettingsToggle',
                $this->fieldLabel('DisplaySettings'),
                array(
                    $fieldGroup,
                )
        )->setHeadingLevel(4)->setStartClosed(true);
        $fields->insertAfter($displaySettingsToggle, 'Content');

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

            $filter = sprintf(
                "ProductGroupID = %d OR
                 \"%s\".\"ID\" IN (%s)",
                $this->ID,
                Tools::get_table_name(Product::class),
                $mirroredProductIdList
            );
        } else {
            $filter = sprintf(
                "ProductGroupID = %d",
                $this->ID
            );
        }

        if ($this->drawCMSFields()) {
            
            $config = GridFieldConfig_RecordViewer::create(100);
            $productsTableField = new GridField(
                'Products',
                $this->fieldLabel('Products'),
                Product::get()->filter(array('ProductGroupID' => $this->ID)),
                $config
            );
            $tabPARAM = "Root." . _t(Product::class . '.TITLE', 'product');
            $fields->addFieldToTab($tabPARAM, $productsTableField);

            $productAdminLink     = Director::baseURL().'admin/silvercart-products';
            $manageProductsButton = new LiteralField(
                'ManageProductsButton',
                sprintf(
                    "<a href=\"%s\">%s</a>",
                    $productAdminLink,
                    $this->fieldLabel('ManageProductsButton')
                )
            );
            $fields->addFieldToTab($tabPARAM, $manageProductsButton);

            $imageUploadField = new UploadField('GroupPicture', $this->fieldLabel('GroupPicture'));
            $imageUploadField->setFolderName('assets/productgroup-images');
            $fields->addFieldToTab('Root.Main', $imageUploadField, 'Content');
        }
        
        $widgetSetContent = $fields->fieldByName('Root.Widgets.WidgetSetContent');
        if ($widgetSetContent) {
            $widgetSetAdminLink  = Director::baseURL().'admin/silvercart-widget-sets';
            $manageWidgetsButton = new LiteralField(
                'ManageWidgetsButton',
                sprintf(
                    "<a href=\"%s\">%s</a>",
                    $widgetSetAdminLink,
                    WidgetSet::singleton()->fieldLabel('ManageWidgetSets')
                )
            );
            $fields->insertAfter($manageWidgetsButton, 'WidgetSetContent');
        }
        
        if ($fields->dataFieldByName('LastEditedForCache') instanceof FormField) {
            $fields->removeByName('LastEditedForCache');
        }

        $this->extend('extendCMSFields', $fields);
        return $fields;
    }

    /**
     * Returns all Product IDs that have this group set as mirror
     * group.
     *
     * @return array
     */
    public function getMirroredProductIDs() {
        $mirroredProductIDs         = array();
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

        $productTable = Tools::get_table_name(Product::class);
        $sqlQuery = new SQLSelect();
        $sqlQuery->setSelect('P_PGMP.' . $productTable . 'ID');
        $sqlQuery->addFrom($productTable . '_ProductGroupMirrorPages P_PGMP');
        $sqlQuery->addWhere(array(
                sprintf(
                    "P_PGMP.%sID IN (%s)",
                    Tools::get_table_name(self::class),
                    $translationProductGroupIDList
                )
            )
        );
        $result = $sqlQuery->execute();

        foreach ($result as $row) {
            $mirroredProductIDs[] = $row[$productTable . 'ID'];
        }
        
        return $mirroredProductIDs;
    }

    /**
     * Indicates wether the CMS Fields should be drawn.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.03.2011
     */
    public function drawCMSFields() {
        $drawCMSFields   = true;
        $updateCMSFields = $this->extend('updateDrawCMSFields', $drawCMSFields);

        if (!empty($updateCMSFields)) {
            $drawCMSFields = $updateCMSFields[0];
        }

        return $drawCMSFields;
    }

    /**
     * Checks if ProductGroup has children or products.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.02.2011
     */
    public function hasProductsOrChildren() {
        if ($this->ActiveProducts()->Count > 0
         || count($this->Children()) > 0) {

            return true;
        }
        return false;
    }

    /**
     * Returns true, when the products count is equal $count
     *
     * @param int $count expected count of products
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public function hasProductCount($count) {
        if ($this->ActiveProducts()->Count == $count) {
            return true;
        }
        return false;
    }

    /**
     * Returns a flat array containing the ID of all child pages of the given page.
     *
     * @param int $pageId The root page ID
     *
     * @return array
     */
    public static function getFlatChildPageIDsForPage($pageId) {
        return Tools::getFlatChildPageIDsForPage($pageId);
    }
    
    /**
     * Returns the active products for this page.
     *
     * @return DataObject
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.07.2015
     */
    public function ActiveProducts() {
        if (!array_key_exists($this->ID, self::$activeProducts)) {
            $requiredAttributes = Product::getRequiredAttributes();
            $activeProducts     = array();
            $productGroupIDs    = self::getFlatChildPageIDsForPage($this->ID);
            $translations       = Tools::get_translations($this);
            
            if ($translations &&
                $translations->count() > 0) {
                foreach ($translations as $translation) {
                    $productGroupIDs = array_merge(
                            $productGroupIDs,
                            self::getFlatChildPageIDsForPage($translation->ID)
                    );
                }
            }
            
            $filter = array(
                '',
            );

            if (!empty($requiredAttributes)) {
                foreach ($requiredAttributes as $requiredAttribute) {
                    //find out if we are dealing with a real attribute or a multilingual field
                    if (array_key_exists($requiredAttribute, Product::config()->get('db')) || $requiredAttribute == "Price") {
                        if ($requiredAttribute == "Price") {
                            // Gross price as default if not defined
                            if (Config::Pricetype() == "net") {
                                $filter[] = sprintf('("PriceNetAmount" != 0.0)');
                            } else {
                                $filter[] = sprintf('("PriceGrossAmount" != 0.0)');
                            }
                        } else {
                            $filter[] = sprintf('"%s" != \'\'', $requiredAttribute);
                        }
                    } else {
                        // if its a multilingual attribute it comes from a relational class
                        $filter[] = sprintf("%s.%s != ''", Tools::get_table_name(ProductTranslation::class), $requiredAttribute);
                    }

                }
            }
            if (count($filter) == 1) {
                $filter = array();
            }
            $productTable = Tools::get_table_name(Product::class);
            $productGroupTable = Tools::get_table_name(ProductGroupPage::class);
            $filterString = sprintf(
                    "isActive = 1
                     AND (ProductGroupID IN (%s)
                         OR ID IN (
                            SELECT
                                %sID
                            FROM
                                %s_ProductGroupMirrorPages
                            WHERE
                                %sID IN (%s)))
                     %s",
                    implode(',', $productGroupIDs),
                    $productTable,
                    $productTable,
                    $productGroupTable,
                    implode(',', $productGroupIDs),
                    implode(' AND ', $filter)
            );
            $this->extend('updateActiveProductsFilter', $filterString);
            
            $records = DB::query(
                sprintf(
                    "SELECT
                        ID
                     FROM
                        %s
                     WHERE
                        %s",
                    Tools::get_table_name(Product::class),
                    $filterString
                )
            );
            
            foreach ($records as $record) {
                $activeProducts[] = $record['ID'];
            }
            
            self::$activeProducts[$this->ID] = $activeProducts;
        }
        
        $result = new DataObject();
        $result->ID = count(self::$activeProducts[$this->ID]);
        $result->Count = count(self::$activeProducts[$this->ID]);
        $result->IDs = self::$activeProducts[$this->ID];
        
        return $result;
    }

    /**
     * Returns all Manufacturers of the groups products.
     *
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2011
     */
    public function getManufacturers() {
        if (is_null($this->manufacturers)) {
            $registeredManufacturers = array();
            $manufacturers = array();

            foreach ($this->getProducts() as $product) {
                if ($product->Manufacturer()) {
                    if (in_array($product->Manufacturer()->Title, $registeredManufacturers) == false) {
                        $registeredManufacturers[] = $product->Manufacturer()->Title;
                        $manufacturers[] = $product->Manufacturer();
                    }
                }
            }
            $this->manufacturers = new ArrayList($manufacturers);
        }
        return $this->manufacturers;
    }

    /**
     * Returns whether the actual view is filtered by this manufacturer or not.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.03.2011
     */
    public function isActive() {
        return Controller::curr()->Link() == $this->Link();
    }
    
    /**
     * Returns whether the current view is the first page of the product list or not
     *
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.09.2018
     */
    public function isFirstPage()
    {
        return $this->getContextController()->isFirstPage();
    }
    
    /**
     * Returns a sorted list of children of this node.
     *
     * @param string $sortField The field used for sorting
     * @param string $sortDir   The sort direction ('ASC' or 'DESC')
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.05.2011
     * 
     * @return ArrayList child pages
     */
    public function OrderedChildren($sortField = 'Title', $sortDir = 'ASC') {
        $children = $this->Children();
        $children->sort($sortField, $sortDir);
        
        return $children;
    }
    
    /**
     * Returns the current context controller.
     * 
     * @return ProductGroupPageController
     */
    public function getContextController()
    {
        $ctrl = Controller::curr();
        if (!$ctrl instanceof ProductGroupPageController
         || $ctrl->data()->ID !== $this->ID) {
            $ctrl = ProductGroupPageController::create($this);
        }
        return $ctrl;
    }
    
    /**
     * All products of this group forced (independent of DoNotShowProducts setting)
     * 
     * @param int    $numberOfProducts The number of products to return
     * @param string $sort             An SQL sort statement
     * @param bool   $disableLimit     Disables the product limitation
     * 
     * @return DataList|false all products of this group
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
     * @return DataList|false all products of this group
     */
    public function getProducts($numberOfProducts = false, $sort = false, $disableLimit = false, $force = false)
    {
        $cacheKey = md5($numberOfProducts.'-'.$sort.'-'.$disableLimit.'-'.$force);

        if (!array_key_exists($cacheKey, $this->cachedProducts)) {
            $this->cachedProducts[$cacheKey] = $this->getContextController()->getProducts($numberOfProducts, $sort, $disableLimit, $force);
        }
        
        return $this->cachedProducts[$cacheKey];
    }

    /**
     * Returns the products of all children (recursively) of the current product group page.
     *
     * @return PaginatedList
     */
    public function getProductsFromChildren()
    {
        $ctrl     = $this->getContextController();
        $products = $this->getProductsFromChildrenList();
        $elements = PaginatedList::create($products);
        $ctrl->addTotalNumberOfProducts($products->count());
        $ctrl->setPaginationContext($elements);
        
        if (method_exists($ctrl, 'getProductsPerPageSetting')) {
            $elements->pageLength = $ctrl->getProductsPerPageSetting();
            $elements->pageStart  = $ctrl->getSqlOffset();
        }
        return $elements;
    }

    /**
     * Returns the products of all children (recursively) of the current product group page.
     *
     * @return \SilverStripe\ORM\SS_List
     */
    public function getProductsFromChildrenList()
    {
        $filter   = $this->getProductsFromChildrenFilter();
        $products = ArrayList::create();
        if (!empty($filter)) {
            $products = Product::getProductsList("({$filter})");
        }
        return $products;
    }

    /**
     * Returns the products of all children (recursively) of the current product group page.
     *
     * @return string
     */
    public function getProductsFromChildrenFilter()
    {
        $filter          = '';
        $pageIDsToWorkOn = $this->getContextController()->getDescendantIDList();
        if (is_array($pageIDsToWorkOn)
         && count($pageIDsToWorkOn) > 0
        ) {
            $productGroupTable = Tools::get_table_name(self::class);
            $productTable = Tools::get_table_name(Product::class);
            if (Config::DefaultLanguage() != i18n::get_locale()) {
                $translationGroupQuery = 'SELECT "STTG"."TranslationGroupID" FROM "SiteTree_translationgroups" AS "STTG" WHERE "STTG"."OriginalID" IN (' . implode(',', $pageIDsToWorkOn) . ')';
                $translationIDsQuery   = 'SELECT "STTG2"."OriginalID" FROM "SiteTree_translationgroups" AS "STTG2" WHERE "STTG2"."TranslationGroupID" IN (' . $translationGroupQuery . ')';
                $mirrored              = 'SELECT "PGMP"."' . $productTable . 'ID" FROM ' . $productTable . '_ProductGroupMirrorPages AS "PGMP" WHERE "PGMP"."' . $productGroupTable . 'ID" IN (' . implode(',', $pageIDsToWorkOn) . ') OR "PGMP"."ProductGroupPageID" IN (' . $translationIDsQuery . ')';
            } else {
                $mirrored = 'SELECT "PGMP"."' . $productTable . 'ID" FROM ' . $productTable . '_ProductGroupMirrorPages AS "PGMP" WHERE "PGMP"."' . $productGroupTable . 'ID" IN (' . implode(',', $pageIDsToWorkOn) . ')';
            }
            $filter = '"' . $productTable . '"."ProductGroupID" IN (' . implode(',', $pageIDsToWorkOn) . ') OR "' . $productTable . '"."ID" IN (' . $mirrored . ')';
        }
        return $filter;
    }
    
    /**
     * Returns the related products or the products of the child product groups.
     * 
     * @return PaginatedList
     */
    public function getProductsToDisplay()
    {
        $productGroupPage = $this->getContextController();

        if (!$productGroupPage instanceof ProductGroupPageController
         || $productGroupPage->getProducts()->count() > 0
        ) {
            $products = $productGroupPage->getProducts();
        } else {
            $products = $productGroupPage->getProductsFromChildren();
        }
        return $products;
    }
    
    /**
     * Returns all product of this group and all children.
     * 
     * @return DataList
     */
    public function getProductsInherited()
    {
        $filter         = '"' . Product::config()->get('table_name') . '"."ProductGroupID" = ' . $this->ID;
        $childrenFilter = $this->getProductsFromChildrenFilter();
        if (!empty($childrenFilter)) {
            $filter = "{$filter} OR {$childrenFilter}";
        }
        $products = Product::getProductsList("({$filter})");
        return $products;
    }
    
    /**
     * Returns all new products of this group and all children.
     * 
     * @param int $limit Optional limit
     * 
     * @return DataList
     */
    public function getNewProducts($limit = null)
    {
        $timeDifference = "-" . Product::getIsNewProductDefaultTimeDifference();
        $date           = new DateTime(date('Y-m-d H:i:s'));
        $date->format('Y/m/d');
        $date->modify($timeDifference);
        $modifiedDate   = $date->getTimestamp();
        $minCreated     = date('Y-m-d H:i:s', $modifiedDate);
        $products       = $this->getProductsInherited()
                ->where('"' . Product::config()->get('table_name') . '"."Created" > \'' . $minCreated . '\'')
                ->sort('"' . Product::config()->get('table_name') . '"."Created"', 'DESC');
        if (!is_null($limit)) {
            $products = $products->limit($limit);
        }
        return $products;
    }
    
    /**
     * Returns the new products in an ArrayData format to use in a template.
     * 
     * @param int $limit Optional limit
     * 
     * @return ArrayData
     */
    public function getNewProductsForTemplate($limit = null)
    {
        return ArrayData::create([
            "Title"                => _t(self::class . ".NewProductsIn", "New Products in {productgroup}", ['productgroup' => $this->Title]),
            "Elements"             => $this->getNewProducts($limit),
            "ID"                   => "{$this->ID}-newproducts",
            "NewProductsLink"      => $this->Link('newproducts'),
            "NewProductsLinkTitle" => $this->fieldLabel('NewProductsLinkTitle'),
        ]);
    }
    
    /**
     * Returns whether to show new products or not.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.09.2018
     */
    public function ShowNewProducts()
    {
        $showNewProducts = $this->config()->get('show_new_products');
        if ($showNewProducts
         && !$this->getNewProducts()->exists()) {
            $showNewProducts = false;
        }
        return $showNewProducts;
    }
    
    /**
     * Returns all pre-orderable products of this group and all children.
     * 
     * @param int $limit Optional limit
     * 
     * @return DataList
     */
    public function getPreorderableProducts($limit = null)
    {
        $products = $this->getProductsInherited()
                ->where('"' . Product::config()->get('table_name') . '"."ReleaseDate" > NOW()')
                ->sort('"' . Product::config()->get('table_name') . '"."ReleaseDate"', 'ASC');
        if (!is_null($limit)) {
            $products = $products->limit($limit);
        }
        return $products;
    }
    
    /**
     * Returns the pre-orderable products in an ArrayData format to use in a template.
     * 
     * @param int $limit Optional limit
     * 
     * @return ArrayData
     */
    public function getPreorderableProductsForTemplate($limit = null)
    {
        return ArrayData::create([
            "Title"                         => _t(self::class . ".PreorderableProductsIn", "Pre-Orders in {productgroup}", ['productgroup' => $this->Title]),
            "Elements"                      => $this->getPreorderableProducts($limit),
            "ID"                            => "{$this->ID}-preorders",
            "PreorderableProductsLink"      => $this->Link('preorders'),
            "PreorderableProductsLinkTitle" => $this->fieldLabel('PreorderableProductsLinkTitle'),
        ]);
    }
    
    /**
     * Returns whether to show new products or not.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.10.2018
     */
    public function ShowPreorderableProducts()
    {
        $showPreorderableProducts = $this->config()->get('show_preorderable_products');
        if ($showPreorderableProducts
         && !$this->getPreorderableProducts()->exists()) {
            $showPreorderableProducts = false;
        }
        return $showPreorderableProducts;
    }
    
    /**
     * Returns the meta description. If not set, it will be generated by it's
     * related products or the single product in detail view
     * 
     * @return string
     */
    public function getMetaDescription()
    {
        $metaDescription = $this->getField('MetaDescription');
        if (!$this->getCMSFieldsIsCalled
         && !Tools::isBackendEnvironment()
        ) {
            if (empty($metaDescription)) {
                $ctrl = Controller::curr();
                if ($ctrl instanceof ProductGroupPageController
                 && $ctrl->isProductDetailView()
                ) {
                    $product = $ctrl->getDetailViewProduct();
                    $metaDescription = $product->MetaDescription;
                } elseif ($ctrl instanceof ProductGroupPageController) {
                    $descriptionArray = [$this->Title];
                    $children         = $this->Children();
                    if ($children->count() > 0) {
                        $map = $children->map();
                        if ($map instanceof Map) {
                            $map = $map->toArray();
                        }
                        $descriptionArray = array_merge($descriptionArray, $map);
                    }
                    $products = $this->getProductsToDisplay();
                    if ($products->count() > 0) {
                        $currOffset       = $ctrl->CurrentOffset();
                        $sqlOffset        = $ctrl->getProductsPerPageSetting();
                        $products         = array_slice($products->map()->toArray(), $currOffset, $sqlOffset);
                        $descriptionArray = array_merge($descriptionArray, $products);
                    }
                    $metaDescription = SeoTools::extractMetaDescriptionOutOfArray($descriptionArray);
                }
            }
            $this->extend('updateMetaDescription', $metaDescription);
        }
        return $metaDescription;
    }
    
    /**
     * Returns the meta title. If not set, the meta-title of the 
     * single product in detail view or the title of the SiteTree object 
     * will be returned
     * 
     * @return string
     */
    public function getMetaTitle()
    {
        $metaTitle = $this->getField('MetaTitle');
        if (!$this->getCMSFieldsIsCalled
         && !Tools::isBackendEnvironment()
        ) {
            if (empty($metaTitle)) {
                $ctrl = Controller::curr();
                if ($ctrl instanceof ProductGroupPageController
                 && $ctrl->isProductDetailView()
                ) {
                    $product = $ctrl->getDetailViewProduct();
                    $metaTitle = _t(self::class . '.BuyTitle', 'Buy {title}', [
                        'title' => $product->MetaTitle,
                    ]);
                } else {
                    $pageXofY  = "";
                    $buyTitle  = _t(self::class . '.BuyTitle', 'Buy {title}', [
                        'title' => $this->BreadcrumbTitle,
                    ]);
                    if ($ctrl->HasMorePagesThan(1)) {
                        $pageXofY = _t(Page::class . '.PageXofY', 'Page {x} of {y}', [
                            'x' => $ctrl->getProducts()->CurrentPage(),
                            'y' => $ctrl->getProducts()->Pages()->count(),
                        ]);
                        $pageXofY = "({$pageXofY}) ";
                    }
                    $metaTitle = "{$this->Title} {$pageXofY}| {$buyTitle}";
                }
            }
            $this->extend('updateMetaTitle', $metaTitle);
        }
        return $metaTitle;
    }
    
    /**
     * Returns the productsPerPage setting.
     * 
     * @return string
     */
    public function getproductsPerPage()
    {
        $productsPerPage = $this->getField('productsPerPage');
        if (!$this->getCMSFieldsIsCalled
         && !Tools::isBackendEnvironment()
        ) {
            $this->extend('updateProductsPerPage', $productsPerPage);
        }
        return $productsPerPage;
    }
    
    /**
     * Returns the productGroupsPerPage setting.
     * 
     * @return string
     */
    public function getproductGroupsPerPage()
    {
        $productGroupsPerPage = $this->getField('productGroupsPerPage');
        if (!$this->getCMSFieldsIsCalled
         && !Tools::isBackendEnvironment()
        ) {
            $this->extend('updateProductGroupsPerPage', $productGroupsPerPage);
        }
        return $productGroupsPerPage;
    }
    
    /**
     * Returns the useContentFromParent setting.
     * 
     * @return string
     */
    public function getuseContentFromParent()
    {
        $useContentFromParent = $this->getField('useContentFromParent');
        if (!$this->getCMSFieldsIsCalled
         && !Tools::isBackendEnvironment()
        ) {
            $this->extend('updateUseContentFromParent', $useContentFromParent);
        }
        return $useContentFromParent;
    }
    
    /**
     * Returns the DoNotShowProducts setting.
     * 
     * @return string
     */
    public function getDoNotShowProducts()
    {
        $doNotShowProducts = $this->getField('DoNotShowProducts');
        if (!$this->getCMSFieldsIsCalled
         && !Tools::isBackendEnvironment()
        ) {
            $this->extend('updateDoNotShowProducts', $doNotShowProducts);
        }
        return $doNotShowProducts;
    }
    
    /**
     * Adds a ParentID if not available.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.10.2016
     */
    protected function onBeforeWrite()
    {
        if (!isset($this->ParentID)) {
            $productGroupHolder = ProductGroupHolder::get()->first();
            $this->ParentID     = $productGroupHolder->ID;
        }
        parent::onBeforeWrite();
    }
    
    /**
     * Set LastEdited field to now for the ProductGroupHolder.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.07.2012
     */
    public function onBeforeDelete()
    {
        $productGroupHolderPage = $this->getProductGroupHolderPage();

        if ($productGroupHolderPage) {
            $now = new DateTime();
            $productGroupHolderPage->LastEdited = $now->format('Y-m-d H:i:s');
            $productGroupHolderPage->write();
        }

        parent::onBeforeDelete();
    }

    /**
     * Returns the first ProductGroupHolder page.
     *
     * @param SiteTree $context An optional SiteTree object
     *
     * @return ProductGroupHolder
     */
    public function getProductGroupHolderPage($context = null)
    {
        if (is_null($context)) {
            $context = $this;
        }

        if ( $context->ParentID > 0
         && !$context->Parent() instanceof ProductGroupHolder
        ) {
            $context = $this->getProductGroupHolderPage($context->Parent());
        }

        return $context;
    }
    
    /**
     * Returns the title including the parent product group titles.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.09.2018
     */
    public function getBreadcrumbTitle()
    {
        $group = $this->owner;
        $ctrl  = Controller::curr();
        /* @var $group \SilverCart\Model\Pages\ProductGroupPage */
        /* @var $ctrl \SilverCart\Model\Pages\ProductGroupPageController */
        $items = $group->getBreadcrumbItems(20, ProductGroupHolder::class);
        $map   = $items->map('ID', 'Title')->toArray();
        if ($ctrl->hasMethod('isProductDetailView')
         && $ctrl->isProductDetailView()) {
            array_pop($map);
        }
        return implode($group->config()->get('breadcrumb_title_delimiter'), $map);
    }
    
    /**
     * Returns a string to display how many products on how many pages are found
     * 
     * @return string
     */
    public function getProductsOnPagesString()
    {
        $productsOnPagesString = '';
        $products = $this->getPaginationContext();
        if ($products->exists() &&
            $products->TotalPages() != 1) {
            $singularOrPlural = 'PRODUCTS_ON_PAGES';
        } elseif ($products->exists() &&
                  $products->TotalPages() == 1) {
            $singularOrPlural = 'PRODUCTS_ON_PAGE';
        } else {
            $singularOrPlural = 'PRODUCTS_ON_PAGES';
        }
        if ($products->count() > 0) {
            $productsOnPagesString = _t(ProductGroupPage::class . '.' . $singularOrPlural,
                    '{productcount} products on {pagecount} pages',
                    [
                        'productcount' => $products->count(),
                        'pagecount' => $products->TotalPages(),
                    ]
            );
        }
        return $productsOnPagesString;
    }
    
    /**
     * Returns the pagination context.
     * 
     * @return PaginatedList
     */
    public function getPaginationContext()
    {
        if (is_null($this->paginationContext)) {
            $this->paginationContext = $this->getProducts();
        }
        return $this->paginationContext;
    }
    
    /**
     * Sets the pagination context.
     * 
     * @param type $paginationContext
     * 
     * @return void
     */
    public function setPaginationContext($paginationContext)
    {
        $this->paginationContext = $paginationContext;
    }
    
    /**
     * Updates the LastEditedForCache property for Stage and Live version.
     * 
     * @param string $newDate New date in Y-m-d H:i:s format
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.10.2017
     */
    public function updateLastEditedForCache($newDate = null)
    {
        if (is_null($newDate)) {
            $newDate = date('Y-m-d H:i:s');
        }
        $latestPublished = $this->latestPublished();
        $this->LastEditedForCache = $newDate;
        $this->write();
        if ($latestPublished) {
            $this->copyVersionToStage(Versioned::DRAFT, Versioned::LIVE);
        } else {
            $liveVersion = Versioned::get_one_by_stage(self::class, Versioned::LIVE, '"' . self::$table_name . '"."ID" = ' . $this->ID);
            if ($liveVersion instanceof ProductGroupPage &&
                $liveVersion->exists()) {
                $liveVersion->LastEditedForCache = $this->LastEditedForCache;
                $liveVersion->writeToStage(Versioned::LIVE);
            }
        }
    }

    /**
     * Returns the pages original breadcrumbs
     *
     * @param int    $maxDepth       maximum depth level of shown pages in breadcrumbs
     * @param bool   $unlinked       true, if the breadcrumbs should be displayed without links
     * @param string $stopAtPageType name of pagetype to stop at
     * @param bool   $showHidden     true, if hidden pages should be displayed in breadcrumbs
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.07.2012
     */
    public function OriginalBreadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false)
    {
        return parent::Breadcrumbs($maxDepth, $unlinked, $stopAtPageType, $showHidden);
    }

    /**
     * Adds the product title to the bradcrumbs if the current page is a product detail page.
     *
     * @param int    $maxDepth       maximum depth level of shown pages in breadcrumbs
     * @param string $stopAtPageType name of pagetype to stop at
     * @param bool   $showHidden     true, if hidden pages should be displayed in breadcrumbs
     * 
     * @return ArrayList
     */
    public function getBreadcrumbItems($maxDepth = 20, $stopAtPageType = false, $showHidden = false)
    {
        $this->beforeUpdateBreadcrumbItems(function($items) {
            $ctrl  = Controller::curr();
            if ($ctrl->hasMethod('isProductDetailView')
             && $ctrl->isProductDetailView()
            ) {
                $title = DBText::create();
                $title->setValue($ctrl->getDetailViewProduct()->Title);
                $items->push(ArrayData::create([
                    'MenuTitle' => $title,
                    'Title'     => $title,
                    'Link'      => $ctrl->getDetailViewProduct()->Link(),
                ]));
            } elseif ($ctrl->getAction() === 'newproducts') {
                $title = DBText::create();
                $title->setValue($this->fieldLabel('NewProducts'));
                $items->push(ArrayData::create([
                    'MenuTitle' => $title,
                    'Title'     => $title,
                    'Link'      => $ctrl->Link('newproducts'),
                ]));
            } elseif ($ctrl->getAction() === 'preorders') {
                $title = DBText::create();
                $title->setValue($this->fieldLabel('PreorderableProducts'));
                $items->push(ArrayData::create([
                    'MenuTitle' => $title,
                    'Title'     => $title,
                    'Link'      => $ctrl->Link('preorders'),
                ]));
            }
        });
        $items = parent::getBreadcrumbItems($maxDepth, $stopAtPageType, $showHidden);
        $this->extend('updateBreadcrumbParts', $items);
        return $items;
    }

    /**
     * Returns the cache key parts for this product group
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2018
     */
    public function CacheKeyParts()
    {
        if (is_null($this->cacheKeyParts)) {
            $ctrl = Controller::curr();
            /* @var $ctrl ProductGroupPageController */
            $cacheKeyParts = [
                $this->ID,
                $this->LastEdited,
                $this->LastEditedForCache,
                $this->MemberGroupCacheKey(),
                $ctrl->getSqlOffset(),
                $ctrl->getProductsPerPageSetting(),
                GroupViewHandler::getActiveGroupView(),
                str_replace('-', '_', Tools::string2urlSegment(Product::defaultSort())),
            ];
            $this->extend('updateCacheKeyParts', $cacheKeyParts);
            $this->cacheKeyParts = $cacheKeyParts;
        }
        return $this->cacheKeyParts;
    }
    
    /**
     * Returns the cache key for this product group
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2018
     */
    public function CacheKey()
    {
        if (is_null($this->cacheKey)) {
            $cacheKey = implode('_', $this->CacheKeyParts());
            $this->extend('updateCacheKey', $cacheKey);
            $this->cacheKey = $cacheKey;
        }
        return $this->cacheKey;
    }
    
    /**
     * Returns custom content to render before the content widget area, injected
     * by extensions.
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.09.2018
     */
    public function BeforeInsertWidgetAreaContent()
    {
        $content = '';
        $this->extend('updateBeforeInsertWidgetAreaContent', $content);
        return Tools::string2html($content);
    }
    
    /**
     * Returns custom navigation items to render after the default navigation in
     * the product group sidebar.
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.10.2018
     */
    public function ExtendedDynamicProductGroupNavigationItems()
    {
        $items = ArrayList::create();
        $this->extend('updateDynamicProductGroupNavigationItems', $items);
        return $items;
    }
    
    /**
     * Returns whether this is a ProductGroupPage, so true..
     * 
     * @return boolean
     */
    public function IsProductGroupPage()
    {
        return true;
    }
}