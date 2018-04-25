<?php

namespace SilverCart\Model\Pages;

use SilverCart\Admin\Model\Config;
use SilverCart\Model\Payment\PaymentMethod;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;

/**
 * PaymentNotification Controller class. Handles Payment provider requests.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PaymentNotificationController extends \PageController {
    
    /**
     * Allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = array(
        'process',
    );

    /**
     * Initialisierung
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2017
     */
    public function init() {
        if (Config::EnableSSL()) {
            Director::forceSSL();
        }
        parent::init();
    }
    /**
     * Bestimmt das zu ladende Zahlungsmodul und ruft dessen Verarbeitungs
     * methode auf.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function process(HTTPRequest $request) {
        $paymentName = $this->urlParams['ID'];
        $paymentID   = $this->urlParams['OtherID'];
        $payment     = PaymentMethod::get()->byID($paymentID);
        if ($payment instanceof PaymentMethod &&
            $payment->exists()) {
            return $payment->doProcessNotification($request);
        }
    }
}