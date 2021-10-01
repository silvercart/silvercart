<?php

namespace SilverCart\Security;

use SilverCart\Model\Forms\FormField;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;

/**
 * 
 * @package SilverCart
 * @subpackage Commission\Security
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 *
 * Additional required fields can also be set via config API, eg.
 * <code>
 * SilverCart\Security\FormFieldValidator:
 *     custom_required:
 *         - Title
 *         - Type
 * </code>
 */
class FormFieldValidator extends RequiredFields
{
    /**
     * Fields that are required by this validator
     * 
     * @var string[]
     */
    private static $custom_required = [
        'Title',
        'Type',
    ];
    /**
     * Determine what partner this validator is meant for
     * 
     * @var FormField
     */
    protected $forFormField = null;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $required = func_get_args();
        if (isset($required[0]) && is_array($required[0])) {
            $required = $required[0];
        }
        $required = array_merge($required, $this->config()->custom_required);
        parent::__construct(array_unique($required));
    }

    /**
     * Get the FormField this validator applies to.
     * 
     * @return FormField
     */
    public function getForFormField() : ?FormField
    {
        return $this->forFormField;
    }

    /**
     * Set the FormField this validator applies to.
     * 
     * @param FormField $value FormField
     * 
     * @return $this
     */
    public function setForFormField(FormField $value) : FormFieldValidator
    {
        $this->forFormField = $value;
        return $this;
    }

    /**
     * Check if the submitted partner data is valid (server-side)
     *
     * Check if a partner with that code doesn't already exist, or if it does
     * that it is this partner.
     *
     * @param array $data Submitted data
     * 
     * @return bool
     */
    public function php($data) : bool
    {
        $valid           = parent::php($data);
        $identifierField = 'Name';
        $id              = isset($data['ID']) ? (int) $data['ID'] : 0;
        if (isset($data[$identifierField])) {
            if (Convert::raw2htmlid($data[$identifierField]) !== $data[$identifierField]) {
                $this->validationError(
                    $identifierField,
                    _t(FormField::class . '.ValidationInvalidName', 'Invalid name. A form field name must begin with a letter ([A-Za-z]) and may be followed by any number of letters, digits ([0-9]), hyphens ("-"), underscores ("_"), colons (":"), and periods (".").'),
                    'required'
                );
                $valid = false;
            }
            if (!$id && ($ctrl = $this->form->getController())) {
                if ($ctrl instanceof GridFieldDetailForm_ItemRequest
                 && $record = $ctrl->getRecord()
                ) {
                    $id = $record->ID;
                }
            }
            $existing = $this->getForFormField();
            if ((int) $id === 0
             && $existing instanceof FormField
            ) {
                $id = $existing->exists() ? $existing->ID : 0;
            }
            $data['ID'] = $id;
            $filter     = [$identifierField => $data[$identifierField]];
            if ($existing instanceof FormField) {
                $filter['ContactFormPageID'] = $existing->ContactFormPageID;
            }
            $duplicates = FormField::get()->filter($filter);
            if ($id > 0) {
                $duplicates = $duplicates->exclude('ID', $id);
            }
            if ($duplicates->count() > 0) {
                $this->validationError(
                    $identifierField,
                    _t(FormField::class . '.ValidationFormFieldExists', 'A resource already exists with the same {identifier}', ['identifier' => FormField::singleton()->fieldLabel($identifierField)]),
                    'required'
                );
                $valid = false;
            }
        }
        // Execute the validators on the extensions
        $results   = $this->extend('updatePHP', $data, $this->form);
        $results[] = $valid;
        return min($results);
    }
}