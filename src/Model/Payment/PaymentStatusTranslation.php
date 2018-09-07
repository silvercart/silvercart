<?php

namespace SilverCart\Model\Payment;

use SilverCart\Dev\Tools;
use SilverStripe\ORM\DataObject;

/**
 * Translation class for PaymentStatus.
 *
 * @package SilverCart
 * @subpackage Model_Payment
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 07.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PaymentStatusTranslation extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(255)'
    ];
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = [
        'PaymentStatus' => PaymentStatus::class,
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartPaymentStatusTranslation';
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name()
    {
        return Tools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name()
    {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Field labels.
     * 
     * @param bool $includerelations include relations?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        $this->beforeUpdateFieldLabels(function (&$labels) {
            $labels = array_merge(
                    $labels,
                    Tools::field_labels_for(self::class),
                    Tools::field_labels_for(PaymentStatus::class)
            );
        });
        return parent::fieldLabels($includerelations);
    }
}