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
 * @subpackage Products
 */

/**
 * abstract for a product
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sascha Koehler <skoehler@pixeltricks.de>, <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license see license file in modules root directory
 */
class SilvercartProduct extends DataObject {

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
        'Weight'                      => 'Int', //unit is gramm
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
        'isActive'          => '(isActive)',
        'PriceGrossAmount'  => '(PriceGrossAmount)',
        'PriceNetAmount'    => '(PriceNetAmount)',
        'MSRPriceAmount'    => '(MSRPriceAmount)',
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
     * Wee have to save the deeplink value this way because the framework will
     * not show a DataObjects ID.
     *
     * @var mixed
     */
    protected $deeplinkValue = null;

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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.05.2012
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }
    
    /**
     * getter for the ShortDescription, looks for set translation
     * 
     * @param bool $includeHtml include html tags or remove them from description
     * 
     * @return string The ShortDescription from the translation object or an empty string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>, Patrick Schneider <pschneider@pixeltricks.de>
     * @since 15.05.2012
     */
    public function getShortDescription($includeHtml = true) {
        $shortDescription = $this->getLanguageFieldValue('ShortDescription');
        if (!$includeHtml) {
            // decode
            $shortDescription = utf8_encode(html_entity_decode(strip_tags($shortDescription)));
        }
        $this->extend('updateShortDescription', $shortDescription);
        return $shortDescription;
    }
    
    /**
     * getter for the LongDescription, looks for set translation
     * 
     * @param bool $includeHtml include html tags or remove them from description
     * 
     * @return string The LongDescription from the translation object or an empty string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>, Patrick Schneider <pschneider@pixeltricks.de>
     * @since 15.05.2012
     */
    public function getLongDescription($includeHtml = true) {
        $longDescription = $this->getLanguageFieldValue('LongDescription');
        if (!$includeHtml) {
            // decode
            $longDescription = utf8_encode(html_entity_decode(strip_tags($longDescription)));
        }
        $this->extend('updateLongDescription', $longDescription);
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
            $this->extend('updateMetaTitle', $metaDescription);
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
                $customerGroup = DataObject::get_one('Group', "\"Group\".\"Code\" = 'b2c'");
            }
            $shippingFee = SilvercartShippingMethod::getAllowedShippingFeeFor($this, $country, $customerGroup);
        }
        return $shippingFee;
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
            'ProductNumberShop'                     => $this->fieldLabel('ProductNumberShop'),
            'Title'                                 => $this->singular_name(),
            'SilvercartProductGroup.Title'          => $this->fieldLabel('SilvercartProductGroup'),
            'SilvercartManufacturer.Title'          => $this->fieldLabel('SilvercartManufacturer'),
            'SilvercartAvailabilityStatus.Title'    => $this->fieldLabel('SilvercartAvailabilityStatus'),
            'isActiveString'                        => $this->fieldLabel('isActive'),
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
            'SilvercartProductGroupID' => array(
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
                ''                                     => $this->fieldLabel('CatalogSort'),
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2012
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
                'ProductNumberShop'                     => _t('SilvercartProduct.PRODUCTNUMBER', 'product number'),
                'ProductNumberManufacturer'             => _t('SilvercartProduct.PRODUCTNUMBER_MANUFACTURER', 'product number (manufacturer)'),
                'EANCode'                               => _t('SilvercartProduct.EAN', 'EAN'),
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
                'ID'                                    => 'ID', //needed for the deeplink feature
                'SilvercartProductLanguages'            => _t('SilvercartConfig.TRANSLATIONS'),
                'SilvercartProductGroupItemsWidgets'    => _t('SilvercartProductGroupItemsWidget.CMS_PRODUCTGROUPTABNAME'),
                'WidgetArea'                            => _t('SilvercartProduct.WIDGETAREA'),
                'Prices'                                => _t('SilvercartPrice.PLURALNAME'),
                'SEO'                                   => _t('Silvercart.SEO'),
                'SilvercartProductCondition'            => _t('SilvercartProductCondition.SINGULARNAME'),
                'Deeplinks'                             => _t('Silvercart.DEEPLINKS'),
                'TitleAsc'                              => _t('SilvercartProduct.TITLE_ASC'),
                'TitleDesc'                             => _t('SilvercartProduct.TITLE_DESC'),
                'PriceAmountAsc'                        => _t('SilvercartProduct.PRICE_AMOUNT_ASC'),
                'PriceAmountDesc'                       => _t('SilvercartProduct.PRICE_AMOUNT_DESC'),
                'CatalogSort'                           => _t('SilvercartProduct.CATALOGSORT'),
                'DefaultShippingFee'                    => _t('SilvercartShippingFee.SINGULARNAME'),
                'AddSilvercartImage'                    => _t('SilvercartProduct.AddSilvercartImage'),
                'AddSilvercartFile'                     => _t('SilvercartProduct.AddSilvercartFile'),
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.11.2012
     */
    public static function defaultSort() {
        if (is_null(self::$scDefaultSort)) {
            $sort                   = Session::get('SilvercartProduct.defaultSort');
            $sortableFrontendFields = singleton('SilvercartProduct')->sortableFrontendFields();
            if (is_null($sort) ||
                $sort === false ||
                !is_string($sort) ||
                !array_key_exists($sort, $sortableFrontendFields)) {
                $sort = Object::get_static('SilvercartProduct', 'default_sort');
                if (strpos($sort, '.') === false) {
                    $sort = 'SilvercartProduct.' . $sort;
                    self::setDefaultSort($sort);
                }
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
     */
    public static function get($callerClass = null, $filter = "", $sort = "", $join = "", $limit = null, $containerClass = 'DataList') {
        $products = parent::get($callerClass, $filter, $sort, $join, $limit, $containerClass);
        
        if (!SilvercartTools::isBackendEnvironment()) {
            $requiredAttributesFilter = self::buildRequiredAttributesFilter();
            if (!is_null($requiredAttributesFilter)) {
                $products = $products->where($requiredAttributesFilter);
            }
        }
        
        return $products;
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
     * Getter similar to DataObject::get(); returns a SS_List of products filtered by the requirements in self::getRequiredAttributes();
     * If an product is free of charge, it can have no price. This is for giveaways and gifts.
     *
     * @param string  $whereClause to be inserted into the sql where clause
     * @param string  $sort        string with sort clause
     * @param string  $join        string to be used as SQL JOIN clause;
     * @param integer $limit       DataObject limit
     *
     * @return PaginatedList|ArrayList PaginatedList of products or empty ArrayList
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.09.2012
     */
    public static function getProducts($whereClause = "", $sort = null, $join = null, $limit = null) {
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
        // Support for translatable fields
        $join = $join . sprintf(" LEFT JOIN SilvercartProductLanguage ON (SilvercartProductLanguage.SilvercartProductID = SilvercartProduct.ID AND SilvercartProductLanguage.Locale = '%s') ", Translatable::get_current_locale());

        if ($whereClause != "") {
            $filter = $filter . $whereClause . ' AND ';
        }

        $filter .= 'isActive = 1 AND SilvercartProductGroupID > 0';

        if ($sort === null) {
            $sort = self::defaultSort();
        }
        
        $sortStmnt = '';
        if (!empty($sort)) {
            $sortStmnt = 'ORDER BY ' . $sort;
        }

        $productCount = null;
        if (!is_null($limit)) {
            // get count for paging
            $query = sprintf(
                    "SELECT
                        COUNT(SilvercartProduct.ID) AS ProductCount
                        FROM
                        SilvercartProduct
                        %s
                        WHERE
                            %s
                        %s",
                    $join,
                    $filter,
                    $sortStmnt
            );
            $records = DB::query($query);
            foreach ($records as $record) {
                $productCount = $record['ProductCount'];
            }

            if (is_array($limit)) {
                $length = $limit['limit'];
                $start  = $limit['start'];
            } elseif (stripos($limit, 'OFFSET')) {
                list($length, $start) = preg_split("/ +OFFSET +/i", trim($limit));
            } else {
                $result = preg_split("/ *, */", trim($limit));
                $start  = $result[0];
                $length = isset($result[1]) ? $result[1] : null;
            }
            if (!$length) {
                $length = $start;
                $start = 0;
            }
        }
        $query = sprintf(
                "SELECT
                    SilvercartProduct.ID
                    FROM
                    SilvercartProduct
                    %s
                    WHERE
                        %s
                    %s
                    %s",
                $join,
                $filter,
                $sortStmnt,
                is_null($limit) ? "" : "LIMIT " . $limit
        );

        $records = DB::query($query);
        $recordsArray = array();
        foreach ($records as $record) {
            $recordsArray[] = $record['ID'];
        }
        if (count($recordsArray) > 0) {
            $productIDs = implode(',', $recordsArray);

            $databaseFilteredProducts = DataObject::get(
                    'SilvercartProduct',
                    sprintf(
                            "\"SilvercartProduct\".\"ID\" IN (%s)",
                            $productIDs
                    ),
                    $sort
            );
        } else {
            $databaseFilteredProducts = new ArrayList();
        }
        if (!is_null($productCount) &&
            Controller::curr()->hasMethod('getProductsPerPageSetting') &&
            $databaseFilteredProducts) {
            $databaseFilteredProducts = new PaginatedList($databaseFilteredProducts);
            $databaseFilteredProducts->setPageLength(Controller::curr()->getProductsPerPageSetting());
        }

        // Result sorting
        if (strpos($sort, 'SilvercartProductLanguage.') !== false) {
            $dataObjectSort = str_replace('SilvercartProductLanguage.', '', $sort);
        } else {
            if (strpos($sort, 'PriceGrossAmount') !== false) {
                $dataObjectSort = str_replace('SilvercartProduct.PriceGrossAmount', 'Price', $sort);
            } else if (strpos($sort, 'PriceNetAmount') !== false) {
                $dataObjectSort = str_replace('SilvercartProduct.PriceNetAmount', 'Price', $sort);
            } else {
                $dataObjectSort = $sort;
            }
        }

        return $databaseFilteredProducts;
    }

    /**
     * Creates a whitelist with restricted fields for the FormScaffolder.
     *
     * @param array $params Parameters to manipulate the scaffolding
     *
     * @return FieldList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
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
        
        $silvercartProductGroupMirrorPagesField   = new TreeMultiselectField(
                'SilvercartProductGroupMirrorPages',
                $this->fieldLabel('SilvercartProductGroupMirrorPages'),
                'SiteTree'
        );
        $silvercartProductGroupMirrorPagesField->setTreeBaseID($productGroupHolderID);

        $fields->addFieldToTab('Root.ProductGroups', $silvercartProductGroupDropdown);
        $fields->addFieldToTab('Root.ProductGroups', $silvercartProductGroupMirrorPagesField);
    }

    /**
     * Adds the fields for the Widgets tab
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     * 
     * @todo Check widget admin
     */
    public function getFieldsForWidgets($fields) {
        $fields->findOrMakeTab('Root.Widgets', $this->fieldLabel('WidgetArea'));
        $fields->addFieldsToTab(
                'Root.Widgets',
                SilvercartDataObject::scaffoldFormFields(
                        $this->WidgetArea(),
                        array(
                            'includeRelations'  => ($this->isInDB()),
                            'tabbed'            => false,
                            'ajaxSafe'          => true
                        )
                )
        );
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

        $productNumberGroup = new SilvercartFieldGroup('ProductNumberGroup', '', $fields);
        $productNumberGroup->push($fields->dataFieldByName('ProductNumberShop'));
        $productNumberGroup->push($fields->dataFieldByName('ProductNumberManufacturer'));
        $productNumberGroup->push($fields->dataFieldByName('EANCode'));
        $fields->insertAfter($productNumberGroup, 'isActive');

        $availabilityGroup  = new SilvercartFieldGroup('AvailabilityGroup', $this->fieldLabel('SilvercartAvailabilityStatus'), $fields);
        $availabilityGroup->push(           $fields->dataFieldByName('SilvercartAvailabilityStatusID'));
        $availabilityGroup->breakAndPush(   $fields->dataFieldByName('PurchaseMinDuration'));
        $availabilityGroup->push(           $fields->dataFieldByName('PurchaseMaxDuration'));
        $availabilityGroup->push(           $fields->dataFieldByName('PurchaseTimeUnit'));
        $availabilityGroup->breakAndPush(   $fields->dataFieldByName('StockQuantity'));
        $availabilityGroup->push(           $fields->dataFieldByName('StockQuantityOverbookable'));
        $availabilityGroup->push(           $fields->dataFieldByName('StockQuantityExpirationDate'));
        $fields->insertAfter($availabilityGroup, 'LongDescription');
        $miscGroup = new SilvercartFieldGroup('MiscGroup', _t('SilvercartRegistrationPage.OTHERITEMS'), $fields);
        $miscGroup->pushAndBreak(   $fields->dataFieldByName('SilvercartManufacturerID'));
        $miscGroup->breakAndPush(   $fields->dataFieldByName('PackagingQuantity'));
        $miscGroup->pushAndBreak(   $fields->dataFieldByName('SilvercartQuantityUnitID'));
        $miscGroup->breakAndPush(   $fields->dataFieldByName('Weight'));
        $miscGroup->breakAndPush(   $fields->dataFieldByName('SilvercartProductConditionID'));
        $fields->insertAfter($miscGroup, 'AvailabilityGroup');
    }

    /**
     * Adds or modifies the fields for the Prices tab
     *
     * @param FieldList $fields    FieldList to add fields to
     * @param bool      $addToMain Should the price fields be added to main tab?
     * 
     * @return void
     */
    public function getFieldsForPrices($fields, $addToMain = false) {
        $fields->findOrMakeTab('Root.Prices', $this->fieldLabel('Prices'));
        SilvercartTax::presetDropdownWithDefault($fields->dataFieldByName('SilvercartTaxID'), $this);
        
        $pricesGroup  = new SilvercartFieldGroup('PricesGroup', '', $fields);
        $pricesGroup->push($fields->dataFieldByName('PriceGross'));
        $pricesGroup->push($fields->dataFieldByName('PriceNet'));
        $pricesGroup->push($fields->dataFieldByName('MSRPrice'));
        $pricesGroup->push($fields->dataFieldByName('PurchasePrice'));
        $pricesGroup->push($fields->dataFieldByName('SilvercartTaxID'));
        if ($addToMain) {
            $fields->insertBefore($pricesGroup, 'Title');
        } else {
            $fields->addFieldToTab('Root.Prices', $pricesGroup);
        }
    }

    /**
     * Adds or modifies the fields for the SEO tab
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    public function getFieldsForSeo($fields) {
        $fields->findOrMakeTab('Root.SEO', $this->fieldLabel('SEO'));
        $fields->addFieldToTab('Root.SEO', $fields->dataFieldByName('MetaTitle'));
        $fields->addFieldToTab('Root.SEO', $fields->dataFieldByName('MetaDescription'));
        $fields->addFieldToTab('Root.SEO', $fields->dataFieldByName('MetaKeywords'));
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
     * Adds or modifies the fields for the Deeplinks tab
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    public function getFieldsForDeeplinks($fields) {
        $fields->findOrMakeTab('Root.Deeplinks', $this->fieldLabel('Deeplinks'));
        $fields->addFieldToTab('Root.Deeplinks', new LiteralField('deeplinkText', _t('SilvercartProduct.DEEPLINK_TEXT')));
        if ($this->canView()) {
            $deeplinks = SilvercartDeeplink::get()->filter('isActive', 1);
            if ($deeplinks) {
                $idx = 1;
                foreach ($deeplinks as $deeplink) {
                    if (isset($deeplink->productAttribute)) {
                        $attribute = $deeplink->productAttribute;
                        if (isset($this->{$attribute})) {
                            $this->deeplinkValue = (string)$this->{$attribute};
                            $productDeeplink = $deeplink->getDeeplinkUrl() . $this->deeplinkValue;
                            $fieldName = sprintf(_t('SilvercartProduct.DEEPLINK_FOR'), $attribute);
                            $fields->addFieldToTab('Root.Deeplinks', new ReadonlyField($attribute.$idx, $fieldName, $productDeeplink));
                            $idx++;
                        }
                    }
                }
            }
        }
    }
    
    /**
     * CMS fields of a product
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this, 'isActive');
        
        $this->getFieldsForMain($fields);
        $this->getFieldsForPrices($fields);
        $this->getFieldsForWidgets($fields);
        $this->getFieldsForSeo($fields);
        $this->getFieldsForDeeplinks($fields);
        $this->getFieldsForImages($fields);
        $this->getFieldsForFiles($fields);
        
        $this->extend('updateCMSFields', $fields);
        return $fields;
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
     * @param string $priceType Set to 'gross' or 'net' to get the desired prices.
     *                          If not given the price type will be automatically determined.
     *
     * @return Money price dependent on customer class and configuration
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.10.2011
     */
    public function getPrice($priceType = '') {
        $cacheHash = md5($priceType);
        $cacheKey = 'getPrice_'.$cacheHash;

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
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.08.2011
     */
    public function getPriceNice() {
        $priceNice = '';
        $price     = $this->getPrice();

        if ($price) {
            $priceNice = $price->Nice();
        }

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
     * Return the google taxonomy breadcrumb for the product group of this
     * product.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2011
     */
    public function getGoogleTaxonomyCategory() {
        $category = '';

        if ($this->SilvercartProductGroup() &&
            $this->SilvercartProductGroup()->SilvercartGoogleMerchantTaxonomy()) {

            $category = $this->SilvercartProductGroup()->SilvercartGoogleMerchantTaxonomy()->BreadCrumb();
        }

        return $category;
    }

    /**
     * get some random products to fill a controller every now and then
     *
     * @param integer $amount        How many products should be returned?
     * @param boolean $masterProduct Should only master products be returned?
     *
     * @return PaginatedList PaginatedList of random products
     *
     * @author Roland Lehmann
     * @copyright Pixeltricks GmbH
     * @since 23.10.2010
     */
    public static function getRandomProducts($amount = 4, $masterProduct = true) {
        if ($masterProduct) {
            return self::get("\"SilvercartMasterProductID\" = '0'", "RAND()", null, $amount);
        } else {
            return self::get(null, "RAND()", null, $amount);
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.03.2013
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
                $controller->isProductDetailView()) {

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
     * @since 01.03.2013
     */
    public function registerProductAddCartForm($addCartFormName, $formIdentifier, $controller = null) {
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
     * @param int   $cartID   ID of the users shopping cart
     * @param float $quantity Amount of products to be added
     *
     * @return mixed SilvercartShoppingCartPosition|boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 22.11.2010
     */
    public function addToCart($cartID, $quantity = 1) {
        $addToCartAllowed = true;

        $this->extend('updateAddToCart', $addToCartAllowed);

        if ($quantity == 0 || $cartID == 0) {
            return false;
        }

        if (!$addToCartAllowed) {
            return false;
        }

        $filter               = sprintf("\"SilvercartProductID\" = '%s' AND SilvercartShoppingCartID = '%s'", $this->ID, $cartID);
        $shoppingCartPosition = DataObject::get_one('SilvercartShoppingCartPosition', $filter);

        if (!$shoppingCartPosition) {
            $shoppingCartPosition = new SilvercartShoppingCartPosition();

            $shoppingCartPosition->castedUpdate(
                array(
                    'SilvercartShoppingCartID' => $cartID,
                    'SilvercartProductID' => $this->ID
                )
            );
            $shoppingCartPosition->write();
            $shoppingCartPosition = DataObject::get_one('SilvercartShoppingCartPosition', $filter);
        }

        if ($shoppingCartPosition->isQuantityIncrementableBy($quantity)) {
            if ($shoppingCartPosition->Quantity + $quantity > SilvercartConfig::addToCartMaxQuantity()) {
                $shoppingCartPosition->Quantity += SilvercartConfig::addToCartMaxQuantity() - $shoppingCartPosition->Quantity;
                $shoppingCartPosition->write(); //we have to write because we need the ID
                SilvercartShoppingCartPositionNotice::setNotice($shoppingCartPosition->ID, "maxQuantityReached");
            } else {
                $shoppingCartPosition->Quantity += $quantity;
            }
        } else {
            if ($this->StockQuantity > 0) {
                if ($shoppingCartPosition->Quantity + $this->StockQuantity > SilvercartConfig::addToCartMaxQuantity()) {
                    $shoppingCartPosition->Quantity += SilvercartConfig::addToCartMaxQuantity() - $shoppingCartPosition->Quantity;
                    $shoppingCartPosition->write(); //we have to write because we need the ID
                    SilvercartShoppingCartPositionNotice::setNotice($shoppingCartPosition->ID, "maxQuantityReached");
                } else {
                    $shoppingCartPosition->Quantity += $this->StockQuantity - $shoppingCartPosition->Quantity;
                    $shoppingCartPosition->write(); //we have to write because we need the ID
                    SilvercartShoppingCartPositionNotice::setNotice($shoppingCartPosition->ID, "remaining");
                }
            } else {
                return false;
            }
        }
        $shoppingCartPosition->write();

        SilvercartPlugin::call($this, 'onAfterAddToCart', array($shoppingCartPosition));

        return $shoppingCartPosition;
    }

    /**
     * Returns the product group of this product dependant on the current locale
     *
     * @return SilvercartProductGroupPage
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function SilvercartProductGroup() {
        $silvercartProductGroup = null;
        $currentLocale          = Translatable::get_current_locale();
        if ($this->getComponent('SilvercartProductGroup')) {
            $silvercartProductGroup = $this->getComponent('SilvercartProductGroup');
            if ($silvercartProductGroup->Locale != $currentLocale) {
                $silvercartProductGroup = $silvercartProductGroup->getTranslation($currentLocale);
            }
        }
        return $silvercartProductGroup;
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.01.2013
     */
    public function Link() {
        $link = '';

        if (Controller::curr() instanceof SilvercartProductGroupPage_Controller &&
            !Controller::curr() instanceof SilvercartSearchResultsPage_Controller &&
            $this->SilvercartProductGroupMirrorPages()->find('ID', Controller::curr()->data()->ID)) {
            $link = Controller::curr()->OriginalLink() . $this->ID . '/' . $this->title2urlSegment();
        } elseif (Controller::curr() instanceof SilvercartProductGroupPage_Controller && 
                  Translatable::get_current_locale() != SilvercartConfig::DefaultLanguage()) {
            Translatable::disable_locale_filter();
            if ($this->SilvercartProductGroupMirrorPages()->find('ID', Controller::curr()->getTranslation(SilvercartConfig::DefaultLanguage())->ID)) {
                $link = Controller::curr()->Link() . $this->ID . '/' . $this->title2urlSegment();
            }
            Translatable::enable_locale_filter();
        }
        if (empty($link) &&
            $this->SilvercartProductGroup()) {
            $link = $this->SilvercartProductGroup()->OriginalLink() . $this->ID . '/' . $this->title2urlSegment();
        }
        
        return $link;
    }

    /**
     * Canonical link to the controller, that shows this product
     * An product has a unique URL
     *
     * @return string URL of $this
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.07.2012
     */
    public function CanonicalLink() {
        $link = '';

        if ($this->SilvercartProductGroup()) {
            $link = $this->SilvercartProductGroup()->OriginalLink() . $this->ID . '/' . $this->title2urlSegment();
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
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.11.2010
     */
    public function getTaxAmount() {
        $showPricesGross = false;
        $member          = Member::currentUser();

        if ($member) {
            if ($member->showPricesGross()) {
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
     * Returns a HTML snippet to display the availability of the product.
     *
     * @return string
     */
    public function getAvailability() {
        if ($this->SilvercartAvailabilityStatus()) {
            if ($this->SilvercartAvailabilityStatus()->Code == 'not-available'
             && !empty($this->PurchaseTimeUnit)
             && (!empty($this->PurchaseMinDuration)
              || !empty($this->PurchaseMaxDuration))) {
                $class = 'available-in';
                if (empty($this->PurchaseMinDuration)) {
                    $title = sprintf(_t('SilvercartAvailabilityStatus.STATUS_AVAILABLE_IN'), $this->PurchaseMinDuration, _t('Silvercart.' . strtoupper($this->PurchaseTimeUnit)));
                } elseif (empty($this->PurchaseMinDuration)) {
                    $title = sprintf(_t('SilvercartAvailabilityStatus.STATUS_AVAILABLE_IN'), $this->PurchaseMinDuration, _t('Silvercart.' . strtoupper($this->PurchaseTimeUnit)));
                } else {
                    $title = sprintf(_t('SilvercartAvailabilityStatus.STATUS_AVAILABLE_IN_MIN_MAX'), $this->PurchaseMinDuration, $this->PurchaseMaxDuration, _t('Silvercart.' . strtoupper($this->PurchaseTimeUnit)));
                }
            } else {
                $class = $this->SilvercartAvailabilityStatus()->Code;
                $title = $this->SilvercartAvailabilityStatus()->Title;
            }
            $html = '<span class="' . $class . '">' . $title . '</span>';
        } else {
            $html = '';
        }
        return $html;
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
     * @return float the tax rate in percent
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.03.2011
     */
    public function getTaxRate() {
        return $this->SilvercartTax()->getTaxRate();
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
            $this->extend('updateSilvercartTax', $this->cachedSilvercartTax);
        }
        return $this->cachedSilvercartTax;
    }

        /**
     * We want to delete all attributed WidgetAreas and Widgets before deletion.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function onBeforeDelete() {
        parent::onBeforeDelete();
        if ($this->WidgetArea()->exists()) {
            foreach ($this->WidgetArea()->Widgets() as $widget) {
                $widget->delete();
            }

            $this->WidgetArea()->delete();
        }
        $this->extend('updateOnBeforeDelete');
    }

    /**
     * We check if the SortOrder field has changed. If the change originated
     * from a sort action of the dataobjectmanager and is not related to the
     * main productgroup of the product we reset it to the old value.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.03.2011
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        
        // We want to prevent that sorting with the dataobjectmanager module
        // for mirrored products saves the false SortOrder value.
        $request                      = Controller::curr()->request;
        $silvercartProductGroupPageID = $request->getVar('controllerID');

        if ($silvercartProductGroupPageID) {
            if ($this->SilvercartProductGroup()->ID != $silvercartProductGroupPageID) {
                $sortOrder = $this->record['SortOrder'];

                // Reset SortOrder to old value
                $this->record['SortOrder'] = $this->original['SortOrder'];

                // Set the sort order for the relation
                $productGroupMirrorSortOrder = DataObject::get_one(
                    'SilvercartProductGroupMirrorSortOrder',
                    sprintf(
                        "SilvercartProductGroupPageID = %d AND SilvercartProductID = %d",
                        $silvercartProductGroupPageID,
                        $this->ID
                    )
                );
                if ($productGroupMirrorSortOrder) {
                    $productGroupMirrorSortOrder->setField('SortOrder', $sortOrder);
                    $productGroupMirrorSortOrder->write();
                }
            }
        }
        
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
     * We have to adjust the SilvercartProductGroupMirrorSortOrder table.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.05.2012
     */
    public function onAfterWrite() {
        parent::onAfterWrite();

        // Update relations if necessary
        $mirrorPageIDs                          = array();
        $silvercartProductGroupSortOrderPageIDs = array();

        foreach ($this->SilvercartProductGroupMirrorPages() as $silvercartProductGroupMirrorPage) {
            $mirrorPageIDs[] = $silvercartProductGroupMirrorPage->ID;
        }

        $silvercartProductGroupSortOrderPages = SilvercartProductGroupMirrorSortOrder::get()->filter('SilvercartProductID', $this->ID);

        // delete old records
        if ($silvercartProductGroupSortOrderPages->exists()) {
            foreach ($silvercartProductGroupSortOrderPages as $silvercartProductGroupSortOrderPage) {
                if (!in_array($silvercartProductGroupSortOrderPage->SilvercartProductGroupPageID, $mirrorPageIDs)) {
                    $silvercartProductGroupSortOrderPage->delete();
                } else {
                    $silvercartProductGroupSortOrderPageIDs[] = $silvercartProductGroupSortOrderPage->SilvercartProductGroupPageID;
                }
            }
        }

        // insert new records
        foreach ($mirrorPageIDs as $mirrorPageID) {
            if (!in_array($mirrorPageID, $silvercartProductGroupSortOrderPageIDs)) {
                $newProductGroupMirrorSortOrder = new SilvercartProductGroupMirrorSortOrder();
                $newProductGroupMirrorSortOrder->setField('SilvercartProductID', $this->ID);
                $newProductGroupMirrorSortOrder->setField('SilvercartProductGroupPageID', $mirrorPageID);
                $newProductGroupMirrorSortOrder->setField('SortOrder', $this->original['SortOrder'] ? $this->original['SortOrder'] : $this->record['SortOrder']);
                $newProductGroupMirrorSortOrder->write();
            }
        }
        
        if ($this->WidgetAreaID == 0) {
            $widgetArea = new WidgetArea();
            $widgetArea->write();
            
            $this->WidgetAreaID = $widgetArea->ID;
            $this->write();
        }
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
     * @copyright 2011 pixeltricks GmbH
     * @since 12.04.2011
     */
    public function NoImage() {
        $image = 'silvercart/images/noimage.png';

        $this->extend('updateNoImage', $image);

        return $image;
    }

    /**
     * Returns the url to a placeholder thumbnail image.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 12.04.2011
     */
    public function NoImageSmall() {
        $image = 'silvercart/images/noimage.png';

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
     * @return DataList|ArrayList|false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.02.2013
     */
    public function getSilvercartImages($filter = '') {
        $images = $this->SilvercartImages($filter);
        $silvercartImages = false;

        $this->extend('updateGetSilvercartImages', $images);

        if ($images->count() > 0) {
            $existingImages = new ArrayList();
            foreach ($images as $image) {
                if (!file_exists($image->Image()->getFullPath())) {
                    $noImageObj = SilvercartConfig::getNoImage();

                    if ($noImageObj) {
                        $noImageObj->setField('Title', 'No Image');

                        $image = new SilvercartImage();
                        $image->ImageID             = $noImageObj->ID;
                        $image->SilvercartProductID = $this->ID;
                    }
                }
                $existingImages->push($image);
            }
            $silvercartImages = $existingImages;
        } else {
            $noImageObj = SilvercartConfig::getNoImage();

            if ($noImageObj) {
                $image = new SilvercartImage();
                $image->ImageID             = $noImageObj->ID;
                $image->SilvercartProductID = $this->ID;
                $noImageArray = new ArrayList();
                $noImageArray->push($image);
                $silvercartImages = $noImageArray;
            }
        }

        return $silvercartImages;
    }

    /**
     * decrements the products stock quantity of this product
     *
     * @param integer $quantity the amount to subtract from the current stock quantity
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.7.2011
     *
     * @return void
     */
    public function decrementStockQuantity($quantity) {
        $this->StockQuantity = $this->StockQuantity - $quantity;
        $this->write();
    }

    /**
     * increments the products stock quantity of this product
     *
     * @param integer $quantity the amount to add to the current stock quantity
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.03.2012
     *
     * @return void
     */
    public function incrementStockQuantity($quantity) {
        $this->StockQuantity = $this->StockQuantity + $quantity;
        $this->write();
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.7.2011
     */
    public function isBuyableDueToStockManagementSettings() {
        //is the product already in the cart?
        $cartPositionQuantity = 0;
        if (Member::currentUser() && Member::currentUser()->SilvercartShoppingCart()) {
            $cartPositionQuantity = Member::currentUser()->SilvercartShoppingCart()->getQuantity($this->ID);
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
}
