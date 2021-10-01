<?php

namespace SilverCart\Model\Forms;


use ReflectionClass;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Pages\ContactFormPage;
use SilverCart\Model\Translation\TranslatableDataObjectExtension;
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
use SilverStripe\Forms\GroupedDropdownField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TimeField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\SS_List;
use SilverStripe\Security\Member;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

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
 * @method ContactFormPage ContactFormPage() Returns the related ContactFormPage.
 * 
 * @method \SilverStripe\ORM\HasManyList FormFieldOptions()      Return the related FormFieldOptions.
 * @method \SilverStripe\ORM\HasManyList FormFieldTranslations() Return the related FormFieldTranslations.
 */
class FormField extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    /**
     * Sets the custom form data.
     * 
     * @param array $data Data
     * 
     * @return void
     */
    public static function setCustomFormData(array $data) : void
    {
        self::$customFormData = $data;
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
     * @param string    $name       Field / tab name
     * 
     * @return void
     */
    public static function getFormFieldCMSFields(FieldList $fields, SS_List $formFields = null, string $name = 'FormFields') : void
    {
        if ($formFields === null) {
            $formFields = self::get();
        }
        $grid       = GridField::create($name, self::singleton()->i18n_plural_name(), $formFields, GridFieldConfig_RecordEditor::create());
        $subjectTab = $fields->findOrMakeTab("Root.{$name}", self::singleton()->i18n_plural_name());
        $subjectTab->setTitle(self::singleton()->i18n_plural_name());
        $fields->addFieldToTab("Root.{$name}", $grid);
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
            $fields->removeByName('Type');
            $fields->insertAfter('IsRequired', DropdownField::create('Type', $this->fieldLabel('Type'), $types, $this->Type));
            $typesWithOptions = [
                DropdownField::class,
                OptionsetField::class,
            ];
            $fields->removeByName('FormFieldOptions');
            if (in_array($this->Type, $typesWithOptions)) {
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
        $types = [
            CheckboxField::class,
            DateField::class,
            DatetimeField::class,
            DropdownField::class,
            OptionsetField::class,
            TextField::class,
            TextareaField::class,
            TimeField::class,
        ];
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
        if (array_key_exists($this->Name, $_POST)) {
            $value = $_POST[$this->Name];
        } elseif (array_key_exists($this->Name, $customData)) {
            $value = $customData[$this->Name];
        } elseif (!empty($this->DefaultValue)) {
            $value = $this->DefaultValue;
        } elseif (!empty($this->PresetWith)) {
            $ctrl         = Controller::curr();
            $object       = null;
            $parts        = explode('.', $this->PresetWith);
            $className    = array_shift($parts);
            $relationName = '';
            $property     = array_shift($parts);
            switch ($className) {
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
                $typesWithOptions = [
                    DropdownField::class,
                    OptionsetField::class,
                ];
                if (in_array($this->Type, $typesWithOptions)) {
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
            // Address
            'Company',
            'Salutation',
            'AcademicTitle',
            'FirstName',
            'Surname',
            'Addition',
            'Street',
            'StreetNumber',
            'Postcode',
            'City',
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
        $this->extend('updatePresetWithSourceWhitelist', $whitelist);
        foreach ($contextObjectNames as $contextObjectName) {
            $contextObject = singleton($contextObjectName);
            /* @var $contextObject \SilverStripe\ORM\DataObject */
            $contextSource = $contextObject->config()->db;
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
        if (class_exists($this->Type)) {
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
        $typesWithOptions = [
            DropdownField::class,
            OptionsetField::class,
        ];
        if (in_array($this->Type, $typesWithOptions)) {
            $args = [
                $this->Name,
                $this->Title,
                $this->FormFieldOptions()->map('ID', 'Title')->toArray(),
                $this->getFormFieldValue(),
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