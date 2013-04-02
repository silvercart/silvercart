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
 * Fixes the missing index of the DataObjectManagers SortableDataObject
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 28.03.2012
 * @license see license file in modules root directory
 */
class SilvercartSortableDataObject extends DataExtension {

    /**
     * indexes
     *
     * @var array
     */
    public static $indexes = array(
        'SortOrder' => '(SortOrder)'
        );
    
    /**
     * Adds the sortable extensions to the given class
     *
     * @param string $className Class to extend
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function add_sortable_class($className) {
//        if (!SortableDataObject::is_sortable_class($className)) {
//            DataObject::add_extension($className, 'SilvercartSortableDataObject');
//            DataObject::add_extension($className, 'SortableDataObject');
//            SortableDataObject::$sortable_classes[] = $className;
//        }
    }
    
    /**
     * Adds the sortable extensions to the given classes
     *
     * @param array $classes Classes to extend
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public static function add_sortable_classes(array $classes) {
        foreach ($classes as $class) {
            self::add_sortable_class($class);
        }
    }

}