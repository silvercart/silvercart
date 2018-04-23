<?php

namespace SilverCart\Forms\Checkout;

use SilverCart\Forms\CustomForm;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\OptionsetField;
use SilverCart\Model\Pages\CheckoutStep;

/**
 * Form to use in checkout.
 *
 * @package SilverCart
 * @subpackage Forms_Checkout
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 25.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CheckoutNewCustomerForm extends CustomForm {
    
    /**
     * List of required fields.
     *
     * @var array
     */
    private static $requiredFields = [
        'AnonymousOptions',
    ];

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $options = [
                '1' => $this->fieldLabel('ProceedWithRegistration'),
                '2' => $this->fieldLabel('ProceedWithoutRegistration'),
            ];
            $value = null;
            if ($this->getController()->getCheckout()->getDataValue('IsAnonymousCheckout') === true) {
                $value = '2';
            }
            $fields += [
                OptionsetField::create('AnonymousOptions', '', $options, $value),
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
            $actions += [
                FormAction::create('submit', CheckoutStep::singleton()->fieldLabel('Forward'))
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
     * @since 16.11.2017
     */
    public function doSubmit($data, CustomForm $form) {
        switch ($data['AnonymousOptions']) {
            case '2':
                // Checkout without registration
                $checkout = $this->getController()->getCheckout();
                /* @var $checkout \SilverCart\Checkout\Checkout */
                $currentStep = $checkout->getCurrentStep();
                $checkout->addDataValue('IsAnonymousCheckout', true);
                $currentStep->complete();
                $currentStep->redirectToNextStep();
                break;
            case '1':
            default:
                $checkout = $this->getController()->getCheckout();
                /* @var $checkout \SilverCart\Checkout\Checkout */
                $checkout->addDataValue('ShowRegistrationForm', true);
                $checkout->saveInSession();
                $this->getController()->redirectBack();
        }
    }
    
}
