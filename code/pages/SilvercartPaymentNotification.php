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

    /**
     * create a default record
     *
     * @return void 
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 23.11.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        if (!SiteTree::get_by_link(_t('SilvercartPaymentNotification.URL_SEGMENT', 'payment-notification'))) {

            $checkPage = DataObject::get_one(
                'Page',
                'ParentID = 0',
                true,
                'Sort DESC'
            );
            if ($checkPage) {
                $sort = $checkPage->Sort + 1;
            } else {
                $sort = 1;
            }

            $page               = new SilvercartPaymentNotification();
            $page->URLSegment   = _t('SilvercartPaymentNotification.URL_SEGMENT');
            $page->Title        = _t('SilvercartPaymentNotification.TITLE', 'payment notification');
            $page->Status       = 'Published';
            $page->Sort         = $sort;
            $page->ShowInMenus  = 0;
            $page->ShowInSearch = 0;
            $page->write();
            $page->publish('Stage', 'Live');
            $page->flushCache();
            DB::alteration_message('SilvercartPaymentNotification Page created', 'created');
        }
    }
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
