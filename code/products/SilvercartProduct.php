<?php
/*
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
 */

/**
 * abstract for a product
 *
 * @author Sascha Koehler <skoehler@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProduct extends DataObject {

    /**
     * singular name for backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $singular_name = "product";

    /**
     * plural name for backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $plural_name = "products";

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
        'Title'                     => 'VarChar(255)',
        'PurchasePrice'             => 'Money',
        'Price'                     => 'Money', //if no price object exists this attribute is choosen
        'ShortDescription'          => 'VarChar(255)',
        'LongDescription'           => 'Text',
        'MSRPrice'                  => 'Money',
        'MetaDescription'           => 'VarChar(255)',
        'Weight'                    => 'Int', //unit is gramm
        'Quantity'                  => 'Int', //Quantity Pieces (Pack)
        'MetaTitle'                 => 'VarChar(64)', //search engines use only 64 chars
        'MetaKeywords'              => 'VarChar',
        'isFreeOfCharge'            => 'Boolean', //evades the mechanism of preventing products without price to go into the frontend
        'ProductNumberShop'         => 'VarChar(50)',
        'ProductNumberManufacturer' => 'VarChar(50)',
        'EANCode'                   => 'VarChar(13)',
        'isActive'                  => 'Boolean(1)'
    );

    /**
     * Summaryfields for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $summary_fields = array(
        'Title'                         => 'Artikel',
        'SilvercartManufacturer.Title'  => 'Hersteller'
    );

    /**
     * List of searchable fields for the model admin
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $searchable_fields = array(
        'Title',
        'ShortDescription',
        'LongDescription',
        'SilvercartManufacturer.Title',
        'isFreeOfCharge'
    );

    /**
     * Field labels for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $field_labels = array(
        'Title'                         => 'Bezeichnung',
        'LongDescription'               => 'Artikelbeschreibung',
        'SilvercartManufacturer.Title'  => 'Hersteller',
        'isFreeOfCharge'                => 'kostenlos',
        'PurchasePrice'                 => 'Einkaufspreis',
        'MSRPrice'                      => 'UVP'
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
     * 1:n relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public static $has_one = array(
        'SilvercartTax'             => 'SilvercartTax',
        'SilvercartManufacturer'    => 'SilvercartManufacturer',
        'SilvercartProductGroup'    => 'SilvercartProductGroupPage',
        'SilvercartMasterProduct'   => 'SilvercartProduct',
        'Image'                     => 'Image'
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
        'SilvercartShoppingCartPositions' => 'SilvercartShoppingCartPosition'
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

    // -----------------------------------------------------------------------
    // Methods
    // -----------------------------------------------------------------------

    /**
     * Constructor. We localize the static variables here.
     *
     * @param array|null $record      This will be null for a new database record.
     *                                  Alternatively, you can pass an array of
     *                                  field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                  don't have their defaults set.
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$summary_fields = array(
            'Title'                         => _t('SilvercartProduct.SINGULARNAME'),
            'SilvercartManufacturer.Title'  => _t('SilvercartManufacturer.SINGULARNAME')
        );
        self::$field_labels = array(
            'Title'                     => _t('SilvercartProduct.COLUMN_TITLE'),
            'LongDescription'           => _t('SilvercartProduct.DESCRIPTION'),
            'ShortDescription'          => _t('SilvercartProduct.SHORTDESCRIPTION'),
            'manufacturer.Title'        => _t('SilvercartManufacturer.SINGULARNAME'),
            'isFreeOfCharge'            => _t('SilvercartProduct.FREE_OF_CHARGE', 'free of charge'),
            'PurchasePrice'             => _t('SilvercartProduct.PURCHASEPRICE', 'purchase price'),
            'MSRPrice'                  => _t('SilvercartProduct.MSRP', 'MSR price'),
            'Price'                     => _t('SilvercartProduct.PRICE', 'price'),
            'MetaDescription'           => _t('SilvercartProduct.METADESCRIPTION', 'meta description'),
            'Weight'                    => _t('SilvercartProduct.WEIGHT', 'weight'),
            'Quantity'                  => _t('SilvercartProduct.QUANTITY', 'quantity'),
            'MetaTitle'                 => _t('SilvercartProduct.METATITLE', 'meta title'),
            'MetaKeywords'              => _t('SilvercartProduct.METAKEYWORDS', 'meta keywords'),
            'ProductNumberShop'         => _t('SilvercartProduct.PRODUCTNUMBER', 'product number'),
            'ProductNumberManufacturer' => _t('SilvercartProduct.PRODUCTNUMBER_MANUFACTURER', 'product number (manufacturer)'),
            'EANCode'                   => _t('SilvercartProduct.EAN', 'EAN'),
            'SilvercartTax'             => _t('SilvercartTax.SINGULARNAME', 'tax'),
            'SilvercartManufacturer'    => _t('SilvercartManufacturer.SINGULARNAME', 'manufacturer'),
            'SilvercartProductGroup'    => _t('SilvercartProductGroupPage.SINGULARNAME', 'product group'),
            'SilvercartMasterProduct'   => _t('SilvercartProduct.MASTERPRODUCT', 'master product'),
            'Image'                     => _t('SilvercartProduct.IMAGE', 'product image'),
        );
        self::$singular_name = _t('SilvercartProduct.SINGULARNAME', 'product');
        self::$plural_name = _t('SilvercartProduct.PLURALNAME', 'products');
        parent::__construct($record, $isSingleton);
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
     * @author Roland Lehmann
     * @since 23.10.2010
     */
    public static function get($whereClause = "", $sort = null, $join = null, $limit = null) {
        $requiredAttributes = self::getRequiredAttributes();
        if (!empty($requiredAttributes)) {
            $filter = "";
            foreach ($requiredAttributes as $requiredAttribute) {
                if ($requiredAttribute == "Price") {
                    $filter .= sprintf("(`PriceAmount` !='' OR `isFreeOfCharge` = '1') AND ");
                } else {
                    $filter .= sprintf("`%s` !='' AND ", $requiredAttribute);
                }
            }
            //The where clause must not end with "AND"
            $filter = substr($filter, 0, -5);
            if ($whereClause != "") {
                $filter = $filter . " AND " . $whereClause;
            }
            //
        } else {
            $filter = $whereClause;
        }

        if (!empty($filter)) {
            $filter .= ' AND ';
        }
        $filter .= 'isActive = 1';

        $products = DataObject::get('SilvercartProduct', $filter, $sort, $join, $limit);
        if ($products) {
            return $products;
        } else {
            return false;
        }
    }

    /**
     * Customizes the backend popup for Products.
     *
     * @return FieldSet the editible fields
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    public function getCMSFields_forPopup() {
        $fields = $this->getCMSFields();
        $fields->removeByName('SilvercartMasterProduct'); //remove the dropdown for the relation masterProduct
        //Get all products that have no master
        $var = sprintf("\"SilvercartMasterProduct\" = '%s'", "0");
        $silvercartMasterProducts = DataObject::get("SilvercartProduct", $var);
        $dropdownField = new DropdownField(
            'SilvercartMasterProductID',
            _t('SilvercartProduct.MASTERPRODUCT', 'master product'),
            $silvercartMasterProducts->toDropDownMap(),
            null,
            null,
            _t('SilvercartProduct.CHOOSE_MASTER', '-- choose master --')
        );
        $fields->push($dropdownField);
        return $fields;
    }

    /**
     * define the searchable fields and search methods for the frontend
     *
     * @return SearchContext ???
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
     * @return array DataObjectSet of random products
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
     * @since 23.10.2010
     * @return void
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
        $remove     = array('ä',    'ö',    'ü',    'Ä',    'Ö',    'Ü',    '/',    '?',    '&',    '#',    ' ');
        $replace    = array('ae',   'oe',   'ue',   'Ae',   'Oe',   'Ue',   '-',    '-',    '-',    '-',    '');
        $string = str_replace($remove, $replace, $this->Title);
        return $string;
    }

    /**
     * adds an product to the cart or increases its amount
     *
     * @param int $cartID   ID of the users shopping cart
     * @param int $quantity Amount of products to be added
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.2010
     *
     * @return bool
     */
    public function addToCart($cartID, $quantity = 1) {
        if ($quantity == 0) {
            return false;
        }

        $filter           = sprintf("\"SilvercartProductID\" = '%s' AND SilvercartShoppingCartID = '%s'", $this->ID, $cartID);
        $existingPosition = DataObject::get_one('SilvercartShoppingCartPosition', $filter);

        if ($existingPosition) {
            $existingPosition->Quantity += $quantity;
            $existingPosition->write();
        } else {
            $shoppingCartPosition                           = new SilvercartShoppingCartPosition();
            $shoppingCartPosition->SilvercartShoppingCartID = $cartID;
            $shoppingCartPosition->SilvercartProductID      = $this->ID;
            $shoppingCartPosition->Quantity                 = $quantity;
            $shoppingCartPosition->write();

            $cart = DataObject::get_by_id('SilvercartShoppingCart', $cartID);
        }

        return true;
    }

    /**
     * Link to the controller, that shows this product
     * An product has a unique URL
     *
     * @return string URL of $this
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    public function Link() {
        return $this->SilvercartProductGroup()->Link() . $this->ID . '/' . $this->title2urlSegment();
    }

    /**
     * Form for adding an product to a cart
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     * @return Form add an product to the cart
     */
    public function addToCartForm() {
        $fields = new FieldSet();
        $fields->push(new HiddenField('SilvercartProductID', 'SilvercartProductID', $this->ID));
        $fields->push(new NumericField('SilvercartProductAmount', _t('SilvercartProduct.QUANTITY', 'quantity'), $value = 1));
        $actions = new FieldSet();
        $actions->push(new FormAction('doAddToCart', _t('SilvercartProduct.ADD_TO_CART', 'add to cart')));
        $form = new Form(Controller::curr(), 'doAddToCart', $fields, $actions);
        return $form;
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
        $taxRate = $this->Price->getAmount() - ($this->Price->getAmount() / (100 + $this->SilvercartTax()->Rate) * 100);

        return $taxRate;
    }
}
