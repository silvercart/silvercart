<?php
/**
 * Displays products with similar attributes
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 20.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartProductGroupPage extends Page {

    public static $singular_name = "product group";
    public static $plural_name = "product groups";
    public static $allowed_children = array('SilvercartProductGroupPage');
    public static $can_be_root = false;
    public static $db = array(
    );
    public static $has_one = array(
        'GroupPicture' => 'Image'
    );
    public static $has_many = array(
        'SilvercartProducts' => 'SilvercartProduct'
    );
    public static $many_many = array(
        'SilvercartAttributes' => 'SilvercartAttribute'
    );

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
        $this->GroupPicture()->Title = $this->Title;
    }

    /**
     * Return all fields of the backend
     *
     * @return FieldSet Fields of the CMS
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $productsTableField = new HasManyDataObjectManager(
                        $this,
                        'SilvercartProducts',
                        'SilvercartProduct',
                        array(
                            'Title' => _t('SilvercartProductCategoryPage.COLUMN_TITLE'),
                            'PriceAmount' => _t('SilvercartProduct.PRICE', 'price'),
                            'Weight' => _t('SilvercartProduct.WEIGHT', 'weight')
                        ),
                        'getCMSFields',
                        "`SilvercartProductGroupID` = $this->ID"
        );
        $tabPARAM = "Root.Content."._t('SilvercartProduct.TITLE', 'product');
        $fields->addFieldToTab($tabPARAM, $productsTableField);
        
        $attributeTableField = new ManyManyDataObjectManager(
                        $this,
                        'SilvercartAttributes',
                        'SilvercartAttribute',
                        array(
                            'Title' => _t('SilvercartProductCategoryPage.COLUMN_TITLE')
                        )
        );
        $tabPARAM2 = "Root.Content." . _t('SilvercartProductGroupPage.ATTRIBUTES', 'attributes');
        $fields->addFieldToTab($tabPARAM2, $attributeTableField);
        $tabPARAM3 = "Root.Content." . _t('SilvercartProductGroupPage.GROUP_PICTURE', 'group picture');
        $fields->addFieldToTab($tabPARAM3, new FileIFrameField('GroupPicture', _t('SilvercartProductGroupPage.GROUP_PICTURE', 'group picture')));
        
        return $fields;
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
        if ($this->SilvercartProducts()->Count() > 0
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
        if ($this->SilvercartProducts()->Count() == $count) {
            return true;
        }
        return false;
    }

}

/**
 * Controller Class
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 18.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartProductGroupPage_Controller extends Page_Controller {

    protected $groupProducts;

    /**
     * execute these statements on object call
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function init() {
        // Get Products for this category
        if (!isset($_GET['start']) ||
            !is_numeric($_GET['start']) ||
            (int)$_GET['start'] < 1) {
            $SQL_start = 0;
        } else {
            $SQL_start = (int) $_GET['start'];
        }

        $this->groupProducts = SilvercartProduct::get(sprintf("`SilvercartProductGroupID` = '%s'",$this->ID), null, null, sprintf("%s,15",$SQL_start));

        // Initialise formobjects
        $productIdx = 0;
        if ($this->groupProducts) {
            $productAddCartForm = $this->getCartFormName();
            foreach ($this->groupProducts as $product) {
                $this->registerCustomHtmlForm('ProductAddCartForm'.$productIdx, new $productAddCartForm($this, array('productID' => $product->ID)));
                $product->setField('Thumbnail', $product->image()->SetWidth(150));
                $product->productAddCartForm = $this->InsertCustomHtmlForm(
                    'ProductAddCartForm'.$productIdx,
                    array(
                        $product
                    )
                );
                $productIdx++;
            }
        }

        parent::init();
    }

    /**
     * All products of this group
     * 
     * @return DataObjectSet all products of this group or FALSE
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getProducts() {
       return $this->groupProducts;
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
}