<?php
/**
 * feddback from payment providers
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 23.11.2010
 * @license none
 */
class SilvercartPaymentNotification extends Page {

}

/**
 * Script fuer Rueckmeldungen von Zahlungsprovidern.
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 23.11.2010
 * @license LGPL
 */
class SilvercartPaymentNotification_Controller extends Page_Controller {
    
    /**
     * Initialisierung
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 02.12.2010
     */
    public function init() {
        Director::forceSSL();
        parent::init();
    }
    /**
     * Bestimmt das zu ladende Zahlungsmodul und ruft dessen Verarbeitungs
     * methode auf.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 23.11.2010
     */
    public function process() {
        $paymentNotificationClassName = 'SilvercartPayment'.$this->urlParams['ID'].'Notification';

        if (class_exists($paymentNotificationClassName)) {
            $paymentNotificationClass = new $paymentNotificationClassName();
            return $paymentNotificationClass->process();
        }
    }
}
