<?php

/**
 * checkout step for shipping method
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license BSD
 */
class CheckoutFormStep3 extends CustomHtmlForm {

    protected $formFields = array(
        'ShippingMethod' => array(
            'type' => 'DropdownField',
            'title' => 'Versandart',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        )
    );

    /**
     * Initialisierung
     *
     * @param Controller $controller  Das Controllerobjekt
     * @param array      $params      Zusaetzliche Parameter
     * @param array      $preferences Array mit Voreinstellungen
     * @param bool       $barebone    Gibt an, ob das Formular komplett initialisiert werden soll
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 3.1.2011
     * @return void
     */
    protected function fillInFieldValues() {
        $this->controller->fillFormFields(&$this->formFields);
        $stepData = $this->controller->getCombinedStepData();
        $paymentMethod = DataObject::get_by_id('PaymentMethod', $stepData['PaymentMethod']);
        if ($paymentMethod) {
            $allowedShippingMethods = $paymentMethod->shippingMethods();
            if ($allowedShippingMethods) {
                $this->formFields['ShippingMethod']['value'] = $allowedShippingMethods->map('ID', 'TitleWithCarrierAndFee', '--Versandart--');
            }
        }
    }

    /**
     * Wird ausgefuehrt, wenn nach dem Senden des Formulars keine Validierungs-
     * fehler aufgetreten sind.
     * Speichert die gesendeten Formulardaten in der Session zum spaeteren
     * Abruf.
     *
     * @param SS_HTTPRequest $data     Enthaelt die gesendeten "rohen" Formulardaten
     * @param Form           $form     wird nicht verwendet
     * @param array          $formData Enthaelt die geparsten Formulardaten
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

