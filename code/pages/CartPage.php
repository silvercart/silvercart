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

    public static $singular_name = "Warenkorb";
    public static $plural_name = "Warenkörbe";

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
            $cartPage->Title = "Warenkorb";
            $cartPage->URLSegment = "warenkorb";
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

}