<?php

namespace SilverCart\Model\ShopEmail;

use SilverCart\Admin\Dev\ExampleData;
use SilverCart\Dev\Tools;
use SilverCart\Model\ShopEmail;
use SilverCart\Model\Translation\TranslatableDataObjectExtension;
use SilverStripe\Control\Director;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Shop Email Content.
 * 
 * @package SilverCart
 * @subpackage SubPackage
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.10.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $DisplayPosition     DisplayPosition
 * @property int    $Sort                Sort Order
 * @property string $ClassNameNice       ClassName Nice
 * @property string $Content             Content
 * @property string $DisplayContent      Display Content
 * @property string $DisplayPositionNice Display Position Nice
 * @property int    $ShopEmailID         ShopEmail ID
 * 
 * @method ShopEmail ShopEmail() Returns the related ShopEmail.
 * 
 * @method \SilverStripe\ORM\HasManyList ContentTranslations() Returns the related ContentTranslations.
 * 
 * @mixin TranslatableDataObjectExtension
 */
class Content extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    /**
     * Return a subclass map of Content that shouldn't be hidden through {@link Content::$hide_ancestor}
     *
     * @return array
     */
    public static function content_type_classes() : array
    {
        $classes        = ClassInfo::getValidSubClasses(self::class);
        $kill_ancestors = [];
        // figure out if there are any classes we don't want to appear
        foreach ($classes as $class) {
            $instance = singleton($class);
            // do any of the progeny want to hide an ancestor?
            if ($ancestor_to_hide = $instance->config()->get('hide_ancestor')) {
                // note for killing later
                $kill_ancestors[] = $ancestor_to_hide;
            }
        }
        // If any of the descendents don't want any of the elders to show up, cruelly render the elders surplus to
        // requirements
        if ($kill_ancestors) {
            $kill_ancestors = array_unique($kill_ancestors);
            foreach ($kill_ancestors as $mark) {
                // unset from $classes
                $idx = array_search($mark, $classes, true);
                if ($idx !== false) {
                    unset($classes[$idx]);
                }
            }
        }
        return $classes;
    }
    
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilverCart_ShopEmail_Content';
    /**
     * DB attributes
     *
     * @var string[]
     */
    private static $db = [
        'DisplayPosition' => 'Varchar',
        'Sort'            => 'Int',
    ];
    /**
     * Has one relations.
     * 
     * @var string[]
     */
    private static $has_one = [
        'ShopEmail' => ShopEmail::class,
    ];
    /**
     * Has many relations.
     * 
     * @var string[]
     */
    private static $has_many = [
        'ContentTranslations' => ContentTranslation::class,
    ];
    /**
     * Casted properties
     *
     * @var string[]
     */
    private static $casting = [
        'ClassNameNice'       => 'Text',
        'Content'             => 'HTMLText',
        'DisplayContent'      => 'HTMLText',
        'DisplayPositionNice' => 'Text',
    ];
    /**
     * Extensions
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
    private static $insert_translation_cms_fields_after = 'DisplayPosition';
    
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
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return string[]
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'ClassName' => _t(self::class . '.ClassName', 'Type')
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
            if ($this->ClassName !== Content::class) {
                $fields->removeByName('ContentTranslations');
            }
            $fields->removeByName('Sort');
            $fields->dataFieldByName('ShopEmailID')->setReadonly(true)->setDisabled(true);
            if ($this->exists()
             && count(self::content_type_classes()) > 1
            ) {
                $fields->addFieldToTab('Root.Main', DropdownField::create('ClassName', $this->fieldLabel('ClassName'), $this->getClassDropdown()));
            }
            if ($this->ShopEmail()->exists()) {
                $fields->removeByName('DisplayPosition');
                $fields->addFieldToTab('Root.Main', DropdownField::create('DisplayPosition', $this->fieldLabel('DisplayPosition'), $this->ShopEmail()->getCustomContentBlocks(), $this->DisplayPosition));
                $exampleEmail = ExampleData::render_example_email($this->ShopEmail()->TemplateName);
                if (!empty($exampleEmail)) {
                    $fields->findOrMakeTab('Root.Preview', $this->ShopEmail()->fieldLabel('Preview'));
                    $frame = '<iframe class="full-height" src="' . Director::absoluteURL('example-data/renderemail/' . $this->ShopEmail()->TemplateName) . '"></iframe>';
                    $fields->addFieldToTab('Root.Preview', LiteralField::create('Preview', $frame));
                }
            }
        });
        return parent::getCMSFields();
    }

    /**
     * Summary fields
     *
     * @return string[]
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'DisplayPositionNice' => $this->fieldLabel('DisplayPosition'),
            'Title'               => $this->fieldLabel('Content'),
        ];
        if (count(self::content_type_classes()) > 1) {
            $summaryFields['ClassNameNice'] = $this->fieldLabel('ClassName');
        }
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }

    /**
     * Get the class dropdown used in the CMS to change the class of a content. 
     * This returns the list of options in the dropdown as a Map from class name 
     * to singular name.
     *
     * @return array
     */
    protected function getClassDropdown() : array
    {
        $classes      = self::content_type_classes();
        $currentClass = null;
        $result       = [];
        foreach ($classes as $class) {
            $instance       = singleton($class);
            $pageTypeName   = $instance->i18n_singular_name();
            $currentClass   = $class;
            $result[$class] = $pageTypeName;
        }
        // sort alphabetically, and put current on top
        asort($result);
        if ($currentClass) {
            $currentPageTypeName = $result[$currentClass];
            unset($result[$currentClass]);
            $result = array_reverse($result);
            $result[$currentClass] = $currentPageTypeName;
            $result = array_reverse($result);
        }
        return $result;
    }
    
    /**
     * Returns the title.
     * 
     * @return string
     */
    public function getTitle() : string
    {
        return $this->getContent()->LimitWordCount(10, '...');
    }
    
    /**
     * 
     * @return string
     */
    public function getDisplayPositionNice() : string
    {
        $nice   = (string) $this->DisplayPosition;
        $blocks = $this->ShopEmail()->getCustomContentBlocks();
        if (array_key_exists($nice, $blocks)) {
            $nice = $blocks[$nice];
        }
        return $nice;
    }
    
    /**
     * Returns the Content
     *
     * @return DBHTMLText
     */
    public function getContent() : DBHTMLText
    {
        $content = $this->getTranslationFieldValue('Content');
        if (!($content instanceof DBHTMLText)) {
            $content = DBHTMLText::create()->setValue($content);
        }
        return $content;
    }
    
    /**
     * Returns the DisplayContent
     *
     * @return DBHTMLText
     */
    public function getDisplayContent() : DBHTMLText
    {
        return $this->getContent();
    }
    
    /**
     * Returns the ClassName as an i18n human readable string.
     *
     * @return string
     */
    public function getClassNameNice() : string
    {
        return (string) singleton($this->ClassName)->i18n_singular_name();
    }
}