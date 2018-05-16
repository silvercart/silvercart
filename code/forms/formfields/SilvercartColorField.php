<?php

/**
 * Color form field.
 * 
 * @package SilverCart
 * @subpackage Forms_FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 16.05.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartColorField extends FormField {
    
    /**
     * Changes the type attribute to "color" and returns the default attributes.
     * 
     * @return string
     */
    public function getAttributes() {
        $attributes = parent::getAttributes();
        $attributes['type'] = 'color';
        return $attributes;
    }
    
}