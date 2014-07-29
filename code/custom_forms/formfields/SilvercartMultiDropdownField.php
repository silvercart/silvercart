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
 * Adds some additional functionallity to default text field
 *
 * @package Silvercart
 * @subpackage Forms_FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 23.04.2012
 * @license see license file in modules root directory
 */
class SilvercartMultiDropdownField extends DropdownField {
    
    /**
     * Returns the HTML for the field
     * 
     * @param array $properties Properties
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.06.2014
     */
    public function Field($properties = array()) {
        $baseUrl = SilvercartTools::getBaseURLSegment();
        Requirements::css($baseUrl . 'silvercart/css/screen/backend/SilvercartMultiDropdownField.css');
        Requirements::javascript($baseUrl . 'silvercart/script/SilvercartMultiDropdownField.js');
        return parent::Field($properties);
    }

    /**
     * Manipulates the name attribute
     * 
     * @return array
     */
    public function getAttributes() {
        $name = $this->getName();
        if (strpos($name, ']') == strlen($name) - 1) {
            $newName = str_replace(']', '-orig]', $this->getName());
        } else {
            $newName = $name . '-orig';
        }
        return array_merge(
                parent::getAttributes(),
                array(
                    'name' => $newName,
                )
        );
    }

    /**
     * Adds 'dropdown' to the css class names.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.06.2014
     */
    public function extraClass() {
        return parent::extraClass() . ' dropdown';
    }

}