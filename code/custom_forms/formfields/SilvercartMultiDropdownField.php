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
 * Adds some additional functionallity to default text field
 *
 * @package Silvercart
 * @subpackage FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 23.04.2012
 * @license see license file in modules root directory
 */
class SilvercartMultiDropdownField extends DropdownField {
    
    /**
     * Returns the HTML for the field
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2012
     */
    public function Field() {
        $baseUrl = SilvercartTools::getBaseURLSegment();
        Requirements::css($baseUrl . 'silvercart/css/screen/backend/SilvercartMultiDropdownField.css');
        Requirements::javascript($baseUrl . 'silvercart/script/SilvercartMultiDropdownField.js');
        
        $options = '';

        $source = $this->getSource();
        if ($source) {
            // For SQLMap sources, the empty string needs to be added specially
            if (is_object($source) && $this->emptyString) {
                $options .= $this->createTag('option', array('value' => ''), $this->emptyString);
            }

            foreach ($source as $value => $title) {
                // Blank value of field and source (e.g. "" => "(Any)")
                if ($value === '' && ($this->value === '' || $this->value === null)) {
                    $selected = 'selected';
                } else {
                        // Normal value from the source
                    if ($value) {
                        $selected = ($value == $this->value) ? 'selected' : null;
                    } else {
                        // Do a type check comparison, we might have an array key of 0
                        $selected = ($value === $this->value) ? 'selected' : null;
                    }

                    $this->isSelected = ($selected) ? true : false;
                }

                $options .= $this->createTag(
                    'option',
                    array(
                        'selected'  => $selected,
                        'value'     => $value
                    ),
                    Convert::raw2xml($title)
                );
            }
        }

        $attributes = array(
                'class'     => ($this->extraClass() ? $this->extraClass() : ''),
                'id'        => $this->id(),
                'name'      => $this->name . '-orig',
                'tabindex'  => $this->getTabIndex()
        );

        if ($this->disabled) {
            $attributes['disabled'] = 'disabled';
        }

        return $this->createTag('select', $attributes, $options);
    }
}