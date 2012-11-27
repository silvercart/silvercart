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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProduct extends DataObject {

    /**
     * Can contain an identifier for addToCart forms.
     *
     * @var mixed null|string
     */
    public $addCartFormIdentifier = null;

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
        'PriceGross'                  => 'Money', //price taxes including
        'PriceNet'                    => 'Money', //price taxes excluded
        'MSRPrice'                    => 'Money', //manufacturers recommended price
        'PurchasePrice'               => 'Money', //the price the shop owner bought the product for
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
        'SilvercartOrders'                   => 'SilvercartOrder',
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
    );

    /**
     * The default sorting.
     *
     * @var string
     */
    public static $default_sort = 'ProductNumberShop ASC';

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
     * @var DataObjectSet 
     */
    protected $pluggedInTabs = null;
    
    /**
     * All added product information via module
     * 
     * @var DataObjectSet 
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
     * @param string $delimiter Delimiter char to seperate product groups (default is Page::$breadcrumbs_delimiter)
     * 
     * @return string
     */
    public function getSilvercartProductGroupBreadcrumbs($unlinked = true, $delimiter = null) {
        $breadcrumbs = '';
        if ($this->SilvercartProductGroupID > 0) {
            $originalDelimiter = Page::$breadcrumbs_delimiter;
            if (!is_null($delimiter)) {
                Page::$breadcrumbs_delimiter = ' ' . $delimiter . ' ';
            }
            $breadcrumbs = $this->SilvercartProductGroup()->Breadcrumbs(20, $unlinked);
            Page::$breadcrumbs_delimiter = $originalDelimiter;
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
                $customerGroup = DataObject::get_one('Group', "`Group`.`Code` = 'b2c'");
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
     * Is this product viewable in the frontend?
     *
     * @param Member $member the current member
     * 
     * @return bool
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.05.2012
     */
    public function canView($member = null) {
        $canView = parent::canView($member);
        if (!$canView &&
            $this->isActive) {
            $canView = true;
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
            )
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
                'SilvercartProductGroupItemsWidgets'    => _t('SilvercartProductGroupItemsWidget.TITLE'),
                'WidgetArea'                            => _t('WidgetArea.SINGULARNAME'),
                'Prices'                                => _t('SilvercartPrice.PLURALNAME'),
                'SEO'                                   => _t('Silvercart.SEO'),
                'SilvercartProductCondition'            => _t('SilvercartProductCondition.SINGULARNAME'),
                'Deeplinks'                             => _t('Silvercart.Deeplinks'),
                'TitleAsc'                              => _t('SilvercartProduct.TITLE_ASC'),
                'TitleDesc'                             => _t('SilvercartProduct.TITLE_DESC'),
                'PriceAmountAsc'                        => _t('SilvercartProduct.PRICE_AMOUNT_ASC'),
                'PriceAmountDesc'                       => _t('SilvercartProduct.PRICE_AMOUNT_DESC'),
                'CatalogSort'                           => _t('SilvercartProduct.CATALOGSORT'),
                'DefaultShippingFee'                    => _t('SilvercartShippingFee.SINGULARNAME'),
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@œÄixeltricks.de>
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
     * Getter similar to DataObject::get(); returns a DataObectSet of products filtered by the requirements in self::getRequiredAttributes();
     * If an product is free of charge, it can have no price. This is for giveaways and gifts.
     *
     * @param string  $whereClause to be inserted into the sql where clause
     * @param string  $sort        string with sort clause
     * @param string  $join        string to be used as SQL JOIN clause;
     * @param integer $limit       DataObject limit
     *
     * @return DataObjectSet DataObjectSet of products or false
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.09.2012
     */
    public static function get($whereClause = "", $sort = null, $join = null, $limit = null) {
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
                            $filter .= sprintf("(`PriceNetAmount` != 0.0) AND ");
                        } else {
                            $filter .= sprintf("(`PriceGrossAmount` != 0.0) AND ");
                        }
                    } else {
                        $filter .= sprintf("`%s` != '' AND ", $requiredAttribute);
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

        $filter .= 'isActive = 1';

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
                        COUNT(`SilvercartProduct`.`ID`) AS ProductCount
                        FROM
                        `SilvercartProduct`
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
                    `SilvercartProduct`.`ID`
                    FROM
                    `SilvercartProduct`
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
                            "`SilvercartProduct`.`ID` IN (%s)",
                            $productIDs
                    ),
                    $sort
            );
        } else {
            $databaseFilteredProducts = new DataObjectSet();
        }
        if (!is_null($productCount) &&
            Controller::curr()->hasMethod('getProductsPerPageSetting') &&
            $databaseFilteredProducts) {
            $databaseFilteredProducts->setPageLength(Controller::curr()->getProductsPerPageSetting());
            $databaseFilteredProducts->setPageLimits($start, $length, $productCount);
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
     * @return FieldSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.05.2012
     */
    public function scaffoldFormFields($params) {
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
            'fieldClasses' => array(
                'PriceGross'    => 'SilvercartMoneyField',
                'PriceNet'      => 'SilvercartMoneyField',
                'MSRPrice'      => 'SilvercartMoneyField',
                'PurchasePrice' => 'SilvercartMoneyField',
            ),
        );

        $this->extend('updateScaffoldFormFields', $params);

        return parent::scaffoldFormFields($params);
    }

    /**
     * Adds the fields for the MirrorProductGroups tab
     *
     * @param FieldSet $fields FieldSet to add fields to
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
     * @param FieldSet $fields FieldSet to add fields to
     * 
     * @return void
     */
    public function getFieldsForWidgets($fields) {
        $availableWidgets = array();

        $classes = ClassInfo::subclassesFor('Widget');
        array_shift($classes);
        foreach ($classes as $class) {
            if ($class == 'SilvercartWidget') {
                continue;
            }
            $widgetClass        = singleton($class);
            $availableWidgets[] = array($widgetClass->ClassName, $widgetClass->Title());
        }

        $widgetAreaField = new SilvercartHasManyOrderField(
            $this->WidgetArea(),
            'Widgets',
            'WidgetArea',
            'Widget Konfiguration',
            $availableWidgets
        );

        $fields->addFieldToTab('Root.Widgets', $widgetAreaField);
    }

    /**
     * Adds or modifies the fields for the Main tab
     *
     * @param FieldSet $fields FieldSet to add fields to
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
        $availabilityGroup->push($fields->dataFieldByName('SilvercartAvailabilityStatusID'));
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
     * @param FieldSet $fields    FieldSet to add fields to
     * @param bool     $addToMain Should the price fields be added to main tab?
     * 
     * @return void
     */
    public function getFieldsForPrices($fields, $addToMain = false) {
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
     * @param FieldSet $fields FieldSet to add fields to
     * 
     * @return void
     */
    public function getFieldsForSeo($fields) {
        $fields->addFieldToTab('Root.SEO', $fields->dataFieldByName('MetaTitle'));
        $fields->addFieldToTab('Root.SEO', $fields->dataFieldByName('MetaDescription'));
        $fields->addFieldToTab('Root.SEO', $fields->dataFieldByName('MetaKeywords'));
    }

    /**
     * Adds or modifies the fields for the Images tab
     *
     * @param FieldSet $fields FieldSet to add fields to
     * 
     * @return void
     */
    public function getFieldsForImages($fields) {
        $silvercartImageField = new ComplexTableField(
                $this,
                'SilvercartImages',
                'SilvercartImage',
                null,
                'getCMSFieldsForProduct',
                sprintf(                 
                        "`SilvercartImage`.`SilvercartProductID` = '%s'",
                        $this->ID
                )
        );
        $silvercartImageField->setPermissions(
                array(
                    'add',
                    'edit',
                    'delete',
                )
        );
        $fields->addFieldToTab('Root.SilvercartImages', $silvercartImageField);
    }

    /**
     * Adds or modifies the fields for the Files tab
     *
     * @param FieldSet $fields FieldSet to add fields to
     * 
     * @return void
     */
    public function getFieldsForFiles($fields) {
        $silvercartFileField = new ComplexTableField(
                $this,
                'SilvercartFiles',
                'SilvercartFile',
                null,
                'getCMSFieldsForProduct',
                sprintf(
                        "`SilvercartFile`.`SilvercartProductID` = '%s'",
                        $this->ID
                )
        );
        $silvercartFileField->setPermissions(
                array(
                    'add',
                    'edit',
                    'delete',
                )
        );
        $fields->addFieldToTab('Root.SilvercartFiles', $silvercartFileField);
    }

    /**
     * Adds or modifies the fields for the Deeplinks tab
     *
     * @param FieldSet $fields FieldSet to add fields to
     * 
     * @return void
     */
    public function getFieldsForDeeplinks($fields) {
        $fields->addFieldToTab('Root.Deeplinks', new LiteralField('deeplinkText', _t('SilvercartProduct.DEEPLINK_TEXT')));
        if ($this->canView()) {
            $deeplinks = DataObject::get('SilvercartDeeplink', '`isActive` = 1');
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
     * Replaces the SilvercartProductGroupID DropDownField with a GroupedDropDownField.
     * Be aware that new properties/relations will not be scaffolded any more.
     *
     * @param array $params See {@link scaffoldFormFields()}
     *
     * @return FieldSet
     */
    public function getCMSFields($params = null) {
        $this->getCMSFieldsIsCalled = true;
        $fields = $this->scaffoldFormFields($params);
        
        /***********************************************************************
         * TRANSLATION SECTION
         **********************************************************************/
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage(true));
        $afterFieldName = 'EANCode';
        foreach ($languageFields as $languageField) {
            $fields->insertAfter($languageField, $afterFieldName);
            $afterFieldName = $languageField->Name();
        }
        
        if (!$this->ID) {
            $this->getFieldsForMain($fields);
            $this->getFieldsForPrices($fields, true);
        } else {
            /***********************************************************************
            * TAB SECTION
            **********************************************************************/
            $root                   = $fields->findOrMakeTab('Root');
            $main                   = $fields->findOrMakeTab('Root.Main');
            $prices                 = $fields->findOrMakeTab('Root.Prices',                 $this->fieldLabel('Prices'));
            $seo                    = $fields->findOrMakeTab('Root.SEO',                    $this->fieldLabel('SEO'));
            $productGroups          = $fields->findOrMakeTab('Root.ProductGroups',          $this->fieldLabel('SilvercartProductGroups'));
            $widgets                = $fields->findOrMakeTab('Root.Widgets',                $this->fieldLabel('WidgetArea'));
            $deeplinks              = $fields->findOrMakeTab('Root.Deeplinks',              $this->fieldLabel('Deeplinks'));

            /***********************************************************************
            * TAB SORT SECTION
            **********************************************************************/
            $root->removeByName('Prices');
            $fields->insertBefore($prices, 'SilvercartProductLanguages');
            $root->removeByName('SEO');
            $fields->insertBefore($seo, 'SilvercartProductLanguages');
            $root->removeByName('ProductGroups');
            $fields->insertBefore($productGroups, 'SilvercartProductLanguages');

            /***********************************************************************
            * TAB REMOVAL SECTION
            **********************************************************************/
            $root->removeByName('SilvercartShoppingCartPositions');

            /***********************************************************************
            * FIELDS SECTION
            **********************************************************************/
            $this->getFieldsForMain($fields);
            $this->getFieldsForPrices($fields);
            $this->getFieldsForSeo($fields);
            $this->getFieldsForProductGroups($fields);
            $this->getFieldsForWidgets($fields);
            $this->getFieldsForDeeplinks($fields);
            $this->getFieldsForImages($fields);
            $this->getFieldsForFiles($fields);
        }
        
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    /**
     * Customizes the backend popup for Products.
     *
     * @return FieldSet the editible fields
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.10.2011
     */
    public function getCMSFields_forPopup() {
        $fields = $this->getCMSFields();
        $fields->removeByName('SilvercartMasterProduct'); //remove the dropdown for the relation masterProduct
        $fields->removeByName('SilvercartShoppingCartPositions');//There is not enough space for so many tabs
        //Get all products that have no master
        $var = sprintf("`SilvercartMasterProductID` = '%s'", "0");
        $silvercartMasterProducts = DataObject::get("SilvercartProduct", $var);
        $silvercartMasterProductMap = array();
        if ($silvercartMasterProducts) {
            $silvercartMasterProductMap = $silvercartMasterProducts->map();
        }
        $dropdownField = new DropdownField(
            'SilvercartMasterProductID',
            _t('SilvercartProduct.MASTERPRODUCT', 'master product'),
            $silvercartMasterProductMap,
            null,
            null,
            _t('SilvercartProduct.CHOOSE_MASTER', '-- choose master --')
        );
        
        $fields->addFieldToTab('Root.Main', $dropdownField);

        $this->extend('updateCMSFields_forPopup', $fields);
        $this->getCMSFieldsIsCalled = false;
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
     * @return array DataObjectSet of random products
     *
     * @author Roland Lehmann
     * @copyright Pixeltricks GmbH
     * @since 23.10.2010
     */
    public static function getRandomProducts($amount = 4, $masterProduct = true) {
        if ($masterProduct) {
            return self::get("`SilvercartMasterProductID` = '0'", "RAND()", null, $amount);
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
     * @since 23.10.2010
     * @author Roland Lehmann
     */
    public static function setRequiredAttributes($concatinatedAttributesString) {
        $requiredAttributes      = array();
        $requiredAttributesArray = explode(",", str_replace(" ", "", $concatinatedAttributesString));

        foreach ($requiredAttributesArray as $attribute) {
            if (!in_array($attribute, self::$blacklistedRequiredAttributes)) {
                $requiredAttributes[] = $attribute;
            }
        }

        self::$requiredAttributes = $requiredAttributes;
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.11.2012
     */
    public function productAddCartForm() {
        $controller      = Controller::curr();
        $addCartFormName = 'ProductAddCartForm';

        if (method_exists($controller, 'isProductDetailView') &&
            $controller->isProductDetailView()) {
            
            $addCartFormName = 'SilvercartProductAddCartFormDetail';
        }

        if ($this->addCartFormIdentifier !== null) {
            $addCartFormIdentifier = $this->addCartFormIdentifier;
        } else {
            $addCartFormIdentifier = $this->ID;
        }

        return Controller::curr()->InsertCustomHtmlForm(
            $addCartFormName.$addCartFormIdentifier,
            array(
                $this
            )
        );
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
     * Link to the controller, that shows this product
     * An product has a unique URL
     *
     * @return string URL of $this
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.05.2012
     */
    public function Link() {
        $link = '';

        if (Controller::curr() instanceof SilvercartProductGroupPage_Controller &&
            !Controller::curr() instanceof SilvercartSearchResultsPage_Controller &&
            $this->SilvercartProductGroupMirrorPages()->find('ID', Controller::curr()->data()->ID)) {
            $link = Controller::curr()->OriginalLink() . $this->ID . '/' . $this->title2urlSegment();
        } elseif ($this->SilvercartProductGroup()) {
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
     * We want to delete all attributed WidgetAreas and Widgets before deletion.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.05.2012
     */
    public function onBeforeDelete() {
        parent::onBeforeDelete();
        foreach ($this->WidgetArea()->Widgets() as $widget) {
            $widget->delete();
        }
        
        $this->WidgetArea()->delete();
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

        $silvercartProductGroupSortOrderPages = DataObject::get(
            'SilvercartProductGroupMirrorSortOrder',
            sprintf(
                "SilvercartProductID = %d",
                $this->ID
            )
        );

        // delete old records
        if ($silvercartProductGroupSortOrderPages) {
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
     * saves the value of the field LongDescription correctly into HTMLText
     * 
     * @param string $value the field value
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.01.2012
     */
    public function saveLongDescription($value) {
        $languageObj = $this->getLanguage();
        $languageObj->LongDescription = $value;
        $languageObj->write();
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
     * Returns a DataObjectSet of attributed images. If there are no images
     * attributed the method checks if there's a standard no-image
     * visualitation defined in SilvercartConfig and returns the defined image
     * as DataObjectSet. As last resort boolean false is returned.
     *
     * @param string $filter An optional sql filter statement
     *
     * @return mixed DataObjectSet|bool false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 27.06.2011
     */
    public function getSilvercartImages($filter = '') {
        $images = $this->SilvercartImages($filter);

        $this->extend('updateGetSilvercartImages', $images);

        if ($images->Count() > 0) {
            $existingImages = new DataObjectSet();
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
            return $existingImages;
        } else {
            $noImageObj = SilvercartConfig::getNoImage();

            if ($noImageObj) {
                $noImageObj->setField('Title', 'No Image');
                $silvercartImageObj = new SilvercartImage();
                $silvercartImageObj->ImageID             = $noImageObj->ID;
                $silvercartImageObj->SilvercartProductID = $this->ID;

                $images = new DataObjectSet();
                $images->addWithoutWrite($silvercartImageObj);

                return $images;
            }
        }

        return false;
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
     * @return DataObjectSet  
     */
    public function getPluggedInTabs() {
        if (is_null($this->pluggedInTabs)) {
            $this->pluggedInTabs = SilvercartPlugin::call($this, 'getPluggedInTabs', array(), false, 'DataObjectSet');
        }
        return $this->pluggedInTabs;
    }
    
    /**
     * returns all additional information about a product
     * 
     * @return DataObjectSet 
     */
    public function getPluggedInProductMetaData() {
        if (is_null($this->pluggedInProductMetaData)) {
            $this->pluggedInProductMetaData = SilvercartPlugin::call($this, 'getPluggedInProductMetaData', array(), false, 'DataObjectSet');
        }
        return $this->pluggedInProductMetaData;
    }
}

/**
 * Handles a managed model class and provides default collection filtering behavior.
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 01.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProduct_CollectionController extends ModelAdmin_CollectionController {
    
    /**
     * We use a slice techniqure here since imports of large datasets fail
     * with the standard import mechanism.
     *
     * @param array          $data    Some data
     * @param Form           $form    The form object
     * @param SS_HTTPRequest $request The request object
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.08.2011
     */
    public function import($data, $form, $request) {
        $modelName = $data['ClassName'];

        if (!$this->showImportForm() || (is_array($this->showImportForm()) && !in_array($modelName,$this->showImportForm()))) {
            return false;
        }
        $importers = $this->parentController->getModelImporters();
        $importerClass = $importers[$modelName];

        $loader = new $importerClass($data['ClassName']);

        // File wasn't properly uploaded, show a reminder to the user
        if (empty($_FILES['_CsvFile']['tmp_name']) ||
            file_get_contents($_FILES['_CsvFile']['tmp_name']) == '') {

            $form->sessionMessage(_t('ModelAdmin.NOCSVFILE', 'Please browse for a CSV file to import'), 'good');
            Director::redirectBack();
            return false;
        }

        if (!empty($data['EmptyBeforeImport']) && $data['EmptyBeforeImport']) { //clear database before import
            $loader->deleteExistingRecords = true;
        }
        $results = $loader->load($_FILES['_CsvFile']['tmp_name']);

        $message = '';
        if ($results instanceof BulkLoader_Result) {
            if ($results->CreatedCount()) {
                    $message .= sprintf(
                    _t('ModelAdmin.IMPORTEDRECORDS', "Imported %s records."),
                    $results->CreatedCount()
                );
            }
            if ($results->UpdatedCount()) {
                $message .= sprintf(
                    _t('ModelAdmin.UPDATEDRECORDS', "Updated %s records."),
                    $results->UpdatedCount()
                );
            }
            if ($results->DeletedCount()) {
                $message .= sprintf(
                    _t('ModelAdmin.DELETEDRECORDS', "Deleted %s records."),
                    $results->DeletedCount()
                );
            }
            if (!$results->CreatedCount() && !$results->UpdatedCount()) {
                $message .= _t('ModelAdmin.NOIMPORT', "Nothing to import");
            }
        }

        $form->sessionMessage($message, 'good');
        Director::redirectBack();
    }

    /**
     * Imports a slice of a CSV file.
     *
     * @param array           &$data    The sent data
     * @param Form            &$form    The connected form
     * @param SS_HTTP_Request &$request The request object
     * @param string          &$output  The resulting output as html string
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 16.08.2011
     */
    public function importCsvSlice(&$data, &$form, &$request, &$output) {
        $importers      = $this->parentController->getModelImporters();
        $importerClass  = $importers['SilvercartProduct'];
        $loader         = new $importerClass('SilvercartProduct');
        $csvFile        = isset($_REQUEST['csvFile']) ? urldecode($_REQUEST['csvFile']) : '';

        if ($csvFile) {
            $result = $loader->load($csvFile);
        }

        print $result;
        exit();
    }

    /**
     * We extend the sidebar template renderer so that you can alter it in your
     * decorators.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 11.03.2011
     */
    public function getModelSidebar() {
        $sidebarHtml = $this->renderWith('SilvercartProductModelSidebar');

        $this->extend('getUpdatedModelSidebar', $sidebarHtml);
        return $sidebarHtml;
    }

    /**
     * Set the sort order specifically.
     *
     * @param array $searchCriteria The search criteria
     *
     * @return SQLQuery
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.03.2012
     */
    public function getSearchQuery($searchCriteria) {
        $query = parent::getSearchQuery($searchCriteria);

        $query->orderby(SilvercartProduct::defaultSort());

        return $query;
    }

    /**
     * Generate a CSV import form for a single {@link DataObject} subclass.
     *
     * We extend this form so that you can alter it in your decorators.
     *
     * @return Form
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.03.2011
     */
    public function ImportForm() {
        $form = parent::ImportForm();

        $this->extend('updateImportForm', $form);

        return $form;
    }

    /**
     * A form that let's the user import images for existing products.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.08.2011
     */
    public function ImportImagesForm() {
        $fields = new FieldSet(
            new HeaderField(
                'importImagesHeadline',
                _t('SilvercartProduct.IMPORTIMAGESFORM_HEADLINE'),
                3
            ),
            new LiteralField(
                'imageDirectoryDesc',
                _t('SilvercartProduct.IMPORTIMAGESFORM_IMAGEDIRECTORY_DESC').':'
            ),
            new TextField(
                'imageDirectory',
                _t('SilvercartProduct.IMPORTIMAGESFORM_IMAGEDIRECTORY')
            )
        );
        $actions = new FieldSet(
            new FormAction(
                'importImages',
                _t('SilvercartProduct.IMPORTIMAGESFORM_ACTION')
            )
        );

        $form = new Form(
            $this,
            'ImportImagesForm',
            $fields,
            $actions
        );
        $form->setFormMethod('get');
        $form->disableSecurityToken();

        return $form;
    }

    /**
     * Imports images with the settings from $this->ImportImagesForm().
     *
     * @param array          $data    The data sent
     * @param Form           $form    The form object
     * @param SS_HTTPRequest $request The request object
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.08.2011
     */
    public function importImages($data, $form, $request) {
        $this->Log('', 'importImages');
        $this->Log('', 'importImages');
        $this->Log('starting import', 'importImages');
        $resultsForm                = $this->ResultsForm(array_merge($form->getData(), $data));
        $consecutiveNumberSeparator = '__';
        $fileNamesToSearchFiltered  = array();
        $mapNamesFiltered           = array();

        if (empty($data['imageDirectory'])) {
            return sprintf(
                "<p style=\"margin: 10px;\">%s</p>",
                _t('SilvercartProduct.IMPORTIMAGESFORM_ERROR_NOIMAGEDIRECTORYGIVEN')
            );
        }

        if (!is_dir($data['imageDirectory'])) {
            return sprintf(
                "<p style=\"margin: 10px;\">%s</p>",
                _t('SilvercartProduct.IMPORTIMAGESFORM_ERROR_DIRECTORYNOTVALID')
            );
        }

        $files              = scandir($data['imageDirectory']);
        $foundFiles         = count($files) - 2;
        $importedFiles      = 0;
        $fileNamesToSearch  = array();
        $mapNames           = array();

        foreach ($files as $file) {
            $fileInfo = pathinfo($file);

            if (empty($fileInfo['extension'])) {
                continue;
            }

            $fileName            = basename($file, '.'.$fileInfo['extension']);
            $fileNamesToSearch[] = Convert::raw2sql($fileName);
            $mapNames[Convert::raw2sql($fileName)] = $file;
        }

        foreach ($fileNamesToSearch as $fileNameToSearch) {
            if (strpos($fileNameToSearch, $consecutiveNumberSeparator) === false) {
                if (!in_array($fileNameToSearch, $fileNamesToSearchFiltered)) {
                    $fileNamesToSearchFiltered[] = $fileNameToSearch;
                }
            } else {
                $fileNameElements = explode($consecutiveNumberSeparator, $fileNameToSearch);

                if (!in_array($fileNameElements[0], $fileNamesToSearchFiltered)) {
                    $fileNamesToSearchFiltered[] = $fileNameElements[0];
                }
            }
        }

        foreach ($mapNames as $mapNameKey => $mapNameValue) {
            if (strpos($mapNameKey, $consecutiveNumberSeparator) === false) {
                $mapNameKeyFiltered = $mapNameKey;
            } else {
                $mapNameKeyElements = explode($consecutiveNumberSeparator, $mapNameKey);
                $mapNameKeyFiltered = $mapNameKeyElements[0];
            }

            if (!array_key_exists($mapNameKeyFiltered, $mapNamesFiltered)) {
                $mapNamesFiltered[$mapNameKeyFiltered] = array();
            }

            $mapNamesFiltered[$mapNameKeyFiltered][] = $mapNameValue;
        }

        // Add trailing slash if necessary
        if (substr($data['imageDirectory'], -1) != '/') {
            $data['imageDirectory'] .= '/';
        }

        /** @var $products SilvercartProduct **/
        $products = $this->findProductsByNumbers(implode(',', $fileNamesToSearchFiltered), $mapNamesFiltered);

        // Create Image object and SilvercartImage objects and connect them
        // to the respective SilvercartProduct
        if ($products) {
            foreach ($products as $product) {
                if (!array_key_exists('fileName', $product)) {
                    continue;
                }
                foreach ($product['fileName'] as $fileName) {
                    // disable caching to prevent duplicated image objects
                    $existingImage = DataObject::get_one(
                        'Image',
                        sprintf(
                            "Filename = 'assets/Uploads/%s'",
                            $fileName
                        ),
                        false
                    );

                    if ($existingImage) {
                        $this->Log('using an existing image', 'importImages');
                        $this->Log("\t" . 'ProductID: ' . $product['ID'], 'importImages');
                        $this->Log("\t" . 'ImageID:   ' . $existingImage->ID, 'importImages');
                        $this->Log("\t" . 'Filename:   ' . $fileName, 'importImages');
                        // overwrite existing image
                        $image       = $existingImage;
                        $newFilePath = $image->getFullPath();

                        if (!copy($data['imageDirectory'].$fileName, $newFilePath)) {
                            continue;
                        }

                        $silvercartImage = DataObject::get_one('SilvercartImage', sprintf("`ImageID` = '%s' AND `SilvercartProductID` = '%s'", $image->ID, $product['ID']));
                        if (!$silvercartImage) {
                            $silvercartImage = $this->createSilvercartImage(
                                $product['ID'],
                                $image->ID,
                                $fileName
                            );
                        }
  
                        $image->deleteFormattedImages();
                        $importedFiles++;
                    } else {
                        // Create new image
                        $image = $this->createImageObject(
                            $data['imageDirectory'].$fileName,
                            $fileName,
                            $fileName,
                            'Image'
                        );

                        $this->Log('creating new image', 'importImages');
                        $this->Log("\t" . 'ProductID: ' . $product['ID'], 'importImages');
                        $this->Log("\t" . 'ImageID:   ' . $image->ID, 'importImages');
                        $this->Log("\t" . 'Filename:   ' . $fileName, 'importImages');

                        if ($image) {
                            // Create Image object
                            $silvercartImage = $this->createSilvercartImage(
                                $product['ID'],
                                $image->ID,
                                $fileName
                            );

                            if ($silvercartImage) {
                                $importedFiles++;
                            }
                            unset($image);
                            unset($silvercartImage);
                        }
                    }              
                }
            }

            // Unlink imported images from original location. We have to do
            // this in a separated loop because one image can be used for
            // many products.
            foreach ($products as $product) {
                if (!array_key_exists('fileName', $product)) {
                    continue;
                }
            }
        }

        print "<div style=\"margin: 10px\">";
        printf(
            _t('SilvercartProduct.IMPORTIMAGESFORM_REPORT'),
            $foundFiles,
            $importedFiles
        );
        print "</div>";
        $this->Log('end', 'importImages');
    }

    /**
     * Create a SilvercartImage object with the given parameters.
     *
     * @param int    $silvercartProductID The ID of the attributed SilvercartProduct
     * @param int    $imageID             The ID of the attributed image
     * @param string $title               The title for the image
     *
     * @return mixed SilvercartImage|boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.08.2011
     */
    protected function createSilvercartImage($silvercartProductID, $imageID, $title) {
        $sqlQuery = new SQLQuery(
            'ID',
            'SilvercartImage',
            null,
            'ID DESC',
            null,
            null,
            '1'
        );
        $insertID = $sqlQuery->execute()->value();
        $insertID = (int) $insertID + 1;

        DB::query(
            sprintf(
                '
                INSERT INTO
                    SilvercartImage(
                        ID, ImageID
                    ) VALUES(
                        %d, %d
                    )
                ',
                $insertID, $imageID
            )
        );
        
        DB::query(
            sprintf(
                "
                INSERT INTO
                    SilvercartImageLanguage(
                        SilvercartImageID, Locale, Created
                    ) VALUES(
                        %d, '%s', '%s'
                    )
                ",
                $insertID, Translatable::get_current_locale(), date('Y-m-d H:i:s')
            )
        );

        $object = DataObject::get_by_id(
            'SilvercartImage', 
            $insertID
        );

        if ($object) {
            $object->setField('ClassName',              'SilvercartImage');
            $object->setField('Created',                date('Y-m-d H:i:s'));
            $object->setField('SilvercartProductID',    $silvercartProductID);
            $object->setField('ImageID',                $imageID);
            $object->setField('Title',                  $title);
            $object->write();
        }

        return $object;
    }

    /**
     * Create an Image object from the given filepath.
     *
     * @param string $filePath        The filepath
     * @param string $fileName        The filename
     * @param string $title           The title of the image
     * @param string $objectClassName The classname of the object to use
     *
     * @return mixed Image|boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 26.08.2011
     */
    protected function createImageObject($filePath, $fileName, $title, $objectClassName) {
        if (!is_file($filePath)) {
            return false;
        }

        // move image to silverstripe path
        $newFilePath = Director::baseFolder().'/assets/Uploads/'.$fileName;

        if (!copy($filePath, $newFilePath)) {
            return false;
        }

        $sqlQuery = new SQLQuery(
            'ID',
            'File',
            null,
            'ID DESC',
            null,
            null,
            '1'
        );
        $insertID = $sqlQuery->execute()->value();
        $insertID = (int) $insertID + 1;

        DB::query(
            sprintf(
                '
                INSERT INTO
                    File(
                        ID
                    ) VALUES(
                        %d
                    )
                ',
                $insertID
            )
        );

        $object = DataObject::get_by_id(
            'File',
            $insertID
        );

        if ($object) {
            $object->setField('ClassName',   $objectClassName);
            $object->setField('Created',     date('Y-m-d H:i:s'));
            $object->setField('Name',        $title);
            $object->setField('Title',       $title);
            $object->setField('Filename',    'assets/Uploads/'.$fileName);
            $object->setField('ParentID',    1);
            $object->setField('OwnerID',     1);
            $object->write();
        }

        return $object;
    }

    /**
     * Tries to find a product by the given number. The fields searched for are:
     *     - ProductNumberShop
     *     - ProductNumberManufacturer
     * Returns the ID of the found product or false.
     *
     * @param string $numbers  The number to search for
     * @param string $mapNames ???
     *
     * @return mixed int|boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.08.2011
     */
    protected function findProductsByNumbers($numbers, $mapNames) {
        $resultSet = SilvercartPlugin::call($this, 'overwriteFindProductsByNumbers', array($numbers, $mapNames), true, array());

        if (is_array($resultSet) &&
            count($resultSet) > 0
            && !empty($resultSet[0])) {
            return $resultSet[0];
        }

        $resultSet  = array();
        $query      = DB::query(
            sprintf("
                SELECT
                    `SilvercartProduct`.`ID`,
                    `SilvercartProduct`.`ProductNumberShop`,
                    `SilvercartProduct`.`ProductNumberManufacturer`
                FROM
                    `SilvercartProduct`
                WHERE
                    FIND_IN_SET(`SilvercartProduct`.`ProductNumberShop`, '%s') OR
                    FIND_IN_SET(`SilvercartProduct`.`ProductNumberManufacturer`, '%s')
                ",
                $numbers,
                $numbers
            )
        );

        if ($query) {
            foreach ($query as $result) {

                if (array_key_exists($result['ProductNumberShop'], $mapNames)) {
                    $result['fileName'] = $mapNames[$result['ProductNumberShop']];
                } else if (array_key_exists($result['ProductNumberManufacturer'], $mapNames)) {
                    $result['fileName'] = $mapNames[$result['ProductNumberManufacturer']];
                }

                $resultSet[] = $result;
            }
        }
        return $resultSet;
    }

    /**
     * Extends the search form.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.03.2011
     */
    public function SearchForm() {
        $form = parent::SearchForm();

        $form->Fields()->replaceField(
            'SilvercartProductGroup__ID',
            new GroupedDropdownField(
                'SilvercartProductGroupID',
                _t('SilvercartProductGroupPage.SINGULARNAME'),
                SilvercartProductGroupHolder_Controller::getRecursiveProductGroupsForGroupedDropdownAsArray()
            )
        );

        $this->extend('updateSearchForm', $form);

        return $form;
    }

    /**
     * Create a custom form so that we can extend it.
     *
     * @param string $formIdentifier Identifier of the form to extend
     *
     * @return Form
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 11.03.2011
     */
    public function CustomForm($formIdentifier) {
        $form = '';
        $form = $this->$formIdentifier();

        $this->extend('updateCustomForm', $form, $formIdentifier);

        return $form;
    }

    /**
     * Custom form action so that we can decorate it.
     *
     * @param array           $data    The sent data
     * @param Form            $form    The connected form
     * @param SS_HTTP_Request $request The request object
     *
     * @return mixed|void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 11.03.2011
     */
    public function customFormAction($data, $form = null, $request = null) {
        $output = '';

        $this->extend('updateCustomFormAction', $data, $form, $request, $output);

        $this->$data['action']($data, $form, $request, $output);

        return $output;
    }

    /**
     * Checks, whether the current product has more than $count images.
     *
     * @param int $count Count to check
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2011
     */
    public function hasMoreImagesThan($count) {
        $hasMoreImagesThanCount = false;
        if ($this->SilvercartImages()->Count() > $count) {
            $hasMoreImagesThanCount = true;
        }
        return $hasMoreImagesThanCount;
    }

    /**
     * Return the columns available in the column selection field.
     * Overload this to make other columns available.
     *
     * This is used for the CSV export, too.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.07.2011
     */
    public function columnsAvailable() {
        $columnsAvailable = array(
            'ID'                                    => 'ID',
            'Title'                                 => 'Title',
            'ShortDescription'                      => 'ShortDescription',
            'LongDescription'                       => 'LongDescription',
            'MetaDescription'                       => 'MetaDescription',
            'MetaTitle'                             => 'MetaTitle',
            'MetaKeywords'                          => 'MetaKeywords',
            'ProductNumberShop'                     => 'ProductNumberShop',
            'ProductNumberManufacturer'             => 'ProductNumberManufacturer',
            'PurchasePriceAmount'                   => 'PurchasePriceAmount',
            'PurchasePriceCurrency'                 => 'PurchasePriceCurrency',
            'MSRPriceAmount'                        => 'MSRPriceAmount',
            'MSRPriceCurrency'                      => 'MSRPriceCurrency',
            'PriceGrossAmount'                      => 'PriceGrossAmount',
            'PriceGrossCurrency'                    => 'PriceGrossCurrency',
            'PriceNetAmount'                        => 'PriceNetAmount',
            'PriceNetCurrency'                      => 'PriceNetCurrency',
            'Weight'                                => 'Weight',
            'EANCode'                               => 'EANCode',
            'isActive'                              => 'isActive',
            'PurchaseMinDuration'                   => 'PurchaseMinDuration',
            'PurchaseMaxDuration'                   => 'PurchaseMaxDuration',
            'PurchaseTimeUnit'                      => 'PurchaseTimeUnit',
            'StockQuantity'                         => 'StockQuantity',
            'StockQuantityOverbookable'             => 'StockQuantityOverbookable',
            'SilvercartProductGroupID'              => 'SilvercartProductGroupID',
            'SilvercartManufacturerID'              => 'SilvercartManufacturerID',
            'SilvercartAvailabilityStatusID'        => 'SilvercartAvailabilityStatusID',
            'SilvercartProductGroup.Title'          => _t('SilvercartProductGroupPage.SINGULARNAME'),
            'SilvercartManufacturer.Title'          => _t('SilvercartManufacturer.SINGULARNAME'),
            'SilvercartAvailabilityStatus.Title'    => _t('SilvercartAvailabilityStatus.SINGULARNAME'),
            'isActiveString'                        => _t('SilvercartProduct.IS_ACTIVE'),
        );

        $this->extend('updateColumnsAvailable', $columnsAvailable);

        return $columnsAvailable;
    }

    /**
     * Returns all columns used for tabular search results display.
     * Defaults to all fields specified in {@link DataObject->summaryFields()}.
     * Fixes a bug in case of an empty summaryFields set.
     * 
     * @param array   $searchCriteria Limit fields by populating the 'ResultsAssembly' key
     * @param boolean $selectedOnly   Limit by 'ResultsAssempty
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.05.2012
     */
    public function getResultColumns($searchCriteria, $selectedOnly = true) {
        $summaryFields = $this->columnsAvailable();

        if ($selectedOnly &&
            isset($searchCriteria['ResultAssembly'])) {
            $resultAssembly = $searchCriteria['ResultAssembly'];
            if (!is_array($resultAssembly)) {
                $explodedAssembly   = explode(' *, *', $resultAssembly);
                $resultAssembly     = array();
                foreach ($explodedAssembly as $item) {
                    $resultAssembly[$item] = true;
                }
            }
            $intersectedSummaryFields = array_intersect_key($summaryFields, $resultAssembly);
            if (empty($intersectedSummaryFields)) {
                $summaryFields = singleton($this->modelClass)->summaryFields();;
            } else {
                $summaryFields = $intersectedSummaryFields;
            }
        }
        return $summaryFields;
    }

    /**
     * Logs into the CMS via CURL and returns the cookie variables.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 16.08.2011
     */
    protected function doCurlLogin() {
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, Director::absoluteURL(Director::baseURL()."Security/LoginForm"));
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLINFO_HEADER_OUT,true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER,true);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            sprintf(
                "Email=%s&Password=%s",
                SS_API_USERNAME,
                SS_API_PASSWORD
            )
        );
        $result1 = curl_exec($ch);
        $headers = preg_replace("/<html.*<\/html>/ims","",$result1);

        curl_close($ch);

        // Get the cookie vars
        $cookies = array();
        $headers = explode("\n",$headers);

        foreach ($headers as $h) {
            if (preg_match("/Set-Cookie: (.*)/",$h,$m)) {
                $vars = explode(";",$m[1]);

                foreach ($vars as $v) {
                    if (strpos($v, '=') !== false) {
                        $pieces = explode("=",trim($v));
                        $cookies[trim($pieces[0])] = trim($pieces[1]);
                    }
                }
            }
        }

        // Generate Cookie String
        $cStr = "";
        foreach ($cookies as $k => $v) {
            $cStr.= $k."=".$v.";";
        }

        return $cStr;
    }

    /**
     * Write a log message.
     *
     * @param string $logString String to log
     * @param string $filename  Name of logfile
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Patrick Schneider <pschneider@pixeltricks.de>
     * @copyright 2012 pixeltricks GmbH
     * @since 04.01.2012
     */
    protected function Log($logString, $filename = 'importProducts') {
        SilvercartConfig::Log('SilvercartProduct', $logString, $filename);
    }
}

/**
 * Default record controller for products
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 14.03.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProduct_RecordController extends SilvercartHasManyOrderField_RecordController {
    
    /**
     * Makes the record controller decoratable
     *
     * @param HttpRequest $request Request
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2012
     */
    public function handleAction($request) {
        $this->extend('onBeforeHandleAction', $request);
        $result             = false;
        $extensionResults   = $this->extend('handleAction', $request);
        if (is_array($extensionResults) &&
            count($extensionResults) > 0) {
            foreach ($extensionResults as $extensionResult) {
                if ($extensionResult !== false) {
                    $result = $extensionResult;
                    break;
                }
            }
        }
        if ($result === false) {
            $result = parent::handleAction($request);
        }
        $this->extend('onAfterHandleAction', $request);
        return $result;
    }

}
