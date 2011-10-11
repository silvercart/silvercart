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
 * @subpackage Pages
 */

/**
 * Confirmation page for Closed-Opt-In
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 20.10.2010
 */
class SilvercartRegisterConfirmationPage extends Page {

    public static $singular_name = "register confirmation page";
    
    public static $icon = "silvercart/images/page_icons/registration_confirmation";
}

/**
 * Controller of this page type
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartRegisterConfirmationPage_Controller extends Page_Controller {

    /**
     * statments to be  executed on initialisation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.10.2010
     * @return void
     */
    public function init() {
        parent::init();
    }
}
