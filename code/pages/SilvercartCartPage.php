<?php

/**
 * represents a shopping cart. Every customer has one initially.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license BSD
 * @since 23.10.2010
 */
class SilvercartCartPage extends Page {

    public static $singular_name = "cart page";

}

/**
 * related controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartCartPage_Controller extends Page_Controller {

    /**
     * Initialise the shopping cart.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 11.02.2011
     */
    public function init() {
        if (Member::currentUser() &&
            Member::currentUser()->SilvercartShoppingCart()) {

            Member::currentUser()->SilvercartShoppingCart();
        }
        parent::init();
    }

    /** Indicates wether ui elements for removing items and altering their
     * quantity should be shown in the shopping cart templates.
     *
     * @return boolean true
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public function getEditableShoppingCart() {
        return true;
    }

}