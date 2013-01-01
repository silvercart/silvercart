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
 * This site is not visible in the frontend.
 * Its purpose is to gather the meta navigation sites in the backend for better usability.
 * Now a shop admin has a correspondence between front end site order and backend tree structure.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartMetaNavigationHolder extends Page {

    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    public static $icon = "silvercart/images/page_icons/metanavigation_holder";

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     *
     * @return string the objects plural name
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }

    /**
     * Returns the given WidgetSet many-to-many relation.
     * If there is no relation, the parent relation will be recursively used
     *
     * @param string $widgetSetName The name of the widget set relation
     *
     * @return SilvercartWidgetSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.10.2012
     */
    public function getWidgetSetRelation($widgetSetName) {
        $widgetSet = $this->getManyManyComponents($widgetSetName);
        $parent    = $this->getParent();

        if ($widgetSet->Count() == 0 &&
            $parent &&
            ($parent instanceof SilvercartMetaNavigationPage ||
             $parent instanceof SilvercartMetaNavigationHolder) &&
            array_key_exists($widgetSetName, $parent->many_many()) &&
            $parent->$widgetSetName()->count() > 0) {

            $widgetSet = $parent->$widgetSetName();
        }
        return $widgetSet;
    }
}

/**
 * correlating controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartMetaNavigationHolder_Controller extends Page_Controller {


    /**
     * Uses the children of SilvercartMetaNavigationHolder to render a subnavigation
     * with the SilvercartSubNavigation.ss template.
     * 
     * @param string $identifierCode param only added because it exists on parent::getSubNavigation
     *                               to avoid strict notice
     *
     * @return string
     */
    public function getSubNavigation($identifierCode = 'SilvercartProductGroupHolder') {
        $root   = $this->dataRecord;
        $output = '';
        if ($root->ClassName != 'SilvercartMetaNavigationHolder') {
            while ($root->ClassName != 'SilvercartMetaNavigationHolder') {
                $root = $root->Parent();
                if ($root->ParentID == 0) {
                    $root = null;
                    break;
                }
            }
        }
        if (!is_null($root)) {
            $elements = array(
                'SubElements' => $root->Children(),
            );
            $output = $this->customise($elements)->renderWith(
                array(
                    'SilvercartSubNavigation',
                )
            );
        }
        return $output;
    }
}
