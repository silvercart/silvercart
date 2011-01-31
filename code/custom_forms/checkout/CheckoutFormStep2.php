<?php

/**
 * checkout step for payment method
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license BSD
 */
class CheckoutFormStep2 extends CustomHtmlForm {

    protected $formFields = array(
        'PaymentMethod' => array(
            'type' => 'DropdownField',
            'title' => 'Bezahlart',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        )
    );

    /**
     * preferences
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 26.1.2011
     */
    protected $preferences = array(
        'submitButtonTitle'         => 'weiter',
        'stepTitle' => 'Zahlart'
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
        $this->controller->fillFormFields(&$this->formFields);

        $stepData = $this->controller->getCombinedStepData();
        if ($stepData['Shipping_Country'] != "") {
            $shippingCountry = DataObject::get_by_id('Country', $stepData['Shipping_Country']);
            if ($shippingCountry) {
                $allowedPaymentMethods = $shippingCountry->paymentMethods();
                if ($allowedPaymentMethods) {
                    $this->formFields['PaymentMethod']['value'] = $allowedPaymentMethods->toDropDownMap('ID', 'Name', _t('CheckoutFormStep2.EMPTYSTRING_PAYMENTMETHOD', '--choose payment method--'));
                }
            }
        }
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
     * @since 09.11.2010
     */
    public function submitSuccess($data, $form, $formData) {
        $this->controller->setStepData($formData);
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }

}

