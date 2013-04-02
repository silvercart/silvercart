<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
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