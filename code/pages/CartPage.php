<?php

/**
 * represents a shopping cart. Every customer has one initially.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license BSD
 * @since 23.10.2010
 */
class CartPage extends Page {

    public static $singular_name = "";

    /**
     * Constructor
     *
     * @param array|null $record      This will be null for a new database record.  Alternatively, you can pass an array of
     *                                field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                don't have their defaults set.
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 2.2.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$singular_name = _t('Page.CART');
        parent::__construct($record, $isSingleton);
    }

    /**
     * default instances related to $this
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @return void
     * @since 23.10.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $records = DataObject::get_one($this->ClassName);
        if (!$records) {
            $cartPage = new CartPage();
            $cartPage->Title = _t('Page.CART');
            $cartPage->URLSegment = _t('CartPage.URL_SEGMENT', 'cart');
            $cartPage->Status = "Published";
            $cartPage->ShowInMenus = true;
            $cartPage->ShowInSearch = false;
            $cartPage->write();
            $cartPage->publish("Stage", "Live");
        }
    }

}
/**
 * related controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class CartPage_Controller extends Page_Controller {

    /**
     * Calls the registered shoppingcart modules method "ShoppingCartInit"
     * if available.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 21.01.2011
     */
    public function init() {
        $registeredModules  = ShoppingCart::$registeredModules;

        foreach ($registeredModules as $registeredModule) {
            $registeredModuleObj = new $registeredModule();

            if ($registeredModuleObj->hasMethod('ShoppingCartInit')) {
                $registeredModuleObj->ShoppingCartInit();
            }
        }
        
        parent::init();
    }
}