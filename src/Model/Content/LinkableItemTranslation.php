<?php

namespace SilverCart\Model\Content;

use SilverCart\Dev\Tools;
use SilverCart\Model\Translation\TranslationExtension;
use SilverStripe\ORM\DataObject;

/**
 * Represents a linkable item translation.
 * 
 * @package SilverCart
 * @subpackage Model\Content
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.02.2022
 * @copyright 2022 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Title       Title
 * @property string $Description Description
 * 
 * @method LinkableItem LinkableItem() Returns the related LinkableItem.
 * 
 * @mixin TranslationExtension
 */
class LinkableItemTranslation extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB table name.
     * 
     * @var string
     */
    private static $table_name = 'SilverCart_Content_LinkableItemTranslation';
    /**
     * DB attributes.
     * 
     * @var string[]
     */
    private static $db = [
        'Title'       => 'Varchar',
        'Description' => 'Text',
    ];
    /**
     * Has one relations.
     * 
     * @var string[]
     */
    private static $has_one = [
        'LinkableItem' => LinkableItem::class,
    ];
    /**
     * Extensions.
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
     * @param bool $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'Title'       => LinkableItem::singleton()->fieldLabel('Title'),
            'Description' => LinkableItem::singleton()->fieldLabel('Description'),
        ]);
    }
}