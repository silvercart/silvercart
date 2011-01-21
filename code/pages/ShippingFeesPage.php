<?php

/**
 * show the shipping fee matrix
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 18.11.2010
 * @license BSD
 */
class ShippingFeesPage extends Page {
    public static $singular_name = "Versandkostenseite";
    public static $allowed_children = array(
        'none'
    );
}

/**
 * corresponding controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 18.11.2010
 * @license BSD
 */
class ShippingFeesPage_Controller extends Page_Controller {

    /**
     * get all carriers; for the frontend
     *
     * @return DataObjectSet all carrier objects
     * @since 18.11.10
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     */
    public function Carriers() {
        $carriers = DataObject::get('Carrier');
        return $carriers;
    }
}

