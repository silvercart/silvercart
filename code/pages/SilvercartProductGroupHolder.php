<?php

/**
 * to display a group of products
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license BSD
 * @since 23.10.2010
 */
class SilvercartProductGroupHolder extends Page {

    public static $singular_name = "";
    public static $plural_name = "";
    public static $allowed_children = array(
        'SilvercartProductGroupPage'
    );

    /**
     * default instances related to $this
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     * @return void
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $records = DataObject::get_one('SilvercartProductGroupHolder');
        if (!$records) {
            $page = new SilvercartProductGroupHolder();
            $page->Title = _t('SilvercartProductGroupHolder.PLURALNAME', 'product groups');
            $page->URLSegment = _t('SilvercartProductGroupHolder.URL_SEGMENT', 'productgroups');
            $page->Status = "Published";
            $page->write();
            $page->publish("Stage", "Live");
        }
    }

}

/**
 * correlating controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartProductGroupHolder_Controller extends Page_Controller {

    protected $groupProducts;

    /**
     * statements to be called on oject instantiation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2010
     * @return void
     */
    public function init() {


        // Get Products for this category
        if (!isset($_GET['start']) ||
                !is_numeric($_GET['start']) ||
                (int) $_GET['start'] < 1) {
            $_GET['start'] = 0;
        }

        $SQL_start = (int) $_GET['start'];

        $this->groupProducts = SilvercartProduct::getRandomProducts(5);

        // Initialise formobjects
        $templateProductList = new DataObjectSet();
        $productIdx = 0;
        if ($this->groupProducts) {
            $productAddCartForm = $this->getCartFormName();
            foreach ($this->groupProducts as $product) {
                $this->registerCustomHtmlForm('ProductAddCartForm' . $productIdx, new $productAddCartForm($this, array('productID' => $product->ID)));
                $product->setField('Link', $product->Link());
                $product->productAddCartForm = $this->InsertCustomHtmlForm(
                                'ProductAddCartForm' . $productIdx,
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
     * to be called on a template
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @return DataObjectSet set of randomly choosen product objects
     * @since 23.10.2010
     */
    public function randomProducts() {
        return $this->groupProducts;
    }

}