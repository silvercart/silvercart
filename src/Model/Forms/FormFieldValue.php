<?php

namespace SilverCart\Model\Forms;

use SilverCart\Dev\Tools;
use SilverCart\Model\ContactMessage;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\SS_List;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Custom form field value.
 * 
 * @package SilverCart
 * @subpackage Model\Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 17.09.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $FieldTitle Title
 * @property string $FieldValue Value
 * 
 * @method FormField FormField() Returns the related FormField.
 * 
 * @method \SilverStripe\ORM\HasManyList FormFieldOptionTranslations() Return the related FormFieldOptionTranslations.
 */
class FormFieldValue extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    /**
     * Adds the blacklist management fields to the given CMS $fields.
     * 
     * @param FieldList $fields          CMS fields to add blacklis entry fields to
     * @param SS_List   $formFieldValues FormFieldValues
     * @param string    $insertAfter     Field name to insert the custom fields after
     * 
     * @return void
     */
    public static function getFormFieldValueCMSFields(FieldList $fields, SS_List $formFieldValues = null, string $insertAfter = '') : void
    {
        if ($formFieldValues === null) {
            $formFieldValues = self::get();
        }
        foreach ($formFieldValues as $index => $formFieldValue) {
            /* @var $formFieldValue FormFieldValue */
            $fieldName = "CustomFormField_{$index}";
            $field     = ReadonlyField::create($fieldName, $formFieldValue->FieldTitle, $formFieldValue->FieldValue);
            if (!$fields->dataFieldByName($insertAfter)) {
                $fields->addFieldToTab('Root.Main', $field);
            } else {
                $fields->insertAfter($insertAfter, $field);
            }
            $insertAfter = $fieldName;
        }
    }
    
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilverCart_Forms_FormFieldValue';
    /**
     * DB attributes.
     * 
     * @var string[]
     */
    private static $db = [
        'FieldTitle' => 'Varchar',
        'FieldValue' => 'Text',
    ];
    /**
     * Has one relations.
     * 
     * @var string[]
     */
    private static $has_one = [
        'FormField'      => FormField::class,
        'ContactMessage' => ContactMessage::class,
    ];
    /**
     * DB attributes.
     * 
     * @var string[]
     */
    private static $summary_fields = [
        'FieldTitle',
        'FieldValue',
    ];
    
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
        return $this->defaultFieldLabels($includerelations, []);
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            
        });
        return parent::getCMSFields();
    }
    
    /**
     * On before write.
     * 
     * @return void
     */
    protected function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        if ($this->FormField()->FormFieldOptions()->exists()
         && $this->FormField()->FormFieldOptions()->byID($this->FieldValue) instanceof FormFieldOption
        ) {
            $this->FieldValue = $this->FormField()->FormFieldOptions()->byID($this->FieldValue)->Title;
        }
    }
    
    /**
     * Returns the translated title.
     * 
     * @return string
     */
    public function getTitle() : string
    {
        $title = $this->getField('FieldTitle');
        if ($this->FormField()->exists()) {
            $title = $this->FormField()->Title;
        }
        return (string) $title;
    }
}