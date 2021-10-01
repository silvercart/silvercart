<?php

namespace SilverCart\Model\Pages\ContactFormPage;

use SilverCart\Dev\Tools;
use SilverCart\Model\Translation\TranslationExtension;
use SilverStripe\ORM\DataObject;

/**
 * Translations for ContactFormPage Subject.
 *
 * @package SilverCart
 * @subpackage Model\Pages\ContactFormPage
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Subject Subject
 * @property string $Title   Alias for Subject
 * 
 * @method Subject Subject() Returns the related Subject.
 */
class SubjectTranslation extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilverCart_ContactFormPage_SubjectTranslation';
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'Subject' => 'Varchar',
    ];
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = [
        'Subject' => Subject::class,
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
            'Subject' => Subject::singleton()->fieldLabel('Subject'),
        ]);
    }
    
    /**
     * Returns the Subject.
     *
     * @return string
     */
    public function getTitle() : string
    {
        return (string) $this->Subject;
    }
}