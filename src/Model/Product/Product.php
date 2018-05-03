<?php

namespace SilverCart\Model\Product;

use DateTime;
use SilverCart\Admin\Forms\FileUploadField;
use SilverCart\Admin\Forms\ImageUploadField;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\SeoTools;
use SilverCart\Dev\Tools;
use SilverCart\Forms\AddToCartForm;
use SilverCart\Forms\FormFields\FieldGroup;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Order\ShoppingCartPosition;
use SilverCart\Model\Order\ShoppingCartPositionNotice;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Pages\ProductGroupPageController;
use SilverCart\Model\Pages\RegistrationPage;
use SilverCart\Model\Pages\SearchResultsPageController;
use SilverCart\Model\Product\AvailabilityStatus;
use SilverCart\Model\Product\File;
use SilverCart\Model\Product\Manufacturer;
use SilverCart\Model\Product\Image;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\ProductCondition;
use SilverCart\Model\Product\ProductTranslation;
use SilverCart\Model\Product\QuantityUnit;
use SilverCart\Model\Product\Tax;
use SilverCart\Model\Shipment\ShippingFee;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverCart\Model\Widgets\ProductGroupItemsWidget;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\Forms\TreeMultiselectField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\ORM\SS_List;
use SilverStripe\ORM\Filters\ExactMatchFilter;
use SilverStripe\ORM\Filters\PartialMatchFilter;
use SilverStripe\ORM\Search\SearchContext;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\View\ArrayData;
use SilverStripe\View\SSViewer;
use SilverStripe\Widgets\Model\WidgetArea;
use WidgetSets\Model\WidgetSet;

/**
 * abstract for a product.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Product extends DataObject implements PermissionProvider {

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
    private static $db = array(
        'isActive'                    => 'Boolean(1)',
        'ProductNumberShop'           => 'Varchar(50)',
        'ProductNumberManufacturer'   => 'Varchar(50)',
        'EANCode'                     => 'Varchar(13)',
        'PriceGross'                  => \SilverCart\ORM\FieldType\DBMoney::class, //price taxes including
        'PriceNet'                    => \SilverCart\ORM\FieldType\DBMoney::class, //price taxes excluded
        'MSRPrice'                    => \SilverCart\ORM\FieldType\DBMoney::class, //manufacturers recommended price
        'PurchasePrice'               => \SilverCart\ORM\FieldType\DBMoney::class, //the price the shop owner bought the product for
        'PurchaseMinDuration'         => 'Int',
        'PurchaseMaxDuration'         => 'Int',
        'PurchaseTimeUnit'            => 'Enum(",Days,Weeks,Months","")',
        'StockQuantity'               => 'Int',
        'StockQuantityOverbookable'   => 'Boolean(0)',
        'StockQuantityExpirationDate' => 'Date',
        'PackagingQuantity'           => 'Int',
        'Weight'                      => 'Float', //unit is gramm
        'ReleaseDate'                 => 'DBDatetime',
        'LaunchDate'                  => 'DBDatetime',
        'SalesBanDate'                => 'DBDatetime',
        'ExcludeFromPaymentDiscounts' => 'Boolean(0)',
        'IsNotBuyable'                => 'Boolean(0)',
    );

    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_one = array(
        'Tax'                => Tax::class,
        'Manufacturer'       => Manufacturer::class,
        'ProductGroup'       => ProductGroupPage::class,
        'MasterProduct'      => Product::class,
        'AvailabilityStatus' => AvailabilityStatus::class,
        'ProductCondition'   => ProductCondition::class,
        'QuantityUnit'       => QuantityUnit::class,
        'WidgetArea'         => WidgetArea::class,
    );

    /**
     * n:m relations
     *
     * @var array
     */
    private static $has_many = array(
        'ProductTranslations'   => ProductTranslation::class,
        'Images'                => Image::class,
        'Files'                 => File::class,
        'ShoppingCartPositions' => ShoppingCartPosition::class,
    );

    /**
     * Belongs-many-many relations.
     *
     * @var array
     */
    private static $many_many = array(
        'ProductGroupMirrorPages' => ProductGroupPage::class,
    );

    /**
     * m:n relations
     *
     * @var array
     */
    private static $belongs_many_many = array(
        'ShoppingCarts'            => ShoppingCart::class,
        'ProductGroupItemsWidgets' => ProductGroupItemsWidget::class,
    );
    
    /**
     * Adds database indexes
     * 
     * @var array 
     */
    private static $indexes = array(
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
    private static $casting = array(
        'isActiveString'              => 'Varchar(8)',
        'ProductMirrorGroupIDs'       => 'Text',
        'PriceIsLowerThanMsr'         => 'Boolean',
        'Title'                       => 'Text',
        'ShortDescription'            => 'Text',
        'LongDescription'             => 'HTMLText',
        'MetaDescription'             => 'Text',
        'MetaTitle'                   => 'Text',
        'MetaKeywords'                => 'Text',
        'Link'                        => 'Text',
        'AbsoluteLink'                => 'Text',
        'DefaultShippingFee'          => 'Text',
        'MSRPriceNice'                => 'Text',
        'BeforeProductHtmlInjections' => 'HTMLText',
        'AfterProductHtmlInjections'  => 'HTMLText',
    );

    /**
     * The default sorting.
     *
     * @var string
     */
    private static $default_sort = 'ProductNumberShop';

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartProduct';

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
     * @var ArrayList 
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
     * Cached Tax object. The related tax object will be stored in
     * this property after its first call.
     *
     * @var Tax
     */
    protected $cachedTax = null;
    
    /**
     * Cached AvailabilityStatus object. The related status object 
     * will be stored in this property after its first call.
     *
     * @var AvailabilityStatus
     */
    protected $cachedAvailabilityStatus = null;
    
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
     * @var SS_List
     */
    protected $images = null;
    
    /**
     * Determines whether to ignore tax exemption or not.
     *
     * @var bool 
     */
    protected $ignoreTaxExemption = false;
    
    /**
     * The first image out of the related Images.
     *
     * @var Image
     */
    protected $listImage = null;
    
    /**
     * List of already requested and localized i18n links.
     *
     * @var array
     */
    protected $i18nLinks = [];

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
        return Tools::singular_name_for($this);
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
        return Tools::plural_name_for($this);
    }

    /**
     * getter for the Title, looks for set translation
     * 
     * @return string The Title from the translation object or an empty string
     */
    public function getTitle() {
        $title = $this->getTranslationFieldValue('Title');
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
     */
    public function getShortDescription($includeHtml = true) {
        $shortDescription = $this->getTranslationFieldValue('ShortDescription');
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
     */
    public function getLongDescription($includeHtml = true) {
        $longDescription = $this->getTranslationFieldValue('LongDescription');
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
     */
    public function getMetaDescription() {
        $metaDescription = $this->getTranslationFieldValue('MetaDescription');
        if (!$this->getCMSFieldsIsCalled) {
            if (empty($metaDescription)) {
                $metaDescription = SeoTools::extractMetaDescriptionOutOfArray(
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
     */
    public function getMetaTitle() {
        $metaTitle = $this->getTranslationFieldValue('MetaTitle');
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
     */
    public function getMetaKeywords() {
        $metaKeywords = $this->getTranslationFieldValue('MetaKeywords');
        if (!$this->getCMSFieldsIsCalled) {
            if (empty($metaKeywords)) {
                $metaKeywords = SeoTools::extractMetaKeywords($this->getTitle());
            }
            $this->extend('updateMetaKeywords', $metaKeywords);
        }
        return $metaKeywords;
    }
    
    /**
     * Returns the default shipping fee for this product
     *
     * @param Country $country       Country to get fee for
     * @param Group   $customerGroup Group to get fee for
     * 
     * @return ShippingFee 
     */
    public function getDefaultShippingFee(Country $country = null, $customerGroup = null) {
        $shippingFee = '';
        if (!is_null($country)) {
            if (is_null($customerGroup)) {
                $customerGroup = Customer::default_customer_group();
            }
            $shippingFee = ShippingMethod::getAllowedShippingFeeFor($this, $country, $customerGroup, true);
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
        $image = $this->getImages()->first();
        if ($image instanceof Image &&
            $image->Image()->exists()) {
            
            $imageFile = $image->Image();
            $maxRatio  = 2.5;

            if ($imageFile->getWidth() > 0 &&
                $imageFile->getHeight() > 0) {
                
                $orientation = $imageFile->getOrientation();
                $ratio       = $imageFile->getWidth() / $imageFile->getHeight();

                if ($orientation == \SilverStripe\Assets\Image_Backend::ORIENTATION_LANDSCAPE &&
                    ($ratio <= $maxRatio ||
                     $imageFile->getWidth() < 400)) {
                    $hasPortraitOrientationImage = true;
                } elseif ($orientation == \SilverStripe\Assets\Image_Backend::ORIENTATION_PORTRAIT) {
                    $hasPortraitOrientationImage = true;
                } elseif ($orientation != \SilverStripe\Assets\Image_Backend::ORIENTATION_LANDSCAPE) {
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
            'SILVERCART_PRODUCT_CREATE' => _t(Product::class . '.SILVERCART_PRODUCT_CREATE', 'Can create products'),
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
    public function canCreate($member = null, $context = array()) {
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
            $member = Customer::currentUser();
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
        if (!Tools::isBackendEnvironment()) {
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
            'ListImageThumbnail'       => $this->fieldLabel(''),
            'ProductNumberShop'        => $this->fieldLabel('ProductNumberShop'),
            'Title'                    => $this->singular_name(),
            'ProductGroup.Title'       => $this->fieldLabel('ProductGroup'),
            'Manufacturer.Title'       => $this->fieldLabel('Manufacturer'),
            'AvailabilityStatus.Title' => $this->fieldLabel('AvailabilityStatus'),
            'isActiveString'           => $this->fieldLabel('isActive'),
            'PriceGross'               => $this->fieldLabel('PriceGross'),
            'PriceNet'                 => $this->fieldLabel('PriceNet'),
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
                'filter'    => PartialMatchFilter::class,
            ),
            'ProductTranslations.Title' => array(
                'title'     => $this->fieldLabel('Title'),
                'filter'    => PartialMatchFilter::class,
            ),
            'ProductTranslations.ShortDescription' => array(
                'title'     => $this->fieldLabel('ShortDescription'),
                'filter'    => PartialMatchFilter::class,
            ),
            'ProductTranslations.LongDescription' => array(
                'title'     => $this->fieldLabel('LongDescription'),
                'filter'    => PartialMatchFilter::class,
            ),
            'Manufacturer.Title' => array(
                'title'     => $this->fieldLabel('Manufacturer'),
                'filter'    => PartialMatchFilter::class,
             ),
            'ProductNumberManufacturer' => array(
                'title'     => $this->fieldLabel('ProductNumberManufacturer'),
                'filter'    => PartialMatchFilter::class,
             ),
            'isActive' => array(
                'title'     => $this->fieldLabel('isActive'),
                'filter'    => ExactMatchFilter::class,
            ),
            'ProductGroup.ID' => array(
                'title'     => $this->fieldLabel('ProductGroup'),
                'filter'    => ExactMatchFilter::class,
            ),
            'ProductGroupMirrorPages.ID' => array(
                'title'     => $this->fieldLabel('ProductGroupMirrorPages'),
                'filter'    => ExactMatchFilter::class,
            ),
            'AvailabilityStatus.ID' => array(
                'title'     => $this->fieldLabel('AvailabilityStatus'),
                'filter'    => ExactMatchFilter::class,
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
            $productTable     = Tools::get_table_name(Product::class);
            $translationTable = Tools::get_table_name(ProductTranslation::class);
            $sortableFrontendFields = array(
                $translationTable . '.Title ASC'  => $this->fieldLabel('TitleAsc'),
                $translationTable . '.Title DESC' => $this->fieldLabel('TitleDesc'),
            );
            if (Config::Pricetype() == 'gross') {
                $sortableFrontendFields = array_merge(
                        $sortableFrontendFields,
                        array(
                            $productTable . '.PriceGrossAmount ASC'  => $this->fieldLabel('PriceAmountAsc'),
                            $productTable . '.PriceGrossAmount DESC' => $this->fieldLabel('PriceAmountDesc'),
                        )
                );
            } else {
                $sortableFrontendFields = array_merge(
                        $sortableFrontendFields,
                        array(
                            $productTable . '.PriceNetAmount ASC'    => $this->fieldLabel('PriceAmountAsc'),
                            $productTable . '.PriceNetAmount DESC'   => $this->fieldLabel('PriceAmountDesc'),
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
                'Title'                                => _t(Product::class . '.COLUMN_TITLE', 'Title'),
                'LongDescription'                      => _t(Product::class . '.DESCRIPTION', 'Description'),
                'ShortDescription'                     => _t(Product::class . '.SHORTDESCRIPTION', 'Listdescription'),
                'manufacturer.Title'                   => Manufacturer::singleton()->singular_name(),
                'PurchasePrice'                        => _t(Product::class . '.PURCHASEPRICE', 'purchase price'),
                'PurchasePriceAmount'                  => _t(Product::class . '.PURCHASEPRICE', 'purchase price'),
                'PurchasePriceCurrency'                => _t(Product::class . '.PURCHASEPRICE_CURRENCY', 'purchase currency'),
                'MSRPrice'                             => _t(Product::class . '.MSRP', 'MSR price'),
                'MSRPriceAmount'                       => _t(Product::class . '.MSRP', 'MSR price'),
                'MSRPriceCurrency'                     => _t(Product::class . '.MSRP_CURRENCY', 'MSR currency'),
                'Price'                                => _t(Product::class . '.PRICE', 'price'),
                'PriceGross'                           => _t(Product::class . '.PRICE_GROSS', 'price (gross)'),
                'PriceGrossAmount'                     => _t(Product::class . '.PRICE_GROSS', 'price (gross)'),
                'PriceGrossCurrency'                   => _t(Product::class . '.PRICE_GROSS_CURRENCY', 'currency (gross)'),
                'PriceNet'                             => _t(Product::class . '.PRICE_NET', 'price (net)'),
                'PriceNetAmount'                       => _t(Product::class . '.PRICE_NET', 'price (net)'),
                'PriceNetCurrency'                     => _t(Product::class . '.PRICE_NET_CURRENCY', 'currency (net)'),
                'MetaDescription'                      => _t(Product::class . '.METADESCRIPTION', 'meta description'),
                'Weight'                               => _t(Product::class . '.WEIGHT', 'weight'),
                'MetaTitle'                            => _t(Product::class . '.METATITLE', 'meta title'),
                'MetaKeywords'                         => _t(Product::class . '.METAKEYWORDS', 'meta keywords'),
                'PackagingContent'                     => _t(ProductPage::class . '.PACKAGING_CONTENT', 'Content'),
                'ProductNumberShop'                    => _t(Product::class . '.PRODUCTNUMBER', 'Item number'),
                'ProductNumberShort'                   => _t(Product::class . '.PRODUCTNUMBER_SHORT', 'Item no.'),
                'ProductNumberManufacturer'            => _t(Product::class . '.PRODUCTNUMBER_MANUFACTURER', 'product number (manufacturer)'),
                'EANCode'                              => _t(Product::class . '.EAN', 'EAN'),
                'BasicData'                            => _t(Product::class . '.BasicData', 'Basic data'),
                'MiscGroup'                            => _t(RegistrationPage::class . '.OTHERITEMS', 'Miscellaneous'),
                'TimeGroup'                            => _t(Product::class . '.TimeGroup', 'Time Control'),
                'ReleaseDate'                          => _t(Product::class . '.ReleaseDate', 'Release Date'),
                'ReleaseDateInfo'                      => _t(Product::class . '.ReleaseDateInfo', 'Release Date Info'),
                'LaunchDate'                           => _t(Product::class . '.LaunchDate', 'Launch Date'),
                'LaunchDateInfo'                       => _t(Product::class . '.LaunchDateInfo', 'Launch Date Info'),
                'SalesBanDate'                         => _t(Product::class . '.SalesBanDate', 'Sale Ban Date'),
                'SalesBanDateInfo'                     => _t(Product::class . '.SalesBanDateInfo', 'Sale Ban Date Info'),
                'Tax'                                  => Tax::singleton()->singular_name(),
                'Manufacturer'                         => Manufacturer::singleton()->singular_name(),
                'ProductGroup'                         => ProductGroupPage::singleton()->singular_name(),
                'ProductGroups'                        => _t(ProductGroupPage::class . '.PLURALNAME', 'product groups'),
                'MasterProduct'                        => _t(Product::class . '.MASTERPRODUCT', 'master product'),
                'Image'                                => _t(Product::class . '.IMAGE', 'product image'),
                'AvailabilityStatus'                   => AvailabilityStatus::singleton()->singular_name(),
                'PurchaseMinDuration'                  => _t(Product::class . '.PURCHASE_MIN_DURATION', 'Min. purchase duration'),
                'PurchaseMaxDuration'                  => _t(Product::class . '.PURCHASE_MAX_DURATION', 'Max. purchase duration'),
                'PurchaseTimeUnit'                     => _t(Product::class . '.PURCHASE_TIME_UNIT', 'Purchase time unit'),
                'Files'                                => File::singleton()->plural_name(),
                'Images'                               => Image::singleton()->plural_name(),
                'File'                                 => File::singleton()->singular_name(),
                'Image'                                => Image::singleton()->singular_name(),
                'ShoppingCartPositions'                => _t(ShoppingCartPosition::class . '.PLURALNAME', 'Cart positions'),
                'ShoppingCarts'                        => _t(ShoppingCart::class . '.PLURALNAME', 'Carts'),
                'Orders'                               => _t(Order::class . '.PLURALNAME', 'Orders'),
                'ProductGroupMirrorPages'              => _t(Product::class . '.MirrorPage_PLURALNAME', 'Mirror-Productgroups'),
                'QuantityUnit'                         => _t(Product::class . '.AMOUNT_UNIT', 'amount Unit'),
                'isActive'                             => _t(Product::class . '.IS_ACTIVE', 'is active'),
                'StockQuantity'                        => _t(Product::class . '.STOCKQUANTITY', 'stock quantity'),
                'StockQuantityOverbookable'            => _t(Product::class . '.STOCK_QUANTITY', 'Is the stock quantity of this product overbookable?'),
                'StockQuantityOverbookableShort'       => _t(Product::class . '.STOCK_QUANTITY_SHORT', 'Is overbookable?'),
                'StockQuantityExpirationDate'          => _t(Product::class . '.STOCK_QUANTITY_EXPIRATION_DATE', 'Date from which on the stock quantity is no more overbookable'),
                'PackagingQuantity'                    => _t(Product::class . '.PACKAGING_QUANTITY', 'purchase quantity'),
                'ID'                                   => 'ID',
                'ProductTranslations'                  => _t(Config::class . '.TRANSLATIONS', 'Translations'),
                'ProductGroupItemsWidgets'             => _t(ProductGroupItemsWidget::class . '.CMS_PRODUCTGROUPTABNAME', 'Product Group'),
                'WidgetArea'                           => _t(Product::class . '.WIDGETAREA', 'Widgets'),
                'Prices'                               => _t(Product::class . '.PRICES', 'Prices'),
                'SEO'                                  => _t(Config::class . '.SEO', 'SEO'),
                'ProductCondition'                     => ProductCondition::singleton()->singular_name(),
                'TitleAsc'                             => _t(Product::class . '.TITLE_ASC', 'Title ascending'),
                'TitleDesc'                            => _t(Product::class . '.TITLE_DESC', 'Title descending'),
                'PriceAmountAsc'                       => _t(Product::class . '.PRICE_AMOUNT_ASC', 'Price ascending'),
                'PriceAmountDesc'                      => _t(Product::class . '.PRICE_AMOUNT_DESC', 'Price descending'),
                'DefaultShippingFee'                   => ShippingFee::singleton()->singular_name(),
                'RefreshCache'                         => _t(Product::class . '.RefreshCache', 'Refresh cache of this product on after write'),
                'ExcludeFromPaymentDiscounts'          => _t(Product::class . '.ExcludeFromPaymentDiscounts', 'This product is excluded from payment discounts.'),
                'AddImage'                             => _t(Product::class . '.AddImage', 'Add Image'),
                'AddFile'                              => _t(Product::class . '.AddFile', 'Add File'),
                'IsNotBuyable'                         => _t(Product::class . '.IsNotBuyable', 'Is not buyable'),
                'ProductTranslations.Title'            => _t(Product::class . '.COLUMN_TITLE', 'Title'),
                'ProductTranslations.ShortDescription' => _t(Product::class . '.SHORTDESCRIPTION', 'Listdescription'),
                'ProductTranslations.LongDescription'  => _t(Product::class . '.DESCRIPTION', 'Description'),
                'Manufacturer.Title'                   => _t(Product::class . '.COLUMN_TITLE', 'Title'),
                'ProductGroupMirrorPages.ID'           => _t(Product::class . '.PLURALNAME', 'Mirror-Productgroups'),
                'AvailabilityStatus.ID'                => AvailabilityStatus::singleton()->singular_name(),
                'Yes'                                  => Tools::field_label('Yes'),
                'No'                                   => Tools::field_label('No'),
                'Days'                                 => _t(Product::class . '.DAYS','Days'),
                'Weeks'                                => _t(Product::class . '.WEEKS','Weeks'),
                'Months'                               => _t(Product::class . '.MONTHS','Months'),
                'ChangeQuantity'                       => _t(Product::class . '.CHANGE_QUANTITY_CART', 'Change quantity'),
                'AddToCart'                            => _t(Product::class . '.ADD_TO_CART', 'Add to cart'),
                'Quantity'                             => _t(Product::class . '.QUANTITY', 'Quantity'),
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Returns YES when isActive is true, else it will return NO
     * (dependent on chosen language)
     *
     * @return string
     */
    public function getisActiveString() {
        $isActiveString = $this->fieldLabel('No');
        if ($this->isActive) {
            $isActiveString = $this->fieldLabel('Yes');
        }
        return $isActiveString;
    }

    /**
     * Returns the product condition. If none is defined at the product we
     * try to get the standard product condition as defined in the
     * Config.
     *
     * @return string
     */
    public function getCondition() {
        $condition = '';

        if ($this->ProductConditionID > 0) {
            $condition = $this->ProductCondition()->Title;
        } else {
            if (Config::getStandardProductCondition()) {
                $condition = Config::getStandardProductCondition()->Title;
            }
        }

        return $condition;
    }

    /**
     * Returns the default sort order and direction.
     *
     * @return string
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
            $sort                   = Tools::Session()->get('SilvercartProduct.defaultSort');
            $sortableFrontendFields = Product::singleton()->sortableFrontendFields();
            if (is_null($sort) ||
                $sort === false ||
                !is_string($sort) ||
                !array_key_exists($sort, $sortableFrontendFields)) {
                $sort = Product::config()->get('default_sort');
                self::setDefaultSort($sort);
                Product::config()->set('default_sort', '');
            } else {
                Product::config()->set('default_sort', '');
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
        Tools::Session()->set('SilvercartProduct.defaultSort', $defaultSort);
        Tools::saveSession();
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
    public static function get($callerClass = Product::class, $filter = "", $sort = "", $join = "", $limit = null, $containerClass = DataList::class) {
        $products = parent::get($callerClass, $filter, $sort, $join, $limit, $containerClass);
        
        if (!Tools::isBackendEnvironment() &&
            !Tools::isIsolatedEnvironment()) {
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
     * @return Product
     */
    public static function get_by_product_number($productNumber) {
        $product = self::get()
                ->filter('ProductNumberShop', $productNumber)
                ->first();
        if (is_null($product)) {
            $product = Product::singleton();
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
        $pricetype          = Config::Pricetype();
        $exclude            = array();
        
        $requiredAttributes[] = 'isActive';

        if (!empty($requiredAttributes)) {
            foreach ($requiredAttributes as $requiredAttribute) {
                //find out if we are dealing with a real attribute or a multilingual field
                if (array_key_exists($requiredAttribute, Product::config()->get('db')) || $requiredAttribute == "Price") {
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
                $fieldName = sprintf('"%s"."ID"', DataObject::getSchema()->baseDataClass(Product::class));
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
        $pricetype          = Config::Pricetype();
        $filter             = "";

        if (!empty($requiredAttributes)) {
            foreach ($requiredAttributes as $requiredAttribute) {
                //find out if we are dealing with a real attribute or a multilingual field
                if (array_key_exists($requiredAttribute, Product::config()->get('db')) || $requiredAttribute == "Price") {
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
                    $filter .= sprintf("%s.%s != '' AND ", Tools::get_table_name(ProductTranslation::class), $requiredAttribute);
                }
                
            }
        }
        $filter .= 'isActive = 1 AND ProductGroupID > 0';
        
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
     * @return SS_List
     */
    public static function getProducts($whereClause = "", $sort = null, $joins = null, $limit = null) {
        $filter = self::get_frontend_sql_filter();

        if ($whereClause != "") {
            $filter = $filter . ' AND ' . $whereClause;
        }


        if ($sort === null) {
            $sort = self::defaultSort();
        }
        
        $productTable = Tools::get_table_name(Product::class);
        $onclause = sprintf('"SPL"."ProductID" = "%s"."ID" AND "SPL"."Locale" = \'%s\'', $productTable, Tools::current_locale());
        $databaseFilteredProducts = Product::get()
                ->leftJoin(Tools::get_table_name(ProductTranslation::class), $onclause, 'SPL')
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
     * @return PaginatedList
     */
    public static function getPaginatedProducts($whereClause = "", $sort = null, $joins = null, $limit = null, $request = null, $pageLength = null) {
        $paginatedProducts = null;
        if (is_null($request)) {
            $request = $_GET;
        }
        if (is_null($pageLength)) {
            $pageLength = Config::ProductsPerPage();
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
                'Tax',
                'Manufacturer',
                'AvailabilityStatus',
                'QuantityUnit',
                'ProductCondition',
                'Files',
                'Orders',
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

        return DataObjectExtension::scaffoldFormFields($this, $params);
    }

    /**
     * Adds the fields for the MirrorProductGroups tab
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    public function getFieldsForProductGroups($fields) {
        $productGroupHolder = Tools::PageByIdentifierCode('SilvercartProductGroupHolder');

        $silvercartProductGroupDropdown = new TreeDropdownField(
                'ProductGroupID',
                $this->fieldLabel('ProductGroup'),
                SiteTree::class
        );

        if ($productGroupHolder) {
            $productGroupHolderID = $productGroupHolder->ID;
        } else {
            $productGroupHolderID = 0;
        }
        $silvercartProductGroupDropdown->setTreeBaseID($productGroupHolderID);
        
        if ($this->exists()) {
            $productGroupMirrorPagesField   = new TreeMultiselectField(
                    'ProductGroupMirrorPages',
                    $this->fieldLabel('ProductGroupMirrorPages'),
                    SiteTree::class
            );
            $productGroupMirrorPagesField->setTreeBaseID($productGroupHolderID);

            $fields->removeByName('ProductGroupMirrorPages');
            $fields->insertBefore($silvercartProductGroupDropdown, 'ProductNumberGroup');
            $fields->insertAfter($productGroupMirrorPagesField, 'ProductGroupID');
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
        $fields->dataFieldByName('StockQuantityExpirationDate')->config()->set('showcalendar', true);
        $fields->dataFieldByName('StockQuantityExpirationDate')->FieldHolder();
        $purchaseTimeUnitSource = array(
            'Days'      => $this->fieldLabel('Days'),
            'Weeks'     => $this->fieldLabel('Weeks'),
            'Months'    => $this->fieldLabel('Months'),
        );
        $fields->dataFieldByName('PurchaseTimeUnit')->setSource($purchaseTimeUnitSource);
        
        $productNumberGroup = new FieldGroup('ProductNumberGroup', '', $fields);
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

        $availabilityGroup  = new FieldGroup('AvailabilityGroup', '', $fields);
        $availabilityGroup->push(           $fields->dataFieldByName('AvailabilityStatusID'));
        $availabilityGroup->breakAndPush(   $fields->dataFieldByName('PurchaseMinDuration'));
        $availabilityGroup->push(           $fields->dataFieldByName('PurchaseMaxDuration'));
        $availabilityGroup->push(           $fields->dataFieldByName('PurchaseTimeUnit'));
        $availabilityGroup->breakAndPush(   $fields->dataFieldByName('StockQuantity'));
        $availabilityGroup->push(           $fields->dataFieldByName('StockQuantityOverbookable'));
        $availabilityGroup->push(           $fields->dataFieldByName('StockQuantityExpirationDate'));
        $availabilityGroupToggle = ToggleCompositeField::create(
                'AvailabilityGroupToggle',
                $this->fieldLabel('AvailabilityStatus'),
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
        
        $timeGroup = new FieldGroup('TimeGroup', '', $fields);
        $timeGroup->push(        $fields->dataFieldByName('ReleaseDate'));
        $timeGroup->pushAndBreak(new LiteralField('ReleaseDateInfo', '<br/><br/>' . $this->fieldLabel('ReleaseDateInfo')));
        $timeGroup->push(        $fields->dataFieldByName('LaunchDate'));
        $timeGroup->pushAndBreak(new LiteralField('LaunchDateInfo', '<br/><br/>' . $this->fieldLabel('LaunchDateInfo')));
        $timeGroup->push(        $fields->dataFieldByName('SalesBanDate'));
        $timeGroup->pushAndBreak(new LiteralField('SalesBanDateInfo', '<br/><br/>' . $this->fieldLabel('SalesBanDateInfo')));
        $timeGroupToggle = ToggleCompositeField::create(
                'TimeGroupToggle',
                $this->fieldLabel('TimeGroup'),
                array(
                    $timeGroup,
                )
        )->setHeadingLevel(4)->setStartClosed(true);
        $fields->insertAfter($timeGroupToggle, 'ProductDescriptionToggle');
        
        $miscGroup = new FieldGroup('MiscGroup', '', $fields);
        $manufactuerField = $fields->dataFieldByName('ManufacturerID');
        if (!is_null($manufactuerField)) {
            $miscGroup->pushAndBreak($manufactuerField);
        }
        $miscGroup->breakAndPush(   $fields->dataFieldByName('ExcludeFromPaymentDiscounts'));
        $miscGroup->breakAndPush(   $fields->dataFieldByName('PackagingQuantity'));
        $miscGroup->pushAndBreak(   $fields->dataFieldByName('QuantityUnitID'));
        $miscGroup->breakAndPush(   $fields->dataFieldByName('Weight'));
        $miscGroup->breakAndPush(   $fields->dataFieldByName('ProductConditionID'));
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
        Tax::presetDropdownWithDefault($fields->dataFieldByName('TaxID'), $this);
        
        $pricesGroup  = new FieldGroup('PricesGroup', '', $fields);
        $pricesGroup->push($fields->dataFieldByName('PriceGross'));
        $pricesGroup->push($fields->dataFieldByName('PriceNet'));
        $pricesGroup->push($fields->dataFieldByName('MSRPrice'));
        $pricesGroup->push($fields->dataFieldByName('PurchasePrice'));
        $pricesGroup->push($fields->dataFieldByName('TaxID'));
        
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
        $imageGridField = $fields->dataFieldByName('Images');
        $imageGridField->getConfig()->removeComponentsByType(GridFieldAddNewButton::class);
        $imageGridField->getConfig()->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
        $imageGridField->getConfig()->addComponent(new GridFieldDeleteAction());
        
        if (class_exists('GridFieldSortableRows')) {
            $imageGridField->getConfig()->addComponent(new GridFieldSortableRows('SortOrder'));
        }
        
        $imageUploadField = new ImageUploadField('UploadImages', $this->fieldLabel('AddImage'));
        $imageUploadField->setFolderName('assets/product-images');
        
        $fields->addFieldToTab('Root.Images', $imageUploadField, 'Images');
    }

    /**
     * Adds or modifies the fields for the Files tab
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    public function getFieldsForFiles($fields) {
        $fileGridField = $fields->dataFieldByName('Files');
        $fileGridField->getConfig()->removeComponentsByType(GridFieldAddNewButton::class);
        $fileGridField->getConfig()->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
        $fileGridField->getConfig()->addComponent(new GridFieldDeleteAction());
        
        $fileUploadField = new FileUploadField('UploadFiles', $this->fieldLabel('AddFile'));
        $fileUploadField->setFolderName('assets/product-files');
        
        $fields->addFieldToTab('Root.Files', $fileUploadField, 'Files');
    }
    
    /**
     * CMS fields of a product
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $this->getCMSFieldsIsCalled = true;
        $fields = DataObjectExtension::getCMSFields($this, 'isActive');
        
        $fields->removeByName('ProductGroupItemsWidgets');
        $fields->removeByName('MasterProductID');
        
        $this->getFieldsForMain($fields);
        $this->getFieldsForPrices($fields);
        $this->getFieldsForProductGroups($fields);
        $this->getFieldsForSeo($fields);
        if ($this->exists()) {
            $this->getFieldsForWidgets($fields);
            $this->getFieldsForImages($fields);
            $this->getFieldsForFiles($fields);
        }
        
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    /**
     * Returns an HTML encoded long description, preserving HTML tags.
     *
     * @return string
     */
    public function getHtmlEncodedLongDescription() {
        $output = str_replace(
            array(
                '&lt;',
                '&gt;'
            ),
            array(
                '<',
                '>'
            ),
            htmlentities($this->LongDescription, ENT_NOQUOTES, 'UTF-8', false)
        );

        return Tools::string2html($output);
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
            'ShoppingCartPositions',
            'WidgetArea',
            'ShoppingCarts',
            'Orders',
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
     */
    public function getHtmlEncodedShortDescription($cutToLength = false) {
        $output = str_replace(
            array(
                '&lt;',
                '&gt;'
            ),
            array(
                '<',
                '>'
            ),
            htmlentities($this->ShortDescription, ENT_NOQUOTES, 'UTF-8', false)
        );

        if ($cutToLength !== false) {
            $line = $output;
            if (preg_match('/^.{1,'.$cutToLength.'}\b/s', $output, $match)) {
                $line = $match[0];
            }

            $output = $line;
        }

        return Tools::string2html($output);
    }

    /**
     * Getter for product price
     * May be decorated by the module silvercart_graduatedprices
     *
     * @param string $priceType          Set to 'gross' or 'net' to get the desired prices.
     *                                   If not given the price type will be automatically determined.
     * @param bool   $ignoreTaxExemption Determines whether to ignore tax exemption or not.
     *
     * @return DBMoney
     */
    public function getPrice($priceType = '', $ignoreTaxExemption = false) {
        $cacheHash = md5($priceType);
        $cacheKey = 'getPrice_' . $cacheHash . '_' . $ignoreTaxExemption ? '1' : '0';

        if (array_key_exists($cacheKey, $this->cacheHashes)) {
            return $this->cacheHashes[$cacheKey];
        }

        if (empty($priceType)) {
            $priceType = Config::PriceType();
        }
        
        if ($priceType == "net") {
            $price = clone $this->PriceNet;
        } elseif ($priceType == "gross") {
            $price = clone $this->PriceGross;
        } else {
            $price = clone $this->PriceGross;
        }
        
        $member = Customer::currentUser();
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
     * @return SearchContext
     */
    public function getCustomSearchContext() {
        $fields = $this->scaffoldSearchFields(
            array(
                'restrictFields' => array(
                    'Title',
                    'LongDescription',
                    'Manufacturer.Title'
                )
            )
        );
        $filters = array(
            'Title' => new PartialMatchFilter('Title'),
            'LongDescription' => new ExactMatchFilter('LongDescription'),
            'Manufacturer.Title' => new PartialMatchFilter('Manufacturer.Title')
        );
        return new SearchContext(get_class($this), $fields, $filters);
    }

    /**
     * get some random products to fill a controller every now and then
     *
     * @param integer $amount        How many products should be returned?
     * @param boolean $masterProduct Should only master products be returned?
     *
     * @return PaginatedList
     */
    public static function getRandomProducts($amount = 4, $masterProduct = true) {
        if ($masterProduct) {
            return self::get()->filter('MasterProductID', 0)->sort('RAND()')->limit($amount);
        } else {
            return self::get()->sort('RAND()')->limit($amount);
        }
    }

    /**
     * get all required attributes as an array.
     *
     * @return array the attributes required to display an product in the frontend
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
     * @return string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    private function title2urlSegment() {
        return Tools::string2urlSegment($this->Title);
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
     * @return ShoppingCartPosition
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.06.2014
     */
    public function addToCart($cartID, $quantity = 1, $increment = false) {
        $addToCartAllowed = true;

        $this->extend('updateAddToCart', $addToCartAllowed);
        
        if ($this->IsNotBuyable ||
            ($quantity == 0 || $cartID == 0) ||
            !$addToCartAllowed ||
            !$this->isBuyableDueToStockManagementSettings()) {
            return false;
        }

        $shoppingCartPosition = ShoppingCartPosition::get()->filter(array(
            'ProductID' => $this->ID,
            'ShoppingCartID' => $cartID,
        ))->first();

        if (!($shoppingCartPosition instanceof ShoppingCartPosition) ||
            !$shoppingCartPosition->exists()) {
            $shoppingCartPosition = new ShoppingCartPosition();

            $shoppingCartPosition->castedUpdate(
                array(
                    'ShoppingCartID' => $cartID,
                    'ProductID' => $this->ID
                )
            );
            $shoppingCartPosition->write();
            $shoppingCartPosition = ShoppingCartPosition::get()->filter(array(
                'ProductID' => $this->ID,
                'ShoppingCartID' => $cartID,
            ))->first();
        }
        
        $positionNotice = null;
        if ($shoppingCartPosition->Quantity < $quantity) {
            $quantityToAdd = $quantity - $shoppingCartPosition->Quantity;
            if ($shoppingCartPosition->isQuantityIncrementableBy($quantityToAdd)) {
                if ($quantity > Config::addToCartMaxQuantity()) {
                    $shoppingCartPosition->Quantity = Config::addToCartMaxQuantity();
                    $positionNotice = 'maxQuantityReached';
                } else {
                    $shoppingCartPosition->Quantity = $quantity;
                }
            } elseif ($this->StockQuantity > 0) {
                if ($shoppingCartPosition->Quantity + $this->StockQuantity > Config::addToCartMaxQuantity()) {
                    $shoppingCartPosition->Quantity = Config::addToCartMaxQuantity();
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

        if ($shoppingCartPosition instanceof ShoppingCartPosition) {
            $shoppingCartPosition->write();
            if (!is_null($positionNotice)) {
                ShoppingCartPositionNotice::setNotice($shoppingCartPosition->ID, $positionNotice);
            }
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
        if ($this->getPositionInCart($cartID) instanceof ShoppingCartPosition) {
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
     * @return ShoppingCartPosition
     */
    public function getPositionInCart($cartID = null) {
        if (is_null($cartID) &&
            Customer::currentUser() instanceof Member) {
            $cartID = Customer::currentUser()->getCart()->ID;
        }
        if (!array_key_exists($cartID, $this->positionInCart)) {
            $this->positionInCart[$cartID] = ShoppingCartPosition::get()->filter(array(
                'ProductID' => $this->ID,
                'ShoppingCartID' => $cartID,
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
     */
    public function getQuantityInCart($cartID = null) {
        if (!array_key_exists($cartID, $this->quantityInCart)) {
            $quantityInCart = 0;
            $position       = $this->getPositionInCart($cartID);
            $precision      = 0;
            if ($this->QuantityUnit()->isInDb()) {
                $precision = $this->QuantityUnit()->numberOfDecimalPlaces;
            }
            if ($position instanceof ShoppingCartPosition) {
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
            Customer::currentUser() instanceof Member) {
            $cartID = Customer::currentUser()->getCart()->ID;
        }
        if (!array_key_exists($cartID, $this->quantityInCartString)) {
            $quantityInCartString = '';
            if ($this->isInCart($cartID)) {
                $quantityInCartString = _t(Product::class .  '.QUANTITY_IS_IN_CART',
                        '{quantity} {unit} already in cart',
                        [
                            'quantity' => $this->getQuantityInCart($cartID),
                            'unit'     => $this->QuantityUnit()->Title,
                        ]
                );
            }
            $this->quantityInCartString[$cartID] = $quantityInCartString;
            $this->extend('updateQuantityInCartString', $this->quantityInCartString[$cartID]);
        }
        return $this->quantityInCartString[$cartID];
    }

    /**
     * Returns the product group of this product dependent on the current locale
     *
     * @return ProductGroupPage
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.12.2015
     */
    public function ProductGroup() {
        $productGroup  = null;
        $currentLocale = Tools::current_locale();
        if ($this->getComponent('ProductGroup')) {
            $productGroup = $this->getComponent('ProductGroup');
            if ($productGroup->Locale != $currentLocale &&
                Tools::has_translation($productGroup, $currentLocale)) {
                $productGroup = Tools::get_translation($productGroup, $currentLocale);
            }
        }
        return $productGroup;
    }
    
    /**
     * Builds the product link with the given parameters.
     * 
     * @param ProductGroupPage $productGroup Base object to build the link
     * @param string           $urlSegment   URL segment
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2018
     */
    public function buildLinkWithGroup($productGroup, $urlSegment) {
        return $this->buildLink($productGroup->OriginalLink(), $urlSegment);
    }
    
    /**
     * Builds the product link with the given parameters.
     * 
     * @param string $groupLink  Link of the group to get product link for
     * @param string $urlSegment URL segment
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2018
     */
    public function buildLink($groupLink, $urlSegment) {
        $link = '';
        $linkIdentifier = $this->ID;
        $this->extend('updatelinkIdentifier', $linkIdentifier);
        
        $link = $groupLink . $linkIdentifier . '/' . $urlSegment;
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
     * @param string $locale Locale to get product link for
     *
     * @return string URL of $this
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Ramon Kupper <rkupper@pixeltricks.de>
     * @since 26.04.2018
     */
    public function Link($locale = null) {
        if (is_null($locale)) {
            $locale = Tools::current_locale();
        }
        if (array_key_exists($locale, $this->i18nLinks)) {
            return $this->i18nLinks[$locale];
        }
        
        $controller           = Controller::curr();
        $productGroup         = $controller->data();
        
        if ($controller instanceof ProductGroupPageController &&
            !($controller instanceof SearchResultsPageController)) {

            $buildLink = false;

            if ($this->ProductGroupMirrorPages()->find('ID', $productGroup->ID)) {
                $buildLink = true;
            } elseif (Tools::current_locale() != Config::DefaultLanguage()) {
                $productGroupTranslation = Tools::get_translation($productGroup, Config::DefaultLanguage());
                if ($this->ProductGroupMirrorPages()->find('ID', $productGroupTranslation->ID)) {
                    $buildLink = true;
                }
            }

            if ($buildLink) {
                $translation          = $this->getTranslationFor($locale);
                $i18nURLSegment       = $this->title2urlSegment();
                $i18nProductGroupLink = $productGroup->LocaleOriginalLink($locale);
                if ($translation instanceof ProductTranslation) {
                    $i18nURLSegment = Tools::string2urlSegment($translation->Title);
                }
                $i18nLink = $this->buildLink($i18nProductGroupLink, $i18nURLSegment);
            }
        }
        if (empty($i18nLink) &&
            $this->ProductGroup()) {
            $i18nLink = $this->buildLinkWithGroup($this->ProductGroup(), $this->title2urlSegment());
        }
        $this->i18nLinks[$locale] = $i18nLink;
        
        return $i18nLink;
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
        if ($this->ProductGroup()) {
            $link = $this->buildLinkWithGroup($this->ProductGroup(), $this->title2urlSegment());
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
        return Tools::PageByIdentifierCodeLink('SilvercartContactFormPage') . 'productQuestion/' . $this->ID;
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

        if (Controller::curr() instanceof ProductGroupPageController &&
            !Controller::curr() instanceof SearchResultsPageController &&
            $this->ProductGroupID == Controller::curr()->data()->ID) {
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
        $member          = Customer::currentUser();

        if ($member) {
            if ($member->showPricesGross(true)) {
                $showPricesGross = true;
            }
        } else {
            $defaultPriceType = Config::DefaultPriceType();

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
        if ($this->AvailabilityStatus()->exists()) {
            $offers['availability'] = $this->AvailabilityStatus()->MicrodataCode;
        }
        if ($this->ProductCondition()->exists()) {
            $offers['itemCondition'] = $this->ProductCondition()->MicrodataCode;
        }

        
        $listImage = $this->getListImage();
        $imageURL  = '';
        if (is_object($listImage)) {
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
        
        $manufacturer = $this->Manufacturer();
        if ($manufacturer instanceof Manufacturer &&
            $manufacturer->exists()) {
            $manufacturerData = array(
                '@type' => 'Thing',
                'name'  => $manufacturer->Title,
            );
            if ($manufacturer->Logo()->exists()) {
                $manufacturerData["logo"] = $manufacturer->Logo()->getAbsoluteURL();
            }
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
        $output = null;
        if ($this->AvailabilityStatus()) {
            if ($this->AvailabilityStatus()->Code == 'not-available'
             && !empty($this->PurchaseTimeUnit)
             && (!empty($this->PurchaseMinDuration)
              || !empty($this->PurchaseMaxDuration))) {
                $class = 'available-in ' . $baseCssClass . ' ' . $baseCssClass . '-warning';
                if (empty($this->PurchaseMinDuration)) {
                    $title = _t(AvailabilityStatus::class . '.STATUS_AVAILABLE_IN',
                            'available in {duration} {timeunit}',
                            [
                                'duration' => $this->PurchaseMaxDuration,
                                'timeunit' => $this->fieldLabel($this->PurchaseTimeUnit),
                            ]
                    );
                } elseif (empty($this->PurchaseMaxDuration)) {
                    $title = _t(AvailabilityStatus::class . '.STATUS_AVAILABLE_IN',
                            'available in {duration} {timeunit}',
                            [
                                'duration' => $this->PurchaseMinDuration,
                                'timeunit' => $this->fieldLabel($this->PurchaseTimeUnit),
                            ]
                    );
                } else {
                    $title = _t(AvailabilityStatus::class . '.STATUS_AVAILABLE_IN_MIN_MAX',
                            'available within {minduration} to {maxduration} {timeunit}',
                            [
                                'minduration' => $this->PurchaseMinDuration,
                                'maxduration' => $this->PurchaseMaxDuration,
                                'timeunit' => $this->fieldLabel($this->PurchaseTimeUnit),
                            ]
                    );
                }
            } else {
                $class = $this->AvailabilityStatus()->Code . ' ' . $baseCssClass . ' ' . $baseCssClass . '-'.$this->AvailabilityStatus()->badgeColor;
                $title = $this->AvailabilityStatus()->Title;
            }
            $output = $this->customise(new ArrayData(array(
                'AvailabilityCSSClasses' => $class . ' ' . $additionalCssClasses,
                'Title'                  => $title,
            )))->renderWith(SSViewer::get_templates_by_class(static::class, '_Availability'));
        }
        return $output;
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
     * Returns the related AvailabilityStatus object.
     * Provides an extension hook to update the status object by decorator.
     * 
     * @return AvailabilityStatus
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2014
     */
    public function AvailabilityStatus() {
        if (is_null($this->cachedAvailabilityStatus)) {
            $this->cachedAvailabilityStatus = $this->getComponent('AvailabilityStatus');
            $this->extend('updateAvailabilityStatus', $this->cachedAvailabilityStatus);
            if (!$this->cachedAvailabilityStatus instanceof AvailabilityStatus ||
                !$this->cachedAvailabilityStatus->exists()) {
                $default = AvailabilityStatus::getDefault();
                if ($default instanceof AvailabilityStatus &&
                    $default->exists()) {
                    $this->cachedAvailabilityStatus = $default;
                }
            }
        }
        return $this->cachedAvailabilityStatus;
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

        if ($this->AvailabilityStatusID > 0) {
            $showAvailability = true;
        }

        return $showAvailability;
    }

    /**
     * used to determine weather something should be shown on a template or not
     *
     * @return bool
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.3.2011
     */
    public function showPricesGross() {
        $pricetype = Config::Pricetype();
        if ($pricetype == "gross") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the tax rate in percent. The attribute 'Rate' of the relation
     * 'Tax' is not used to handle with complex tax systems without
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
        return $this->Tax()->getTaxRate($ignoreTaxExemption);
    }

    /**
     * Returns the related Tax object.
     * Provides an extension hook to update the tax object by decorator.
     * 
     * @return Tax
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.01.2013
     */
    public function Tax() {
        if (is_null($this->cachedTax)) {
            $this->cachedTax = $this->getComponent('Tax');
            $this->extend('updateTax', $this->cachedTax);
        }
        return $this->cachedTax;
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
        
        foreach ($this->ShoppingCartPositions() as $position) {
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
        
        if ($this->ProductGroup()) {
            $translations = Tools::get_translations($this->ProductGroup());
            if ($translations) {
                foreach ($translations as $translation) {
                    if ($this->ProductGroupMirrorPages()->find('ID', $translation->ID)) {
                        continue;
                    }
                    $this->ProductGroupMirrorPages()->add($translation);
                }
            }
        }
        
        if (array_key_exists('StockQuantity', $this->original)) {
            $stockQuantityBefore = $this->original['StockQuantity'];
            $this->checkForAvailabilityStatusChange($stockQuantityBefore, false);
        }
        
        if (!$this->isActive) {
            foreach ($this->ShoppingCartPositions() as $position) {
                $position->delete();
            }
        }
        
        if ($this->TaxID == 0) {
            $defaultTaxRate = Tax::getDefault();
            if ($defaultTaxRate instanceof Tax &&
                $defaultTaxRate->exists()) {
                $this->TaxID = $defaultTaxRate->ID;
            }
        }
        
        $priceGrossCurrency = $this->PriceGross->getCurrency();
        if (is_null($this->PriceGrossCurrency) &&
            is_null($priceGrossCurrency)) {
            $this->PriceGrossCurrency = Config::DefaultCurrency();
        }
        $priceNetCurrency = $this->PriceNet->getCurrency();
        if (is_null($this->PriceNetCurrency) &&
            is_null($priceNetCurrency)) {
            $this->PriceNetCurrency = Config::DefaultCurrency();
        }
        $MSRPriceCurrency = $this->MSRPrice->getCurrency();
        if (is_null($this->MSRPriceCurrency) &&
            is_null($MSRPriceCurrency)) {
            $this->MSRPriceCurrency = Config::DefaultCurrency();
        }
        $purchasePriceCurrency = $this->PurchasePrice->getCurrency();
        if (is_null($this->PurchasePriceCurrency) &&
            is_null($purchasePriceCurrency)) {
            $this->PurchasePriceCurrency = Config::DefaultCurrency();
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
        if ($this->ProductGroup()->exists()) {
            $this->ProductGroup()->updateLastEditedForCache();
        }
        foreach ($this->ProductGroupMirrorPages() as $productGroup) {
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
            'TaxID',
            'ManufacturerID',
            'ProductGroupID',
            'AvailabilityStatusID',
            'ProductConditionID',
            'QuantityUnitID',
        );
        $this->extend('updateCacheRelevantFields', $cacheRelevantFields);
        return $cacheRelevantFields;
    }

    /**
     * Returns a ArrayList of attributed images. If there are no images
     * attributed the method checks if there's a standard no-image
     * visualitation defined in Config and returns the defined image
     * as ArrayList. As last resort boolean false is returned.
     *
     * @return SS_List
     */
    public function getImages() {
        if (is_null($this->images)) {
            $images = false;
            $this->extend('overwriteImages', $images);
            if ($images == false) {
                $images = $this->Images();
                
                $this->extend('updateGetImages', $images);

                if ($images->count() > 0) {
                    $existingImages = new ArrayList();
                    foreach ($images as $image) {
                        if (!$image->Image()->exists()) {
                            $noImageObj = Config::getNoImage();

                            if ($noImageObj) {
                                $image = new Image();
                                $image->ImageID   = $noImageObj->ID;
                                $image->ProductID = $this->ID;
                            }
                        }
                        $existingImages->push($image);
                    }
                    $images = $existingImages;
                }
            }
            if (!($images instanceof ArrayList) ||
                $images->count() == 0) {
               
                $noImageObj = Config::getNoImage();
                if ($noImageObj->exists()) {
                    $image = Image::get()->filter('ImageID', $noImageObj->ID)->first();
                    if (!($image instanceof Image) ||
                        !$image->exists()) {
                        
                        $image = new Image();
                        $image->ImageID = $noImageObj->ID;
                        $image->write();
                    }
                    $images = new ArrayList();
                    $images->push($image);
                }
            }
            
            $this->images = $images;
        }

        return $this->images;
    }

    /**
     * Returns $this->getImages() without the first image.
     *
     * @return DataList|ArrayList
     */
    public function getThumbnails() {
        $images = $this->getImages();

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
     * Returns the first image out of the related Images.
     * 
     * @return Image
     */
    public function getListImage() {
        if (is_null($this->listImage)) {
            $this->listImage = false;
            $images = $this->getImages();

            if ($images->count() > 0) {
                $this->listImage = $images->first()->Image();
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
        $produtcTable = Tools::get_table_name(Product::class);
        DB::query("LOCK TABLES " . $produtcTable . " WRITE");
        DB::query(
                sprintf(
                        "UPDATE " . $produtcTable . " SET StockQuantity=StockQuantity%s%s WHERE ID = '%s'",
                        $operator,
                        $quantity,
                        $this->ID
                )
        );
        $results = DB::query(
                sprintf(
                        "SELECT StockQuantity FROM " . $produtcTable . " WHERE ID = '%s'",
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
            if (Config::EnableStockManagement()) {
                if (!Config::isStockManagementOverbookable() &&
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
        if (Customer::currentUser() && Customer::currentUser()->getCart()) {
            $cartPositionQuantity = Customer::currentUser()->getCart()->getQuantity($this->ID);
        }
        if (Config::EnableStockManagement()) {
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
            $newStatus = AvailabilityStatus::get_negative_status();
            if ($newStatus instanceof AvailabilityStatus) {
                $this->AvailabilityStatusID = $newStatus->ID;
                if ($doWrite) {
                    $this->write();
                }
            }
        } elseif ($this->StockQuantity > 0 &&
            $stockQuantityBefore <= 0) {
            // check for automatic positive availability status and set it.
            $newStatus = AvailabilityStatus::get_positive_status();
            if ($newStatus instanceof AvailabilityStatus) {
                $this->AvailabilityStatusID = $newStatus->ID;
                if ($doWrite) {
                    $this->write();
                }
            }
        }
    }

    /**
     * Returns a string of comma separated IDs of the attributed
     * ProductGroupMirror objects.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.08.2011
     */
    public function getProductMirrorGroupIDs() {
        $idListArray = array();
        $idList      = '';

        if ($this->ProductGroupMirrorPages()) {
            foreach ($this->ProductGroupMirrorPages() as $productGroupMirrorPage) {
                $idListArray[] = $productGroupMirrorPage->ID;
                unset($productGroupMirrorPage);
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
            $this->pluggedInTabs = new ArrayList();
            $this->extend('addPluggedInTab', $this->pluggedInTabs);
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
            $this->pluggedInProductMetaData = new ArrayList();
            $this->extend('addPluggedInProductMetaData', $this->pluggedInProductMetaData);
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
            $this->pluggedInProductListAdditionalData = new ArrayList();
            $this->extend('addPluggedInProductListAdditionalData', $this->pluggedInProductListAdditionalData);
        }
        return $this->pluggedInProductListAdditionalData;
    }
    
    /**
     * Returns all additional information to display between Images and Content.
     * 
     * @return ArrayList 
     */
    public function getPluggedInAfterImageContent() {
        if (is_null($this->pluggedInAfterImageContent)) {
            $this->pluggedInAfterImageContent = new ArrayList();
            $this->extend('addPluggedInAfterImageContent', $this->pluggedInAfterImageContent);
        }
        return $this->pluggedInAfterImageContent;
    }
    
    /**
     * Returns the AddToCartForm.
     * 
     * @param string $viewContext View context (for example 'List', 'Detail', 'Title')
     * 
     * @return AddToCartForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2017
     */
    public function AddToCartForm($viewContext = '') {
        $form = new AddToCartForm($this, Controller::curr());
        $form->setViewContext($viewContext);
        return $form;
    }
    
}