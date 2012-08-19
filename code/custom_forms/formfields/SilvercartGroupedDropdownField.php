<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * A grouped dropdown formfield that can display an unlimited depth of entries.
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 03.06.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartGroupedDropdownField extends DropdownField {

    /**
     * HTML for field
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.08.2012
     */
    public function Field() {
            // Initialisations
            $options = '';
            $classAttr = '';

            if ($extraClass = trim($this->extraClass())) {
                    $classAttr = "class=\"$extraClass\"";
            }

            foreach ($this->getSource() as $value => $title) {
                    if (is_array($title)) {
            $options .= "<optgroup label=\"$value\">";
            $options .= $this->buildOptionSet($title);
                            $options .= "</optgroup>";
                    } else { // Fall back to the standard dropdown field
                            $selected = $value == $this->value ? " selected=\"selected\"" : "";
                            $options .= "<option$selected value=\"$value\">$title</option>";
                    }
            }

            $id = $this->id();

            return "<select $classAttr name=\"$this->name\" id=\"$id\">$options</select>";
    }
    
    /**
     * Recursively build the option tags for the select field
     * 
     * @param array $definition ???
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.06.2011
     */
    protected function buildOptionSet($definition) {
        $options = '';
        
        foreach ($definition as $key => $value) {
            
            if (is_array($value)) {
                $options .= "<optgroup label=\"$key\">";
                $options .= $this->buildOptionSet($value);
                $options .= "</optgroup>";
            } else {
                $selected = '';

                if ($key == $this->value) {
                    $selected = " selected=\"selected\"";
                }

                $options .= "<option$selected value=\"$key\">$value</option>";
            }
        }
        
        return $options;
    }
}
