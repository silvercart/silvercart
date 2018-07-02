<?php

namespace SilverCart\Forms\Checkout;

use SilverCart\Forms\CustomForm;
use SilverCart\Model\Payment\PaymentMethod;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\Validator;

/**
 * Form to choose the payment method in checkout.
 *
 * @package SilverCart
 * @subpackage Forms_Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutChoosePaymentMethodForm extends CustomForm {
    
    /**
     * Custom extra CSS classes.
     *
     * @var array
     */
    protected $customExtraClasses = [
        'form-horizontal',
    ];

    /**
     * The payment method chosen in checkout
     *
     * @var PaymentMethod
     */
    protected $paymentMethod = null;
    
    /**
     * Create a new form, with the given fields an action buttons.
     *
     * @param PaymentMethod  $paymentMethod Payment method context
     * @param RequestHandler $controller    Optional parent request handler
     * @param string         $name          The method on the controller that will return this form object.
     * @param FieldList      $fields        All of the fields in the form - a {@link FieldList} of {@link FormField} objects.
     * @param FieldList      $actions       All of the action buttons in the form - a {@link FieldLis} of {@link FormAction} objects
     * @param Validator      $validator     Override the default validator instance (Default: {@link RequiredFields})
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.11.2017
     */
    public function __construct(PaymentMethod $paymentMethod, RequestHandler $controller = null, $name = self::DEFAULT_NAME, FieldList $fields = null, FieldList $actions = null, Validator $validator = null) {
        $this->setPaymentMethod($paymentMethod);
        parent::__construct($controller, $name, $fields, $actions, $validator);
    }

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $fields += [
                HiddenField::create('PaymentMethod', 'PaymentMethod', $this->getPaymentMethod()->ID),
            ];
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions() {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $title = _t(self::class . '.ChoosePaymentMethod',
                    'I would like to pay with {payment}',
                    [
                        'payment' => $this->getPaymentMethod()->Name,
                    ]
            );
            
            $actions += [
                FormAction::create('submit', $title)
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            ];
        });
        return parent::getCustomActions();
    }
    
    /**
     * Submits the form.
     * 
     * @param array      $data Submitted data
     * @param CustomForm $form Form
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function doSubmit($data, CustomForm $form) {
        $checkout = $this->getController()->getCheckout();
        /* @var $checkout \SilverCart\Checkout\Checkout */
        $chosenPaymentMethod = $checkout->getDataValue('PaymentMethod');
        $currentStep         = $checkout->getCurrentStep();
        $sentPaymentMethod   = $data['PaymentMethod'];
        if ($chosenPaymentMethod != $sentPaymentMethod) {
            $currentStep->resetNextSteps();
        }
        $paymentMethod = PaymentMethod::get()->byID($sentPaymentMethod);
        if ($paymentMethod instanceof PaymentMethod &&
            $paymentMethod->exists()) {
            
            $checkout->addDataValue('PaymentMethod', $sentPaymentMethod);
            $currentStep->complete();
            $currentStep->redirectToNextStep();
        } else {
            $this->getController()->redirectBack();
        }
    }
    
    /**
     * Returns the related payment method
     *
     * @return PaymentMethod 
     */
    public function getPaymentMethod() {
        return $this->paymentMethod;
    }

    /**
     * Sets the related payment method
     *
     * @param PaymentMethod $paymentMethod Related payment method
     * 
     * @return void
     */
    public function setPaymentMethod(PaymentMethod $paymentMethod) {
        $this->paymentMethod = $paymentMethod;
    }
    
}
