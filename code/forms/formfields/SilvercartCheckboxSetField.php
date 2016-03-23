<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of the SilverStripe Community module.
 *
 * @package Community
 * @subpackage Formfields
 */

/**
 * Extension for the default CheckboxSetField.
 *
 * @package Community
 * @subpackage Formfields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.07.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartCheckboxSetField extends DataExtension {
    
    /**
     * Returns a string which represents the selected options.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.04.2013
     */
    public function SelectedOptionsString() {
        $selectedOptionsString = _t('SilvercartOrderSearchForm.PLEASECHOOSE');
        
        $source = $this->owner->source;
        $values = $this->owner->value;
        if ($values instanceof SS_List || is_array($values)) {
            $items = $values;
        } else {
            $items = explode(',', $values);
            $items = str_replace('{comma}', ',', $items);
        }
        
        if (!empty($items) &&
            !empty($source)) {
            
            $itemTitles = array();
            foreach ($source as $value => $item) {
                if (in_array($value, $items)) {
                    if ($item instanceof DataObject) {
                        $value = $item->ID;
                        $title = $item->Title;
                    } else {
                        $title = $item;
                    }
                    $itemTitles[] = $title;
                }
            }
            $selectedOptionsString = implode(', ', $itemTitles);
        }
        
        return $selectedOptionsString;
    }
    
    /**
     * Returns whether there are selected options.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.09.2014
     */
    public function HasSelectedOptions() {
        $hasSelectedOptions = false;
        
        $items  = array();
        $source = $this->owner->source;
        $values = $this->owner->value;
        if ($values instanceof SS_List || is_array($values)) {
            $items = $values;
        } elseif (!empty ($values)) {
            $items = str_replace('{comma}', ',', explode(',', $values));
        }
        
        if (!empty($items) &&
            !empty($source) &&
            count($items) > 0) {
            $hasSelectedOptions = true;
        }
        
        return $hasSelectedOptions;
    }
    
}