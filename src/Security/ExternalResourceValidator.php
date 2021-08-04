<?php

namespace SilverCart\Security;

use SilverCart\Model\CookieConsent\ExternalResource;
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
 * SilverCart\Security\ExternalResourceValidator:
 *     custom_required:
 *         - Title
 * </code>
 */
class ExternalResourceValidator extends RequiredFields
{
    /**
     * Fields that are required by this validator
     * 
     * @var string[]
     */
    private static $custom_required = [
        'Name',
    ];
    /**
     * Determine what partner this validator is meant for
     * 
     * @var ExternalResource
     */
    protected $forExternalResource = null;

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
     * Get the ExternalResource this validator applies to.
     * 
     * @return ExternalResource
     */
    public function getForExternalResource() : ?ExternalResource
    {
        return $this->forExternalResource;
    }

    /**
     * Set the ExternalResource this validator applies to.
     * 
     * @param ExternalResource $value ExternalResource
     * 
     * @return $this
     */
    public function setForExternalResource(ExternalResource $value) : ExternalResourceValidator
    {
        $this->forExternalResource = $value;
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
            if (!$id && ($ctrl = $this->form->getController())) {
                if ($ctrl instanceof GridFieldDetailForm_ItemRequest
                 && $record = $ctrl->getRecord()
                ) {
                    $id = $record->ID;
                }
            }
            if ((int) $id === 0
             && $existing = $this->getForExternalResource()
            ) {
                $id = $existing->exists() ? $existing->ID : 0;
            }
            $data['ID'] = $id;
            $duplicates = ExternalResource::get()->filter($identifierField, $data[$identifierField]);
            if ($id > 0) {
                $duplicates = $duplicates->exclude('ID', $id);
            }
            if ($duplicates->count() > 0) {
                $this->validationError(
                    $identifierField,
                    _t(ExternalResource::class . '.ValidationExternalResourceExists', 'A resource already exists with the same {identifier}', ['identifier' => ExternalResource::singleton()->fieldLabel($identifierField)]),
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