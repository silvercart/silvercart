<?php

namespace SilverCart\Forms\FormFields;

/**
 * Text input field with validation for correct email format according to RFC 2822.
 * A copy of SilverStripe\Forms\EmailField but extends from 
 * SilverCart\Forms\FormFields\TextField instead of SilverStripe\Forms\TextField
 * to provide the placeholder attribute.
 * 
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class EmailField extends TextField
{
    /**
     * Input type
     *
     * @var string
     */
    protected $inputType = 'email';
    
    /**
     * {@inheritdoc}
     */
    public function Type()
    {
        return 'email text';
    }

    /**
     * Validates for RFC 2822 compliant email addresses.
     *
     * @see http://www.regular-expressions.info/email.html
     * @see http://www.ietf.org/rfc/rfc2822.txt
     *
     * @param Validator $validator
     *
     * @return string
     */
    public function validate($validator)
    {
        $this->value = trim($this->value);

        $pattern = '^[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$';

        // Escape delimiter characters.
        $safePattern = str_replace('/', '\\/', $pattern);

        if ($this->value && !preg_match('/' . $safePattern . '/i', $this->value)) {
            $validator->validationError(
                $this->name,
                _t('SilverStripe\\Forms\\EmailField.VALIDATION', 'Please enter an email address'),
                'validation'
            );

            return false;
        }

        return true;
    }

    /**
     * Returns the schema validation.
     * 
     * @return array
     */
    public function getSchemaValidation()
    {
        $rules = parent::getSchemaValidation();
        $rules['email'] = true;
        return $rules;
    }
}