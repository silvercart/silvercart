<?php

namespace SilverCart\Model\Forms;

use SilverCart\Dev\Tools;
use SilverCart\Model\Translation\TranslatableDataObjectExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\SS_List;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Custom form field option.
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
 * @property string $Title Title
 * @property int    $Sort  Sort order
 * 
 * @method FormField FormField() Returns the related FormField.
 * 
 * @method \SilverStripe\ORM\HasManyList FormFieldOptionTranslations() Return the related FormFieldOptionTranslations.
 */
class FormFieldOption extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    /**
     * Adds the blacklist management fields to the given CMS $fields.
     * 
     * @param FieldList $fields           CMS fields to add blacklis entry fields to
     * @param SS_List   $formFieldOptions FormFieldOptions
     * @param string    $name             Field / tab name
     * 
     * @return void
     */
    public static function getFormFieldOptionCMSFields(FieldList $fields, SS_List $formFieldOptions = null, string $name = 'FormFieldOptions') : void
    {
        if ($formFieldOptions === null) {
            $formFieldOptions = self::get();
        }
        $grid       = GridField::create($name, self::singleton()->i18n_plural_name(), $formFieldOptions, GridFieldConfig_RecordEditor::create());
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
    private static $table_name = 'SilverCart_Forms_FormFieldOption';
    /**
     * DB attributes.
     * 
     * @var string[]
     */
    private static $db = [
        'Sort' => 'Int',
    ];
    /**
     * Casted attributes.
     * 
     * @var string[]
     */
    private static $casting = [
        'Title' => 'Varchar',
    ];
    /**
     * Has one relations.
     * 
     * @var string[]
     */
    private static $has_one = [
        'FormField' => FormField::class,
    ];
    /**
     * Has one relations.
     * 
     * @var string[]
     */
    private static $has_many = [
        'FormFieldOptionTranslations' => FormFieldOptionTranslation::class,
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
    private static $insert_translation_cms_fields_before = 'Sort';
    
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
     * Returns the translated title.
     * 
     * @return string
     */
    public function getTitle() : string
    {
        return (string) $this->getTranslationFieldValue('Title');
    }
}