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
 * @subpackage Base
 */

/**
 * Adds the ability to update many_many_extraFields of a component sets item.
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 18.06.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartComponentSetDecorator extends DataObjectDecorator {
    
    protected $type;
    protected $ownerObj;
    protected $ownerClass;
    protected $tableName;
    protected $childClass;
    protected $joinField;

    /**
     * Updates the many_many_extraFields of a component sets item.
     *
     * @param Component $item        Component to update many_many_extraFields for
     * @param array     $extraFields many_many_extraFields to update
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.01.2012
     */
    public function update($item, $extraFields = null) {
        $this->loadMetaData();
        if (!isset($item)) {
            user_error("ComponentSet::update() Not passed an object or ID", E_USER_ERROR);
        }

        if (is_object($item)) {
            if (!is_a($item, $this->childClass)) {
                user_error("ComponentSet::update() Tried to add an '{$item->class}' object, but a '{$this->childClass}' object expected", E_USER_ERROR);
            }
        } else {
            if (!$this->childClass) {
                user_error("ComponentSet::update() \$this->childClass not set", E_USER_ERROR);
            }

            $item = DataObject::get_by_id($this->childClass, $item);
            if (!$item) {
                return;
            }
        }

        // If we've already got a database object, then update the database
        if ($this->ownerObj->ID && is_numeric($this->ownerObj->ID)) {
            $this->loadChildIntoDatabase($item, $extraFields);
        }
    }
    
    /**
     * Saves many_many_extraFields into the database for the given $item.
     *
     * @param Component $item        Component to update many_many_extraFields for
     * @param array     $extraFields many_many_extraFields to update
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.01.2012
     */
    protected function loadChildIntoDatabase($item, $extraFields = null) {
        $this->loadMetaData();
        if ($this->type == '1-to-many') {
            $child = DataObject::get_by_id($this->childClass,$item->ID);
            if (!$child) {
                $child = $item;
            }
            $joinField = $this->joinField;
            $child->$joinField = $this->ownerObj->ID;
            $child->write();
        } else {		
            $parentField = $this->ownerClass . 'ID';
            $childField = ($this->childClass == $this->ownerClass) ? "ChildID" : ($this->childClass . 'ID');

            DB::query( "DELETE FROM \"$this->tableName\" WHERE \"$parentField\" = {$this->ownerObj->ID} AND \"$childField\" = {$item->ID}" );

            $extraKeys = $extraValues = '';
            if ($extraFields) {
                foreach ($extraFields as $k => $v) {
                    $extraKeys .= ", \"$k\"";
                    $extraValues .= ", '" . DB::getConn()->addslashes($v) . "'";
                }
            }

            DB::query("INSERT INTO \"$this->tableName\" (\"$parentField\",\"$childField\" $extraKeys) VALUES ({$this->ownerObj->ID}, {$item->ID} $extraValues)");
        }
    }
    
    /**
     * Loads some meta data of the owner object.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.01.2012
     */
    protected function loadMetaData() {
        $componentInfo      = $this->owner->getComponentInfo();
        $this->type         = $componentInfo['type'];
        $this->ownerObj     = $componentInfo['ownerObj'];
        $this->ownerClass   = $componentInfo['ownerClass'];
        $this->tableName    = $componentInfo['tableName'];
        $this->childClass   = $componentInfo['childClass'];
        $this->joinField    = $componentInfo['joinField'];
    }
    
}