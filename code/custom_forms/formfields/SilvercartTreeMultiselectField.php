<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms_FormFields
 */

/**
 * A formfield for relating multiple pages to an object
 *
 * @package Silvercart
 * @subpackage Forms_FormFields
 * @copyright 2013 pixeltricks GmbH
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.05.2012
 * @license see license file in modules root directory 
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