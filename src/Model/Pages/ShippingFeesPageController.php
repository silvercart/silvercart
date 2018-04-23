<?php

namespace SilverCart\Model\Pages;

use SilverCart\Model\Pages\MetaNavigationHolderController;
use SilverCart\Model\Shipment\Carrier;

/**
 * ShippingFeesPage Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ShippingFeesPageController extends MetaNavigationHolderController {

    /**
     * get all carriers; for the frontend
     *
     * @return DataList all carriers or empty DataList
     * 
     * @since 18.11.10
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     */
    public function Carriers() {
        $carriers = Carrier::get()->sort(array('priority' => 'DESC'));
        return $carriers;
    }
}