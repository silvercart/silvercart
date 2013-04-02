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
 * ModelAdmin for Members.
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 05.09.2012
 * @license see license file in modules root directory
 */
class SilvercartCustomerAdmin extends ModelAdmin {

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
    public static $url_segment = 'silvercart-customers';

    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Customers';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'Member'
    );
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2012
     */
    public function init() {
        parent::init();
        $this->extend('updateInit');
    }
    
    /**
     * label for backend
     *
     * @return string title for backend navigation 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2013
     */
    public function SectionTitle() {
        return _t("SilvercartCustomer.PLURALNAME");
    }
}