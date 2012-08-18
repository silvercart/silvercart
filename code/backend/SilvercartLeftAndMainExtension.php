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
class SilvercartLeftAndMainExtension extends DataExtension {

    /**
     * The new main menu routine.
     * 
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.01.2012
     */
    public function SilvercartMainMenu() {

        // Don't accidentally return a menu if you're not logged in - it's used to determine access.
        if (!Member::currentUser()) {
            return new DataObjectSet();
        }

        // Encode into DO set
        $menu                  = new DataObjectSet();
        $menuItems             = CMSMenu::get_viewable_menu_items();
        $menuNonCmsIdentifiers = SilvercartConfig::getMenuNonCmsIdentifiers();

        if ($menuItems) {
            foreach ($menuItems as $code => $menuItem) {
                // alternate permission checks (in addition to LeftAndMain->canView())
                if (
                    isset($menuItem->controller) 
                    && $this->owner->hasMethod('alternateMenuDisplayCheck')
                    && !$this->owner->alternateMenuDisplayCheck($menuItem->controller)
                ) {
                    continue;
                }

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

                if (!empty($menuItem->controller)) {
                    $urlSegment = singleton($menuItem->controller)->stat('url_segment');
                    $doSkip     = false;

                    foreach ($menuNonCmsIdentifiers as $identifier) {
                        if (substr($urlSegment, 0, strlen($identifier)) === $identifier) {
                            $doSkip = true;
                        }
                    }

                    if ($doSkip) {
                        continue;
                    }
                }

                // already set in CMSMenu::populate_menu(), but from a static pre-controller
                // context, so doesn't respect the current user locale in _t() calls - as a workaround,
                // we simply call LeftAndMain::menu_title_for_class() again if we're dealing with a controller
                if ($menuItem->controller) {
                    $defaultTitle = LeftAndMain::menu_title_for_class($menuItem->controller);
                    $title = _t("{$menuItem->controller}.MENUTITLE", $defaultTitle);
                } else {
                    $title = $menuItem->title;
                }

                $menu->push(new ArrayData(array(
                    "MenuItem"    => $menuItem,
                    "Title"       => Convert::raw2xml($title),
                    "Code"        => $code,
                    "Link"        => $menuItem->url,
                    "LinkingMode" => $linkingmode
                )));
            }
        }

        return $menu;
    }

    /**
     * Returns Silvercart specific menus.
     * 
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.01.2012
     */
    public function SilvercartMenus() {
        $silvercartMenus = new DataObjectSet();
        $menuItems       = CMSMenu::get_viewable_menu_items();

        foreach (SilvercartConfig::getRegisteredMenus() as $menu) {
            $menuSectionIndicator = '';
            $modelAdmins          = new DataObjectSet();
            $groupedModelAdmins   = new DataObjectSet();

            foreach ($menuItems as $code => $menuItem) {
                if (
                    isset($menuItem->controller) &&
                    $this->owner->hasMethod('alternateMenuDisplayCheck') &&
                    !$this->owner->alternateMenuDisplayCheck($menuItem->controller)
                ) {
                    continue;
                }

                if (empty($menuItem->controller)) {
                    continue;
                }

                $menuCode = singleton($menuItem->controller)->stat('menuCode');

                if ($menuCode == $menu['code']) {
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

                    if ($title == $this->owner->SectionTitle()) {
                        $menuSectionIndicator = ': '.$title;
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
                }
            }

            $modelAdmins->sort('SortIndex', 'ASC');
            $currentSection = '';

            foreach ($modelAdmins as $modelAdmin) {
                if ($modelAdmin->Section != $currentSection) {
                    $currentSection = $modelAdmin->Section;

                    if ($modelAdmin->Section != 'base') {
                        $groupedModelAdmins->push(
                            new DataObject(
                                array(
                                    "IsSection" => true,
                                    "name"      => _t('SilvercartMenu.SECTION_'.$currentSection, $currentSection)
                                )
                            )
                        );
                    }
                }

                $groupedModelAdmins->push($modelAdmin);
            }

            if ($groupedModelAdmins->Count() > 0) {
                $silvercartMenus->push(
                    new DataObject(
                        array(
                            'name'        => $menu['name'],
                            'MenuSection' => $menuSectionIndicator,
                            'code'        => $menu['code'],
                            'ModelAdmins' => $groupedModelAdmins
                        )
                    )
                );
            }
        }

        return $silvercartMenus;
    }

    /**
     * Returns the active CMS section title
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.01.2012
     */
    public function getCmsSection() {
        $section               = '';
        $urlSegment            = $this->owner->stat('url_segment');
        $menuNonCmsIdentifiers = SilvercartConfig::getMenuNonCmsIdentifiers();
        $foundCmsSection       = true;

        foreach ($menuNonCmsIdentifiers as $identifier) {
            if (in_array(substr($urlSegment, 0, strlen($identifier)), $menuNonCmsIdentifiers)) {
                $foundCmsSection = false;
            }
        }

        if ($foundCmsSection) {
            $section = ': '.$this->owner->SectionTitle();
        }

        return $section;
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
