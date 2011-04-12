<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
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
 * @subpackage Backend
 */

/**
 * The Silvercart configuration backend.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 08.04.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartContactMessageAdmin extends ModelAdmin {

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'SilvercartContactMessage',
    );
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
    public static $menu_title = 'Kontaktanfragen';
    /**
     * The collection controller class to use for the shop configuration.
     *
     * @var string
     */
    public static $collection_controller_class = 'SilvercartContactMessageAdmin_CollectionController';

    public static $menu_priority = -1;

    /**
     * constructor
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2011
     */
    public function __construct() {
        self::$menu_title = _t('SilvercartContactMessageAdmin.MENU_TITLE', 'Contact Messages');
        parent::__construct();
    }

}

/**
 * Modifies the model admin search panel.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 08.04.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartContactMessageAdmin_CollectionController extends ModelAdmin_CollectionController {

    public $showImportForm = false;

    /**
     * Disable the creation of SilvercartContactMessage DataObjects.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2011
     */
    public function alternatePermissionCheck() {
        return false;
    }

}
