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
 * ModelAdmin for SilvercartCarriers.
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 16.01.2012
 * @license see license file in modules root directory
 */
class SilvercartCarrierAdmin extends SilvercartModelAdmin {
    
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
    public static $menuSortIndex = 50;

    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-carriers';

    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Carriers';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'SilvercartCarrier',
    );
}

