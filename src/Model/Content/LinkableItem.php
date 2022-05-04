<?php

namespace SilverCart\Model\Content;

use Sheadawson\Linkable\Forms\LinkField;
use Sheadawson\Linkable\Models\Link;
use SilverCart\Dev\Tools;
use SilverCart\Extensions\Model\FontAwesomeExtension;
use SilverCart\Model\Translation\TranslatableDataObjectExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\SSViewer;

/**
 * A linkable item to use for display purposes.
 * 
 * @package SilverCart
 * @subpackage Model\Content
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.02.2022
 * @copyright 2022 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Title          Title
 * @property string $Description    Description
 * @property string $CustomIconHTML Custom Icon HTML
 * @property string $ExtraClasses   ExtraClasses
 * @property int    $Sort           Sort
 * 
 * @method Link       Link()       Returns the related Link.
 * @method SiteConfig SiteConfig() Returns the related SiteConfig.
 * 
 * @method \SilverStripe\ORM\HasManyList LinkableItemTranslations() Returns the related translations.
 * 
 * @mixin FontAwesomeExtension
 * @mixin TranslatableDataObjectExtension
 */
class LinkableItem extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Table name
     * 
     * @var string
     */
    private static $table_name = 'SilverCart_Content_LinkableItem';
    /**
     * DB attributes
     * 
     * @var string[]
     */
    private static $db = [
        'ExtraClasses'   => 'Varchar',
        'CustomIconHTML' => 'Text',
        'Sort'           => 'Int',
    ];
    /**
     * Casted attributes
     * 
     * @var string[]
     */
    private static $casting = [
        'Title'       => 'Varchar',
        'Description' => 'Text',
    ];
    /**
     * Has one relations.
     * 
     * @var string[]
     */
    private static $has_one = [
        'Link'       => Link::class,
        'SiteConfig' => SiteConfig::class,
    ];
    /**
     * Has many relations
     * 
     * @var string[]
     */
    private static $has_many = [
        'LinkableItemTranslations' => LinkableItemTranslation::class,
    ];
    /**
     * Default sort fields and directions
     *
     * @var string
     */
    private static $default_sort = 'Sort';
    /**
     * Extensions
     * 
     * @var string[]
     */
    private static $extensions = [
        FontAwesomeExtension::class,
        TranslatableDataObjectExtension::class,
    ];
    /**
     * Searchable fields
     * 
     * @var string[]
     */
    private static $searchable_fields = [
        'LinkableItemTranslations.Title',
        'LinkableItemTranslations.Description',
    ];
    /**
     * Summary fields
     * 
     * @var string[]
     */
    private static $summary_fields = [
        'Title',
        'Description',
        'Link.LinkType',
    ];
    /**
     * Determines to insert the translation CMS fields by TranslatableDataObjectExtension.
     * 
     * @var bool
     */
    private static $insert_translation_cms_fields = true;
    /**
     * Determines to insert the translation CMS fields before this field.
     * 
     * @var string
     */
    private static $insert_translation_cms_fields_before = 'ExtraClasses';
    /**
     * Determines to insert the FA CMS fields after this field.
     * 
     * @var string
     */
    private static $insert_fa_field_after = 'ExtraClasses';

    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object.
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
     * @param bool $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, []);
    }
    
    /**
     * Returns the CMS fields
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            Link::config()->set('templates', []);
            $fields->removeByName('Sort');
            $fields->replaceField(
                'LinkID',
                LinkField::create('LinkID', $this->fieldLabel('Link'))
                    ->setAllowedTypes([])
            );
            $fields->dataFieldByName('ExtraClasses')->setDescription($this->owner->fieldLabel('ExtraClassesDesc'));
        });
        return parent::getCMSFields();
    }
    
    /**
     * Returns the translated Title.
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
     * Adds an email link if no link is related yet. Returns the created or
     * related link.
     * 
     * @param string      $email           Email address to set
     * @param string|null $title           Title to set
     * @param bool        $openInNewWindow Open link in new window?
     * 
     * @return Link
     */
    public function addEmailLink(string $email, ?string $title = null, bool $openInNewWindow = false) : Link
    {
        $link = $this->Link();
        if (!$link->exists()) {
            $link                  = Link::create();
            $link->Email           = $email;
            $link->Title           = $title;
            $link->OpenInNewWindow = $openInNewWindow;
            $link->Type            = 'Email';
            $link->write();
            $this->LinkID = $link->ID;
            $this->write();
        }
        return $link;
    }
    
    /**
     * Adds an external link if no link is related yet. Returns the created or
     * related link.
     * 
     * @param string      $url             URL to set
     * @param string|null $title           Title to set
     * @param bool        $openInNewWindow Open link in new window?
     * 
     * @return Link
     */
    public function addExternalLink(string $url, ?string $title = null, bool $openInNewWindow = false) : Link
    {
        $link = $this->Link();
        if (!$link->exists()) {
            $link                  = Link::create();
            $link->URL             = $url;
            $link->Title           = $title;
            $link->OpenInNewWindow = $openInNewWindow;
            $link->Type            = 'URL';
            $link->write();
            $this->LinkID = $link->ID;
            $this->write();
        }
        return $link;
    }
    
    /**
     * Adds a phone link if no link is related yet. Returns the created or
     * related link.
     * 
     * @param string      $phone           Phone number to set
     * @param string|null $title           Title to set
     * @param bool        $openInNewWindow Open link in new window?
     * 
     * @return Link
     */
    public function addPhoneLink(string $phone, ?string $title = null, bool $openInNewWindow = false) : Link
    {
        $link = $this->Link();
        if (!$link->exists()) {
            $link                  = Link::create();
            $link->Phone           = $phone;
            $link->Title           = $title;
            $link->OpenInNewWindow = $openInNewWindow;
            $link->Type            = 'Phone';
            $link->write();
            $this->LinkID = $link->ID;
            $this->write();
        }
        return $link;
    }
    
    /**
     * Returns the icon for template.
     * 
     * @param string $cssClasses Optional CSS classes
     * 
     * @return DBHTMLText
     */
    public function Icon(string $cssClasses = '') : DBHTMLText
    {
        if (empty($this->CustomIconHTML)) {
            $icon = $this->FontAwesomeIconHTML($cssClasses);
        } else {
            $icon = DBHTMLText::create()->setValue($this->CustomIconHTML);
        }
        return $icon;
    }
    
    /**
     * Returns the rendered object.
     * 
     * @param string $templateAddition Optional template name addition
     * 
     * @return DBHTMLText
     */
    public function forTemplate(string $templateAddition = '') : DBHTMLText
    {
        $addition  = empty($templateAddition) ? '' : "_{$templateAddition}";
        $templates = SSViewer::get_templates_by_class(static::class, $addition, __CLASS__);
        return $this->renderWith($templates);
    }
}