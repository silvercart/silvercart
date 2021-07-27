<?php

namespace SilverCart\Model\Customer;

use SilverCart\Dev\Tools;
use SilverCart\Model\Translation\TranslationExtension;
use SilverStripe\ORM\DataObject;

/**
 * Translations for Country.
 *
 * @package SilverCart
 * @subpackage Model\Customer
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 23.07.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @mixin TranslationExtension
 * 
 * @property string $Reason Reason
 */
class DeletedCustomerReasonTranslation extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'Silvercart_DeletedCustomerReasonTranslation';
    /**
     * Attributes.
     *
     * @var string[]
     */
    private static $db = [
        'Reason' => 'Text',
    ];
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = [
        'DeletedCustomerReason' => DeletedCustomerReason::class,
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
     * Summary fields.
     * 
     * @var string[]
     */
    private static $summary_fields = [
        'Reason',
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
     * @param bool $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, array_merge(DeletedCustomerReason::singleton()->defaultFieldLabels(), []));
    }
    
    /**
     * Returns the title.
     * 
     * @return string
     */
    public function getTitle() : string
    {
        return "{$this->NativeNameForLocale}: {$this->Reason}";
    }
}
