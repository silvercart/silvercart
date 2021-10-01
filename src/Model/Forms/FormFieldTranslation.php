<?php

namespace SilverCart\Model\Forms;

use SilverCart\Dev\Tools;
use SilverCart\Model\Translation\TranslationExtension;
use SilverStripe\ORM\DataObject;

/**
 * Translations for custom FormField.
 *
 * @package SilverCart
 * @subpackage Model\Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Subject Subject
 * @property string $Title   Alias for Subject
 * 
 * @method FormField FormField() Returns the related FormField.
 */
class FormFieldTranslation extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilverCart_Forms_FormFieldTranslation';
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'Title'       => 'Varchar',
        'Description' => 'HTMLText',
    ];
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = [
        'FormField' => FormField::class,
    ];
    /**
     * Summary fields.
     *
     * @var string[]
     */
    private static $summary_fields = [
        'Title',
    ];
    /**
     * List of extensions to use.
     *
     * @var string[]
     */
    private static $extensions = [
        TranslationExtension::class,
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
        return $this->defaultFieldLabels($includerelations, [
            'Description' => FormField::singleton()->fieldLabel('Description'),
            'Title'       => FormField::singleton()->fieldLabel('Title'),
        ]);
    }
}