<?php

namespace SilverCart\Extensions\Forms\FormFields;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\DropdownField;
use SilverStripe\View\Requirements;

/**
 * Extension for DropdownField.
 * 
 * @package SilverCart
 * @subpackage Extensions\Forms\FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.09.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property DropdownField $owner Owner
 */
class DropdownFieldExtension extends Extension
{
    /**
     * Adds JS chosen to the dropdown field.
     * 
     * @return DropdownField
     */
    public function addChosen() : DropdownField
    {
        Requirements::themedCSS('client/css/chosen.min');
        Requirements::themedJavascript('client/javascript/chosen.jquery.min');
        Requirements::customScript('$("select.chosen-select").chosen({disable_search_threshold: 10});', 'sc-chosen');
        $this->owner->addExtraClass('chosen-select');
        return $this->owner;
    }
}