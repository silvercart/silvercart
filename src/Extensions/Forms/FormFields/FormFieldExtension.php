<?php

namespace SilverCart\Extensions\Forms\FormFields;

use SilverStripe\Core\Extension;

/** 
 * Extension for the default SilverStripe\Forms\FormField.
 *
 * @package SilverCart
 * @subpackage Extensions_Forms_FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.06.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class FormFieldExtension extends Extension {
    
    /**
     * Determines whether the field validation failed.
     *
     * @var bool
     */
    protected $validationFailed = false;
    
    /**
     * Returns whether the field validation failed.
     * 
     * @return bool
     */
    public function getValidationFailed() {
        return $this->owner->validationFailed;
    }

    /**
     * Sets whether the field validation failed.
     * 
     * @param bool $validationFailed Validation failed?
     * 
     * @return $this->owner
     */
    public function setValidationFailed($validationFailed) {
        $this->owner->validationFailed = $validationFailed;
        return $this->owner;
    }

}