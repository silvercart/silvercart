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
     * create default records
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 16.02.2011
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        // Create an own group for anonymous customers
        $anonymousGroup = DataObject::get_one('Group', "`Code` = 'anonymous'");
        if (!$anonymousGroup) {
            $anonymousGroup = new Group();
            $anonymousGroup->Title = _t('SilvercartCustomer.ANONYMOUSCUSTOMER', 'anonymous customer');
            $anonymousGroup->Code = "anonymous";
            $anonymousGroup->write();
        }

        // Create an own group for b2b customers
        $B2Bgroup = DataObject::get_one('Group', "`Code` = 'b2b'");
        if (!$B2Bgroup) {
            $B2Bgroup = new Group();
            $B2Bgroup->Title = _t('SilvercartCustomer.BUSINESSCUSTOMER', 'business customer');
            $B2Bgroup->Code = "b2b";
            $B2Bgroup->write();
        }

        //create a group for b2c customers
        $B2Cgroup = DataObject::get_one('Group', "`Code` = 'b2c'");
        if (!$B2Cgroup) {
            $B2Cgroup = new Group();
            $B2Cgroup->Title = _t('SilvercartCustomer.REGULARCUSTOMER', 'regular customer');
            $B2Cgroup->Code = "b2c";
            $B2Cgroup->write();
        }

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

        // create countries
        $this->requireOrUpdateCountries();
        if (!DataObject::get_one('SilvercartCountry', "`Active`=1")) {
            $country = DataObject::get_one('SilvercartCountry', sprintf("`ISO2`='%s'", substr(i18n::get_locale(), 3)));
            if ($country) {
                $country->Active = true;
                $country->write();
            }
        }

        //create order stati
        if (!DataObject::get_one('SilvercartOrderStatus')) {

            $defaultStatusEntries = array(
                'pending' => _t('SilvercartOrderStatus.WAITING_FOR_PAYMENT', 'waiting for payment'),
                'payed' => _t('SilvercartOrderStatus.PAYED', 'payed')
            );

            foreach ($defaultStatusEntries as $code => $title) {
                $obj = new SilvercartOrderStatus();
                $obj->Title = $title;
                $obj->Code = $code;
                $obj->write();
            }
        }

        // create availability status
        if (!DataObject::get_one('SilvercartAvailabilityStatus')) {

            $defaultStatusEntries = array(
                'available'     => _t('SilvercartAvailabilityStatus.STATUS_AVAILABLE', 'available'),
                'not-available' => _t('SilvercartAvailabilityStatus.STATUS_NOT_AVAILABLE', 'not available')
            );

            foreach ($defaultStatusEntries as $code => $title) {
                $obj = new SilvercartAvailabilityStatus();
                $obj->Title = $title;
                $obj->Code = $code;
                $obj->write();
            }
        }

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

        /*
         * and now the whole site tree
         */

        $rootPage = DataObject::get_one('SilvercartPage',"`IdentifierCode` = 'SilvercartCartPage'");
        if (!$rootPage) {
            //create a silvercart front page (parent of all other SilverCart pages
            $rootPage = new SilvercartFrontPage();
            $rootPage->IdentifierCode = "SilvercartFrontPage";
            $rootPage->Title = 'Silvercart';
            $rootPage->MenuTitle = "Silvercart";
            if (SiteTree::get_by_link('home')) {
                $rootPage->URLSegment = 'webshop';
            } else {
                $rootPage->URLSegment = 'home';
            }
            $rootPage->ShowInMenus = false;
            $rootPage->ShowInSearch = false;
            $rootPage->Status = "Published";
            $rootPage->CanViewType = "Anyone";
            $rootPage->Content = _t('SilvercartFrontPage.DEFAULT_CONTENT', '<h2>Welcome to <strong>SilverCart</strong> Webshop!</h2>');
            $rootPage->write();
            $rootPage->publish("Stage", "Live");
            
            //create a deeplink page as child of the silvercart root
            $deeplinkPage = new SilvercartDeeplinkPage();
            $deeplinkPage->IdentifierCode = "SilvercartDeeplinkPage";
            $deeplinkPage->Title = _t('SilvercartDeeplinkPage.SINGULARNAME');
            $deeplinkPage->URLSegment = 'deeplink';
            $deeplinkPage->Status = 'Published';
            $deeplinkPage->ParentID = $rootPage->ID;
            $deeplinkPage->ShowInMenus = false;
            $deeplinkPage->ShowInSearch = false;
            $deeplinkPage->CanViewType = "Anyone";
            $deeplinkPage->write();
            $deeplinkPage->publish("Stage", "Live");

            //create a silvercart product group holder as a child af the silvercart root
            $productGroupHolder = new SilvercartProductGroupHolder();
            $productGroupHolder->Title = _t('SilvercartProductGroupHolder.PAGE_TITLE', 'product groups');
            $productGroupHolder->URLSegment = _t('SilvercartProductGroupHolder.URL_SEGMENT', 'productgroups');
            $productGroupHolder->Status = "Published";
            $productGroupHolder->ParentID = $rootPage->ID;
            $productGroupHolder->IdentifierCode = "SilvercartProductGroupHolder";
            $productGroupHolder->write();
            $productGroupHolder->publish("Stage", "Live");

            //create a cart page
            $cartPage = new SilvercartCartPage();
            $cartPage->Title = _t('SilvercartPage.CART');
            $cartPage->URLSegment = _t('SilvercartCartPage.URL_SEGMENT', 'cart');
            $cartPage->Status = "Published";
            $cartPage->ShowInMenus = true;
            $cartPage->ShowInSearch = false;
            $cartPage->IdentifierCode = "SilvercartCartPage";
            $cartPage->ParentID = $rootPage->ID;
            $cartPage->write();
            $cartPage->publish("Stage", "Live");

            //create a silvercart checkout step (checkout) as achild of the silvercart root
            $checkoutStep = new SilvercartCheckoutStep();
            $checkoutStep->Title = _t('SilvercartPage.CHECKOUT');
            $checkoutStep->URLSegment = _t('SilvercartCheckoutStep.URL_SEGMENT', 'checkout');
            $checkoutStep->Status = "Published";
            $checkoutStep->ShowInMenus = true;
            $checkoutStep->ShowInSearch = true;
            $checkoutStep->basename = 'SilvercartCheckoutFormStep';
            $checkoutStep->showCancelLink = true;
            $checkoutStep->cancelPageID = $cartPage->ID;
            $checkoutStep->ParentID = $rootPage->ID;
            $checkoutStep->IdentifierCode = "SilvercartCheckoutStep";
            $checkoutStep->write();
            $checkoutStep->publish("Stage", "Live");

            //create a my account holder page as child of the silvercart root
            $myAccountHolder = new SilvercartMyAccountHolder();
            $myAccountHolder->Title = _t('SilvercartMyAccountHolder.TITLE', 'my account');
            $myAccountHolder->URLSegment = _t('SilvercartMyAccountHolder.URL_SEGMENT', 'my-account');
            $myAccountHolder->Status = "Published";
            $myAccountHolder->ShowInMenus = false;
            $myAccountHolder->ShowInSearch = false;
            $myAccountHolder->ParentID = $rootPage->ID;
            $myAccountHolder->IdentifierCode = "SilvercartMyAccountHolder";
            $myAccountHolder->write();
            $myAccountHolder->publish("Stage", "Live");

            //create a silvercart data page as a child of silvercart my account holder
            $dataPage = new SilvercartDataPage();
            $dataPage->Title = _t('SilvercartDataPage.TITLE', 'my data');
            $dataPage->URLSegment = _t('SilvercartDataPage.URL_SEGMENT', 'my-data');
            $dataPage->Status = "Published";
            $dataPage->ShowInMenus = true;
            $dataPage->ShowInSearch = false;
            $dataPage->CanViewType = "Inherit";
            $dataPage->ParentID = $myAccountHolder->ID;
            $dataPage->IdentifierCode = "SilvercartDataPage";
            $dataPage->write();
            $dataPage->publish("Stage", "Live");

            //create a silvercart order holder as a child of silvercart my account holder
            $orderHolder = new SilvercartOrderHolder();
            $orderHolder->Title = _t('SilvercartOrderHolder.TITLE', 'my orders');
            $orderHolder->URLSegment = 'my-orders';
            $orderHolder->Status = "Published";
            $orderHolder->ShowInMenus = true;
            $orderHolder->ShowInSearch = false;
            $orderHolder->CanViewType = "Inherit";
            $orderHolder->ParentID = $myAccountHolder->ID;
            $orderHolder->IdentifierCode = "SilvercartOrderHolder";
            $orderHolder->write();
            $orderHolder->publish("Stage", "Live");

            //create an order detail page as a child of the order holder
            $orderDetailPage = new SilvercartOrderDetailPage();
            $orderDetailPage->Title = _t('SilvercartOrderDetailPage.TITLE', 'order details');
            $orderDetailPage->URLSegment = 'order-details';
            $orderDetailPage->Status = "Published";
            $orderDetailPage->ShowInMenus = false;
            $orderDetailPage->ShowInSearch = false;
            $orderDetailPage->CanViewType = "Inherit";
            $orderDetailPage->ParentID = $orderHolder->ID;
            $orderDetailPage->IdentifierCode = "SilvercartOrderDetailPage";
            $orderDetailPage->write();
            $orderDetailPage->publish("Stage", "Live");

            //create a silvercart address holder as a child of silvercart my account holder
            $addressHolder = new SilvercartAddressHolder();
            $addressHolder->Title = _t('SilvercartAddressHolder.TITLE', 'address overview');
            $addressHolder->URLSegment = 'address-overview';
            $addressHolder->Status = "Published";
            $addressHolder->ShowInMenus = true;
            $addressHolder->ShowInSearch = false;
            $addressHolder->CanViewType = "Inherit";
            $addressHolder->ParentID = $myAccountHolder->ID;
            $addressHolder->IdentifierCode = "SilvercartAddressHolder";
            $addressHolder->write();
            $addressHolder->publish("Stage", "Live");

            //create a silvercart address page as a child of silvercart my account holder
            $addressPage = new SilvercartAddressPage();
            $addressPage->Title = _t('SilvercartAddressPage.TITLE', 'address details');
            $addressPage->URLSegment = 'address-details';
            $addressPage->Status = "Published";
            $addressPage->ShowInMenus = false;
            $addressPage->ShowInSearch = false;
            $addressPage->CanViewType = "Inherit";
            $addressPage->ParentID = $addressHolder->ID;
            $addressPage->IdentifierCode = "SilvercartAddressPage";
            $addressPage->write();
            $addressPage->publish("Stage", "Live");

            //create a meta navigation holder
            $metaNavigationHolder = new SilvercartMetaNavigationHolder();
            $metaNavigationHolder->Title = _t('SilvercartMetaNavigationHolder.SINGULARNAME');
            $metaNavigationHolder->URLSegment = _t('SilvercartMetaNavigationHolder.URL_SEGMENT', 'metanavigation');
            $metaNavigationHolder->Status = "Published";
            $metaNavigationHolder->ShowInMenus = 0;
            $metaNavigationHolder->IdentifierCode = "SilvercartMetaNavigationHolder";
            $metaNavigationHolder->ParentID = $rootPage->ID;
            $metaNavigationHolder->write();
            $metaNavigationHolder->publish("Stage", "Live");

            //create a contact form page as a child of the meta navigation holder
            $contactPage = new SilvercartContactFormPage();
            $contactPage->Title = _t('SilvercartContactFormPage.TITLE', 'contact');
            $contactPage->URLSegment = _t('SilvercartContactFormPage.URL_SEGMENT', 'contact');
            $contactPage->Status = "Published";
            $contactPage->ShowInMenus = 1;
            $contactPage->IdentifierCode = "SilvercartContactFormPage";
            $contactPage->ParentID = $metaNavigationHolder->ID;
            $contactPage->write();
            $contactPage->publish("Stage", "Live");

            //create a terms of service page as a child of the meta navigation holder
            $termsOfServicePage = new SilvercartMetaNavigationPage();
            $termsOfServicePage->Title = _t('SilvercartPage.TITLE_TERMS', 'terms of service');
            $termsOfServicePage->URLSegment = _t('SilvercartPage.URL_SEGMENT_TERMS', 'terms-of-service');
            $termsOfServicePage->Status = "Published";
            $termsOfServicePage->ShowInMenus = 1;
            $termsOfServicePage->ParentID = $metaNavigationHolder->ID;
            $termsOfServicePage->IdentifierCode = "TermsOfServicePage";
            $termsOfServicePage->write();
            $termsOfServicePage->publish("Stage", "Live");

            //create an imprint page as a child of the meta navigation holder
            $imprintPage = new SilvercartMetaNavigationPage();
            $imprintPage->Title = _t('SilvercartPage.TITLE_IMPRINT', 'imprint');
            $imprintPage->URLSegment = _t('SilvercartPage.URL_SEGMENT_IMPRINT', 'imprint');
            $imprintPage->Status = "Published";
            $imprintPage->ShowInMenus = 1;
            $imprintPage->ParentID = $metaNavigationHolder->ID;
            $imprintPage->IdentifierCode = "ImprintPage";
            $imprintPage->write();
            $imprintPage->publish("Stage", "Live");

            //create a data privacy statement page as a child of the meta navigation holder
            $dataPrivacyStatementPage = new SilvercartMetaNavigationPage();
            $dataPrivacyStatementPage->Title = _t('SilvercartDataPrivacyStatementPage.TITLE', 'data privacy statement');
            $dataPrivacyStatementPage->URLSegment = _t('SilvercartDataPrivacyStatementPage.URL_SEGMENT', 'data-privacy-statement');
            $dataPrivacyStatementPage->Status = "Published";
            $dataPrivacyStatementPage->ShowInMenus = 1;
            $dataPrivacyStatementPage->IdentifierCode = "SilvercartDataPrivacyStatementPage";
            $dataPrivacyStatementPage->ParentID = $metaNavigationHolder->ID;
            $dataPrivacyStatementPage->write();
            $dataPrivacyStatementPage->publish("Stage", "Live");

            //create a silvercart shipping fees page as child of the meta navigation holder
            $shippingFeesPage = new SilvercartShippingFeesPage();
            $shippingFeesPage->Title = _t('SilvercartShippingFeesPage.TITLE', 'shipping fees');
            $shippingFeesPage->URLSegment = _t('SilvercartShippingFeesPage.URL_SEGMENT', 'shipping-fees');
            $shippingFeesPage->Status = "Published";
            $shippingFeesPage->ShowInMenus = 1;
            $shippingFeesPage->ParentID = $metaNavigationHolder->ID;
            $shippingFeesPage->IdentifierCode = "SilvercartShippingFeesPage";
            $shippingFeesPage->write();
            $shippingFeesPage->publish("Stage", "Live");

            // create SilvercartFooterNavigationHolder and a about page as child
            $footerNavigationHolder = new SilvercartFooterNavigationHolder();
            $footerNavigationHolder->Title = _t('SilvercartFooterNavigationHolder.SINGULARNAME');
            $footerNavigationHolder->URLSegment = _t('SilvercartFooterNavigationHolder.URL_SEGMENT', 'footernavigation');
            $footerNavigationHolder->Status = "Published";
            $footerNavigationHolder->ShowInMenus = 0;
            $footerNavigationHolder->IdentifierCode = "FooterNavigationHolder";
            $footerNavigationHolder->ParentID = $rootPage->ID;
            $footerNavigationHolder->write();
            $footerNavigationHolder->publish("Stage", "Live");

            $aboutPage = new Page();
            $aboutPage->Title = _t('SilvercartPage.ABOUT_US', 'about us');
            $aboutPage->URLSegment = _t('SilvercartPage.ABOUT_US_URL_SEGMENT', 'about-us');
            $aboutPage->Status = "Published";
            $aboutPage->ShowInMenus = 1;
            $aboutPage->ParentID = $footerNavigationHolder->ID;
            $aboutPage->IdentifierCode = "AboutPage";
            $aboutPage->write();
            $aboutPage->publish("Stage", "Live");

            //create a contact form response page
            $contactFormResponsePage = new SilvercartContactFormResponsePage();
            $contactFormResponsePage->Title = _t('SilvercartContactFormResponsePage.CONTACT_CONFIRMATION', 'contact confirmation');
            $contactFormResponsePage->URLSegment = _t('SilvercartContactFormResponsePage.URL_SEGMENT', 'contactconfirmation');
            $contactFormResponsePage->Status = "Published";
            $contactFormResponsePage->ShowInMenus = false;
            $contactFormResponsePage->ShowInSearch = false;
            $contactFormResponsePage->IdentifierCode = "SilvercartContactFormResponsePage";
            $contactFormResponsePage->ParentID = $rootPage->ID;
            $contactFormResponsePage->Content = _t('SilvercartContactFormResponsePage.CONTENT', 'Many thanks for Your message. Your request will be answered as soon as possible.');
            $contactFormResponsePage->write();
            $contactFormResponsePage->publish("Stage", "Live");

            //create a silvercart order confirmation page as a child of the silvercart root
            $orderConfirmationPage = new SilvercartOrderConfirmationPage();
            $orderConfirmationPage->Title = _t('SilvercartOrderConfirmationPage.SINGULARNAME', 'order conirmation page');
            $orderConfirmationPage->URLSegment = _t('SilvercartOrderConfirmationPage.URL_SEGMENT', 'order-conirmation');
            $orderConfirmationPage->Status = "Published";
            $orderConfirmationPage->ShowInMenus = false;
            $orderConfirmationPage->ShowInSearch = false;
            $orderConfirmationPage->CanViewType = "LoggedInUsers";
            $orderConfirmationPage->IdentifierCode = "SilvercartOrderConfirmationPage";
            $orderConfirmationPage->ParentID = $rootPage->ID;
            $orderConfirmationPage->write();
            $orderConfirmationPage->publish("Stage", "Live");

            //create a payment notification page as a child of the silvercart root
            $paymentNotification = new SilvercartPaymentNotification();
            $paymentNotification->URLSegment = _t('SilvercartPaymentNotification.URL_SEGMENT');
            $paymentNotification->Title = _t('SilvercartPaymentNotification.TITLE', 'payment notification');
            $paymentNotification->Status = 'Published';
            $paymentNotification->ShowInMenus = 0;
            $paymentNotification->ShowInSearch = 0;
            $paymentNotification->ParentID = $rootPage->ID;
            $paymentNotification->IdentifierCode = "SilvercartPaymentNotification";
            $paymentNotification->write();
            $paymentNotification->publish('Stage', 'Live');
            DB::alteration_message('SilvercartPaymentNotification Page created', 'created');

            //create a silvercart registration page as a child of silvercart root
            $registrationPage = new SilvercartRegistrationPage();
            $registrationPage->Title = _t('SilvercartRegistrationPage.TITLE', 'registration page');
            $registrationPage->URLSegment = _t('SilvercartRegistrationPage.URL_SEGMENT', 'registration');
            $registrationPage->Status = "Published";
            $registrationPage->ShowInMenus = false;
            $registrationPage->ShowInSearch = true;
            $registrationPage->ParentID = $rootPage->ID;
            $registrationPage->IdentifierCode = "SilvercartRegistrationPage";
            $registrationPage->write();
            $registrationPage->publish("Stage", "Live");

            //create a silvercart registration confirmation page as a child the silvercart registration page
            $registerConfirmationPage = new SilvercartRegisterConfirmationPage();
            $registerConfirmationPage->Title = _t('SilvercartRegisterConfirmationPage.TITLE', 'register confirmation page');
            $registerConfirmationPage->URLSegment = _t('SilvercartRegisterConfirmationPage.URL_SEGMENT', 'register-confirmation');
            $registerConfirmationPage->Content = _t('SilvercartRegisterConfirmationPage.CONTENT');
            $registerConfirmationPage->Status = "Published";
            $registerConfirmationPage->ParentID = $registrationPage->ID;
            $registerConfirmationPage->ShowInMenus = false;
            $registerConfirmationPage->ShowInSearch = false;
            $registerConfirmationPage->IdentifierCode = "SilvercartRegisterConfirmationPage";
            $registerConfirmationPage->write();
            $registerConfirmationPage->publish("Stage", "Live");

            //create a silvercart search results page as a child of the silvercart root
            $searchResultsPage = new SilvercartSearchResultsPage();
            $searchResultsPage->Title = _t('SilvercartSearchResultsPage.TITLE', 'search results');
            $searchResultsPage->URLSegment = _t('SilvercartSearchResultsPage.URL_SEGMENT', 'search-results');
            $searchResultsPage->Status = "Published";
            $searchResultsPage->ShowInMenus = false;
            $searchResultsPage->ShowInSearch = false;
            $searchResultsPage->ParentID = $rootPage->ID;
            $searchResultsPage->IdentifierCode = "SilvercartSearchResultsPage";
            $searchResultsPage->write();
            $searchResultsPage->publish("Stage", "Live");

            // Create a SilvercartNewsletterPage as a child of the Silvercart root node.
            $newsletterPage                 = new SilvercartNewsletterPage();
            $newsletterPage->Title          = _t('SilvercartNewsletterPage.TITLE', 'Newsletter');
            $newsletterPage->URLSegment     = _t('SilvercartNewsletterPage.URL_SEGMENT', 'newsletter');
            $newsletterPage->Status         = "Published";
            $newsletterPage->ShowInMenus    = true;
            $newsletterPage->ShowInSearch   = true;
            $newsletterPage->ParentID       = $metaNavigationHolder->ID;
            $newsletterPage->IdentifierCode = "SilvercartNewsletterPage";
            $newsletterPage->write();
            $newsletterPage->publish("Stage", "Live");

            // Create a SilvercartNewsletterResponsePage as a child of the SilvercartNewsletterPage node.
            $newsletterResponsePage                 = new SilvercartNewsletterResponsePage();
            $newsletterResponsePage->Title          = _t('SilvercartNewsletterResponsePage.TITLE', 'Newsletter Status');
            $newsletterResponsePage->URLSegment     = _t('SilvercartNewsletterResponsePage.URL_SEGMENT', 'newsletter_status');
            $newsletterResponsePage->Status         = "Published";
            $newsletterResponsePage->ShowInMenus    = false;
            $newsletterResponsePage->ShowInSearch   = false;
            $newsletterResponsePage->ParentID       = $newsletterPage->ID;
            $newsletterResponsePage->IdentifierCode = "SilvercartNewsletterResponsePage";
            $newsletterResponsePage->write();
            $newsletterResponsePage->publish("Stage", "Live");
            
            //create a silvercart newsletter opt-in confirmation page
            $newsletterOptInConfirmationPage = new SilvercartNewsletterOptInConfirmationPage();
            $newsletterOptInConfirmationPage->Title = _t('SilvercartNewsletterOptInConfirmationPage.TITLE', 'register confirmation page');
            $newsletterOptInConfirmationPage->URLSegment = _t('SilvercartNewsletterOptInConfirmationPage.URL_SEGMENT', 'register-confirmation');
            $newsletterOptInConfirmationPage->Content = _t('SilvercartNewsletterOptInConfirmationPage.CONTENT');
            $newsletterOptInConfirmationPage->ConfirmationFailureMessage = _t('SilvercartNewsletterOptInConfirmationPage.CONFIRMATIONFAILUREMESSAGE');
            $newsletterOptInConfirmationPage->ConfirmationSuccessMessage = _t('SilvercartNewsletterOptInConfirmationPage.CONFIRMATIONSUCCESSMESSAGE');
            $newsletterOptInConfirmationPage->AlreadyConfirmedMessage = _t('SilvercartNewsletterOptInConfirmationPage.ALREADYCONFIRMEDMESSAGE');
            $newsletterOptInConfirmationPage->Status = "Published";
            $newsletterOptInConfirmationPage->ParentID = $newsletterPage->ID;
            $newsletterOptInConfirmationPage->ShowInMenus = false;
            $newsletterOptInConfirmationPage->ShowInSearch = false;
            $newsletterOptInConfirmationPage->IdentifierCode = "SilvercartNewsletterOptInConfirmationPage";
            $newsletterOptInConfirmationPage->write();
            $newsletterOptInConfirmationPage->publish("Stage", "Live");
        }

        /*
         * create shop emails
         */
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

        $this->extend('updateDefaultRecords', $rootPage);

        self::createTestConfiguration();
        self::createTestData();
    }

    /**
     * Requires all default countries or syncs them if GeoNames is activated.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2011
     */
    public function requireOrUpdateCountries() {
        $config = SilvercartConfig::getConfig();
        if ($config->GeoNamesActive) {
            $geoNames = new SilvercartGeoNames($config->GeoNamesUserName, $config->GeoNamesAPI);
            $geoNames->countryInfo();
        } elseif (!DataObject::get('SilvercartCountry')) {
            require_once(Director::baseFolder() . '/silvercart/code/config/SilvercartRequireDefaultCountries.php');
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
            $manufacturer->Title = 'Testmanufacturer';
            $manufacturer->URL = 'http://www.silvercart.org/';
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
                    'Title'                     => _t('SilvercartTestData.PRODUCTPAYMENTPAYPAL_TITLE'),
                    'PriceGrossAmount'          => 9.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 9.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 9.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 9.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'ShortDescription'          => _t('SilvercartTestData.PRODUCTPAYMENTPAYPAL_SHORTDESC'),
                    'LongDescription'           => _t('SilvercartTestData.PRODUCTPAYMENTPAYPAL_LONGDESC'),
                    'MetaDescription'           => _t('SilvercartTestData.PRODUCTPAYMENTPAYPAL_SHORTDESC'),
                    'MetaTitle'                 => _t('SilvercartTestData.PRODUCTPAYMENTPAYPAL_TITLE'),
                    'MetaKeywords'              => _t('SilvercartTestData.PRODUCTPAYMENTPAYPAL_KEYWORDS'),
                    'Weight'                    => 250,
                    'StockQuantity'             => 5,
                    'ProductNumberShop'         => '10001',
                    'ProductNumberManufacturer' => 'SC_Mod_100',
                    'SilvercartProductGroupID'  => $productGroupPayment->ID,
                    'productImage'              => 'logopaypal.jpg',
                    'sortOrder'                 => 1
                ),
                array(
                    'Title'                     => _t('SilvercartTestData.PRODUCTPAYMENTIPAYMENT_TITLE'),
                    'PriceGrossAmount'          => 18.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 18.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 18.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 18.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'ShortDescription'          => _t('SilvercartTestData.PRODUCTPAYMENTIPAYMENT_SHORTDESC'),
                    'LongDescription'           => _t('SilvercartTestData.PRODUCTPAYMENTIPAYMENT_LONGDESC'),
                    'MetaDescription'           => _t('SilvercartTestData.PRODUCTPAYMENTIPAYMENT_SHORTDESC'),
                    'MetaTitle'                 => _t('SilvercartTestData.PRODUCTPAYMENTIPAYMENT_TITLE'),
                    'MetaKeywords'              => _t('SilvercartTestData.PRODUCTPAYMENTIPAYMENT_KEYWORDS'),
                    'Weight'                    => 260,
                    'StockQuantity'             => 3,
                    'ProductNumberShop'         => '10002',
                    'ProductNumberManufacturer' => 'SC_Mod_101',
                    'SilvercartProductGroupID'  => $productGroupPayment->ID,
                    'productImage'              => 'logoipayment.gif',
                    'sortOrder'                 => 2
                ),
                array(
                    'Title'                     => _t('SilvercartTestData.PRODUCTPAYMENTSAFERPAY_TITLE'),
                    'PriceGrossAmount'          => 36.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 36.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 36.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 36.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'ShortDescription'          => _t('SilvercartTestData.PRODUCTPAYMENTSAFERPAY_SHORTDESC'),
                    'LongDescription'           => _t('SilvercartTestData.PRODUCTPAYMENTSAFERPAY_LONGDESC'),
                    'MetaDescription'           => _t('SilvercartTestData.PRODUCTPAYMENTSAFERPAY_SHORTDESC'),
                    'MetaTitle'                 => _t('SilvercartTestData.PRODUCTPAYMENTSAFERPAY_TITLE'),
                    'MetaKeywords'              => _t('SilvercartTestData.PRODUCTPAYMENTSAFERPAY_KEYWORDS'),
                    'Weight'                    => 270,
                    'StockQuantity'             => 12,
                    'ProductNumberShop'         => '10003',
                    'ProductNumberManufacturer' => 'SC_Mod_102',
                    'SilvercartProductGroupID'  => $productGroupPayment->ID,
                    'productImage'              => 'logosaferpay.jpg',
                    'sortOrder'                 => 3
                ),
                array(
                    'Title'                     => _t('SilvercartTestData.PRODUCTPAYMENTPREPAYMENT_TITLE'),
                    'PriceGrossAmount'          => 27.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 27.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 27.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 27.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'ShortDescription'          => _t('SilvercartTestData.PRODUCTPAYMENTPREPAYMENT_SHORTDESC'),
                    'LongDescription'           => _t('SilvercartTestData.PRODUCTPAYMENTPREPAYMENT_LONGDESC'),
                    'MetaDescription'           => _t('SilvercartTestData.PRODUCTPAYMENTPREPAYMENT_SHORTDESC'),
                    'MetaTitle'                 => _t('SilvercartTestData.PRODUCTPAYMENTPREPAYMENT_TITLE'),
                    'MetaKeywords'              => _t('SilvercartTestData.PRODUCTPAYMENTPREPAYMENT_KEYWORDS'),
                    'Weight'                    => 290,
                    'StockQuantity'             => 9,
                    'ProductNumberShop'         => '10004',
                    'ProductNumberManufacturer' => 'SC_Mod_103',
                    'SilvercartProductGroupID'  => $productGroupPayment->ID,
                    'productImage'              => 'logoprepayment.png',
                    'sortOrder'                 => 4
                ),
                array(
                    'Title'                     => _t('SilvercartTestData.PRODUCTMARKETINGCROSSSELLING_TITLE'),
                    'PriceGrossAmount'          => 12.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 12.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 12.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 12.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'ShortDescription'          => _t('SilvercartTestData.PRODUCTMARKETINGCROSSSELLING_SHORTDESC'),
                    'LongDescription'           => _t('SilvercartTestData.PRODUCTMARKETINGCROSSSELLING_LONGDESC'),
                    'MetaDescription'           => _t('SilvercartTestData.PRODUCTMARKETINGCROSSSELLING_SHORTDESC'),
                    'MetaTitle'                 => _t('SilvercartTestData.PRODUCTMARKETINGCROSSSELLING_TITLE'),
                    'MetaKeywords'              => _t('SilvercartTestData.PRODUCTMARKETINGCROSSSELLING_KEYWORDS'),
                    'Weight'                    => 145,
                    'StockQuantity'             => 26,
                    'ProductNumberShop'         => '10006',
                    'ProductNumberManufacturer' => 'SC_Mod_104',
                    'SilvercartProductGroupID'  => $productGroupMarketing->ID,
                    'productImage'              => 'logocrossselling.png',
                    'sortOrder'                 => 1
                ),
                array(
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
                    'Title'                     => _t('SilvercartTestData.PRODUCTMARKETINGPROTECTEDSHOPS_TITLE'),
                    'PriceGrossAmount'          => 49.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 49.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 49.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 49.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'ShortDescription'          => _t('SilvercartTestData.PRODUCTMARKETINGPROTECTEDSHOPS_SHORTDESC'),
                    'LongDescription'           => _t('SilvercartTestData.PRODUCTMARKETINGPROTECTEDSHOPS_LONGDESC'),
                    'MetaDescription'           => _t('SilvercartTestData.PRODUCTMARKETINGPROTECTEDSHOPS_SHORTDESC'),
                    'MetaTitle'                 => _t('SilvercartTestData.PRODUCTMARKETINGPROTECTEDSHOPS_TITLE'),
                    'MetaKeywords'              => _t('SilvercartTestData.PRODUCTMARKETINGPROTECTEDSHOPS_KEYWORDS'),
                    'Weight'                    => 75,
                    'StockQuantity'             => 101,
                    'ProductNumberShop'         => '10008',
                    'ProductNumberManufacturer' => 'SC_Mod_106',
                    'SilvercartProductGroupID'  => $productGroupMarketing->ID,
                    'productImage'              => 'logoprotectedshops.jpg',
                    'sortOrder'                 => 3
                ),
                array(
                    'Title'                     => _t('SilvercartTestData.PRODUCTOTHERSDHL_TITLE'),
                    'PriceGrossAmount'          => 27.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 27.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 27.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 27.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'ShortDescription'          => _t('SilvercartTestData.PRODUCTOTHERSDHL_SHORTDESC'),
                    'LongDescription'           => _t('SilvercartTestData.PRODUCTOTHERSDHL_LONGDESC'),
                    'MetaDescription'           => _t('SilvercartTestData.PRODUCTOTHERSDHL_SHORTDESC'),
                    'MetaTitle'                 => _t('SilvercartTestData.PRODUCTOTHERSDHL_TITLE'),
                    'MetaKeywords'              => _t('SilvercartTestData.PRODUCTOTHERSDHL_KEYWORDS'),
                    'Weight'                    => 95,
                    'StockQuantity'             => 12,
                    'ProductNumberShop'         => '10009',
                    'ProductNumberManufacturer' => 'SC_Mod_107',
                    'SilvercartProductGroupID'  => $productGroupOthers->ID,
                    'productImage'              => 'logodhl.jpg',
                    'sortOrder'                 => 1
                ),
                array(
                    'Title'                     => _t('SilvercartTestData.PRODUCTOTHERSSOLR_TITLE'),
                    'PriceGrossAmount'          => 9.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 9.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 9.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 9.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'ShortDescription'          => _t('SilvercartTestData.PRODUCTOTHERSSOLR_SHORTDESC'),
                    'LongDescription'           => _t('SilvercartTestData.PRODUCTOTHERSSOLR_LONGDESC'),
                    'MetaDescription'           => _t('SilvercartTestData.PRODUCTOTHERSSOLR_SHORTDESC'),
                    'MetaTitle'                 => _t('SilvercartTestData.PRODUCTOTHERSSOLR_TITLE'),
                    'MetaKeywords'              => _t('SilvercartTestData.PRODUCTOTHERSSOLR_KEYWORDS'),
                    'Weight'                    => 25,
                    'StockQuantity'             => 0,
                    'ProductNumberShop'         => '10010',
                    'ProductNumberManufacturer' => 'SC_Mod_108',
                    'SilvercartProductGroupID'  => $productGroupOthers->ID,
                    'productImage'              => 'logosolr.png',
                    'sortOrder'                 => 2
                ),
                array(
                    'Title'                     => _t('SilvercartTestData.PRODUCTOTHERSPDFINVOICE_TITLE'),
                    'PriceGrossAmount'          => 18.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 18.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 18.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 18.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'ShortDescription'          => _t('SilvercartTestData.PRODUCTOTHERSPDFINVOICE_SHORTDESC'),
                    'LongDescription'           => _t('SilvercartTestData.PRODUCTOTHERSPDFINVOICE_LONGDESC'),
                    'MetaDescription'           => _t('SilvercartTestData.PRODUCTOTHERSPDFINVOICE_SHORTDESC'),
                    'MetaTitle'                 => _t('SilvercartTestData.PRODUCTOTHERSPDFINVOICE_TITLE'),
                    'MetaKeywords'              => _t('SilvercartTestData.PRODUCTOTHERSPDFINVOICE_KEYWORDS'),
                    'Weight'                    => 173,
                    'StockQuantity'             => 14,
                    'ProductNumberShop'         => '10011',
                    'ProductNumberManufacturer' => 'SC_Mod_109',
                    'SilvercartProductGroupID'  => $productGroupOthers->ID,
                    'productImage'              => 'logopdfinvoice.jpg',
                    'sortOrder'                 => 3
                ),
                array(
                    'Title'                     => _t('SilvercartTestData.PRODUCTOTHERSVOUCHERS_TITLE'),
                    'PriceGrossAmount'          => 32.99,
                    'PriceGrossCurrency'        => _t('SilvercartTestData.CURRENCY'),
                    'PriceNetAmount'            => 32.99 / 119 * 100,
                    'PriceNetCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'MSRPriceAmount'            => 32.99 / 100 * 120,
                    'MSRPriceCurrency'          => _t('SilvercartTestData.CURRENCY'),
                    'PurchasePriceAmount'       => 32.99,
                    'PurchasePriceCurrency'     => _t('SilvercartTestData.CURRENCY'),
                    'ShortDescription'          => _t('SilvercartTestData.PRODUCTOTHERSVOUCHERS_SHORTDESC'),
                    'LongDescription'           => _t('SilvercartTestData.PRODUCTOTHERSVOUCHERS_LONGDESC'),
                    'MetaDescription'           => _t('SilvercartTestData.PRODUCTOTHERSVOUCHERS_SHORTDESC'),
                    'MetaTitle'                 => _t('SilvercartTestData.PRODUCTOTHERSVOUCHERS_TITLE'),
                    'MetaKeywords'              => _t('SilvercartTestData.PRODUCTOTHERSVOUCHERS_KEYWORDS'),
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
            $exampleDataDir = Director::baseFolder().'/assets/'._t('SilvercartTestData.IMAGEFOLDERNAME');
            $imageFolder = new Folder();
            $imageFolder->setName(_t('SilvercartTestData.IMAGEFOLDERNAME'));
            $imageFolder->write();
            
            if (!file_exists($exampleDataDir)) {
                mkdir($exampleDataDir);
            }
            
            // Create products
            foreach ($products as $product) {
                $productItem                            = new SilvercartProduct();
                $productItem->SilvercartTaxID           = $taxRateID;
                $productItem->SilvercartManufacturerID  = $manufacturer->ID;
                $productItem->Title                     = $product['Title'];
                $productItem->ShortDescription          = $product['ShortDescription'];
                $productItem->LongDescription           = $product['LongDescription'];
                $productItem->MetaDescription           = $product['MetaDescription'];
                $productItem->MetaTitle                 = $product['MetaTitle'];
                $productItem->MetaKeywords              = $product['MetaKeywords'];
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
            $widgetFrontPageContent1->setField('sortOrder', 1);
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
            $widgetFrontPageContent2->setField('sortOrder', 2);
            $widgetFrontPageContent2->write();

            $widgetSetFrontPageContentArea->Widgets()->add($widgetFrontPageContent2);

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
            $widgetFrontPageSidebar1->setField('sortOrder', 1);
            $widgetFrontPageSidebar1->write();

            $widgetSetFrontPageSidebarArea->Widgets()->add($widgetFrontPageSidebar1);
            
            $widgetFrontPageSidebar2 = new SilvercartShoppingCartWidget();
            $widgetFrontPageSidebar2->setField('sortOrder', 2);
            $widgetFrontPageSidebar2->write();

            $widgetSetFrontPageSidebarArea->Widgets()->add($widgetFrontPageSidebar2);
            
            $widgetFrontPageSidebar3 = new SilvercartLoginWidget();
            $widgetFrontPageSidebar3->setField('sortOrder', 3);
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
            $widgetProductGroupPageSidebar1->setField('sortOrder', 1);
            $widgetProductGroupPageSidebar1->write();

            $widgetSetProductGroupPagesSidebarArea->Widgets()->add($widgetProductGroupPageSidebar1);
            
            $widgetProductGroupPageSidebar2 = new SilvercartShoppingCartWidget();
            $widgetProductGroupPageSidebar2->setField('sortOrder', 2);
            $widgetProductGroupPageSidebar2->write();

            $widgetSetProductGroupPagesSidebarArea->Widgets()->add($widgetProductGroupPageSidebar2);
            
            $widgetProductGroupPageSidebar3 = new SilvercartLoginWidget();
            $widgetProductGroupPageSidebar3->setField('sortOrder', 3);
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

                //relate carrier to zones
                $zoneDomestic = DataObject::get_one("SilvercartZone", sprintf("`Title` = '%s'", _t('SilvercartZone.DOMESTIC', 'domestic')));
                if (!$zoneDomestic) {
                    $zoneDomestic = new SilvercartZone();
                    $zoneDomestic->Title = _t('SilvercartZone.DOMESTIC', 'domestic');
                }
                $zoneDomestic->SilvercartCarrierID = $carrier->ID;
                $zoneDomestic->write();

                $ZoneEu = DataObject::get_one("SilvercartZone", "`Title` = 'EU'");
                if (!$ZoneEu) {
                    $ZoneEu = new SilvercartZone();
                    $ZoneEu->Title = 'EU';
                }
                $ZoneEu->SilvercartCarrierID = $carrier->ID;
                $ZoneEu->write();

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
                $zoneDomestic->SilvercartCountries()->add($country);
                
                // create if not exists, activate and relate payment method
                $paymentMethod = DataObject::get_one('SilvercartPaymentPrepayment');
                if (!$paymentMethod) {
                    $paymentMethod = new SilvercartPaymentPrepayment();
                    $paymentMethod->Name = _t('SilvercartPaymentPrepayment.SINGULARNAME');
                }
                $paymentMethod->isActive = true;
                $orderStatusPending = DataObject::get_one("SilvercartOrderStatus", "`Code` = 'pending'");
                if ($orderStatusPending) {
                    $paymentMethod->orderStatus = $orderStatusPending->Code;
                }
                $paymentMethod->write();
                $country->SilvercartPaymentMethods()->add($paymentMethod);

                // create a shipping method
                $shippingMethod = DataObject::get_one("SilvercartShippingMethod", sprintf("`Title` = '%s'", _t('SilvercartShippingMethod.PACKAGE', 'package')));
                if (!$shippingMethod) {
                    $shippingMethod = new SilvercartShippingMethod();
                    $shippingMethod->Title = _t('SilvercartShippingMethod.PACKAGE', 'package');
                    //relate shipping method to carrier
                    $shippingMethod->SilvercartCarrierID = $carrier->ID;
                }
                $shippingMethod->isActive = 1;
                $shippingMethod->write();
                $shippingMethod->SilvercartZones()->add($zoneDomestic);

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
            $taxRate = DataObject::get_one(
                            'SilvercartTax'
            );

            if (!$taxRate) {
                $lowerTaxRate = new SilvercartTax();
                $lowerTaxRate->setField('Rate', 7);
                $lowerTaxRate->setField('Title', '7%');
                $lowerTaxRate->write();

                $higherTaxRate = new SilvercartTax();
                $higherTaxRate->setField('Rate', 19);
                $higherTaxRate->setField('Title', '19%');
                $higherTaxRate->write();
            }
        }
    }
}
