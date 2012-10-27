<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
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
 * @copyright 2012 pixeltricks GmbH
 * @since 05.09.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCustomerAdmin extends ModelAdmin {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'config';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSortIndex = 119;

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSection = 'others';

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
    public static $menu_title = 'Carriers';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'Member' => array(
            'collection_controller' => 'SilvercartCustomerAdmin_CollectionController'
        )
    );

    /**
     * Constructor
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2012
     */
    public function __construct() {
        self::$menu_title = _t('SilvercartCustomerAdmin.customers');
        
        parent::__construct();
    }
    
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
}