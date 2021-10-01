<?php

namespace SilverCart\Model\Pages\ContactFormPage;

use Moo\HasOneSelector\Form\Field as HasOneSelector;
use SilverCart\Dev\Tools;
use SilverCart\Model\ContactMessage;
use SilverCart\Model\EmailAddress;
use SilverCart\Model\Pages\ContactFormPage;
use SilverCart\Model\Translation\TranslatableDataObjectExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\SS_List;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * ContactForm Subject.
 *
 * @package SilverCart
 * @subpackage Model\Pages\ContactFormPage
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 14.09.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @mixin TranslatableDataObjectExtension
 * 
 * @property int        $Sort           Sort order
 * @property string     $Subject        Subject
 * @property string     $Title          Alias for Subject
 * @property DBHTMLText $RecipientsHtml Recipients as HTML string
 * 
 * @method ContactFormPage ContactFormPage() Returns the related ContactFormPage.
 * 
 * @method \SilverStripe\ORM\HasManyList ContactMessages()     Returns the related ContactMessages.
 * @method \SilverStripe\ORM\HasManyList SubjectTranslations() Returns the related SubjectTranslations.
 * 
 * @method \SilverStripe\ORM\ManyManyList Recipients() Returns the related Recipients.
 */
class Subject extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    /**
     * Adds the blacklist management fields to the given CMS $fields.
     * 
     * @param FieldList $fields   CMS fields to add blacklis entry fields to
     * @param SS_List   $subjects Subjects
     * @param string    $name     Field / tab name
     * 
     * @return void
     */
    public static function getSubjectCMSFields(FieldList $fields, SS_List $subjects = null, string $name = 'Subjects') : void
    {
        if ($subjects === null) {
            $subjects = self::get();
        }
        $grid       = GridField::create($name, self::singleton()->i18n_plural_name(), $subjects, GridFieldConfig_RecordEditor::create());
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
    private static $table_name = 'SilverCart_ContactFormPage_Subject';
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
        'Subject'        => 'Varchar',
        'RecipientsHtml' => 'HTMLText',
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
     * Has many relations.
     *
     * @var string[]
     */
    private static $has_many = [
        'ContactMessages'     => ContactMessage::class,
        'SubjectTranslations' => SubjectTranslation::class,
    ];
    /**
     * Many many relations.
     *
     * @var string[]
     */
    private static $many_many = [
        'Recipients' => EmailAddress::class,
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
        'Subject',
        'RecipientsHtml',
    ];
    /**
     * List of extensions to use.
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
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'RecipientsHtml' => _t(self::class . '.Recipients', 'Recipients'),
        ]);
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            if (class_exists(HasOneSelector::class)) {
                $fields->removeByName('ContactFormPageID');
                $contactFormPageField = HasOneSelector::create('ContactFormPage', $this->fieldLabel('ContactFormPage'), $this, ContactFormPage::class)->setLeftTitle($this->fieldLabel('ContactFormPage'));
                $contactFormPageField->removeAddable();
                $fields->insertAfter('Sort', $contactFormPageField);
            }
        });
        return parent::getCMSFields();
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
    
    /**
     * Returns the Subject.
     *
     * @return string
     */
    public function getSubject() : string
    {
        return (string) $this->getTranslationFieldValue('Subject');
    }
    
    /**
     * Returns the additional email recipients as a html string
     * 
     * @return DBHTMLText
     */
    public function getRecipientsHtml() : DBHTMLText
    {
        $recipientsArray = [];
        if ($this->Recipients()->exists()) {
            foreach ($this->Recipients() as $recipient) {
                $recipientsArray[] = htmlentities($recipient->getEmailAddressWithName());
            }
        }
        return DBHTMLText::create()->setValue(implode('<br/>', $recipientsArray));
    }
}