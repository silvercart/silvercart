<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Products
 */

/**
 * abstract for a product
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sascha Koehler <skoehler@pixeltricks.de>,
 *         Roland Lehmann <rlehmann@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 12.07.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartProduct extends DataObject implements PermissionProvider {

    /**
     * Can contain an identifier for addToCart forms.
     *
     * @var mixed null|string
     */
    public $addCartFormIdentifier = null;

    /**
     * Can contain a name for addToCart forms.
     *
     * @var mixed null|string
     */
    public $addCartFormName = null;

    /**
     * attributes
     *
     * @var array
     */
    public static $db = array(
        'isActive'                    => 'Boolean(1)',
        'ProductNumberShop'           => 'VarChar(50)',
        'ProductNumberManufacturer'   => 'VarChar(50)',
        'EANCode'                     => 'VarChar(13)',
        'PriceGross'                  => 'SilvercartMoney', //price taxes including
        'PriceNet'                    => 'SilvercartMoney', //price taxes excluded
        'MSRPrice'                    => 'SilvercartMoney', //manufacturers recommended price
        'PurchasePrice'               => 'SilvercartMoney', //the price the shop owner bought the product for
        'PurchaseMinDuration'         => 'Int',
        'PurchaseMaxDuration'         => 'Int',
        'PurchaseTimeUnit'            => 'Enum(",Days,Weeks,Months","")',
        'StockQuantity'               => 'Int',
        'StockQuantityOverbookable'   => 'Boolean(0)',
        'StockQuantityExpirationDate' => 'Date',
        'PackagingQuantity'           => 'Int',
        'Weight'                      => 'Float', //unit is gramm
        'ReleaseDate'                 => 'Datetime',
        'LaunchDate'                  => 'Datetime',
        'SalesBanDate'                => 'Datetime',
        'ExcludeFromPaymentDiscounts' => 'Boolean(0)',
        'IsNotBuyable'                => 'Boolean(0)',
    );

    /**
     * 1:n relations
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartTax'                 => 'SilvercartTax',
        'SilvercartManufacturer'        => 'SilvercartManufacturer',
        'SilvercartProductGroup'        => 'SilvercartProductGroupPage',
        'SilvercartMasterProduct'       => 'SilvercartProduct',
        'SilvercartAvailabilityStatus'  => 'SilvercartAvailabilityStatus',
        'SilvercartProductCondition'    => 'SilvercartProductCondition',
        'SilvercartQuantityUnit'        => 'SilvercartQuantityUnit',
        'WidgetArea'                    => 'WidgetArea',
    );

    /**
     * n:m relations
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartProductLanguages'        => 'SilvercartProductLanguage',
        'SilvercartImages'                  => 'SilvercartImage',
        'SilvercartFiles'                   => 'SilvercartFile',
        'SilvercartShoppingCartPositions'   => 'SilvercartShoppingCartPosition',
    );

    /**
     * Belongs-many-many relations.
     *
     * @var array
     */
    public static $many_many = array(
        'SilvercartProductGroupMirrorPages' => 'SilvercartProductGroupPage'
    );

    /**
     * m:n relations
     *
     * @var array
     */
    public static $belongs_many_many = array(
        'SilvercartShoppingCarts'            => 'SilvercartShoppingCart',
        'SilvercartProductGroupItemsWidgets' => 'SilvercartProductGroupItemsWidget',
    );
    
    /**
     * Adds database indexes
     * 
     * @var array 
     */
    public static $indexes = array(
        'isActive'          => '("isActive")',
        'PriceGrossAmount'  => '("PriceGrossAmount")',
        'PriceNetAmount'    => '("PriceNetAmount")',
        'MSRPriceAmount'    => '("MSRPriceAmount")',
        'ProductNumberShop' => '("ProductNumberShop")',
        'EANCode'           => '("EANCode")',
    );

    /**
     * Casting.
     *
     * @var array
     */
    public static $casting = array(
        'isActiveString'                    => 'VarChar(8)',
        'SilvercartProductMirrorGroupIDs'   => 'Text',
        'PriceIsLowerThanMsr'               => 'Boolean',
        'Title'                             => 'Text',
        'ShortDescription'                  => 'Text',
        'LongDescription'                   => 'HTMLText',
        'MetaDescription'                   => 'Text',
        'MetaTitle'                         => 'Text',
        'MetaKeywords'                      => 'Text',
        'Link'                              => 'Text',
        'AbsoluteLink'                      => 'Text',
        'SilvercartProductGroupBreadcrumbs' => 'Text',
        'DefaultShippingFee'                => 'Text',
        'MSRPriceNice'                      => 'Text',
        'BeforeProductHtmlInjections'       => 'HTMLText',
        'AfterProductHtmlInjections'        => 'HTMLText',
    );

    /**
     * The default sorting.
     *
     * @var string
     */
    public static $default_sort = 'ProductNumberShop';

    /**
     * Grant API access on this item.
     *
     * @var bool
     */
    private static $api_access = true;

    /**
     * Array of all attributes that must be set to show an product in the frontend and enter it via backend.
     *
     * @var array
     */
    protected static $requiredAttributes = array();

    /**
     * Blacklist of attributes that may not be set as required attributes.
     *
     * @var array
     */
    protected static $blacklistedRequiredAttributes = array();
    
    /**
     * Temporary extended sortable frontend fields
     *
     * @var array
     */
    protected static $extendedSortableFrontendFields = array();

    /**
     * Contains hashes for caching.
     *
     * @var array
     */
    protected $cacheHashes = array();

    /**
     * The final price object (dependent on customer class and custom extensions
     * like rebates @see $this->getPrice())
     *
     * @var Money
     */
    protected $price = null;
    
    /**
     * All added product tabs via module
     * 
     * @var ArrayList 
     */
    protected $pluggedInTabs = null;
    
    /**
     * All added product additional information via module
     * 
     * @var ArrayList 
     */
    protected $pluggedInProductListAdditionalData = null;
    
    /**
     * All added product additional information to display between Images and 
     * Content.
     * 
     * @var DataObjectSet 
     */
    protected $pluggedInAfterImageContent = null;
    
    /**
     * All added product information via module
     * 
     * @var ArrayList 
     */
    protected $pluggedInProductMetaData = null;
    
    /**
     * Marker to check whether the CMS fields are called or not
     *
     * @var bool 
     */
    protected $getCMSFieldsIsCalled = false;
    
    /**
     * Default sort string to use for products
     *
     * @var string
     */
    protected static $scDefaultSort = null;
    
    /**
     * The sortable fields that can be used in frontend
     *
     * @var array
     */
    protected static $sortableFrontendFields = null;
    
    /**
     * Determines whether the stock quantity is overbookable or not
     *
     * @var bool 
     */
    protected $isStockQuantityOverbookable = null;
    
    /**
     * Cached SilvercartTax object. The related tax object will be stored in
     * this property after its first call.
     *
     * @var SilvercartTax
     */
    protected $cachedSilvercartTax = null;
    
    /**
     * Cached SilvercartAvailabilityStatus object. The related status object 
     * will be stored in this property after its first call.
     *
     * @var SilvercartAvailabilityStatus
     */
    protected $cachedSilvercartAvailabilityStatus = null;
    
    /**
     * The position of the product in cart.
     *
     * @var array
     */
    protected $positionInCart = array();
    
    /**
     * The quantity of the product in cart.
     *
     * @var array
     */
    protected $quantityInCart = array();
    
    /**
     * The quantity of the product in cart as a human readable string.
     *
     * @var string
     */
    protected $quantityInCartString = array();
    
    /**
     * Images to show
     *
     * @var array
     */
    protected $images = array();
    
    /**
     * Determines whether to ignore tax exemption or not.
     *
     * @var bool 
     */
    protected $ignoreTaxExemption = false;
    
    /**
     * The first image out of the related SilvercartImages.
     *
     * @var Image
     */
    protected $listImage = null;

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     *
     * @return string The objects singular name
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.05.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     *
     * @return string the objects plural name
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.05.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }

    /**
     * getter for the Title, looks for set translation
     * 
     * @return string The Title from the translation object or an empty string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.02.2014
     */
    public function getTitle() {
        $title = $this->getLanguageFieldValue('Title');
        if (!$this->getCMSFieldsIsCalled) {
            $this->extend('updateTitle', $title);
        }
        return $title;
    }
    
    /**
     * getter for the ShortDescription, looks for set translation
     * 
     * @param bool $includeHtml include html tags or remove them from description
     * 
     * @return string The ShortDescription from the translation object or an empty string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Patrick Schneider <pschneider@pixeltricks.de>
     * @since 19.05.2014
     */
    public function getShortDescription($includeHtml = true) {
        $shortDescription = $this->getLanguageFieldValue('ShortDescription');
        if (!$this->getCMSFieldsIsCalled) {
            if (!$includeHtml) {
                // decode
                $shortDescription = utf8_encode(html_entity_decode(strip_tags($shortDescription)));
            }
            $this->extend('updateShortDescription', $shortDescription);
        }
        return $shortDescription;
    }
    
    /**
     * getter for the LongDescription, looks for set translation
     * 
     * @param bool $includeHtml include html tags or remove them from description
     * 
     * @return string The LongDescription from the translation object or an empty string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Patrick Schneider <pschneider@pixeltricks.de>
     * @since 19.05.2014
     */
    public function getLongDescription($includeHtml = true) {
        $longDescription = $this->getLanguageFieldValue('LongDescription');
        if (!$this->getCMSFieldsIsCalled) {
            if (!$includeHtml) {
                // decode
                $longDescription = utf8_encode(html_entity_decode(strip_tags($longDescription)));
            }
            $this->extend('updateLongDescription', $longDescription);
        }
        return $longDescription;
    }
    
    /**
     * Returns the meta description. If not set, it will be generated by it's
     * related products.
     * 
     * @return string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public function getMetaDescription() {
        $metaDescription = $this->getLanguageFieldValue('MetaDescription');
        if (!$this->getCMSFieldsIsCalled) {
            if (empty($metaDescription)) {
                $metaDescription = SilvercartSeoTools::extractMetaDescriptionOutOfArray(
                        array(
                            $this->getTitle(),
                            $this->getLongDescription(),
                        )
                );
            }
            $this->extend('updateMetaDescription', $metaDescription);
        }
        return $metaDescription;
    }
    
    /**
     * Returns the meta title. If not set, it will be generated by it's
     * title.
     * 
     * @return string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public function getMetaTitle() {
        $metaTitle = $this->getLanguageFieldValue('MetaTitle');
        if (!$this->getCMSFieldsIsCalled) {
            if (empty($metaTitle)) {
                $metaTitle = Convert::raw2att($this->getTitle());
            }
            $this->extend('updateMetaTitle', $metaTitle);
        }
        return $metaTitle;
    }
    
    /**
     * Returns the meta keywords. If not set, it will be generated by it's
     * title.
     * 
     * @return string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public function getMetaKeywords() {
        $metaKeywords = $this->getLanguageFieldValue('MetaKeywords');
        if (!$this->getCMSFieldsIsCalled) {
            if (empty($metaKeywords)) {
                $metaKeywords = SilvercartSeoTools::extractMetaKeywords($this->getTitle());
            }
            $this->extend('updateMetaKeywords', $metaKeywords);
        }
        return $metaKeywords;
    }

    /**
     * Returns the breadcrumbs for the product group
     *
     * @param bool   $unlinked  Set to false to get linked breacrumbs (HTML)
     * @param string $delimiter Delimiter char to seperate product groups
     * 
     * @return string
     */
    public function getSilvercartProductGroupBreadcrumbs($unlinked = true, $delimiter = null) {
        $breadcrumbs = '';
        if ($this->SilvercartProductGroupID > 0) {
            $breadcrumbs = $this->SilvercartProductGroup()->Breadcrumbs(20, $unlinked);
        }
        return $breadcrumbs;
    }
    
    /**
     * Returns a fallback default country.
     * 
     * @return SilvercartCountry|null
     */
    public function getDefaultShippingCountry()
    {
        return SilvercartCustomer::currentShippingCountry();
    }
    
    /**
     * Returns the default shipping fee for this product
     *
     * @param SilvercartCountry $country       Country to get fee for
     * @param Group             $customerGroup Group to get fee for
     * 
     * @return SilvercartShippingFee 
     */
    public function getDefaultShippingFee(SilvercartCountry $country = null, $customerGroup = null) {
        $shippingFee = '';
        if (!is_null($country)) {
            if (is_null($customerGroup)) {
                $customerGroup = SilvercartCustomer::default_customer_group();
            }
            $shippingFee = SilvercartShippingMethod::getAllowedShippingFeeFor($this, $country, $customerGroup, true);
        }
        return $shippingFee;
    }
    
    /**
     * Returns the MSR price.
     * 
     * @return Money
     */
    public function getMSRPrice() {
        $msrPrice = $this->getField('MSRPrice');
        if (!$this->getCMSFieldsIsCalled) {
            $this->extend('updateMSRPrice', $msrPrice);
        }
        return $msrPrice;
    }
    
    /**
     * Returns the MSR price in a nice format
     * 
     * @return string
     */
    public function getMSRPriceNice() {
        return $this->MSRPrice->Nice();
    }
    
    /**
     * Returns some injected markup to display before the products detail data.
     * 
     * @return string
     */
    public function getBeforeProductHtmlInjections() {
        $beforeProductHtmlInjections = '';
        $this->extend('updateBeforeProductHtmlInjections', $beforeProductHtmlInjections);
        return $beforeProductHtmlInjections;
    }
    
    /**
     * Returns some injected markup to display after the products detail data.
     * 
     * @return string
     */
    public function getAfterProductHtmlInjections() {
        $afterProductHtmlInjections = '';
        $this->extend('updateAfterProductHtmlInjections', $afterProductHtmlInjections);
        return $afterProductHtmlInjections;
    }

    /**
     * Returns if the MSR price is greater than 0
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.11.2012
     */
    public function hasMSRPrice() {
        $hasMsrPrice = false;

        if ($this->MSRPrice->getAmount() > 0) {
            $hasMsrPrice = true;
        }

        return $hasMsrPrice;
    }
    
    /**
     * Returns whether the first image of this product has a portrait orientation.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.06.2017
     */
    public function hasPortraitOrientationImage() {
        $hasPortraitOrientationImage = false;
        $image = $this->getSilvercartImages()->first();
        if ($image instanceof SilvercartImage &&
            $image->Image()->exists()) {
            
            $imageFile = $image->Image();
            $maxRatio  = 2.5;

            if ($imageFile->getWidth() > 0 &&
                $imageFile->getHeight() > 0) {
                
                $orientation = $imageFile->getOrientation();
                $ratio       = $imageFile->getWidth() / $imageFile->getHeight();

                if ($orientation == Image::ORIENTATION_LANDSCAPE &&
                    ($ratio <= $maxRatio ||
                     $imageFile->getWidth() < 400)) {
                    $hasPortraitOrientationImage = true;
                } elseif ($orientation == Image::ORIENTATION_PORTRAIT) {
                    $hasPortraitOrientationImage = true;
                } elseif ($orientation != Image::ORIENTATION_LANDSCAPE) {
                    $hasPortraitOrientationImage = true;
                }
            }
        }
        $this->extend('updateHasPortraitOrientationImage', $hasPortraitOrientationImage, $imageFile);
        return $hasPortraitOrientationImage;
    }
    
    /**
     * Returns whether the first image of this product has a landscape orientation.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.06.2017
     */
    public function hasLandscapeOrientationImage() {
        return !$this->hasPortraitOrientationImage();
    }

    /**
     * Return a map of permission codes to add to the dropdown shown in the Security section of the CMS.
     * array(
     *   'VIEW_SITE' => 'View the site',
     * );
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.09.2014
     */
    public function providePermissions() {
        $permissions = array(
            'SILVERCART_PRODUCT_CREATE' => _t('SilvercartProduct.SILVERCART_PRODUCT_CREATE'),
        );
        $this->extend('updatePermissions', $permissions);
        return $permissions;
    }
    
    /**
     * Checks whether the given member can create a product.
     * 
     * @param Member $member Member to check
     *
     * @return false 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.09.2014
     */
    public function canCreate($member = null) {
        $can = Permission::checkMember($member, 'SILVERCART_PRODUCT_CREATE');
        $this->extend('updateCanCreate', $member, $can);
        return $can;
    }

    /**
     * Checks whether the given member can edit this product.
     * 
     * @param Member $member Member to check
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function canEdit($member = null) {
        $can = false;
        if (is_null($member)) {
            $member = SilvercartCustomer::currentUser();
        }
        if ($member instanceof Member &&
            (Permission::checkMember($member, 'ADMIN'))) {
            $can = true;
        }
        $this->extend('updateCanEdit', $member, $can);
        return $can;
    }

    /**
     * Is this product viewable in the frontend?
     *
     * @param Member $member the current member
     * 
     * @return bool
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.02.2013
     */
    public function canView($member = null) {
        $canView = parent::canView($member);
        if (!$canView &&
            $this->isActive) {
            $canView = true;
        }
        if (!SilvercartTools::isBackendEnvironment()) {
            if (!$this->isActive) {
                $canView = false;
            }
        }
        return $canView;
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.05.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'ListImageThumbnail'                    => $this->fieldLabel(''),
            'ProductNumberShop'                     => $this->fieldLabel('ProductNumberShop'),
            'Title'                                 => $this->singular_name(),
            'SilvercartProductGroup.Title'          => $this->fieldLabel('SilvercartProductGroup'),
            'SilvercartManufacturer.Title'          => $this->fieldLabel('SilvercartManufacturer'),
            'SilvercartAvailabilityStatus.Title'    => $this->fieldLabel('SilvercartAvailabilityStatus'),
            'isActiveString'                        => $this->fieldLabel('isActive'),
            'PriceGross'                            => $this->fieldLabel('PriceGross'),
            'PriceNet'                              => $this->fieldLabel('PriceNet'),
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * Searchable fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.05.2012
     */
    public function searchableFields() {
        $searchableFields = array(
            'ProductNumberShop' => array(
                'title'     => $this->fieldLabel('ProductNumberShop'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartProductLanguages.Title' => array(
                'title'     => $this->fieldLabel('Title'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartProductLanguages.ShortDescription' => array(
                'title'     => $this->fieldLabel('ShortDescription'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartProductLanguages.LongDescription' => array(
                'title'     => $this->fieldLabel('LongDescription'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartManufacturer.Title' => array(
                'title'     => $this->fieldLabel('SilvercartManufacturer'),
                'filter'    => 'PartialMatchFilter'
             ),
            'ProductNumberManufacturer' => array(
                'title'     => $this->fieldLabel('ProductNumberManufacturer'),
                'filter'    => 'PartialMatchFilter'
             ),
            'isActive' => array(
                'title'     => $this->fieldLabel('isActive'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartProductGroup.ID' => array(
                'title'     => $this->fieldLabel('SilvercartProductGroup'),
                'filter'    => 'ExactMatchFilter'
            ),
            'SilvercartProductGroupMirrorPages.ID' => array(
                'title'     => $this->fieldLabel('SilvercartProductGroupMirrorPages'),
                'filter'    => 'ExactMatchFilter'
            ),
            'SilvercartAvailabilityStatus.ID' => array(
                'title'     => $this->fieldLabel('SilvercartAvailabilityStatus'),
                'filter'    => 'ExactMatchFilter'
            ),
        );
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }

    /**
     * Adds temporary extended sortable frontend fields
     * 
     * @param array $extendedSortableFrontendFields Temporary extended sortable frontend fields
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.09.2012
     */
    public static function addExtendedSortableFrontendFields($extendedSortableFrontendFields) {
        foreach ($extendedSortableFrontendFields as $sortField => $sortLabel) {
            self::$extendedSortableFrontendFields[$sortField] = $sortLabel;
        }
    }

    /**
     * Returns the fields to sort a product by in frontend
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.11.2012
     */
    public function sortableFrontendFields() {
        if (is_null(self::$sortableFrontendFields)) {
            $sortableFrontendFields = array(
                'SilvercartProductLanguage.Title ASC'  => $this->fieldLabel('TitleAsc'),
                'SilvercartProductLanguage.Title DESC' => $this->fieldLabel('TitleDesc'),
            );
            if (SilvercartConfig::Pricetype() == 'gross') {
                $sortableFrontendFields = array_merge(
                        $sortableFrontendFields,
                        array(
                            'SilvercartProduct.PriceGrossAmount ASC'  => $this->fieldLabel('PriceAmountAsc'),
                            'SilvercartProduct.PriceGrossAmount DESC' => $this->fieldLabel('PriceAmountDesc'),
                        )
                );
            } else {
                $sortableFrontendFields = array_merge(
                        $sortableFrontendFields,
                        array(
                            'SilvercartProduct.PriceNetAmount ASC'    => $this->fieldLabel('PriceAmountAsc'),
                            'SilvercartProduct.PriceNetAmount DESC'   => $this->fieldLabel('PriceAmountDesc'),
                        )
                );
            }

            $allSortableFrontendFields = array_merge(
                    $sortableFrontendFields,
                    self::$extendedSortableFrontendFields
            );

            $this->extend('updateSortableFrontentFields', $allSortableFrontendFields);
            self::$sortableFrontendFields = $allSortableFrontendFields;
        }
        return self::$sortableFrontendFields;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.07.2013
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title'                                 => _t('SilvercartProduct.COLUMN_TITLE'),
                'LongDescription'                       => _t('SilvercartProduct.DESCRIPTION'),
                'ShortDescription'                      => _t('SilvercartProduct.SHORTDESCRIPTION'),
                'manufacturer.Title'                    => _t('SilvercartManufacturer.SINGULARNAME'),
                'PurchasePrice'                         => _t('SilvercartProduct.PURCHASEPRICE', 'purchase price'),
                'PurchasePriceAmount'                   => _t('SilvercartProduct.PURCHASEPRICE', 'purchase price'),
                'PurchasePriceCurrency'                 => _t('SilvercartProduct.PURCHASEPRICE_CURRENCY', 'purchase currency'),
                'MSRPrice'                              => _t('SilvercartProduct.MSRP', 'MSR price'),
                'MSRPriceAmount'                        => _t('SilvercartProduct.MSRP', 'MSR price'),
                'MSRPriceCurrency'                      => _t('SilvercartProduct.MSRP_CURRENCY', 'MSR currency'),
                'Price'                                 => _t('SilvercartProduct.PRICE', 'price'),
                'PriceGross'                            => _t('SilvercartProduct.PRICE_GROSS', 'price (gross)'),
                'PriceGrossAmount'                      => _t('SilvercartProduct.PRICE_GROSS', 'price (gross)'),
                'PriceGrossCurrency'                    => _t('SilvercartProduct.PRICE_GROSS_CURRENCY', 'currency (gross)'),
                'PriceNet'                              => _t('SilvercartProduct.PRICE_NET', 'price (net)'),
                'PriceNetAmount'                        => _t('SilvercartProduct.PRICE_NET', 'price (net)'),
                'PriceNetCurrency'                      => _t('SilvercartProduct.PRICE_NET_CURRENCY', 'currency (net)'),
                'MetaDescription'                       => _t('SilvercartProduct.METADESCRIPTION', 'meta description'),
                'Weight'                                => _t('SilvercartProduct.WEIGHT', 'weight'),
                'MetaTitle'                             => _t('SilvercartProduct.METATITLE', 'meta title'),
                'MetaKeywords'                          => _t('SilvercartProduct.METAKEYWORDS', 'meta keywords'),
                'PackagingContent'                      => _t('SilvercartProductPage.PACKAGING_CONTENT'),
                'ProductNumberShop'                     => _t('SilvercartProduct.PRODUCTNUMBER', 'product number'),
                'ProductNumberShort'                    => _t('SilvercartProduct.PRODUCTNUMBER_SHORT'),
                'ProductNumberManufacturer'             => _t('SilvercartProduct.PRODUCTNUMBER_MANUFACTURER', 'product number (manufacturer)'),
                'EANCode'                               => _t('SilvercartProduct.EAN', 'EAN'),
                'BasicData'                             => _t('SilvercartProduct.BasicData'),
                'MiscGroup'                             => _t('SilvercartRegistrationPage.OTHERITEMS'),
                'TimeGroup'                             => _t('SilvercartProduct.TimeGroup', 'Time Control'),
                'ReleaseDate'                           => _t('SilvercartProduct.ReleaseDate', 'Release Date'),
                'ReleaseDateInfo'                       => _t('SilvercartProduct.ReleaseDateInfo', 'Release Date Info'),
                'LaunchDate'                            => _t('SilvercartProduct.LaunchDate', 'Launch Date'),
                'LaunchDateInfo'                        => _t('SilvercartProduct.LaunchDateInfo', 'Launch Date Info'),
                'SalesBanDate'                          => _t('SilvercartProduct.SalesBanDate', 'Sale Ban Date'),
                'SalesBanDateInfo'                      => _t('SilvercartProduct.SalesBanDateInfo', 'Sale Ban Date Info'),
                'SilvercartTax'                         => _t('SilvercartTax.SINGULARNAME', 'tax'),
                'SilvercartManufacturer'                => _t('SilvercartManufacturer.SINGULARNAME', 'manufacturer'),
                'SilvercartProductGroup'                => _t('SilvercartProductGroupPage.SINGULARNAME', 'product group'),
                'SilvercartProductGroups'               => _t('SilvercartProductGroupPage.PLURALNAME', 'product groups'),
                'SilvercartProductGroupBreadcrumbs'     => _t('SilvercartProductGroupPage.BREADCRUMBS'),
                'SilvercartMasterProduct'               => _t('SilvercartProduct.MASTERPRODUCT', 'master product'),
                'Image'                                 => _t('SilvercartProduct.IMAGE', 'product image'),
                'SilvercartAvailabilityStatus'          => _t('SilvercartAvailabilityStatus.SINGULARNAME', 'Availability Status'),
                'PurchaseMinDuration'                   => _t('SilvercartProduct.PURCHASE_MIN_DURATION', 'Min. purchase duration'),
                'PurchaseMaxDuration'                   => _t('SilvercartProduct.PURCHASE_MAX_DURATION', 'Max. purchase duration'),
                'PurchaseTimeUnit'                      => _t('SilvercartProduct.PURCHASE_TIME_UNIT', 'Purchase time unit'),
                'SilvercartFiles'                       => _t('SilvercartFile.PLURALNAME', 'Files'),
                'SilvercartImages'                      => _t('SilvercartImage.PLURALNAME', 'Images'),
                'SilvercartFile'                        => _t('SilvercartFile.SINGULARNAME', 'File'),
                'SilvercartImage'                       => _t('SilvercartImage.SINGULARNAME', 'Image'),
                'SilvercartShoppingCartPositions'       => _t('SilvercartShoppingCartPosition.PLURALNAME', 'Cart positions'),
                'SilvercartShoppingCarts'               => _t('SilvercartShoppingCart.PLURALNAME', 'Carts'),
                'SilvercartOrders'                      => _t('SilvercartOrder.PLURALNAME', 'Orders'),
                'SilvercartProductGroupMirrorPages'     => _t('SilvercartProductGroupMirrorPage.PLURALNAME', 'Mirror-Productgroups'),
                'SilvercartQuantityUnit'                => _t('SilvercartProduct.AMOUNT_UNIT', 'amount Unit'),
                'isActive'                              => _t('SilvercartProduct.IS_ACTIVE'),
                'StockQuantity'                         => _t('SilvercartProduct.STOCKQUANTITY', 'stock quantity'),
                'StockQuantityOverbookable'             => _t('SilvercartProduct.STOCK_QUANTITY', 'Is the stock quantity of this product overbookable?'),
                'StockQuantityOverbookableShort'        => _t('SilvercartProduct.STOCK_QUANTITY_SHORT', 'Is overbookable?'),
                'StockQuantityExpirationDate'           => _t('SilvercartProduct.STOCK_QUANTITY_EXPIRATION_DATE'),
                'PackagingQuantity'                     => _t('SilvercartProduct.PACKAGING_QUANTITY', 'purchase quantity'),
                'ID'                                    => 'ID',
                'SilvercartProductLanguages'            => _t('SilvercartConfig.TRANSLATIONS'),
                'SilvercartProductGroupItemsWidgets'    => _t('SilvercartProductGroupItemsWidget.CMS_PRODUCTGROUPTABNAME'),
                'WidgetArea'                            => _t('SilvercartProduct.WIDGETAREA'),
                'Prices'                                => _t('SilvercartPrice.PLURALNAME'),
                'SEO'                                   => _t('Silvercart.SEO'),
                'SilvercartProductCondition'            => _t('SilvercartProductCondition.SINGULARNAME'),
                'TitleAsc'                              => _t('SilvercartProduct.TITLE_ASC'),
                'TitleDesc'                             => _t('SilvercartProduct.TITLE_DESC'),
                'PriceAmountAsc'                        => _t('SilvercartProduct.PRICE_AMOUNT_ASC'),
                'PriceAmountDesc'                       => _t('SilvercartProduct.PRICE_AMOUNT_DESC'),
                'DefaultShippingFee'                    => _t('SilvercartShippingFee.SINGULARNAME'),
                'RefreshCache'                          => _t('SilvercartProduct.RefreshCache'),
                'ExcludeFromPaymentDiscounts'           => _t('SilvercartProduct.ExcludeFromPaymentDiscounts'),
                'AddSilvercartImage'                    => _t('SilvercartProduct.AddSilvercartImage'),
                'AddSilvercartFile'                     => _t('SilvercartProduct.AddSilvercartFile'),
                'IsNotBuyable'                          => _t('SilvercartProduct.IsNotBuyable'),
                
                'SilvercartProductLanguages.Title'            => _t('SilvercartProduct.COLUMN_TITLE'),
                'SilvercartProductLanguages.ShortDescription' => _t('SilvercartProduct.SHORTDESCRIPTION'),
                'SilvercartProductLanguages.LongDescription'  => _t('SilvercartProduct.DESCRIPTION'),
                'SilvercartManufacturer.Title'                => _t('SilvercartProduct.COLUMN_TITLE'),
                'SilvercartProductGroupMirrorPages.ID'        => _t('SilvercartProductGroupMirrorPage.PLURALNAME'),
                'SilvercartAvailabilityStatus.ID'             => _t('SilvercartAvailabilityStatus.SINGULARNAME'),
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Returns YES when isActive is true, else it will return NO
     * (dependant on chosen language)
     *
     * @return string
     */
    public function getisActiveString() {
        $isActiveString = _t('Silvercart.NO');
        if ($this->isActive) {
            $isActiveString = _t('Silvercart.YES');
        }
        return $isActiveString;
    }

    /**
     * Returns the product condition. If none is defined at the product we
     * try to get the standard product condition as defined in the
     * SilvercartConfig.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.08.2011
     */
    public function getCondition() {
        $condition = '';

        if ($this->SilvercartProductConditionID > 0) {
            $condition = $this->SilvercartProductCondition()->Title;
        } else {
            if (SilvercartConfig::getStandardProductCondition()) {
                $condition = SilvercartConfig::getStandardProductCondition()->Title;
            }
        }

        return $condition;
    }

    /**
     * Returns the default sort order and direction.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.03.2012
     */
    public function getDefaultSort() {
        $sort = self::defaultSort();

        $this->extend('updateGetDefaultSort', $sort);

        return $sort;
    }

    /**
     * Returns the default sort order and direction.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@œÄixeltricks.de>
     * @since 30.08.2013
     */
    public static function defaultSort() {
        if (is_null(self::$scDefaultSort)) {
            $sort                   = Session::get('SilvercartProduct.defaultSort');
            $sortableFrontendFields = singleton('SilvercartProduct')->sortableFrontendFields();
            if (is_null($sort) ||
                $sort === false ||
                !is_string($sort) ||
                !array_key_exists($sort, $sortableFrontendFields)) {
                $sort = Config::inst()->get('SilvercartProduct', 'default_sort');
                if (strpos($sort, '.') === false) {
                    $sort = 'SilvercartProduct.' . $sort;
                }
                self::setDefaultSort($sort);
                Config::inst()->update('SilvercartProduct', 'default_sort', '');
            } else {
                Config::inst()->update('SilvercartProduct', 'default_sort', '');
            }
            self::$scDefaultSort = $sort;
        }
        return self::$scDefaultSort;
    }

    /**
     * Sets the default sort order and direction.
     *
     * @param string $defaultSort Default sort order and direction
     * 
     * @return void
     */
    public static function setDefaultSort($defaultSort) {
        Session::set('SilvercartProduct.defaultSort', $defaultSort);
        Session::save();
    }
    
    /**
     * Returns a list of products using the given filter parameters.
     * The required attributes stored in self::$requiredAttributes will be added 
     * to the filter parameters.
     * 
     * @param string $callerClass    Caller class name
     * @param string $filter         Filter to use
     * @param string $sort           Sort field(s) and direction
     * @param string $join           Join tables
     * @param string $limit          Result limitation
     * @param string $containerClass Container class
     * 
     * @return DataList
     * 
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @since 19.08.2014
     */
    public static function get($callerClass = 'SilvercartProduct', $filter = "", $sort = "", $join = "", $limit = null, $containerClass = 'DataList') {
        $products = parent::get($callerClass, $filter, $sort, $join, $limit, $containerClass);
        
        if (!SilvercartTools::isBackendEnvironment() &&
            !SilvercartTools::isIsolatedEnvironment()) {
            $requiredAttributesFilter = self::buildRequiredAttributesFilter();
            if (!is_null($requiredAttributesFilter)) {
                $products = $products->where($requiredAttributesFilter);
            }
        }
        
        return $products;
    }
    
    /**
     * Returns the product with the given product number.
     * 
     * @param string $productNumber Product number
     * 
     * @return SilvercartProduct
     */
    public static function get_by_product_number($productNumber) {
        $product = self::get()
                ->filter('ProductNumberShop', $productNumber)
                ->first();
        if (is_null($product)) {
            $product = new SilvercartProduct();
        }
        return $product;
    }
    
    /**
     * Uses the required attributes stored in self::$requiredAttributes to build
     * the filter to use to get a product list.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.01.2013
     */
    public static function buildRequiredAttributesFilter() {
        $filter             = null;
        $requiredAttributes = self::getRequiredAttributes();
        $pricetype          = SilvercartConfig::Pricetype();
        $exclude            = array();
        
        $requiredAttributes[] = 'isActive';

        if (!empty($requiredAttributes)) {
            foreach ($requiredAttributes as $requiredAttribute) {
                //find out if we are dealing with a real attribute or a multilingual field
                if (array_key_exists($requiredAttribute, DataObject::custom_database_fields('SilvercartProduct')) || $requiredAttribute == "Price") {
                    if ($requiredAttribute == "Price") {
                        // Gross price as default if not defined
                        if ($pricetype == "net") {
                            $exclude['PriceNetAmount'] = 0;
                        } else {
                            $exclude['PriceGrossAmount'] = 0;
                        }
                    } else {
                        $exclude[$requiredAttribute] = '';
                    }
                } else {
                    // if its a multilingual attribute it comes from a relational class
                    $exclude[$requiredAttribute] = '';
                }
                
            }
        }

        $SQL_Statements = array();
        foreach ($exclude as $fieldName => $value) {
            if ($fieldName == 'ID') {
                $fieldName = sprintf('"%s"."ID"', ClassInfo::baseDataClass('SilvercartProduct'));
            } else {
                $fieldName = '"' . Convert::raw2sql($fieldName) . '"';
            }

            if (is_array($value)) {
                $SQL_Statements[] = ($fieldName . ' NOT IN (\'' . implode('\',\'', Convert::raw2sql($value)) . '\')');
            } else {
                $SQL_Statements[] = ($fieldName . ' != \'' . Convert::raw2sql($value) . '\'');
            }
        }

        if (count($SQL_Statements) > 0) {
            $filter = implode(" AND ", $SQL_Statements);
        }
        return $filter;
    }
    
    /**
     * Returns the SQL filter to prevent to display products which don't match the required
     * attributes.
     * 
     * @return string
     */
    public static function get_frontend_sql_filter() {
        $requiredAttributes = self::getRequiredAttributes();
        $pricetype          = SilvercartConfig::Pricetype();
        $filter             = "";

        if (!empty($requiredAttributes)) {
            foreach ($requiredAttributes as $requiredAttribute) {
                //find out if we are dealing with a real attribute or a multilingual field
                if (array_key_exists($requiredAttribute, DataObject::custom_database_fields('SilvercartProduct')) || $requiredAttribute == "Price") {
                    if ($requiredAttribute == "Price") {
                        // Gross price as default if not defined
                        if ($pricetype == "net") {
                            $filter .= sprintf("(PriceNetAmount != 0.0) AND ");
                        } else {
                            $filter .= sprintf("(PriceGrossAmount != 0.0) AND ");
                        }
                    } else {
                        $filter .= sprintf("%s != '' AND ", $requiredAttribute);
                    }
                } else {
                    // if its a multilingual attribute it comes from a relational class
                    $filter .= sprintf("SilvercartProductLanguage.%s != '' AND ", $requiredAttribute);
                }
                
            }
        }
        $filter .= 'isActive = 1 AND SilvercartProductGroupID > 0';
        
        return $filter;
    }

    /**
     * Getter similar to DataObject::get(); returns a SS_List of products filtered by the requirements in self::getRequiredAttributes();
     * If an product is free of charge, it can have no price. This is for giveaways and gifts.
     *
     * Expected format of $joins:
     * <pre>
     * array(
     *      array(
     *          'table' => 'JoinTableName_1',
     *          'on'    => 'JoinTableOnClause_1',
     *          'alias' => 'JoinTableAlias_1',
     *      ),
     *      array(
     *          'table' => 'JoinTableName_2',
     *          'on'    => 'JoinTableOnClause_2',
     *          'alias' => 'JoinTableAlias_2',
     *      ),
     *      ...
     * )
     * </pre>
     * 
     * @param string  $whereClause to be inserted into the sql where clause
     * @param string  $sort        string with sort clause
     * @param array   $joins       left join data as multi dimensional array
     * @param integer $limit       DataObject limit
     *
     * @return PaginatedList|ArrayList PaginatedList of products or empty ArrayList
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.02.2015
     */
    public static function getProducts($whereClause = "", $sort = null, $joins = null, $limit = null) {
        $filter = self::get_frontend_sql_filter();

        if ($whereClause != "") {
            $filter = $filter . ' AND ' . $whereClause;
        }


        if ($sort === null) {
            $sort = self::defaultSort();
        }
        
        $onclause = sprintf('"SPL"."SilvercartProductID" = "SilvercartProduct"."ID" AND "SPL"."Locale" = \'%s\'', Translatable::get_current_locale());
        $databaseFilteredProducts = SilvercartProduct::get()
                ->leftJoin('SilvercartProductLanguage', $onclause, 'SPL')
                ->where($filter)
                ->sort($sort);
        if (!is_null($joins) &&
            is_array($joins)) {
            foreach ($joins as $joinData) {
                $table    = $alias = $joinData['table'];
                $onClause = $joinData['on'];
                if (array_key_exists('alias', $joinData)) {
                    $alias = $joinData['alias'];
                }
                $databaseFilteredProducts = $databaseFilteredProducts->leftJoin($table, $onClause, $alias);
            }
        }
        if (!is_null($limit)) {
            $offset = 0;
            if (strpos($limit, ',') !== false) {
                list($offset, $limit) = explode(',', $limit);
            }
            $databaseFilteredProducts = $databaseFilteredProducts->limit($limit, $offset);
        }
        if (Controller::curr()->hasMethod('getProductsPerPageSetting') &&
            $databaseFilteredProducts) {
            $databaseFilteredProducts = new PaginatedList($databaseFilteredProducts, $_GET);
            $databaseFilteredProducts->setPageLength(Controller::curr()->getProductsPerPageSetting());
        }

        return $databaseFilteredProducts;
    }

    /**
     * Getter similar to DataObject::get(); returns a SS_List of products filtered by the requirements in self::getRequiredAttributes();
     * If an product is free of charge, it can have no price. This is for giveaways and gifts.
     *
     * Expected format of $joins:
     * <pre>
     * array(
     *      array(
     *          'table' => 'JoinTableName_1',
     *          'on'    => 'JoinTableOnClause_1',
     *          'alias' => 'JoinTableAlias_1',
     *      ),
     *      array(
     *          'table' => 'JoinTableName_2',
     *          'on'    => 'JoinTableOnClause_2',
     *          'alias' => 'JoinTableAlias_2',
     *      ),
     *      ...
     * )
     * </pre>
     * 
     * @param string  $whereClause to be inserted into the sql where clause
     * @param string  $sort        string with sort clause
     * @param array   $joins       left join data as multi dimensional array
     * @param integer $limit       DataObject limit
     * @param array   $request     Request data
     * @param integer $pageLength  Count of items per page
     *
     * @return PaginatedList|ArrayList PaginatedList of products or empty ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.10.2014
     */
    public static function getPaginatedProducts($whereClause = "", $sort = null, $joins = null, $limit = null, $request = null, $pageLength = null) {
        $paginatedProducts = null;
        if (is_null($request)) {
            $request = $_GET;
        }
        if (is_null($pageLength)) {
            $pageLength = SilvercartConfig::ProductsPerPage();
        }
        $products = self::getProducts($whereClause, $sort, $joins, $limit);
        if ($products instanceof SS_List &&
            $products->exists()) {
            if ($products instanceof PaginatedList) {
                $paginatedProducts = $products;
            } else {
                $paginatedProducts = new PaginatedList($products, $request);
            }
            $paginatedProducts->setPageLength($pageLength);
        }
        return $paginatedProducts;
    }

    /**
     * Creates a whitelist with restricted fields for the FormScaffolder.
     *
     * @param array $params Parameters to manipulate the scaffolding
     *
     * @return FieldList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.12.2013
     */
    public function scaffoldFormFields($params = null) {
        $params = array(
            'tabbed' => true,
            'restrictFields' => array(
                'Title',
                'ShortDescription',
                'LongDescription',
                'MetaDescription',
                'MetaTitle',
                'MetaKeywords',
                'ProductNumberShop',
                'ProductNumberManufacturer',
                'PurchasePrice',
                'MSRPrice',
                'PriceGross',
                'PriceNet',
                'Weight',
                'EANCode',
                'isActive',
                'PurchaseMinDuration',
                'PurchaseMaxDuration',
                'PurchaseTimeUnit',
                'SilvercartTax',
                'SilvercartManufacturer',
                'SilvercartAvailabilityStatus',
                'SilvercartQuantityUnit',
                'SilvercartProductCondition',
                'SilvercartFiles',
                'SilvercartOrders',
                'StockQuantity',
                'StockQuantityOverbookable',
                'StockQuantityExpirationDate',
                'PackagingQuantity',
                'ExcludeFromPaymentDiscounts',
                'IsNotBuyable',
            ),
            'includeRelations' => array(
                'has_many'  => true,
            ),
        );

        $this->extend('updateScaffoldFormFields', $params);

        return SilvercartDataObject::scaffoldFormFields($this, $params);
    }

    /**
     * Adds the fields for the MirrorProductGroups tab
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    public function getFieldsForProductGroups($fields) {
        $productGroupHolder = SilvercartTools::PageByIdentifierCode('SilvercartProductGroupHolder');

        $silvercartProductGroupDropdown = new TreeDropdownField(
                'SilvercartProductGroupID',
                $this->fieldLabel('SilvercartProductGroup'),
                'SiteTree'
        );

        if ($productGroupHolder) {
            $productGroupHolderID = $productGroupHolder->ID;
        } else {
            $productGroupHolderID = 0;
        }
        $silvercartProductGroupDropdown->setTreeBaseID($productGroupHolderID);
        
        if ($this->exists()) {
            $silvercartProductGroupMirrorPagesField   = new TreeMultiselectField(
                    'SilvercartProductGroupMirrorPages',
                    $this->fieldLabel('SilvercartProductGroupMirrorPages'),
                    'SiteTree'
            );
            $silvercartProductGroupMirrorPagesField->setTreeBaseID($productGroupHolderID);

            $fields->removeByName('SilvercartProductGroupMirrorPages');
            $fields->insertBefore($silvercartProductGroupDropdown, 'ProductNumberGroup');
            $fields->insertAfter($silvercartProductGroupMirrorPagesField, 'SilvercartProductGroupID');
        } else {
            $fields->insertBefore($silvercartProductGroupDropdown, 'ProductNumberGroup');
        }
    }

    /**
     * Adds the fields for the Widgets tab
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    public function getFieldsForWidgets($fields) {
        $widgetAreaFields = WidgetSet::scaffold_widget_area_fields_for($this);
        $fields->addFieldsToTab('Root.Widgets', $widgetAreaFields);
    }

    /**
     * Adds or modifies the fields for the Main tab
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    public function getFieldsForMain($fields) {
        $fields->dataFieldByName('StockQuantityOverbookable')->setTitle($this->fieldLabel('StockQuantityOverbookableShort'));
        $fields->dataFieldByName('StockQuantityExpirationDate')->addExtraClass("date");
        $fields->dataFieldByName('StockQuantityExpirationDate')->setConfig('showcalendar', true);
        $fields->dataFieldByName('StockQuantityExpirationDate')->FieldHolder();
        $purchaseTimeUnitSource = array(
            'Days'      => _t('Silvercart.DAYS','Days'),
            'Weeks'     => _t('Silvercart.WEEKS','Weeks'),
            'Months'    => _t('Silvercart.MONTHS','Months'),
        );
        $fields->dataFieldByName('PurchaseTimeUnit')->setSource($purchaseTimeUnitSource);

        $fields->dataFieldByName('ReleaseDate')
                ->getDateField()
                ->addExtraClass("date")
                ->setConfig('showcalendar', true);
        $fields->dataFieldByName('LaunchDate')
                ->getDateField()
                ->addExtraClass("date")
                ->setConfig('showcalendar', true);
        $fields->dataFieldByName('SalesBanDate')
                ->getDateField()
                ->addExtraClass("date")
                ->setConfig('showcalendar', true);
        
        $productNumberGroup = new SilvercartFieldGroup('ProductNumberGroup', '', $fields);
        $productNumberGroup->push($fields->dataFieldByName('ProductNumberShop'));
        $productNumberGroup->push($fields->dataFieldByName('ProductNumberManufacturer'));
        $productNumberGroup->push($fields->dataFieldByName('EANCode'));
        $baseDataToggle = ToggleCompositeField::create(
                'ProductBaseDataToggle',
                $this->fieldLabel('BasicData'),
                array(
                    $fields->dataFieldByName('isActive'),
                    $fields->dataFieldByName('IsNotBuyable'),
                    $productNumberGroup,
                )
        )->setHeadingLevel(4)->setStartClosed(false);
        $fields->removeByName('isActive');
        $fields->removeByName('IsNotBuyable');
        $fields->insertBefore($baseDataToggle, 'Title');
        if ($this->exists()) {
            $fields->insertAfter(new CheckboxField('RefreshCache', $this->fieldLabel('RefreshCache')), 'isActive');
        }

        $availabilityGroup  = new SilvercartFieldGroup('AvailabilityGroup', '', $fields);
        $availabilityGroup->push(           $fields->dataFieldByName('SilvercartAvailabilityStatusID'));
        $availabilityGroup->breakAndPush(   $fields->dataFieldByName('PurchaseMinDuration'));
        $availabilityGroup->push(           $fields->dataFieldByName('PurchaseMaxDuration'));
        $availabilityGroup->push(           $fields->dataFieldByName('PurchaseTimeUnit'));
        $availabilityGroup->breakAndPush(   $fields->dataFieldByName('StockQuantity'));
        $availabilityGroup->push(           $fields->dataFieldByName('StockQuantityOverbookable'));
        $availabilityGroup->push(           $fields->dataFieldByName('StockQuantityExpirationDate'));
        $availabilityGroupToggle = ToggleCompositeField::create(
                'AvailabilityGroupToggle',
                $this->fieldLabel('SilvercartAvailabilityStatus'),
                array(
                    $availabilityGroup,
                )
        )->setHeadingLevel(4)->setStartClosed(false);
        $fields->insertAfter($availabilityGroupToggle, 'ProductBaseDataToggle');
        
        $descriptionToggle = ToggleCompositeField::create(
                'ProductDescriptionToggle',
                $this->fieldLabel('LongDescription'),
                array(
                    $fields->dataFieldByName('Title'),
                    $fields->dataFieldByName('ShortDescription'),
                    $fields->dataFieldByName('LongDescription'),
                )
        )->setHeadingLevel(4)->setStartClosed(false);
        $fields->removeByName('Title');
        $fields->removeByName('ShortDescription');
        $fields->removeByName('LongDescription');
        $fields->insertAfter($descriptionToggle, 'AvailabilityGroupToggle');
        
        $timeGroup = new SilvercartFieldGroup('TimeGroup', '', $fields);
        $timeGroup->push(        $fields->dataFieldByName('ReleaseDate'));
        $timeGroup->pushAndBreak(new LabelField('ReleaseDateInfo', '<br/><br/><br/>' . $this->fieldLabel('ReleaseDateInfo')));
        $timeGroup->push(        $fields->dataFieldByName('LaunchDate'));
        $timeGroup->pushAndBreak(new LabelField('LaunchDateInfo', '<br/><br/><br/>' . $this->fieldLabel('LaunchDateInfo')));
        $timeGroup->push(        $fields->dataFieldByName('SalesBanDate'));
        $timeGroup->pushAndBreak(new LabelField('SalesBanDateInfo', '<br/><br/><br/>' . $this->fieldLabel('SalesBanDateInfo')));
        $timeGroupToggle = ToggleCompositeField::create(
                'TimeGroupToggle',
                $this->fieldLabel('TimeGroup'),
                array(
                    $timeGroup,
                )
        )->setHeadingLevel(4)->setStartClosed(true);
        $fields->insertAfter($timeGroupToggle, 'ProductDescriptionToggle');
        
        $miscGroup = new SilvercartFieldGroup('MiscGroup', '', $fields);
        if ($fields->hasField('SilvercartManufacturerID')) {
            $miscGroup->pushAndBreak(   $fields->dataFieldByName('SilvercartManufacturerID'));
        }
        $miscGroup->breakAndPush(   $fields->dataFieldByName('ExcludeFromPaymentDiscounts'));
        $miscGroup->breakAndPush(   $fields->dataFieldByName('PackagingQuantity'));
        $miscGroup->pushAndBreak(   $fields->dataFieldByName('SilvercartQuantityUnitID'));
        $miscGroup->breakAndPush(   $fields->dataFieldByName('Weight'));
        $miscGroup->breakAndPush(   $fields->dataFieldByName('SilvercartProductConditionID'));
        $miscGroupToggle = ToggleCompositeField::create(
                'AvailabilityGroupToggle',
                $this->fieldLabel('MiscGroup'),
                array(
                    $miscGroup,
                )
        )->setHeadingLevel(4)->setStartClosed(true);
        $fields->insertAfter($miscGroupToggle, 'TimeGroupToggle');
    }

    /**
     * Adds or modifies the fields for the Prices tab
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    public function getFieldsForPrices($fields) {
        SilvercartTax::presetDropdownWithDefault($fields->dataFieldByName('SilvercartTaxID'), $this);
        
        $pricesGroup  = new SilvercartFieldGroup('PricesGroup', '', $fields);
        $pricesGroup->push($fields->dataFieldByName('PriceGross'));
        $pricesGroup->push($fields->dataFieldByName('PriceNet'));
        $pricesGroup->push($fields->dataFieldByName('MSRPrice'));
        $pricesGroup->push($fields->dataFieldByName('PurchasePrice'));
        $pricesGroup->push($fields->dataFieldByName('SilvercartTaxID'));
        
        $this->extend('updateFieldsForPrices', $pricesGroup, $fields);
        $fields->insertAfter($pricesGroup, 'ProductNumberGroup');
    }

    /**
     * Adds or modifies the fields for the SEO tab
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    public function getFieldsForSeo($fields) {
        $seoToggle = ToggleCompositeField::create(
                'SEOToggle',
                $this->fieldLabel('SEO'),
                array(
                    $fields->dataFieldByName('MetaTitle'),
                    $fields->dataFieldByName('MetaDescription'),
                    $fields->dataFieldByName('MetaKeywords'),
                )
        )->setHeadingLevel(4)->setStartClosed(true);
        $fields->removeByName('MetaTitle');
        $fields->removeByName('MetaDescription');
        $fields->removeByName('MetaKeywords');
        $fields->insertAfter($seoToggle, 'ProductDescriptionToggle');
    }

    /**
     * Adds or modifies the fields for the Images tab
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    public function getFieldsForImages($fields) {
        $imageGridField = $fields->dataFieldByName('SilvercartImages');
        $imageGridField->getConfig()->removeComponentsByType('GridFieldAddNewButton');
        $imageGridField->getConfig()->removeComponentsByType('GridFieldAddExistingAutocompleter');
        $imageGridField->getConfig()->addComponent(new GridFieldDeleteAction());
        
        if (class_exists('GridFieldSortableRows')) {
            $imageGridField->getConfig()->addComponent(new GridFieldSortableRows('SortOrder'));
        }
        
        $imageUploadField = new SilvercartImageUploadField('UploadSilvercartImages', $this->fieldLabel('AddSilvercartImage'));
        $imageUploadField->setFolderName('Uploads/product-images');
        
        $fields->addFieldToTab('Root.SilvercartImages', $imageUploadField, 'SilvercartImages');
    }

    /**
     * Adds or modifies the fields for the Files tab
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    public function getFieldsForFiles($fields) {
        $fileGridField = $fields->dataFieldByName('SilvercartFiles');
        $fileGridField->getConfig()->removeComponentsByType('GridFieldAddNewButton');
        $fileGridField->getConfig()->removeComponentsByType('GridFieldAddExistingAutocompleter');
        $fileGridField->getConfig()->addComponent(new GridFieldDeleteAction());
        
        $fileUploadField = new SilvercartFileUploadField('UploadSilvercartFiles', $this->fieldLabel('AddSilvercartFile'));
        $fileUploadField->setFolderName('Uploads/product-files');
        
        $fields->addFieldToTab('Root.SilvercartFiles', $fileUploadField, 'SilvercartFiles');
    }
    
    /**
     * CMS fields of a product
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $this->beforeUpdateCMSFields(function($fields) {
            $fields->removeByName('SilvercartProductGroupItemsWidgets');
            $fields->removeByName('SilvercartMasterProductID');

            $this->getFieldsForMain($fields);
            $this->getFieldsForPrices($fields);
            $this->getFieldsForProductGroups($fields);
            $this->getFieldsForSeo($fields);
            if ($this->exists()) {
                $this->getFieldsForWidgets($fields);
                $this->getFieldsForImages($fields);
                $this->getFieldsForFiles($fields);
            }
        });
        $this->getCMSFieldsIsCalled = true;
        return SilvercartDataObject::getCMSFields($this, 'isActive');
    }

    /**
     * Returns an HTML encoded long description, preserving HTML tags.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.11.2011
     */
    public function getHtmlEncodedLongDescription() {
        $output = htmlentities($this->LongDescription, ENT_NOQUOTES, 'UTF-8', false);

        $output = str_replace(
            array(
                '&lt;',
                '&gt;'
            ),
            array(
                '<',
                '>'
            ),
            $output
        );

        return $output;
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.03.2013
     */
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'SilvercartShoppingCartPositions',
            'WidgetArea',
            'SilvercartShoppingCarts',
            'SilvercartOrders',
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }

    /**
     * Returns an HTML encoded short description, preserving HTML tags.
     *
     * @param int $cutToLength Limit the length of the result to the given
     *                         number of characters.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.2011
     */
    public function getHtmlEncodedShortDescription($cutToLength = false) {
        $output = htmlentities($this->ShortDescription, ENT_NOQUOTES, 'UTF-8', false);

        $output = str_replace(
            array(
                '&lt;',
                '&gt;'
            ),
            array(
                '<',
                '>'
            ),
            $output
        );

        if ($cutToLength !== false) {
            $line = $output;
            if (preg_match('/^.{1,'.$cutToLength.'}\b/s', $output, $match)) {
                $line = $match[0];
            }

            $output = $line;
        }

        return $output;
    }

    /**
     * Getter for product price
     * May be decorated by the module silvercart_graduatedprices
     *
     * @param string $priceType          Set to 'gross' or 'net' to get the desired prices.
     *                                   If not given the price type will be automatically determined.
     * @param bool   $ignoreTaxExemption Determines whether to ignore tax exemption or not.
     *
     * @return Money price dependent on customer class and configuration
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function getPrice($priceType = '', $ignoreTaxExemption = false) {
        $cacheHash = md5($priceType);
        $cacheKey = 'getPrice_' . $cacheHash . '_' . $ignoreTaxExemption ? '1' : '0';

        if (array_key_exists($cacheKey, $this->cacheHashes)) {
            return $this->cacheHashes[$cacheKey];
        }

        if (empty($priceType)) {
            $priceType = SilvercartConfig::PriceType();
        }
        
        if ($priceType == "net") {
            $price = clone $this->PriceNet;
        } elseif ($priceType == "gross") {
            $price = clone $this->PriceGross;
        } else {
            $price = clone $this->PriceGross;
        }
        
        $member = SilvercartCustomer::currentUser();
        if (!$ignoreTaxExemption &&
            !$this->ignoreTaxExemption &&
            $member instanceof Member &&
            $member->doesNotHaveToPayTaxes() &&
            $priceType != "net") {
            $this->ignoreTaxExemption = true;
            $price->setAmount($price->getAmount() - $this->getTaxAmount());
            $this->ignoreTaxExemption = false;
        }

        $price->setAmount(round($price->getAmount(), 2));

        if ($price->getAmount() < 0) {
            $price->setAmount(0);
        }
        //overwrite the price in a decorator
        $this->extend('updatePrice', $price);
        $this->price = $price;

        $this->cacheHashes[$cacheKey] = $this->price;
        return $this->price;
    }

    /**
     * Returns the formatted (Nice) price.
     *
     * @return string
     */
    public function getPriceNice() {
        $priceNice = '';
        $price     = $this->getPrice();

        if ($price) {
            $priceNice = $price->Nice();
        }
        $this->extend('updatePriceNice', $priceNice);

        return $priceNice;
    }

    /**
     * define the searchable fields and search methods for the frontend
     *
     * @return SearchContext ???
     *
     * @author Roland Lehmann
     * @since 23.10.2010
     */
    public function getCustomSearchContext() {
        $fields = $this->scaffoldSearchFields(
            array(
                'restrictFields' => array(
                    'Title',
                    'LongDescription',
                    'SilvercartManufacturer.Title'
                )
            )
        );
        $filters = array(
            'Title' => new PartialMatchFilter('Title'),
            'LongDescription' => new ExactMatchFilter('LongDescription'),
            'SilvercartManufacturer.Title' => new PartialMatchFilter('SilvercartManufacturer.Title')
        );
        return new SearchContext($this->class, $fields, $filters);
    }

    /**
     * get some random products to fill a controller every now and then
     *
     * @param integer $amount        How many products should be returned?
     * @param boolean $masterProduct Should only master products be returned?
     *
     * @return PaginatedList PaginatedList of random products
     * 
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @since 19.08.2014
     */
    public static function getRandomProducts($amount = 4, $masterProduct = true) {
        if ($masterProduct) {
            return self::get('SilvercartProduct', "\"SilvercartMasterProductID\" = '0'", "RAND()", null, $amount);
        } else {
            return self::get('SilvercartProduct', null, "RAND()", null, $amount);
        }
    }

    /**
     * get all required attributes as an array.
     *
     * @return array the attributes required to display an product in the frontend
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    public static function getRequiredAttributes() {
        return self::$requiredAttributes;
    }

    /**
     * define all attributes that must be filled out to show products in the frontend.
     *
     * @param string $concatinatedAttributesString a string with all attribute names, seperated by comma, with or without whitespaces
     *
     * @return void
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.12.2012
     */
    public static function setRequiredAttributes($concatinatedAttributesString) {
        $requiredAttributesArray = explode(",", str_replace(" ", "", $concatinatedAttributesString));
        foreach ($requiredAttributesArray as $attribute) {
            self::addRequiredAttribute($attribute);
        }
    }

    /**
     * Adds an attribute to the required attributes
     *
     * @param string $attribute The attribute to add
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.12.2012
     */
    public static function addRequiredAttribute($attribute) {
        if (!in_array($attribute, self::$blacklistedRequiredAttributes) &&
            !in_array($attribute, self::$requiredAttributes)) {
            self::$requiredAttributes[] = $attribute;
        }
    }

    /**
     * Blacklists a required attribute.
     *
     * @param string $attributeName The name of the attribute to blacklist
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function blacklistRequiredAttribute($attributeName) {
        if (!in_array($attributeName, self::$blacklistedRequiredAttributes)) {
            self::$blacklistedRequiredAttributes[] = $attributeName;
        }
    }

    /**
     * Removes an attribute from the required attributes list.
     *
     * @param string $attributeName The name of the attribute to remove
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function removeRequiredAttribute($attributeName) {
        if (in_array($attributeName, self::$requiredAttributes)) {
            self::$requiredAttributes = array_diff($attributeName, array_slice(self::$requiredAttributes, 0));
        }
    }

    /**
     * Resets the required attributes list.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.01.2013
     */
    public static function resetRequiredAttributes() {
        self::$requiredAttributes = array();
    }

    /**
     * Remove chars from the title that are not appropriate for an url
     *
     * @return string sanitized product title
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    private function title2urlSegment() {
        return SilvercartTools::string2urlSegment($this->Title);
    }

    /**
     * Returns an addToCartForm as HTML string.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2013
     */
    public function productAddCartForm() {
        $controller = Controller::curr();

        if ($this->addCartFormIdentifier !== null) {
            $addCartFormIdentifier = $this->addCartFormIdentifier;
        } else {
            $addCartFormIdentifier = $this->ID;
        }

        if ($this->addCartFormName !== null) {
            $addCartFormName = $this->addCartFormName;
        } else {
            $addCartFormName = 'ProductAddCartForm';

            if (method_exists($controller, 'isProductDetailView') &&
                $controller->isProductDetailView() &&
                $controller->getDetailViewProduct()->ID == $this->ID) {

                $addCartFormName = 'SilvercartProductAddCartFormDetail';
            }
        }

        $formIdentifier = $addCartFormName.$addCartFormIdentifier;
        $this->registerProductAddCartForm($addCartFormName, $formIdentifier);
        
        return Controller::curr()->InsertCustomHtmlForm(
            $formIdentifier,
            array(
                $this
            )
        );
    }
    
    /**
     * Registers a CustomHtmlForm with the given name, identifier and controller.
     * 
     * @param string     $addCartFormName Name of the form
     * @param string     $formIdentifier  Identifier of the form
     * @param Controller $controller      Controller of the form
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2013
     */
    public function registerProductAddCartForm($addCartFormName, $formIdentifier, $controller = null) {
        if ($addCartFormName == 'ProductAddCartForm') {
            $addCartFormName = 'SilvercartProductAddCartForm';
        }
        if (is_null($controller)) {
            $controller = Controller::curr();
        }
        if ($controller->getRegisteredCustomHtmlForm($formIdentifier) == false) {
            $controller->registerCustomHtmlForm(
                    $formIdentifier,
                    new $addCartFormName(
                        $controller,
                        array(
                            'productID' => $this->ID,
                        )
                    )
            );
        }
    }

    /**
     * adds an product to the cart or increases its amount
     * If stock managament is activated:
     * -If the product's stock quantity is overbookable there are noc hanges in
     *  behaviour.
     * -If the stock quantity of a product is NOT overbookable and the $quantity
     *  is larger than the stock quantity $quantity will be set to stock quantity.
     * -If the stock quantity of a product is NOT overbookable and the products
     *  stock quantity is less than zero false will be returned.
     *
     * @param int     $cartID    ID of the users shopping cart
     * @param float   $quantity  Amount of products to be added
     * @param boolean $increment Set to true to increment the quantity instead
     *                           of setting it absolutely
     *
     * @return mixed SilvercartShoppingCartPosition|boolean false
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.06.2014
     */
    public function addToCart($cartID, $quantity = 1, $increment = false) {
        $addToCartAllowed = true;

        $this->extend('updateAddToCart', $addToCartAllowed);
        
        if ($this->IsNotBuyable) {
            return false;
        }

        if ($quantity == 0 || $cartID == 0) {
            return false;
        }

        if (!$addToCartAllowed) {
            return false;
        }
        
        if (!$this->isBuyableDueToStockManagementSettings()) {
            return false;
        }

        $shoppingCartPosition = SilvercartShoppingCartPosition::get()->filter(array(
            'SilvercartProductID' => $this->ID,
            'SilvercartShoppingCartID' => $cartID,
        ))->first();

        if (!$shoppingCartPosition) {
            $shoppingCartPosition = new SilvercartShoppingCartPosition();

            $shoppingCartPosition->castedUpdate(
                array(
                    'SilvercartShoppingCartID' => $cartID,
                    'SilvercartProductID' => $this->ID
                )
            );
            $shoppingCartPosition->write();
            $shoppingCartPosition = SilvercartShoppingCartPosition::get()->filter(array(
                'SilvercartProductID' => $this->ID,
                'SilvercartShoppingCartID' => $cartID,
            ))->first();
        }
        
        $positionNotice = null;
        if ($shoppingCartPosition->Quantity < $quantity) {
            $quantityToAdd = $quantity - $shoppingCartPosition->Quantity;
            if ($shoppingCartPosition->isQuantityIncrementableBy($quantityToAdd)) {
                if ($quantity > SilvercartConfig::addToCartMaxQuantity()) {
                    $shoppingCartPosition->Quantity = SilvercartConfig::addToCartMaxQuantity();
                    $positionNotice = 'maxQuantityReached';
                } else {
                    $shoppingCartPosition->Quantity = $quantity;
                }
            } elseif ($this->StockQuantity > 0) {
                if ($shoppingCartPosition->Quantity + $this->StockQuantity > SilvercartConfig::addToCartMaxQuantity()) {
                    $shoppingCartPosition->Quantity = SilvercartConfig::addToCartMaxQuantity();
                    $positionNotice = 'maxQuantityReached';
                } else {
                    $shoppingCartPosition->Quantity = $this->StockQuantity;
                    $positionNotice = 'remaining';
                }
            } else {
                $shoppingCartPosition = false;
            }
        } else {
            if ($increment) {
                $shoppingCartPosition->Quantity += $quantity;
            } else {
                $shoppingCartPosition->Quantity = $quantity;
            }
        }

        if ($shoppingCartPosition instanceof SilvercartShoppingCartPosition) {
            $shoppingCartPosition->write();
            if (!is_null($positionNotice)) {
                SilvercartShoppingCartPositionNotice::setNotice($shoppingCartPosition->ID, $positionNotice);
            }
            SilvercartPlugin::call($this, 'onAfterAddToCart', array($shoppingCartPosition));
        }
        $this->extend('onAfterAddToCart', $shoppingCartPosition);

        return $shoppingCartPosition;
    }
    
    /**
     * Checks whether the product is inside the cart with the given ID
     * 
     * @param int $cartID Cart ID to check positions for
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.03.2013
     */
    public function isInCart($cartID = null) {
        $isInCart = false;
        if ($this->getPositionInCart($cartID) instanceof SilvercartShoppingCartPosition) {
            $isInCart = true;
        }
        $this->extend('updateIsInCart', $isInCart);
        return $isInCart;
    }
    
    /**
     * Returns the position of the product in cart
     * 
     * @param int $cartID Cart ID to check positions for
     * 
     * @return SilvercartShoppingCartPosition
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function getPositionInCart($cartID = null) {
        if (is_null($cartID) &&
            SilvercartCustomer::currentUser() instanceof Member) {
            $cartID = SilvercartCustomer::currentUser()->getCart()->ID;
        }
        if (!array_key_exists($cartID, $this->positionInCart)) {
            $this->positionInCart[$cartID] = SilvercartShoppingCartPosition::get()->filter(array(
                'SilvercartProductID' => $this->ID,
                'SilvercartShoppingCartID' => $cartID,
            ))->first();
            $this->extend('updatePositionInCart', $this->positionInCart[$cartID]);
        }
        return $this->positionInCart[$cartID];
    }
    
    /**
     * Returns the quantity of the product in cart
     * 
     * @param int $cartID Cart ID to check positions for
     * 
     * @return float
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.03.2013
     */
    public function getQuantityInCart($cartID = null) {
        if (!array_key_exists($cartID, $this->quantityInCart)) {
            $quantityInCart = 0;
            $position       = $this->getPositionInCart($cartID);
            $precision      = 0;
            if ($this->SilvercartQuantityUnit()->isInDb()) {
                $precision = $this->SilvercartQuantityUnit()->numberOfDecimalPlaces;
            }
            if ($position instanceof SilvercartShoppingCartPosition) {
                $quantityInCart = round($position->Quantity, $precision);
            }
            $this->quantityInCart[$cartID] = $quantityInCart;
            $this->extend('updateQuantityInCart', $this->quantityInCart[$cartID]);
        }
        return $this->quantityInCart[$cartID];
    }
    
    /**
     * Returns the quantity of the product in cart as a human readable string.
     * 
     * @param int $cartID Cart ID to check positions for
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2014
     */
    public function getQuantityInCartString($cartID = null) {
        if (is_null($cartID) &&
            SilvercartCustomer::currentUser() instanceof Member) {
            $cartID = SilvercartCustomer::currentUser()->getCart()->ID;
        }
        if (!array_key_exists($cartID, $this->quantityInCartString)) {
            $quantityInCartString = '';
            if ($this->isInCart($cartID)) {
                $quantityInCartString = sprintf(
                        _t('SilvercartProduct.QUANTITY_IS_IN_CART'),
                        $this->getQuantityInCart($cartID),
                        $this->SilvercartQuantityUnit()->Title
                );
            }
            $this->quantityInCartString[$cartID] = $quantityInCartString;
            $this->extend('updateQuantityInCartString', $this->quantityInCartString[$cartID]);
        }
        return $this->quantityInCartString[$cartID];
    }

    /**
     * Returns the product group of this product dependant on the current locale
     *
     * @return SilvercartProductGroupPage
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.12.2015
     */
    public function SilvercartProductGroup() {
        $silvercartProductGroup = null;
        $currentLocale          = Translatable::get_current_locale();
        if ($this->getComponent('SilvercartProductGroup')) {
            $silvercartProductGroup = $this->getComponent('SilvercartProductGroup');
            if ($silvercartProductGroup->Locale != $currentLocale &&
                $silvercartProductGroup->hasTranslation($currentLocale)) {
                $silvercartProductGroup = $silvercartProductGroup->getTranslation($currentLocale);
            }
        }
        return $silvercartProductGroup;
    }
    
    /**
     * Builds the product link with the given parameters.
     * 
     * @param ViewableData $controller Base object to build the link.
     * @param string       $urlSegment URL segment.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.03.2015
     */
    public function buildLink($controller, $urlSegment) {
        $link = '';
        $linkIdentifier = $this->ID;
        $this->extend('updatelinkIdentifier', $linkIdentifier);
        
        $link = $controller->OriginalLink() . $linkIdentifier . '/' . $urlSegment;
        return $link;
    }
    
    /**
     * Alias for Link()
     *
     * @return string
     */
    public function getLink() {
        return $this->Link();
    }

    /**
     * Link to this product.
     * The link is in context of the current controller. If the current 
     * controller does not match some related product criteria (mirrored product 
     * group, translation of a mirrored product group or translation of main
     * group) the main group will be used as context.
     *
     * @return string URL of $this
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Ramon Kupper <rkupper@pixeltricks.de>
     * @since 03.03.2015
     */
    public function Link() {
        $link = '';
        
        if (Controller::curr() instanceof SilvercartProductGroupPage_Controller &&
            !Controller::curr() instanceof SilvercartSearchResultsPage_Controller &&
            $this->SilvercartProductGroupMirrorPages()->find('ID', Controller::curr()->data()->ID)) {
            $link = $this->buildLink(Controller::curr(), $this->title2urlSegment());
        } elseif (Controller::curr() instanceof SilvercartProductGroupPage_Controller && 
                  Translatable::get_current_locale() != SilvercartConfig::DefaultLanguage()) {
            Translatable::disable_locale_filter();
            if ($this->SilvercartProductGroupMirrorPages()->find('ID', Controller::curr()->getTranslation(SilvercartConfig::DefaultLanguage())->ID)) {
                $link = $this->buildLink(Controller::curr(), $this->title2urlSegment());
            }
            Translatable::enable_locale_filter();
        }
        if (empty($link) &&
            $this->SilvercartProductGroup()) {
            $link = $this->buildLink($this->SilvercartProductGroup(), $this->title2urlSegment());
        }
        
        return $link;
    }

    /**
     * Canonical link to the controller, that shows this product.
     * Any product has an unique URL
     *
     * @return string URL of $this
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.06.2017
     */
    public function CanonicalLink() {
        $link = $this->Link();
        if ($this->SilvercartProductGroup()) {
            $link = $this->buildLink($this->SilvercartProductGroup(), $this->title2urlSegment());
        }
        return $link;
    }
    
    /**
     * Alias for AbsoluteLink()
     *
     * @return string
     */
    public function getAbsoluteLink() {
        return $this->AbsoluteLink();
    }

    /**
     * Returns the link to this product with protocol and domain
     *
     * @return string the absolute link to this product
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 6.6.2011
     */
    public function AbsoluteLink() {
        return Director::absoluteURL($this->Link());
    }
    
    /**
     * Returns the link to send a product question to the shop manager
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.05.2012
     */
    public function ProductQuestionLink() {
        return SilvercartTools::PageByIdentifierCodeLink('SilvercartContactFormPage') . 'productQuestion/' . $this->ID;
    }
    
    /**
     * Returns whether the current view is a mirrored product detail view
     *
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.07.2012
     */
    public function IsMirroredView() {
        $isMirroredView = true;

        if (Controller::curr() instanceof SilvercartProductGroupPage_Controller &&
            !Controller::curr() instanceof SilvercartSearchResultsPage_Controller &&
            $this->SilvercartProductGroupID == Controller::curr()->data()->ID) {
            $isMirroredView = false;
        }
        
        return $isMirroredView;
    }

    /**
     * returns the tax amount included in $this
     *
     * @return float
     */
    public function getTaxAmount() {
        $showPricesGross = false;
        $member          = SilvercartCustomer::currentUser();

        if ($member) {
            if ($member->showPricesGross(true)) {
                $showPricesGross = true;
            }
        } else {
            $defaultPriceType = SilvercartConfig::DefaultPriceType();

            if ($defaultPriceType == 'gross') {
                $showPricesGross = true;
            }
        }

        if ($showPricesGross) {
            $taxRate = $this->getPrice()->getAmount() - ($this->getPrice()->getAmount() / (100 + $this->getTaxRate()) * 100);
        } else {
            $taxRate = $this->getPrice()->getAmount() * ($this->getTaxRate() / 100);
        }
        return $taxRate;
    }

    /**
     * return the tax amount nice with only 2 decimal places and replaced . in ,
     * includes currency symbol from current locale
     *
     * @return string
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 02.09.2011
     */
    public function getTaxAmountNice() {
        return str_replace('.', ',', number_format($this->getTaxAmount(),2)) . ' ' . $this->Price->getSymbol();
    }
    
    /**
     * Creates the product micro data as a JSON string to use for SEO.
     * 
     * @param bool $plain Set to true to get the plain JSON string without HTML tag
     * 
     * @return string
     */
    public function getMicrodata($plain = false) {
        if ($this->getPriceIsLowerThanMsr()) {
            $offers = array(
                '@type'         => 'AggregateOffer',
                'highPrice'     => number_format($this->MSRPrice->getAmount(), 2, '.', ''),
                'lowPrice'      => number_format($this->getPrice()->getAmount(), 2, '.', ''),
                'priceCurrency' => $this->getPrice()->getCurrency(),
            );
        } else {
            $offers = array(
                '@type'         => 'Offer',
                'price'         => number_format($this->getPrice()->getAmount(), 2, '.', ''),
                'priceCurrency' => $this->getPrice()->getCurrency(),
            );
        }
        if ($this->SilvercartAvailabilityStatus()->exists()) {
            $offers['availability'] = $this->SilvercartAvailabilityStatus()->MicrodataCode;
        }
        if ($this->SilvercartProductCondition()->exists()) {
            $offers['itemCondition'] = $this->SilvercartProductCondition()->MicrodataCode;
        }
        
        $imageURL  = '';
        $listImage = $this->getListImage();
        if ($listImage instanceof Image) {
            $imageURL = $listImage->getAbsoluteURL();
        }
        
        $jsonData = array(
            '@context'    => 'http://schema.org',
            '@type'       => 'Product',
            'sku'         => $this->ProductNumberShop,
            'mpn'         => $this->ProductNumberShop,
            'name'        => htmlentities(strip_tags($this->Title)),
            'description' => htmlentities(strip_tags($this->getLongDescription())),
            'url'         => $this->AbsoluteLink(),
            'image'       => $imageURL,
            'offers'      => $offers,
        );
        
        if ($this->EANCode) {
            $jsonData["gtin"] = $this->EANCode;
        }

        $manufacturer = $this->SilvercartManufacturer();
        if ($manufacturer instanceof SilvercartManufacturer &&
            $manufacturer->exists()) {
            $manufacturerData = array(
                '@type' => 'Thing',
                'name'  => $manufacturer->Title,
            );
            $jsonData["brand"] = $manufacturerData;
        }
        
        $this->extend('updateMicrodata', $jsonData);

        if (defined('JSON_PRETTY_PRINT')) {
            $output = json_encode($jsonData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        } else {
            $output = json_encode($jsonData);
        }
        if (!$plain) {
            $output = '<script type="application/ld+json">' . $output . '</script>';
        }
        return $output;
    }
    
    /**
     * Returns the purchase min duration in business days.
     * 
     * @return int
     */
    public function getPurchaseMinDurationDays() {
        $days = 0;
        if (!empty($this->PurchaseTimeUnit) &&
            !empty($this->PurchaseMinDuration)) {
            $days = (int) $this->PurchaseMinDuration * $this->getPurchaseTimeUnitBusinessDays();
        }
        return $days;
    }
    
    /**
     * Returns the purchase min duration in business days.
     * 
     * @return int
     */
    public function getPurchaseMaxDurationDays() {
        $days = 0;
        if (!empty($this->PurchaseTimeUnit) &&
            !empty($this->PurchaseMaxDuration)) {
            $days = (int) $this->PurchaseMaxDuration * $this->getPurchaseTimeUnitBusinessDays();
        }
        return $days;
    }
    
    /**
     * Returns the count of business days for the related purchase time unit.
     * 
     * @return int
     */
    public function getPurchaseTimeUnitBusinessDays() {
        switch ($this->PurchaseTimeUnit) {
            case 'Months':
                $timeUnitDays = 24;
                break;
            case 'Weeks':
                $timeUnitDays = 6;
                break;
            case 'Days':
            default:
                $timeUnitDays = 1;
                break;
        }
        return $timeUnitDays;
    }

    /**
     * Returns a HTML snippet to display the availability of the product.
     *
     * @param string $baseCssClass         Base CSS class to use to render the badge (default: label)
     * @param string $additionalCssClasses Additional CSS classes to use to render the badge.
     * 
     * @return string
     */
    public function getAvailability($baseCssClass = 'label', $additionalCssClasses = '') {
        if ($this->SilvercartAvailabilityStatus()) {
            if ($this->SilvercartAvailabilityStatus()->Code == 'not-available'
             && !empty($this->PurchaseTimeUnit)
             && (!empty($this->PurchaseMinDuration)
              || !empty($this->PurchaseMaxDuration))) {
                $class = 'available-in ' . $baseCssClass . ' ' . $baseCssClass . '-warning';
                if (empty($this->PurchaseMinDuration)) {
                    $title = sprintf(_t('SilvercartAvailabilityStatus.STATUS_AVAILABLE_IN'), $this->PurchaseMaxDuration, _t('Silvercart.' . strtoupper($this->PurchaseTimeUnit)));
                } elseif (empty($this->PurchaseMaxDuration)) {
                    $title = sprintf(_t('SilvercartAvailabilityStatus.STATUS_AVAILABLE_IN'), $this->PurchaseMinDuration, _t('Silvercart.' . strtoupper($this->PurchaseTimeUnit)));
                } else {
                    $title = sprintf(_t('SilvercartAvailabilityStatus.STATUS_AVAILABLE_IN_MIN_MAX'), $this->PurchaseMinDuration, $this->PurchaseMaxDuration, _t('Silvercart.' . strtoupper($this->PurchaseTimeUnit)));
                }
            } else {
                $class = $this->SilvercartAvailabilityStatus()->Code . ' ' . $baseCssClass . ' ' . $baseCssClass . '-'.$this->SilvercartAvailabilityStatus()->badgeColor;
                $title = $this->SilvercartAvailabilityStatus()->Title;
            }
            $html = '<span class="' . $class . ' ' . $additionalCssClasses . '" title="' . $title . '">' . $title . '</span>';
        } else {
            $html = '';
        }
        return $html;
    }

    /**
     * Returns a HTML snippet to display the availability of the product.
     * Alias for self::getAvailability().
     *
     * @param string $baseCssClass         Base CSS class to use to render the badge (default: label)
     * @param string $additionalCssClasses Additional CSS classes to use to render the badge.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.06.2017
     */
    public function Availability($baseCssClass = 'label', $additionalCssClasses = '') {
        return $this->getAvailability($baseCssClass, $additionalCssClasses);
    }

    /**
     * Returns the related SilvercartAvailabilityStatus object.
     * Provides an extension hook to update the status object by decorator.
     * 
     * @return SilvercartAvailabilityStatus
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2014
     */
    public function SilvercartAvailabilityStatus() {
        if (is_null($this->cachedSilvercartAvailabilityStatus)) {
            $this->cachedSilvercartAvailabilityStatus = $this->getComponent('SilvercartAvailabilityStatus');
            $this->extend('updateSilvercartAvailabilityStatus', $this->cachedSilvercartAvailabilityStatus);
            if (!$this->cachedSilvercartAvailabilityStatus instanceof SilvercartAvailabilityStatus ||
                !$this->cachedSilvercartAvailabilityStatus->exists()) {
                $default = SilvercartAvailabilityStatus::getDefault();
                if ($default instanceof SilvercartAvailabilityStatus &&
                    $default->exists()) {
                    $this->cachedSilvercartAvailabilityStatus = $default;
                }
            }
        }
        return $this->cachedSilvercartAvailabilityStatus;
    }

    /**
     * Indicates wether the availability information should be shown. If
     * there's no status attributed we don't want to show it.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.08.2011
     */
    public function showAvailability() {
        $showAvailability = false;

        if ($this->SilvercartAvailabilityStatusID > 0) {
            $showAvailability = true;
        }

        return $showAvailability;
    }

    /**
     * used to determine weather something should be shown on a template or not
     *
     * @return bool
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.3.2011
     */
    public function showPricesGross() {
        $pricetype = SilvercartConfig::Pricetype();
        if ($pricetype == "gross") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the tax rate in percent. The attribute 'Rate' of the relation
     * 'SilvercartTax' is not used to handle with complex tax systems without
     * clearly defined product taxes.
     * 
     * @param bool $ignoreTaxExemption Determines whether to ignore tax exemption or not.
     *
     * @return float the tax rate in percent
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.07.2013
     */
    public function getTaxRate($ignoreTaxExemption = false) {
        if ($this->ignoreTaxExemption) {
            $ignoreTaxExemption = $this->ignoreTaxExemption;
        }
        return $this->SilvercartTax()->getTaxRate($ignoreTaxExemption);
    }

    /**
     * Returns the related SilvercartTax object.
     * Provides an extension hook to update the tax object by decorator.
     * 
     * @return SilvercartTax
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.01.2013
     */
    public function SilvercartTax() {
        if (is_null($this->cachedSilvercartTax)) {
            $this->cachedSilvercartTax = $this->getComponent('SilvercartTax');
            if (!$this->getCMSFieldsIsCalled) {
                $this->extend('updateTax', $this->cachedSilvercartTax);
                $this->extend('updateSilvercartTax', $this->cachedSilvercartTax);
            }
        }
        return $this->cachedSilvercartTax;
    }

    /**
     * Returns the related WidgetArea object.
     * If there is no WidgetArea related, a new one will be created and related.
     * 
     * @return WidgetArea
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.03.2014
     */
    public function WidgetArea() {
        $widgetArea = $this->addWidgetAreaIfNotExists($this->getComponent('WidgetArea'));
        return $widgetArea;
    }

    /**
     * Deletes all related WidgetAreas and Widgets before deletion.
     * Deletes all related shopping cart positions before deletion.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2013
     */
    public function onBeforeDelete() {
        parent::onBeforeDelete();
        if ($this->WidgetArea()->exists()) {
            foreach ($this->WidgetArea()->Widgets() as $widget) {
                $widget->delete();
            }

            $this->WidgetArea()->delete();
        }
        
        foreach ($this->SilvercartShoppingCartPositions() as $position) {
            $position->delete();
        }
        $this->extend('updateOnBeforeDelete');
    }

    /**
     * - Adds some extended i18n handling for mirrored product groups.
     * - Changes the availability status if necessary.
     * - Deletes shopping cart positions when changing to inactive.
     * - Marks the product for cache refreshing if necessary.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.02.2016
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        
        if ($this->SilvercartProductGroup()) {
            $translations = $this->SilvercartProductGroup()->getTranslations();
            if ($translations) {
                foreach ($translations as $translation) {
                    if ($this->SilvercartProductGroupMirrorPages()->find('ID', $translation->ID)) {
                        continue;
                    }
                    $this->SilvercartProductGroupMirrorPages()->add($translation);
                }
            }
        }
        
        if (array_key_exists('StockQuantity', $this->original)) {
            $stockQuantityBefore = $this->original['StockQuantity'];
            $this->checkForAvailabilityStatusChange($stockQuantityBefore, false);
        }
        
        if (!$this->isActive) {
            foreach ($this->SilvercartShoppingCartPositions() as $position) {
                $position->delete();
            }
        }
        
        if ($this->SilvercartTaxID == 0) {
            $defaultTaxRate = SilvercartTax::getDefault();
            if ($defaultTaxRate instanceof SilvercartTax &&
                $defaultTaxRate->exists()) {
                $this->SilvercartTaxID = $defaultTaxRate->ID;
            }
        }
        
        $priceGrossCurrency = $this->PriceGross->getCurrency();
        if (is_null($this->PriceGrossCurrency) &&
            is_null($priceGrossCurrency)) {
            $this->PriceGrossCurrency = SilvercartConfig::DefaultCurrency();
        }
        $priceNetCurrency = $this->PriceNet->getCurrency();
        if (is_null($this->PriceNetCurrency) &&
            is_null($priceNetCurrency)) {
            $this->PriceNetCurrency = SilvercartConfig::DefaultCurrency();
        }
        $MSRPriceCurrency = $this->MSRPrice->getCurrency();
        if (is_null($this->MSRPriceCurrency) &&
            is_null($MSRPriceCurrency)) {
            $this->MSRPriceCurrency = SilvercartConfig::DefaultCurrency();
        }
        $purchasePriceCurrency = $this->PurchasePrice->getCurrency();
        if (is_null($this->PurchasePriceCurrency) &&
            is_null($purchasePriceCurrency)) {
            $this->PurchasePriceCurrency = SilvercartConfig::DefaultCurrency();
        }
        
        if (array_key_exists('RefreshCache', $_POST) &&
            ($_POST['RefreshCache'] == '1' ||
             $_POST['RefreshCache'] == 'on')) {
            $this->markForCacheRefresh();
        }
    }
    
    /**
     * Extension to add (mirrored) product groups to the cache refesh marker.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.10.2017
     */
    public function extendMarkForCacheRefresh() {
        if ($this->SilvercartProductGroup()->exists()) {
            $this->SilvercartProductGroup()->updateLastEditedForCache();
        }
        foreach ($this->SilvercartProductGroupMirrorPages() as $productGroup) {
            $productGroup->updateLastEditedForCache();
        }
    }

    /**
     * We make this method extendable here.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.11.2011
     */
    public function onAfterDelete() {
        parent::onAfterDelete();

        $this->extend('updateOnAfterDelete');
    }

    /**
     * Adds a widget area if not done yet.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.02.2016
     */
    public function onAfterWrite() {
        parent::onAfterWrite();
        
        $this->addWidgetAreaIfNotExists();
    }
    
    /**
     * Adds a new WidgetArea to the product if not existing yet.
     * 
     * @param WidgetArea $widgetArea Optional WidgetArea to use as fallback
     * 
     * @return WidgetArea
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.03.2014
     */
    public function addWidgetAreaIfNotExists($widgetArea = null) {
        if ($this->WidgetAreaID == 0 ||
            ($widgetArea instanceof WidgetArea &&
             !$widgetArea->exists())) {
            $widgetArea = new WidgetArea();
            $widgetArea->write();
            
            $this->WidgetAreaID = $widgetArea->ID;
            $this->write();
        }
        return $widgetArea;
    }

    /**
     * Sets the cache relevant fields.
     * 
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.02.2013
     */
    public function getCacheRelevantFields() {
        $cacheRelevantFields = array(
            'isActive',
            'ProductNumberShop',
            'ProductNumberManufacturer',
            'EANCode',
            'PriceGrossAmount',
            'PriceNetAmount',
            'MSRPriceAmount',
            'PurchaseMinDuration',
            'PurchaseMaxDuration',
            'PurchaseTimeUnit',
            'PackagingQuantity',
            'StockQuantity'     => 0,
            
            'SilvercartTaxID',
            'SilvercartManufacturerID',
            'SilvercartProductGroupID',
            'SilvercartAvailabilityStatusID',
            'SilvercartProductConditionID',
            'SilvercartQuantityUnitID',
        );
        $this->extend('updateCacheRelevantFields', $cacheRelevantFields);
        return $cacheRelevantFields;
    }

    /**
     * Returns the url to a placeholder image.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.04.2011
     */
    public function NoImage() {
        $image = 'silvercart/img/noimage.png';

        $this->extend('updateNoImage', $image);

        return $image;
    }

    /**
     * Returns the url to a placeholder thumbnail image.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.04.2011
     */
    public function NoImageSmall() {
        $image = 'silvercart/img/noimage.png';

        $this->extend('updateNoImageSmall', $image);

        return $image;
    }

    /**
     * Returns a ArrayList of attributed images. If there are no images
     * attributed the method checks if there's a standard no-image
     * visualitation defined in SilvercartConfig and returns the defined image
     * as ArrayList. As last resort boolean false is returned.
     *
     * @param string $filter An optional sql filter statement
     *
     * @return SS_List
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>,
     *         Ramon Kupper <rkupper@pixeltricks.de>
     * @since 19.05.2018
     */
    public function getSilvercartImages($filter = '') {
        if (get_class(Controller::curr()) == 'CMSPageEditController' &&
            Controller::curr()->getRequest()->param('Action') == 'view') {
            // workaround to fix a readonly field bug when trying to open a
            // product in view mode  (action 'view' of GridFieldDetailForm_ItemRequest)
            return;
        }
        if (!array_key_exists($filter, $this->images)) {
            $images = false;
            $this->extend('overwriteSilvercartImages', $images);
            if ($images == false) {
                $images = $this->SilvercartImages($filter);
                
                $this->extend('updateGetSilvercartImages', $images);

                if ($images->count() > 0) {
                    $existingImages = new ArrayList();
                    foreach ($images as $image) {
                        if (!file_exists($image->Image()->getFullPath())) {
                            $noImageObj = SilvercartConfig::getNoImage();

                            if ($noImageObj) {
                                $image = new SilvercartImage();
                                $image->ImageID             = $noImageObj->ID;
                                $image->SilvercartProductID = $this->ID;
                            }
                        }
                        $existingImages->push($image);
                    }
                    $images = $existingImages;
                }
            }
            if (!($images instanceof ArrayList) ||
                $images->count() == 0) {
               
                $noImageObj = SilvercartConfig::getNoImage();
                if ($noImageObj->exists()) {
                    $image = SilvercartImage::get()->filter('ImageID', $noImageObj->ID)->first();
                    if (!($image instanceof SilvercartImage) ||
                        !$image->exists()) {
                        
                        $image = new SilvercartImage();
                        $image->ImageID = $noImageObj->ID;
                        $image->write();
                    }
                    $images = new ArrayList();
                    $images->push($image);
                }
            }
            
            $this->images[$filter] = $images;
        }

        return $this->images[$filter];
    }

    /**
     * Returns $this->getSilvercartImages() without the first image.
     *
     * @param string $filter An optional sql filter statement
     *
     * @return DataList|ArrayList|false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since  2013-05-21
     */
    public function getSilvercartThumbnails($filter = '') {
        $images = $this->getSilvercartImages($filter);

        if ($images) {
            $images->shift();
        }
        
        return $images;
    }
    
    /**
     * Returns the list image as a thumbnail Image.
     * 
     * @return Image
     */
    public function getListImageThumbnail() {
        $thumb = false;
        $file = $this->getListImage();
        if (is_object($file)) {
            $thumb = $file->ImageThumbnail(60,30);
        }
        return $thumb;
    }

    /**
     * Returns the first image out of the related SilvercartImages.
     * 
     * @param string $filter Filter for the related SilvercartImages
     * 
     * @return Image
     */
    public function getListImage($filter = '') {
        if (is_null($this->listImage)) {
            $this->listImage = false;
            $images = $this->getSilvercartImages($filter);

            
            if ($images->count() > 0) {
                $this->listImage = $images->First()->Image();
            }
        }

        return $this->listImage;
    }

    /**
     * Increments or decrements the products stock quantity.
     * By default the quantity will be incremented.
     *
     * @param int  $quantity  The amount to subtract from the current stock quantity
     * @param bool $increment Set to false to decrement quantity.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.06.2014
     */
    public function changeStockQuantityBy($quantity, $increment = true) {
        if ($increment) {
            $operator = '+';
        } else {
            $operator = '-';
        }
        DB::query("LOCK TABLES SilvercartProduct WRITE");
        DB::query(
                sprintf(
                        "UPDATE SilvercartProduct SET StockQuantity=StockQuantity%s%s WHERE ID = '%s'",
                        $operator,
                        $quantity,
                        $this->ID
                )
        );
        $results = DB::query(
                sprintf(
                        "SELECT StockQuantity FROM SilvercartProduct WHERE ID = '%s'",
                        $this->ID
                )
        );
        DB::query("UNLOCK TABLES");
        
        $firstRow = $results->first();
        $stockQuantityBefore = $this->StockQuantity;
        $this->StockQuantity = $firstRow['StockQuantity'];
        $this->checkForAvailabilityStatusChange($stockQuantityBefore);
    }

    /**
     * decrements the products stock quantity of this product
     *
     * @param integer $quantity the amount to subtract from the current stock quantity
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.05.2014
     */
    public function decrementStockQuantity($quantity) {
        $this->changeStockQuantityBy($quantity, false);
    }

    /**
     * increments the products stock quantity of this product
     *
     * @param integer $quantity the amount to add to the current stock quantity
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.05.2014
     */
    public function incrementStockQuantity($quantity) {
        $this->changeStockQuantityBy($quantity);
    }

    /**
     * Is this products stock quantity overbookable?
     * If this product does not have overbookablility set the general setting of
     * the config object is choosen.
     * If stock management is deactivated true will be returned.
     *
     * @return boolean is the product overbookable?
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.11.2012
     */
    public function isStockQuantityOverbookable() {
        if (is_null($this->isStockQuantityOverbookable)) {
            $overbookable = true;
            if (SilvercartConfig::EnableStockManagement()) {
                if (!SilvercartConfig::isStockManagementOverbookable() &&
                    !$this->StockQuantityOverbookable) {

                    $overbookable = false;
                }
            }
            $this->isStockQuantityOverbookable = $overbookable;
        }
        return $this->isStockQuantityOverbookable;
    }

    /**
     * Is this product buyable with the given stock management settings?
     * If Stock management is deactivated true is returned.
     * If stock management is activated but the quantity is overbookable true is
     * returned.
     *
     * @return boolean Can this product be bought due to stock management
     *                 settings and the customers cart?
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 15.11.2014
     */
    public function isBuyableDueToStockManagementSettings() {
        //is the product already in the cart?
        $cartPositionQuantity = 0;
        if (SilvercartCustomer::currentUser() && SilvercartCustomer::currentUser()->getCart()) {
            $cartPositionQuantity = SilvercartCustomer::currentUser()->getCart()->getQuantity($this->ID);
        }
        if (SilvercartConfig::EnableStockManagement()) {
            if (!$this->isStockQuantityOverbookable() &&
                ($this->StockQuantity - $cartPositionQuantity) <= 0) {

                return false;
            }

            if ($this->StockQuantityExpirationDate) {
                $curDate        = new DateTime();
                $expirationDate = new DateTime(strftime($this->StockQuantityExpirationDate));

                if ( $this->isStockQuantityOverbookable() &&
                    ($this->StockQuantity - $cartPositionQuantity) <= 0 &&
                     $expirationDate < $curDate) {

                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Returns if a product is new dependent on its creation date (Created) and the given
     * time difference ($timeDifference).
     * 
     * @param string $timeDifference '+8 month' OR '-8 day' OR '-1 year'
     *
     * @return bool
     *
     * @author Jiri Ripa <jripa@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.06.2017
     */
    public function isNewProduct($timeDifference = '+2 month') {
        if ($timeDifference == '+2 month') {
            $this->extend('updateIsNewProductTimeDifference', $timeDifference);
        }
        $isNew = false;
        $date  = new DateTime($this->Created);
        $date->format('Y/m/d');
        $date->modify($timeDifference);
        $modifiedDate = $date->getTimestamp();
        $currentDate  = time();
        if ($modifiedDate > $currentDate) {
            $isNew = true;
        }
        $this->extend('updateIsNewProduct', $isNew);
        return $isNew;
    }
    
    /**
     * Checks whether there is a status change needed and executes the change if 
     * needed.
     * 
     * @param int  $stockQuantityBefore Stock quantity before change.
     * @param bool $doWrite             Set to false to prevent a write.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.06.2014
     */
    protected function checkForAvailabilityStatusChange($stockQuantityBefore, $doWrite = true) {
        if ($this->StockQuantity <= 0 &&
            $stockQuantityBefore > 0) {
            // check for automatic negative availability status and set it.
            $newStatus = SilvercartAvailabilityStatus::get_negative_status();
            if ($newStatus instanceof SilvercartAvailabilityStatus) {
                $this->SilvercartAvailabilityStatusID = $newStatus->ID;
                if ($doWrite) {
                    $this->write();
                }
            }
        } elseif ($this->StockQuantity > 0 &&
            $stockQuantityBefore <= 0) {
            // check for automatic positive availability status and set it.
            $newStatus = SilvercartAvailabilityStatus::get_positive_status();
            if ($newStatus instanceof SilvercartAvailabilityStatus) {
                $this->SilvercartAvailabilityStatusID = $newStatus->ID;
                if ($doWrite) {
                    $this->write();
                }
            }
        }
    }

    /**
     * Returns a string of comma separated IDs of the attributed
     * SilvercartProductGroupMirror objects.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.08.2011
     */
    public function getSilvercartProductMirrorGroupIDs() {
        $idListArray = array();
        $idList      = '';

        if ($this->SilvercartProductGroupMirrorPages()) {
            foreach ($this->SilvercartProductGroupMirrorPages() as $silvercartProductGroupMirrorPage) {
                $idListArray[] = $silvercartProductGroupMirrorPage->ID;
                unset($silvercartProductGroupMirrorPage);
            }
        }

        if (!empty($idListArray)) {
            $idList = implode(',', $idListArray);
        }

        return $idList;
    }
    
    /**
     * Checks whether price is lower than MSR
     *
     * @return boolean 
     */
    public function getPriceIsLowerThanMsr() {
        $priceIsLowerThanMsr    = false;
        $price                  = $this->getPrice();
        $msr                    = $this->MSRPrice;
        if ($price->getAmount() < $msr->getAmount()) {
            $priceIsLowerThanMsr = true;
        }
        return $priceIsLowerThanMsr;
    }

    /**
     * returns all additional product tabs
     * 
     * @return ArrayList  
     */
    public function getPluggedInTabs() {
        if (is_null($this->pluggedInTabs)) {
            $this->pluggedInTabs = SilvercartPlugin::call($this, 'getPluggedInTabs', array(), false, 'ArrayList');
        }
        return $this->pluggedInTabs;
    }
    
    /**
     * returns all additional information about a product
     * 
     * @return ArrayList 
     */
    public function getPluggedInProductMetaData() {
        if (is_null($this->pluggedInProductMetaData)) {
            $this->pluggedInProductMetaData = SilvercartPlugin::call($this, 'getPluggedInProductMetaData', array(), false, 'ArrayList');
        }
        return $this->pluggedInProductMetaData;
    }
    
    /**
     * returns all additional list information about a product
     * 
     * @return ArrayList 
     */
    public function getPluggedInProductListAdditionalData() {
        if (is_null($this->pluggedInProductListAdditionalData)) {
            $this->pluggedInProductListAdditionalData = SilvercartPlugin::call($this, 'getPluggedInProductListAdditionalData', array(), false, 'ArrayList');
        }
        return $this->pluggedInProductListAdditionalData;
    }
    
    /**
     * Returns all additional information to display between Images and Content.
     * 
     * @return DataObjectSet 
     */
    public function getPluggedInAfterImageContent() {
        if (is_null($this->pluggedInAfterImageContent)) {
            $this->pluggedInAfterImageContent = SilvercartPlugin::call($this, 'getPluggedInAfterImageContent', array(), false, 'DataObjectSet');
        }
        return $this->pluggedInAfterImageContent;
    }
}

/**
 * Translations for a product
 *
 * @package Silvercart
 * @subpackage Translation
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 03.01.2012
 * @license see license file in modules root directory
 */
class SilvercartProductLanguage extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'Title'             => 'VarChar(255)',
        'ShortDescription'  => 'Text',
        'LongDescription'   => 'HTMLText',
        'MetaDescription'   => 'VarChar(255)',
        'MetaTitle'         => 'VarChar(64)', //search engines use only 64 chars
        'MetaKeywords'      => 'VarChar'
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartProduct' => 'SilvercartProduct'
    );
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.02.2013
     */
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'SilvercartProduct'
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);
        
        return $fields;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 05.01.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'Title'             => _t('SilvercartProduct.COLUMN_TITLE'),
                    'ShortDescription'  => _t('SilvercartProduct.SHORTDESCRIPTION'),
                    'LongDescription'   => _t('SilvercartProduct.DESCRIPTION'),
                    'MetaDescription'   => _t('SilvercartProduct.METADESCRIPTION'),
                    'MetaKeywords'      => _t('SilvercartProduct.METAKEYWORDS'),
                    'MetaTitle'         => _t('SilvercartProduct.METATITLE')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * columns for table overview
     *
     * @return array $summaryFields 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title'               => $this->fieldLabel('Title'),
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Sets the cache relevant fields.
     * 
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.02.2013
     */
    public function getCacheRelevantFields() {
        $cacheRelevantFields = array(
            'Title',
            'ShortDescription',
            'LongDescription',
            'MetaDescription',
            'MetaTitle',
            'MetaKeywords',
        );
        $this->extend('updateCacheRelevantFields', $cacheRelevantFields);
        return $cacheRelevantFields;
    }
}