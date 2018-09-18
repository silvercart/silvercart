<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Pages\AddressHolder;
use SilverCart\Model\Pages\Page;
use SilverStripe\Control\Controller;
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
class CheckoutStep extends \Page
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = [
        'ContentStep1'  => 'HTMLText',
        'ContentStep2'  => 'HTMLText',
        'ContentStep3'  => 'HTMLText',
        'ContentStep4'  => 'HTMLText',
        'ContentStep5'  => 'HTMLText',
        'ContentStep6'  => 'HTMLText',
    ];
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
     * @since 18.09.2018
     */
    public function fieldLabels($includerelations = true)
    {
        $this->beforeUpdateFieldLabels(function (&$labels) {
            $labels = array_merge(
                    $labels,
                    Tools::field_labels_for(self::class),
                    [
                        'StepContent'         => _t(CheckoutStep::class . '.StepContent', 'Content for single steps'),
                        'Forward'             => _t(CheckoutStep::class . '.FORWARD', 'Next'),
                        'OrderNow'            => _t(CheckoutStep::class . '.ORDER_NOW', 'Order now'),
                        'ChosenPayment'       => _t(CheckoutStep::class . '.CHOSEN_PAYMENT', 'chosen payment method'),
                        'ChosenShipping'      => _t(CheckoutStep::class . '.CHOSEN_SHIPPING', 'chosen shipping method'),
                        'SubscribeNewsletter' => _t(CheckoutStep::class . '.I_SUBSCRIBE_NEWSLETTER', 'I subscribe to the newsletter'),
                        'ThanksForYourOrder'  => _t(Page::class . '.ORDER_THANKS', 'Many thanks for your order'),
                        'Register'            => _t(Page::class . '.REGISTER', 'Register'),
                    ]
            );
        });
        return parent::fieldLabels($includerelations);
    }

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
     * CMS fields
     * 
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function($fields) {
            $fields->findOrMakeTab('Root.StepContent', $this->fieldLabel('StepContent'));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('ContentStep1', $this->fieldLabel('ContentStep1'))->addExtraClass('stacked')->setRows(8));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('ContentStep2', $this->fieldLabel('ContentStep2'))->addExtraClass('stacked')->setRows(8));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('ContentStep3', $this->fieldLabel('ContentStep3'))->addExtraClass('stacked')->setRows(8));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('ContentStep4', $this->fieldLabel('ContentStep4'))->addExtraClass('stacked')->setRows(8));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('ContentStep5', $this->fieldLabel('ContentStep5'))->addExtraClass('stacked')->setRows(8));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('ContentStep6', $this->fieldLabel('ContentStep6'))->addExtraClass('stacked')->setRows(8));
        });
        return parent::getCMSFields();
    }
    
    /**
     * Returns the meta title for the current checkout context.
     * 
     * @return string
     */
    public function getMetaTitle()
    {
        $metaTitle = $this->Title;
        $ctrl      = Controller::curr();
        $action    = $ctrl->getAction();
        /* @var $ctrl CheckoutStepController */
        switch ($action) {
            case 'step':
                $step = $ctrl->getCheckout()->getCurrentStep();
                if ($step->hasMethod('ShowRegistrationForm')
                 && $step->ShowRegistrationForm()) {
                    $stepTitle = $this->fieldLabel('Register');
                } else {
                    $stepTitle = $step->StepTitle();
                }
                $metaTitle = "{$stepTitle} - {$metaTitle}";
                break;
            case 'editAddress':
                $editAddress = Address::singleton()->fieldLabel('EditAddress');
                $metaTitle   = "{$editAddress} - {$metaTitle}";
                break;
            case 'addNewAddress':
                $addNewAddress = AddressHolder::singleton()->fieldLabel('AddNewAddress');
                $metaTitle   = "{$addNewAddress} - {$metaTitle}";
                break;
            case 'thanks':
                $metaTitle   = "{$this->fieldLabel('ThanksForYourOrder')} - {$metaTitle}";
                break;
            default:
                $metaTitle = $this->Title;
        }
        return $metaTitle;
    }
}