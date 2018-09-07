<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Controllers\ModelAdmin;
use SilverCart\Model\Payment\PaymentStatus;

/**
 * ModelAdmin for PaymentStatus.
 * 
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2018 pixeltricks GmbH
 * @since 07.09.2018
 * @license see license file in modules root directory
 */
class PaymentStatusAdmin extends ModelAdmin
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
    private static $menuSortIndex = 11;
    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-payment-status';
    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Payment Status';
    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = [
        PaymentStatus::class,
    ];
}