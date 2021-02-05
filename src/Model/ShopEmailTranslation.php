<?php

namespace SilverCart\Model;

use SilverCart\Dev\Tools;
use SilverCart\Model\ShopEmail;
use SilverStripe\ORM\DataObject;

/**
 * ShopEmail Translation.
 *
 * @package SilverCart
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 10.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Subject            Subject
 * @property string $CustomEmailContent Custom Email Content
 * 
 * @method ShopEmail ShopEmail() Returns the related ShopEmail.
 */
class ShopEmailTranslation extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'Subject'            => 'Text',
        'CustomEmailContent' => 'HTMLText',
    ];
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = [
        'ShopEmail' => ShopEmail::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartShopEmailTranslation';
    
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
            'Subject'   => ShopEmail::singleton()->fieldLabel('Subject'),
            'ShopEmail' => ShopEmail::singleton()->singular_name(),
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
            'Subject'   => $this->fieldLabel('Subject'),
        ]);
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
}