<?php

namespace SilverCart\Forms\FormFields;

use SilverCart\Dev\Tools;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\SS_List;

/** 
 * Extension for the default SilverStripe\Forms\CheckboxSetField.
 *
 * @package SilverCart
 * @subpackage Forms_FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckboxSetFieldExtension extends Extension {
    
    /**
     * Returns a string which represents the selected options.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.04.2013
     */
    public function SelectedOptionsString() {
        $selectedOptionsString = Tools::field_label('PleaseChoose');
        
        $source = $this->owner->source;
        $values = $this->owner->value;
        if ($values instanceof SS_List || is_array($values)) {
            $items = $values;
        } else {
            $items = str_replace('{comma}', ',', explode(',', $values));
        }
        
        if (!empty($items) &&
            !empty($source)) {
            
            $itemTitles = [];
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
        
        $items  = [];
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