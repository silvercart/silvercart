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
class SilvercartCheckoutStep extends CustomHtmlFormStepPage {

    /**
     * Creates a default checkout page if non exists.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 28.01.2011
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();
        $shoppingCartPageID = 1;
        $records            = DataObject::get_one('SilvercartCheckoutStep');
        if (!$records) {

            // Set the ShoppingCartPage ID if available
            $shoppingCartPage = DataObject::get_one(
                'SilvercartCartPage'
            );

            if ($shoppingCartPage) {
                $shoppingCartPageID = $shoppingCartPage->ID;
            }

            $page                   = new SilvercartCheckoutStep();
            $page->Title            = _t('SilvercartPage.CHECKOUT');
            $page->URLSegment       = _t('SilvercartCheckoutStep.URL_SEGMENT', 'checkout');
            $page->Status           = "Published";
            $page->ShowInMenus      = true;
            $page->ShowInSearch     = true;
            $page->basename         = 'SilvercartCheckoutFormStep';
            $page->showCancelLink   = true;
            $page->cancelPageID     = $shoppingCartPageID;
            $page->write();
            $page->publish("Stage", "Live");
        }
    }
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
class SilvercartCheckoutStep_Controller extends CustomHtmlFormStepPage_Controller {

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
        $this->preferences['templateDir'] = PIXELTRICKS_CHECKOUT_BASE_PATH_REL . 'templates/Layout/';

        parent::init();

        // Inject payment and shippingmethods to shoppingcart, if available
        $member = Member::currentUser();
        
        if ($member) {
            $stepData       = $this->getCombinedStepData();
            $shoppingCart   = $member->SilvercartShoppingCart();

            if (isset($stepData['ShippingMethod'])) {
                $shoppingCart->setShippingMethodID($stepData['ShippingMethod']);
            }
            if (isset($stepData['PaymentMethod'])) {
                $shoppingCart->setPaymentMethodID($stepData['PaymentMethod']);
            }
        }
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
     * @param bool $includeShoppingCart set wether the shoppingcart should be
     *                                  deleted
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pxieltricks GmbH
     * @since 22.11.2010
     */
    public function deleteSessionData($includeShoppingCart = true) {
        parent::deleteSessionData();

        $member = Member::currentUser();

        if ($includeShoppingCart && $member) {
            if ($member->SilvercartShoppingCartID != 0) {
                $shoppingCart = $member->SilvercartShoppingCart();
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

    /**
     * Fügt den Versandadressdaten ein Präfix hinzu.
     *
     * @param string $prefix Präfix
     * @param array  $data   Extrahiert die Versandadressdaten aus den Checkout-daten.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 05.01.2011
     */
    public function extractAddressDataFrom($prefix, $data) {
        $addressData = array();
        $shippingDataFields = array(
            $prefix.'_Salutation'       => 'Salutation',
            $prefix.'_FirstName'        => 'FirstName',
            $prefix.'_Surname'          => 'Surname',
            $prefix.'_Addition'         => 'Addition',
            $prefix.'_Street'           => 'Street',
            $prefix.'_StreetNumber'     => 'StreetNumber',
            $prefix.'_Postcode'         => 'Postcode',
            $prefix.'_City'             => 'City',
            $prefix.'_Phone'            => 'Phone',
            $prefix.'_PhoneAreaCode'    => 'PhoneAreaCode',
            $prefix.'_Country'          => 'CountryID'
        );

        if (is_array($data)) {
            foreach ($shippingDataFields as $shippingFieldName => $dataFieldName) {
                if (isset($data[$shippingFieldName])) {
                    $addressData[$dataFieldName] = $data[$shippingFieldName];
                }
            }
        }

        return $addressData;
    }

    /**
     * Indicates wether ui elements for removing items and altering their
     * quantity should be shown in the shopping cart templates.
     *
     * During the checkout process the user may not be able to alter the
     * shopping cart.
     *
     * @return boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.02.2011
     */
    public function getEditableShoppingCart() {
        return false;
    }
}
