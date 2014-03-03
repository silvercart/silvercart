<?php
/**
 * Copyright 2014 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage ModelAdmins
 */

/**
 * ModelAdmin for SilvercartPaymentMethods.
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sebastian Diel <sdiel@pixeltricks.de>,
 *         Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2014 pixeltricks GmbH
 * @since 03.03.2014
 * @license see license file in modules root directory
 */
class SilvercartPaymentMethodAdmin extends SilvercartModelAdmin {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'handling';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSortIndex = 10;

    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-payment-methods';

    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Payment Methods';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'SilvercartPaymentMethod'
    );
    
}



