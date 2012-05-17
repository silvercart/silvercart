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
 * @subpackage Config
 */

/**
 * Collects all default records to avoid redundant code when it comes to relations.
 * You do not need to search for other default records, they are all here.
 *
 * @package Silvercart
 * @subpackage Config
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 16.02.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartRequireDefaultRecords extends DataObject {

    /**
     * If set to true the next /dev/build/ will add test data to the database.
     *
     * @var boolean
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.02.2011
     */
    protected static $enableTestData = false;
    
    /**
     * Translation locale
     *
     * @var string 
     */
    protected $translationLocale = null;
    
    /**
     * Returns the translation locale
     *
     * @return string 
     */
    public function getTranslationLocale() {
        return $this->translationLocale;
    }

    /**
     * Sets the translation locale
     *
     * @param string $translationLocale Translation locale
     * 
     * @return void
     */
    public function setTranslationLocale($translationLocale) {
        $this->translationLocale = $translationLocale;
    }
    
    /**
     * Creates the default groups used in SilverCart
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012 
     */
    public function createDefaultGroups() {
        // Create an own group for anonymous customers
        $anonymousGroup = DataObject::get_one('Group', "`Code` = 'anonymous'");
        if (!$anonymousGroup) {
            $anonymousGroup             = new Group();
            $anonymousGroup->Title      = _t('SilvercartCustomer.ANONYMOUSCUSTOMER', 'anonymous customer');
            $anonymousGroup->Code       = "anonymous";
            $anonymousGroup->Pricetype  = "gross";
            $anonymousGroup->write();
        }

        // Create an own group for b2b customers
        $B2Bgroup = DataObject::get_one('Group', "`Code` = 'b2b'");
        if (!$B2Bgroup) {
            $B2Bgroup               = new Group();
            $B2Bgroup->Title        = _t('SilvercartCustomer.BUSINESSCUSTOMER', 'business customer');
            $B2Bgroup->Code         = "b2b";
            $B2Bgroup->Pricetype    = "net";
            $B2Bgroup->write();
        }

        //create a group for b2c customers
        $B2Cgroup = DataObject::get_one('Group', "`Code` = 'b2c'");
        if (!$B2Cgroup) {
            $B2Cgroup               = new Group();
            $B2Cgroup->Title        = _t('SilvercartCustomer.REGULARCUSTOMER', 'regular customer');
            $B2Cgroup->Code         = "b2c";
            $B2Cgroup->Pricetype    = "gross";
            $B2Cgroup->write();
        }
    }
    
    /**
     * Creates the default SilvercartConfig if not exists
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function createDefaultConfig() {
        // create a SilvercartConfig if not exist
        if (!DataObject::get_one('SilvercartConfig')) {
            $silvercartConfig = new SilvercartConfig();
            $silvercartConfig->DefaultCurrency = 'EUR';
            $email = Email::getAdminEmail();
            if ($email != '') {
                $silvercartConfig->EmailSender = $email;
            }
            $silvercartConfig->write();
        }
    }
    
    /**
     * Creates the default SilvercartOrderStatus if not exists
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function createDefaultOrderStatus() {
        // create order status
        $defaultStatusEntries = array(
            'pending' => array(
                'en_US' => 'Waiting for payment',
                'en_GB' => 'Waiting for payment',
                'de_DE' => 'Auf Zahlungseingang wird gewartet',
            ),
            'payed' => array(
                'en_US' => 'Payed',
                'en_GB' => 'Payed',
                'de_DE' => 'Bezahlt',
            ),
            'shipped' => array(
                'en_US' => 'Order shipped',
                'en_GB' => 'Order shipped',
                'de_DE' => 'Bestellung versendet',
            ),
            'inwork' => array(
                'en_US' => 'In work',
                'en_GB' => 'In work',
                'de_DE' => 'Bestellung in Arbeit',
            ),
        );
        $locales        = array('de_DE', 'en_GB', 'en_US');
        $fallbackLocale = false;

        if (!in_array(Translatable::get_current_locale(), $locales)) {
            $locales[]      = Translatable::get_current_locale();
            $fallbackLocale = Translatable::get_current_locale();
        }

        if ($fallbackLocale !== false) {
            $defaultStatusEntries['pending'][$fallbackLocale] = $defaultStatusEntries['pending']['en_US'];
            $defaultStatusEntries['payed'][$fallbackLocale]   = $defaultStatusEntries['payed']['en_US'];
            $defaultStatusEntries['shipped'][$fallbackLocale] = $defaultStatusEntries['shipped']['en_US'];
            $defaultStatusEntries['inwork'][$fallbackLocale]  = $defaultStatusEntries['inwork']['en_US'];
        }
        $this->createDefaultTranslatableDataObject($defaultStatusEntries, 'SilvercartOrderStatus');
    }
    
    /**
     * Creates the default SilvercartAvailabilityStatus if not exists
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function createDefaultAvailabilityStatus() {
        $defaults = array(
            'available'     => array(
                'en_US' => 'available',
                'en_GB' => 'available',
                'de_DE' => 'verfügbar'
            ),
            'not-available' => array(
                'en_US' => 'unavailable',
                'en_GB' => 'unavailable',
                'de_DE' => 'nicht verfügbar'
            )
        );
        $locales        = array('de_DE', 'en_GB', 'en_US');
        $fallbackLocale = false;

        if (!in_array(Translatable::get_current_locale(), $locales)) {
            $locales[]      = Translatable::get_current_locale();
            $fallbackLocale = Translatable::get_current_locale();
        }

        if ($fallbackLocale !== false) {
            $defaults['available'][$fallbackLocale]     = $defaults['available']['en_US'];
            $defaults['not-available'][$fallbackLocale] = $defaults['not-available']['en_US'];
        }
        $this->createDefaultTranslatableDataObject($defaults, 'SilvercartAvailabilityStatus');
    }
    
    /**
     * Creates a translatable DataObject by the given entries and for the current 
     * locale.
     *
     * @param array  $translatableDataObjectEntries      Entries to create
     * @param string $translatableDataObjectName         Name of DataObject to create entries for
     * @param string $translatableDataObjectLanguageName Name of DataObjectLanguage to create entries for (if not default)
     * @param string $translatableDataObjectRelationName Name of relation name DataObjectLanguage -> DataObject (if not default)
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function createDefaultTranslatableDataObject($translatableDataObjectEntries, $translatableDataObjectName, $translatableDataObjectLanguageName = '', $translatableDataObjectRelationName = '') {
        if (empty($translatableDataObjectLanguageName)) {
            $translatableDataObjectLanguageName = $translatableDataObjectName . 'Language';
        }
        if (empty($translatableDataObjectRelationName)) {
            $translatableDataObjectRelationName = $translatableDataObjectName . 'ID';
        }
        $translationLocale = $this->getTranslationLocale();
        foreach ($translatableDataObjectEntries as $code => $languages) {
            $obj = DataObject::get_one(
                    $translatableDataObjectName,
                    sprintf(
                            "`%s`.`Code` = '%s'",
                            $translatableDataObjectName,
                            $code
                    )
            );
            if (!$obj) {
                $obj = new $translatableDataObjectName();
                $obj->Code = $code;
                $obj->write();
            }
            if (!array_key_exists($translationLocale, $languages) &&
                array_key_exists('en_US', $languages)) {
                $languages[$translationLocale] = $languages['en_US'];
            }
            foreach ($languages as $locale => $title) {
                $objLanguage    = DataObject::get_one(
                        $translatableDataObjectLanguageName,
                        sprintf(
                                "`Locale` = '%s' AND `%s` = '%s'",
                                $locale,
                                $translatableDataObjectRelationName,
                                $obj->ID
                        )
                );
                if (!$objLanguage) {
                    $objLanguage = new $translatableDataObjectLanguageName();
                    $objLanguage->Locale                                = $locale;
                    $objLanguage->{$translatableDataObjectRelationName} = $obj->ID;
                    $objLanguage->Title                                 = $title;
                    $objLanguage->write();
                } elseif (empty($objLanguage->Title)) {
                    $objLanguage->Title                                 = $title;
                    $objLanguage->write();
                }
            }
        }
    }
    
    /**
     * Creates the default SilvercartNumberRanges if not exists
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function createDefaultNumberRanges() {
        // create number ranges
        $orderNumbers = DataObject::get('SilvercartNumberRange', "`Identifier`='OrderNumber'");
        if (!$orderNumbers) {
            $orderNumbers = new SilvercartNumberRange();
            $orderNumbers->Identifier = 'OrderNumber';
            $orderNumbers->Title = _t('SilvercartNumberRange.ORDERNUMBER', 'Ordernumber');
            $orderNumbers->write();
        }
        $customerNumbers = DataObject::get('SilvercartNumberRange', "`Identifier`='CustomerNumber'");
        if (!$customerNumbers) {
            $customerNumbers = new SilvercartNumberRange();
            $customerNumbers->Identifier = 'CustomerNumber';
            $customerNumbers->Title = _t('SilvercartNumberRange.CUSTOMERNUMBER', 'Customernumber');
            $customerNumbers->write();
        }
    }
    
    /**
     * Creates the default SiteTree if not exists
     * 
     * @return SilvercartFrontPage
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function createDefaultSiteTree() {
        $rootPage = DataObject::get_one('SilvercartPage',"`IdentifierCode` = 'SilvercartCartPage'");
        if (!$rootPage) {
            //create a silvercart front page (parent of all other SilverCart pages
            $rootPage                   = new SilvercartFrontPage();
            $rootPage->IdentifierCode   = "SilvercartFrontPage";
            $rootPage->Title            = 'SilverCart';
            $rootPage->MenuTitle        = "SilverCart";
            if (SiteTree::get_by_link('home')) {
                $rootPage->URLSegment   = 'webshop';
            } else {
                $rootPage->URLSegment   = 'home';
            }
            $rootPage->ShowInMenus      = false;
            $rootPage->ShowInSearch     = false;
            $rootPage->Status           = "Published";
            $rootPage->CanViewType      = "Anyone";
            $rootPage->Content          = _t('SilvercartFrontPage.DEFAULT_CONTENT', '<h2>Welcome to <strong>SilverCart</strong> Webshop!</h2>');
            $rootPage->write();
            $rootPage->publish("Stage", "Live");
            
            //create a deeplink page as child of the silvercart root
            $deeplinkPage                   = new SilvercartDeeplinkPage();
            $deeplinkPage->IdentifierCode   = "SilvercartDeeplinkPage";
            $deeplinkPage->Title            = _t('SilvercartDeeplinkPage.DEFAULT_TITLE');
            $deeplinkPage->URLSegment       = 'deeplink';
            $deeplinkPage->Status           = 'Published';
            $deeplinkPage->ParentID         = $rootPage->ID;
            $deeplinkPage->ShowInMenus      = false;
            $deeplinkPage->ShowInSearch     = false;
            $deeplinkPage->CanViewType      = "Anyone";
            $deeplinkPage->write();
            $deeplinkPage->publish("Stage", "Live");

            //create a silvercart product group holder as a child af the silvercart root
            $productGroupHolder                 = new SilvercartProductGroupHolder();
            $productGroupHolder->Title          = _t('SilvercartProductGroupHolder.DEFAULT_TITLE',      'product groups');
            $productGroupHolder->URLSegment     = _t('SilvercartProductGroupHolder.DEFAULT_URLSEGMENT', 'productgroups');
            $productGroupHolder->Status         = "Published";
            $productGroupHolder->ParentID       = $rootPage->ID;
            $productGroupHolder->IdentifierCode = "SilvercartProductGroupHolder";
            $productGroupHolder->write();
            $productGroupHolder->publish("Stage", "Live");

            //create a cart page
            $cartPage                   = new SilvercartCartPage();
            $cartPage->Title            = _t('SilvercartCartPage.DEFAULT_TITLE');
            $cartPage->URLSegment       = _t('SilvercartCartPage.DEFAULT_URLSEGMENT', 'cart');
            $cartPage->Status           = "Published";
            $cartPage->ShowInMenus      = true;
            $cartPage->ShowInSearch     = false;
            $cartPage->IdentifierCode   = "SilvercartCartPage";
            $cartPage->ParentID         = $rootPage->ID;
            $cartPage->write();
            $cartPage->publish("Stage", "Live");

            //create a silvercart checkout step (checkout) as achild of the silvercart root
            $checkoutStep                   = new SilvercartCheckoutStep();
            $checkoutStep->Title            = _t('SilvercartCheckoutStep.DEFAULT_TITLE');
            $checkoutStep->URLSegment       = _t('SilvercartCheckoutStep.DEFAULT_URLSEGMENT', 'checkout');
            $checkoutStep->Status           = "Published";
            $checkoutStep->ShowInMenus      = true;
            $checkoutStep->ShowInSearch     = true;
            $checkoutStep->basename         = 'SilvercartCheckoutFormStep';
            $checkoutStep->showCancelLink   = true;
            $checkoutStep->cancelPageID     = $cartPage->ID;
            $checkoutStep->ParentID         = $rootPage->ID;
            $checkoutStep->IdentifierCode   = "SilvercartCheckoutStep";
            $checkoutStep->write();
            $checkoutStep->publish("Stage", "Live");

            //create a my account holder page as child of the silvercart root
            $myAccountHolder                    = new SilvercartMyAccountHolder();
            $myAccountHolder->Title             = _t('SilvercartMyAccountHolder.DEFAULT_TITLE', 'my account');
            $myAccountHolder->URLSegment        = _t('SilvercartMyAccountHolder.DEFAULT_URLSEGMENT', 'my-account');
            $myAccountHolder->Status            = "Published";
            $myAccountHolder->ShowInMenus       = false;
            $myAccountHolder->ShowInSearch      = false;
            $myAccountHolder->ParentID          = $rootPage->ID;
            $myAccountHolder->IdentifierCode    = "SilvercartMyAccountHolder";
            $myAccountHolder->write();
            $myAccountHolder->publish("Stage", "Live");

            //create a silvercart data page as a child of silvercart my account holder
            $dataPage                   = new SilvercartDataPage();
            $dataPage->Title            = _t('SilvercartDataPage.DEFAULT_TITLE', 'my data');
            $dataPage->URLSegment       = _t('SilvercartDataPage.DEFAULT_URLSEGMENT', 'my-data');
            $dataPage->Status           = "Published";
            $dataPage->ShowInMenus      = true;
            $dataPage->ShowInSearch     = false;
            $dataPage->CanViewType      = "Inherit";
            $dataPage->ParentID         = $myAccountHolder->ID;
            $dataPage->IdentifierCode   = "SilvercartDataPage";
            $dataPage->write();
            $dataPage->publish("Stage", "Live");

            //create a silvercart order holder as a child of silvercart my account holder
            $orderHolder                    = new SilvercartOrderHolder();
            $orderHolder->Title             = _t('SilvercartOrderHolder.DEFAULT_TITLE', 'my orders');
            $orderHolder->URLSegment        = _t('SilvercartOrderHolder.DEFAULT_URLSEGMENT', 'my-orders');
            $orderHolder->Status            = "Published";
            $orderHolder->ShowInMenus       = true;
            $orderHolder->ShowInSearch      = false;
            $orderHolder->CanViewType       = "Inherit";
            $orderHolder->ParentID          = $myAccountHolder->ID;
            $orderHolder->IdentifierCode    = "SilvercartOrderHolder";
            $orderHolder->write();
            $orderHolder->publish("Stage", "Live");

            //create an order detail page as a child of the order holder
            $orderDetailPage                    = new SilvercartOrderDetailPage();
            $orderDetailPage->Title             = _t('SilvercartOrderDetailPage.DEFAULT_TITLE', 'order details');
            $orderDetailPage->URLSegment        = _t('SilvercartOrderDetailPage.DEFAULT_URLSEGMENT', 'order-details');
            $orderDetailPage->Status            = "Published";
            $orderDetailPage->ShowInMenus       = false;
            $orderDetailPage->ShowInSearch      = false;
            $orderDetailPage->CanViewType       = "Inherit";
            $orderDetailPage->ParentID          = $orderHolder->ID;
            $orderDetailPage->IdentifierCode    = "SilvercartOrderDetailPage";
            $orderDetailPage->write();
            $orderDetailPage->publish("Stage", "Live");

            //create a silvercart address holder as a child of silvercart my account holder
            $addressHolder                  = new SilvercartAddressHolder();
            $addressHolder->Title           = _t('SilvercartAddressHolder.DEFAULT_TITLE', 'address overview');
            $addressHolder->URLSegment      = _t('SilvercartAddressHolder.DEFAULT_URLSEGMENT', 'address-overview');
            $addressHolder->Status          = "Published";
            $addressHolder->ShowInMenus     = true;
            $addressHolder->ShowInSearch    = false;
            $addressHolder->CanViewType     = "Inherit";
            $addressHolder->ParentID        = $myAccountHolder->ID;
            $addressHolder->IdentifierCode  = "SilvercartAddressHolder";
            $addressHolder->write();
            $addressHolder->publish("Stage", "Live");

            //create a silvercart address page as a child of silvercart my account holder
            $addressPage                    = new SilvercartAddressPage();
            $addressPage->Title             = _t('SilvercartAddressPage.DEFAULT_TITLE', 'address details');
            $addressPage->URLSegment        = _t('SilvercartAddressPage.DEFAULT_URLSEGMENT', 'address-details');
            $addressPage->Status            = "Published";
            $addressPage->ShowInMenus       = false;
            $addressPage->ShowInSearch      = false;
            $addressPage->CanViewType       = "Inherit";
            $addressPage->ParentID          = $addressHolder->ID;
            $addressPage->IdentifierCode    = "SilvercartAddressPage";
            $addressPage->write();
            $addressPage->publish("Stage", "Live");

            //create a meta navigation holder
            $metaNavigationHolder                   = new SilvercartMetaNavigationHolder();
            $metaNavigationHolder->Title            = _t('SilvercartMetaNavigationHolder.DEFAULT_TITLE');
            $metaNavigationHolder->URLSegment       = _t('SilvercartMetaNavigationHolder.DEFAULT_URLSEGMENT', 'metanavigation');
            $metaNavigationHolder->Status           = "Published";
            $metaNavigationHolder->ShowInMenus      = 0;
            $metaNavigationHolder->IdentifierCode   = "SilvercartMetaNavigationHolder";
            $metaNavigationHolder->ParentID         = $rootPage->ID;
            $metaNavigationHolder->write();
            $metaNavigationHolder->publish("Stage", "Live");

            //create a contact form page as a child of the meta navigation holder
            $contactPage                    = new SilvercartContactFormPage();
            $contactPage->Title             = _t('SilvercartContactFormPage.DEFAULT_TITLE', 'contact');
            $contactPage->URLSegment        = _t('SilvercartContactFormPage.DEFAULT_URLSEGMENT', 'contact');
            $contactPage->Status            = "Published";
            $contactPage->ShowInMenus       = 1;
            $contactPage->IdentifierCode    = "SilvercartContactFormPage";
            $contactPage->ParentID          = $metaNavigationHolder->ID;
            $contactPage->write();
            $contactPage->publish("Stage", "Live");

            //create a terms of service page as a child of the meta navigation holder
            $termsOfServicePage                 = new SilvercartMetaNavigationPage();
            $termsOfServicePage->Title          = _t('TermsOfServicePage.DEFAULT_TITLE', 'terms of service');
            $termsOfServicePage->URLSegment     = _t('TermsOfServicePage.DEFAULT_URLSEGMENT', 'terms-of-service');
            $termsOfServicePage->Status         = "Published";
            $termsOfServicePage->ShowInMenus    = 1;
            $termsOfServicePage->ParentID       = $metaNavigationHolder->ID;
            $termsOfServicePage->IdentifierCode = "TermsOfServicePage";
            $termsOfServicePage->write();
            $termsOfServicePage->publish("Stage", "Live");

            //create an imprint page as a child of the meta navigation holder
            $imprintPage                    = new SilvercartMetaNavigationPage();
            $imprintPage->Title             = _t('ImprintPage.DEFAULT_TITLE', 'imprint');
            $imprintPage->URLSegment        = _t('ImprintPage.DEFAULT_URLSEGMENT', 'imprint');
            $imprintPage->Status            = "Published";
            $imprintPage->ShowInMenus       = 1;
            $imprintPage->ParentID          = $metaNavigationHolder->ID;
            $imprintPage->IdentifierCode    = "ImprintPage";
            $imprintPage->write();
            $imprintPage->publish("Stage", "Live");

            //create a data privacy statement page as a child of the meta navigation holder
            $dataPrivacyStatementPage                   = new SilvercartMetaNavigationPage();
            $dataPrivacyStatementPage->Title            = _t('SilvercartDataPrivacyStatementPage.DEFAULT_TITLE', 'data privacy statement');
            $dataPrivacyStatementPage->URLSegment       = _t('SilvercartDataPrivacyStatementPage.DEFAULT_URLSEGMENT', 'data-privacy-statement');
            $dataPrivacyStatementPage->Status           = "Published";
            $dataPrivacyStatementPage->ShowInMenus      = 1;
            $dataPrivacyStatementPage->IdentifierCode   = "SilvercartDataPrivacyStatementPage";
            $dataPrivacyStatementPage->ParentID         = $metaNavigationHolder->ID;
            $dataPrivacyStatementPage->write();
            $dataPrivacyStatementPage->publish("Stage", "Live");

            //create a silvercart shipping fees page as child of the meta navigation holder
            $shippingFeesPage                   = new SilvercartShippingFeesPage();
            $shippingFeesPage->Title            = _t('SilvercartShippingFeesPage.DEFAULT_TITLE', 'shipping fees');
            $shippingFeesPage->URLSegment       = _t('SilvercartShippingFeesPage.DEFAULT_URLSEGMENT', 'shipping-fees');
            $shippingFeesPage->Status           = "Published";
            $shippingFeesPage->ShowInMenus      = 1;
            $shippingFeesPage->ParentID         = $metaNavigationHolder->ID;
            $shippingFeesPage->IdentifierCode   = "SilvercartShippingFeesPage";
            $shippingFeesPage->write();
            $shippingFeesPage->publish("Stage", "Live");

            //create a silvercart shipping fees page as child of the meta navigation holder
            $paymentMethodsPage                 = new SilvercartPaymentMethodsPage();
            $paymentMethodsPage->Title          = _t('SilvercartPaymentMethodsPage.DEFAULT_TITLE',      'Payment methods');
            $paymentMethodsPage->URLSegment     = _t('SilvercartPaymentMethodsPage.DEFAULT_URLSEGMENT', 'payment-methods');
            $paymentMethodsPage->Status         = "Published";
            $paymentMethodsPage->ShowInMenus    = 1;
            $paymentMethodsPage->ParentID       = $metaNavigationHolder->ID;
            $paymentMethodsPage->IdentifierCode = "SilvercartPaymentMethodsPage";
            $paymentMethodsPage->write();
            $paymentMethodsPage->publish("Stage", "Live");

            //create a contact form response page
            $contactFormResponsePage = new SilvercartContactFormResponsePage();
            $contactFormResponsePage->Title             = _t('SilvercartContactFormResponsePage.DEFAULT_TITLE', 'contact confirmation');
            $contactFormResponsePage->URLSegment        = _t('SilvercartContactFormResponsePage.DEFAULT_URLSEGMENT', 'contactconfirmation');
            $contactFormResponsePage->Status            = "Published";
            $contactFormResponsePage->ShowInMenus       = false;
            $contactFormResponsePage->ShowInSearch      = false;
            $contactFormResponsePage->IdentifierCode    = "SilvercartContactFormResponsePage";
            $contactFormResponsePage->ParentID          = $contactPage->ID;
            $contactFormResponsePage->Content           = _t('SilvercartContactFormResponsePage.DEFAULT_CONTENT', 'Many thanks for Your message. Your request will be answered as soon as possible.');
            $contactFormResponsePage->write();
            $contactFormResponsePage->publish("Stage", "Live");

            //create a silvercart order confirmation page as a child of the silvercart root
            $orderConfirmationPage                  = new SilvercartOrderConfirmationPage();
            $orderConfirmationPage->Title           = _t('SilvercartOrderConfirmationPage.DEFAULT_TITLE', 'order conirmation page');
            $orderConfirmationPage->URLSegment      = _t('SilvercartOrderConfirmationPage.DEFAULT_URLSEGMENT', 'order-conirmation');
            $orderConfirmationPage->Status          = "Published";
            $orderConfirmationPage->ShowInMenus     = false;
            $orderConfirmationPage->ShowInSearch    = false;
            $orderConfirmationPage->CanViewType     = "LoggedInUsers";
            $orderConfirmationPage->IdentifierCode  = "SilvercartOrderConfirmationPage";
            $orderConfirmationPage->ParentID        = $rootPage->ID;
            $orderConfirmationPage->write();
            $orderConfirmationPage->publish("Stage", "Live");

            //create a payment notification page as a child of the silvercart root
            $paymentNotification                    = new SilvercartPaymentNotification();
            $paymentNotification->Title             = _t('SilvercartPaymentNotification.DEFAULT_TITLE', 'payment notification');
            $paymentNotification->URLSegment        = _t('SilvercartPaymentNotification.DEFAULT_URLSEGMENT', 'payment-notification');
            $paymentNotification->Status            = 'Published';
            $paymentNotification->ShowInMenus       = 0;
            $paymentNotification->ShowInSearch      = 0;
            $paymentNotification->ParentID          = $rootPage->ID;
            $paymentNotification->IdentifierCode    = "SilvercartPaymentNotification";
            $paymentNotification->write();
            $paymentNotification->publish('Stage', 'Live');
            DB::alteration_message('SilvercartPaymentNotification Page created', 'created');

            //create a silvercart registration page as a child of silvercart root
            $registrationPage                   = new SilvercartRegistrationPage();
            $registrationPage->Title            = _t('SilvercartRegistrationPage.DEFAULT_TITLE', 'registration page');
            $registrationPage->URLSegment       = _t('SilvercartRegistrationPage.DEFAULT_URLSEGMENT', 'registration');
            $registrationPage->Status           = "Published";
            $registrationPage->ShowInMenus      = false;
            $registrationPage->ShowInSearch     = true;
            $registrationPage->ParentID         = $rootPage->ID;
            $registrationPage->IdentifierCode   = "SilvercartRegistrationPage";
            $registrationPage->write();
            $registrationPage->publish("Stage", "Live");

            //create a silvercart registration confirmation page as a child the silvercart registration page
            $registerConfirmationPage                   = new SilvercartRegisterConfirmationPage();
            $registerConfirmationPage->Title            = _t('SilvercartRegisterConfirmationPage.DEFAULT_TITLE', 'register confirmation page');
            $registerConfirmationPage->URLSegment       = _t('SilvercartRegisterConfirmationPage.DEFAULT_URLSEGMENT', 'register-confirmation');
            $registerConfirmationPage->Content          = _t('SilvercartRegisterConfirmationPage.DEFAULT_CONTENT');
            $registerConfirmationPage->Status           = "Published";
            $registerConfirmationPage->ParentID         = $registrationPage->ID;
            $registerConfirmationPage->ShowInMenus      = false;
            $registerConfirmationPage->ShowInSearch     = false;
            $registerConfirmationPage->IdentifierCode   = "SilvercartRegisterConfirmationPage";
            $registerConfirmationPage->write();
            $registerConfirmationPage->publish("Stage", "Live");

            //create a silvercart search results page as a child of the silvercart root
            $searchResultsPage                  = new SilvercartSearchResultsPage();
            $searchResultsPage->Title           = _t('SilvercartSearchResultsPage.DEFAULT_TITLE', 'search results');
            $searchResultsPage->URLSegment      = _t('SilvercartSearchResultsPage.DEFAULT_URLSEGMENT', 'search-results');
            $searchResultsPage->Status          = "Published";
            $searchResultsPage->ShowInMenus     = false;
            $searchResultsPage->ShowInSearch    = false;
            $searchResultsPage->ParentID        = $rootPage->ID;
            $searchResultsPage->IdentifierCode  = "SilvercartSearchResultsPage";
            $searchResultsPage->write();
            $searchResultsPage->publish("Stage", "Live");

            // Create a SilvercartNewsletterPage as a child of the Silvercart root node.
            $newsletterPage                 = new SilvercartNewsletterPage();
            $newsletterPage->Title          = _t('SilvercartNewsletterPage.DEFAULT_TITLE', 'Newsletter');
            $newsletterPage->URLSegment     = _t('SilvercartNewsletterPage.DEFAULT_URLSEGMENT', 'newsletter');
            $newsletterPage->Status         = "Published";
            $newsletterPage->ShowInMenus    = true;
            $newsletterPage->ShowInSearch   = true;
            $newsletterPage->ParentID       = $metaNavigationHolder->ID;
            $newsletterPage->IdentifierCode = "SilvercartNewsletterPage";
            $newsletterPage->write();
            $newsletterPage->publish("Stage", "Live");

            // Create a SilvercartNewsletterResponsePage as a child of the SilvercartNewsletterPage node.
            $newsletterResponsePage                 = new SilvercartNewsletterResponsePage();
            $newsletterResponsePage->Title          = _t('SilvercartNewsletterResponsePage.DEFAULT_TITLE', 'Newsletter Status');
            $newsletterResponsePage->URLSegment     = _t('SilvercartNewsletterResponsePage.DEFAULT_URLSEGMENT', 'newsletter_status');
            $newsletterResponsePage->Status         = "Published";
            $newsletterResponsePage->ShowInMenus    = false;
            $newsletterResponsePage->ShowInSearch   = false;
            $newsletterResponsePage->ParentID       = $newsletterPage->ID;
            $newsletterResponsePage->IdentifierCode = "SilvercartNewsletterResponsePage";
            $newsletterResponsePage->write();
            $newsletterResponsePage->publish("Stage", "Live");
            
            //create a silvercart newsletter opt-in confirmation page
            $newsletterOptInConfirmationPage                                = new SilvercartNewsletterOptInConfirmationPage();
            $newsletterOptInConfirmationPage->Title                         = _t('SilvercartNewsletterOptInConfirmationPage.DEFAULT_TITLE', 'register confirmation page');
            $newsletterOptInConfirmationPage->URLSegment                    = _t('SilvercartNewsletterOptInConfirmationPage.DEFAULT_URLSEGMENT', 'register-confirmation');
            $newsletterOptInConfirmationPage->Content                       = _t('SilvercartNewsletterOptInConfirmationPage.DEFAULT_CONTENT');
            $newsletterOptInConfirmationPage->ConfirmationFailureMessage    = _t('SilvercartNewsletterOptInConfirmationPage.DEFAULT_CONFIRMATIONFAILUREMESSAGE');
            $newsletterOptInConfirmationPage->ConfirmationSuccessMessage    = _t('SilvercartNewsletterOptInConfirmationPage.DEFAULT_CONFIRMATIONSUCCESSMESSAGE');
            $newsletterOptInConfirmationPage->AlreadyConfirmedMessage       = _t('SilvercartNewsletterOptInConfirmationPage.DEFAULT_ALREADYCONFIRMEDMESSAGE');
            $newsletterOptInConfirmationPage->Status                        = "Published";
            $newsletterOptInConfirmationPage->ParentID                      = $newsletterPage->ID;
            $newsletterOptInConfirmationPage->ShowInMenus                   = false;
            $newsletterOptInConfirmationPage->ShowInSearch                  = false;
            $newsletterOptInConfirmationPage->IdentifierCode                = "SilvercartNewsletterOptInConfirmationPage";
            $newsletterOptInConfirmationPage->write();
            $newsletterOptInConfirmationPage->publish("Stage", "Live");
        }
        return $rootPage;
    }
    
    /**
     * Creates the default SilvercartShopEmails if not exists
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function createDefaultShopEmails() {
        $shopEmailRegistrationOptIn = DataObject::get_one(
                        'SilvercartShopEmail',
                        "Identifier = 'RegistrationOptIn'"
        );
        if (!$shopEmailRegistrationOptIn) {
            $shopEmailRegistrationOptIn = new SilvercartShopEmail();
            $shopEmailRegistrationOptIn->setField('Identifier', 'RegistrationOptIn');
            $shopEmailRegistrationOptIn->setField('Subject', _t('SilvercartRegistrationPage.PLEASE_COFIRM', 'please confirm Your registration'));
            $shopEmailRegistrationOptIn->setField('EmailText', _t('SilvercartRegistrationPage.CONFIRMATION_TEXT', '<h1>Complete registration</h1><p>Please confirm Your activation or copy the link to Your Browser.</p><p><a href="$ConfirmationLink">Confirm registration</a></p><p>In case You did not register please ignore this mail.</p><p>Your shop team</p>'));
            $shopEmailRegistrationOptIn->write();
        }
        $shopEmailRegistrationConfirmation = DataObject::get_one(
                        'SilvercartShopEmail',
                        "Identifier = 'RegistrationConfirmation'"
        );
        if (!$shopEmailRegistrationConfirmation) {
            $shopEmailRegistrationConfirmation = new SilvercartShopEmail();
            $shopEmailRegistrationConfirmation->setField('Identifier', 'RegistrationConfirmation');
            $shopEmailRegistrationConfirmation->setField('Subject', _t('SilvercartRegistrationPage.THANKS', 'Many thanks for Your registration'));
            $shopEmailRegistrationConfirmation->setField('EmailText', _t('SilvercartRegistrationPage.SUCCESS_TEXT', '<h1>Registration completed successfully!</h1><p>Many thanks for Your registration.</p><p>Have a nice time on our website!</p><p>Your webshop team</p>'));
            $shopEmailRegistrationConfirmation->write();
        }
        $checkOrderMail = DataObject::get_one(
            'SilvercartShopEmail',
            "`Identifier` = 'MailOrderConfirmation'"
        );
        if (!$checkOrderMail) {
            $orderMail = new SilvercartShopEmail();
            $orderMail->setField('Identifier',   'MailOrderConfirmation');
            $orderMail->setField('Subject', _t('SilvercartShopEmail.ORDER_ARRIVED_EMAIL_SUBJECT'));
            $orderMail->setField('Variables',    "\$FirstName\n\$Surname\n\$Salutation\n\$Order");
            $defaultTemplateFile = Director::baseFolder() . '/silvercart/templates/email/SilvercartMailOrderConfirmation.ss';
            if (is_file($defaultTemplateFile)) {
                $defaultTemplate = file_get_contents($defaultTemplateFile);
                $defaultTemplate = SilvercartShopEmail::parse($defaultTemplate);
            } else {
                $defaultTemplate = '';
            }
            $orderMail->setField('EmailText',    $defaultTemplate);
            $orderMail->write();
        }
        $checkOrderMail = DataObject::get_one(
            'SilvercartShopEmail',
            "`Identifier` = 'MailOrderNotification'"
        );
        if (!$checkOrderMail) {
            $orderMail = new SilvercartShopEmail();
            $orderMail->setField('Identifier',   'MailOrderNotification');
            $orderMail->setField('Subject', _t('SilvercartShopEmail.NEW_ORDER_PLACED'));
            $orderMail->setField('Variables',    "\$FirstName\n\$Surname\n\$Salutation\n\$Order");
            $defaultTemplateFile = Director::baseFolder() . '/silvercart/templates/email/SilvercartMailOrderNotification.ss';
            if (is_file($defaultTemplateFile)) {
                $defaultTemplate = file_get_contents($defaultTemplateFile);
                $defaultTemplate = SilvercartShopEmail::parse($defaultTemplate);
            } else {
                $defaultTemplate = '';
            }
            $orderMail->setField('EmailText',    $defaultTemplate);
            $orderMail->write();
        }
        $contactEmail = DataObject::get_one(
            'SilvercartShopEmail',
            "`Identifier` = 'ContactMessage'"
        );
        if (!$contactEmail) {
            $contactEmail = new SilvercartShopEmail();
            $contactEmail->setField('Identifier',   'ContactMessage');
            $contactEmail->setField('Subject',      _t('SilvercartContactFormPage.REQUEST', 'request via contact form'));
            $contactEmail->setField('Variables',    "\$FirstName\n\$Surname\n\$Email\n\$Message");
            $contactEmail->setField('EmailText',    _t('SilvercartContactMessage.TEXT'));
            $contactEmail->write();
        }
        $shopEmailNewsletterOptIn = DataObject::get_one(
            'SilvercartShopEmail',
            "Identifier = 'NewsletterOptIn'"
        );
        if (!$shopEmailNewsletterOptIn) {
            $shopEmailNewsletterOptIn = new SilvercartShopEmail();
            $shopEmailNewsletterOptIn->setField('Identifier', 'NewsletterOptIn');
            $shopEmailNewsletterOptIn->setField('Subject', _t('SilvercartNewsletterOptInConfirmationPage.TITLE'));
            $shopEmailNewsletterOptIn->setField('EmailText', _t('SilvercartNewsletterOptInConfirmationPage.EMAIL_CONFIRMATION_TEXT'));
            $shopEmailNewsletterOptIn->write();
        }
        $shopEmailNewsletterOptInConfirmation = DataObject::get_one(
            'SilvercartShopEmail',
            "Identifier = 'NewsletterOptInConfirmation'"
        );
        if (!$shopEmailNewsletterOptInConfirmation) {
            $shopEmailNewsletterOptInConfirmation = new SilvercartShopEmail();
            $shopEmailNewsletterOptInConfirmation->setField('Identifier', 'NewsletterOptInConfirmation');
            $shopEmailNewsletterOptInConfirmation->setField('Subject', _t('SilvercartNewsletterOptInConfirmationPage.TITLE_THANKS'));
            $shopEmailNewsletterOptInConfirmation->setField('EmailText', _t('SilvercartNewsletterOptInConfirmationPage.CONFIRMATIONSUCCESSMESSAGE'));
            $shopEmailNewsletterOptInConfirmation->write();
        }
        $shopEmailOrderShippedNotification = DataObject::get_one(
            'SilvercartShopEmail',
            "Identifier = 'OrderShippedNotification'"
        );
        if (!$shopEmailOrderShippedNotification) {
            $shopEmailOrderShippedNotification = new SilvercartShopEmail();
            $shopEmailOrderShippedNotification->setField('Identifier', 'OrderShippedNotification');
            $shopEmailOrderShippedNotification->setField('Subject',     _t('SilvercartShopEmail.ORDER_SHIPPED_NOTIFICATION_SUBJECT'));
            $shopEmailOrderShippedNotification->setField('Variables',   "\$FirstName\n\$Surname\n\$Salutation\n\$SilvercartOrder");
            $defaultTemplateFile = Director::baseFolder() . '/silvercart/templates/email/SilvercartMailOrderShippedNotification.ss';
            if (is_file($defaultTemplateFile)) {
                $defaultTemplate = file_get_contents($defaultTemplateFile);
                $defaultTemplate = SilvercartShopEmail::parse($defaultTemplate);
            } else {
                $defaultTemplate = '';
            }
            $shopEmailOrderShippedNotification->setField('EmailText',    $defaultTemplate);
            $shopEmailOrderShippedNotification->write();
        }
        
        // attribute ShopEmails to order status
        $orderStatus = DataObject::get_one('SilvercartOrderStatus', "Code='shipped'");
        $orderEmail  = DataObject::get_one('SilvercartShopEmail', "Identifier='OrderShippedNotification'");
        
        if ($orderStatus && $orderEmail) {
            $orderStatus->SilvercartShopEmails()->add($orderEmail);
        }
    }

    /**
     * create default records.
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function requireDefaultRecords() {
        SilvercartLanguageHelper::createSilvercartCacheModule();
        SilvercartLanguageHelper::createMissingLocales();

        parent::requireDefaultRecords();
        // create groups
        $this->createDefaultGroups();
        // create config
        $this->createDefaultConfig();
        // create countries
        $this->requireOrUpdateCountries();
        // create order status
        $this->createDefaultOrderStatus();
        // create availability status
        $this->createDefaultAvailabilityStatus();
        // create number ranges
        $this->createDefaultNumberRanges();
        // and now the whole site tree
        $rootPage = $this->createDefaultSiteTree();
        // create shop emails
        $this->createDefaultShopEmails();

        $this->extend('updateDefaultRecords', $rootPage);

        self::createTestConfiguration();
        self::createTestData();
    }
    
    /**
     * Will create a translation of all pages of the SiteTree for the defined
     * translationLocale
     *
     * @param int $parentID ID of the parent to get pages for
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.05.2012
     */
    public function translateSiteTree($parentID = 0) {
        $translatableFieldTypes = array(
            'varchar',
            'htmltext',
            'text',
        );
        
        $translationLocale = $this->getTranslationLocale();
        $pages = DataObject::get(
                'SiteTree',
                sprintf(
                    "`ParentID` = '%s'",
                    $parentID
                )
        );
        if ($pages) {
            foreach ($pages as $page) {
                if (!$page->getTranslation($translationLocale)) {
                    $translation = $page->createTranslation($translationLocale);
                    
                    foreach ($translation->db() as $name => $type) {
                        $isTranslatable = false;
                        foreach ($translatableFieldTypes as $translatableFieldType) {
                            if (strpos(strtolower($type), $translatableFieldType) === 0) {
                                $isTranslatable = true;
                                break;
                            }
                        }
                        if ($isTranslatable) {
                            i18n::set_locale($translationLocale);
                            $translation->{$name} = _t($translation->ClassName . '.DEFAULT_' . strtoupper($name), _t($translation->IdentifierCode . '.DEFAULT_' . strtoupper($name), $translation->{$name}));
                        }
                    }
                    $translation->write();
                }
                $this->translateSiteTree($page->ID);
            }
        }
    }
    
    /**
     * Will publish all pages of the SiteTree for the defined translationLocale
     *
     * @param int $parentID ID of the parent to get pages for
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.05.2012
     */
    public function publishSiteTree($parentID = 0) {
        $translationLocale = $this->getTranslationLocale();
        $pages = DataObject::get(
                'SiteTree',
                sprintf(
                    "`ParentID` = '%s' AND `Locale` = '%s'",
                    $parentID,
                    $translationLocale
                )
        );
        if ($pages) {
            foreach ($pages as $page) {
                $page->publish("Stage", "Live");
                $this->publishSiteTree($page->ID);
            }
        }
    }
    
    /**
     * Static accessor to trigger SiteTree translation
     *
     * @param string $locale Locale to translate SiteTree for
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.05.2012
     */
    public static function doTranslateSiteTree($locale) {
        $obj = new SilvercartRequireDefaultRecords();
        $obj->requireDefaultTranslations($locale);
    }
    
    /**
     * Static accessor to trigger SiteTree publishing
     *
     * @param string $locale Locale to publish SiteTree for
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.05.2012
     */
    public static function doPublishSiteTree($locale) {
        $obj = new SilvercartRequireDefaultRecords();
        $obj->setTranslationLocale($locale);
        $obj->publishSiteTree();
    }

    /**
     * create default records dependant on the given locale.
     * 
     * @param string $locale The locale to get records for
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function requireDefaultTranslations($locale) {
        $originalLocale = i18n::get_locale();
        i18n::set_locale($locale);
        $this->setTranslationLocale($locale);
        // create order status
        $this->createDefaultOrderStatus();
        // create availability status
        $this->createDefaultAvailabilityStatus();
        // and now the whole site tree
        $this->translateSiteTree();
        // create shop emails
        //$this->translateShopEmails();
        i18n::set_locale($originalLocale);
    }

    /**
     * Requires all default countries or syncs them if GeoNames is activated.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.05.2012
     */
    public function requireOrUpdateCountries() {
        $config = SilvercartConfig::getConfig();
        if ($config->GeoNamesActive) {
            $geoNames = new SilvercartGeoNames($config->GeoNamesUserName, $config->GeoNamesAPI);
            $geoNames->countryInfo();
        } elseif (!DataObject::get('SilvercartCountry')) {
            require_once(Director::baseFolder() . '/silvercart/code/config/SilvercartRequireDefaultCountries.php');
        }
        if (!DataObject::get_one('SilvercartCountry', "`Active`=1")) {
            $country = DataObject::get_one('SilvercartCountry', sprintf("`ISO2`='%s'", substr(i18n::get_locale(), 3)));
            if ($country) {
                $country->Active = true;
                $country->write();
            }
        }
    }

    /**
     * enables the creation of test data on /dev/build
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 21.02.2011
     */
    public static function enableTestData() {
        self::$enableTestData = true;
    }

    /**
     * determine weather test data is enabled or not
     *
     * @return bool is test data enabled?
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.3.2011
     */
    public static function isEnabledTestData() {
        if (self::$enableTestData === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * disables the creation of test data on /dev/build. This is set by default,
     * so you do not have to disable creation of test data if it was not enabled
     * before.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 21.02.2011
     */
    public static function disableTestData() {
        self::$enableTestData = false;
    }

    /**
     * creates test data on /dev/build or by adding test data in ModelAdmin.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.07.2011
     */
    public static function createTestData() {
        if (self::$enableTestData === true) {
            if (SiteTree::get_by_link('testgroup1')) {
                // test data already created
                return false;
            }
            self::createTestTaxRates();
            // get SilvercartProductGroupHolder and tax rate
            $silvercartProductGroupHolder = DataObject::get_one('SilvercartProductGroupHolder');
            $taxRateID = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;

            //create a manufacturer
            $manufacturer = new SilvercartManufacturer();
            $manufacturer->Title = 'Pixeltricks GmbH';
            $manufacturer->URL = 'http://www.pixeltricks.de/';
            $manufacturer->write();
            
            //create product groups
            $productGroupPayment = new SilvercartProductGroupPage();
            $productGroupPayment->Title = _t('SilvercartTestData.PRODUCTGROUPPAYMENT_TITLE');
            $productGroupPayment->URLSegment = _t('SilvercartTestData.PRODUCTGROUPPAYMENT_URLSEGMENT');
            $productGroupPayment->Content = _t('SilvercartTestData.PRODUCTGROUP_CONTENT');
            $productGroupPayment->Status = "Published";
            $productGroupPayment->IdentifierCode = 'SilvercartProductGroupPayment';
            $productGroupPayment->ParentID = $silvercartProductGroupHolder->ID;
            $productGroupPayment->ShowInMenus = true;
            $productGroupPayment->ShowInSearch = true;
            $productGroupPayment->Sort = 1;
            $productGroupPayment->write();
            $productGroupPayment->publish("Stage", "Live");
            
            $productGroupMarketing = new SilvercartProductGroupPage();
            $productGroupMarketing->Title = _t('SilvercartTestData.PRODUCTGROUPMARKETING_TITLE');
            $productGroupMarketing->URLSegment = _t('SilvercartTestData.PRODUCTGROUPMARKETING_URLSEGMENT');
            $productGroupMarketing->Content = _t('SilvercartTestData.PRODUCTGROUP_CONTENT');
            $productGroupMarketing->Status = "Published";
            $productGroupMarketing->IdentifierCode = 'SilvercartproductGroupMarketing';
            $productGroupMarketing->ParentID = $silvercartProductGroupHolder->ID;
            $productGroupMarketing->ShowInMenus = true;
            $productGroupMarketing->ShowInSearch = true;
            $productGroupMarketing->Sort = 2;
            $productGroupMarketing->write();
            $productGroupMarketing->publish("Stage", "Live");
            
            $productGroupOthers = new SilvercartProductGroupPage();
            $productGroupOthers->Title = _t('SilvercartTestData.PRODUCTGROUPOTHERS_TITLE');
            $productGroupOthers->URLSegment = _t('SilvercartTestData.PRODUCTGROUPOTHERS_URLSEGMENT');
            $productGroupOthers->Content = _t('SilvercartTestData.PRODUCTGROUP_CONTENT');
            $productGroupOthers->Status = "Published";
            $productGroupOthers->IdentifierCode = 'SilvercartproductGroupOthers';
            $productGroupOthers->ParentID = $silvercartProductGroupHolder->ID;
            $productGroupOthers->ShowInMenus = true;
            $productGroupOthers->ShowInSearch = true;
            $productGroupOthers->Sort = 3;
            $productGroupOthers->write();
            $productGroupOthers->publish("Stage", "Live");
            
            // Define products
            $products = array(
                array(
                    'en_US'                     => array(
                        'Title'            => 'Paypal',
                        'ShortDescription' => 'The world' . "'" . 's most loved way to pay and get paid.',
                        'LongDescription'  => 'PayPal works behind the scenes to help protect you and your customers. Your customers will love the speed of PayPal streamlined checkout experience. And you will love the sales boost PayPal can deliver. PayPal is ideal for selling overseas. You can accept payments in 22 currencies from 190 countries and markets worldwide. Source: www.paypal.com',
                        'MetaDescription'  => 'The world' . "'" . 's most loved way to pay and get paid.',
                        'MetaKeywords'     => 'SilverCart, modules, PayPal, payment',
                        'MetaTitle'        => 'Paypal'
                    ),
                    'en_GB' => array(
                        'Title'            => 'Paypal',
                        'ShortDescription' => 'The world' . "'" . 's most loved way to pay and get paid.',
                        'LongDescription'  => 'PayPal works behind the scenes to help protect you and your customers. Your customers will love the speed of PayPal streamlined checkout experience. And you will love the sales boost PayPal can deliver. PayPal is ideal for selling overseas. You can accept payments in 22 currencies from 190 countries and markets worldwide. Source: www.paypal.com',
                        'MetaDescription'  => 'The world' . "'" . 's most loved way to pay and get paid.',
                        'MetaKeywords'     => 'SilverCart, modules, PayPal, payment',
                        'MetaTitle'        => 'Paypal'
                    ),
                    'de_DE' => array(
                        'Title'            => 'Paypal',
                        'ShortDescription' => 'PayPal ist sicherererer. Für Daten, für Einkäufe - Für alles',
                        'LongDescription'  => 'PayPal für Ihren Shop Sie haben einen Online-Shop und fragen sich, warum Sie PayPal anbieten sollen? Ganz einfach: Ihre Kunden bezahlen mit nur zwei Klicks. Sie schließen den Kauf zufrieden ab, kommen gerne wieder - und Sie steigern Ihren Umsatz! Das kann PayPal für Sie tun – und mehr!',
                        'MetaDescription'  => 'PayPal ist sicherererer. Für Daten, für Einkäufe - Für alles',
                        'MetaKeywords'     => 'SilverCart, Modul, PayPal, Zahlart',
                        'MetaTitle'        => 'Paypal'
                    ),
                    'PriceGrossAmount'          => 9.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 9.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 9.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 9.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'Weight'                    => 250,
                    'StockQuantity'             => 5,
                    'ProductNumberShop'         => '10001',
                    'ProductNumberManufacturer' => 'SC_Mod_100',
                    'SilvercartProductGroupID'  => $productGroupPayment->ID,
                    'productImage'              => 'logopaypal.jpg',
                    'sortOrder'                 => 1
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'iPayment',
                        'ShortDescription' => 'iPayment is one of the largest providers of credit and debit card-based payment processing services in the country, processing more than $30 billion in credit and debit card volume annually.',
                        'LongDescription'  => '<p>Receive best in class service no matter what size your business is, with iPayment. We’re committed to making your business more successful by delivering credit and debit card-based payment processing services that are customized to suit your needs.</p><ul><li>Major credit cards: MasterCard®, Visa®, American Express®, Discover® and JCB®</li><li>PIN-secured and signature debit cards</li><li>Gift and loyalty cards</li><li>Petroleum services</li><li>Paper and electronic check services</li><li>Cash advance funding program</li></ul><p><small>Source: www.ipaymentinc.com/</small></p>',
                        'MetaDescription'  => 'iPayment is one of the largest providers of credit and debit card-based payment processing services in the country, processing more than $30 billion in credit and debit card volume annually.',
                        'MetaKeywords'     => 'SilverCart, modules, iPayment, payment',
                        'MetaTitle'        => 'iPayment'
                    ),
                    'en_GB' => array(
                        'Title'            => 'iPayment',
                        'ShortDescription' => 'iPayment is one of the largest providers of credit and debit card-based payment processing services in the country, processing more than $30 billion in credit and debit card volume annually.',
                        'LongDescription'  => '<p>Receive best in class service no matter what size your business is, with iPayment. We’re committed to making your business more successful by delivering credit and debit card-based payment processing services that are customized to suit your needs.</p><ul><li>Major credit cards: MasterCard®, Visa®, American Express®, Discover® and JCB®</li><li>PIN-secured and signature debit cards</li><li>Gift and loyalty cards</li><li>Petroleum services</li><li>Paper and electronic check services</li><li>Cash advance funding program</li></ul><p><small>Source: www.ipaymentinc.com/</small></p>',
                        'MetaDescription'  => 'iPayment is one of the largest providers of credit and debit card-based payment processing services in the country, processing more than $30 billion in credit and debit card volume annually.',
                        'MetaKeywords'     => 'SilverCart, modules, iPayment, payment',
                        'MetaTitle'        => 'iPayment'
                    ),
                    'de_DE' => array(
                        'Title'            => 'iPayment',
                        'ShortDescription' => 'iPayment unterstützt Ihren Geschäftserfolg im Internet, indem es Ihren Kunden die sichere Bezahlung per Kreditkarte, internetbasiertem elektronischen Lastschriftverfahren und weiteren Zahlungsmedien ermöglicht.',
                        'LongDescription'  => 'ipayment unterstützt Ihren Geschäftserfolg im Internet, indem es Ihren Kunden die sichere Bezahlung per Kreditkarte, internetbasiertem elektronischen Lastschriftverfahren und weiteren Zahlungsmedien ermöglicht. Je nach genutztem Zahlungsanbieter können Sie Ihren Kunden über ipayment die Bezahlung mit folgenden Zahlungsmedien anbieten: Visa MasterCard Maestro American Express JCB Diners Club Visa Electron Solo Internetbasiertes Elektronisches Lastschriftverfahren (ELV) paysafecard Das Unternehmen, über das Sie Ihre Onlinezahlungen abwickeln möchten, können Sie dabei selbst auswählen - ipayment verfügt über Schnittstellen zu den wichtigsten Zahlungsanbietern. Sie schließen den Akzeptanzvertrag mit dem Anbieter Ihrer Wahl - ipayment sorgt für die reibungslose und sichere Abwicklung! Dazu nimmt ipayment die Zahlungsvorgänge direkt aus Ihrem System auf und verarbeitet sie im Hochleistungsrechenzentrum von 1&1 in Karlsruhe. Selbstverständlich erfüllt ipayment dabei die Zertifizierungsanforderungen gemäß dem PCI DSS (Payment Card Industry Data Security Standard). ',
                        'MetaDescription'  => 'iPayment unterstützt Ihren Geschäftserfolg im Internet, indem es Ihren Kunden die sichere Bezahlung per Kreditkarte, internetbasiertem elektronischen Lastschriftverfahren und weiteren Zahlungsmedien ermöglicht.',
                        'MetaKeywords'     => 'SilverCart, Module, iPayment, Zahlart',
                        'MetaTitle'        => 'iPayment'
                    ),
                    'PriceGrossAmount'          => 18.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 18.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 18.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 18.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'Weight'                    => 260,
                    'StockQuantity'             => 3,
                    'ProductNumberShop'         => '10002',
                    'ProductNumberManufacturer' => 'SC_Mod_101',
                    'SilvercartProductGroupID'  => $productGroupPayment->ID,
                    'productImage'              => 'logoipayment.gif',
                    'sortOrder'                 => 2
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'Saferpay',
                        'ShortDescription' => 'Saferpay has set the standard for e-payment solutions in German-speaking Europe.',
                        'LongDescription'  => '<h3>Saferpay e-payment solutions for professionals and beginners</h3><p>Saferpay integrates all popular payment means in your Web shop through a single interface. This makes it easy to make adaptations and upgrades. What’s more, Saferpay enables the secure online processing of written and phone orders.</p><h3>More payment means – more turnover!</h3><p>Boost your turnover by offering a variety of payment means! With Saferpay you can offer your customers all popular payment means through a single interface, flexibly, easily & securely! You can accept all popular credit cards and debit cards with Saferpay and can activate new payment means at any time or deactivate existing ones and thus can flexibly react to your e-commerce requirements.</p><h3>More profit with security!</h3><p>SIX Card Solutions offers you comprehensive solutions from a single source to handle cashless, electronic payment processing as a merchant in e-commerce or in the phone/mail-order business as securely and conveniently as possible. The e-payment solution supports all current security standards. Increase confidence among your customers!</p>',
                        'MetaDescription'  => 'Saferpay has set the standard for e-payment solutions in German-speaking Europe.',
                        'MetaKeywords'     => 'SilverCart, modules, Saferpay, payment',
                        'MetaTitle'        => 'Saferpay'
                    ),
                    'en_GB' => array(
                        'Title'            => 'Saferpay',
                        'ShortDescription' => 'Saferpay has set the standard for e-payment solutions in German-speaking Europe.',
                        'LongDescription'  => '<h3>Saferpay e-payment solutions for professionals and beginners</h3><p>Saferpay integrates all popular payment means in your Web shop through a single interface. This makes it easy to make adaptations and upgrades. What’s more, Saferpay enables the secure online processing of written and phone orders.</p><h3>More payment means – more turnover!</h3><p>Boost your turnover by offering a variety of payment means! With Saferpay you can offer your customers all popular payment means through a single interface, flexibly, easily & securely! You can accept all popular credit cards and debit cards with Saferpay and can activate new payment means at any time or deactivate existing ones and thus can flexibly react to your e-commerce requirements.</p><h3>More profit with security!</h3><p>SIX Card Solutions offers you comprehensive solutions from a single source to handle cashless, electronic payment processing as a merchant in e-commerce or in the phone/mail-order business as securely and conveniently as possible. The e-payment solution supports all current security standards. Increase confidence among your customers!</p>',
                        'MetaDescription'  => 'Saferpay has set the standard for e-payment solutions in German-speaking Europe.',
                        'MetaKeywords'     => 'SilverCart, modules, Saferpay, payment',
                        'MetaTitle'        => 'Saferpay'
                    ),
                    'de_DE' => array(
                        'Title'            => 'Saferpay',
                        'ShortDescription' => 'Saferpay hat im deutschsprachigen Europa den Standard für E-Payment-Lösungen gesetzt und steht damit als Synonym für "sicheres Bezahlen im Internet."',
                        'LongDescription'  => '<h3>Saferpay E-Payment-Lösungen für Profis und Einsteiger</h3><p>Saferpay hat im deutschsprachigen Europa den Standard für E-Payment-Lösungen gesetzt und steht damit als Synonym für "sicheres Bezahlen im Internet." Dank Saferpay müssen sich Online-Händler wie Karteninhaber über die Sicherheit beim Einkaufen im Internet keine Sorgen mehr machen. Händler kennen und schätzen das sichere Bezahlen im Internet über Saferpay weltweit.</p><p>Saferpay integriert alle gängigen Zahlungsmittel in Ihren Webshop - über eine einzige Schnittstelle. Dadurch sind Anpassungen und Erweiterungen problemlos umsetzbar. Darüber hinaus ermöglicht Saferpay die sichere Onlineabwicklung von schriftlichen und telefonischen Bestellungen.</p><h3>Mehr Zahlungsmittel – mehr Umsatz!</h3><p>Steigern Sie Ihren Umsatz durch das Angebot einer Vielzahl an Zahlungsmitteln! Mit Saferpay bieten Sie Ihren Kunden alle gängigen Zahlungsmittel über eine einzige Schnittstelle – flexibel, einfach & sicher! Mit Saferpay können Sie alle gängigen Kreditkarten und Debitkarten akzeptieren. Sie können jederzeit neue Zahlungsmittel aufschalten oder bestehende wieder abschalten und somit flexibel auf die Bedürfnisse im E-Commerce reagieren.</p><h3>Mit Sicherheit mehr Gewinn!</h3><p>Um die bargeldlose, elektronische Zahlungsabwicklung für Sie als Händler im E-Commerce oder Phone-/Mail-Order Business so sicher und bequem wie möglich zu machen, bietet die SIX Card Solutions Ihnen als Händler Komplettlösungen aus einer Hand. Die E-Payment-Lösung unterstützt alle heutigen Sicherheitsstandards. Stärken Sie das Vertrauen Ihrer Kunden !</p>',
                        'MetaDescription'  => 'Saferpay hat im deutschsprachigen Europa den Standard für E-Payment-Lösungen gesetzt und steht damit als Synonym für "sicheres Bezahlen im Internet."',
                        'MetaKeywords'     => 'SilverCart, Module, Saferpay, Zahlart',
                        'MetaTitle'        => 'Saferpay'
                    ),
                    'PriceGrossAmount'          => 36.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 36.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 36.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 36.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'Weight'                    => 270,
                    'StockQuantity'             => 12,
                    'ProductNumberShop'         => '10003',
                    'ProductNumberManufacturer' => 'SC_Mod_102',
                    'SilvercartProductGroupID'  => $productGroupPayment->ID,
                    'productImage'              => 'logosaferpay.jpg',
                    'sortOrder'                 => 3
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'Prepayment',
                        'ShortDescription' => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic.',
                        'LongDescription'  => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic. This module provides beside prepayment also payment via invoice.',
                        'MetaDescription'  => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic.',
                        'MetaKeywords'     => 'SilverCart, modules, Prepayment, payment',
                        'MetaTitle'        => 'Prepayment'
                    ),
                    'en_GB' => array(
                        'Title'            => 'Prepayment',
                        'ShortDescription' => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic.',
                        'LongDescription'  => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic. This module provides beside prepayment also payment via invoice.',
                        'MetaDescription'  => 'Flexible payment system for all payment systems which don' . "'" . 't need any automated logic.',
                        'MetaKeywords'     => 'SilverCart, modules, Prepayment, payment',
                        'MetaTitle'        => 'Prepayment'
                    ),
                    'de_DE' => array(
                        'Title'            => 'Vorkasse',
                        'ShortDescription' => 'Flexibles Zahlungs-Modul für alle Zahlungsarten, die keine automatisierte Logik erfordern.',
                        'LongDescription'  => 'Flexibles Zahlungs-Modul für alle Zahlungsarten, die keine automatisierte Logik erfordern. Dieses Modul bietet neben der Vorkasse auch Rechnung als Zahlungsart.',
                        'MetaDescription'  => 'Flexibles Zahlungs-Modul für alle Zahlungsarten, die keine automatisierte Logik erfordern.',
                        'MetaKeywords'     => 'SilverCart, Module, Prepayment, Zahlart',
                        'MetaTitle'        => 'Vorkasse'
                    ),
                    'PriceGrossAmount'          => 27.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 27.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 27.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 27.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'Weight'                    => 290,
                    'StockQuantity'             => 9,
                    'ProductNumberShop'         => '10004',
                    'ProductNumberManufacturer' => 'SC_Mod_103',
                    'SilvercartProductGroupID'  => $productGroupPayment->ID,
                    'productImage'              => 'logoprepayment.png',
                    'sortOrder'                 => 4
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'Cross selling',
                        'ShortDescription' => 'Cross selling is a practice of suggesting related products or services to a customer who is considering buying something.',
                        'LongDescription'  => 'It is a practice of suggesting related products or services to a customer who is considering buying something. Encourage established customers to buy different but related products. Getting a computer buyer to purchase a printer, for example. Source: www.procopytips.com',
                        'MetaDescription'  => 'Cross selling is a practice of suggesting related products or services to a customer who is considering buying something.',
                        'MetaKeywords'     => 'SilverCart, module, Cross selling, marketing',
                        'MetaTitle'        => 'Cross selling'
                    ),
                    'en_GB' => array(
                        'Title'            => 'Cross selling',
                        'ShortDescription' => 'Cross selling is a practice of suggesting related products or services to a customer who is considering buying something.',
                        'LongDescription'  => 'It is a practice of suggesting related products or services to a customer who is considering buying something. Encourage established customers to buy different but related products. Getting a computer buyer to purchase a printer, for example. Source: www.procopytips.com',
                        'MetaDescription'  => 'Cross selling is a practice of suggesting related products or services to a customer who is considering buying something.',
                        'MetaKeywords'     => 'SilverCart, module, Cross selling, marketing',
                        'MetaTitle'        => 'Cross selling'
                    ),
                    'de_DE' => array(
                        'Title'            => 'Cross-Selling',
                        'ShortDescription' => 'Kreuzverkauf bezeichnet im Marketing den Verkauf von sich ergänzenden Produkten oder Dienstleistungen.',
                        'LongDescription'  => 'Verkaufs- bzw. Marketinginstrument, bei dem Informationen über bereits existierende Kunden oder über bekanntes Konsumentenverhalten genutzt wird, um zusätzliche Käufe anderer Produkte zu begünstigen. Quelle: www.desig-n.de ',
                        'MetaDescription'  => 'Kreuzverkauf bezeichnet im Marketing den Verkauf von sich ergänzenden Produkten oder Dienstleistungen.',
                        'MetaKeywords'     => 'SilverCart, Modul, Cross-Selling, Marketing',
                        'MetaTitle'        => 'Cross-Selling'
                    ),
                    'PriceGrossAmount'          => 12.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 12.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 12.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 12.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'Weight'                    => 145,
                    'StockQuantity'             => 26,
                    'ProductNumberShop'         => '10006',
                    'ProductNumberManufacturer' => 'SC_Mod_104',
                    'SilvercartProductGroupID'  => $productGroupMarketing->ID,
                    'productImage'              => 'logocrossselling.png',
                    'sortOrder'                 => 1
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'eKomi',
                        'ShortDescription' => 'Increase sales with eKomi’s trusted independent customer review system!',
                        'LongDescription'  => 'eKomi – The Feedback Company, helps companies through their web-based social SaaS technology with authentic and valuable reviews from customers and helps increasing the customer satisfaction and sales. Generate valuable customer reviews with eKomi' . "'" . 's intelligent, easy to install software and increase sales, trust and customer loyalty. <small>Source: www.ekomi.co.uk</small>',
                        'MetaDescription'  => 'Increase sales with eKomi’s trusted independent customer review system!',
                        'MetaKeywords'     => 'SilverCart, module, Ekomi, marketing',
                        'MetaTitle'        => 'eKomi'
                    ),
                    'en_GB' => array(
                        'Title'            => 'eKomi',
                        'ShortDescription' => 'Increase sales with eKomi’s trusted independent customer review system!',
                        'LongDescription'  => 'eKomi – The Feedback Company, helps companies through their web-based social SaaS technology with authentic and valuable reviews from customers and helps increasing the customer satisfaction and sales. Generate valuable customer reviews with eKomi' . "'" . 's intelligent, easy to install software and increase sales, trust and customer loyalty. <small>Source: www.ekomi.co.uk</small>',
                        'MetaDescription'  => 'Increase sales with eKomi’s trusted independent customer review system!',
                        'MetaKeywords'     => 'SilverCart, module, Ekomi, marketing',
                        'MetaTitle'        => 'eKomi'
                    ),
                    'de_DE' => array(
                        'Title'            => 'eKomi',
                        'ShortDescription' => 'Mehr Umsatz und Vertrauen durch unabhängige Kunden- und Produktbewertungen!',
                        'LongDescription'  => 'Beginnen Sie noch heute, durch intelligente Kundenbefragung authentisches und wertvolles Kundenfeedback zu gewinnen und damit Ihre Kundenzufriedenheit und Ihren Umsatz zu steigern. ',
                        'MetaDescription'  => 'Mehr Umsatz und Vertrauen durch unabhängige Kunden- und Produktbewertungen!',
                        'MetaKeywords'     => 'SilverCart, Modul, Ekomi, Marketing',
                        'MetaTitle'        => 'eKomi'
                    ),
                    'Title'                     => _t('SilvercartTestData.PRODUCTMARKETINGEKOMI_TITLE'),
                    'PriceGrossAmount'          => 32.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 32.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 32.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 32.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'ShortDescription'          => _t('SilvercartTestData.PRODUCTMARKETINGEKOMI_SHORTDESC'),
                    'LongDescription'           => _t('SilvercartTestData.PRODUCTMARKETINGEKOMI_LONGDESC'),
                    'MetaDescription'           => _t('SilvercartTestData.PRODUCTMARKETINGEKOMI_SHORTDESC'),
                    'MetaTitle'                 => _t('SilvercartTestData.PRODUCTMARKETINGEKOMI_TITLE'),
                    'MetaKeywords'              => _t('SilvercartTestData.PRODUCTMARKETINGEKOMI_KEYWORDS'),
                    'Weight'                    => 345,
                    'StockQuantity'             => 146,
                    'ProductNumberShop'         => '10007',
                    'ProductNumberManufacturer' => 'SC_Mod_105',
                    'SilvercartProductGroupID'  => $productGroupMarketing->ID,
                    'productImage'              => 'logoekomi.jpg',
                    'sortOrder'                 => 2
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'Protected Shops',
                        'ShortDescription' => 'Make your online shop more secure! Try the Protected Shops quality rating system to boost your sales!',
                        'LongDescription'  => 'In the online business you will be confronted with unmanageable specifications which can be very expensive if you breach the conditions. Protected Shops offers a quality rating system to boost your sales. 67% of customers trust in a indepented shop ratings. Use the Vote connect interface of Protected Shops to integrate the quality rating system provided by Protected Shops into SilverCart.',
                        'MetaDescription'  => 'Make your online shop more secure! Try the Protected Shops quality rating system to boost your sales!',
                        'MetaKeywords'     => 'SilverCart, modules, ProtectedShops, marketing',
                        'MetaTitle'        => 'Protected Shops'
                    ),
                    'en_GB' => array(
                        'Title'            => 'Protected Shops',
                        'ShortDescription' => 'Make your online shop more secure! Try the Protected Shops quality rating system to boost your sales!',
                        'LongDescription'  => 'In the online business you will be confronted with unmanageable specifications which can be very expensive if you breach the conditions. Protected Shops offers a quality rating system to boost your sales. 67% of customers trust in a indepented shop ratings. Use the Vote connect interface of Protected Shops to integrate the quality rating system provided by Protected Shops into SilverCart.',
                        'MetaDescription'  => 'Make your online shop more secure! Try the Protected Shops quality rating system to boost your sales!',
                        'MetaKeywords'     => 'SilverCart, modules, ProtectedShops, marketing',
                        'MetaTitle'        => 'Protected Shops'
                    ),
                    'de_DE' => array(
                        'Title'            => 'Protected Shops',
                        'ShortDescription' => 'Machen Sie Ihr Online-Business sicherer! Wer im Internet handelt, kann seinen Umsatz durch das Protected Shops Bewertungssystem steigern. ',
                        'LongDescription'  => 'Wer im Internet handelt, ist mit einer unüberschaubaren Menge rechtlicher Vorgaben konfrontiert, die bei Nichteinhaltung zu einem teuren Unterfangen werden können. Gerade von Konkurrenten, die ihren Mitbewerb durch teuere Abmahnungen zu schädigen versuchen, geht für Ihr Unternehmen eine große Gefahr aus. Wer im Internet handelt, kann seinen Umsatz durch das Protected Shops Bewertungssystem steigern. 67% der Online Käufer vertrauen auf Online-Konsumentenbewertungen (Quelle: www.nielsen.com vom 24.07.2009). Mit unserer Vote Connect Schnittstelle integrieren Sie das Protected Shops Kundenbewertungssystem in Ihren Shop. ',
                        'MetaDescription'  => 'Machen Sie Ihr Online-Business sicherer! Wer im Internet handelt, kann seinen Umsatz durch das Protected Shops Bewertungssystem steigern. ',
                        'MetaKeywords'     => 'SilverCart, Module, ProtectedShops, Marketing',
                        'MetaTitle'        => 'Protected Shops'
                    ),
                    'PriceGrossAmount'          => 49.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 49.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 49.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 49.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'Weight'                    => 75,
                    'StockQuantity'             => 101,
                    'ProductNumberShop'         => '10008',
                    'ProductNumberManufacturer' => 'SC_Mod_106',
                    'SilvercartProductGroupID'  => $productGroupMarketing->ID,
                    'productImage'              => 'logoprotectedshops.jpg',
                    'sortOrder'                 => 3
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'DHL',
                        'ShortDescription' => 'Packet interface for the shipping provider DHL (EasyLog)',
                        'LongDescription'  => 'Packet interface for the shipping provider DHL. Interface to export ordernumbers into Easylog and import tracking numbers back into SilverCart.',
                        'MetaDescription'  => 'Packet interface for the shipping provider DHL (EasyLog)',
                        'MetaKeywords'     => 'SilverCart, modules, shipping, DHL',
                        'MetaTitle'        => 'DHL'
                    ),
                    'en_GB' => array(
                        'Title'            => 'DHL',
                        'ShortDescription' => 'Packet interface for the shipping provider DHL (EasyLog)',
                        'LongDescription'  => 'Packet interface for the shipping provider DHL. Interface to export ordernumbers into Easylog and import tracking numbers back into SilverCart.',
                        'MetaDescription'  => 'Packet interface for the shipping provider DHL (EasyLog)',
                        'MetaKeywords'     => 'SilverCart, modules, shipping, DHL',
                        'MetaTitle'        => 'DHL'
                    ),
                    'de_DE' => array(
                        'Title'            => 'DHL',
                        'ShortDescription' => 'Paketschnittstelle zum Versandanbieter DHL (Easylog)',
                        'LongDescription'  => 'Paketschnittstelle zum Versandanbieter DHL für den Export von Bestellungen nach Easylog und den Import von Sendungsnachverfolgungsnummern in SilverCart.',
                        'MetaDescription'  => 'Paketschnittstelle zum Versandanbieter DHL (Easylog)',
                        'MetaKeywords'     => 'SilverCart, Module, Versand, DHL',
                        'MetaTitle'        => 'DHL'
                    ),
                    'PriceGrossAmount'          => 27.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 27.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 27.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 27.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'Weight'                    => 95,
                    'StockQuantity'             => 12,
                    'ProductNumberShop'         => '10009',
                    'ProductNumberManufacturer' => 'SC_Mod_107',
                    'SilvercartProductGroupID'  => $productGroupOthers->ID,
                    'productImage'              => 'logodhl.jpg',
                    'sortOrder'                 => 1
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'Apache Solr',
                        'ShortDescription' => 'Solr is the popular, blazing fast open source enterprise search platform from the Apache Lucene project.',
                        'LongDescription'  => '<p>Solr is the popular, blazing fast open source enterprise search platform from the Apache Lucene project. Its major features include powerful full-text search, hit highlighting, faceted search, dynamic clustering, database integration, rich document (e.g., Word, PDF) handling, and geospatial search. Solr is highly scalable, providing distributed search and index replication, and it powers the search and navigation features of many of the world' . "'" . 's largest internet sites.</p><p>Solr' . "'" . 's powerful external configuration allows it to be tailored to almost any type of application without Java coding, and it has an extensive plugin architecture when more advanced customization is required. Soruce: http://lucene.apache.org/solr/</p>',
                        'MetaDescription'  => 'Solr is the popular, blazing fast open source enterprise search platform from the Apache Lucene project.',
                        'MetaKeywords'     => 'SilverCart, modules, Apache Solr',
                        'MetaTitle'        => 'Apache Solr'
                    ),
                    'en_GB' => array(
                        'Title'            => 'Apache Solr',
                        'ShortDescription' => 'Solr is the popular, blazing fast open source enterprise search platform from the Apache Lucene project.',
                        'LongDescription'  => '<p>Solr is the popular, blazing fast open source enterprise search platform from the Apache Lucene project. Its major features include powerful full-text search, hit highlighting, faceted search, dynamic clustering, database integration, rich document (e.g., Word, PDF) handling, and geospatial search. Solr is highly scalable, providing distributed search and index replication, and it powers the search and navigation features of many of the world' . "'" . 's largest internet sites.</p><p>Solr' . "'" . 's powerful external configuration allows it to be tailored to almost any type of application without Java coding, and it has an extensive plugin architecture when more advanced customization is required. Soruce: http://lucene.apache.org/solr/</p>',
                        'MetaDescription'  => 'Solr is the popular, blazing fast open source enterprise search platform from the Apache Lucene project.',
                        'MetaKeywords'     => 'SilverCart, modules, Apache Solr',
                        'MetaTitle'        => 'Apache Solr'
                    ),
                    'de_DE' => array(
                        'Title'            => 'Apache Solr',
                        'ShortDescription' => 'Solr basiert auf Lucene Core und ist eine Volltext-Suchmaschine mit Web-Schnittstelle.',
                        'LongDescription'  => 'Dokumente zur Indexierung übernimmt Solr im XML-Format per HTTP-Request. Suchanfragen werden mittels HTTP GET durchgeführt, Resultate werden als XML oder in anderen Formaten wie JSON zurückgegeben. Solr lässt sich in einen Webserver und Servlet-Container wie Apache Tomcat integrieren. Mit Jetty enthält das Solr-Softwarepaket zudem selbst einen Servlet-Container. Mit dem Release 3.1 sind die Projekte Solr und Lucene zu einer Entwicklung zusammengeführt worden, die von einem gemeinsamen Projektteam weiterentwickelt werden. Quelle: Wikipedia.',
                        'MetaDescription'  => 'Solr basiert auf Lucene Core und ist eine Volltext-Suchmaschine mit Web-Schnittstelle.',
                        'MetaKeywords'     => 'SilverCart, Module, Apache Solr',
                        'MetaTitle'        => 'Apache Solr'
                    ),
                    'PriceGrossAmount'          => 9.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 9.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 9.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 9.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'Weight'                    => 25,
                    'StockQuantity'             => 0,
                    'ProductNumberShop'         => '10010',
                    'ProductNumberManufacturer' => 'SC_Mod_108',
                    'SilvercartProductGroupID'  => $productGroupOthers->ID,
                    'productImage'              => 'logosolr.png',
                    'sortOrder'                 => 2
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'PDF Invoice',
                        'ShortDescription' => 'Automatically generate PDF invoices',
                        'LongDescription'  => 'Automatically generated purchase order as PDF file.',
                        'MetaDescription'  => 'Automatically generate PDF invoices',
                        'MetaKeywords'     => 'SilverCart, modules, PDF invoice',
                        'MetaTitle'        => 'PDF Invoice'
                    ),
                    'en_GB' => array(
                        'Title'            => 'PDF Invoice',
                        'ShortDescription' => 'Automatically generate PDF invoices',
                        'LongDescription'  => 'Automatically generated purchase order as PDF file.',
                        'MetaDescription'  => 'Automatically generate PDF invoices',
                        'MetaKeywords'     => 'SilverCart, modules, PDF invoice',
                        'MetaTitle'        => 'PDF Invoice'
                    ),
                    'de_DE' => array(
                        'Title'            => 'PDF-Rechnung',
                        'ShortDescription' => 'Automatische Generierung von PDF-Rechnungen',
                        'LongDescription'  => 'Erstellt automatisiert PDF-Rechnungen bei Bestellungen.',
                        'MetaDescription'  => 'Automatische Generierung von PDF-Rechnungen',
                        'MetaKeywords'     => 'SilverCart, Module, PDF-Rechnung',
                        'MetaTitle'        => 'PDF-Rechnung'
                    ),
                    'PriceGrossAmount'          => 18.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 18.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 18.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 18.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'Weight'                    => 173,
                    'StockQuantity'             => 14,
                    'ProductNumberShop'         => '10011',
                    'ProductNumberManufacturer' => 'SC_Mod_109',
                    'SilvercartProductGroupID'  => $productGroupOthers->ID,
                    'productImage'              => 'logopdfinvoice.jpg',
                    'sortOrder'                 => 3
                ),
                array(
                    'en_US'                     => array(
                        'Title'            => 'Vouchers',
                        'ShortDescription' => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'LongDescription'  => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'MetaDescription'  => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'MetaKeywords'     => 'SilverCart, modules, vouchers',
                        'MetaTitle'        => 'Vouchers'
                    ),
                    'en_GB' => array(
                        'Title'            => 'Vouchers',
                        'ShortDescription' => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'LongDescription'  => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'MetaDescription'  => 'Create various vouchers with percentage or absolute price discount plus coupons for products.',
                        'MetaKeywords'     => 'SilverCart, modules, vouchers',
                        'MetaTitle'        => 'Vouchers'
                    ),
                    'de_DE' => array(
                        'Title'            => 'Gutscheine',
                        'ShortDescription' => 'Gutscheinerstellung mit prozentualem oder absolutem Rabatt sowie Warengutscheinen.',
                        'LongDescription'  => 'Gutscheinerstellung mit prozentualem oder absolutem Rabatt sowie Warengutscheinen.',
                        'MetaDescription'  => 'Gutscheinerstellung mit prozentualem oder absolutem Rabatt sowie Warengutscheinen.',
                        'MetaKeywords'     => 'Silvercart, Module, Gutscheine',
                        'MetaTitle'        => 'Gutscheine'
                    ),
                    'PriceGrossAmount'          => 32.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 32.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 32.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 32.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'Weight'                    => 373,
                    'StockQuantity'             => 24,
                    'ProductNumberShop'         => '10012',
                    'ProductNumberManufacturer' => 'SC_Mod_110',
                    'SilvercartProductGroupID'  => $productGroupOthers->ID,
                    'productImage'              => 'logovouchers.png',
                    'sortOrder'                 => 4
                )
            );
            
            // Create folder for product images
            $exampleDataDir = Director::baseFolder().'/assets/test_images/';
            $imageFolder = new Folder();
            $imageFolder->setName('test_images');
            $imageFolder->write();
            
            if (!file_exists($exampleDataDir)) {
                mkdir($exampleDataDir);
            }
            
            $locales        = array('de_DE', 'en_GB', 'en_US');
            $fallbackLocale = false;

            if (!in_array(Translatable::get_current_locale(), $locales)) {
                $locales[]      = Translatable::get_current_locale();
                $fallbackLocale = Translatable::get_current_locale();
            }

            // Create products
            foreach ($products as $product) {
                $productItem                            = new SilvercartProduct();
                $productItem->SilvercartTaxID           = $taxRateID;
                $productItem->SilvercartManufacturerID  = $manufacturer->ID;
                $productItem->Weight                    = $product['Weight'];
                $productItem->StockQuantity             = $product['StockQuantity'];
                $productItem->ProductNumberShop         = $product['ProductNumberShop'];
                $productItem->ProductNumberManufacturer = $product['ProductNumberManufacturer'];
                $productItem->SilvercartProductGroupID  = $product['SilvercartProductGroupID'];
                $productItem->PriceGrossAmount          = $product['PriceGrossAmount'];
                $productItem->PriceGrossCurrency        = $product['PriceGrossCurrency'];
                $productItem->PriceNetAmount            = $product['PriceNetAmount'];
                $productItem->PriceNetCurrency          = $product['PriceNetCurrency'];
                $productItem->MSRPriceAmount            = $product['MSRPriceAmount'];
                $productItem->MSRPriceCurrency          = $product['MSRPriceCurrency'];
                $productItem->PurchasePriceAmount       = $product['PurchasePriceAmount'];
                $productItem->PurchasePriceCurrency     = $product['PurchasePriceCurrency'];
                $productItem->SortOrder                 = $product['sortOrder'];
                $productItem->write();

                if ($fallbackLocale !== false) {
                    $product[$fallbackLocale] = $product['en_US'];
                }
                
                //create the language objects for the locales
                foreach ($locales as $locale) {
                   /*
                    * We need to check if a language object exists alredy because
                    * a hook of SilvercartProduct defaultly creates one.
                    */
                    $language = DataObject::get_one('SilvercartProductLanguage', sprintf("`SilvercartProductID` = '%s' AND `Locale` = '%s'", $productItem->ID, $locale));
                    if (!$language) {
                        $language = new SilvercartProductLanguage();
                        $language->Locale = $locale;
                    }
                    $language->SilvercartProductID = $productItem->ID;
                    foreach ($product[$locale] as $attribute => $value) {
                        $language->{$attribute} = $value;
                    }
                    $language->write(); 
                }
                
                // Add product image
                if (array_key_exists('productImage', $product)) {
                    copy(
                        Director::baseFolder().'/silvercart/images/exampledata/'.$product['productImage'],
                        $exampleDataDir.'/'.$product['productImage']
                    );

                    $productImage = new Image();
                    $productImage->setName($product['productImage']);
                    $productImage->setFilename($exampleDataDir.'/'.$product['productImage']);
                    $productImage->setParentID($imageFolder->ID);
                    $productImage->write();

                    $silvercartImage = new SilvercartImage();
                    $silvercartImage->setField('SilvercartProductID', $productItem->ID);
                    $silvercartImage->setField('ImageID',             $productImage->ID);
                    $silvercartImage->write();
                }
            }
            
            // create widget sets
            $widgetSetFrontPageContentArea = new WidgetArea();
            $widgetSetFrontPageContentArea->write();
            
            $widgetSetFrontPageContent = new SilvercartWidgetSet();
            $widgetSetFrontPageContent->setField('Title', _t('SilvercartTestData.WIDGETSET_FRONTPAGE_CONTENT_TITLE'));
            $widgetSetFrontPageContent->setField('WidgetAreaID', $widgetSetFrontPageContentArea->ID);
            $widgetSetFrontPageContent->write();
            
            $widgetSetFrontPageSidebarArea = new WidgetArea();
            $widgetSetFrontPageSidebarArea->write();
            
            $widgetSetFrontPageSidebar = new SilvercartWidgetSet();
            $widgetSetFrontPageSidebar->setField('Title', _t('SilvercartTestData.WIDGETSET_FRONTPAGE_SIDEBAR_TITLE'));
            $widgetSetFrontPageSidebar->setField('WidgetAreaID', $widgetSetFrontPageSidebarArea->ID);
            $widgetSetFrontPageSidebar->write();
            
            $widgetSetProductGroupPagesSidebarArea = new WidgetArea();
            $widgetSetProductGroupPagesSidebarArea->write();
            
            $widgetSetProductGroupPagesSidebar = new SilvercartWidgetSet();
            $widgetSetProductGroupPagesSidebar->setField('Title', _t('SilvercartTestData.WIDGETSET_PRODUCTGROUPPAGES_SIDEBAR_TITLE'));
            $widgetSetProductGroupPagesSidebar->setField('WidgetAreaID', $widgetSetProductGroupPagesSidebarArea->ID);
            $widgetSetProductGroupPagesSidebar->write();
            
            // Attribute widget sets to pages
            $frontPage = SilvercartPage_Controller::PageByIdentifierCode('SilvercartFrontPage');
            
            if ($frontPage) {
                $frontPage->WidgetSetContent()->add($widgetSetFrontPageContent);
                $frontPage->WidgetSetSidebar()->add($widgetSetFrontPageSidebar);
            }
            
            $productGroupHolderPage = SilvercartPage_Controller::PageByIdentifierCode('SilvercartProductGroupHolder');
            
            if ($productGroupHolderPage) {
                $productGroupHolderPage->WidgetSetSidebar()->add($widgetSetProductGroupPagesSidebar);
            }
            
            // Create Widgets
            $widgetFrontPageContent1 = new SilvercartProductGroupItemsWidget();
            $widgetFrontPageContent1->setField('FrontTitle', _t('SilvercartTestData.WIDGETSET_FRONTPAGE_CONTENT1_TITLE'));
            $widgetFrontPageContent1->setField('FrontContent', _t('SilvercartTestData.WIDGETSET_FRONTPAGE_CONTENT1_CONTENT'));
            $widgetFrontPageContent1->setField('numberOfProductsToShow', 4);
            $widgetFrontPageContent1->setField('SilvercartProductGroupPageID', $productGroupPayment->ID);
            $widgetFrontPageContent1->setField('useListView', 0);
            $widgetFrontPageContent1->setField('isContentView', 1);
            $widgetFrontPageContent1->setField('useSlider', 0);
            $widgetFrontPageContent1->setField('buildArrows', 0);
            $widgetFrontPageContent1->setField('buildNavigation', 1);
            $widgetFrontPageContent1->setField('buildStartStop', 0);
            $widgetFrontPageContent1->setField('slideDelay', 6000);
            $widgetFrontPageContent1->setField('transitionEffect', 'fade');
            $widgetFrontPageContent1->setField('Sort', 2);
            $widgetFrontPageContent1->write();

            $widgetSetFrontPageContentArea->Widgets()->add($widgetFrontPageContent1);
            
            $widgetFrontPageContent2 = new SilvercartProductGroupItemsWidget();
            $widgetFrontPageContent2->setField('FrontTitle', _t('SilvercartTestData.WIDGETSET_FRONTPAGE_CONTENT2_TITLE'));
            $widgetFrontPageContent2->setField('FrontContent', _t('SilvercartTestData.WIDGETSET_FRONTPAGE_CONTENT2_CONTENT'));
            $widgetFrontPageContent2->setField('numberOfProductsToShow', 1);
            $widgetFrontPageContent2->setField('numberOfProductsToFetch', 4);
            $widgetFrontPageContent2->setField('SilvercartProductGroupPageID', $productGroupOthers->ID);
            $widgetFrontPageContent2->setField('useListView', 1);
            $widgetFrontPageContent2->setField('isContentView', 1);
            $widgetFrontPageContent2->setField('useSlider', 1);
            $widgetFrontPageContent2->setField('buildArrows', 0);
            $widgetFrontPageContent2->setField('buildNavigation', 1);
            $widgetFrontPageContent2->setField('buildStartStop', 0);
            $widgetFrontPageContent2->setField('slideDelay', 6000);
            $widgetFrontPageContent2->setField('transitionEffect', 'horizontalSlide');
            $widgetFrontPageContent2->setField('Sort', 3);
            $widgetFrontPageContent2->write();

            $widgetSetFrontPageContentArea->Widgets()->add($widgetFrontPageContent2);
            
            $widgetFrontPageContent3 = new SilvercartImageSliderWidget();
            $widgetFrontPageContent3->setField('buildArrows', 0);
            $widgetFrontPageContent3->setField('buildNavigation', 1);
            $widgetFrontPageContent3->setField('buildStartStop', 0);
            $widgetFrontPageContent3->setField('slideDelay', 10000);
            $widgetFrontPageContent3->setField('transitionEffect', 'fade');
            $widgetFrontPageContent3->setField('Sort', 1);
            $widgetFrontPageContent3->write();

            $widgetSetFrontPageContentArea->Widgets()->add($widgetFrontPageContent3);
            
            copy(
                Director::baseFolder().'/silvercart/images/exampledata/silvercart_teaser.jpg',
                $exampleDataDir.'/silvercart_teaser.jpg'
            );
            $teaserImage = new Image();
            $teaserImage->setFilename($exampleDataDir.'/silvercart_teaser.jpg');
            $teaserImage->setParentID($imageFolder->ID);
            $teaserImage->write();
            
            $slideImage = new SilvercartImageSliderImage();
            #$slideImage->setField('Title',   'Silvercart Teaser');
            $slideImage->setField('ImageID', $teaserImage->ID);
            $slideImage->write();
            $sliderImageTranslations = array(
                'en_GB' => 'SilverCart Teaser',
                'en_US' => 'SilverCart Teaser',
                'de_DE' => 'SilverCart Teaser'
            );
            $locales        = array('de_DE', 'en_GB', 'en_US');
            $fallbackLocale = false;

            if (!in_array(Translatable::get_current_locale(), $locales)) {
                $locales[]      = Translatable::get_current_locale();
                $fallbackLocale = Translatable::get_current_locale();
            }

            if ($fallbackLocale !== false) {
                $sliderImageTranslations[$fallbackLocale] = $sliderImageTranslations['en_US'];
            }

            foreach ($sliderImageTranslations as $locale => $translation) {
                $filter = sprintf("`Locale` = '%s'", $locale);
                $translationObj = DataObject::get_one("SilvercartImageSliderImageLanguage", $filter);
                if (!$translationObj) {
                    $translationObj = new SilvercartImageSliderImageLanguage();
                    $translationObj->Locale = $locale;
                    $translationObj->SilvercartImageSliderImageID = $slideImage->ID;
                }
                $translationObj->Title = $translation;
                $translationObj->write();
            }
            
            $widgetFrontPageContent3->slideImages()->add($slideImage);

            $widgetFrontPageSidebar1 = new SilvercartProductGroupItemsWidget();
            $widgetFrontPageSidebar1->setField('numberOfProductsToShow', 3);
            $widgetFrontPageSidebar1->setField('SilvercartProductGroupPageID', $productGroupMarketing->ID);
            $widgetFrontPageSidebar1->setField('useSlider', 0);
            $widgetFrontPageSidebar1->setField('useListView', 1);
            $widgetFrontPageSidebar1->setField('isContentView', 0);
            $widgetFrontPageSidebar1->setField('buildArrows', 0);
            $widgetFrontPageSidebar1->setField('buildNavigation', 1);
            $widgetFrontPageSidebar1->setField('buildStartStop', 0);
            $widgetFrontPageSidebar1->setField('slideDelay', 4000);
            $widgetFrontPageSidebar1->setField('transitionEffect', 'horizontalSlide');
            $widgetFrontPageSidebar1->setField('Sort', 1);
            $widgetFrontPageSidebar1->write();

            $widgetSetFrontPageSidebarArea->Widgets()->add($widgetFrontPageSidebar1);
            
            $widgetFrontPageSidebar2 = new SilvercartShoppingCartWidget();
            $widgetFrontPageSidebar2->setField('Sort', 2);
            $widgetFrontPageSidebar2->write();

            $widgetSetFrontPageSidebarArea->Widgets()->add($widgetFrontPageSidebar2);
            
            $widgetFrontPageSidebar3 = new SilvercartLoginWidget();
            $widgetFrontPageSidebar3->setField('Sort', 3);
            $widgetFrontPageSidebar3->write();

            $widgetSetFrontPageSidebarArea->Widgets()->add($widgetFrontPageSidebar3);
            
            // product group page widgets
            
            $widgetProductGroupPageSidebar1 = new SilvercartProductGroupItemsWidget();
            $widgetProductGroupPageSidebar1->setField('numberOfProductsToShow', 3);
            $widgetProductGroupPageSidebar1->setField('SilvercartProductGroupPageID', $productGroupMarketing->ID);
            $widgetProductGroupPageSidebar1->setField('useSlider', 0);
            $widgetProductGroupPageSidebar1->setField('useListView', 1);
            $widgetProductGroupPageSidebar1->setField('isContentView', 0);
            $widgetProductGroupPageSidebar1->setField('buildArrows', 0);
            $widgetProductGroupPageSidebar1->setField('buildNavigation', 1);
            $widgetProductGroupPageSidebar1->setField('buildStartStop', 0);
            $widgetProductGroupPageSidebar1->setField('slideDelay', 4000);
            $widgetProductGroupPageSidebar1->setField('transitionEffect', 'horizontalSlide');
            $widgetProductGroupPageSidebar1->setField('Sort', 1);
            $widgetProductGroupPageSidebar1->write();

            $widgetSetProductGroupPagesSidebarArea->Widgets()->add($widgetProductGroupPageSidebar1);
            
            $widgetProductGroupPageSidebar2 = new SilvercartShoppingCartWidget();
            $widgetProductGroupPageSidebar2->setField('Sort', 2);
            $widgetProductGroupPageSidebar2->write();

            $widgetSetProductGroupPagesSidebarArea->Widgets()->add($widgetProductGroupPageSidebar2);
            
            $widgetProductGroupPageSidebar3 = new SilvercartLoginWidget();
            $widgetProductGroupPageSidebar3->setField('Sort', 3);
            $widgetProductGroupPageSidebar3->write();

            $widgetSetProductGroupPagesSidebarArea->Widgets()->add($widgetProductGroupPageSidebar3);
            
            return true;
        }
    }

    /**
     * creates test configuration data on /dev/build or by adding test
     * configuration in ModelAdmin.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.07.2011
     */
    public static function createTestConfiguration() {
        if (self::$enableTestData === true) {
            //create a carrier and an associated zone and shipping method
            $carrier = DataObject::get_one('SilvercartCarrier');
            if (!$carrier) {
                self::createTestTaxRates();
                
                $carrier = new SilvercartCarrier();
                $carrier->Title = 'DHL';
                $carrier->FullTitle = 'DHL International GmbH';
                $carrier->write();
                $carrierLanguages = array(
                    'en_GB' => array(
                        'Title' => 'DHL',
                        'FullTitle' => 'DHL International GmbH'
                    ),
                    'en_US' => array(
                        'Title' => 'DHL',
                        'FullTitle' => 'DHL International GmbH'
                    ),
                    'de_DE' => array(
                        'Title' => 'DHL',
                        'FullTitle' => 'DHL International GmbH'
                    )
                );
            
                $locales        = array('de_DE', 'en_GB', 'en_US');
                $fallbackLocale = false;

                if (!in_array(Translatable::get_current_locale(), $locales)) {
                    $locales[]      = Translatable::get_current_locale();
                    $fallbackLocale = Translatable::get_current_locale();
                }

                if ($fallbackLocale !== false) {
                    $carrierLanguages[$fallbackLocale] = $carrierLanguages['en_US'];
                }

                foreach ($carrierLanguages as $locale => $attributes) {
                    $filter = sprintf("`SilvercartCarrierID` = '%s' AND `Locale` = '%s'", $carrier->ID, $locale);
                    $languageObj = DataObject::get_one('SilvercartCarrierLanguage', $filter);
                    if (!$languageObj) {
                        $languageObj = new SilvercartCarrierLanguage();
                        $languageObj->Locale = $locale;
                        $languageObj->SilvercartCarrierID = $carrier->ID;
                    }
                    foreach ($attributes as $attribute => $value) {
                        $languageObj->{$attribute} = $value;
                    }
                    $languageObj->write();
                    
                }
                //relate carrier to zones
                $zoneDomestic = DataObject::get_one("SilvercartZone");
                if (!$zoneDomestic) {
                    $zones = array(
                        array(
                            'en_GB' => 'Domestic',
                            'en_US' => 'Domestic',
                            'de_DE' => 'Inland'
                        ),
                        array(
                            'en_GB' => 'EU',
                            'en_US' => 'European Union',
                            'de_DE' => 'EU'
                        )
                    );

                    $locales        = array('de_DE', 'en_GB', 'en_US');
                    $fallbackLocale = false;

                    if (!in_array(Translatable::get_current_locale(), $locales)) {
                        $locales[]      = Translatable::get_current_locale();
                        $fallbackLocale = Translatable::get_current_locale();
                    }

                    if ($fallbackLocale !== false) {
                        $zones[0][$fallbackLocale] = $zones[0]['en_US'];
                        $zones[1][$fallbackLocale] = $zones[1]['en_US'];
                    }
                    
                    foreach ($zones as $zone) {
                        $zoneObj = new SilvercartZone();
                        $zoneObj->write();
                        $zoneObj->SilvercartCarriers()->add($carrier);
                        $zoneObj->write();
                        foreach ($zone as $locale => $title) {
                            $filter = sprintf("`SilvercartZoneID` = '%s' AND `Locale` = '%s'", $zoneObj->ID, $locale);
                            $zoneLanguage = DataObject::get_one('SilvercartZoneLanguage', $filter);
                            if (!$zoneLanguage) {
                                $zoneLanguage = new SilvercartZoneLanguage();
                                $zoneLanguage->SilvercartZoneID = $zoneObj->ID;
                                $zoneLanguage->Locale = $locale;
                            }
                            $zoneLanguage->Title = $title;
                            $zoneLanguage->write();
                        }
                    }
                    
                }
                //Retrieve the active country if exists
                $country = DataObject::get_one('SilvercartCountry', "`Active` = 1");
                if (!$country) {
                    //Retrieve the country dynamically depending on the installation language
                    $installationLanguage = i18n::get_locale();
                    $ISO2 = substr($installationLanguage, -2);
                    $country = DataObject::get_one('SilvercartCountry', sprintf("`ISO2` = '%s'", $ISO2));
                    if (!$country) {
                        $country = new SilvercartCountry();
                        $country->Title = 'Testcountry';
                        $country->ISO2 = $ISO2;
                        $country->ISO3 = $ISO2;
                    }
                    $country->Active = true;
                    $country->write();
                }
                
                $zoneDomestic = DataObject::get_by_id('SilvercartZone', 1);
                $zoneDomestic->SilvercartCountries()->add($country);
                
                // create if not exists, activate and relate payment method
                $paymentMethod = DataObject::get_one('SilvercartPaymentPrepayment');
                if (!$paymentMethod) {
                    $paymentMethodHandler = new SilvercartPaymentMethod();
                    $paymentMethodHandler->requireDefaultRecords();
                }
                $paymentMethod = DataObject::get_one('SilvercartPaymentPrepayment');
                $paymentMethod->isActive = true;
                $orderStatusPending = DataObject::get_one("SilvercartOrderStatus", "`Code` = 'pending'");
                if ($orderStatusPending) {
                    $paymentMethod->orderStatus = $orderStatusPending->Code;
                }
                $paymentMethod->write();
                $country->SilvercartPaymentMethods()->add($paymentMethod);

                // create a shipping method
                $shippingMethod = DataObject::get_one("SilvercartShippingMethod");
                if (!$shippingMethod) {
                    $shippingMethod = new SilvercartShippingMethod();
                    //relate shipping method to carrier
                    $shippingMethod->SilvercartCarrierID = $carrier->ID;
                }
                $shippingMethod->isActive = 1;
                $shippingMethod->write();
                $shippingMethod->SilvercartZones()->add($zoneDomestic);
                
                
                //create the language objects for the shipping method
                $shippingMethodTranslations = array(
                    'de_DE' => 'Paket',
                    'en_GB' => 'Package',
                    'en_US' => 'Package'
                );

                $locales        = array('de_DE', 'en_GB', 'en_US');
                $fallbackLocale = false;

                if (!in_array(Translatable::get_current_locale(), $locales)) {
                    $locales[]      = Translatable::get_current_locale();
                    $fallbackLocale = Translatable::get_current_locale();
                }

                if ($fallbackLocale !== false) {
                    $shippingMethodTranslations[$fallbackLocale] = $shippingMethodTranslations['en_US'];
                }

                foreach ($shippingMethodTranslations as $locale => $title) {
                    $filter = sprintf("`Locale` = '%s' AND `SilvercartShippingMethodID` = '%s'", $locale, $shippingMethod->ID);
                    $shippingMethodLanguage = DataObject::get_one('SilvercartShippingMethodLanguage', $filter);
                    if (!$shippingMethodLanguage) {
                        $shippingMethodLanguage = new SilvercartShippingMethodLanguage();
                        $shippingMethodLanguage->Locale = $locale;
                        $shippingMethodLanguage->SilvercartShippingMethodID = $shippingMethod->ID;
                    }
                    $shippingMethodLanguage->Title = $title;
                    $shippingMethodLanguage->write();
                }

                // create a shipping fee and relate it to zone, tax and shipping method
                $shippingFee = DataObject::get_one('SilvercartShippingFee');
                if (!$shippingFee) {
                    $shippingFee = new SilvercartShippingFee();
                    $shippingFee->MaximumWeight = '100000';
                    $shippingFee->Price = new Money();
                    $shippingFee->Price->setAmount('3.9');
                    $shippingFee->Price->setCurrency('EUR');
                }
                $shippingFee->SilvercartShippingMethodID = $shippingMethod->ID;
                $shippingFee->SilvercartZoneID = $zoneDomestic->ID;
                $higherTaxRate = DataObject::get_one('SilvercartTax', "`Rate` = 19");
                $shippingFee->SilvercartTaxID = $higherTaxRate->ID;
                $shippingFee->write();
                
                return true;
            }
        }
        return false;
    }

    /**
     * creates test tax rates on /dev/build or creating test data in ModelAdmin.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.07.2011
     */
    public static function createTestTaxRates() {
        if (self::$enableTestData === true) {
            // create two standard german tax rates if no tax rate exists
            $taxRate = DataObject::get_one('SilvercartTax');

            if (!$taxRate) {
                $taxrates = array(
                    '19' => array(
                        'en_US' => '19%',
                        'en_GB' => '19%',
                        'de_DE' => '19%'
                    ),
                    '7' => array(
                        'en_US' => '7%',
                        'en_GB' => '7%',
                        'de_DE' => '7%'
                    )
                );

                $locales        = array('de_DE', 'en_GB', 'en_US');
                $fallbackLocale = false;

                if (!in_array(Translatable::get_current_locale(), $locales)) {
                    $locales[]      = Translatable::get_current_locale();
                    $fallbackLocale = Translatable::get_current_locale();
                }

                if ($fallbackLocale !== false) {
                    $taxrates[0][$fallbackLocale] = $taxrates[0]['en_US'];
                    $taxrates[1][$fallbackLocale] = $taxrates[1]['en_US'];
                }
                
                foreach ($taxrates as $taxrate => $languages) {
                    $rateObj = new SilvercartTax();
                    $rateObj->Rate = $taxrate;
                    $rateObj->write();
                    foreach ($languages as $locale => $title) {
                        $filter = sprintf("`Locale` = '%s' AND `SilvercartTaxID` = '%s'", $locale, $rateObj->ID);
                        $rateLanguage = DataObject::get_one('SilvercartTaxLanguage', $filter);
                        if (!$rateLanguage) {
                            $rateLanguage = new SilvercartTaxLanguage();
                            $rateLanguage->Locale = $locale;
                            $rateLanguage->SilvercartTaxID = $rateObj->ID;
                        }
                        $rateLanguage->Title = $title;
                        $rateLanguage->write();
                    }
                }
            }
        }
    }
}
