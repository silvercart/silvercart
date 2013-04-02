<?php
/**
 * Copyright 2013 pixeltricks GmbH
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
 * @subpackage Widgets
 */

/**
 * Contains an arbitrary number of widgets.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 20.02.2013
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartWidgetSet extends DataExtension {

    /**
     * used to override the WidgetSet::getCMSFields to use the
     * SilverCarts scaffholding with excluded attributes and relations
     * 
     * @return array
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 20.02.2013
     */
    public function overrideGetCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this->owner);
        $fields->addFieldsToTab(
            'Root.Main',
             $this->owner->scaffoldWidgetAreaFields()
        );
        return $fields;

    }
    
    /**
     * exclude these fields from form scaffolding
     *
     * @return array the field names in a numeric array 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 20.02.2013
     */
    public function excludeFromScaffolding() {
        $excludedFields = array(
            'WidgetArea'
        );
        return $excludedFields;
    }
}
