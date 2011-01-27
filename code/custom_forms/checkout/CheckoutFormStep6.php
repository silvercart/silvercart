<?php

/**
 * CheckoutReturnFromPaymentProviderPage
 * Legt die Bestellung an.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license BSD
 */
class CheckoutFormStep6 extends CustomHtmlForm {

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
     * Prozessormethode.
     *
     * @return empty
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.11.2010
     */
    public function process() {
        $checkoutData = $this->controller->getCombinedStepData();

        if (!$this->paymentMethodObj) {
            $this->paymentMethodObj = DataObject::get_by_id(
                            'PaymentMethod',
                            $checkoutData['PaymentMethod']
            );
        }

        if ($this->paymentMethodObj) {
            $this->paymentMethodObj->setController($this->controller);
            $this->paymentMethodObj->processReturnJumpFromPaymentProvider();
        } else {
            Director::redirect($this->controller->Link() . '/Cancel');
        }
    }

}

