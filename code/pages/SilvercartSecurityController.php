<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * Injects CustomHtmlForm objects into the security controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 15.10.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartSecurityController extends DataExtension {
    
    /**
     * We register the common forms for SilvercartPages here.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.10.2011
     */
    public function onBeforeInit() {
        if (!isset($_SESSION['Silvercart'])) {
            $_SESSION['Silvercart'] = array();
        }
        if (!isset($_SESSION['Silvercart']['errors'])) {
            $_SESSION['Silvercart']['errors'] = array();
        }
        
        $this->owner->registerCustomHtmlForm('SilvercartQuickSearchForm', new SilvercartQuickSearchForm($this->owner));
        $this->owner->registerCustomHtmlForm('SilvercartQuickLoginForm',  new SilvercartQuickLoginForm($this->owner));
        
        SilvercartPlugin::call($this->owner, 'init', array($this->owner));
    }
}