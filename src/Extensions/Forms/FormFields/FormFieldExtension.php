<?php

namespace SilverCart\Extensions\Forms\FormFields;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\Form;

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
    
    /**
     * Adds a CSS class to the field if an error occured.
     * 
     * @param string $class CSS class to add
     * 
     * @return $this->owner
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.07.2018
     */
    public function addErrorClass($class) {
        $form = $this->owner->getForm();
        if ($form instanceof Form &&
            $form->getMessageType() == 'error') {
            $this->owner->addExtraClass($class);
        }
        return $this->owner;
    }
    
    /**
     * Updates the attributes.
     * 
     * @param array &$attributes Attributes to update.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2018
     */
    public function updateAttributes(&$attributes) {
        if (!$this->HasRequiredProperty()) {
            if (array_key_exists('required', $attributes)) {
                unset($attributes['required']);
            }
            if (array_key_exists('aria-required', $attributes)) {
                unset($attributes['aria-required']);
            }
        }
    }
    
    /**
     * Returns whether this form field needs the required HTML property.
     * 
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2018
     */
    public function HasRequiredProperty() {
        $form = $this->owner->getForm();
        if (is_object($form) &&
            ($validator = $form->getValidator())) {
            $validator = $form->getValidator();
            if (is_object($validator)) {
                if ($validator->hasMethod('fieldHasRequiredProperty')) {
                    return $validator->fieldHasRequiredProperty($this->owner->getName());
                } else {
                    return $validator->fieldIsRequired($this->owner->getName());
                }
            }
        }
        return false;
    }

}