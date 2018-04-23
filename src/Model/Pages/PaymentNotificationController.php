<?php

namespace SilverCart\Model\Pages;

use SilverCart\Admin\Model\Config;
use SilverStripe\Control\Director;

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
     * @since 28.09.2017
     */
    public function process() {
        $paymentName = $this->urlParams['ID'];
        $paymentNotificationClassName = 'SilverCart\\' . $paymentName . '\\Payment' . $paymentName . 'Notification';
        $paymentChannel = '';
        if (array_key_exists('OtherID', $this->urlParams)) {
            $paymentChannel = $this->urlParams['OtherID'];
        }
        if (class_exists($paymentNotificationClassName)) {
            $paymentNotificationClass = new $paymentNotificationClassName();
            return $paymentNotificationClass->process($paymentChannel);
        }
    }
}