<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartProductGroupPage extends Page {

    /**
     * Set allowed childrens for this page.
     *
     * @var array
     */
    private static $allowed_children = array('SilvercartProductGroupPage');

    /**
     * ???.
     *
     * @var boolean
     */
    private static $can_be_root = false;
    
    /**
     * The icon for this page type in the backend sitetree.
     * 
     * @var string
     */
    private static $icon = "silvercart/img/page_icons/product_group";

    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'productsPerPage'               => 'Int',
        'productGroupsPerPage'          => 'Int',
        'useContentFromParent'          => 'Boolean(0)',
        'DefaultGroupView'              => 'VarChar(255)',
        'UseOnlyDefaultGroupView'       => 'Enum("no,yes,inherit","inherit")',
        'DefaultGroupHolderView'        => 'VarChar(255)',
        'UseOnlyDefaultGroupHolderView' => 'Enum("no,yes,inherit","inherit")',
        'DoNotShowProducts'             => 'Boolean(0)',
        'LastEditedForCache'            => 'SS_DateTime',
    );

    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = array(
        'GroupPicture'                      => 'Image',
    );

    /**
     * Has-many relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'SilvercartProducts' => 'SilvercartProduct'
    );

    /**
     * Belongs-many-many relationships.
     *
     * @var array
     */
    private static $belongs_many_many = array(
        'SilvercartMirrorProducts'      => 'SilvercartProduct',
    );

    /**
     * Casting
     *
     * @var array
     */
    private static $casting = array(
        'ProductsOnPagesString' => 'HTMLText',
    );

    /**
     * Saves the result from $this->getProducts()
     *
     * @var array
     */
    protected $cachedProducts = array();

    /**
     * Contains all manufacturers of the products contained in this product
     * group page.
     *
     * @var boolean
     */
    protected $manufacturers = null;
    
    /**
     * Contains the number of all active SilvercartProducts for this page for
     * caching purposes.
     *
     * @var array
     */
    protected static $activeSilvercartProducts = array();
    
    /**
     * Indicator to check whether getCMSFields is called
     *
     * @var boolean
     */
    protected $getCMSFieldsIsCalled = false;
    
    /**
     * List of already requested links
     *
     * @var array
     */
    protected $links = array();
    
    /**
     * Optional pagination context. If not set, the return value of getProducts() will be used.
     *
     * @var PaginatedList
     */
    protected $paginationContext = null;

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
    public function  __construct($record = null, $isSingleton = false) {
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
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.05.2012
     */
    public function singular_name() {
        SilvercartTools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.05.2012
     */
    public function plural_name() {
        SilvercartTools::plural_name_for($this);
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
     * builds the ProductPages link according to its custom URL rewriting rule
     *
     * @param string $action Action to call. Will be ignored for product detail views.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.03.2015
     */
    public function Link($action = null) {
        $linkKey = (string) $action;
        $linkKey .= '-' . $this->Locale;

        if (!array_key_exists($linkKey, $this->links)) {
            $returnProductLink = false;

            if (Controller::curr()->hasMethod('isProductDetailView') &&
                Controller::curr()->isProductDetailView() &&
                Controller::curr()->data()->ID == $this->ID &&
                Controller::curr()->data() === $this) {
                $returnProductLink     = true;
                $product               = Controller::curr()->getDetailViewProduct();
                $this->links[$linkKey] = $product->Link();
            } elseif (Controller::curr()->hasMethod('isProductDetailView') &&
                      Controller::curr()->isProductDetailView()) {
                $translations = $this->getTranslations();
                if ($translations) {
                    $translation = $translations->find('ID', Controller::curr()->data()->ID);
                    if ($translation) {
                        $product = Controller::curr()->getDetailViewProduct();
                        if ($product instanceof SilvercartProduct) {
                            $returnProductLink     = true;
                            $productLanguage       = $product->getLanguageFor($this->Locale);
                            if ($productLanguage instanceof SilvercartProductLanguage) {
                                $this->links[$linkKey] = $product->buildLink($this, SilvercartTools::string2urlSegment($productLanguage->Title));
                            } else {
                                $this->links[$linkKey] = '';
                            }
                        }
                    }
                }
            }

            if (!$returnProductLink) {
                $this->links[$linkKey] = parent::Link($action);
            }
        }
        return $this->links[$linkKey];
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
    public function CanonicalLink() {
        $link = parent::CanonicalLink();
        if (!(Controller::curr()->hasMethod('isProductDetailView') &&
              Controller::curr()->isProductDetailView())) {
            if (array_key_exists('start', $_GET)) {
                $char = '?';
                if (strpos($link, $char) !== false) {
                    $char = '&';
                }
                if (strpos($link, '?start=') === false &&
                    strpos($link, '&start=') === false &&
                    (int) $_GET['start'] > 0) {
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
     * Returns the back link
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.05.2014
     */
    public function BackLink() {
        if (Controller::curr()->getRequest()->requestVar('_REDIRECT_BACK_URL')) {
            $url = Controller::curr()->getRequest()->requestVar('_REDIRECT_BACK_URL');
        } elseif (Controller::curr()->getRequest()->getHeader('Referer')) {
            $url = Controller::curr()->getRequest()->getHeader('Referer');
        } else {
            $url = $this->OriginalLink();
        }
        if (!$this->isInternalUrl($url) ||
            Director::makeRelative($url) == Director::makeRelative($this->Link())) {
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
    public function BackPage() {
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
                $silvercartProduct = DataObject::get_by_id(
                    'SilvercartProduct',
                    Convert::raw2xml($productId)
                );

                if ($silvercartProduct) {
                    $backPage = new DataObject();
                    $backPage->MenuTitle = $silvercartProduct->Title;
                }
            } else {
                $backPage = new DataObject();
                $backPage->MenuTitle = _t('SilvercartPage.BACK_TO_DEFAULT');
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
                'productsPerPage'               => _t('SilvercartProductGroupPage.PRODUCTSPERPAGE'),
                'productGroupsPerPage'          => _t('SilvercartProductGroupPage.PRODUCTGROUPSPERPAGE'),
                'useContentFromParent'          => _t('SilvercartProductGroupPage.USE_CONTENT_FROM_PARENT'),
                'DefaultGroupView'              => _t('SilvercartProductGroupPage.DEFAULTGROUPVIEW'),
                'UseOnlyDefaultGroupView'       => _t('SilvercartProductGroupPage.USEONLYDEFAULTGROUPVIEW'),
                'DefaultGroupHolderView'        => _t('SilvercartProductGroupPage.DEFAULTGROUPHOLDERVIEW'),
                'UseOnlyDefaultGroupHolderView' => _t('SilvercartProductGroupPage.USEONLYDEFAULTGROUPHOLDERVIEW'),
                'DoNotShowProducts'             => _t('SilvercartProductGroupPage.DONOTSHOWPRODUCTS'),
                'DisplaySettings'               => _t('SilvercartProductGroupPage.DisplaySettings'),
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
        
        $useOnlydefaultGroupviewSource  = array(
            'inherit'   => _t('SilvercartProductGroupPage.DEFAULTGROUPVIEW_DEFAULT'),
            'yes'       => _t('Silvercart.YES'),
            'no'        => _t('Silvercart.NO'),
        );
        
        $useContentField                    = new CheckboxField('useContentFromParent',     $this->fieldLabel('useContentFromParent'));
        $doNotShowProductsField             = new CheckboxField('DoNotShowProducts',        $this->fieldLabel('DoNotShowProducts'));
        $productsPerPageField               = new TextField('productsPerPage',              $this->fieldLabel('productsPerPage'));
        $defaultGroupViewField              = SilvercartGroupViewHandler::getGroupViewDropdownField('DefaultGroupView', $this->fieldLabel('DefaultGroupView'), $this->DefaultGroupView, _t('SilvercartProductGroupPage.DEFAULTGROUPVIEW_DEFAULT'));
        $useOnlyDefaultGroupViewField       = new DropdownField('UseOnlyDefaultGroupView',  $this->fieldLabel('UseOnlyDefaultGroupView'), $useOnlydefaultGroupviewSource, $this->UseOnlyDefaultGroupView);
        $productGroupsPerPageField          = new TextField('productGroupsPerPage',         $this->fieldLabel('productGroupsPerPage'));
        $defaultGroupHolderViewField        = SilvercartGroupViewHandler::getGroupViewDropdownField('DefaultGroupHolderView', $this->fieldLabel('DefaultGroupHolderView'), $this->DefaultGroupHolderView, _t('SilvercartProductGroupPage.DEFAULTGROUPVIEW_DEFAULT'));
        $useOnlyDefaultGroupHolderViewField = new DropdownField('UseOnlyDefaultGroupHolderView',  $this->fieldLabel('UseOnlyDefaultGroupHolderView'), $useOnlydefaultGroupviewSource, $this->UseOnlyDefaultGroupHolderView);
        $fieldGroup                         = new SilvercartFieldGroup('FieldGroup', '', $fields);
        
        $productsPerPageField->setRightTitle(_t('SilvercartProductGroupPage.PRODUCTSPERPAGEHINT'));
        $productGroupsPerPageField->setRightTitle(_t('SilvercartProductGroupPage.PRODUCTSPERPAGEHINT'));
        
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
                "SilvercartProductGroupID = %d OR
                 \"SilvercartProduct\".\"ID\" IN (%s)",
                $this->ID,
                $mirroredProductIdList
            );
        } else {
            $filter = sprintf(
                "SilvercartProductGroupID = %d",
                $this->ID
            );
        }

        if ($this->drawCMSFields()) {
            
            $config = GridFieldConfig_RecordViewer::create(100);
            $productsTableField = new GridField(
                'SilvercartProducts',
                $this->fieldLabel('SilvercartProducts'),
                SilvercartProduct::get()->filter(array('SilvercartProductGroupID' => $this->ID)),
                $config
            );
            $tabPARAM = "Root."._t('SilvercartProduct.TITLE', 'product');
            $fields->addFieldToTab($tabPARAM, $productsTableField);

            $productAdminLink     = Director::baseURL().'admin/silvercart-products';
            $manageProductsButton = new LiteralField(
                'ManageProductsButton',
                sprintf(
                    "<a href=\"%s\">%s</a>",
                    $productAdminLink,
                    _t('SilvercartProductGroupPage.MANAGE_PRODUCTS_BUTTON')
                )
            );
            $fields->addFieldToTab($tabPARAM, $manageProductsButton);

            $imageUploadField = new UploadField('GroupPicture', _t('SilvercartProductGroupPage.GROUP_PICTURE'));
            $imageUploadField->setFolderName('Uploads/productgroup-images');
            $fields->addFieldToTab('Root.Main', $imageUploadField, 'Content');
        }
        
        $widgetSetContent = $fields->fieldByName('Root.Widgets.WidgetSetContent');
        if ($widgetSetContent) {
            $widgetSetAdminLink     = Director::baseURL().'admin/silvercart-widget-sets';
            $manageWidgetsButton = new LiteralField(
                'ManageWidgetsButton',
                sprintf(
                    "<a href=\"%s\">%s</a>",
                    $widgetSetAdminLink,
                    _t('SilvercartWidgetSet.MANAGE_WIDGETS_BUTTON')
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
     * Checks whether the given group view is allowed to render for this group
     *
     * @param string $groupView GroupView code
     * 
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.06.2012
     */
    public function isGroupViewAllowed($groupView) {
        $groupViewAllowed = true;
        if ($this->getUseOnlyDefaultGroupViewInherited() &&
            $groupView != $this->getDefaultGroupViewInherited()) {
            $groupViewAllowed = false;
        }
        return $groupViewAllowed;
    }

    /**
     * Returns the inherited DefaultGroupView
     *
     * @param SilvercartProductGroupPage $context Context
     * 
     * @return string
     */
    public function getDefaultGroupViewInherited($context = null) {
        if (is_null($context)) {
            $context = $this;
        }
        $defaultGroupView = $context->DefaultGroupView;
        if (empty($defaultGroupView) ||
            SilvercartGroupViewHandler::getGroupView($defaultGroupView) === false) {
            if ($context->Parent() instanceof SilvercartProductGroupPage) {
                $defaultGroupView = $this->getDefaultGroupViewInherited($context->Parent());
            } else if ($context->Parent() instanceof SilvercartProductGroupHolder) {
                $defaultGroupView = $this->getDefaultGroupViewInherited($context->Parent());
            } else {
                $defaultGroupView = SilvercartGroupViewHandler::getDefaultGroupView();
            }
        }
        return $defaultGroupView;
    }
    
    /**
     * Returns the inherited UseOnlyDefaultGroupView
     *
     * @param SilvercartProductGroupPage $context Context
     * 
     * @return string
     */
    public function getUseOnlyDefaultGroupViewInherited($context = null) {
        if (is_null($context)) {
            $context = $this;
        }
        $useOnlyDefaultGroupView = $context->UseOnlyDefaultGroupView;
        if ($useOnlyDefaultGroupView == 'inherit') {
            if ($context->Parent() instanceof SilvercartProductGroupPage) {
                $useOnlyDefaultGroupView = $this->getUseOnlyDefaultGroupViewInherited($context->Parent());
            } else if ($context->Parent() instanceof SilvercartProductGroupHolder) {
                $useOnlyDefaultGroupView = $this->getUseOnlyDefaultGroupViewInherited($context->Parent());
            } else {
                $useOnlyDefaultGroupView = false;
            }
        } elseif ($useOnlyDefaultGroupView == 'yes') {
            $useOnlyDefaultGroupView = true;
        } else {
            $useOnlyDefaultGroupView = false;
        }
        return $useOnlyDefaultGroupView;
    }
    
    /**
     * Checks whether the given group view is allowed to render for this group
     *
     * @param string $groupHolderView GroupHolderView code
     * 
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.06.2012
     */
    public function isGroupHolderViewAllowed($groupHolderView) {
        $groupHolderViewAllowed = true;
        if ($this->getUseOnlyDefaultGroupHolderViewInherited() &&
            $groupHolderView != $this->getDefaultGroupHolderViewInherited()) {
            $groupHolderViewAllowed = false;
        }
        return $groupHolderViewAllowed;
    }

    /**
     * Returns the inherited DefaultGroupHolderView
     *
     * @param SilvercartProductGroupPage $context Context
     * 
     * @return string
     */
    public function getDefaultGroupHolderViewInherited($context = null) {
        if (is_null($context)) {
            $context = $this;
        }
        $defaultGroupHolderView = $context->DefaultGroupHolderView;
        if (empty($defaultGroupHolderView) ||
            SilvercartGroupViewHandler::getGroupHolderView($defaultGroupHolderView) === false) {
            if ($context->Parent() instanceof SilvercartProductGroupPage ||
                $context->Parent() instanceof SilvercartProductGroupHolder) {
                $defaultGroupHolderView = $this->getDefaultGroupHolderViewInherited($context->Parent());
            } else {
                $defaultGroupHolderView = SilvercartGroupViewHandler::getDefaultGroupHolderView();
            }
        }
        return $defaultGroupHolderView;
    }
    
    /**
     * Returns the inherited UseOnlyDefaultGroupHolderView
     *
     * @param SilvercartProductGroupPage $context Context
     * 
     * @return string
     */
    public function getUseOnlyDefaultGroupHolderViewInherited($context = null) {
        if (is_null($context)) {
            $context = $this;
        }
        $useOnlyDefaultGroupHolderView = $context->UseOnlyDefaultGroupHolderView;
        if ($useOnlyDefaultGroupHolderView == 'inherit') {
            if ($context->Parent() instanceof SilvercartProductGroupPage ||
                $context->Parent() instanceof SilvercartProductGroupHolder) {
                $useOnlyDefaultGroupHolderView = $this->getUseOnlyDefaultGroupHolderViewInherited($context->Parent());
            } else {
                $useOnlyDefaultGroupHolderView = false;
            }
        } elseif ($useOnlyDefaultGroupHolderView == 'yes') {
            $useOnlyDefaultGroupHolderView = true;
        } else {
            $useOnlyDefaultGroupHolderView = false;
        }
        return $useOnlyDefaultGroupHolderView;
    }

    /**
     * Returns all SilvercartProductIDs that have this group set as mirror
     * group.
     *
     * @return array
     */
    public function getMirroredProductIDs() {
        $mirroredProductIDs         = array();
        $translations               = $this->getTranslations();
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

        $sqlQuery = new SQLQuery();
        $sqlQuery->setSelect('SP_SPGMP.SilvercartProductID');
        $sqlQuery->addFrom('SilvercartProduct_SilvercartProductGroupMirrorPages SP_SPGMP');
        $sqlQuery->addWhere(array(
                sprintf(
                    "SP_SPGMP.SilvercartProductGroupPageID IN (%s)",
                    $translationProductGroupIDList
                )
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.08.2012
     */
    public static function getFlatChildPageIDsForPage($pageId) {
        return SilvercartTools::getFlatChildPageIDsForPage($pageId);
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
    public function ActiveSilvercartProducts() {
        if (!array_key_exists($this->ID, self::$activeSilvercartProducts)) {
            $requiredAttributes = SilvercartProduct::getRequiredAttributes();
            $activeProducts     = array();
            $productGroupIDs    = self::getFlatChildPageIDsForPage($this->ID);
            $translations       = $this->getTranslations();
            
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
                    if (array_key_exists($requiredAttribute, DataObject::custom_database_fields('SilvercartProduct')) || $requiredAttribute == "Price") {
                        if ($requiredAttribute == "Price") {
                            // Gross price as default if not defined
                            if (SilvercartConfig::Pricetype() == "net") {
                                $filter[] = sprintf('("PriceNetAmount" != 0.0)');
                            } else {
                                $filter[] = sprintf('("PriceGrossAmount" != 0.0)');
                            }
                        } else {
                            $filter[] = sprintf('"%s" != \'\'', $requiredAttribute);
                        }
                    } else {
                        // if its a multilingual attribute it comes from a relational class
                        $filter[] = sprintf("SilvercartProductLanguage.%s != ''", $requiredAttribute);
                    }

                }
            }
            if (count($filter) == 1) {
                $filter = array();
            }
            $filterString = sprintf(
                    "isActive = 1
                     AND (SilvercartProductGroupID IN (%s)
                         OR ID IN (
                            SELECT
                                SilvercartProductID
                            FROM
                                SilvercartProduct_SilvercartProductGroupMirrorPages
                            WHERE
                                SilvercartProductGroupPageID IN (%s)))
                     %s",
                    implode(',', $productGroupIDs),
                    implode(',', $productGroupIDs),
                    implode(' AND ', $filter)
            );
            $this->extend('updateActiveSilvercartProductsFilter', $filterString);
            
            $records = DB::query(
                sprintf(
                    "SELECT
                        ID
                     FROM
                        SilvercartProduct
                     WHERE
                        %s",
                    $filterString
                )
            );
            
            foreach ($records as $record) {
                $activeProducts[] = $record['ID'];
            }
            
            self::$activeSilvercartProducts[$this->ID] = $activeProducts;
        }
        
        $result = new DataObject();
        $result->ID = count(self::$activeSilvercartProducts[$this->ID]);
        $result->Count = count(self::$activeSilvercartProducts[$this->ID]);
        $result->IDs = self::$activeSilvercartProducts[$this->ID];
        
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
                if ($product->SilvercartManufacturer()) {
                    if (in_array($product->SilvercartManufacturer()->Title, $registeredManufacturers) == false) {
                        $registeredManufacturers[] = $product->SilvercartManufacturer()->Title;
                        $manufacturers[] = $product->SilvercartManufacturer();
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
     * All products of this group forced (independant of DoNotShowProducts setting)
     * 
     * @param int    $numberOfProducts The number of products to return
     * @param string $sort             An SQL sort statement
     * @param bool   $disableLimit     Disables the product limitation
     * 
     * @return DataList|false all products of this group
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.06.2012
     */
    public function getProducts($numberOfProducts = false, $sort = false, $disableLimit = false, $force = false) {
        $cacheKey = md5($numberOfProducts.'-'.$sort.'-'.$disableLimit.'-'.$force);

        if (!array_key_exists($cacheKey, $this->cachedProducts)) {
            if (Controller::curr() instanceof SilvercartProductGroupPage_Controller &&
                Controller::curr()->data()->ID === $this->ID) {

                $controller = Controller::curr();
            } else {
                $controller = new SilvercartProductGroupPage_Controller($this);
            }

            $this->cachedProducts[$cacheKey] = $controller->getProducts($numberOfProducts, $sort, $disableLimit, $force);
        }
        
        return $this->cachedProducts[$cacheKey];
    }

    /**
     * Returns the products of all children (recursively) of the current product group page.
     *
     * @return PaginatedList
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.06.2017
     */
    public function getProductsFromChildren() {
        if (Controller::curr() instanceof SilvercartProductGroupPage_Controller &&
            Controller::curr()->data()->ID === $this->ID) {

            $productGroupPage = Controller::curr();
        } else {
            $productGroupPage = new SilvercartProductGroupPage_Controller($this);
        }

        if (!$productGroupPage instanceof SilvercartProductGroupPage_Controller ||
             $productGroupPage->getProducts()->count() > 0) {

            return new PaginatedList(new ArrayList());
        }

        $products        = new ArrayList();
        $pageIDsToWorkOn = $productGroupPage->getDescendantIDList();
        if (is_array($pageIDsToWorkOn) &&
            count($pageIDsToWorkOn) > 0) {
            if (SilvercartConfig::DefaultLanguage() != i18n::get_locale()) {
                $translationGroupQuery = 'SELECT "STTG"."TranslationGroupID" FROM "SiteTree_translationgroups" AS "STTG" WHERE "STTG"."OriginalID" IN (' . implode(',', $pageIDsToWorkOn) . ')';
                $translationIDsQuery   = 'SELECT "STTG2"."OriginalID" FROM "SiteTree_translationgroups" AS "STTG2" WHERE "STTG2"."TranslationGroupID" IN (' . $translationGroupQuery . ')';
                $mirrored              = 'SELECT "SPGMP"."SilvercartProductID" FROM SilvercartProduct_SilvercartProductGroupMirrorPages AS "SPGMP" WHERE "SPGMP"."SilvercartProductGroupPageID" IN (' . implode(',', $pageIDsToWorkOn) . ') OR "SPGMP"."SilvercartProductGroupPageID" IN (' . $translationIDsQuery . ')';
            } else {
                $mirrored = 'SELECT "SPGMP"."SilvercartProductID" FROM SilvercartProduct_SilvercartProductGroupMirrorPages AS "SPGMP" WHERE "SPGMP"."SilvercartProductGroupPageID" IN (' . implode(',', $pageIDsToWorkOn) . ')';
            }
            $products = SilvercartProduct::getProducts('("SilvercartProduct"."SilvercartProductGroupID" IN (' . implode(',', $pageIDsToWorkOn) . ') OR "SilvercartProduct"."ID" IN (' . $mirrored . '))');
        }

        $elements = new PaginatedList($products);
        $productGroupPage->addTotalNumberOfProducts($products->count());
        $productGroupPage->setPaginationContext($elements);
        
        if (method_exists($productGroupPage, 'getProductsPerPageSetting')) {
            $elements->pageLength = $productGroupPage->getProductsPerPageSetting();
            $elements->pageStart  = $productGroupPage->getSqlOffset();
        }
        return $elements;
    }
    
    /**
     * Returns the related products or the products of the child product groups.
     * 
     * @return PaginatedList
     */
    public function getProductsToDisplay() {
        if (Controller::curr() instanceof SilvercartProductGroupPage_Controller &&
            Controller::curr()->data()->ID === $this->ID) {

            $productGroupPage = Controller::curr();
        } else {
            $productGroupPage = new SilvercartProductGroupPage_Controller($this);
        }

        if (!$productGroupPage instanceof SilvercartProductGroupPage_Controller ||
             $productGroupPage->getProducts()->count() > 0) {
            $products = $productGroupPage->getProducts();
        } else {
            $products = $productGroupPage->getProductsFromChildren();
        }
        return $products;
    }
    
    /**
     * Returns the meta description. If not set, it will be generated by it's
     * related products or the single product in detail view
     * 
     * @return string
     */
    public function getMetaDescription() {
        $metaDescription = $this->getField('MetaDescription');
        if (!$this->getCMSFieldsIsCalled) {
            if (empty($metaDescription)) {
                $ctrl = Controller::curr();
                if (($ctrl instanceof SilvercartProductGroupPage_Controller) &&  $ctrl->isProductDetailView()) {
                    $product = $ctrl->getDetailViewProduct();
                    $metaDescription = $product->MetaDescription;
                } else {
                    $descriptionArray = array($this->Title);
                    $children         = $this->Children();
                    if ($children->count() > 0) {
                        $map = $children->map();
                        if ($map instanceof SS_Map) {
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
                    $metaDescription = SilvercartSeoTools::extractMetaDescriptionOutOfArray($descriptionArray);
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
     * 
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @since 06.11.2014
     */
    public function getMetaTitle() {
        $metaTitle = $this->getField('MetaTitle');
        if (!$this->getCMSFieldsIsCalled) {
            if (empty($metaTitle)) {
                if ((Controller::curr() instanceof SilvercartProductGroupPage_Controller) &&  Controller::curr()->isProductDetailView()) {
                    $product = Controller::curr()->getDetailViewProduct();
                    $metaTitle = $product->MetaTitle;
                } else {
                    $metaTitle = $this->Title;
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
    public function getproductsPerPage() {
        $productsPerPage = $this->getField('productsPerPage');
        if (!$this->getCMSFieldsIsCalled) {
            $this->extend('updateProductsPerPage', $productsPerPage);
        }
        return $productsPerPage;
    }
    
    /**
     * Returns the productGroupsPerPage setting.
     * 
     * @return string
     */
    public function getproductGroupsPerPage() {
        $productGroupsPerPage = $this->getField('productGroupsPerPage');
        if (!$this->getCMSFieldsIsCalled) {
            $this->extend('updateProductGroupsPerPage', $productGroupsPerPage);
        }
        return $productGroupsPerPage;
    }
    
    /**
     * Returns the useContentFromParent setting.
     * 
     * @return string
     */
    public function getuseContentFromParent() {
        $useContentFromParent = $this->getField('useContentFromParent');
        if (!$this->getCMSFieldsIsCalled) {
            $this->extend('updateUseContentFromParent', $useContentFromParent);
        }
        return $useContentFromParent;
    }
    
    /**
     * Returns the DoNotShowProducts setting.
     * 
     * @return string
     */
    public function getDoNotShowProducts() {
        $doNotShowProducts = $this->getField('DoNotShowProducts');
        if (!$this->getCMSFieldsIsCalled) {
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
    protected function onBeforeWrite() {
        if (!isset($this->ParentID)) {
            $productGroupHolder = SilvercartProductGroupHolder::get()->first();
            $this->ParentID     = $productGroupHolder->ID;
        }
        parent::onBeforeWrite();
    }
    
    /**
     * Set LastEdited field to now for the SilvercartProductGroupHolder.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.07.2012
     */
    public function onBeforeDelete() {
        $productGroupHolderPage = $this->getSilvercartProductGroupHolderPage();

        if ($productGroupHolderPage) {
            $now = new DateTime();
            $productGroupHolderPage->LastEdited = $now->format('Y-m-d H:i:s');
            $productGroupHolderPage->write();
        }

        parent::onBeforeDelete();
    }

    /**
     * Returns the first SilvercartProductGroupHolder page.
     *
     * @param SiteTree $context An optional SiteTree object
     *
     * @return SilvercartProductGroupHolder
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.07.2012
     */
    public function getSilvercartProductGroupHolderPage($context = null) {
        if (is_null($context)) {
            $context = $this;
        }

        if ( $context->ParentID > 0 &&
            !$context->Parent() instanceof SilvercartProductGroupHolder) {

            $context = $this->getSilvercartProductGroupHolderPage($context->Parent());
        }

        return $context;
    }
    
    /**
     * Returns a string to display how many products on how many pages are found
     * 
     * @return string
     */
    public function getProductsOnPagesString() {
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
            $productsOnPagesString = sprintf(
                    _t('SilvercartProductGroupPage.' . $singularOrPlural),
                    $products->count(),
                    $products->TotalPages()
            );
        }
        return $productsOnPagesString;
    }
    
    /**
     * Returns the pagination context.
     * 
     * @return PaginatedList
     */
    public function getPaginationContext() {
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
    public function setPaginationContext($paginationContext) {
        $this->paginationContext = $paginationContext;
    }
    
}

/**
 * Controller Class.
 * This controller handles the actions for product group views and product detail
 * views.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 30.08.2013
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartProductGroupPage_Controller extends Page_Controller {

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
    public static $registeredFilterPlugins = array();
    
    /**
     * Contains a DataList of products for this page or null. Used for
     * caching.
     *
     * @var mixed null|ArrayList
     */
    protected $groupProducts = array();

    /**
     * Contains the SilvercartProduct object that is used for the detail view
     * or null. Used for caching.
     *
     * @var mixed null|SilvercartProduct
     */
    protected $detailViewProduct = null;

    /**
     * Contains filters for the SQL query that retrieves the products for this
     * page.
     *
     * @var array
     */
    protected $listFilters = array();
    
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
    protected $widgetOutput = array();

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
    protected $productDetailViewParams = array();
    
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
     * Current SQL offset
     *
     * @var int 
     */
    protected $sqlOffsets = array();
    
    /**
     * List of allowed actions.
     *
     * @var array
     */
    public static $allowed_actions = array(
        'detail',
        'chsffopt',
        'chpppopt',
    );
    
    /**
     * Detail product to show
     *
     * @var SilvercartProduct
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
    public function init($skip = false) {
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

                $product = $this->getDetailViewProduct();

                $this->registerCustomHtmlForm(
                    'SilvercartProductAddCartFormDetail'.$product->ID,
                    new SilvercartProductAddCartFormDetail(
                        $this,
                        array(
                            'productID'          => $product->ID,
                            '_REDIRECT_BACK_URL' => $this->BackLink()
                        )
                    )
                );

                foreach ($product->WidgetArea()->Widgets() as $widget) {
                    $widgetControllerClass = $widget->class."_Controller";

                    if (class_exists($widgetControllerClass)) {
                        $widgetController = new $widgetControllerClass($widget);

                        if (method_exists($widgetController, 'registerCustomHtmlForms')) {
                            $widgetController->registerCustomHtmlForms();
                        }
                    }
                }
            } else {
                // a product group view is requested
                $this->initProductGroupPageProductForms();

                // Register selector forms, e.g. the "products per page" selector
                $selectorForm = new SilvercartProductGroupPageSelectorsForm($this);
                $selectorForm->setSecurityTokenDisabled();
                $selectorFormBottom = new SilvercartProductGroupPageSelectorsForm($this);
                $selectorFormBottom->setSecurityTokenDisabled();

                $this->registerCustomHtmlForm(
                    'SilvercartProductGroupPageSelectors',
                    $selectorForm
                );
                $this->registerCustomHtmlForm(
                    'SilvercartProductGroupPageSelectorsBottom',
                    $selectorFormBottom
                );
            }
        }
    }

    /**
     * Initialises the form objects for this page's products.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-01-08
     */
    public function initProductGroupPageProductForms() {
        if (!$this->extend('overwriteInitProductGroupPageProductForms')) {
            $products = $this->getProducts();

            Session::set("SilvercartProductGroupPageID", $this->ID);
            Session::save();
            // Initialise formobjects
            if ($products) {
                $backlink               = $this->Link()."?start=".$this->SQL_start;
                $productAddCartFormName = $this->getCartFormName();
                foreach ($products as $product) {
                    $productAddCartForm = new $productAddCartFormName(
                        $this,
                        array(
                            'productID' => $product->ID,
                            'backLink'  => $backlink,
                        )
                    );
                    $this->registerCustomHtmlForm('ProductAddCartForm' . $product->ID, $productAddCartForm);
                    $product->productAddCartFormObj = $productAddCartForm;
                }
            }
        }
    }

    /**
     * Returns the total number of products for the current controller.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.12.2012
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
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.12.2012
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
     * Uses the children of SilvercartMyAccountHolder to render a subnavigation
     * with the SilvercartSubNavigation.ss template.
     * 
     * @param string $identifierCode param only added because it exists on parent::getSubNavigation
     *                               to avoid strict notice
     *
     * @return string
     */
    public function getSubNavigation($identifierCode = 'SilvercartProductGroupHolder') {
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
    public function OriginalBreadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        return parent::Breadcrumbs($maxDepth, $unlinked, $stopAtPageType, $showHidden);
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
        return $this->data()->OriginalLink();
    }

    /**
     * manipulates the parts the pages breadcrumbs if a product detail view is 
     * requested.
     *
     * @param int    $maxDepth         maximum depth level of shown pages in breadcrumbs
     * @param bool   $unlinked         true, if the breadcrumbs should be displayed without links
     * @param string $stopAtPageType   name of pagetype to stop at
     * @param bool   $showHidden       true, if hidden pages should be displayed in breadcrumbs
     * @param bool   $showProductTitle true, if product title should be displayed in breadcrumbs
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Patrick Schneider <pschneider@pixeltricks.de>
     * @since 09.10.2012
     */
    public function BreadcrumbParts($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false, $showProductTitle = false) {
        $parts = parent::BreadcrumbParts($maxDepth, $unlinked, $stopAtPageType, $showHidden);
        
        if ($this->isProductDetailView()) {
            if ($showProductTitle) {
                $title = new Text();
                $title->setValue($this->getDetailViewProduct()->Title);
                $parts->push(
                        new ArrayData(
                                array(
                                    'MenuTitle' => $title,
                                    'Title' => $title,
                                    'Link'  => '',
                                )
                        )
                );
            }
        }
        
        $this->extend('updateBreadcrumbParts', $parts);
        
        return $parts;
    }
    
    /**
     * returns the breadcrumbs as ArrayList for use in controls without product title
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
    public function DropdownBreadcrumbsWithProduct($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        return $this->BreadcrumbParts($maxDepth, $unlinked, $stopAtPageType, $showHidden, true);
    }

    /**
     * manipulates the defaul logic of building the pages breadcrumbs if a
     * product detail view is requested and returns the breadcrumbs without 
     * product title.
     *
     * @param int    $maxDepth       maximum depth level of shown pages in breadcrumbs
     * @param bool   $unlinked       true, if the breadcrumbs should be displayed without links
     * @param string $stopAtPageType name of pagetype to stop at
     * @param bool   $showHidden     true, if hidden pages should be displayed in breadcrumbs
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.10.2012
     */
    public function BreadcrumbsWithoutProduct($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        return $this->Breadcrumbs($maxDepth, $unlinked, $stopAtPageType, $showHidden, false);
    }

    /**
     * manipulates the defaul logic of building the pages breadcrumbs if a
     * product detail view is requested and returns the breadcrumbs without 
     * product title.
     *
     * @param int    $maxDepth         maximum depth level of shown pages in breadcrumbs
     * @param string $stopAtPageType   name of pagetype to stop at
     * @param bool   $showHidden       true, if hidden pages should be displayed in breadcrumbs
     * @param bool   $showProductTitle true, if product title should be displayed in breadcrumbs
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.04.2013
     */
    public function BreadcrumbsWithoutLink($maxDepth = 20, $stopAtPageType = false, $showHidden = false, $showProductTitle = true) {
        return $this->Breadcrumbs($maxDepth, true, $stopAtPageType, $showHidden, $showProductTitle);
    }

    /**
     * manipulates the defaul logic of building the pages breadcrumbs if a
     * product detail view is requested.
     *
     * @param int    $maxDepth         maximum depth level of shown pages in breadcrumbs
     * @param bool   $unlinked         true, if the breadcrumbs should be displayed without links
     * @param string $stopAtPageType   name of pagetype to stop at
     * @param bool   $showHidden       true, if hidden pages should be displayed in breadcrumbs
     * @param bool   $showProductTitle true, if product title should be displayed in breadcrumbs
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.04.2015
     */
    public function Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false, $showProductTitle = true) {
        if ($this->isProductDetailView()) {
            return $this->BreadcrumbsForProduct($maxDepth, $unlinked, $stopAtPageType, $showHidden, $showProductTitle);
        }
        $breadcrumbs = null;
        $this->extend('overwriteBreadcrumbs', $breadcrumbs);
        if (is_null($breadcrumbs)) {
            $breadcrumbs = parent::Breadcrumbs($maxDepth, $unlinked, $stopAtPageType, $showHidden);
        }
        return $breadcrumbs;
    }

    /**
     * Returns the breadcrumbs for a product detail.
     *
     * @param int    $maxDepth         maximum depth level of shown pages in breadcrumbs
     * @param bool   $unlinked         true, if the breadcrumbs should be displayed without links
     * @param string $stopAtPageType   name of pagetype to stop at
     * @param bool   $showHidden       true, if hidden pages should be displayed in breadcrumbs
     * @param bool   $showProductTitle true, if product title should be displayed in breadcrumbs
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.12.2015
     */
    public function BreadcrumbsForProduct($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false, $showProductTitle = true) {
        $parts    = $this->BreadcrumbParts($maxDepth, $unlinked, $stopAtPageType, $showHidden, $showProductTitle);
		$template = new SSViewer('BreadcrumbsTemplate');
		return $template->process($this->customise((array(
			'Pages' => ($parts)
		))));
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
     * All products of this group forced (independant of DoNotShowProducts setting)
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
        $hashKey = md5($numberOfProducts . '_' . $sort . '_' . $disableLimit . Translatable::get_current_locale());
        if ($this->data()->DoNotShowProducts &&
            !$force) {
            $this->groupProducts[$hashKey] = new ArrayList();
        } elseif (!array_key_exists($hashKey, $this->groupProducts) || $force) {
            $SQL_start       = $this->getSqlOffset($numberOfProducts);
            $productsPerPage = $this->getProductsPerPageSetting();
            $pluginProducts  = SilvercartPlugin::call($this, 'overwriteGetProducts', array($numberOfProducts, $productsPerPage, $SQL_start, $sort), true, new ArrayList());

            if (!empty($pluginProducts)) {
                $this->groupProducts[$hashKey] = $pluginProducts;
            } else {
                $this->listFilters = array();
                $filter            = '';
                
                // ----------------------------------------------------------------
                // Get products that have this group set as mirror group
                // ----------------------------------------------------------------

                if ($numberOfProducts !== false) {
                    $productsPerPage = (int) $numberOfProducts;
                }
                
                $translations               = $this->getTranslations();
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
                    $manufacturer = SilvercartManufacturer::getByUrlSegment($this->urlParams['ID']);
                    if ($manufacturer) {
                        $this->addListFilter('SilvercartManufacturerID', $manufacturer->ID);
                    }
                }

                if (empty($mirroredProductIdList)) {
                    $this->listFilters['original'] = sprintf(
                        "SilvercartProductGroupID IN (%s)",
                        $translationProductGroupIDList
                    );
                } else {
                    $this->listFilters['original'] = sprintf(
                        "(SilvercartProductGroupID IN (%s) OR
                        \"SilvercartProduct\".\"ID\" IN (%s))",
                        $translationProductGroupIDList,
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
                    $sort = SilvercartProduct::defaultSort();
                    $this->extend('updateGetProductsSort', $sort);
                }
                
                $groupProducts = SilvercartProduct::getProducts($filter, $sort);
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
     * @since 03.04.2017
     */
    public function CacheKeyParts() {
        if (is_null($this->cacheKeyParts)) {
            $cacheKeyParts = array(
                $this->LastEdited,
                $this->LastEditedForCache,
                $this->MemberGroupCacheKey(),
                $this->getSqlOffset(),
                $this->getProductsPerPageSetting(),
                SilvercartGroupViewHandler::getActiveGroupView(),
                str_replace('-', '_', SilvercartTools::string2urlSegment(SilvercartProduct::defaultSort())),
            );
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
     * @since 03.04.2017
     */
    public function CacheKey() {
        if (is_null($this->cacheKey)) {
            $cacheKey = implode('_', $this->CacheKeyParts());
            $this->extend('updateCacheKey', $cacheKey);
            $this->cacheKey = $cacheKey;
        }
        return $this->cacheKey;
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
            $groupProducts = array();
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
                "SilvercartProductGroupID = '%s'",
                $this->ID
            );
        } else {
            $listFilters['original'] = sprintf(
                "(SilvercartProductGroupID = '%s' OR
                  \"SilvercartProduct\".\"ID\" IN (%s))",
                $this->ID,
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

        $products = SilvercartProduct::getProducts($filter, $sort, null, $numberOfProducts);
        
        return $products;
    }
    
    /**
     * Returns the number of products per page according to where it is set.
     * Highest priority has the customer's configuration setting if available.
     * Next comes the shop owners setting for this page; if that's not
     * configured we use the global setting from SilvercartConfig.
     *
     * @return int
     */
    public function getProductsPerPageSetting() {
        $productsPerPage = 0;
        $member          = SilvercartCustomer::currentUser();
        
        $productsPerPage = self::getProductsPerPage();
        if (is_null($productsPerPage)) {
            if ($member &&
                $member->getSilvercartCustomerConfig() &&
                $member->getSilvercartCustomerConfig()->productsPerPage !== null &&
                array_key_exists($member->getSilvercartCustomerConfig()->productsPerPage, SilvercartConfig::$productsPerPageOptions)) {

                $productsPerPage = $member->getSilvercartCustomerConfig()->productsPerPage;
            } else if ($this->productsPerPage) {
                $productsPerPage = $this->productsPerPage;
            } else {
                $productsPerPage = SilvercartConfig::ProductsPerPage();
            }
        }
        if ($productsPerPage == 0) {
            $productsPerPage = SilvercartConfig::getProductsPerPageUnlimitedNumber();
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
        if (array_key_exists($count, SilvercartConfig::$productsPerPageOptions)) {
            Session::set('SilvercartProductGroup.productsPerPage', $count);
            Session::save();
        }
    }

    /**
     * Returns the products per page count.
     * 
     * @return int
     */
    public static function getProductsPerPage() {
        return Session::get('SilvercartProductGroup.productsPerPage');
    }

    /**
     * Returns the sortable frontend fields as ArrayList.
     * 
     * @return ArrayList
     */
    public function getSortableFrontendFields() {
        if (is_null($this->sortableFrontendFields)) {
            $this->sortableFrontendFields = new ArrayList();
            $product                      = singleton('SilvercartProduct');
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
                        $pageLength = SilvercartConfig::ProductGroupsPerPage();
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
     * Action to show a product detail page.
     * Returns the rendered detail page.
     * 
     * @param SS_HTTPRequest $request Request
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2013
     */
    public function detail(SS_HTTPRequest $request) {
        $params     = $request->allParams();
        $productID  = $params['ID'];
        $product    = SilvercartProduct::get()->byID($productID);
        
        if (!($product instanceof SilvercartProduct) ||
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
            SilvercartTools::redirectPermanentlyTo($productLink);
        }
        
        $this->setProduct($product);
        return $this->render();
    }
    
    /**
     * chsffopt stands for "CHange Sortable Frontend Field Option".
     * Changes the sort order type for product lists.
     * 
     * @param SS_HTTPRequest $request Request
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.11.2014
     */
    public function chsffopt(SS_HTTPRequest $request) {
        $newOption                   = $request->param('ID');
        $product                     = singleton('SilvercartProduct');
        $sortableFrontendFields      = $product->sortableFrontendFields();
        $sortableFrontendFieldValues = array_keys($sortableFrontendFields);
        if (array_key_exists($newOption, $sortableFrontendFieldValues)) {
            $sortOrder = $sortableFrontendFieldValues[$newOption];
            SilvercartProduct::setDefaultSort($sortOrder);
        }
        $this->redirect($this->Link());
    }
    
    /**
     * chpppopt stands for "CHange Products Per Page Option".
     * Changes the quantity of products to display in a product lists.
     * 
     * @param SS_HTTPRequest $request Request
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.11.2014
     */
    public function chpppopt(SS_HTTPRequest $request) {
        $member                      = SilvercartCustomer::currentUser();
        $newOption                   = $request->param('ID');
        $product                     = singleton('SilvercartProduct');
        $sortableFrontendFields      = $product->sortableFrontendFields();
        $sortableFrontendFieldValues = array_keys($sortableFrontendFields);
        if (array_key_exists($newOption, $sortableFrontendFieldValues)) {
            $sortOrder = $sortableFrontendFieldValues[$newOption];
            SilvercartProduct::setDefaultSort($sortOrder);
        }
        
        if ($member instanceof Member &&
            $member->exists()) {
            $member->getSilvercartCustomerConfig()->productsPerPage = $newOption;
            $member->getSilvercartCustomerConfig()->write();
        }
        self::setProductsPerPage($newOption);
        $this->redirect($this->Link());
    }
    
    /**
     * Returns the detail product to show
     * 
     * @return SilvercartProduct
     */
    public function getProduct() {
        return $this->product;
    }
    
    /**
     * Sets the detail product to show
     * 
     * @param SilvercartProduct $product The detail product to show
     * 
     * @return void
     */
    public function setProduct($product) {
        $this->product = $product;
    }

    /**
     * handles the requested action.
     * If a product detail view is requested, the detail view template will be
     * rendered an displayed.
     *
     * @param SS_HTTPRequest $request request data
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
                SilvercartTools::redirectPermanentlyTo($productLink);
            }
            
            $this->setProduct($product);
            return $this->render();
        } elseif ($this->isFilteredByManufacturer()) {
            $url = str_replace($this->urlParams['Action'] . '/' . $this->urlParams['ID'], '', $_REQUEST['url']);
            $this->urlParams['Action'] = '';
            $this->urlParams['ID'] = '';
            $customRequest = new SS_HTTPRequest('GET', $url, array(), array(), null);
            return parent::handleAction($customRequest, $action);
            exit();
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
            $this->ProductDetailRequirements();
            $templates = $viewer->templates();
            $viewer    = new SSViewer(
                    array(
                        'SilvercartProductPage',
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
            $this->ProductDetailRequirements();
            $output = $this->customise(array())->renderWith(array('SilvercartProductPage','Page'));
            
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
     * renders a product detail view template (if requested)
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.07.2012
     */
    protected function ProductDetailRequirements() {
        Requirements::customScript("
            $(document).ready(function() {
                $('a.silvercart-product-detail-image').fancybox();
            });
        ", 'SilvercartProductDetailRequirements');
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
        if ($this->getDetailViewProduct() instanceof SilvercartProduct) {
            $isProductDetailView = true;
        }
        return $isProductDetailView;
    }

    /**
     * returns the chosen product when requesting a product detail view.
     *
     * @return SilvercartProduct
     */
    public function getDetailViewProduct() {
        if (!is_numeric($this->urlParams['Action'])) {
            return null;
        }
        if (is_null($this->detailViewProduct)) {
            $this->detailViewProduct = SilvercartProduct::get()->byID(Convert::raw2sql($this->urlParams['Action']));
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
                if ($product->SilvercartManufacturer()->ID > 0) {
                    return $product->MetaTitle ."/". $product->SilvercartManufacturer()->Title;
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

            if (is_array($params) && array_key_exists('Action', $params) && $params['Action'] == _t('SilvercartProductGroupPage.MANUFACTURER_LINK','manufacturer') && !empty ($params['ID'])) {
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
     * @example $productGroup->addListFilter('SilvercartManufacturerID','5');
     *          Will add the following filter: "AND \"SilvercartManufacturerID\" = '5'"
     * @example $productGroup->addListFilter('SilvercartManufacturerID','(5,6,7)','IN','OR');
     *          Will add the following filter: "OR \"SilvercartManufacturerID\" IN (5,6,7)"
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
    public function getInjectedProducts($excludeWidgets = array()) {
        $injectedProducts = new ArrayList();
        if ($this->WidgetSetContent()->count() > 0) {
            foreach ($this->WidgetSetContent() as $widgetSet) {
                if ($widgetSet->WidgetArea()->Widgets()->count() > 0) {
                    foreach ($widgetSet->WidgetArea()->Widgets() as $widget) {
                        if (in_array($widget->class, $excludeWidgets)) {
                            continue;
                        }
                        $controllerClass = $widget->class . '_Controller';
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
