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
 * @subpackage Forms_GridField
 */

/**
 * Allows to add HTML code to a GridFields DataColumns.
 *
 * @package Silvercart
 * @subpackage Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 26.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldDataColumns extends GridFieldDataColumns {

    /**
     * Casts a field to a string which is safe to insert into HTML
     *
     * @param GridField $gridField GridField to cast value for
     * @param string    $fieldName Field name to cast value for
     * @param string    $value     Value to cast
     * 
     * @return string 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.03.2013
     */
    protected function castValue($gridField, $fieldName, $value) {
        // If a fieldCasting is specified, we assume the result is safe
        if (array_key_exists($fieldName, $this->fieldCasting)) {
            $value = $gridField->getCastedValue($value, $this->fieldCasting[$fieldName]);
        } else if (is_object($value)) {
            // If the value is an object, we do one of two things
            if (method_exists($value, 'Nice')) {
                // If it has a "Nice" method, call that & make sure the result is safe
                $value = Convert::raw2xml($value->Nice());
            } else {
                // Otherwise call forTemplate - the result of this should already be safe
                $value = $value->forTemplate();
            }
        } elseif (!$this->isHtmlAllowedFor($fieldName, $gridField)) {
            // Otherwise, just treat as a text string & make sure the result is safe
            $value = Convert::raw2xml($value);
        }

        return $value;
    }
    
    /**
     * Check whether HTML output is allowed for the given field.
     * 
     * @param string    $fieldName Field name to check
     * @param GridField $gridField GridField to check
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.03.2013
     */
    public function isHtmlAllowedFor($fieldName, $gridField) {
        $htmlIsAllowed = false;
        
        $object = singleton($gridField->getModelClass());
        if ($object instanceof DataObject &&
            $object->hasMethod('allowHtmlDataFor') &&
            is_array($object->allowHtmlDataFor()) &&
            in_array($fieldName, $object->allowHtmlDataFor())) {
            $htmlIsAllowed = true;
        }
        
        return $htmlIsAllowed;
    }

}