<?php

namespace SilverCart\Forms;

use Exception;
use SilverCart\Model\Pages\Page;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FileField;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

/**
 * custom form definition.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 03.11.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CustomRequiredFields extends RequiredFields
{
    /**
     * Password validation pattern.
     *
     * @var string
     */
    private static $password_pattern   = '^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=\S+$).{8,}$';
    /**
     * Password min length.
     *
     * @var int
     */
    private static $password_minlength = 8;
    /**
     * List of required fields with callbacks.
     *
     * @var array
     */
    protected $requiredCallbacks = [];

    /**
     * Returns the list of required fields with callbacks.
     * 
     * @return array
     */
    public function getRequiredCallbacks() : array
    {
        return $this->requiredCallbacks;
    }

    /**
     * Sets the list of required fields with callbacks.
     * 
     * @param array $requiredCallbacks List of required fields with callbacks
     * 
     * @return void
     */
    public function setRequiredCallbacks(array $requiredCallbacks) : CustomRequiredFields
    {
        $this->requiredCallbacks = $requiredCallbacks;
        return $this;
    }

    /**
     * Adds a required field with callback to the list.
     * 
     * @param string $fieldName    Field name
     * @param string $callbackName Callback name
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function addRequiredCallback(string $fieldName, string $callbackName) : CustomRequiredFields
    {
        $this->requiredCallbacks[$fieldName] = $callbackName;
        return $this;
    }

    /**
     * Returns if the list of required fields with callbacks contains elements.
     * 
     * @return bool
     */
    public function hasRequiredCallbacks() : bool
    {
        return !empty($this->requiredCallbacks);
    }

    /**
     * Returns true if the named field is "required" AND hasn't the 
     * "isFilledInDependentOn" callback.
     *
     * Used by {@link FormField} to return a value for FormField::HasRequiredProperty(),
     * to do things like show *s on the form template.
     *
     * @param string $fieldName
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2018
     */
    public function fieldHasRequiredProperty($fieldName) : bool
    {
        $isRequired = isset($this->required[$fieldName]);
        if ($isRequired
         && array_key_exists($fieldName, $this->requiredCallbacks)
        ) {
            $cb = $this->requiredCallbacks[$fieldName];
            if (is_array($cb)
             && array_key_exists('isFilledInDependentOn', $cb)
            ) {
                $isRequired = false;
            }
        }
        return $isRequired;
    }
    
    /**
     * Validates the fields with the matching callback validation methods.
     * 
     * @param array $data Form data to validate
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function validateWithCallbacks($data) {
        $valid = true;
        $fields = $this->form->Fields();
        $requireCallbacks = $this->getRequiredCallbacks();
        
        foreach ($fields as $field) {
            if (array_key_exists($field->getName(), $requireCallbacks)) {
                $valid = ($field->validate($this) && $valid);
            }
        }
        
        foreach ($requireCallbacks as $fieldName => $callbacks) {
            if (!$fieldName) {
                continue;
            }
            if (array_key_exists($fieldName, $this->required)) {
                unset($this->required[$fieldName]);
            }
            if ($fieldName instanceof FormField) {
                $formField = $fieldName;
                $fieldName = $formField->getName();
            } else {
                $formField = $fields->dataFieldByName($fieldName);
            }
            if (is_null($formField)) {
                continue;
            }
            if (!is_array($callbacks)) {
                $callbacks = [$callbacks];
            }
            // submitted data for file upload fields come back as an array
            $value = isset($data[$fieldName]) ? $data[$fieldName] : null;
            
            $this->handleCallbackValidation($formField, $value, $callbacks, $valid);
        }
        
        
        if (!empty($this->required)) {
            $originalValid = parent::php($data);
            if ($valid) {
                $valid = $originalValid;
            }
        }
        
        return $valid;
    }
    
    /**
     * Handles the callback validation for the given formfield.
     * 
     * @param FormField $formField Form field
     * @param mixed     $value     Value
     * @param array     $callbacks Callbacks
     * @param boolean   $valid     Valid?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    protected function handleCallbackValidation($formField, $value, $callbacks, &$valid) {
        if (is_null($formField)) {
            return;
        }
        $fieldName = $formField->getName();
        if (is_string($value)) {
            $value = trim($value);
        }
        foreach ($callbacks as $callbackName => $callbackData) {
            if ($this->hasMethod($callbackName)) {
                $errorData = $this->$callbackName($formField, $value, $callbackData);
                if ($errorData['error']) {
                    $this->validationError(
                        $fieldName,
                        $errorData['errorMessage'],
                        ValidationResult::TYPE_ERROR
                    );
                    $valid = false;
                }
            }
        }
    }

    /**
     * Allows validation of fields via specification of a php function for
     * validation which is executed after the form is submitted.
     *
     * @param array $data Form data
     *
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function php($data) {
        if ($this->hasRequiredCallbacks()) {
            $valid = $this->validateWithCallbacks($data);
        } else {
            $valid = parent::php($data);
        }
        return $valid;
    }

    /**
     * Does the given email address exists in database?
     *
     * @param FormField $formField      Form field
     * @param mixed     $value          Value to check
     * @param boolean   $expectedResult The expected result
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.11.2017
     */
    public function doesEmailExist(FormField $formField, $value, $expectedResult) {
        $emailExists    = false;
        $error          = false;
        $errorMessage   = '';
        $member         = Member::get()->filter('Email', $value)->first();
        $currentUser    = Security::getCurrentUser();
        
        if ($member instanceof Member &&
            $currentUser instanceof Member &&
            $member->ID != $currentUser->ID) {
            $emailExists = true;
        }

        if ($emailExists != $expectedResult) {
            $error = true;
            if ($emailExists) {
                $errorMessage = Page::singleton()->fieldLabel('EmailAlreadyRegisterd');
            } else {
                $errorMessage = Page::singleton()->fieldLabel('EmailNotRegisterd');
            }
        }

        return [
            'error'         => $error,
            'errorMessage'  => $errorMessage
        ];
    }

    /**
     * Does the input string match the length defined? Whitespaces do not count
     *
     * @param FormField $formField Form field
     * @param mixed     $value     Value to check
     * @param int       $length    Tthe expressions exact length
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function hasLength(FormField $formField, $value, $length) {
        $error        = false;
        $errorMessage = '';
        $valueLength  = (int) strlen($value);

        if ($valueLength > 0 &&
            $valueLength !== (int) $length) {

            $error = true;
            $errorMessage = _t(CustomRequiredFields::class . '.FieldMustHaveExactlyChars',
                    'The field "{name}" requires exactly {count} characters.',
                    [
                        'name'  => strip_tags($formField->Title() ? $formField->Title() : $formField->getName()),
                        'count' => $length,
                    ]
            );
        }

        return [
            'error'        => $error,
            'errorMessage' => $errorMessage,
        ];
    }

    /**
     * Does the input strings have the minimum age?
     *
     * @param FormField $formField Form field
     * @param mixed     $value     Value to check
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function hasMinAge(FormField $formField, $value) {
        $error        = false;
        $errorMessage = '';
        $baseName     = str_replace('Day', '', $formField->getName());
        $fields       = $this->form->Fields();
        $monthField   = $fields->dataFieldByName($baseName . 'Month');
        $yearField    = $fields->dataFieldByName($baseName . 'Year');
        $birthday     = $yearField->Value() . '-' . $monthField->Value() . '-' . $value;

        if (!Config::CheckMinimumAgeToOrder($birthday)) {
            $error = true;
            $errorMessage = Config::MinimumAgeToOrderError();
        }
        
        return [
            'error'        => $error,
            'errorMessage' => $errorMessage,
        ];
    }

    /**
     * Does the input strings have the minimum length? Whitespaces do not count
     *
     * @param FormField $formField Form field
     * @param mixed     $value     Value to check
     * @param int       $minLength The expressions minimum length
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function hasMinLength(FormField $formField, $value, $minLength) {
        $error        = false;
        $errorMessage = '';

        if (strlen($value) > 0 &&
            strlen($value) < $minLength) {

            $error = true;
            $errorMessage = _t(CustomRequiredFields::class . '.FieldMustHaveMinChars',
                    'Enter at least {count} characters.',
                    [
                        'count' => $minLength,
                    ]
            );
        }

        return [
            'error'        => $error,
            'errorMessage' => $errorMessage,
        ];
    }

    /**
     * Checks if input containes special chars and if the result corresponds to
     * the expected result
     * 
     * @param FormField $formField      Form field
     * @param mixed     $value          Value to check
     * @param boolean   $expectedResult The expected result
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function hasSpecialSigns(FormField $formField, $value, $expectedResult) {
        $errorMessage   = '';
        $match          = false;
        $matches        = [];

        preg_match(
            '/^[A-Za-z0-9@\.]+$/',
            $value,
            $matches
        );

        if ($matches && ($matches[0] == $value)) {
            $match = true;
        }

        if ($match == $expectedResult) {
            $error = false;
        } else {
            $error = true;

            if ($match) {
                $errorMessage = _t(CustomRequiredFields::class . '.FieldMustContainSpecialSigns',
                        'The field "{name}" must contain special signs (other signs than letters, numbers and the signs "@" and ".").',
                        [
                            'name' => strip_tags($formField->Title() ? $formField->Title() : $formField->getName()),
                        ]
                );
            } else {
                $errorMessage = _t(CustomRequiredFields::class . '.FieldMustNotContainSpecialSigns',
                        'The field "{name}" must not contain special signs (letters, numbers and the signs "@" and ".").',
                        [
                            'name' => strip_tags($formField->Title() ? $formField->Title() : $formField->getName()),
                        ]
                );
            }
        }

        return [
            'error'        => $error,
            'errorMessage' => $errorMessage
        ];
    }

    /**
     * Checks if the field input is a currency
     *
     * @param FormField $formField      Form field
     * @param mixed     $value          Value to check
     * @param mixed     $expectedResult the expected result
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function isCurrency(FormField $formField, $value, $expectedResult) {
        $error = false;
        $errorMessage = '';

        if (!empty($value)) {
            $matches = [];
            $nrOfMatches = preg_match('/^[\d]*[,]?[^\D]*$/', $value, $matches);

            if ($nrOfMatches === 0) {
                $error        = true;
                $errorMessage = _t(CustomRequiredFields::class . '.FieldMustBeCurrencyAmount', 'Please enter a valid currency amount (e.g. 1499,00).');
            }
        }

        return [
            'error'        => $error,
            'errorMessage' => $errorMessage,
        ];
    }

    /**
     * Does a field contain only characters for quantity specification?
     *
     * @param FormField $formField             Form field
     * @param mixed     $value                 Value to check
     * @param int       $numberOfDecimalPlaces The number of decimal places that are allowed
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function isDecimalNumber(FormField $formField, $value, $numberOfDecimalPlaces) {
        $error = false;
        $errorMessage = '';
        $isQuantityField = true;

        $valueWithoutNumbersAndDotsAndCommas = preg_replace(
            '/[0-9,\.]*/',
            '',
            $value
        );
        $cleanValue = str_replace(',', '.', $value);

        if (strlen($valueWithoutNumbersAndDotsAndCommas) > 0) {
            $isQuantityField = false;
        }

        if ($isQuantityField === false) {
            $error        = true;
            $errorMessage = _t(CustomRequiredFields::class . '.FieldMustBeDecimal',
                    'The field {name} may consist of numbers and "." or "," only.',
                    [
                        'name'   => strip_tags($formField->Title() ? $formField->Title() : $formField->getName()),
                    ]
            );
        } else {
            // Check for number of decimal places
            $separatorPos         = strpos($cleanValue, '.');
            $decimalPlacesInValue = strlen($value) - ($separatorPos + 1);

            if ($decimalPlacesInValue > $numberOfDecimalPlaces) {
                $error        = true;
                $errorMessage = _t(CustomRequiredFields::class . '.FieldCanNotHaveMoreDecimalPlaces',
                        'The field {name} can not have more than {places} decimal places.',
                        [
                            'name'   => strip_tags($formField->Title() ? $formField->Title() : $formField->getName()),
                            'places' => $numberOfDecimalPlaces,
                        ]
                );
            }
        }

        return [
            'error'        => $error,
            'errorMessage' => $errorMessage,
        ];
    }

    /**
     * Checks, whether the given string matches basicly an email address.
     * The rule is: one or more chars, then '@', then two ore more chars, then
     * '.', then two or more chars. This matching was simplified because the 
     * stricter version did not match some special cases.
     *
     * @param FormField $formField      Form field
     * @param mixed     $value          Value to check
     * @param boolean   $expectedResult The expected result
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function isEmailAddress(FormField $formField, $value, $expectedResult) {
        $error          = false;
        $errorMessage   = '';

        if (!empty($value)) {
            $isValidEmailAddress = $this->isValidEmailAddress($value);
            if ($isValidEmailAddress != $expectedResult) {
                $error = true;

                if ($isValidEmailAddress) {
                    $errorMessage = _t(CustomRequiredFields::class . '.FieldMustNotBeEmail', 'Please don\'t enter an email address.');
                } else {
                    $errorMessage = _t(CustomRequiredFields::class . '.FieldMustBeEmail', 'Please enter a valid email address.');
                }
            }
        }

        return [
            'error'        => $error,
            'errorMessage' => $errorMessage
        ];
    }
    
    /**
     * Checks if the given value is a valid password.
     * 
     * @param FormField $formField      Form field
     * @param string    $value          Value to check
     * @param bool      $expectedResult The expected result
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.07.2019
     */
    public function isValidPassword(FormField $formField, string $value, bool $expectedResult) : array
    {
        $error         = false;
        $errorMessages = [];
        if (!empty($value)) {
            if (mb_strlen($value) < $this->config()->password_minlength) {
                $error           = true;
                $errorMessages[] = _t(CustomRequiredFields::class . '.FieldMustHaveMinChars',
                        'Enter at least {count} characters.',
                        [
                            'count' => $this->config()->password_minlength,
                        ]
                );
            }
            if (preg_match("@{$this->config()->password_pattern}@", $value) !== 1) {
                $error           = true;
                $errorMessages[] = _t(RegisterRegularCustomerForm::class . '.PasswordHint', 'Create a password for your login. Your password needs at least {minlength} characters and contain at least 1 capital letter, 1 small letter and 1 number.', [
                    'minlength' => $this->config()->password_minlength,
                ]);
            }
        }
        if ($expectedResult === false
         && $error === true
        ) {
            $error = false;
        }
        return [
            'error'        => $error,
            'errorMessage' => implode(' ', $errorMessages),
        ];
    }

    /**
     * Checks if a field is empty and if this result is expected
     *
     * @param FormField $formField      Form field
     * @param mixed     $value          Value to check
     * @param boolean   $expectedResult The expected result
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function isFilledIn(FormField $formField, $value, $expectedResult) {
        $isFilledIn     = true;
        $error          = false;
        $errorMessage   = '';
        
        if (is_array($value)) {
            if ($formField instanceof FileField &&
                isset($value['error']) &&
                $value['error']) {
                $isFilledIn = false;
            } else {
                $isFilledIn = (count($value)) ? true : false;
            }
        } else {
            // assume a string or integer
            $isFilledIn = (strlen($value)) ? true : false;
        }

        if ($isFilledIn !== $expectedResult) {
            $error = true;
        }

        if ($error) {
            if ($isFilledIn) {
                $errorMessage = _t(CustomRequiredFields::class . '.FieldMustBeEmpty',
                        'The field "{name}" must be empty.',
                        [
                            'name' => strip_tags($formField->Title() ? $formField->Title() : $formField->getName()),
                        ]
                );
            } else {
                $errorMessage = _t(CustomRequiredFields::class . '.FieldMayNotBeEmpty',
                        'The field "{name}" may not be empty.',
                        [
                            'name' => strip_tags($formField->Title() ? $formField->Title() : $formField->getName()),
                        ]
                );
            }
        }

        return [
            'error'         => $error,
            'errorMessage'  => $errorMessage
        ];
    }

    /**
     * Is the field empty? If a dependent field is not filled in an error will
     * be returned
     *
     * @param FormField $formField  Form field
     * @param mixed     $value      Value to check
     * @param array     $parameters Fields to be checked
     *      [
     *          [
     *              'field'     => string,
     *              'hasValue'  => mixed
     *          ],
     *          [
     *              'field'     => mixed
     *          ]
     *      ]
     *
     * @throws Exception
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function isFilledInDependentOn(FormField $formField, $value, $parameters) {
        $error = false;
        $errorMessage = '';
        $dependencyMatch = true;
        $keys = array_keys($parameters);
        if (array_shift($keys) !== 0) {
            $parameters = [$parameters];
        }
        
        foreach ($parameters as $dependentFieldData) {
            $dependentFieldName = $dependentFieldData['field'];
            $dependentValue     = $dependentFieldData['hasValue'];
            $actualValue        = $this->form->getController()->getRequest()->postVar($dependentFieldName);
            if ($this->form->Fields()->dataFieldByName($dependentFieldName) instanceof CheckboxField &&
                is_null($actualValue)) {
                $actualValue = '0';
            }
            if ($dependentValue != $actualValue) {
                $dependencyMatch = false;
                break;
            }
        }
        
        if ($dependencyMatch) {
            return $this->isFilledIn($formField, $value, true);
        }
        return [
            'error'        => $error,
            'errorMessage' => $errorMessage,
        ];
    }

    /**
     * Does a field contain number only
     *
     * @param FormField $formField      Form field
     * @param mixed     $value          Value to check
     * @param boolean   $expectedResult the expected result can be true or false
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function isNumbersOnly(FormField $formField, $value, $expectedResult) {
        $error = false;
        $errorMessage = '';
        $valueWithoutNumbers = preg_replace('/[0-9]*/', '', $value);
        $consistsOfNumbersOnly  = true;

        if (strlen($valueWithoutNumbers) > 0) {
            $consistsOfNumbersOnly = false;
        }
        if ($consistsOfNumbersOnly !== $expectedResult) {
            $error = true;
            $errorMessage = _t(CustomRequiredFields::class . '.FieldMayConsistOfNumbersOnly',
                    'The field "{name}" may consist of numbers only.',
                    [
                        'name' => strip_tags($formField->Title() ? $formField->Title() : $formField->getName()),
                    ]
            );
        }
        
        return [
            'error'        => $error,
            'errorMessage' => $errorMessage,
        ];
    }

    /**
     * Checks if the given value fits with the rules for a phone number.
     *
     * @param FormField $formField      Form field
     * @param mixed     $value          Value to check
     * @param boolean   $expectedResult the expected result can be true or false
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2018
     */
    public function isPhoneNumber(FormField $formField, $value, $expectedResult) {
        $error        = false;
        $errorMessage = '';
        if (!empty($value)) {
            $numbersOnly           = str_replace(['(', '+', ')', '-', ' '], '', $value);
            $valueWithoutNumbers   = preg_replace('/[0-9]*/', '', $numbersOnly);
            $consistsOfNumbersOnly = true;

            if (strlen($numbersOnly) == 0 ||
                strlen($valueWithoutNumbers) > 0) {
                $consistsOfNumbersOnly = false;
            }
            if ($consistsOfNumbersOnly !== $expectedResult) {
                $error = true;
                $errorMessage = _t(CustomRequiredFields::class . '.FieldExpectsValidPhoneNumber',
                            'The field "{name}" expects a valid phone number. (e.g "01234 56789", "+49 1234 5678-9")',
                        [
                            'name' => strip_tags($formField->Title() ? $formField->Title() : $formField->getName()),
                        ]
                );
            }
        }
        
        return [
            'error'        => $error,
            'errorMessage' => $errorMessage,
        ];
    }

    /**
     * Checks the validity of the field dependent of another field and a generic
     * validation method.
     *
     * @param FormField $formField  Form field
     * @param mixed     $value      Value to check
     * @param array     $parameters fields to be checked
     * @param string    $method     Method to call
     *
     * @throws Exception
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function isValidDependentOn(FormField $formField, $value, $parameters, $method) {
        $isValidDependentOn = [
            'error'        => false,
            'errorMessage' => '',
        ];

        if (is_array($parameters)) {

            if (!isset($parameters[0]['field']) ||
                !isset($parameters[0]['requirement'])) {

                throw new Exception(
                    'Field ' . $formField->getName() . ' is misconfigured for "CustomRequiredFields->mustNotEqualDependentOn".'
                );
            }

            $requirement    = $parameters[0]['requirement'];
            $dependentValue = $parameters[1][$parameters[0]['field']];
            switch ($requirement) {
                case 'isFilledIn':
                default:
                    $result = $this->isFilledIn($formField, $dependentValue, true);
                    break;
            }
            if (!$result['error']) {
                $isValidDependentOn = $this->$method($formField, $value, $parameters[2]);
            }
        } else {
            throw new Exception(
                'Field ' . $formField->getName() . ' is misconfigured for "CustomRequiredFields->mustNotEqualDependentOn".'
            );
        }

        return $isValidDependentOn;
    }

    /**
     * Do the values of two fields match?
     *
     * @param FormField $formField      Form field
     * @param mixed     $value          Value to check
     * @param array     $equalFieldName Name of the field to check equality against
     *      array (
     *          'value'      => string: the value the field must have
     *          'fieldName'  => string: Name of the other field
     *      )
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function mustEqual(FormField $formField, $value, $equalFieldName) {
        $error = false;
        $errorMessage = '';
        $equalFieldValue = $this->form->Fields()->dataFieldByName($equalFieldName)->Value();
        $equalFieldTitle = $this->form->Fields()->dataFieldByName($equalFieldName)->Title();
        
        if ($value !== $equalFieldValue) {
            $error = true;
            $errorMessage = _t(CustomRequiredFields::class . '.FieldMustHaveValue',
                    'Please enter the same value as in field "{name}".',
                    [
                        'name' => $equalFieldTitle,
                    ]
            );
        }
        
        return [
            'error'        => $error,
            'errorMessage' => $errorMessage,
        ];
    }

    /**
     * Checks the equality of two fields dependent of another field.
     *
     * @param FormField $formField  Form field
     * @param mixed     $value      Value to check
     * @param array     $parameters fields to be checked
     *      [
     *          [
     *              'field'     => string,
     *              'hasValue'  => mixed
     *          ],
     *          [
     *              'field'     => mixed
     *          ]
     *      ]
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function mustEqualDependentOn(FormField $formField, $value, $parameters) {
        return $this->isValidDependentOn($parameters, 'mustEqual');
    }

    /**
     * checks if two field values do NOT match (inversion of mustEqual())
     *
     * @param FormField $formField         Form field
     * @param mixed     $value             Value to check
     * @param array     $notEqualFieldName Name of the field to check equality against
     *      array (
     *          'value'      => string: value the field must NOT have
     *          'fieldName'  => string: Name of the other field
     *      )
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function mustNotEqual(FormField $formField, $value, $notEqualFieldName) {
        $error = false;
        $errorMessage = '';
        $notEqualFieldValue = $this->form->Fields()->dataFieldByName($notEqualFieldName)->Value();
        $notEqualFieldTitle = $this->form->Fields()->dataFieldByName($notEqualFieldName)->Title();

        if ($value == $notEqualFieldValue) {
            $error = true;
            $errorMessage = _t(CustomRequiredFields::class . '.FieldMayNotHaveValue',
                    'The field "{name1}" may not have the same value as field "{name2}".',
                    [
                        'name1' => strip_tags($formField->Title() ? $formField->Title() : $formField->getName()),
                        'name2' => $notEqualFieldTitle,
                    ]
            );
        }

        return [
            'error'        => $error,
            'errorMessage' => $errorMessage,
        ];
    }

    /**
     * Checks the equality of two fields dependent of another field.
     *
     * @param FormField $formField  Form field
     * @param mixed     $value      Value to check
     * @param array     $parameters fields to be checked
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function mustNotEqualDependentOn(FormField $formField, $value, $parameters) {
        return $this->isValidDependentOn($parameters, 'mustNotEqual');
    }
    

    /**
     * Taken from "https://github.com/iamcal/rfc822".
     *
     * Checks if an email conforms to the rfc822 standard.
     *
     * @param string $email   The email address to check
     * @param array  $options Additional options:
     *      - 'allow_comments'
     *      - 'public_internet'
     *
     * @return boolean
     *
     * @author Cal Henderson <cal@iamcal.com>
     * @since 19.11.2012
     */
    public function isValidEmailAddress($email, $options = []) {
        #
        # you can pass a few different named options as a second argument,
        # but the defaults are usually a good choice.
        #
        $defaults = [
            'allow_comments'	=> true,
            'public_internet'	=> true, # turn this off for 'strict' mode
        ];

        $opts = [];
        foreach ($defaults as $k => $v) {
            $opts[$k] = isset($options[$k]) ? $options[$k] : $v;
        }
        $options = $opts;

        ####################################################################################
        #
        # NO-WS-CTL       =       %d1-8 /         ; US-ASCII control characters
        #                         %d11 /          ;  that do not include the
        #                         %d12 /          ;  carriage return, line feed,
        #                         %d14-31 /       ;  and white space characters
        #                         %d127
        # ALPHA          =  %x41-5A / %x61-7A   ; A-Z / a-z
        # DIGIT          =  %x30-39

        $no_ws_ctl	= "[\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x7f]";
        $alpha		= "[\\x41-\\x5a\\x61-\\x7a]";
        $digit		= "[\\x30-\\x39]";
        $cr		    = "\\x0d";
        $lf		    = "\\x0a";
        $crlf		= "(?:$cr$lf)";

        ####################################################################################
        #
        # obs-char        =       %d0-9 / %d11 /          ; %d0-127 except CR and
        #                         %d12 / %d14-127         ;  LF
        # obs-text        =       *LF *CR *(obs-char *LF *CR)
        # text            =       %d1-9 /         ; Characters excluding CR and LF
        #                         %d11 /
        #                         %d12 /
        #                         %d14-127 /
        #                         obs-text
        # obs-qp          =       "\" (%d0-127)
        # quoted-pair     =       ("\" text) / obs-qp
        $obs_char	= "[\\x00-\\x09\\x0b\\x0c\\x0e-\\x7f]";
        $obs_text	= "(?:$lf*$cr*(?:$obs_char$lf*$cr*)*)";
        $text		= "(?:[\\x01-\\x09\\x0b\\x0c\\x0e-\\x7f]|$obs_text)";

        #
        # there's an issue with the definition of 'text', since 'obs_text' can
        # be blank and that allows qp's with no character after the slash. we're
        # treating that as bad, so this just checks we have at least one
        # (non-CRLF) character
        #
        $text		    = "(?:$lf*$cr*$obs_char$lf*$cr*)";
        $obs_qp		    = "(?:\\x5c[\\x00-\\x7f])";
        $quoted_pair	= "(?:\\x5c$text|$obs_qp)";

        ####################################################################################
        #
        # obs-FWS         =       1*WSP *(CRLF 1*WSP)
        # FWS             =       ([*WSP CRLF] 1*WSP) /   ; Folding white space
        #                         obs-FWS
        # ctext           =       NO-WS-CTL /     ; Non white space controls
        #                         %d33-39 /       ; The rest of the US-ASCII
        #                         %d42-91 /       ;  characters not including "(",
        #                         %d93-126        ;  ")", or "\"
        # ccontent        =       ctext / quoted-pair / comment
        # comment         =       "(" *([FWS] ccontent) [FWS] ")"
        # CFWS            =       *([FWS] comment) (([FWS] comment) / FWS)

        #
        # note: we translate ccontent only partially to avoid an infinite loop
        # instead, we'll recursively strip *nested* comments before processing
        # the input. that will leave 'plain old comments' to be matched during
        # the main parse.
        #
        $wsp		= "[\\x20\\x09]";
        $obs_fws	= "(?:$wsp+(?:$crlf$wsp+)*)";
        $fws		= "(?:(?:(?:$wsp*$crlf)?$wsp+)|$obs_fws)";
        $ctext		= "(?:$no_ws_ctl|[\\x21-\\x27\\x2A-\\x5b\\x5d-\\x7e])";
        $ccontent	= "(?:$ctext|$quoted_pair)";
        $comment	= "(?:\\x28(?:$fws?$ccontent)*$fws?\\x29)";
        $cfws		= "(?:(?:$fws?$comment)*(?:$fws?$comment|$fws))";

        #
        # these are the rules for removing *nested* comments. we'll just detect
        # outer comment and replace it with an empty comment, and recurse until
        # we stop.
        #
        $outer_ccontent_dull	= "(?:$fws?$ctext|$quoted_pair)";
        $outer_ccontent_nest	= "(?:$fws?$comment)";
        $outer_comment		    = "(?:\\x28$outer_ccontent_dull*(?:$outer_ccontent_nest$outer_ccontent_dull*)+$fws?\\x29)";

        ####################################################################################
        #
        # atext           =       ALPHA / DIGIT / ; Any character except controls,
        #                         "!" / "#" /     ;  SP, and specials.
        #                         "$" / "%" /     ;  Used for atoms
        #                         "&" / "'" /
        #                         "*" / "+" /
        #                         "-" / "/" /
        #                         "=" / "?" /
        #                         "^" / "_" /
        #                         "`" / "{" /
        #                         "|" / "}" /
        #                         "~"
        # atom            =       [CFWS] 1*atext [CFWS]
        $atext		= "(?:$alpha|$digit|[\\x21\\x23-\\x27\\x2a\\x2b\\x2d\\x2f\\x3d\\x3f\\x5e\\x5f\\x60\\x7b-\\x7e])";
        $atom		= "(?:$cfws?(?:$atext)+$cfws?)";

        ####################################################################################
        #
        # qtext           =       NO-WS-CTL /     ; Non white space controls
        #                         %d33 /          ; The rest of the US-ASCII
        #                         %d35-91 /       ;  characters not including "\"
        #                         %d93-126        ;  or the quote character
        # qcontent        =       qtext / quoted-pair
        # quoted-string   =       [CFWS]
        #                         DQUOTE *([FWS] qcontent) [FWS] DQUOTE
        #                         [CFWS]
        # word            =       atom / quoted-string
        $qtext		    = "(?:$no_ws_ctl|[\\x21\\x23-\\x5b\\x5d-\\x7e])";
        $qcontent	    = "(?:$qtext|$quoted_pair)";
        $quoted_string	= "(?:$cfws?\\x22(?:$fws?$qcontent)*$fws?\\x22$cfws?)";

        #
        # changed the '*' to a '+' to require that quoted strings are not empty
        #
        $quoted_string	= "(?:$cfws?\\x22(?:$fws?$qcontent)+$fws?\\x22$cfws?)";
        $word		    = "(?:$atom|$quoted_string)";

        ####################################################################################
        #
        # obs-local-part  =       word *("." word)
        # obs-domain      =       atom *("." atom)
        $obs_local_part	= "(?:$word(?:\\x2e$word)*)";
        $obs_domain	    = "(?:$atom(?:\\x2e$atom)*)";

        ####################################################################################
        #
        # dot-atom-text   =       1*atext *("." 1*atext)
        # dot-atom        =       [CFWS] dot-atom-text [CFWS]
        $dot_atom_text	= "(?:$atext+(?:\\x2e$atext+)*)";
        $dot_atom	    = "(?:$cfws?$dot_atom_text$cfws?)";

        ####################################################################################
        #
        # domain-literal  =       [CFWS] "[" *([FWS] dcontent) [FWS] "]" [CFWS]
        # dcontent        =       dtext / quoted-pair
        # dtext           =       NO-WS-CTL /     ; Non white space controls
        #
        #                         %d33-90 /       ; The rest of the US-ASCII
        #                         %d94-126        ;  characters not including "[",
        #                                         ;  "]", or "\"
        $dtext		= "(?:$no_ws_ctl|[\\x21-\\x5a\\x5e-\\x7e])";
        $dcontent	= "(?:$dtext|$quoted_pair)";
        $domain_literal	= "(?:$cfws?\\x5b(?:$fws?$dcontent)*$fws?\\x5d$cfws?)";

        ####################################################################################
        #
        # local-part      =       dot-atom / quoted-string / obs-local-part
        # domain          =       dot-atom / domain-literal / obs-domain
        # addr-spec       =       local-part "@" domain
        $local_part	= "(($dot_atom)|($quoted_string)|($obs_local_part))";
        $domain		= "(($dot_atom)|($domain_literal)|($obs_domain))";
        $addr_spec	= "$local_part\\x40$domain";

        #
        # this was previously 256 based on RFC3696, but dominic's errata was accepted.
        #
        if (strlen($email) > 254) {
            return false;
        }

        #
        # we need to strip nested comments first - we replace them with a simple comment
        #

        if ($options['allow_comments']) {
            $email = $this->email_strip_comments($outer_comment, $email, "(x)");
        }

        #
        # now match what's left
        #

        if (!preg_match("!^$addr_spec$!", $email, $m)) {
            return false;
        }

        $bits = [
            'local'			 => isset($m[1]) ? $m[1] : '',
            'local-atom'	 => isset($m[2]) ? $m[2] : '',
            'local-quoted'	 => isset($m[3]) ? $m[3] : '',
            'local-obs'		 => isset($m[4]) ? $m[4] : '',
            'domain'		 => isset($m[5]) ? $m[5] : '',
            'domain-atom'	 => isset($m[6]) ? $m[6] : '',
            'domain-literal' => isset($m[7]) ? $m[7] : '',
            'domain-obs'	 => isset($m[8]) ? $m[8] : '',
        ];


        #
        # we need to now strip comments from $bits[local] and $bits[domain],
        # since we know they're in the right place and we want them out of the
        # way for checking IPs, label sizes, etc
        #
        if ($options['allow_comments']) {
            $bits['local']	= $this->email_strip_comments($comment, $bits['local']);
            $bits['domain']	= $this->email_strip_comments($comment, $bits['domain']);
        }

        #
        # length limits on segments
        #
        if (strlen($bits['local']) > 64) {
            return false;
        }
        if (strlen($bits['domain']) > 255) {
            return false;
        }

        #
        # restrictions on domain-literals from RFC2821 section 4.1.3
        #
        # RFC4291 changed the meaning of :: in IPv6 addresses - i can mean one or
        # more zero groups (updated from 2 or more).
        #
        if (strlen($bits['domain-literal'])) {
            $Snum			= "(\d{1,3})";
            $IPv4_address_literal	= "$Snum\.$Snum\.$Snum\.$Snum";

            $IPv6_hex		= "(?:[0-9a-fA-F]{1,4})";

            $IPv6_full		= "IPv6\:$IPv6_hex(?:\:$IPv6_hex){7}";

            $IPv6_comp_part		= "(?:$IPv6_hex(?:\:$IPv6_hex){0,7})?";
            $IPv6_comp		= "IPv6\:($IPv6_comp_part\:\:$IPv6_comp_part)";

            $IPv6v4_full		= "IPv6\:$IPv6_hex(?:\:$IPv6_hex){5}\:$IPv4_address_literal";

            $IPv6v4_comp_part	= "$IPv6_hex(?:\:$IPv6_hex){0,5}";
            $IPv6v4_comp		= "IPv6\:((?:$IPv6v4_comp_part)?\:\:(?:$IPv6v4_comp_part\:)?)$IPv4_address_literal";

            #
            # IPv4 is simple
            #
            if (preg_match("!^\[$IPv4_address_literal\]$!", $bits['domain'], $m)) {

                if (intval($m[1]) > 255) {
                    return false;
                }
                if (intval($m[2]) > 255) {
                    return false;
                }
                if (intval($m[3]) > 255) {
                    return false;
                }
                if (intval($m[4]) > 255) {
                    return false;
                }

            } else {

                #
                # this should be IPv6 - a bunch of tests are needed here :)
                #

                while (1) {

                    if (preg_match("!^\[$IPv6_full\]$!", $bits['domain'])) {
                        break;
                    }

                    if (preg_match("!^\[$IPv6_comp\]$!", $bits['domain'], $m)) {
                        list($a, $b) = explode('::', $m[1]);
                        $folded = (strlen($a) && strlen($b)) ? "$a:$b" : "$a$b";
                        $groups = explode(':', $folded);
                        if (count($groups) > 7) {
                            return false;
                        }
                        break;
                    }

                    if (preg_match("!^\[$IPv6v4_full\]$!", $bits['domain'], $m)) {

                        if (intval($m[1]) > 255) {
                            return false;
                        }
                        if (intval($m[2]) > 255) {
                            return false;
                        }
                        if (intval($m[3]) > 255) {
                            return false;
                        }
                        if (intval($m[4]) > 255) {
                            return false;
                        }
                        break;
                    }

                    if (preg_match("!^\[$IPv6v4_comp\]$!", $bits['domain'], $m)) {
                        list($a, $b) = explode('::', $m[1]);
                        $b = substr($b, 0, -1); # remove the trailing colon before the IPv4 address
                        $folded = (strlen($a) && strlen($b)) ? "$a:$b" : "$a$b";
                        $groups = explode(':', $folded);
                        if (count($groups) > 5) {
                            return false;
                        }
                        break;
                    }

                    return false;
                }
            }
        } else {
            #
            # the domain is either dot-atom or obs-domain - either way, it's
            # made up of simple labels and we split on dots
            #

            $labels = explode('.', $bits['domain']);

            #
            # this is allowed by both dot-atom and obs-domain, but is un-routeable on the
            # public internet, so we'll fail it (e.g. user@localhost)
            #

            if ($options['public_internet']) {
                if (count($labels) == 1) {
                    return false;
                }
            }

            #
            # checks on each label
            #

            foreach ($labels as $label) {

                if (strlen($label) > 63) {
                    return false;
                }
                if (substr($label, 0, 1) == '-') {
                    return false;
                }
                if (substr($label, -1) == '-') {
                    return false;
                }
            }

            #
            # last label can't be all numeric
            #

            if ($options['public_internet']) {
                if (preg_match('!^[0-9]+$!', array_pop($labels))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Taken from "https://github.com/iamcal/rfc822".
     *
     * Removes comments from an email.
     *
     * @param string $comment The comment
     * @param string $email   The email
     * @param string $replace The replace string
     *
     * @return string
     *
     * @author Cal Henderson <cal@iamcal.com>
     * @since 19.11.2012
     */
    private function email_strip_comments($comment, $email, $replace='') {
        while (1) {
            $new = preg_replace("!$comment!", $replace, $email);
            if (strlen($new) == strlen($email)) {
                return $email;
            }
            $email = $new;
        }
    }
    
}
