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
 * @subpackage Base
 */

/**
 * Provides methods for common tasks in SilverCart.
 * 
 * @package Silvercart
 * @subpackage Base
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 16.02.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartTools extends Object {

    /**
     * Returns the base URL segment that's used for inclusion of css and
     * javascript files via Requirements.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.02.2012
     */
    public static function getBaseURLSegment() {
        $baseUrl = Director::baseUrl();

        if ($baseUrl === '/') {
            $baseUrl = '';
        }

        if (!empty($baseUrl) &&
             substr($baseUrl, -1) != '/') {

            $baseUrl .= '/';
        }

        return $baseUrl;
    }

    /**
     * Remove chars from the given string that are not appropriate for an url
     *
     * @param string $string String to convert
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.03.2012
     */
    public static function string2urlSegment($string) {
        $remove     = array('ä',    'ö',    'ü',    'Ä',    'Ö',    'Ü',    '/',    '?',    '&',    '#',    '.',    ',',    ' ', '%', '"', "'", '<', '>');
        $replace    = array('ae',   'oe',   'ue',   'Ae',   'Oe',   'Ue',   '-',    '-',    '-',    '-',    '-',    '-',    '',  '',  '',  '',  '',  '');
        $string     = str_replace($remove, $replace, $string);
        return strtolower(urlencode($string));
    }

    /**
     * Writes a log entry
     *
     * @param string $method The method name of the caller
     * @param string $text   The text to log
     * @param string $class  The class name of the caller
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.03.2012
     */
    public static function Log($method, $text, $class = 'undefined') {
        $path = Director::baseFolder() . '/silvercart/log/' . $class . '.log';
        $text = sprintf(
            "%s - Method: '%s' - %s\n",
            date('Y-m-d H:i:s'),
            $method,
            $text
        );
        file_put_contents($path, $text, FILE_APPEND);
    }

    /**
     * Returns the attributed DataObjects as string (limited to 150 chars) by
     * the given ComponentSet.
     * 
     * @param ComponentSet $componentSet ComponentSet to get list for
     * @param string       $dbField      Db field to use to display
     * @param int          $maxLength    Maximum string length
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public static function AttributedDataObject($componentSet, $dbField = "Title", $maxLength = 150) {
        $attributedDataObjectStr    = '';
        $attributedDataObjects      = array();

        foreach ($componentSet as $component) {
            $attributedDataObjects[] = $component->{$dbField};
        }
        
        if (!empty($attributedDataObjects)) {
            $attributedDataObjectStr = implode(', ', $attributedDataObjects);

            if (strlen($attributedDataObjectStr) > $maxLength) {
                $attributedDataObjectStr = substr($attributedDataObjectStr, 0, $maxLength) . '...';
            }
        }

        return $attributedDataObjectStr;
    }

    /**
     * returns a single page by IdentifierCode
     * used to retrieve links dynamically
     *
     * @param string $identifierCode the classes name
     * 
     * @return SiteTree | false a single object of the site tree; without param the SilvercartFrontPage will be returned
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public static function PageByIdentifierCode($identifierCode = "SilvercartFrontPage") {
        $page = DataObject::get_one(
            "SiteTree",
            sprintf(
                "`IdentifierCode` = '%s'",
                $identifierCode
            )
        );

        if ($page) {
            return $page;
        } else {
            return false;
        }
    }

    /**
     * returns a page link by IdentifierCode
     *
     * @param string $identifierCode the DataObjects IdentifierCode
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public static function PageByIdentifierCodeLink($identifierCode = "SilvercartFrontPage") {
        $page = self::PageByIdentifierCode($identifierCode);
        if ($page === false) {
            return '';
        }
        return $page->Link();
    }
    
    /**
     * Returns the translated singular name of the given object. If no 
     * translation exists the class name will be returned.
     * 
     * @param DataObject $dataObject DataObject to get singular name for
     * 
     * @return string The objects singular name 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.05.2012
     */
    public static function singular_name_for($dataObject) {
        if (_t($dataObject->ClassName . '.SINGULARNAME')) {
            return _t($dataObject->ClassName . '.SINGULARNAME');
        } else {
            return ucwords(trim(strtolower(preg_replace('/_?([A-Z])/', ' $1', $dataObject->class))));;
        } 
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @param DataObject $dataObject DataObject to get plural name for
     * 
     * @return string the objects plural name
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.05.2012
     */
    public static function plural_name_for($dataObject) {
        if (_t($dataObject->ClassName . '.PLURALNAME')) {
            return _t($dataObject->ClassName . '.PLURALNAME');
        } else {
            $plural_name = self::singular_name_for($dataObject);
            if (substr($plural_name,-1) == 'e') {
                $plural_name = substr($plural_name,0,-1);
            } elseif (substr($plural_name,-1) == 'y') {
                $plural_name = substr($plural_name,0,-1) . 'ie';
            }
            return ucfirst($plural_name . 's');
        }

    }
}
