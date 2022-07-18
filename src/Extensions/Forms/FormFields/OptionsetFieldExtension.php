<?php

namespace SilverCart\Extensions\Forms\FormFields;

use SilverStripe\Core\Convert;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

/** 
 * Extension for the default SilverStripe\Forms\OptionsetField.
 *
 * @package SilverCart
 * @subpackage Extensions\Forms\FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 12.07.2022
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property OptionsetField $owner Owner
 */
class OptionsetFieldExtension extends Extension
{
    /**
     * Returns the options.
     * 
     * @return ArrayList
     */
    public function getOptions() : ArrayList
    {
        $options = [];
        $odd     = false;
        foreach ($this->owner->getSourceEmpty() as $value => $title) {
            $odd       = !$odd;
            $options[] = $this->getFieldOption($value, $title, $odd);
        }
        return ArrayList::create($options);
    }

    /**
     * Generate an ID property for a single option
     *
     * @param string $value
     * @return string
     */
    protected function getOptionID($value)
    {
        return $this->owner->ID() . '_' . Convert::raw2htmlid($value);
    }

    /**
     * Get the "name" property for each item in the list
     *
     * @return string
     */
    protected function getOptionName()
    {
        return $this->owner->getName();
    }

    /**
     * Get extra classes for each item in the list
     *
     * @param string $value Value of this item
     * @param bool $odd If this item is odd numbered in the list
     * @return string
     */
    protected function getOptionClass($value, $odd)
    {
        $oddClass = $odd ? 'odd' : 'even';
        $valueClass = ' val' . Convert::raw2htmlid($value);
        return $oddClass . $valueClass;
    }

    /**
     * Build a field option for template rendering
     *
     * @param mixed $value Value of the option
     * @param string $title Title of the option
     * @param boolean $odd True if this should be striped odd. Otherwise it should be striped even
     * @return ArrayData Field option
     */
    protected function getFieldOption($value, string $title, bool $odd) : ArrayData
    {
        return ArrayData::create([
            'ID'         => $this->getOptionID($value),
            'Class'      => $this->getOptionClass($value, $odd),
            'Role'       => 'option',
            'Name'       => $this->getOptionName(),
            'Value'      => $value,
            'Title'      => $title,
            'isChecked'  => $this->owner->isSelectedValue($value, $this->owner->Value()),
            'isDisabled' => $this->isDisabledValue($value)
        ]);
    }

    /**
     * Check if the given value is disabled
     *
     * @param string $value
     * @return bool
     */
    protected function isDisabledValue($value)
    {
        if ($this->owner->isDisabled()) {
            return true;
        }
        return in_array($value, $this->owner->getDisabledItems());
    }
}