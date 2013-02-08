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
 * @since 29.03.2011
 * @copyright 2011 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdateAdmin extends ModelAdmin {

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
    public static $menuSortIndex = 140;

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSection = 'maintenance';

    /**
     * Managed models
     *
     * @var array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public static $managed_models = array(
        'SilvercartUpdate'
    );
    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-update';
    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Silvercart Updates';

    
    /**
     * Class name of the results table to use
     *
     * @var string
     */
    protected $resultsTableClassName = 'SilvercartUpdateTableListField';

    /**
     * The priority for backend menu
     *
     * @var int 
     */
    public static $menu_priority = -1;
    
    /**
     * title in the top bar of the CMS
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.08.2012
     */
    public function SectionTitle() {
        $sectionTitle = _t('SilvercartUpdateAdmin.SILVERCART_UPDATE');
        if (SilvercartUpdate::get()->filter("Status", "remaining")->exists()) {
        $sectionTitle .= ' (' . DataObject::get('SilvercartUpdate',"\"Status\"='remaining'")->count() . ')';
        }
        return $sectionTitle;
    }

}
