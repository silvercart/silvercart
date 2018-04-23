<?php

namespace SilverCart\Admin\Forms;

use SilverCart\Dev\Tools;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\View\Requirements;

/**
 * Adds some additional functionallity to default text field
 *
 * @package SilverCart
 * @subpackage Admin_Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 25.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MultiDropdownField extends DropdownField {
    
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
        Requirements::themedCSS('client/admin/css/MultiDropdownField');
        Requirements::themedJavascript('client/admin/javascript/MultiDropdownField');
        return parent::Field($properties);
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
    
    /**
     * Returns a i18n field summary of the selected values.
     * Used to display the current search filter settings in admin area.
     * 
     * @param string $searchValue Original search value
     * 
     * @return string
     */
    public function getSummary($searchValue) {
        $rawName = str_replace('q[', '', str_replace(']', '', $this->getName()));
        $request = Controller::curr()->getRequest();
        $q       = $request->getVar('q');
        $boolKey = $rawName . '-BoolValues';
        if (array_key_exists($boolKey, $q)) {
            $boolValues = $q[$boolKey];
        } else {
            $boolValues = [$searchValue => '1'];
        }
        
        if (!is_array($boolValues)) {
            $boolValues = [];
        }
        
        $parts  = [];
        $source = $this->getSource();
        foreach ($boolValues as $ID => $bool) {
            if (isset($source[$ID])) {
                $parts[] = $source[$ID] . ' [' . ($bool == '1' ? Tools::field_label('Yes') : Tools::field_label('No')) . ']';
            }
        }
        $summary = implode(', ', $parts);
        return $summary;
    }

}