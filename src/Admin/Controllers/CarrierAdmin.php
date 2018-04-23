<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Controllers\ModelAdmin;
use SilverCart\Model\Shipment\Carrier;

/**
 * ModelAdmin for Carriers.
 * 
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class CarrierAdmin extends ModelAdmin {
    
    /**
     * Name of DB field to make records sortable by.
     *
     * @var string
     */
    private static $sortable_field = 'priority';

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    private static $menuCode = 'handling';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    private static $menuSortIndex = 50;

    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-carriers';

    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Carriers';

    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = array(
        Carrier::class,
    );
}

