<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Controllers\ModelAdmin;
use SilverCart\Model\Product\AvailabilityStatus;
use SilverCart\Model\Product\Manufacturer;
use SilverCart\Model\Product\ProductCondition;
use SilverCart\Model\Product\QuantityUnit;
use SilverCart\Model\Product\Tax;

/**
 * ModelAdmin for any product property like:
 * <ul>
 *  <li>AvailabilityStatus</li>
 *  <li>Manufacturer</li>
 *  <li>QuantityUnit</li>
 *  <li>Tax</li>
 *  <li>ProductCondition</li>
 * </ul>
 * 
 * @package SilverCart
 * @subpackage Admin\Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2022 pixeltricks GmbH
 * @since 04.03.2022
 * @license see license file in modules root directory
 */
class ProductPropertiesAdmin extends ModelAdmin
{
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
    private static $menuSortIndex = 15;
    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-product-properties';
    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Properties';
    /**
     * Menu icon
     * 
     * @var string
     */
    private static $menu_icon = null;
    /**
     * Menu icon CSS class
     * 
     * @var string
     */
    private static $menu_icon_class = 'font-icon-tags';
    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = [
        AvailabilityStatus::class,
        Manufacturer::class,
        QuantityUnit::class,
        Tax::class,
        ProductCondition::class,
    ];
}