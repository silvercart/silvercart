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
 * @subpackage FormFields
 */

/**
 * A formfield for relating multiple pages to an object
 *
 * @package Silvercart
 * @subpackage FormFields
 * @copyright pixeltricks GmbH
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.05.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License 
 */
class SilvercartTreeMultiselectField extends TreeMultiselectField {
    
    /**
     * Get the object where the $keyField is equal to a certain value
     *
     * @param string|int $key Key to get object for
     * 
     * @return DataObject
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.05.2012
     */
    protected function objectForKey($key) {
        if ($key == 'unchanged') {
            return false;
        } elseif ($this->keyField == 'ID') {
            return DataObject::get_by_id($this->sourceObject, $key);
        } else {
            return DataObject::get_one($this->sourceObject, "\"{$this->keyField}\" = '" . Convert::raw2sql($key) . "'");
        }
    }
    
}