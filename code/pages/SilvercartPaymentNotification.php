<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * feddback from payment providers
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 23.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPaymentNotification extends Page {
    
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.10.2011
     */
    public static $icon = "silvercart/images/page_icons/metanavigation_page";

}

/**
 * Script fuer Rueckmeldungen von Zahlungsprovidern.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 23.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
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
        if (SilvercartConfig::EnableSSL()) {
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.04.2011
     */
    public function process() {
        $paymentNotificationClassName = 'SilvercartPayment'.$this->urlParams['ID'].'Notification';
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
