<?php

/**
 * holder for customers private area
 * 
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class SilvercartMyAccountHolder extends Page {

    public static $singular_name = "Account holder";
    public static $allowed_children = array(
        "SilvercartDataPage",
        "SilvercartOrderHolder",
        "SilvercartAddressHolder"
    );

}

/**
 * correlating controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class SilvercartMyAccountHolder_Controller extends Page_Controller {

    /**
     * statements to be called on object initialisation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.11.2010
     * @return void
     */
    public function init() {
        Session::clear("redirect"); //if customer has been to the checkout yet this is set to direct him back to the checkout after address editing
        parent::init();
    }

}