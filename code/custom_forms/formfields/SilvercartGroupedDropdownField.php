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
 * A grouped dropdown formfield that can display an unlimited depth of entries.
 *
 * @package Silvercart
 * @subpackage Forms_FormFields
 * @copyright 2013 pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 03.06.2011
 * @license see license file in modules root directory
 */
class SilvercartGroupedDropdownField extends DropdownField {

    /**
     * Markup to render the field with
     * 
     * @param array $properties only declared to be compatible with parent
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>,
     *	       Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.12.2012
     */
    public function Field($properties = array()) {
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
     * @param array $definition Option set definition
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
