<?php

namespace SilverCart\Model\ShopEmail;

use SilverCart\Dev\Tools;
use SilverCart\Model\Translation\TranslationExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Shop Email Content Translation.
 * 
 * @package SilverCart
 * @subpackage SubPackage
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.10.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Content   Content
 * @property int    $ContentID Content ID
 * 
 * @method Content Content() Returns the related Content.
 * 
 * @mixin TranslationExtension
 */
class ContentTranslation extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilverCart_ShopEmail_ContentTranslation';
    /**
     * DB attributes
     *
     * @var string[]
     */
    private static $db = [
        'Content' => 'HTMLText',
    ];
    /**
     * Has one relations.
     * 
     * @var string[]
     */
    private static $has_one = [
        'Content' => Content::class,
    ];
    /**
     * Extensions
     * 
     * @var string[]
     */
    private static $extensions = [
        TranslationExtension::class,
    ];
    
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
            'Content' => Content::singleton()->fieldLabel('Content'),
        ]);
    }

    /**
     * Summary fields
     *
     * @return string[]
     */
    public function summaryFields() : array
    {
        $summaryFields = array_merge(parent::summaryFields(), [
            'Title' => $this->fieldLabel('Content'),
        ]);
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Returns the title.
     * 
     * @return string
     */
    public function getTitle() : string
    {
        return $this->getContentSummary();
    }
    
    /**
     * Returns the title.
     * 
     * @return string
     */
    public function getContentSummary() : string
    {
        return DBHTMLText::create()->setValue($this->Content)->LimitWordCount(10, '...');
    }
}