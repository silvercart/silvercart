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
 * @subpackage Backend
 */

/**
 * Extension for the LeftAndMain class.
 * 
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 16.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartLeftAndMain extends DataExtension {
    
    /**
     * Returns the used SilverCart version
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.02.2013
     */
    public function SilvercartVersion() {
        return SilvercartConfig::SilvercartVersion();
    }

    /**
     * Returns Silvercart specific menus.
     * 
     * @return ArrayList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.02.2013
     */
    public function SilvercartMenus() {
        $silvercartMenus = new ArrayList();
        $menuItems       = CMSMenu::get_viewable_menu_items();
        
        foreach (SilvercartConfig::getRegisteredMenus() as $menu) {
            $modelAdmins          = new ArrayList();

            foreach ($menuItems as $code => $menuItem) {
                if (isset($menuItem->controller) &&
                    $this->owner->hasMethod('alternateMenuDisplayCheck') &&
                    !$this->owner->alternateMenuDisplayCheck($menuItem->controller)) {
                    continue;
                }

                if (empty($menuItem->controller)) {
                    continue;
                }

                $menuCode = singleton($menuItem->controller)->stat('menuCode');

                if ($menuCode == $menu['code'] ||
                    (is_null($menuCode)) &&
                     $menu['code'] == 'default') {
                    $defaultTitle = LeftAndMain::menu_title_for_class($menuItem->controller);
                    $title = _t("{$menuItem->controller}.MENUTITLE", $defaultTitle);

                    $linkingmode = "";

                    if (strpos($this->owner->Link(), $menuItem->url) !== false) {
                        if ($this->owner->Link() == $menuItem->url) {
                            $linkingmode = "current";

                        // default menu is the one with a blank {@link url_segment}
                        } else if (singleton($menuItem->controller)->stat('url_segment') == '') {
                            if ($this->owner->Link() == $this->owner->stat('url_base').'/') {
                                $linkingmode = "current";
                            }
                        } else {
                            $linkingmode = "current";
                        }
                    }

                    // Get the menu section
                    $menuSection = singleton($menuItem->controller)->stat('menuSection');

                    if (empty($menuSection)) {
                        $menuSection = 'base';
                    }

                    // Get the menu sort index
                    $menuSortIndex = singleton($menuItem->controller)->stat('menuSortIndex');

                    if (empty($menuSortIndex )) {
                        $menuSortIndex = 1000;
                    }

                    $modelAdmins->push(
                        new ArrayData(
                            array(
                                "MenuItem"    => $menuItem,
                                "Title"       => Convert::raw2xml($title),
                                "Code"        => $code,
                                "IsSection"   => false,
                                "Section"     => $menuSection,
                                "SortIndex"   => $menuSortIndex,
                                "Link"        => $menuItem->url,
                                "LinkingMode" => $linkingmode
                            )
                        )
                    );
                    unset($menuItems[$code]);
                }
            }

            $modelAdmins->sort('SortIndex', 'ASC');

            if ($modelAdmins->exists()) {
                $silvercartMenus->push(
                    new DataObject(
                        array(
                            'name'        => $menu['name'],
                            'code'        => $menu['code'],
                            'Code'        => $menu['code'],
                            'ModelAdmins' => $modelAdmins
                        )
                    )
                );
            }
        }

        return $silvercartMenus;
    }

    /**
     * Returns the base url.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.05.2012
     */
    public function BaseUrl() {
        return Director::baseUrl();
    }
}
