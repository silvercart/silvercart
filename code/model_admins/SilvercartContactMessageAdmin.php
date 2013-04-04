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
 * ModelAdmin for SilvercartContactMessages
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sascha Koehler <skoehler@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 04.04.2013
 * @license see license file in modules root directory
 */
class SilvercartContactMessageAdmin extends SilvercartModelAdmin {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'customer';

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
    public static $url_segment = 'silvercart-contact-messages';

    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Contact Messages';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'SilvercartContactMessage'
    );

}

