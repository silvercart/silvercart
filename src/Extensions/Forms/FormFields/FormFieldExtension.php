<?php

namespace SilverCart\Extensions\Forms\FormFields;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormField;
use SilverStripe\View\SSViewer;

/** 
 * Extension for the default SilverStripe\Forms\FormField.
 *
 * @package SilverCart
 * @subpackage Extensions_Forms_FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.06.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property FormField $owner Owner
 */
class FormFieldExtension extends Extension
{
    /**
     * Determines whether the field validation failed.
     *
     * @var bool
     */
    public $validationFailed = false;
    /**
     * Determines whether the field is required (forces).
     *
     * @var bool[]
     */
    protected $requiredForced = [];
    
    /**
     * Returns the placeholder
     *
     * @return string
     */
    public function getPlaceholder() : string
    {
        return (string) $this->owner->getAttribute('placeholder');
    }

    /**
     * Sets the placeholder
     *
     * @param string $placeholder Placeholder to set
     * 
     * @return FormField
     */
    public function setPlaceholder($placeholder) : FormField
    {
        $this->owner->setAttribute('placeholder', $placeholder);
        return $this->owner;
    }
    
    /**
     * Returns whether the field validation failed.
     * 
     * @return bool
     */
    public function getValidationFailed() : bool
    {
        return (bool) $this->owner->validationFailed;
    }

    /**
     * Sets whether the field validation failed.
     * 
     * @param bool $validationFailed Validation failed?
     * 
     * @return FormField
     */
    public function setValidationFailed(bool $validationFailed) : FormField
    {
        $this->owner->validationFailed = $validationFailed;
        return $this->owner;
    }
    
    /**
     * Forces the required attribute.
     * 
     * @param bool $isRequired Enable forcing the required attribute or not?
     * 
     * @return FormField
     */
    public function setRequiredForced(bool $isRequired) : FormField
    {
        $objectID = spl_object_id($this->owner);
        if ($isRequired) {
            $this->requiredForced[$objectID] = true;
            $this->owner->setAttribute('required', 'required');
        } else {
            $this->requiredForced[$objectID] = false;
        }
        return $this->owner;
    }
    
    /**
     * Returns whether the required attribute should be forced.
     * 
     * @return bool
     */
    public function getRequiredForced() : bool
    {
        $is       = false;
        $objectID = spl_object_id($this->owner);
        if (array_key_exists($objectID, $this->requiredForced)) {
            $is = $this->requiredForced[$objectID];
        }
        return $is;
    }
    
    /**
     * Adds a CSS class to the field if an error occured.
     * 
     * @param string $class CSS class to add
     * 
     * @return FormField
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.07.2018
     */
    public function addErrorClass(string $class) : FormField
    {
        $form = $this->owner->getForm();
        if ($form instanceof Form
         && $form->getMessageType() == 'error'
        ) {
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
    public function updateAttributes(array &$attributes) : void
    {
        if ($this->getRequiredForced()) {
            return;
        }
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
    public function HasRequiredProperty() : bool
    {
        $has  = false;
        $form = $this->owner->getForm();
        /* @var $form Form */
        if (is_object($form)
         && ($validator = $form->getValidator())
        ) {
            $validator = $form->getValidator();
            if (is_object($validator)) {
                if ($validator->hasMethod('fieldHasRequiredProperty')) {
                    $has = (bool) $validator->fieldHasRequiredProperty($this->owner->getName());
                } else {
                    $has = (bool) $validator->fieldIsRequired($this->owner->getName());
                }
            }
        }
        return $has;
    }
    
    /**
     * Generate an array of class name strings to use for rendering this form field into HTML.
     *
     * @param string $customTemplate
     * @param string $customTemplateSuffix
     *
     * @return array
     */
    protected function _templates(string $customTemplate = null, string $customTemplateSuffix = null) : array
    {
        $templates = SSViewer::get_templates_by_class(get_class($this->owner), $customTemplateSuffix);
        // Prefer any custom template
        if ($customTemplate) {
            // Prioritise direct template
            array_unshift($templates, $customTemplate);
        }
        return $templates;
    }

    /**
     * Returns an array of templates matching with the given $suffix to use for 
     * rendering.
     * 
     * @param string $suffix Template suffix
     *
     * @return array
     */
    public function getCustomFieldTemplates(string $suffix)
    {
        if (strpos($suffix, '_') !== 0) {
            $suffix = "_{$suffix}";
        }
        return $this->_templates(null, $suffix);
    }

    /**
     * Returns a "field holder" for this field.
     *
     * Forms are constructed by concatenating a number of these field holders.
     *
     * The default field holder is a label and a form field inside a div.
     *
     * @see FieldHolder.ss
     *
     * @param array $properties
     *
     * @return DBHTMLText
     */
    public function CustomFieldHolder(string $suffix, $properties = [])
    {
        $context = $this->owner;
        if (count($properties)) {
            $context = $context->customise($properties);
        }
        return $context->renderWith($this->getCustomFieldTemplates($suffix));
    }
}