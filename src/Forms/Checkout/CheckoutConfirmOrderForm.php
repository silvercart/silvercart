<?php

namespace SilverCart\Forms\Checkout;

use SilverCart\Dev\Tools;
use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\TextareaField;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Pages\CheckoutStep;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Payment\PaymentMethod;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Security\Member;

/**
 * Form to confirm the order in checkout.
 *
 * @package SilverCart
 * @subpackage Forms_Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutConfirmOrderForm extends CustomForm {
    
    /**
     * Custom extra CSS classes.
     *
     * @var array
     */
    protected $customExtraClasses = [
        'form-horizontal',
    ];
    
    /**
     * List of required fields.
     *
     * @var array
     */
    private static $requiredFields = [
        'HasAcceptedTermsAndConditions',
        'HasAcceptedRevocationInstruction',
    ];

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            
            $termsAndConditions = _t(CheckoutStep::class . '.I_ACCEPT_TERMS',
                    'I accept the <a href="{link}" target="blank">terms and conditions</a>.',
                    [
                        'link' => Tools::PageByIdentifierCodeLink('TermsOfServicePage')
                    ]
            );
            $revocationInstruction = _t(CheckoutStep::class . '.I_ACCEPT_REVOCATION',
                    'I accept the <a href="{link}" target="blank">revocation instructions</a>.',
                    [
                        'link' => Tools::PageByIdentifierCodeLink('SilvercartRevocationInstructionPage'),
                    ]
            );
            
            $fields += [
                $notesField = TextareaField::create('Note', ''),
                new CheckboxField('HasAcceptedTermsAndConditions', Tools::string2html($termsAndConditions)),
                new CheckboxField('HasAcceptedRevocationInstruction', Tools::string2html($revocationInstruction)),
                new CheckboxField('SubscribedToNewsletter', CheckoutStep::singleton()->fieldLabel('SubscribeNewsletter')),
            ];
            
            $notesField->setPlaceholder(Page::singleton()->fieldLabel('YourRemarks') . '...');
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
            $submitButtonTitle = CheckoutStep::singleton()->fieldLabel('OrderNow');
            $checkout          = $this->getController()->getCheckout();
            $paymentMethodID   = $checkout->getDataValue('PaymentMethod');
            
            if (is_numeric($paymentMethodID)) {
                $paymentMethod = PaymentMethod::get()->byID($paymentMethodID);

                if ($paymentMethod->exists() &&
                    $paymentMethod->hasMethod('getOrderConfirmationSubmitButtonTitle')) {
                    $submitButtonTitle = $paymentMethod->getOrderConfirmationSubmitButtonTitle();
                }
            }
            
            $actions += [
                FormAction::create('submit', $submitButtonTitle)
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
        if (!array_key_exists('SubscribedToNewsletter', $data)) {
            $data['SubscribedToNewsletter'] = false;
        }
        $checkout = $this->getController()->getCheckout();
        /* @var $checkout \SilverCart\Checkout\Checkout */
        $checkout->addDataValue('Note', $data['Note']);
        $checkout->addDataValue('HasAcceptedTermsAndConditions', $data['HasAcceptedTermsAndConditions']);
        $checkout->addDataValue('HasAcceptedRevocationInstruction', $data['HasAcceptedRevocationInstruction']);
        $checkout->addDataValue('SubscribedToNewsletter', $data['SubscribedToNewsletter']);
        
        $currentStep = $checkout->getCurrentStep();
        $currentStep->complete();
        $currentStep->redirectToNextStep();
    }
    
    /**
     * The newsletter checkbox should not be shown if a registered customer has
     * already subscribed to the newsletter.
     * 
     * @return boolean answer 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.04.2018
     */
    public function ShowNewsletterCheckbox() {
        $customer = Customer::currentRegisteredCustomer();
        if ($customer instanceof Member &&
            $customer->SubscribedToNewsletter == 1) {
            return false;
        }
        return true;
    }
    
}

