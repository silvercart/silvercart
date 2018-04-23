<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

/**
 * Checkout step page.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutStep extends \Page {
    
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = array(
        'ContentStep1'  => 'HTMLText',
        'ContentStep2'  => 'HTMLText',
        'ContentStep3'  => 'HTMLText',
        'ContentStep4'  => 'HTMLText',
        'ContentStep5'  => 'HTMLText',
        'ContentStep6'  => 'HTMLText',
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartCheckoutStep';

    /**
     * icon for site tree
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/checkout_page-file.gif";
    
    /**
     * Field labels
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.09.2013
     */
    public function fieldLabels($includerelations = true) {
        $labels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'ContentStep1'        => _t(CheckoutStep::class . '.ContentStep1', 'Content Step 1 - Login/Register'),
                    'ContentStep2'        => _t(CheckoutStep::class . '.ContentStep2', 'Content Step 2 - Shipping-/Invoiceaddress'),
                    'ContentStep3'        => _t(CheckoutStep::class . '.ContentStep3', 'Content Step 3 - Shippingmethod'),
                    'ContentStep4'        => _t(CheckoutStep::class . '.ContentStep4', 'Content Step 4 - Paymentmethod'),
                    'ContentStep5'        => _t(CheckoutStep::class . '.ContentStep5', 'Content Step 5 - Overview'),
                    'ContentStep6'        => _t(CheckoutStep::class . '.ContentStep6', 'Content Step 6 - Confirmation'),
                    'StepContent'         => _t(CheckoutStep::class . '.StepContent', 'Content for single steps'),
                    'Forward'             => _t(CheckoutStep::class . '.FORWARD', 'Next'),
                    'OrderNow'            => _t(CheckoutStep::class . '.ORDER_NOW', 'Order now'),
                    'ChosenPayment'       => _t(CheckoutStep::class . '.CHOSEN_PAYMENT', 'chosen payment method'),
                    'ChosenShipping'      => _t(CheckoutStep::class . '.CHOSEN_SHIPPING', 'chosen shipping method'),
                    'SubscribeNewsletter' => _t(CheckoutStep::class . '.I_SUBSCRIBE_NEWSLETTER', 'I subscribe to the newsletter'),
                )
        );
        
        $this->extend('updateFieldLabels', $labels);
        
        return $labels;
    }

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * CMS fields
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $fields->findOrMakeTab('Root.StepContent', $this->fieldLabel('StepContent'));
        $fields->addFieldToTab('Root.StepContent', new HTMLEditorField('ContentStep1', $this->fieldLabel('ContentStep1'), 15));
        $fields->addFieldToTab('Root.StepContent', new HTMLEditorField('ContentStep2', $this->fieldLabel('ContentStep2'), 15));
        $fields->addFieldToTab('Root.StepContent', new HTMLEditorField('ContentStep3', $this->fieldLabel('ContentStep3'), 15));
        $fields->addFieldToTab('Root.StepContent', new HTMLEditorField('ContentStep4', $this->fieldLabel('ContentStep4'), 15));
        $fields->addFieldToTab('Root.StepContent', new HTMLEditorField('ContentStep5', $this->fieldLabel('ContentStep5'), 15));
        $fields->addFieldToTab('Root.StepContent', new HTMLEditorField('ContentStep6', $this->fieldLabel('ContentStep6'), 15));
        
        return $fields;
    }
}