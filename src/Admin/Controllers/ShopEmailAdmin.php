<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Controllers\ModelAdmin;
use SilverCart\Model\ShopEmail;

/**
 * ModelAdmin for ShopEmails.
 * 
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class ShopEmailAdmin extends ModelAdmin {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    private static $menuCode = 'config';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    private static $menuSortIndex = 20;

    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-shop-emails';

    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Shop Emails';

    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = array(
        ShopEmail::class,
    );
    
}


