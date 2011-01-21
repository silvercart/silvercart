<?php

/**
 * Seite fuer den Checkoutprozess.
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 09.11.2010
 * @license none
 */
class CheckoutStep extends CustomHtmlFormStepPage {

}

/**
 * Seite fuer den Checkoutprozess.
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 09.11.2010
 * @license none
 */
class CheckoutStep_Controller extends CustomHtmlFormStepPage_Controller {

    /**
     * Legt Voreinstellungen fest.
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.11.2010
     */
    protected $preferences = array(
        'templateDir' => ''
    );
    /**
     * Enthaelt das Zahlungsmodul-Objekt.
     *
     * @var PaymentMethod
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    protected $paymentMethodObj = false;

    /**
     * Bindet Formulare ein und laedt CSS- und Javascriptdateien.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 09.11.2010
     */
    public function init() {
        $this->preferences['templateDir'] = PIXELTRICKS_CHECKOUT_BASE_PATH_REL.'templates/Layout/';
        
        parent::init();
    }

    /**
     * Gibt zurueck, ob ein Fehler im Zahlungsmodul aufgetreten ist.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getErrorOccured() {
        return $this->paymentMethodObj->getErrorOccured();
    }

    /**
     * Gibt die Fehlerliste als DataObjectSet zurueck.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getErrorList() {
        return $this->paymentMethodObj->getErrorList();
    }

    /**
     * Loescht den Warenkorb.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pxieltricks GmbH
     * @since 22.11.2010
     */
    public function deleteSessionData() {
        parent::deleteSessionData();

        $member = Member::currentUser();

        if ($member) {
            if ($member->shoppingCartID != 0) {
                $shoppingCart = $member->ShoppingCart();
                $shoppingCart->delete();
            }
        }

        if (isset($_SESSION['paypal_module_payer_id'])) {
            unset($_SESSION['paypal_module_payer_id']);
        }
        if (isset($_SESSION['paypal_module_token'])) {
            unset($_SESSION['paypal_module_token']);
        }
    }
}
