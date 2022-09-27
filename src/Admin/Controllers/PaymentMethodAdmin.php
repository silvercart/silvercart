<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Controllers\ModelAdmin;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Payment\PaymentStatus;

/**
 * ModelAdmin for PaymentMethods.
 * 
 * @package SilverCart
 * @subpackage Admin\Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class PaymentMethodAdmin extends ModelAdmin
{
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
    private static $menuSortIndex = 10;
    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-payment-methods';
    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Payment Methods';
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
    private static $menu_icon_class = 'font-icon-credit-card';
    /**
     * Name of DB field to make records sortable by.
     *
     * @var string
     */
    private static $sortable_field = 'Sort';
    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = [
        PaymentMethod::class,
        PaymentStatus::class,
    ];
}