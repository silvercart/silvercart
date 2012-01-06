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
class SilvercartProduct extends DataObject implements SilvercartMultilingualInterface {

    /**
     * attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $db = array(
        'ProductNumberShop'           => 'VarChar(50)',
        'ProductNumberManufacturer'   => 'VarChar(50)',
        'PurchasePrice'               => 'Money', //the price the shop owner bought the product for
        'MSRPrice'                    => 'Money', //manufacturers recommended price
        'PriceGross'                  => 'Money', //price taxes including
        'PriceNet'                    => 'Money', //price taxes excluded
        'Weight'                      => 'Int', //unit is gramm
        'isFreeOfCharge'              => 'Boolean', //evades filter mechanism
        'EANCode'                     => 'VarChar(13)',
        'isActive'                    => 'Boolean(1)',
        'PurchaseMinDuration'         => 'Int',
        'PurchaseMaxDuration'         => 'Int',
        'PurchaseTimeUnit'            => 'Enum(",Days,Weeks,Months","")',
        'StockQuantity'               => 'Int',
        'StockQuantityOverbookable'   => 'Boolean(0)',
        'PackagingQuantity'           => 'Int',
    );

    /**
     * Array of all attributes that must be set to show an product in the frontend and enter it via backend.
     *
     * @var array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    protected static $requiredAttributes = array();
    
    /**
     * Wee have to save the deeplink value this way because the framework will
     * not show a DataObjects ID.
     * 
     * @var mixed
     * @author Roland Lehmann <rlehmann@pixeltricks.de> 
     */
    protected $deeplinkValue = null;

    /**
     * 1:n relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public static $has_one = array(
        'SilvercartTax'                 => 'SilvercartTax',
        'SilvercartManufacturer'        => 'SilvercartManufacturer',
        'SilvercartProductGroup'        => 'SilvercartProductGroupPage',
        'SilvercartMasterProduct'       => 'SilvercartProduct',
        'SilvercartAvailabilityStatus'  => 'SilvercartAvailabilityStatus',
        'SilvercartProductCondition'    => 'SilvercartProductCondition',
        'SilvercartQuantityUnit'        => 'SilvercartQuantityUnit',
        /**
         * @deprecated HasOne relation Images is deprecated. HasMany relation SilvercartImages should be used instead.
         */
        'Image'                         => 'Image',
    );

    /**
     * n:m relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
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
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.03.2011
     */
    public static $many_many = array(
        'SilvercartProductGroupMirrorPages' => 'SilvercartProductGroupPage'
    );

    /**
     * m:n relations
     *
     * @var array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $belongs_many_many = array(
        'SilvercartShoppingCarts'         => 'SilvercartShoppingCart',
        'SilvercartOrders'                => 'SilvercartOrder'
    );
    
    /**
     * Casting.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 27.06.2011
     */
    public static $casting = array(
        'isActiveString'                    => 'VarChar(8)',
        'SilvercartProductMirrorGroupIDs'   => 'Text',
        'Title'                             => 'Text',
        'ShortDescription'                  => 'Text',
        'LongDescription'                   => 'Text',
        'MetaDescription'                   => 'Text',
        'MetaTitle'                         => 'Text',
        'MetaKeywords'                      => 'Text'
    );
    
    /**
     * The final price object (dependent on customer class and custom extensions
     * like rebates @see $this->getPrice())
     *
     * @var Money
     */
    protected $price = null;
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function singular_name() {
        if (_t('SilvercartProduct.SINGULARNAME')) {
            return _t('SilvercartProduct.SINGULARNAME');
        } else {
            return parent::singular_name();
        } 
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011 
     */
    public function plural_name() {
        if (_t('SilvercartProduct.PLURALNAME')) {
            return _t('SilvercartProduct.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
    }
    
    /**
     * getter for the Title, looks for set translation
     *
     * @param string $locale ???
     * 
     * @return string The Title from the translation object or an empty string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 03.01.2012
     */
    public function getTitle() {
        $title = '';
        if ($this->getLanguage()) {
            $title = $this->getLanguage()->Title;
        }
        return $title;
    }
    
    /**
     * getter for the ShortDescription, looks for set translation
     *
     * @param string $locale ???
     * 
     * @return string The ShortDescription from the translation object or an empty string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 03.01.2012
     */
    public function getShortDescription() {
        $shortDescription = '';
        if ($this->getLanguage()) {
            $shortDescription = $this->getLanguage()->ShortDescription;
        }
        return $shortDescription;
    }
    
    /**
     * getter for the LongDescription, looks for set translation
     *
     * @param string $locale ???
     * 
     * @return string The LongDescription from the translation object or an empty string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 03.01.2012
     */
    public function getLongDescription() {
        $longDescription = '';
        if ($this->getLanguage()) {
            $longDescription = $this->getLanguage()->LongDescription;
        }
        return $longDescription;
    }
    
    /**
     * getter for the MetaDescription, looks for set translation
     *
     * @param string $locale ???
     * 
     * @return string The MetaDescription from the translation object or an empty string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 03.01.2012
     */
    public function getMetaDescription() {
        $metaDescription = '';
        if ($this->getLanguage()) {
            $metaDescription = $this->getLanguage()->MetaDescription;
        }
        return $metaDescription;
    }
    
    /**
     * getter for the MetaTitle, looks for set translation
     *
     * @param string $locale ???
     * 
     * @return string The MetaTitle from the translation object or an empty string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 03.01.2012
     */
    public function getMetaTitle() {
        $metaTitle = '';
        if ($this->getLanguage()) {
            $metaTitle = $this->getLanguage()->MetaTitle;
        }
        return $metaTitle;
    }
    
    /**
     * getter for the MetaKeywords, looks for set translation
     *
     * @param string $locale ???
     * 
     * @return string The MetaKeywords from the translation object or an empty string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 03.01.2012
     */
    public function getMetaKeywords() {
        $metaKeywords = '';
        if ($this->getLanguage()) {
            $metaKeywords = $this->getLanguage()->MetaKeywords;
        }
        return $metaKeywords;
    }


    /**
     * Is this product viewable in the frontend?
     * 
     * @param Member $member the current member
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 6.6.2011
     * @return bool 
     */
    public function canView($member = null) {
        parent::canView($member);
        $publishedProduct = SilvercartProduct::get("`SilvercartProduct`.`ID` = $this->ID");
        if ($publishedProduct) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 10.03.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'ProductNumberShop'                     => _t('SilvercartProduct.PRODUCTNUMBER'),
            'Title'                                 => _t('SilvercartProduct.SINGULARNAME'),
            'SilvercartProductGroup.Title'          => _t('SilvercartProductGroupPage.SINGULARNAME'),
            'SilvercartManufacturer.Title'          => _t('SilvercartManufacturer.SINGULARNAME'),
            'SilvercartAvailabilityStatus.Title'    => _t('SilvercartAvailabilityStatus.SINGULARNAME'),
            'isActiveString'                        => _t('SilvercartProduct.IS_ACTIVE')
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * Searchable fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 10.03.2011
     */
    public function searchableFields() {
        $searchableFields = array(
            'ProductNumberShop' => array(
                'title'     => _t('SilvercartProduct.PRODUCTNUMBER'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartProductLanguages.Title' => array(
                'title'     => _t('SilvercartProduct.COLUMN_TITLE'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartProductLanguages.ShortDescription' => array(
                'title'     => _t('SilvercartProduct.SHORTDESCRIPTION'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartProductLanguages.LongDescription' => array(
                'title'     => _t('SilvercartProduct.DESCRIPTION'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartManufacturer.Title' => array(
                'title'     => _t('SilvercartManufacturer.SINGULARNAME', 'manufacturer'),
                'filter'    => 'PartialMatchFilter'
             ),
            'ProductNumberManufacturer' => array(
                'title'     => _t('SilvercartProduct.PRODUCTNUMBER_MANUFACTURER', 'product number (manufacturer)'),
                'filter'    => 'PartialMatchFilter'
             ),
            'isFreeOfCharge' => array(
                'title'     => _t('SilvercartProduct.FREE_OF_CHARGE', 'free of charge'),
                'filter'    => 'PartialMatchFilter'
            ),
            'isActive' => array(
                'title'     => _t('SilvercartProduct.IS_ACTIVE', 'is active'),
                'filter'    => 'PartialMatchFilter'
            ),
            'SilvercartProductGroupID' => array(
                'title'     => _t('SilvercartProductGroupPage.SINGULARNAME'),
                'filter'    => 'ExactMatchFilter'
            ),
            'SilvercartProductGroupMirrorPages.ID' => array(
                'title'     => _t('SilvercartProductGroupMirrorPage.SINGULARNAME'),
                'filter'    => 'ExactMatchFilter'
            ),
            'SilvercartAvailabilityStatus.ID' => array(
                'title'     => _t('SilvercartAvailabilityStatus.SINGULARNAME'),
                'filter'    => 'ExactMatchFilter'
            )
        );
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
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
     * @since 10.03.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title'                             => _t('SilvercartProduct.COLUMN_TITLE'),
                'LongDescription'                   => _t('SilvercartProduct.DESCRIPTION'),
                'ShortDescription'                  => _t('SilvercartProduct.SHORTDESCRIPTION'),
                'manufacturer.Title'                => _t('SilvercartManufacturer.SINGULARNAME'),
                'isFreeOfCharge'                    => _t('SilvercartProduct.FREE_OF_CHARGE', 'free of charge'),
                'PurchasePrice'                     => _t('SilvercartProduct.PURCHASEPRICE', 'purchase price'),
                'MSRPrice'                          => _t('SilvercartProduct.MSRP', 'MSR price'),
                'PriceGross'                        => _t('SilvercartProduct.PRICE_GROSS', 'price (gross)'),
                'PriceNet'                          => _t('SilvercartProduct.PRICE_NET', 'price (net)'),
                'MetaDescription'                   => _t('SilvercartProduct.METADESCRIPTION', 'meta description'),
                'Weight'                            => _t('SilvercartProduct.WEIGHT', 'weight'),
                'MetaTitle'                         => _t('SilvercartProduct.METATITLE', 'meta title'),
                'MetaKeywords'                      => _t('SilvercartProduct.METAKEYWORDS', 'meta keywords'),
                'ProductNumberShop'                 => _t('SilvercartProduct.PRODUCTNUMBER', 'product number'),
                'ProductNumberManufacturer'         => _t('SilvercartProduct.PRODUCTNUMBER_MANUFACTURER', 'product number (manufacturer)'),
                'EANCode'                           => _t('SilvercartProduct.EAN', 'EAN'),
                'SilvercartTax'                     => _t('SilvercartTax.SINGULARNAME', 'tax'),
                'SilvercartManufacturer'            => _t('SilvercartManufacturer.SINGULARNAME', 'manufacturer'),
                'SilvercartProductGroup'            => _t('SilvercartProductGroupPage.SINGULARNAME', 'product group'),
                'SilvercartMasterProduct'           => _t('SilvercartProduct.MASTERPRODUCT', 'master product'),
                'Image'                             => _t('SilvercartProduct.IMAGE', 'product image'),
                'SilvercartAvailabilityStatus'      => _t('SilvercartAvailabilityStatus.SINGULARNAME', 'Availability Status'),
                'PurchaseMinDuration'               => _t('SilvercartProduct.PURCHASE_MIN_DURATION', 'Min. purchase duration'),
                'PurchaseMaxDuration'               => _t('SilvercartProduct.PURCHASE_MAX_DURATION', 'Max. purchase duration'),
                'PurchaseTimeUnit'                  => _t('SilvercartProduct.PURCHASE_TIME_UNIT', 'Purchase time unit'),
                'SilvercartFiles'                   => _t('SilvercartFile.PLURALNAME', 'Files'),
                'SilvercartImages'                  => _t('SilvercartImage.PLURALNAME', 'Images'),
                'SilvercartShoppingCartPositions'   => _t('SilvercartShoppingCartPosition.PLURALNAME', 'Cart positions'),
                'SilvercartShoppingCarts'           => _t('SilvercartShoppingCart.PLURALNAME', 'Carts'),
                'SilvercartOrders'                  => _t('SilvercartOrder.PLURALNAME', 'Orders'),
                'SilvercartProductGroupMirrorPages' => _t('SilvercartProductGroupMirrorPage.PLURALNAME', 'Mirror-Productgroups'),
                'SilvercartQuantityUnit'            => _t('SilvercartProduct.AMOUNT_UNIT', 'amount Unit'),
                'isActive'                          => _t('SilvercartProduct.IS_ACTIVE'),
                'StockQuantity'                     => _t('SilvercartProduct.STOCKQUANTITY', 'stock quantity'),
                'StockQuantityOverbookable'         => _t('SilvercartProduct.STOCK_QUANTITY', 'Is the stock quantity of this product overbookable?'),
                'PackagingQuantity'                 => _t('SilvercartProduct.PACKAGING_QUANTITY', 'purchase quantity'),
                'ID'                                => 'ID', //needed for the deeplink feature
                'SilvercartProductLanguages'        => _t('SilvercartConfig.TRANSLATIONS')
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
     * Getter similar to DataObject::get(); returns a DataObectSet of products filtered by the requirements in self::getRequiredAttributes();
     * If an product is free of charge, it can have no price. This is for giveaways and gifts.
     *
     * @param string  $whereClause to be inserted into the sql where clause
     * @param string  $sort        string with sort clause
     * @param string  $join        string for a join
     * @param integer $limit       DataObject limit
     *
     * @return DataObjectSet DataObjectSet of products or false
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.10.2011
     */
    public static function get($whereClause = "", $sort = null, $join = null, $limit = null) {
        
        $requiredAttributes = self::getRequiredAttributes();
        $pricetype          = SilvercartConfig::Pricetype();
        $filter             = "";

        if (!empty($requiredAttributes)) {
            foreach ($requiredAttributes as $requiredAttribute) {
                if ($requiredAttribute == "Price") {
                    // Gross price as default if not defined
                    if ($pricetype == "net") {
                        $filter .= sprintf("(`isFreeOfCharge` = 1 OR `PriceNetAmount` != 0.0) AND ");
                    } else {
                        $filter .= sprintf("(`isFreeOfCharge` = 1 OR `PriceGrossAmount` != 0.0) AND ");
                    }
                } else {
                    $filter .= sprintf("`%s` !='' AND ", $requiredAttribute);
                }
            }
        }

        if ($whereClause != "") {
            $filter = $filter . $whereClause . ' AND ';
        }

        $filter .= 'isActive = 1';

        if (!$sort) {
            $sort = 'SilvercartProduct.SortOrder ASC';
        }
        
        $databaseFilteredProducts = DataObject::get('SilvercartProduct', $filter, $sort, $join, $limit);
        
        return $databaseFilteredProducts;
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
        
        if (SilvercartConfig::DisplayTypeOfProductAdminFlat()) {
            $targetTab = 'Root.Main';
        } else {
            $targetTab = 'Root.Main.Content';
        }
        $fields->addFieldToTab($targetTab, $dropdownField);

        $this->extend('updateCMSFields_forPopup', $fields);
        return $fields;
    }
    
    /**
     * Creates a whitelist with restricted fields for the FormScaffolder.
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright pixeltricks GmbH 2011
     * @since 04.05.2011
     */
    public function scaffoldFormFields() {
        $params = array(
            'tabbed' => true,
            'restrictFields' => array(
                'Title',
                'ShortDescription',
                'LongDescription',
                'MetaDescription',
                'MetaTitle',
                'MetaKeywords',
                'ProductNumberShop'.
                'ProductNumberManufacturer',
                'PurchasePrice',
                'MSRPrice',
                'PriceGross',
                'PriceNet',
                'Weight',
                'isFreeOfCharge',
                'EANCode',
                'isActive',
                'PurchaseMinDuration',
                'PurchaseMaxDuration',
                'PurchaseTimeUnit',
                'SilvercartTax',
                'SilvercartManufacturer',
                'SilvercartAvailabilityStatus',
                'SilvercartQuantityUnit',
                'SilvercartFiles',
                'SilvercartOrders',
                'StockQuantity',
                'StockQuantityOverbookable',
                'PackagingQuantity',
            ),
            'includeRelations' => true
        );
        
        $this->extend('updateScaffoldFormFields', $params);
        
        return parent::scaffoldFormFields($params);
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
        $fields = parent::getCMSFields($params);
        // there are two ways to display the products CMS fields (set in Backend)
        if (SilvercartConfig::DisplayTypeOfProductAdminFlat()) {
            //Inject the fields that come from the language object
            //They are added to the content tab for the users comfort.
            $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage());
            foreach ($languageFields as $languageField) {
                $fields->insertBefore($languageField, 'PurchasePrice');
            }
            // remove GoogleSitemap Priority
            $fields->removeByName('Priority');
            $fields->removeByName('GoogleSitemapIntro');
            // --------------------------------------------------------------------
            // Fields for the main tab
            // --------------------------------------------------------------------
            $fields->addFieldToTab('Root.Main', new GroupedDropdownField('SilvercartProductGroupID', _t('SilvercartProductGroupPage.SINGULARNAME', 'product group'), SilvercartProductGroupHolder_Controller::getRecursiveProductGroupsForGroupedDropdownAsArray()),'SilvercartMasterProductID');

            $purchaseMinDurationField   = clone $fields->dataFieldByName('PurchaseMinDuration');
            $fields->removeByName('PurchaseMinDuration');
            $purchaseMaxDurationField   = clone $fields->dataFieldByName('PurchaseMaxDuration');
            $fields->removeByName('PurchaseMaxDuration');
            $purchaseTimeUnitField      = clone $fields->dataFieldByName('PurchaseTimeUnit');
            $source = $purchaseTimeUnitField->getSource();
            $source['Days'] = _t('Silvercart.DAYS','Days');
            $source['Weeks'] = _t('Silvercart.WEEKS','Weeks');
            $source['Months'] = _t('Silvercart.MONTHS','Months');
            $purchaseTimeUnitField->setSource($source);
            $fields->removeByName('PurchaseTimeUnit');
            $availabilityStatusField = clone $fields->dataFieldByName('SilvercartAvailabilityStatusID');
            $fields->removeByName('SilvercartAvailabilityStatusID');
            $productNumberField = new TextField('ProductNumberShop', _t('SilvercartProduct.PRODUCTNUMBER'));
            $manufacturerNumberField = new TextField('ProductNumberManufacturer', _t('SilvercartProduct.PRODUCTNUMBER_MANUFACTURER'));

            $fields->addFieldToTab('Root.Main', $productNumberField, 'Title');
            $fields->addFieldToTab('Root.Main', $manufacturerNumberField, 'Title');
            $fields->addFieldToTab('Root.Main', $availabilityStatusField, 'isFreeOfCharge');
            $fields->addFieldToTab('Root.Main', $purchaseMinDurationField, 'isFreeOfCharge');
            $fields->addFieldToTab('Root.Main', $purchaseMaxDurationField, 'isFreeOfCharge');
            $fields->addFieldToTab('Root.Main', $purchaseTimeUnitField, 'isFreeOfCharge');

            $amountUnitField = clone $fields->dataFieldByName('SilvercartQuantityUnitID');
            $fields->removeByName('SilvercartQuantityUnitID');
            $fields->addFieldToTab('Root.Main', $fields->dataFieldByName('PackagingQuantity'), 'SilvercartTaxID');
            $fields->addFieldToTab('Root.Main', $amountUnitField, 'SilvercartTaxID');

            $conditionMap   = array();
            $conditions     = DataObject::get(
                'SilvercartProductCondition'
            );

            if ($conditions) {
                $conditionMap = $conditions->map('ID', 'Title');
            }

            $conditionField = new DropdownField(
                'SilvercartProductConditionID',
                _t('SilvercartProductCondition.TITLE'),
                $conditionMap,
                $this->SilvercartProductConditionID,
                null,
                _t('SilvercartProductCondition.PLEASECHOOSE')
            );

            $fields->addFieldToTab('Root.Main', $conditionField);

            // --------------------------------------------------------------------
            // Image tab
            // --------------------------------------------------------------------
            $fields->findOrMakeTab('Root.SilvercartImages', _t('SilvercartImage.PLURALNAME', 'Images'));
            if ($this->ID) {
                $silvercartImagesTable = new ImageDataObjectManager($this, 'SilvercartImages', 'SilvercartImage', 'Image', null, null, sprintf("`SilvercartProductID`='%d'", $this->ID));
                $fields->addFieldToTab('Root.SilvercartImages', $silvercartImagesTable);
            } else {
                $silvercartImageInformation = new LiteralField('SilvercartImageInformation', sprintf(
                        _t('FileIFrameField.ATTACHONCESAVED'),
                        _t('SilvercartImage.SINGULARNAME', 'Image')));
                $fields->addFieldToTab('Root.SilvercartImages', $silvercartImageInformation);
            }

            // --------------------------------------------------------------------
            // SEO tab
            // --------------------------------------------------------------------
            $fields->findOrMakeTab('Root.SEO', _t('Silvercart.SEO', 'SEO'));
//            $fields->addFieldToTab('Root.SEO', $fields->dataFieldByName('MetaTitle'));
//            $fields->addFieldToTab('Root.SEO', $fields->dataFieldByName('MetaDescription'));
//            $fields->addFieldToTab('Root.SEO', $fields->dataFieldByName('MetaKeywords'));


            // --------------------------------------------------------------------
            // Product group pages tab
            // --------------------------------------------------------------------
            $productGroupTable = new HasOneComplexTableField(
                $this,
                'SilvercartProductGroup',
                'SilvercartProductGroupPage',
                array(
                    'Breadcrumbs'   => 'Breadcrumbs'
                ),
                null,
                null,
                'SiteTree.ParentID ASC, SiteTree.Sort ASC'
            );

            $productGroupTable->pageSize = 1000;

            $fields->addFieldToTab('Root.SilvercartProductGroup', $productGroupTable);

            // set tab title
            $tab = $fields->findOrMakeTab('Root.SilvercartProductGroup');
            $tab->title = _t('SilvercartProductGroupPage.SINGULARNAME', 'product group');

            // --------------------------------------------------------------------
            // Mirror product group pages tab
            // --------------------------------------------------------------------
            $productGroupMirrorPagesTable = new ManyManyComplexTableField(
                $this,
                'SilvercartProductGroupMirrorPages',
                'SilvercartProductGroupPage',
                array(
                    'Breadcrumbs'   => 'Breadcrumbs'
                ),
                null,
                sprintf(
                    "SiteTree.ID != %d",
                    $this->SilvercartProductGroup()->ID
                ),
                'SiteTree.ParentID ASC, SiteTree.Sort ASC'
            );
            $productGroupMirrorPagesTable->pageSize = 1000;

            $fields->findOrMakeTab('Root.SilvercartProductGroupMirrorPages', _t('SilvercartProductGroupMirrorPage.PLURALNAME', 'Mirror-Productgroups'));
            $fields->addFieldToTab('Root.SilvercartProductGroupMirrorPages', $productGroupMirrorPagesTable);



            // --------------------------------------------------------------------
            // Reorder tabs
            // --------------------------------------------------------------------
            $tabset = false;

            foreach ($fields as $i => $field) {
                if (is_object($field) && $field instanceof TabSet) {
                    $tabset = $field;
                    break;
                }
            }

            if ($tabset) {
                $tabs = array();

                foreach ($tabset->children as $child) {
                    $tabs[$child->name] = $child;
                    $tabset->removeByName($child->name);
                }

                $tabset->push($tabs['Main']); // Main
                $tabset->push($tabs['SilvercartProductGroup']); // Product groups
                $tabset->push($tabs['SilvercartProductGroupMirrorPages']); // Mirror product groups
                if (array_key_exists('SilvercartOrders', $tabs)) {
                    $tabset->push($tabs['SilvercartOrders']); // Orders
                }

                unset($tabs['Main']);
                unset($tabs['SilvercartProductGroup']);
                unset($tabs['SilvercartProductGroupMirrorPages']);

                if (array_key_exists('SilvercartOrders', $tabs)) {
                    unset($tabs['SilvercartOrders']);
                }

                foreach ($tabs as $tabName => $tab) {
                    $tabset->push($tab);
                }
            }

            // remove GoogleSitemap Priority again
            if ($fields->dataFieldByName('Priority')) {
                $priority = clone $fields->dataFieldByName('Priority');
                $fields->removeByName('Priority');
                $fields->removeByName('GoogleSitemapIntro');
                $fields->removeByName('Content');
                $fields->addFieldToTab('Root.SEO', $priority);
            }
            //add a tab for deeplinks
            $fields->findOrMakeTab('Root.Deeplinks');
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
            $CMSFields = $fields;
        } else {
            //define the new tabset
            $CMSFields = new FieldSet(
                $rootTab = new TabSet(
                    'Root',
                    $mainTab = new TabSet(
                        'Main',
                        $contentTab      = new Tab('Content'),
                        $translationsTab = new Tab('Translations'),
                        $pricesTab       = new Tab('Prices'),
                        $manufacturerTab = new Tab('Manufacturer')
                    ),
                    $seoTab = new TabSet(
                        'SEO',
                        $metadataTab = new Tab('MetaData')
                    ),
                    $linksTab = new TabSet(
                        'Links',
                        $mirrorProductGroupsTab = new Tab('MirrorProductGroups'),
                        $productGroupsTab = new Tab('ProductGroups'),
                        $deeplinksTab = new Tab('Deeplinks')
                    ),
                    $stockTab = new TabSet(
                        'Stock',
                        $dataTab = new Tab('Data'),
                        $timeTab = new Tab('Time'),
                        $configTab = new Tab('Config')
                    ),
                    $filesTab = new TabSet(
                        'Files',
                        $attachmentsTab = new Tab('Attachments'),
                        $imagesTab = new Tab('Images')
                    )
                )
            );

            // i18n for the tabs
            $mainTab->setTitle(                 _t('SiteTree.TABMAIN'));
            $contentTab->setTitle(              _t('Silvercart.CONTENT',                                'Content'));
            $translationsTab->setTitle(_t('SilvercartConfig.TRANSLATIONS'));
            $pricesTab->setTitle(               _t('SilvercartPrice.PLURALNAME'));
            $manufacturerTab->setTitle(         _t('SilvercartManufacturer.PLURALNAME'));

            $seoTab->setTitle(                  _t('Silvercart.SEO',                                    'SEO'));
            $metadataTab->setTitle(             _t('SilvercartProduct.METADATA',                        'Meta Data'));

            $linksTab->setTitle(                _t('Silvercart.LINKS',                                  'Links'));
            $mirrorProductGroupsTab->setTitle(  _t('SilvercartProductGroupMirrorPage.PLURALNAME'));
            $productGroupsTab->setTitle(        _t('SilvercartProductGroupPage.SINGULARNAME'));
            $deeplinksTab->setTitle(            _t('Silvercart.DEEPLINKS',                              'Deeplinks'));

            $stockTab->setTitle(                _t('SilvercartConfig.STOCK'));
            $dataTab->setTitle(                 _t('Silvercart.DATA'));
            $timeTab->setTitle(                 _t('Silvercart.TIMES'));
            $configTab->setTitle(               _t('Silvercart.MISC_CONFIG'));

            $filesTab->setTitle(                _t('SilvercartFile.PLURALNAME'));
            $attachmentsTab->setTitle(          _t('SilvercartFile.FILE_ATTACHMENTS'));
            $imagesTab->setTitle(               _t('SilvercartImage.PLURALNAME'));

            //fill the tab Root.Main.Content
            $CMSFields->addFieldToTab('Root.Main.Content', $fields->dataFieldByName('isActive'));
            $CMSFields->addFieldToTab('Root.Main.Content', new TextField('ProductNumberShop', _t('SilvercartProduct.PRODUCTNUMBER')));
            
            //Inject the fields that come from the language object
            //They are added to the content tab for the users comfort.
            $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage());
            $CMSFields->addFieldsToTab('Root.Main.Content', $languageFields);
            $productLanguagesTable = new ComplexTableField($this, 'SilvercartProductLanguages', 'SilvercartProductLanguage', null, 'getCMSFields_forPopup');
            $CMSFields->addFieldToTab('Root.Main.Translations', $productLanguagesTable);
            $taxRates = DataObject::get('SilvercartTax');
            $taxRateMap = array();
            if ($taxRates) {
                $taxRateMap = $taxRates->map();
            }
            $CMSFields->addFieldToTab('Root.Main.Content', new DropdownField('SilvercartTaxID', _t('SilvercartTax.SINGULARNAME'), $taxRateMap, $this->SilvercartTaxID, null, ''));
            $CMSFields->addFieldToTab('Root.Main.Content', $fields->dataFieldByName('Weight'));
            $CMSFields->addFieldToTab('Root.Main.Content', $fields->dataFieldByName('EANCode'));
            $conditionMap   = array();
            $conditions     = DataObject::get(
                'SilvercartProductCondition'
            );

            if ($conditions) {
                $conditionMap = $conditions->map('ID', 'Title');
            }

            $conditionField = new DropdownField(
                'SilvercartProductConditionID',
                _t('SilvercartProductCondition.TITLE'),
                $conditionMap,
                $this->SilvercartProductConditionID,
                null,
                _t('SilvercartProductCondition.PLEASECHOOSE')
            );

            $CMSFields->addFieldToTab('Root.Main.Content', $conditionField);

            //fill the tab Root.Main.Prices
            $CMSFields->addFieldToTab('Root.Main.Prices', $fields->dataFieldByName('PurchasePrice'));
            $CMSFields->addFieldToTab('Root.Main.Prices', $fields->dataFieldByName('MSRPrice'));
            $CMSFields->addFieldToTab('Root.Main.Prices', $fields->dataFieldByName('PriceGross'));
            $CMSFields->addFieldToTab('Root.Main.Prices', $fields->dataFieldByName('PriceNet'));
            $CMSFields->addFieldToTab('Root.Main.Prices', $fields->dataFieldByName('isFreeOfCharge'));

            //fill the tab Root.Main.Manufacturer
            $CMSFields->addFieldToTab('Root.Main.Manufacturer', new TextField('ProductNumberManufacturer', _t('SilvercartProduct.PRODUCTNUMBER_MANUFACTURER'), $this->ProductNumberManufacturer));
            $manufacturers = DataObject::get('SilvercartManufacturer');
            $manufacturerMap = array();
            if ($manufacturers) {
                $manufacturerMap = $manufacturers->map();
            }
            $CMSFields->addFieldToTab('Root.Main.Manufacturer', new DropdownField(
                    'SilvercartManufacturerID',
                    _t('SilvercartManufacturer.SINGULARNAME'),
                    $manufacturerMap,
                    $this->SilvercartManufacturerID,
                    null,
                    _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE')
                    )
                );

            //fill the tab Root.SEO.MetaData
//            $CMSFields->addFieldToTab('Root.SEO.MetaData', $fields->dataFieldByName('MetaTitle'));
//            $CMSFields->addFieldToTab('Root.SEO.MetaData', $fields->dataFieldByName('MetaDescription'));
//            $CMSFields->addFieldToTab('Root.SEO.MetaData', $fields->dataFieldByName('MetaKeywords'));

            //fill the tab Root.Links.MirrorProductGroups
            $productGroupMirrorPagesTable = new ManyManyComplexTableField(
                $this,
                'SilvercartProductGroupMirrorPages',
                'SilvercartProductGroupPage',
                array(
                    'Breadcrumbs'   => 'Breadcrumbs'
                ),
                null,
                sprintf(
                    "SiteTree.ID != %d",
                    $this->SilvercartProductGroup()->ID
                ),
                'SiteTree.ParentID ASC, SiteTree.Sort ASC'
            );
            $productGroupMirrorPagesTable->pageSize = 1000;
            $CMSFields->addFieldToTab('Root.Links.MirrorProductGroups', $productGroupMirrorPagesTable);

            //fill the tab Root.Links.ProductGroup
            $productGroupTable = new HasOneComplexTableField(
                $this,
                'SilvercartProductGroup',
                'SilvercartProductGroupPage',
                array(
                    'Breadcrumbs'   => _t('SilvercartProductGroupPage.SINGULARNAME')
                ),
                null,
                null,
                'SiteTree.ParentID ASC, SiteTree.Sort ASC'
            );
            $productGroupTable->pageSize = 1000;
            $CMSFields->addFieldToTab('Root.Links.ProductGroups', $productGroupTable);

            //fill the tab Root.Links.Deeplinks
            $CMSFields->addFieldToTab('Root.Links.Deeplinks', new LiteralField('deeplinkText', _t('SilvercartProduct.DEEPLINK_TEXT')));
            if ($this->canView()) {
                $deeplinks = DataObject::get('SilvercartDeeplink', '`isActive` = 1');
                if ($deeplinks) {
                    $idx = 1;
                    foreach ($deeplinks as $deeplink) {
                        if (isset($deeplink->productAttribute)) {
                            $attribute = $deeplink->productAttribute;
                            if (isset($this->{$attribute}) && $this->{$attribute} != "") {
                                $this->deeplinkValue = (string)$this->{$attribute};
                                $productDeeplink = $deeplink->getDeeplinkUrl() . $this->deeplinkValue;
                                $fieldName = sprintf(_t('SilvercartProduct.DEEPLINK_FOR'), $attribute);
                                $CMSFields->addFieldToTab('Root.Links.Deeplinks', new ReadonlyField($attribute.$idx, $fieldName, $productDeeplink));
                                $idx++;
                            }
                        }
                    }
                }
            }

            //fill the tab Root.Stock.Data
            $CMSFields->addFieldToTab('Root.Stock.Data', $fields->dataFieldByName('StockQuantity'));
            $CMSFields->addFieldToTab('Root.Stock.Data', $fields->dataFieldByName('PackagingQuantity'));

            //fill the tab Root.Stock.Time
            $CMSFields->addFieldToTab('Root.Stock.Time', $fields->dataFieldByName('PurchaseMinDuration'));
            $CMSFields->addFieldToTab('Root.Stock.Time', $fields->dataFieldByName('PurchaseMaxDuration'));
            $CMSFields->addFieldToTab('Root.Stock.Time', $fields->dataFieldByName('PurchaseTimeUnit'));

            //fill the tab Root.Stock.Config
            $CMSFields->addFieldToTab('Root.Stock.Config', $fields->dataFieldByName('SilvercartAvailabilityStatusID'));
            $CMSFields->addFieldToTab('Root.Stock.Config', $fields->dataFieldByName('StockQuantityOverbookable'));
            
            //fill the tab Root.Files.Images
            if ($this->ID) {
                $silvercartImagesTable = new ImageDataObjectManager(
                        $this,
                        'SilvercartImages',
                        'SilvercartImage',
                        'Image',
                        null,
                        null,
                        sprintf("`SilvercartProductID`='%d'", $this->ID));
                $CMSFields->addFieldToTab('Root.Files.Images', $silvercartImagesTable);
            } else {
                $silvercartImageInformation = new LiteralField('SilvercartImageInformation', sprintf(
                        _t('FileIFrameField.ATTACHONCESAVED'),
                        _t('SilvercartImage.SINGULARNAME', 'Image')));
                $CMSFields->addFieldToTab('Root.Files.Images', $silvercartImageInformation);
            }

            //fill the tab Root.Files.Attachments
            if ($this->ID) {
                $filesTable = new FileDataObjectManager(
                        $this,
                        'SilvercartFiles',
                        'SilvercartFile',
                        'File',
                        null,
                        null,
                        sprintf("`SilvercartProductID`='%d'", $this->ID)
                );
                $CMSFields->addFieldToTab('Root.Files.Attachments', $filesTable);
            } else {
                $silvercartFileInformation = new LiteralField('SilvercartFileInformation', sprintf(
                        _t('FileIFrameField.ATTACHONCESAVED'),
                        _t('SilvercartImage.SINGULARNAME', 'Image')));
                $CMSFields->addFieldToTab('Root.Files.Attachments', $silvercartFileInformation);
            }
        }
        
        $this->extend('updateCMSFields', $CMSFields);
        // here is another extension to prevend multiple loadings of updateCMSFields
        $this->extend('updateSilvercartCMSFields', $CMSFields);
        return $CMSFields;
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
     * @return Money price dependent on customer class and configuration
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.10.2011
     */
    public function getPrice() {
        if (is_null($this->price)) {
            $pricetype = SilvercartConfig::Pricetype();
            if ($pricetype =="net") {
                $price = clone $this->PriceNet;
            } elseif ($pricetype == "gross") {
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
        }
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
        $requiredAttributesArray = array();
        $requiredAttributesArray = explode(",", str_replace(" ", "", $concatinatedAttributesString));
        self::$requiredAttributes = $requiredAttributesArray;
    }

    /**
     * Remove chars from the title that are not appropriate for an url
     *
     * @return string sanitized product title
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    private function title2urlSegment() {
        $remove     = array('ä',    'ö',    'ü',    'Ä',    'Ö',    'Ü',    '/',    '?',    '&',    '#',    ' ', '%', '"', '<', '>');
        $replace    = array('ae',   'oe',   'ue',   'Ae',   'Oe',   'Ue',   '-',    '-',    '-',    '-',    '',  '',  '',  '',  '');
        $string = str_replace($remove, $replace, $this->Title);
        return $string;
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
     * @param int $cartID   ID of the users shopping cart
     * @param int $quantity Amount of products to be added
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
     * Link to the controller, that shows this product
     * An product has a unique URL
     *
     * @return string URL of $this
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    public function Link() {
        $link = '';
        
        if ($this->SilvercartProductGroup()) {
            $link = $this->SilvercartProductGroup()->Link() . $this->ID . '/' . $this->title2urlSegment();
        }
        
        return $link;
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
     * returns the tax amount included in $this
     *
     * @return float
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.11.2010
     */
    public function getTaxAmount() {        
        if (Member::currentUser()->showPricesGross()) {
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
     * We make this method extendable here.
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.11.2011
     */
    public function onBeforeDelete() {
        parent::onBeforeDelete();
        
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
        
        $translations = $this->SilvercartProductGroup()->getTranslations();
        foreach ($translations as $translation) {
            if ($this->SilvercartProductGroupMirrorPages()->find('ID', $translation->ID)) {
                continue;
            }
            $this->SilvercartProductGroupMirrorPages()->add($translation);
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.03.2011
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
        
        SilvercartLanguageHelper::writeLanguageObject($this->getLanguage(), $this->record);
    }
    
    public function getLanguage() {
        if (!isset ($this->languageObj)) {
            $this->languageObj = SilvercartLanguageHelper::getLanguage($this->SilvercartProductLanguages());
            if (!$this->languageObj) {
                $this->languageObj = new SilvercartProductLanguage();
            }
        }
        return $this->languageObj;
    }
    
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
            return $images;
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
     * Is this products stock quantity overbookable?
     * If this product does not have overbookablility set the general setting of
     * the config object is choosen.
     * If stock management is deactivated true will be returned.
     * 
     * @return boolean is the product overbookable?
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.7.2011
     */
    public function isStockQuantityOverbookable() {
        $overbookable = true;
        if (SilvercartConfig::EnableStockManagement()) {
            if (SilvercartConfig::isStockManagementOverbookable() || $this->StockQuantityOverbookable) {
                $overbookable = true;
            } else {
                $overbookable = false;
            }
        } else {
            return true;
        }
        return $overbookable;
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
        if (SilvercartConfig::EnableStockManagement()
                && !$this->isStockQuantityOverbookable() 
                && ($this->StockQuantity - $cartPositionQuantity) <= 0) {
            return false;
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
                if (file_exists($data['imageDirectory'].$product['fileName'])) {
                    unlink($data['imageDirectory'].$product['fileName']);
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
                        ID
                    ) VALUES(
                        %d
                    )
                ',
                $insertID
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
            count($resultSet) > 0) {
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

        if (method_exists($this, $formIdentifier)) {
            $form = $this->$formIdentifier();
        }
        
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
        
        if (method_exists($this, $data['action'])) {
            $this->$data['action']($data, $form, $request, $output);
        }
        
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
            'isFreeOfCharge'                        => 'isFreeOfCharge',
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 16.08.2011
     */
    protected function Log($logString, $filename = 'importProducts') {
        $logDirectory = Director::baseFolder();

        $logDirectory = explode('/', $logDirectory);
        array_pop($logDirectory);
        array_pop($logDirectory);
        $logDirectory = implode('/', $logDirectory);

        $data  = date('d.m.Y H:i:s').":\t".$logString."\n";
        $filename = $logDirectory.'/log/' . $filename . '.log';
        file_put_contents($filename, $data, FILE_APPEND);
    }
}
