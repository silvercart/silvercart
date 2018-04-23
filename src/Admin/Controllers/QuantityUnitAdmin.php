<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Controllers\ModelAdmin;
use SilverCart\Model\Product\QuantityUnit;

/**
 * ModelAdmin for QuantityUnits.
 * 
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class QuantityUnitAdmin extends ModelAdmin {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    private static $menuCode = 'products';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    private static $menuSortIndex = 40;

    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-quantity-units';

    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Quantity Units';

    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = array(
        QuantityUnit::class,
    );
    
}



