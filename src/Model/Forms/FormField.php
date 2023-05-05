<?php

namespace SilverCart\Model\Forms;

use ReflectionClass;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Pages\ContactFormPage;
use SilverCart\Model\Translation\TranslatableDataObjectExtension;
use SilverCart\ORM\ExtensibleDataObject;
use SilverCart\Security\FormFieldValidator;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormField as SilverStripeFormField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GroupedDropdownField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TimeField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\HasManyList;
use SilverStripe\ORM\SS_List;
use SilverStripe\Security\Member;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use function _t;
use function singleton;

/**
 * Custom form field.
 * 
 * @package SilverCart
 * @subpackage Model\Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 17.09.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @mixin TranslatableDataObjectExtension
 * 
 * @property string $Name         Name
 * @property string $Type         Type
 * @property string $Description  Description
 * @property bool   $IsRequired   IsRequired
 * @property string $DefaultValue Default Value
 * @property string $PresetWith   Preset With
 * @property int    $Sort         Sort order
 * 
 * @method FormFieldOption ParentOption() Returns the related ParentOption.
 * @method ContactFormPage ContactFormPage() Returns the related ContactFormPage.
 * 
 * @method HasManyList FormFieldOptions()      Return the related FormFieldOptions.
 * @method HasManyList FormFieldTranslations() Return the related FormFieldTranslations.
 */
class FormField extends DataObject
{
    use ExtensibleDataObject;
    
    public const PRESET_WITH_GENERAL_DATA = 'GeneralData';
    public const PRESET_WITH_GENERAL_DATA_DATE_TIME  = self::PRESET_WITH_GENERAL_DATA . '.DateTime';
    public const PRESET_WITH_GENERAL_DATA_IP_ADDRESS = self::PRESET_WITH_GENERAL_DATA . '.IPAddress';

    /**
     * Sets the custom form data.
     * 
     * @param array $data           Data
     * @param bool  $excludePresets Set to true to exclude all fields with a PresetWith option
     * 
     * @return void
     */
    public static function setCustomFormData(array $data, bool $excludePresets = false) : void
    {
        self::$customFormData               = $data;
        self::$customFormDataExcludePresets = $excludePresets;
    }
    
    /**
     * Returns the custom form data.
     * 
     * @return array
     */
    public static function getCustomFormData() : array
    {
        return (array) self::$customFormData;
    }
    
    /**
     * Adds the blacklist management fields to the given CMS $fields.
     * 
     * @param FieldList $fields     CMS fields to add blacklis entry fields to
     * @param SS_List   $formFields FormFields
     * @param string    $name       Field name
     * @param string    $tabName    Tab name
     * 
     * @return void
     */
    public static function getFormFieldCMSFields(FieldList $fields, SS_List $formFields = null, string $name = 'FormFields', string $tabName = '') : void
    {
        if ($formFields === null) {
            $formFields = self::get();
        }
        $tabTitle = '';
        if ($tabName === '') {
            $tabName  = "Root.{$name}";
            $tabTitle = self::singleton()->i18n_plural_name();
        }
        $grid       = GridField::create($name, self::singleton()->i18n_plural_name(), $formFields, GridFieldConfig_RecordEditor::create());
        $subjectTab = $fields->findOrMakeTab($tabName, self::singleton()->i18n_plural_name());
        if (!empty($tabTitle)) {
            $subjectTab->setTitle($tabTitle);
        }
        $fields->addFieldToTab($tabName, $grid);
        if (class_exists(GridFieldOrderableRows::class)) {
            $grid->getConfig()->addComponent(GridFieldOrderableRows::create('Sort'));
        }
    }
    
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilverCart_Forms_FormField';
    /**
     * DB attributes.
     * 
     * @var string[]
     */
    private static $db = [
        'Name'         => 'Varchar',
        'Type'         => 'Varchar',
        'IsRequired'   => 'Boolean',
        'DefaultValue' => 'Varchar',
        'PresetWith'   => 'Text',
        'Sort'         => 'Int',
    ];
    /**
     * Casted attributes.
     * 
     * @var string[]
     */
    private static $casting = [
        'Title'       => 'Varchar',
        'Description' => 'HTMLText',
        'TypeLabel'   => 'Varchar',
    ];
    /**
     * Has one relations.
     * 
     * @var string[]
     */
    private static $has_one = [
        'ParentOption'    => FormFieldOption::class,
        'ContactFormPage' => ContactFormPage::class,
    ];
    /**
     * Has one relations.
     * 
     * @var string[]
     */
    private static $has_many = [
        'FormFieldOptions'      => FormFieldOption::class,
        'FormFieldTranslations' => FormFieldTranslation::class,
    ];
    /**
     * Owned relations.
     *
     * @var string[]
     */
    private static $owns = [
        'FormFieldOptions',
        'FormFieldTranslations',
    ];
    /**
     * Default sort
     *
     * @var string
     */
    private static $default_sort = 'Sort';
    /**
     * Summary fields.
     *
     * @var string[]
     */
    private static $summary_fields = [
        'Title',
        'Description',
        'IsRequired',
        'TypeLabel',
    ];
    /**
     * Field types.
     *
     * @var string[]
     */
    private static $field_types = [
        CheckboxField::class,
        DateField::class,
        DatetimeField::class,
        DropdownField::class,
        HiddenField::class,
        LiteralField::class,
        NumericField::class,
        OptionsetField::class,
        TextField::class,
        TextareaField::class,
        TimeField::class,
    ];
    /**
     * Field types with options.
     *
     * @var string[]
     */
    private static $field_types_with_options = [
        DropdownField::class,
        OptionsetField::class,
    ];
    /**
     * Extensions.
     * 
     * @var string[]
     */
    private static $extensions = [
        TranslatableDataObjectExtension::class,
    ];
    /**
     * Determines to insert the translation CMS fields automatically.
     *
     * @var bool
     */
    private static $insert_translation_cms_fields = true;
    /**
     * Field name to insert the translation CMS fields after.
     *
     * @var string
     */
    private static $insert_translation_cms_fields_before = 'DefaultValue';
    /**
     * Custom form data.
     * 
     * @var array
     */
    protected static $customFormData = [];
    /**
     * Custom form data exclude presets.
     * 
     * @var bool
     */
    protected static $customFormDataExcludePresets = false;
    
    /**
     * Returns the translated singular name.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param bool $includerelations Include relations?
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        $typeLabels = [];
        foreach ($this->getAllowedFormFieldTypes() as $type) {
            $reflection = new ReflectionClass($type);
            $shortName  = $reflection->getShortName();
            $typeLabels["Type_{$shortName}"] = _t(self::class . ".Type_{$shortName}", $shortName);
        }
        return $this->defaultFieldLabels($includerelations, array_merge([], $typeLabels));
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fields->removeByName('Sort');
            $fields->removeByName('ContactFormPageID');
            $types = [];
            foreach ($this->getAllowedFormFieldTypes() as $type) {
                $reflection   = new ReflectionClass($type);
                $types[$type] = $this->fieldLabel("Type_{$reflection->getShortName()}");
            }
            asort($types);
            $fields->removeByName('Type');
            $fields->insertAfter('IsRequired', DropdownField::create('Type', $this->fieldLabel('Type'), $types, $this->Type));
            $fields->removeByName('FormFieldOptions');
            if (!$this->ParentOption()->exists()) {
                $fields->removeByName('ParentOptionID');
            }
            if (in_array($this->Type, $this->config()->field_types_with_options)) {
                FormFieldOption::getFormFieldOptionCMSFields($fields, $this->FormFieldOptions());
            }
            $fields->removeByName('PresetWith');
            $fields->insertAfter('DefaultValue', GroupedDropdownField::create('PresetWith', $this->fieldLabel('PresetWith'), $this->getPresetWithSource(), $this->Type));
        });
        return parent::getCMSFields();
    }
    
    /**
     * Returns the custom FormFieldValidator to use for CMS field validation.
     * 
     * @return FormFieldValidator
     */
    public function getCMSValidator() : FormFieldValidator
    {
        $validator = FormFieldValidator::create();
        $validator->setForFormField($this);
        $this->extend('updateCMSValidator', $validator);
        return $validator;
    }
    
    /**
     * On before write.
     * 
     * @return void
     */
    protected function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        if (empty($this->Name)) {
            $this->generateFormFieldName();
        }
    }
    
    /**
     * Returns the translated title.
     * 
     * @return string
     */
    public function getTitle() : string
    {
        return (string) $this->getTranslationFieldValue('Title');
    }
    
    /**
     * Returns the translated Description.
     * 
     * @return string
     */
    public function getDescription() : string
    {
        return (string) $this->getTranslationFieldValue('Description');
    }
    
    /**
     * Returns the allowed form field types.
     * 
     * @return array
     */
    public function getAllowedFormFieldTypes() : array
    {
        $types = $this->config()->field_types;
        $this->extend('updateAllowedFormFieldTypes', $types);
        return $types;
    }

    /**
     * Allows user code to hook into DataObject::getAllowedFormFieldTypes prior 
     * to updateAllowedFormFieldTypes being called on extensions.
     *
     * @param callable $callback The callback to execute
     */
    protected function beforeUpdateAllowedFormFieldTypes($callback)
    {
        $this->beforeExtending('updateAllowedFormFieldTypes', $callback);
    }


    /**
     * Returns the form field value.
     * 
     * @return string
     */
    public function getFormFieldValue()
    {
        $value      = '';
        $customData = self::getCustomFormData();
        if (!empty($this->PresetWith)
         && self::$customFormDataExcludePresets
        ) {
            foreach ($customData as $fieldName => $fieldValue) {
                if ($fieldName !== $this->Name) {
                    continue;
                }
                unset($customData[$fieldName]);
            }
        }
        if (array_key_exists($this->Name, $_POST)) {
            $value = $_POST[$this->Name];
        } elseif (array_key_exists($this->Name, $customData)) {
            $value = $customData[$this->Name];
        } elseif (!empty($this->DefaultValue)) {
            $value = $this->DefaultValue;
            if (in_array($this->Type, $this->config()->field_types_with_options)) {
                $option = $this->FormFieldOptions()->filter('Title', $value)->first();
                if ($option instanceof FormFieldOption) {
                    $value = $option->ID;
                }
            }
        } elseif (!empty($this->PresetWith)) {
            $ctrl         = Controller::curr();
            $object       = null;
            $parts        = explode('.', $this->PresetWith);
            $className    = array_shift($parts);
            $relationName = '';
            $property     = array_shift($parts);
            switch ($className) {
                case self::PRESET_WITH_GENERAL_DATA:
                    $value = $this->presetWithGeneralData($property, $parts);
                    break;
                case Member::class:
                    $object = Customer::currentUser();
                    break;
                case Address::class:
                    if ($ctrl->hasMethod('CurrentAddress')) {
                        $object = $ctrl->CurrentAddress();
                    } else {
                        $customer = Customer::currentUser();
                        if ($customer instanceof Member) {
                            $object = $customer->InvoiceAddress();
                        }
                    }
                    break;
                case Order::class:
                    if ($ctrl->hasMethod('CurrentOrder')) {
                        $object = $ctrl->CurrentOrder();
                    }
                    break;
            }
            if ($object !== null) {
                if (empty($parts)) {
                    $value = $object->{$property};
                } else {
                    $relationName = $property;
                    $property     = array_shift($parts);
                    $value        = $object->{$relationName}()->{$property};
                }
                if (in_array($this->Type, $this->config()->field_types_with_options)) {
                    $source = $this->FormFieldOptions()->map('ID', 'Title')->toArray();
                    $key    = array_search($value, $source);
                    if (is_numeric($key)) {
                        $value = $key;
                    }
                }
            }
        }
        return $value;
    }
    
    /**
     * Returns the POST value based option title or the plain value if this is 
     * no option field.
     * 
     * @return string
     */
    public function getFormFieldValueNice() : string
    {
        $value = '';
        $customData = self::getCustomFormData();
        if (array_key_exists($this->Name, $_POST)) {
            $value = $_POST[$this->Name];
        } elseif (array_key_exists($this->Name, $customData)) {
            $value = $customData[$this->Name];
        }
        if (!empty($value)) {
            if (in_array($this->Type, $this->config()->field_types_with_options)) {
                $option = $this->FormFieldOptions()->byID($value);
                if ($option instanceof FormFieldOption) {
                    $value = $option->Title;
                }
            }
        }
        return (string) $value;
    }
    
    /**
     * Returns the geneal data preset value.
     * 
     * @param array $parts Parts
     * 
     * @return string
     */
    public function presetWithGeneralData(string $property, array $parts) : string
    {
        $value = '';
        if (count($parts) === 0) {
            switch ($property) {
                case 'IPAddress':
                    if (array_key_exists('HTTP_X_REAL_IP', $_SERVER)) {
                        $value = $_SERVER['HTTP_X_REAL_IP'];
                    } elseif (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
                        $value = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    } elseif (array_key_exists('REMOTE_ADDR', $_SERVER)) {
                        $value = $_SERVER['REMOTE_ADDR'];
                    }
                    break;
                case 'DateTime':
                    $value = date('Y-m-d H:i:s');
                    break;
            }
        }
        $this->extend('presetWithGeneralData', $value, $property, $parts);
        return $value;
    }
    
    /**
     * Returns the dropdown source.
     * 
     * @return array
     */
    public function getPresetWithSource() : array
    {
        $source             = [''=>''];
        $contextObjectNames = [
            Member::class,
            Address::class,
            Order::class,
        ];
        $whitelist = [
            // Member
            'Email',
            'AcademicTitle',
            'Salutation',
            'CustomerNumber',
            'FirstName',
            'Surname',
            'Name',
            // Address
            'Company',
            'Salutation',
            'AcademicTitle',
            'FirstName',
            'Surname',
            'FullName',
            'Addition',
            'Street',
            'StreetNumber',
            'StreetWithNumber',
            'Postcode',
            'City',
            'PostcodeWithCity',
            'Phone',
            'Fax',
            'State',
            'Country',
            // Order
            'ExternalReference',
            'CustomersEmail',
            'OrderNumber',
            'TrackingCode',
            'TrackingLink',
        ];
        $generalData = [
            self::PRESET_WITH_GENERAL_DATA_IP_ADDRESS => _t(self::class . '.GeneralData_IPAddress', 'Customer IP Address'),
            self::PRESET_WITH_GENERAL_DATA_DATE_TIME  => _t(self::class . '.GeneralData_DateTime', 'Date and Time'),
        ];
        $this->extend('updatePresetWithSourceGeneralData', $generalData);
        $this->extend('updatePresetWithSourceWhitelist', $whitelist);
        $source[_t(self::class . '.GeneralData', 'General Data')] = $generalData;
        foreach ($contextObjectNames as $contextObjectName) {
            $contextObject = singleton($contextObjectName);
            /* @var $contextObject DataObject */
            $contextSource = array_merge(
                    $contextObject->config()->db,
                    $contextObject->config()->casting,
            );
            if ($contextObject->hasExtension(TranslatableDataObjectExtension::class)) {
                $languageContextObject = singleton("{$contextObjectName}Translation");
                $contextSource = array_merge(
                        $languageContextObject->config()->db,
                        $contextSource
                );
            }
            foreach ($contextSource as $fieldName => $fieldType) {
                if (in_array($fieldName, $whitelist)) {
                    $contextSource["{$contextObjectName}.{$fieldName}"] = $contextObject->fieldLabel($fieldName);
                }
                unset($contextSource[$fieldName]);
            }
            $hasOneRelations  = $contextObject->hasOne();
            foreach ($hasOneRelations as $relationName => $className) {
                if (!in_array($relationName, $whitelist)) {
                    continue;
                }
                $singlton = singleton($className);
                /* @var $singlton DataObject */
                $contextSource["{$contextObjectName}.{$relationName}.ID"] = "{$contextObject->fieldLabel($relationName)} (ID)";
                if ($singlton->hasField('Title')
                 || ($singlton->hasExtension(TranslatableDataObjectExtension::class)
                  && singleton($singlton->getTranslationClassName()->hasField('Title')))
                ) {
                    $contextSource["{$contextObjectName}.{$relationName}.Title"] = "{$contextObject->fieldLabel($relationName)} ({$singlton->fieldLabel('Title')})";
                }
            }
            foreach ($whitelist as $whitelistEntry) {
                if (array_key_exists("{$contextObjectName}.{$whitelistEntry}.ID", $contextSource)) {
                    continue;
                }
                if ($contextObject->hasMethod("get{$whitelistEntry}")) {
                    $contextSource["{$contextObjectName}.{$whitelistEntry}"] = $contextObject->fieldLabel($whitelistEntry);
                }
            }
            $source[$contextObject->i18n_singular_name()] = $contextSource;
        }
        return $source;
    }
    
    /**
     * Returns the type label.
     * 
     * @return string
     */
    public function getTypeLabel() : string
    {
        $label = '';
        if (class_exists((string) $this->Type)) {
            $reflection = new ReflectionClass($this->Type);
            $label = $this->fieldLabel("Type_{$reflection->getShortName()}");
        }
        return $label;
    }
    
    /**
     * Returns the form field object.
     * 
     * @return SilverStripeFormField
     */
    public function getFormField() : SilverStripeFormField
    {
        if (in_array($this->Type, $this->config()->field_types_with_options)) {
            $args = [
                $this->Name,
                $this->Title,
                $this->FormFieldOptions()->map('ID', 'Title')->toArray(),
                $this->getFormFieldValue(),
            ];
        } elseif ($this->Type === LiteralField::class) {
            $args = [
                $this->Name,
                DBHTMLText::create()->setProcessShortcodes(true)->setValue($this->Description),
            ];
        } else {
            $args = [
                $this->Name,
                $this->Title,
                $this->getFormFieldValue(),
            ];
        }
        $formField = Injector::inst()->createWithArgs($this->Type, $args);
        /* @var $formField SilverStripeFormField */
        if (!empty($this->Description)) {
            $formField->setDescription($this->Description);
        }
        if ($this->IsRequired) {
            $formField->setRequiredForced(true);
        }
        return $formField;
    }
    
    /**
     * Generates and returns the form field name.
     * 
     * @return string
     */
    public function generateFormFieldName() : string
    {
        $baseName   = Convert::raw2htmlid($this->Title);
        $this->Name = $baseName;
        // Ensure that this object has a non-conflicting Name value.
        $count = 2;
        while (!$this->validFormFieldName()) {
            $this->Name = "{$baseName}-{$count}";
            $count++;
        }
        return $this->Name;
    }

    /**
     * Returns true if this object has a Name value that does not conflict with 
     * any other objects.
     *
     * @return bool
     */
    public function validFormFieldName() : bool
    {
        $source = self::get()->filter([
            'Name'              => $this->Name,
            'ContactFormPageID' => $this->ContactFormPageID,
        ]);
        if ($this->exists()) {
            $source = $source->exclude('ID', $this->ID);
        }
        return !$source->exists();
    }
    
    /**
     * Returns the rendered form field.
     * 
     * @return DBHTMLText
     */
    public function forTemplate() : DBHTMLText
    {
        return $this->getFormField()->forTemplate();
    }
}