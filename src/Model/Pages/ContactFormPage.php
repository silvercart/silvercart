<?php

namespace SilverCart\Model\Pages;

use SilverCart\Model\BlacklistEntry;
use SilverCart\Model\Forms\FormField as FormField;
use SilverCart\Model\Pages\ContactFormPage\Subject;
use SilverCart\Model\Pages\MetaNavigationHolder;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

/**
 * show an process a contact form.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $ResponseContent ResponseContent
 * 
 * @method \SilverStripe\ORM\HasManyList FormFields() Returns the related FormFields.
 * @method \SilverStripe\ORM\HasManyList Subjects()   Returns the related Subjects.
 */
class ContactFormPage extends MetaNavigationHolder
{
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $db = [
        'ResponseContent'       => 'HTMLText',
    ];
    /**
     * Has many relations.
     *
     * @var string[]
     */
    private static $has_many = [
        'FormFields' => FormField::class,
        'Subjects'   => Subject::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartContactFormPage';
    /**
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     * 
     * @var string
     */
    private static $icon_class = 'font-icon-p-attachment';
    
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
            'ResponseContent' => _t(ContactFormPage::class . '.ResponseContent', 'Use field for street'),
            'FormFields'      => _t(ContactFormPage::class . '.FormFields', 'Form Fields'),
            'Subject'         => _t(ContactFormPage::class . '.Subject', 'Reason for your request'),
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
            $fields->addFieldToTab('Root.Main', HTMLEditorField::create('ResponseContent', $this->fieldLabel('ResponseContent')));
            FormField::getFormFieldCMSFields($fields, $this->FormFields());
            Subject::getSubjectCMSFields($fields, $this->Subjects());
            BlacklistEntry::getBlackListCMSFields($fields);
        });
        return parent::getCMSFields();
    }
}