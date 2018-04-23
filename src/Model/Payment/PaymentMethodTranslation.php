<?php

namespace SilverCart\Model\Payment;

use SilverCart\Dev\Tools;
use SilverStripe\ORM\DataObject;

/**
 * Translations for the class PaymentMethod
 * This has no relation to PaymentMethod and is not decorated by the
 * TranslationExtension like the other language classes because it has to
 * be extended by the language class of a payment module
 *
 * @package SilverCart
 * @subpackage Model_Payment
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PaymentMethodTranslation extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'Name'                      => 'Varchar(150)',
        'paymentDescription'        => 'Text',
        'LongPaymentDescription'    => 'Text',
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartPaymentMethodTranslation';

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.05.2012
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.05.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this);
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.05.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'LongPaymentDescription'    => PaymentMethod::singleton()->fieldLabel('LongPaymentDescription'),
                    'Name'                      => PaymentMethod::singleton()->fieldLabel('Name'),
                    'paymentDescription'        => PaymentMethod::singleton()->fieldLabel('PaymentDescription'),
                    'PaymentDescription'        => PaymentMethod::singleton()->fieldLabel('PaymentDescription'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}
