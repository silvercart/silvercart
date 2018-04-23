<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Controllers\ModelAdmin;
use SilverCart\Model\Product\Tax;

/**
 * ModelAdmin for Taxes.
 * 
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class TaxAdmin extends ModelAdmin {

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
    private static $menuSortIndex = 50;

    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-tax';

    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Taxes';

    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = array(
        Tax::class,
    );
    
}


