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
 * @subpackage pages
 */

/**
 * SilvercartDownloadPageHolder 
 *
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 12.07.2012
 * @copyright 2012 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License 
 */
class SilvercartDownloadPageHolder extends Page {
    
    /**
     * allowed child pages in site tree
     *
     * @var array
     */
    public static $allowed_children = array(
      'SilvercartDownloadPage',  
    );
    
    /**
     * returns the singular name
     * 
     * @return string 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 12.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }
    
    /**
     * returns the plural name
     * 
     * @return string 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 12.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }
    
}

/**
 * SilvercartDownloadPageHolder_Controller
 *
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 12.07.2012
 * @copyright 2012 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License 
 */
class SilvercartDownloadPageHolder_Controller extends Page_Controller {
    
}