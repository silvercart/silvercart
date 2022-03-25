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
class CheckoutConfirmOrderForm extends CustomForm
{
    /**
     * Custom extra CSS classes.
     *
     * @var array
     */
    protected $customExtraClasses = [
        'form-horizontal',
    ];

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields()
    {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $fields += [
                $notesField = TextareaField::create('Note', ''),
                CheckboxField::create('SubscribedToNewsletter', CheckoutStep::singleton()->fieldLabel('SubscribeNewsletter')),
            ];
            if ($this->getController()->EnableTermsAndConditionsCheckbox) {
                $fields[] = CheckboxField::create('AcceptTermsAndConditions', $this->getController()->data()->AcceptTermsAndConditionsText);
            }
            $notesField->setPlaceholder(Page::singleton()->fieldLabel('YourRemarks') . '...');
        });
        return parent::getCustomFields();
    }

    /**
     * Returns the required fields.
     * 
     * @return array
     */
    public function getRequiredFields()
    {
        $requiredFields = parent::getRequiredFields();
        if ($this->getController()->EnableTermsAndConditionsCheckbox) {
            $requiredFields[] = 'AcceptTermsAndConditions';
        }
        return $requiredFields;
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions()
    {
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
     * @since 29.09.2018
     */
    public function doSubmit($data, CustomForm $form)
    {
        if (!array_key_exists('SubscribedToNewsletter', $data)) {
            $data['SubscribedToNewsletter'] = false;
        }
        $checkout = $this->getController()->getCheckout();
        /* @var $checkout \SilverCart\Checkout\Checkout */
        $checkout->addDataValue('Note', $data['Note']);
        $checkout->addDataValue('SubscribedToNewsletter', $data['SubscribedToNewsletter']);
        
        $currentStep = $checkout->getCurrentStep();
        $currentStep->complete();
        $currentStep->redirectToNextStep();
    }
    
    /**
     * The newsletter checkbox should not be shown if a registered customer has
     * already subscribed to the newsletter.
     * 
     * @return bool
     */
    public function ShowNewsletterCheckbox() : bool
    {
        $show           = true;
        $newsletterPage = Page::PageByIdentifierCode(Page::IDENTIFIER_NEWSLETTER_PAGE);
        if (!($newsletterPage instanceof Page)) {
            $show = false;
        } else {
            $customer = Customer::currentRegisteredCustomer();
            if ($customer instanceof Member
             && $customer->SubscribedToNewsletter == 1
            ) {
                $show = false;
            }
        }
        return $show;
    }
    
    /**
     * Returns the "accept terms and conditions" text.
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getAcceptTermsAndConditionsText()
    {
        $checkoutStep = $this->getController()->data();
        if (!($checkoutStep instanceof CheckoutStep)) {
            $checkoutStep = CheckoutStep::get()->first();
        }
        return $checkoutStep->TermsAndConditionsText;
    }
}