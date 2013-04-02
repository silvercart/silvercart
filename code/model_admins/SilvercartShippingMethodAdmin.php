<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage ModelAdmins
 */

/**
 * ModelAdmin for SilvercartShippingMethods.
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 16.01.2012
 * @license see license file in modules root directory
 */
class SilvercartShippingMethodAdmin extends SilvercartModelAdmin {
    
    /**
     * Name of DB field to make records sortable by.
     *
     * @var string
     */
    public static $sortable_field = 'priority';

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
    public static $menuSortIndex = 20;

    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-shipping-methods';

    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Shipping Methods';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'SilvercartShippingMethod'
    );

    /**
     * Definition of the Importers for the managed model.
     *
     * @var array
     */
    public static $model_importers = array(
        'SilvercartShippingMethod'  => 'SilvercartShippingMethodCsvBulkLoader',
    );
}



