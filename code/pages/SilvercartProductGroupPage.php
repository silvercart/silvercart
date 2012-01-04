<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * Displays products with similar attributes
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 20.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartProductGroupPage extends Page {

    /**
     * Set allowed childrens for this page.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $allowed_children = array('SilvercartProductGroupPage');

    /**
     * ???.
     *
     * @var boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $can_be_root = false;
    
    public static $icon = "silvercart/images/page_icons/product_group";

    /**
     * Attributes.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $db = array(
        'productsPerPage'       => 'Int',
        'productGroupsPerPage'  => 'Int',
        'useContentFromParent'  => 'Boolean(0)'
    );

    /**
     * Has-one relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $has_one = array(
        'GroupPicture'                      => 'Image',
        'SilvercartGoogleMerchantTaxonomy'  => 'SilvercartGoogleMerchantTaxonomy'
    );

    /**
     * Has-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $has_many = array(
        'SilvercartProducts' => 'SilvercartProduct'
    );

    /**
     * Belongs-many-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    public static $belongs_many_many = array(
        'SilvercartMirrorProducts'  => 'SilvercartProduct'
    );

    /**
     * Contains all manufacturers of the products contained in this product
     * group page.
     *
     * @var boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.03.2011
     */
    protected $manufacturers = null;
    
    /**
     * Contains the number of all active SilvercartProducts for this page for
     * caching purposes.
     *
     * @var int
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.11.2011
     */
    protected $activeSilvercartProducts = null;

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
     * @copyright 2011 pixeltricks GmbH
     * @since 10.02.2011
     */
    public function  __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);
        $this->drawCMSFields = true;
        $this->GroupPicture()->Title = $this->Title;
    }
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.10.2011
     */
    public function singular_name() {
        if (_t('SilvercartProductGroupPage.SINGULARNAME')) {
            $singular_name = _t('SilvercartProductGroupPage.SINGULARNAME');
        } else {
            $singular_name = parent::singular_name();
        }
        return $singular_name;
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.10.2011
     */
    public function plural_name() {
        if (_t('SilvercartProductGroupPage.PLURALNAME')) {
            $plural_name = _t('SilvercartProductGroupPage.PLURALNAME');
        } else {
            $plural_name = parent::plural_name();
        }
        return $plural_name;
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
    public function LinkingMode() {
        if (Session::get("SilvercartProductGroupPageID") && Controller::curr() instanceof SilvercartProductGroupPage_Controller) {
            if ($this->ID == Session::get("SilvercartProductGroupPageID")) {
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
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 20.04.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'productsPerPage'       => _t('SilvercartProductGroupPage.PRODUCTSPERPAGE'),
                'useContentFromParent'  => _t('SilvercartProductGroupPage.USE_CONTENT_FROM_PARENT'),
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Return all fields of the backend.
     *
     * @return FieldSet Fields of the CMS
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.03.2011
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

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
                "`SilvercartProductGroupID` = %d OR
                 `SilvercartProduct`.`ID` IN (%s)",
                $this->ID,
                $mirroredProductIdList
            );
        } else {
            $filter = sprintf(
                "`SilvercartProductGroupID` = %d",
                $this->ID
            );
        }

        if ($this->drawCMSFields()) {
            $productsTableField = new HasManyComplexTableField(
                $this,
                'SilvercartProducts',
                'SilvercartProduct',
                array(
                    'Title' => _t('SilvercartProduct.COLUMN_TITLE'),
                    'Weight' => _t('SilvercartProduct.WEIGHT', 'weight')
                ),
                'getCMSFields_forPopup',
                $filter
            );
            $tabPARAM = "Root.Content."._t('SilvercartProduct.TITLE', 'product');
            $fields->addFieldToTab($tabPARAM, $productsTableField);

            $tabPARAM3 = "Root.Content." . _t('SilvercartProductGroupPage.GROUP_PICTURE', 'group picture');
            $fields->addFieldToTab($tabPARAM3, new FileIFrameField('GroupPicture', _t('SilvercartProductGroupPage.GROUP_PICTURE', 'group picture')));
        }

        $useContentField = new CheckboxField('useContentFromParent', _t('SilvercartProductGroupPage.USE_CONTENT_FROM_PARENT'));
        $fields->addFieldToTab('Root.Content.Main', $useContentField, 'Content');
        
        $productsPerPageField = new TextField('productsPerPage', _t('SilvercartProductGroupPage.PRODUCTSPERPAGE'));
        $fields->addFieldToTab('Root.Content.Main', $productsPerPageField, 'IdentifierCode');
        $productGroupsPerPageField = new TextField('productGroupsPerPage', _t('SilvercartProductGroupPage.PRODUCTGROUPSPERPAGE'));
        $fields->addFieldToTab('Root.Content.Main', $productGroupsPerPageField, 'IdentifierCode');
        
        // Google taxonomy breadcrumb field
        $cachekey       = SilvercartGoogleMerchantTaxonomy::$cacheKey;
        $cache          = SS_Cache::factory($cachekey);
        $breadcrumbList = $cache->load($cachekey);

        if ($breadcrumbList) {
            $breadcrumbList = unserialize($breadcrumbList);
        } else {
            $breadcrumbList         = array();
            $googleMerchantTaxonomy = DataObject::get(
                'SilvercartGoogleMerchantTaxonomy'
            );
            
            if ($googleMerchantTaxonomy) {
                $breadcrumbList = DataObject::get(
                    'SilvercartGoogleMerchantTaxonomy'
                )->map('ID', 'BreadCrumb');
            }
            
            $cache->save(serialize($breadcrumbList));
        }
        $fields->addFieldToTab('Root.Content.Metadata', new DropdownField(
            'SilvercartGoogleMerchantTaxonomyID',
            _t('SilvercartGoogleMerchantTaxonomy.SINGULAR_NAME'),
            $breadcrumbList
        ));

        $this->extend('extendCMSFields', $fields);
        return $fields;
    }

    /**
     * Returns all SilvercartProductIDs that have this group set as mirror
     * group.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.03.2011
     */
    public function getMirroredProductIDs() {
        $mirroredProductIDs = array();

        $sqlQuery = new SQLQuery();
        $sqlQuery->select = array(
            'SP_SPGMP.SilvercartProductID'
        );
        $sqlQuery->from = array(
            'SilvercartProduct_SilvercartProductGroupMirrorPages SP_SPGMP'
        );
        $sqlQuery->where = array(
            sprintf(
                "SP_SPGMP.SilvercartProductGroupPageID = %d",
                $this->ID
            )
        );
        $result = $sqlQuery->execute();

        foreach ($result as $row) {
            $mirroredProductIDs[] = $row['SilvercartProductID'];
        }

        return $mirroredProductIDs;
    }

    /**
     * Indicates wether the CMS Fields should be drawn.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
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
     * Checks if SilvercartProductGroup has children or products.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.02.2011
     */
    public function hasProductsOrChildren() {
        if ($this->ActiveSilvercartProducts()->Count > 0
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
        if ($this->ActiveSilvercartProducts()->Count == $count) {
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
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.12.2011
     */
    public static function getFlatChildPageIDsForPage($pageId) {
        $pageIDs = array($pageId);
        $pageObj = DataObject::get_by_id('SiteTree', $pageId);
        
        if ($pageObj) {
            foreach ($pageObj->Children() as $pageChild) {
                $pageIDs = array_merge($pageIDs, self::getFlatChildPageIDsForPage($pageChild->ID));
            }
        }
        
        return $pageIDs;
    }
    
    /**
     * Returns the active products for this page.
     *
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 25.02.2011
     */
    public function ActiveSilvercartProducts() {
        if (is_null($this->activeSilvercartProducts)) {
            $activeProducts  = array();
            $productGroupIDs = self::getFlatChildPageIDsForPage($this->ID);
            
            $records = DB::query(
                sprintf(
                    "SELECT
                        ID
                     FROM
                        SilvercartProduct
                     WHERE
                        isActive = 1
                        AND (SilvercartProductGroupID IN (%s)
                             OR (
                                SELECT
                                    COUNT(SilvercartProductID)
                                FROM
                                    SilvercartProduct_SilvercartProductGroupMirrorPages
                                WHERE
                                    SilvercartProductGroupPageID IN (%s)) > 0)",
                    implode(',', $productGroupIDs),
                    implode(',', $productGroupIDs)
                )
            );

            foreach ($records as $record) {
                $activeProducts[] = $record['ID'];
            }
            
            $this->activeSilvercartProducts = $activeProducts;
        }
        
        return new DataObject(
            array(
                'ID'    => 0,
                'Count' => count($this->activeSilvercartProducts)
            )
        );
    }

    /**
     * Returns all Manufacturers of the groups products.
     *
     * @return DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2011
     */
    public function getManufacturers() {
        if (is_null($this->manufacturers)) {
            $registeredManufacturers = array();
            $manufacturers = array();

            foreach ($this->SilvercartProducts() as $product) {
                if ($product->SilvercartManufacturer()) {
                    if (in_array($product->SilvercartManufacturer()->Title, $registeredManufacturers) == false) {
                        $registeredManufacturers[] = $product->SilvercartManufacturer()->Title;
                        $manufacturers[] = $product->SilvercartManufacturer();
                    }
                }
            }
            $this->manufacturers = new DataObjectSet($manufacturers);
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
     * Returns a sorted list of children of this node.
     *
     * @param string $sortField The field used for sorting
     * @param string $sortDir   The sort direction ('ASC' or 'DESC')
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.05.2011
     * 
     * @return DataObjectSet child pages
     */
    public function OrderedChildren($sortField = 'Title', $sortDir = 'ASC') {
        $children = $this->Children();
        $children->sort($sortField, $sortDir);
        
        return $children;
    }
    
    /**
     * All products of this group
     * 
     * @param int|bool $numberOfProducts The number of products to return
     * @param bool     $random           Indicates wether the result set should be randomized
     * 
     * @return DataObjectSet all products of this group or FALSE
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getProducts($numberOfProducts = false, $random = false) {
        $controller = new SilvercartProductGroupPage_Controller($this);
        
        return $controller->getProducts($numberOfProducts, $random);
    }
}

/**
 * Controller Class.
 * This controller handles the actions for product group views and product detail
 * views.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 18.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartProductGroupPage_Controller extends Page_Controller {

    /**
     * Contains a list of all registered filter plugins.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public static $registeredFilterPlugins = array();
    
    /**
     * Contains a DataObjectSet of products for this page or null. Used for
     * caching.
     *
     * @var mixed null|DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    protected $groupProducts = null;

    /**
     * Contains the SilvercartProduct object that is used for the detail view
     * or null. Used for caching.
     *
     * @var mixed null|SilvercartProduct
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    protected $detailViewProduct = null;

    /**
     * Contains filters for the SQL query that retrieves the products for this
     * page.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    protected $listFilters = array();
    
    /**
     * Used for offset calculation of the SQL query that retrieves the
     * products for this page.
     *
     * @var int
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    protected $SQL_start = 0;
    
    /**
     * Contains the output of all WidgetSets of the parent page
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 14.07.2011
     */
    protected $widgetOutput = array();

    /**
     * Makes widgets of parent pages load when subpages don't have any attributed.
     *
     * @var boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 19.10.2011
     */
    public $forceLoadOfWidgets = true;
    
    /**
     * Contains the viewable children of this page for caching purposes.
     *
     * @var mixed null|DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.11.2011
     */
    protected $viewableChildren = null;
    
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
    public function CacheKeySilvercartProductGroupPageControls() {
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
     * @param string $object Name of the filter plugin
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public static function registerFilterPlugin($object) {
        $reflectionClass = new ReflectionClass($object);
        
        if ($reflectionClass->hasMethod('filter')) {
            self::$registeredFilterPlugins[] = $object;
        }
    }
    
    /**
     * execute these statements on object call
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function init() {
        parent::init();
        if (isset($_GET['start'])) {
            $this->SQL_start = (int)$_GET['start'];
        }
        
        // there must be two way to initialize this controller:
        if ($this->isProductDetailView()) {
            $this->registerWidgetAreas();
            // a product detail view is requested
            if (!$this->getDetailViewProduct()->isActive) {
                Director::redirect($this->PageByIdentifierCodeLink());
            }
            $this->registerCustomHtmlForm('SilvercartProductAddCartFormDetail', new SilvercartProductAddCartFormDetail($this, array('productID' => $this->getDetailViewProduct()->ID)));
        } else {
            // a product group view is requested
            $this->registerWidgetAreas();
            $products = $this->getProducts();
            Session::set("SilvercartProductGroupPageID", $this->ID);
            Session::save();
            // Initialise formobjects
            $productIdx = 0;
            if ($products) {
                $productAddCartForm = $this->getCartFormName();
                foreach ($products as $product) {
                    $backlink = $this->Link()."?start=".$this->SQL_start;
                    $productAddCartForm = new $productAddCartForm($this, array('productID' => $product->ID, 'backLink' => $backlink));
                    $this->registerCustomHtmlForm('ProductAddCartForm'.$productIdx, $productAddCartForm);
                    $product->productAddCartForm = $this->InsertCustomHtmlForm(
                        'ProductAddCartForm'.$productIdx,
                        array(
                            $product
                        )
                    );
                    $product->productAddCartFormObj = $productAddCartForm;
                    $productIdx++;
                }
            }
            
            // Register selector forms, e.g. the "products per page" selector
            $selectorForm = new SilvercartProductGroupPageSelectorsForm($this);
            $selectorForm->setSecurityTokenDisabled();
                
            $this->registerCustomHtmlForm(
                'SilvercartProductGroupPageSelectors',
                $selectorForm
            );
        }
    }
    
    /**
     * Registers the WidgetAreas and stores their output.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.08.2011
     */
    protected function registerWidgetAreas() {
        $parentPage = $this->getParent();

        if ($parentPage) {
            $parentPageController = ModelAsController::controller_for($parentPage);
            $parentPageController->init();
            
            if ($this->WidgetSetSidebar()->Count() == 0) {
                $identifier           = 'Sidebar';
                $this->saveWidgetOutput($identifier, $parentPageController->InsertWidgetArea($identifier));
            }
            
            if ($this->WidgetSetContent()->Count() == 0) {
                $identifier           = 'Content';
                $this->saveWidgetOutput($identifier, $parentPageController->InsertWidgetArea($identifier));
            }
        }
    }

    /**
     * Uses the children of SilvercartMyAccountHolder to render a subnavigation
     * with the SilvercartSubNavigation.ss template.
     *
     * @return string
     */
    public function getSubNavigation() {
        $cachekey = 'SilvercartSubNavigation'.$this->ID;
        $cache    = SS_Cache::factory($cachekey);
        $result   = $cache->load($cachekey);

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
                        'SilvercartSubNavigation',
                    )
                );
            } else {
                $output = $extendedOutput[0];
            }
            
            $cache->save(serialize($output));
        }
        
        return $output;
    }

    /**
     * returns the top product group (first product group under SilvercartProductGroupHolder)
     *
     * @param SilvercartProductGroupPage $productGroup product group
     *
     * @return SilvercartProductGroupPage
     */
    public function getTopProductGroup($productGroup = false) {
        if (!$productGroup) {
            $productGroup = $this;
        }
        if ($productGroup->Parent()->ClassName == 'SilvercartProductGroupHolder' ||
            $productGroup->ParentID == 0) {
            return $productGroup;
        }
        return $this->getTopProductGroup($productGroup->Parent());
    }

    /**
     * builds the ProductPages link according to its custom URL rewriting rule
     *
     * @param string $action is ignored
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.02.2011
     */
    public function Link($action = null) {
        if ($this->isProductDetailView()) {
            return parent::Link($action) . $this->urlParams['Action'] . '/' . $this->urlParams['ID'];
        }
        return parent::Link($action);
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
     * @return string
     * 
     * @see self::Link()
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.02.2011
     */
    public function OriginalLink() {
        return parent::Link(null);
    }

    /**
     * manipulates the defaul logic of building the pages breadcrumbs if a
     * product detail view is requested.
     *
     * @param int    $maxDepth       maximum depth level of shown pages in breadcrumbs
     * @param bool   $unlinked       true, if the breadcrumbs should be displayed without links
     * @param string $stopAtPageType name of pagetype to stop at
     * @param bool   $showHidden     true, if hidden pages should be displayed in breadcrumbs
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.02.2011
     */
    public function Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        if ($this->isProductDetailView()) {
            $page    = $this;
            $parts   = array();
            $parts[] = $this->getDetailViewProduct()->Title;
            
            while (
                $page
                && (!$maxDepth ||
                     sizeof($parts) < $maxDepth)
                && (!$stopAtPageType ||
                     $page->ClassName != $stopAtPageType)
            ) {
                if ($showHidden ||
                    $page->ShowInMenus ||
                    ($page->ID == $this->ID)) {
                    
                    if ($page->ID == $this->ID) {
                        $link = $page->OriginalLink();
                    } else {
                        $link = $page->Link();
                    }
                    $parts[] = ("<a href=\"" . $link . "\">" . Convert::raw2xml($page->Title) . "</a>");
                }
                $page = $page->Parent;
            }
            return implode(Page::$breadcrumbs_delimiter, array_reverse($parts));
        }
        return parent::Breadcrumbs($maxDepth, $unlinked, $stopAtPageType, $showHidden);
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
     * All products of this group
     * 
     * @param bool|int    $numberOfProducts The number of products to return
     * @param bool|string $sort             An SQL sort statement
     * 
     * @return DataObjectSet all products of this group or FALSE
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getProducts($numberOfProducts = false, $sort = false) {
        if (!($this->groupProducts)) {
            $this->listFilters = array();
            $filter    = '';
            $SQL_start = $this->getSqlOffset($numberOfProducts);
            
            // ----------------------------------------------------------------
            // Get products that have this group set as mirror group
            // ----------------------------------------------------------------
            $productsPerPage = $this->getProductsPerPageSetting();

            if ($numberOfProducts !== false) {
                $productsPerPage = (int) $numberOfProducts;
            }

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
                $manufacturer = SilvercartManufacturer::getByUrlSegment($this->urlParams['ID']);
                if ($manufacturer) {
                    $this->addListFilter('SilvercartManufacturerID', $manufacturer->ID);
                }
            }

            if (empty($mirroredProductIdList)) {
                $this->listFilters['original'] = sprintf(
                    "`SilvercartProductGroupID` = '%s'",
                    $this->ID
                );
            } else {
                $this->listFilters['original'] = sprintf(
                    "(`SilvercartProductGroupID` = '%s' OR
                      `SilvercartProduct`.`ID` IN (%s))",
                    $this->ID,
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

            if (!$sort) {
                $sort = 'CASE WHEN SPGMSO.SortOrder THEN CONCAT(SPGMSO.SortOrder, SilvercartProduct.SortOrder) ELSE SilvercartProduct.SortOrder END ASC';
            }

            $join = sprintf(
                "LEFT JOIN SilvercartProductGroupMirrorSortOrder SPGMSO ON SPGMSO.SilvercartProductGroupPageID = %d AND SPGMSO.SilvercartProductID = SilvercartProduct.ID",
                $this->ID
            );
            
            $this->groupProducts = SilvercartProduct::get($filter, $sort, $join, sprintf("%d,%d", $SQL_start, $productsPerPage));

            // Inject additional methods into the DataObjectSet
            if ($this->groupProducts) {
                $this->groupProducts->HasMorePagesThan = $this->HasMorePagesThan;
            }
        }
        
        return $this->groupProducts;
    }
    
    /**
     * All products of this group
     * 
     * @param int $numberOfProducts The number of products to return
     * 
     * @return DataObjectSet all products of this group or FALSE
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getRandomProducts($numberOfProducts) {
        $listFilters = array();
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
            $manufacturer = SilvercartManufacturer::getByUrlSegment($this->urlParams['ID']);
            if ($manufacturer) {
                $this->addListFilter('SilvercartManufacturerID', $manufacturer->ID);
            }
        }

        if (empty($mirroredProductIdList)) {
            $listFilters['original'] = sprintf(
                "`SilvercartProductGroupID` = '%s'",
                $this->ID
            );
        } else {
            $listFilters['original'] = sprintf(
                "(`SilvercartProductGroupID` = '%s' OR
                  `SilvercartProduct`.`ID` IN (%s))",
                $this->ID,
                $mirroredProductIdList
            );
        }

        foreach ($listFilters as $listFilterIdentifier => $listFilter) {
            $filter .= ' ' . $listFilter;
        }

        $sort = 'RAND()';
        $join = sprintf(
            "LEFT JOIN SilvercartProductGroupMirrorSortOrder SPGMSO ON SPGMSO.SilvercartProductGroupPageID = %d AND SPGMSO.SilvercartProductID = SilvercartProduct.ID",
            $this->ID
        );

        $products = SilvercartProduct::get($filter, $sort, $join, $numberOfProducts);
        
        return $products;
    }
    
    /**
     * Returns the number of products per page according to where it is set.
     * Highest priority has the customer's configuration setting if available.
     * Next comes the shop owners setting for this page; if that's not
     * configured we use the global setting from SilvercartConfig.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.08.2011
     */
    public function getProductsPerPageSetting() {
        $productsPerPage = 0;
        $member          = Member::currentUser();
        
        if ($member &&
            $member->getSilvercartCustomerConfig() &&
            $member->getSilvercartCustomerConfig()->productsPerPage !== null) {
            
            $productsPerPage = $member->getSilvercartCustomerConfig()->productsPerPage;
            
            if ($productsPerPage == 0) {
                $productsPerPage = SilvercartConfig::getProductsPerPageUnlimitedNumber();
            }
        } else if ($this->productsPerPage) {
            $productsPerPage = $this->productsPerPage;
        } else {
            $productsPerPage = SilvercartConfig::ProductsPerPage();
        }
        
        return $productsPerPage;
    }
    
    /**
     * Return the start value for the limit part of the sql query that
     * retrieves the product list for the current product group page.
     * 
     * @param int|bool $numberOfProducts The number of products to return
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 12.06.2011
     */
    public function getSqlOffset($numberOfProducts = false) {
        $productsPerPage = $this->getProductsPerPageSetting();

        if ($numberOfProducts !== false) {
            $productsPerPage = (int) $numberOfProducts;
        }
        
        if ($productsPerPage === SilvercartConfig::getProductsPerPageUnlimitedNumber()) {
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
        
        return $SQL_start;
    }
    
    /**
     * All viewable product groups of this group.
     *
     * @param int $numberOfProductGroups Number of product groups to display
     * 
     * @return DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.07.2011
     */
    public function getViewableChildren($numberOfProductGroups = false) {
        if ($this->viewableChildren === null) {
            $viewableChildren = array();
            
            foreach ($this->Children() as $child) {
                if ($child->hasProductsOrChildren()) {
                    $viewableChildren[] = $child;
                }
            }
            
            if ($numberOfProductGroups == false) {
                if ($this->productGroupsPerPage) {
                    $pageLength = $this->productGroupsPerPage;
                } else {
                    $pageLength = SilvercartConfig::ProductGroupsPerPage();
                }
            } else {
                $pageLength = $numberOfProductGroups;
            }

            $pageStart = $this->getSqlOffsetForProductGroups($numberOfProductGroups);

            $viewableChildrenSet = new DataObjectSet($viewableChildren);
            $viewableChildrenPage = $viewableChildrenSet->getRange($pageStart, $pageLength);
            $viewableChildrenPage->setPaginationGetVar('groupStart');
            $viewableChildrenPage->setPageLimits($pageStart, $pageLength, $viewableChildrenSet->Count());
        
            $this->viewableChildren = $viewableChildrenPage;
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
        if ($this->getViewableChildren()->TotalItems() > $nrOfViewableChildren) {
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
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.07.2011
     */
    public function getSqlOffsetForProductGroups($numberOfProductGroups = false) {
        if ($this->productGroupsPerPage) {
            $productGroupsPerPage = $this->productGroupsPerPage;
        } else {
            $productGroupsPerPage = SilvercartConfig::ProductsPerPage();
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
     * @copyright 2011 pixeltricks GmbH
     * @since 20.04.2011
     */
    public function HasMorePagesThan($maxResults = 10) {
        $items = $this->getProducts()->Pages()->TotalItems();
        $hasMoreResults = false;

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
            $products->TotalItems() > $maxResults) {
            
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
            $products->TotalItems() < $maxResults) {
            return true;
        }
        
        return false;
    }

    /**
     * Returns $Content of the page. If it's empty and
     * the option is set to use the content of a parent page we try to find
     * the first parent page with content and deliver that.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.08.2011
     */
    public function getPageContent() {
        if (!empty($this->Content) ||
            !$this->useContentFromParent) {
            return $this->Content;
        }
        
        $page       = $this;
        $content    = '';
        
        while ($page->ParentID > 0) {
            if (!empty($page->Content)) {
                $content = $page->Content;
                break;
            }
            
            $page = DataObject::get_by_id('SiteTree', $page->ParentID);
        }
        
        return $content;
    }
    
    /**
     * Getter for an products image.
     *
     * @return Image defined via a has_one relation in SilvercartProduct
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getProductImage() {
        return SilvercartProduct::image();
    }

    /**
     * handles the requested action.
     * If a product detail view is requested, the detail view template will be
     * rendered an displayed.
     *
     * @param SS_HTTPRequest $request request data
     *
     * @return mixed
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.02.2011
     */
    public function handleAction($request) {
        if ($this->isProductDetailView()) {
            
            $this->urlParams['Action'] = (int) $this->urlParams['Action'];

            if (!empty($this->urlParams['OtherID']) &&
                    $this->hasMethod($this->urlParams['OtherID'])) {

                $methodName = $this->urlParams['OtherID'];
                
                if (method_exists($this, $methodName)) {
                    return $this->$methodName($request);
                } else {
                    $this->$methodName($request);
                }
            }

            $view = $this->ProductDetailView(
                $this->urlParams['ID']
            );
            
            if ($view !== false) {
                return $view;
            }
        } elseif ($this->isFilteredByManufacturer()) {
            $url = str_replace($this->urlParams['Action'] . '/' . $this->urlParams['ID'], '', $_REQUEST['url']);
            $this->urlParams['Action'] = '';
            $this->urlParams['ID'] = '';
            $customRequest = new SS_HTTPRequest('GET', $url, array(), array(), null);
            return parent::handleAction($customRequest);
            exit();
        }
        return parent::handleAction($request);
    }

    /**
     * renders a product detail view template (if requested)
     *
     * @param string $urlEncodedProductName the url encoded product name
     *
     * @return string the redered template
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.02.2011
     */
    protected function ProductDetailView($urlEncodedProductName) {
        if ($this->isProductDetailView()) {
            
            Requirements::customScript("
                $(document).ready(function() {
                    $('a.silvercart-product-detail-image').fancybox();
                });
            ");
            
            $product = $this->getDetailViewProduct();
            $product->productAddCartForm = $this->InsertCustomHtmlForm('SilvercartProductAddCartFormDetail');
            $viewParams = array(
                'getProduct' => $product,
                'MetaTitle' => $this->DetailViewProductMetaTitle(),
                'MetaTags' => $this->DetailViewProductMetaTags(false),
            );
            return $this->customise($viewParams)->renderWith(array('SilvercartProductPage','Page'));
        }
        return false;
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
        
        if (empty($this->urlParams['Action'])) {
            return false;
        }
        if ($this->hasMethod($this->urlParams['Action'])) {
            return false;
        }
        if ($this->getDetailViewProduct() instanceof SilvercartProduct) {
            return true;
        }
        return false;
    }

    /**
     * returns the chosen product when requesting a product detail view.
     *
     * @return SilvercartProduct
     */
    public function getDetailViewProduct() {
        if (is_numeric($this->urlParams['Action']) == false) {
            return null;
        }
        if (is_null($this->detailViewProduct)) {
            $this->detailViewProduct = DataObject::get_by_id('SilvercartProduct', Convert::raw2sql($this->urlParams['Action']));
        }
        return $this->detailViewProduct;
    }
    
    /**
     * Returns the SQL filter statement for the current query.
     * 
     * @param string $excludeFilter The name of the filter to exclude
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.08.2011
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
     * This function overloads it and parses the meta data attributes of SilvercartProduct
     *
     * @param boolean $includeTitle should the title tag be parsed?
     *
     * @return string with all meta tags
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    protected function DetailViewProductMetaTags($includeTitle = false) {
        $tags = "";
        if ($includeTitle === true || $includeTitle == 'true') {
            $tags .= "<title>" . Convert::raw2xml(($this->MetaTitle) ? $this->MetaTitle : $this->Title) . "</title>\n";
        }

        $tags .= "<meta name=\"generator\" content=\"SilverStripe - http://silverstripe.org\" />\n";

        $charset = ContentNegotiator::get_encoding();
        $tags .= "<meta http-equiv=\"Content-type\" content=\"text/html; charset=$charset\" />\n";
        if ($this->urlParams['ID'] > 0) {
            $product = $this->getDetailViewProduct();
            if ($product->MetaKeywords) {
                $tags .= "<meta name=\"keywords\" content=\"" . Convert::raw2att($product->MetaKeywords) . "\" />\n";
            }
            if ($product->MetaDescription) {
                $tags .= "<meta name=\"description\" content=\"" . Convert::raw2att($product->MetaDescription) . "\" />\n";
            }
        }
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
        $product = $this->getDetailViewProduct();
        if ($product && $product->MetaTitle) {
            if ($product->SilvercartManufacturer()->ID > 0) {
                return $product->MetaTitle ."/". $product->SilvercartManufacturer()->Title;
            }
            return $product->MetaTitle;
        } else {
            return false;
        }
    }

    /**
     * Checks whether the product list should be filtered by manufacturer.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2011
     */
    protected function isFilteredByManufacturer() {
        if ($this->urlParams['Action'] == _t('SilvercartProductGroupPage.MANUFACTURER_LINK','manufacturer') && !empty ($this->urlParams['ID'])) {
            return true;
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
     * @example $productGroup->addListFilter('SilvercartManufacturerID','5');
     *          Will add the following filter: "AND `SilvercartManufacturerID` = '5'"
     * @example $productGroup->addListFilter('SilvercartManufacturerID','(5,6,7)','IN','OR');
     *          Will add the following filter: "OR `SilvercartManufacturerID` IN (5,6,7)"
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2011
     */
    public function addListFilter($property, $value, $comparison = '=', $operator = 'AND') {
        if ($comparison == 'IN') {
            $this->listFilters[] = $operator . " `" . $property . "` " . $comparison . " (" . $value . ")";
        } else {
            $this->listFilters[] = $operator . " `" . $property . "` " . $comparison . " '" . $value . "'";
        }
    }

}
