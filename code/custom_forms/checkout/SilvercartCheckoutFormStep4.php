<?php

/**
 * checkout step for order confirmation
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license BSD
 */
class SilvercartCheckoutFormStep4 extends CustomHtmlForm {

    protected $formFields = array(
        'ChosenShippingMethod' => array(
            'type' => 'ReadonlyField',
            'title' => 'gewählte Versandart'
        ),
        'ChosenPaymentMethod' => array(
            'type' => 'ReadonlyField',
            'title' => 'gewählte Bezahlart'
        ),
        'Note' => array(
            'type' => 'TextareaField'
        ),
        /**
         * leagal fields
         */
        'HasAcceptedTermsAndConditions' => array(
            'type' => 'CheckboxField',
            'title' => 'Ich akzeptiere die allgemeinen Geschäftsbedingungen',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'HasAcceptedRevocationInstruction' => array(
            'type' => 'CheckboxField',
            'title' => 'Ich habe die Widerrufsbelehrung gelesen',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        ),
        'SubscribedToNewsletter' => array(
            'type' => 'CheckboxField',
            'title' => 'Ich möchte den Newsletter abonnieren'
        )
    );
    /**
     * preferences
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 26.1.2011
     */
    protected $preferences = array(
        'submitButtonTitle' => 'bestellen',
        'stepTitle' => 'Übersicht'
    );

    /**
     * constructor
     *
     * @param Controller $controller  the controller object
     * @param array      $params      additional parameters
     * @param array      $preferences array with preferences
     * @param bool       $barebone    is the form initialized completely?
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.01.2011
     */
    public function __construct($controller, $params = null, $preferences = null, $barebone = false) {
        parent::__construct($controller, $params, $preferences, $barebone);

        if (!$barebone) {
            /*
             * redirect a user if his cart is empty
             */
            if (!$this->controller->isFilledCart()) {
                Director::redirect("/home/");
            }
        }
    }

    /**
     * Set initial form values
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 09.11.2010
     */
    protected function fillInFieldValues() {
        $this->preferences['submitButtonTitle'] = _t('SilvercartCheckoutFormStep.ORDER', 'order');
        $this->preferences['stepTitle'] = _t('SilvercartCheckoutFormStep.OVERVIEW', 'overview');
        $this->controller->fillFormFields(&$this->formFields);
        $this->formFields['ChosenShippingMethod']['title'] = _t('SilvercartCheckoutFormStep.CHOOSEN_SHIPPING', 'choosen shipping method');
        $this->formFields['ChosenPaymentMethod']['title'] = _t('SilvercartCheckoutFormStep.CHOOSEN_PAYMENT', 'choosen payment method');
        $this->formFields['HasAcceptedTermsAndConditions']['title'] = _t('SilvercartCheckoutFormStep.I_ACCEPT_TERMS', 'I accept the terms and conditions.');
        $this->formFields['HasAcceptedRevocationInstruction']['title'] = _t('SilvercartCheckoutFormStep.I_ACCEPT_REVOCATION', 'I accept the revocation instructions');
        $this->formFields['SubscribedToNewsletter']['title'] = _t('SilvercartCheckoutFormStep.I_SUBSCRIBE_NEWSLETTER', 'I subscribe to the newsletter');

        $stepData = $this->controller->getCombinedStepData();

        $chosenShippingMethod = DataObject::get_by_id('SilvercartShippingMethod', $stepData['ShippingMethod']);
        if ($chosenShippingMethod) {
            $this->formFields['ChosenShippingMethod']['value'] = $chosenShippingMethod->Title;
        }

        $chosenPaymentMethod = DataObject::get_by_id('SilvercartPaymentMethod', $stepData['PaymentMethod']);
        if ($chosenPaymentMethod) {
            $this->formFields['ChosenPaymentMethod']['value'] = $chosenPaymentMethod->Name;
        }
    }

    /**
     * returns address data as ArrayData
     *
     * @return ArrayData
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.01.2011
     */
    public function AddressData() {
        $checkoutData = $this->controller->getCombinedStepData();
        $shippingAddress = $this->controller->extractAddressDataFrom('Shipping', $checkoutData);
        $invoiceAddress = $this->controller->extractAddressDataFrom('Invoice', $checkoutData);

        $shippingCountry = DataObject::get_by_id(
                        'SilvercartCountry',
                        $shippingAddress['CountryID']
        );

        if ($shippingCountry) {
            $shippingAddress['country'] = $shippingCountry;
        }

        $invoiceCountry = DataObject::get_by_id(
                        'SilvercartCountry',
                        $invoiceAddress['CountryID']
        );

        if ($invoiceCountry) {
            $invoiceAddress['country'] = $invoiceCountry;
        }

        $addressData = new ArrayData(
                        array(
                            'shippingAddress' => $shippingAddress,
                            'invoiceAddress' => $invoiceAddress
                        )
        );
        return $addressData;
    }

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 4.1.2011
     */
    public function submitSuccess($data, $form, $formData) {
        $this->controller->setStepData($formData);
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }

}

