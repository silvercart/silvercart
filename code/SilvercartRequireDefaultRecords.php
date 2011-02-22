<?php
/*
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
 */

/**
 * collects all default records to avoid redundant code when it comes to relations
 * you do not need to search for other default records, they are all here
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 16.02.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartRequireDefaultRecords extends DataObject {

    protected static $enableTestData = false;

    /**
     * create default records
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.02.2011
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        // Create an own group for SilvercartAnonymousCustomer. The group is identified by "Code", so its name can be changed via backend.
        $anonymousGroup = DataObject::get_one('Group', "`Code` = 'anonymous'");
        if (!$anonymousGroup) {
            $anonymousGroup = new Group();
            $anonymousGroup->Title = _t('SilvercartAnonymousCustomer.ANONYMOUSCUSTOMER', 'anonymous customer');
            $anonymousGroup->Code = "anonymous";
            $anonymousGroup->write();
        }

        // Create an own group for b2b customers. The group is identified by "Code", so its name can be changed via backend.
        $B2Bgroup = DataObject::get_one('Group', "`Code` = 'b2b'");
        if (!$B2Bgroup) {
            $B2Bgroup = new Group();
            $B2Bgroup->Title = _t('SilvercartBusinessCustomer.BUSINESSCUSTOMER', 'business customer');
            $B2Bgroup->Code = "b2b";
            $B2Bgroup->write();
        }

        //create a group for b2c customers
        $B2Cgroup = DataObject::get_one('Group', "`Code` = 'b2c'");
        if (!$B2Cgroup) {
            $B2Cgroup = new Group();
            $B2Cgroup->Title = _t('SilvercartRegularCustomer.REGULARCUSTOMER', 'regular customer');
            $B2Cgroup->Code = "b2c";
            $B2Cgroup->write();
        }

        //create a group for b2c optin
        $B2C_optinGroup = DataObject::get_one('Group', "`Code` = 'b2c-optin'");
        if (!$B2C_optinGroup) {
            $B2C_optinGroup = new Group();
            $B2C_optinGroup->Title = _t("SilvercartRegularCustomer.REGULARCUSTOMER_OPTIN", "regular customer unconfirmed");
            $B2C_optinGroup->Code = "b2c-optin";
            $B2C_optinGroup->write();
        }



        //create a carrier and an associated zone and shipping method
        $carrier = DataObject::get_one('SilvercartCarrier');
        if (!$carrier) {
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
                $ZoneEu->SilvercartCarrierID = $carrier->ID;
                $ZoneEu->write();
            }
            $country = DataObject::get_one('SilvercartCountry');
            if (!$country) {
                $country = new SilvercartCountry();
                $country->Title = 'Deutschland';
                $country->ISO2 = 'de';
                $country->ISO3 = 'deu';
                $country->write();
                $zoneDomestic->SilvercartCountries()->add($country);
            }

            // create a shipping method
            $shippingMethod = DataObject::get_one("SilvercartShippingMethod", sprintf("`Title` = '%s'", _t('SilvercartShippingMethod.PACKAGE', 'package')));
            if (!$shippingMethod) {
                $shippingMethod = new SilvercartShippingMethod();
                $shippingMethod->Title = _t('SilvercartShippingMethod.PACKAGE', 'package');
                //relate shipping method to carrier
                $shippingMethod->SilvercartCarrierID = $carrier->ID;
                $shippingMethod->write();
            }

            // create two standard tax rates
            $lowerTaxRate = DataObject::get_one(
                            'SilvercartTax',
                            "Rate = 7"
            );

            if (!$lowerTaxRate) {
                $lowerTaxRate = new SilvercartTax();
                $lowerTaxRate->setField('Rate', 7);
                $lowerTaxRate->setField('Title', '7%');
                $lowerTaxRate->write();
            }

            $higherTaxRate = DataObject::get_one(
                            'SilvercartTax',
                            "Rate = 19"
            );

            if (!$higherTaxRate) {
                $higherTaxRate = new SilvercartTax();
                $higherTaxRate->setField('Rate', 19);
                $higherTaxRate->setField('Title', '19%');
                $higherTaxRate->write();
            }
            // create a shipping fee and relate it to zone, tax and shipping method
            $shippingFee = DataObject::get_one('SilvercartShippingFee');
            if (!$shippingFee) {
                $shippingFee = new SilvercartShippingFee();
                $shippingFee->MaximumWeight = '1000';
                $shippingFee->Price = new Money();
                $shippingFee->Price->setAmount('3.9');
                $shippingFee->Price->setCurrency('EUR');
                $shippingFee->SilvercartShippingMethodID = $shippingMethod->ID;
                $shippingFee->SilvercartZoneID = $zoneDomestic->ID;
                $shippingFee->SilvercartTaxID = $higherTaxRate->ID;
                $shippingFee->write();
            }
        }

        //create order stati
        if (!DataObject::get_one('SilvercartOrderStatus')) {

            $defaultStatusEntries = array(
                'pending' => _t('SilvercartOrderStatus.WAITING_FOR_PAYMENT', 'waiting for payment', null, 'Auf Zahlungseingang wird gewartet'),
                'payed' => _t('SilvercartOrderStatus.PAYED', 'payed')
            );

            foreach ($defaultStatusEntries as $code => $title) {
                $obj = new SilvercartOrderStatus();
                $obj->Title = $title;
                $obj->Code = $code;
                $obj->write();
            }
        }

        /*
         * and now the whole site tree
         */

        $rootPage = DataObject::get_one('Page', "Title = 'Silvercart'");
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

            //create a silvercart product category holder as a child of silvercart root
            $productCategoryHolder = new SilvercartProductCategoryHolder();
            $productCategoryHolder->Title = _t('SilvercartProductCategoryHolder.TITLE', 'category overview');
            $productCategoryHolder->URLSegment = _t('SilvercartProductCategoryHolder.URL_SEGMENT', 'categoryoverview');
            $productCategoryHolder->Status = "Published";
            $productCategoryHolder->ShowInMenus = true;
            $productCategoryHolder->ShowInSearch = true;
            $productCategoryHolder->IdentifierCode = "SilvercartProductCategoryHolder";
            $productCategoryHolder->ParentID = $rootPage->ID;
            $productCategoryHolder->write();
            $productCategoryHolder->publish("Stage", "Live");

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
            $myAccountHolder->CanViewType = "OnlyTheseUsers";
            $myAccountHolder->ParentID = $rootPage->ID;
            $myAccountHolder->IdentifierCode = "SilvercartMyAccountHolder";
            $myAccountHolder->write();
            $myAccountHolder->publish("Stage", "Live");
            $myAccountHolder->ViewerGroups()->add($B2Bgroup);
            $myAccountHolder->ViewerGroups()->add($B2Cgroup);

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
            $orderHolder->Title = _t('SilvercartOrderHolder.TITLE', 'my oders');
            $orderHolder->URLSegment = 'my-oders';
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
            $termsOfServicePage = new Page();
            $termsOfServicePage->Title = _t('SilvercartPage.TITLE_TERMS', 'terms of service');
            $termsOfServicePage->URLSegment = _t('SilvercartPage.URL_SEGMENT_TERMS', 'terms-of-service');
            $termsOfServicePage->Status = "Published";
            $termsOfServicePage->ShowInMenus = 1;
            $termsOfServicePage->ParentID = $metaNavigationHolder->ID;
            $termsOfServicePage->IdentifierCode = "TermsOfServicePage";
            $termsOfServicePage->write();
            $termsOfServicePage->publish("Stage", "Live");

            //create an imprint page as a child of the meta navigation holder
            $imprintPage = new Page();
            $imprintPage->Title = _t('SilvercartPage.TITLE_IMPRINT', 'imprint');
            $imprintPage->URLSegment = _t('SilvercartPage.URL_SEGMENT_IMPRINT', 'imprint');
            $imprintPage->Status = "Published";
            $imprintPage->ShowInMenus = 1;
            $imprintPage->ParentID = $metaNavigationHolder->ID;
            $imprintPage->IdentifierCode = "ImprintPage";
            $imprintPage->write();
            $imprintPage->publish("Stage", "Live");

            //create a data privacy statement page as a child of the meta navigation holder
            $dataPrivacyStatementPage = new SilvercartDataPrivacyStatementPage();
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
            $registerConfirmationPage->ConfirmationFailureMessage = _t('SilvercartRegisterConfirmationPage.CONFIRMATIONFAILUREMESSAGE');
            $registerConfirmationPage->ConfirmationSuccessMessage = _t('SilvercartRegisterConfirmationPage.CONFIRMATIONSUCCESSMESSAGE');
            $registerConfirmationPage->AlreadyConfirmedMessage = _t('SilvercartRegisterConfirmationPage.ALREADYCONFIRMEDMESSAGE');
            $registerConfirmationPage->Status = "Published";
            $registerConfirmationPage->ParentID = $registrationPage->ID;
            $registerConfirmationPage->ShowInMenus = false;
            $registerConfirmationPage->ShowInSearch = false;
            $registerConfirmationPage->IdentifierCode = "SilvercartRegisterConfirmationPage";
            $registerConfirmationPage->write();
            $registerConfirmationPage->publish("Stage", "Live");

            //create a registration welcome page as a child of the registration page+
            $registrationWelcomePage = new Page();
            $registrationWelcomePage->Title = _t('SilvercartPage.WELCOME_PAGE_TITLE', 'welcome');
            $registrationWelcomePage->URLSegment = _t('SilvercartPage.WELCOME_PAGE_URL_SEGMENT');
            $registrationWelcomePage->Content = _t('SilvercartRegisterWelcomePage.CONTENT');
            $registrationWelcomePage->Status = "Published";
            $registrationWelcomePage->ParentID = $registrationPage->ID;
            $registrationWelcomePage->ShowInMenus = false;
            $registrationWelcomePage->ShowInSearch = false;
            $registrationWelcomePage->IdentifierCode = "RegistrationWelcomePage";
            $registrationWelcomePage->write();
            $registrationWelcomePage->publish("Stage", "Live");

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
        }
        self::createTestData();
    }

    /**
     * enables the creation of test data on /dev/build
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2011
     */
    public static function enableTestData() {
        self::$enableTestData = true;
    }

    /**
     * disables the creation of test data on /dev/build
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2011
     */
    public static function disableTestData() {
        self::$enableTestData= false;
    }

    /**
     * creates test data on /dev/build
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2011
     */
    public static function createTestData() {
        if (self::$enableTestData === true) {
            // get SilvercartProductGroupHolder
            $silvercartProductGroupHolder = DataObject::get_one('SilvercartProductGroupHolder');
            if (SiteTree::get_by_link(_t('TestGroup1.URL_SEGMENT', 'testgroup1'))) {
                // test data already created
                return;
            }
            // create test product groups
            $testgroup1 = new SilvercartProductGroupPage();
            $testgroup1->ParentID = $silvercartProductGroupHolder->ID;
            $testgroup1->Title = _t('TestGroup1.TITLE', 'TestGroup1');
            $testgroup1->MenuTitle = _t('TestGroup1.TITLE', 'TestGroup1');
            $testgroup1->URLSegment = _t('TestGroup1.URL_SEGMENT', 'testgroup1');
            $testgroup1->Content = _t('TestGroup1.CONTENT', 'TestGroup1');
            $testgroup1->Status = "Published";
            $testgroup1->ShowInMenus = true;
            $testgroup1->ShowInSearch = false;
            $testgroup1->IdentifierCode = "SilvercartSearchResultsPage";
            $testgroup1->write();
            $testgroup1->publish("Stage", "Live");

            $testgroup2 = new SilvercartProductGroupPage();
            $testgroup2->ParentID = $silvercartProductGroupHolder->ID;
            $testgroup2->Title = _t('TestGroup2.TITLE', 'TestGroup2');
            $testgroup2->MenuTitle = _t('TestGroup2.TITLE', 'TestGroup2');
            $testgroup2->URLSegment = _t('TestGroup2.URL_SEGMENT', 'TestGroup2');
            $testgroup2->Content = _t('TestGroup2.CONTENT', 'TestGroup2');
            $testgroup2->Status = "Published";
            $testgroup2->ShowInMenus = true;
            $testgroup2->ShowInSearch = false;
            $testgroup2->IdentifierCode = "SilvercartSearchResultsPage";
            $testgroup2->write();
            $testgroup2->publish("Stage", "Live");
            
            $testgroup3 = new SilvercartProductGroupPage();
            $testgroup3->ParentID = $silvercartProductGroupHolder->ID;
            $testgroup3->Title = _t('TestGroup3.TITLE', 'TestGroup3');
            $testgroup3->MenuTitle = _t('TestGroup3.TITLE', 'TestGroup3');
            $testgroup3->URLSegment = _t('TestGroup3.URL_SEGMENT', 'TestGroup3');
            $testgroup3->Content = _t('TestGroup3.CONTENT', 'TestGroup3');
            $testgroup3->Status = "Published";
            $testgroup3->ShowInMenus = true;
            $testgroup3->ShowInSearch = false;
            $testgroup3->IdentifierCode = "SilvercartSearchResultsPage";
            $testgroup3->write();
            $testgroup3->publish("Stage", "Live");

            $testgroup4 = new SilvercartProductGroupPage();
            $testgroup4->ParentID = $silvercartProductGroupHolder->ID;
            $testgroup4->Title = _t('TestGroup4.TITLE', 'TestGroup4');
            $testgroup4->MenuTitle = _t('TestGroup4.TITLE', 'TestGroup4');
            $testgroup4->URLSegment = _t('TestGroup4.URL_SEGMENT', 'TestGroup4');
            $testgroup4->Content = _t('TestGroup4.CONTENT', 'TestGroup4');
            $testgroup4->Status = "Published";
            $testgroup4->ShowInMenus = true;
            $testgroup4->ShowInSearch = false;
            $testgroup4->IdentifierCode = "SilvercartSearchResultsPage";
            $testgroup4->write();
            $testgroup4->publish("Stage", "Live");

            // create test manufacturer
            $testmanufacturer1 = new SilvercartManufacturer();
            $testmanufacturer1->Title = _t('TestManufacturer1.TITLE', 'TestManufacturer1');
            $testmanufacturer1->URL = _t('TestManufacturer1.URL', 'http://www.silvercart.org/');
            $testmanufacturer1->write();

            $testmanufacturer2 = new SilvercartManufacturer();
            $testmanufacturer2->Title = _t('TestManufacturer2.TITLE', 'TestManufacturer2');
            $testmanufacturer2->URL = _t('TestManufacturer2.URL', 'http://www.silvercart.org/');
            $testmanufacturer2->write();

            // create test products
            $testproduct1 = new SilvercartProduct();
            $testproduct1->Title                        = _t('TestProduct1.TITLE', 'TestProduct1');
            $testproduct1->ShortDescription             = _t('TestProduct1.SHORTDESCRIPTION', 'TestProduct1');
            $testproduct1->LongDescription              = _t('TestProduct1.LONGDESCRIPTION', 'TestProduct1');
            $testproduct1->MetaDescription              = _t('TestProduct1.METADESCRIPTION', 'TestProduct1');
            $testproduct1->MetaTitle                    = _t('TestProduct1.METATITLE', 'TestProduct1');
            $testproduct1->MetaKeywords                 = _t('TestProduct1.METAKEYWORDS', 'TestProduct1');
            $testproduct1->PurchasePrice                = new Money();
            $testproduct1->PurchasePrice->setAmount(49.90);
            $testproduct1->PurchasePrice->setCurrency('EUR');
            $testproduct1->Price                        = new Money();
            $testproduct1->Price->setAmount(49.90);
            $testproduct1->Price->setCurrency('EUR');
            $testproduct1->MSRPrice                     = new Money();
            $testproduct1->MSRPrice->setAmount(49.90);
            $testproduct1->MSRPrice->setCurrency('EUR');
            $testproduct1->Weight                       = 500;
            $testproduct1->Quantity                     = 1000;
            $testproduct1->isFreeOfCharge               = false;
            $testproduct1->ProductNumberShop            = 'TEST00001';
            $testproduct1->ProductNumberManufacturer    = 'TEST99001';
            $testproduct1->EANCode                      = '';
            $testproduct1->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct1->SilvercartManufacturerID     = $testmanufacturer1->ID;
            $testproduct1->SilvercartProductGroupID     = $testgroup1->ID;
            $testproduct1->write();

            $testproduct2 = new SilvercartProduct();
            $testproduct2->Title                        = _t('TestProduct2.TITLE', 'TestProduct2');
            $testproduct2->ShortDescription             = _t('TestProduct2.SHORTDESCRIPTION', 'TestProduct2');
            $testproduct2->LongDescription              = _t('TestProduct2.LONGDESCRIPTION', 'TestProduct2');
            $testproduct2->MetaDescription              = _t('TestProduct2.METADESCRIPTION', 'TestProduct2');
            $testproduct2->MetaTitle                    = _t('TestProduct2.METATITLE', 'TestProduct2');
            $testproduct2->MetaKeywords                 = _t('TestProduct2.METAKEYWORDS', 'TestProduct2');
            $testproduct2->PurchasePrice                = new Money();
            $testproduct2->PurchasePrice->setAmount(49.90);
            $testproduct2->PurchasePrice->setCurrency('EUR');
            $testproduct2->Price                        = new Money();
            $testproduct2->Price->setAmount(49.90);
            $testproduct2->Price->setCurrency('EUR');
            $testproduct2->MSRPrice                     = new Money();
            $testproduct2->MSRPrice->setAmount(49.90);
            $testproduct2->MSRPrice->setCurrency('EUR');
            $testproduct2->Weight                       = 500;
            $testproduct2->Quantity                     = 1000;
            $testproduct2->isFreeOfCharge               = false;
            $testproduct2->ProductNumberShop            = 'TEST00001';
            $testproduct2->ProductNumberManufacturer    = 'TEST99001';
            $testproduct2->EANCode                      = '';
            $testproduct2->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct2->SilvercartManufacturerID     = $testmanufacturer1->ID;
            $testproduct2->SilvercartProductGroupID     = $testgroup1->ID;
            $testproduct2->write();

            $testproduct3 = new SilvercartProduct();
            $testproduct3->Title                        = _t('TestProduct3.TITLE', 'TestProduct3');
            $testproduct3->ShortDescription             = _t('TestProduct3.SHORTDESCRIPTION', 'TestProduct3');
            $testproduct3->LongDescription              = _t('TestProduct3.LONGDESCRIPTION', 'TestProduct3');
            $testproduct3->MetaDescription              = _t('TestProduct3.METADESCRIPTION', 'TestProduct3');
            $testproduct3->MetaTitle                    = _t('TestProduct3.METATITLE', 'TestProduct3');
            $testproduct3->MetaKeywords                 = _t('TestProduct3.METAKEYWORDS', 'TestProduct3');
            $testproduct3->PurchasePrice                = new Money();
            $testproduct3->PurchasePrice->setAmount(49.90);
            $testproduct3->PurchasePrice->setCurrency('EUR');
            $testproduct3->Price                        = new Money();
            $testproduct3->Price->setAmount(49.90);
            $testproduct3->Price->setCurrency('EUR');
            $testproduct3->MSRPrice                     = new Money();
            $testproduct3->MSRPrice->setAmount(49.90);
            $testproduct3->MSRPrice->setCurrency('EUR');
            $testproduct3->Weight                       = 500;
            $testproduct3->Quantity                     = 1000;
            $testproduct3->isFreeOfCharge               = false;
            $testproduct3->ProductNumberShop            = 'TEST00001';
            $testproduct3->ProductNumberManufacturer    = 'TEST99001';
            $testproduct3->EANCode                      = '';
            $testproduct3->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct3->SilvercartManufacturerID     = $testmanufacturer2->ID;
            $testproduct3->SilvercartProductGroupID     = $testgroup1->ID;
            $testproduct3->write();

            $testproduct4 = new SilvercartProduct();
            $testproduct4->Title                        = _t('TestProduct4.TITLE', 'TestProduct4');
            $testproduct4->ShortDescription             = _t('TestProduct4.SHORTDESCRIPTION', 'TestProduct4');
            $testproduct4->LongDescription              = _t('TestProduct4.LONGDESCRIPTION', 'TestProduct4');
            $testproduct4->MetaDescription              = _t('TestProduct4.METADESCRIPTION', 'TestProduct4');
            $testproduct4->MetaTitle                    = _t('TestProduct4.METATITLE', 'TestProduct4');
            $testproduct4->MetaKeywords                 = _t('TestProduct4.METAKEYWORDS', 'TestProduct4');
            $testproduct4->PurchasePrice                = new Money();
            $testproduct4->PurchasePrice->setAmount(49.90);
            $testproduct4->PurchasePrice->setCurrency('EUR');
            $testproduct4->Price                        = new Money();
            $testproduct4->Price->setAmount(49.90);
            $testproduct4->Price->setCurrency('EUR');
            $testproduct4->MSRPrice                     = new Money();
            $testproduct4->MSRPrice->setAmount(49.90);
            $testproduct4->MSRPrice->setCurrency('EUR');
            $testproduct4->Weight                       = 500;
            $testproduct4->Quantity                     = 1000;
            $testproduct4->isFreeOfCharge               = false;
            $testproduct4->ProductNumberShop            = 'TEST00001';
            $testproduct4->ProductNumberManufacturer    = 'TEST99001';
            $testproduct4->EANCode                      = '';
            $testproduct4->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct4->SilvercartManufacturerID     = $testmanufacturer2->ID;
            $testproduct4->SilvercartProductGroupID     = $testgroup1->ID;
            $testproduct4->write();

            $testproduct5 = new SilvercartProduct();
            $testproduct5->Title                        = _t('TestProduct5.TITLE', 'TestProduct5');
            $testproduct5->ShortDescription             = _t('TestProduct5.SHORTDESCRIPTION', 'TestProduct5');
            $testproduct5->LongDescription              = _t('TestProduct5.LONGDESCRIPTION', 'TestProduct5');
            $testproduct5->MetaDescription              = _t('TestProduct5.METADESCRIPTION', 'TestProduct5');
            $testproduct5->MetaTitle                    = _t('TestProduct5.METATITLE', 'TestProduct5');
            $testproduct5->MetaKeywords                 = _t('TestProduct5.METAKEYWORDS', 'TestProduct5');
            $testproduct5->PurchasePrice                = new Money();
            $testproduct5->PurchasePrice->setAmount(49.90);
            $testproduct5->PurchasePrice->setCurrency('EUR');
            $testproduct5->Price                        = new Money();
            $testproduct5->Price->setAmount(49.90);
            $testproduct5->Price->setCurrency('EUR');
            $testproduct5->MSRPrice                     = new Money();
            $testproduct5->MSRPrice->setAmount(49.90);
            $testproduct5->MSRPrice->setCurrency('EUR');
            $testproduct5->Weight                       = 500;
            $testproduct5->Quantity                     = 1000;
            $testproduct5->isFreeOfCharge               = false;
            $testproduct5->ProductNumberShop            = 'TEST00001';
            $testproduct5->ProductNumberManufacturer    = 'TEST99001';
            $testproduct5->EANCode                      = '';
            $testproduct5->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct5->SilvercartManufacturerID     = $testmanufacturer1->ID;
            $testproduct5->SilvercartProductGroupID     = $testgroup2->ID;
            $testproduct5->write();

            $testproduct6 = new SilvercartProduct();
            $testproduct6->Title                        = _t('TestProduct6.TITLE', 'TestProduct6');
            $testproduct6->ShortDescription             = _t('TestProduct6.SHORTDESCRIPTION', 'TestProduct6');
            $testproduct6->LongDescription              = _t('TestProduct6.LONGDESCRIPTION', 'TestProduct6');
            $testproduct6->MetaDescription              = _t('TestProduct6.METADESCRIPTION', 'TestProduct6');
            $testproduct6->MetaTitle                    = _t('TestProduct6.METATITLE', 'TestProduct6');
            $testproduct6->MetaKeywords                 = _t('TestProduct6.METAKEYWORDS', 'TestProduct6');
            $testproduct6->PurchasePrice                = new Money();
            $testproduct6->PurchasePrice->setAmount(49.90);
            $testproduct6->PurchasePrice->setCurrency('EUR');
            $testproduct6->Price                        = new Money();
            $testproduct6->Price->setAmount(49.90);
            $testproduct6->Price->setCurrency('EUR');
            $testproduct6->MSRPrice                     = new Money();
            $testproduct6->MSRPrice->setAmount(49.90);
            $testproduct6->MSRPrice->setCurrency('EUR');
            $testproduct6->Weight                       = 500;
            $testproduct6->Quantity                     = 1000;
            $testproduct6->isFreeOfCharge               = false;
            $testproduct6->ProductNumberShop            = 'TEST00001';
            $testproduct6->ProductNumberManufacturer    = 'TEST99001';
            $testproduct6->EANCode                      = '';
            $testproduct6->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct6->SilvercartManufacturerID     = $testmanufacturer1->ID;
            $testproduct6->SilvercartProductGroupID     = $testgroup2->ID;
            $testproduct6->write();

            $testproduct7 = new SilvercartProduct();
            $testproduct7->Title                        = _t('TestProduct7.TITLE', 'TestProduct7');
            $testproduct7->ShortDescription             = _t('TestProduct7.SHORTDESCRIPTION', 'TestProduct7');
            $testproduct7->LongDescription              = _t('TestProduct7.LONGDESCRIPTION', 'TestProduct7');
            $testproduct7->MetaDescription              = _t('TestProduct7.METADESCRIPTION', 'TestProduct7');
            $testproduct7->MetaTitle                    = _t('TestProduct7.METATITLE', 'TestProduct7');
            $testproduct7->MetaKeywords                 = _t('TestProduct7.METAKEYWORDS', 'TestProduct7');
            $testproduct7->PurchasePrice                = new Money();
            $testproduct7->PurchasePrice->setAmount(49.90);
            $testproduct7->PurchasePrice->setCurrency('EUR');
            $testproduct7->Price                        = new Money();
            $testproduct7->Price->setAmount(49.90);
            $testproduct7->Price->setCurrency('EUR');
            $testproduct7->MSRPrice                     = new Money();
            $testproduct7->MSRPrice->setAmount(49.90);
            $testproduct7->MSRPrice->setCurrency('EUR');
            $testproduct7->Weight                       = 500;
            $testproduct7->Quantity                     = 1000;
            $testproduct7->isFreeOfCharge               = false;
            $testproduct7->ProductNumberShop            = 'TEST00001';
            $testproduct7->ProductNumberManufacturer    = 'TEST99001';
            $testproduct7->EANCode                      = '';
            $testproduct7->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct7->SilvercartManufacturerID     = $testmanufacturer2->ID;
            $testproduct7->SilvercartProductGroupID     = $testgroup2->ID;
            $testproduct7->write();

            $testproduct8 = new SilvercartProduct();
            $testproduct8->Title                        = _t('TestProduct8.TITLE', 'TestProduct8');
            $testproduct8->ShortDescription             = _t('TestProduct8.SHORTDESCRIPTION', 'TestProduct8');
            $testproduct8->LongDescription              = _t('TestProduct8.LONGDESCRIPTION', 'TestProduct8');
            $testproduct8->MetaDescription              = _t('TestProduct8.METADESCRIPTION', 'TestProduct8');
            $testproduct8->MetaTitle                    = _t('TestProduct8.METATITLE', 'TestProduct8');
            $testproduct8->MetaKeywords                 = _t('TestProduct8.METAKEYWORDS', 'TestProduct8');
            $testproduct8->PurchasePrice                = new Money();
            $testproduct8->PurchasePrice->setAmount(49.90);
            $testproduct8->PurchasePrice->setCurrency('EUR');
            $testproduct8->Price                        = new Money();
            $testproduct8->Price->setAmount(49.90);
            $testproduct8->Price->setCurrency('EUR');
            $testproduct8->MSRPrice                     = new Money();
            $testproduct8->MSRPrice->setAmount(49.90);
            $testproduct8->MSRPrice->setCurrency('EUR');
            $testproduct8->Weight                       = 500;
            $testproduct8->Quantity                     = 1000;
            $testproduct8->isFreeOfCharge               = false;
            $testproduct8->ProductNumberShop            = 'TEST00001';
            $testproduct8->ProductNumberManufacturer    = 'TEST99001';
            $testproduct8->EANCode                      = '';
            $testproduct8->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct8->SilvercartManufacturerID     = $testmanufacturer2->ID;
            $testproduct8->SilvercartProductGroupID     = $testgroup2->ID;
            $testproduct8->write();

            $testproduct9 = new SilvercartProduct();
            $testproduct9->Title                        = _t('TestProduct9.TITLE', 'TestProduct9');
            $testproduct9->ShortDescription             = _t('TestProduct9.SHORTDESCRIPTION', 'TestProduct9');
            $testproduct9->LongDescription              = _t('TestProduct9.LONGDESCRIPTION', 'TestProduct9');
            $testproduct9->MetaDescription              = _t('TestProduct9.METADESCRIPTION', 'TestProduct9');
            $testproduct9->MetaTitle                    = _t('TestProduct9.METATITLE', 'TestProduct9');
            $testproduct9->MetaKeywords                 = _t('TestProduct9.METAKEYWORDS', 'TestProduct9');
            $testproduct9->PurchasePrice                = new Money();
            $testproduct9->PurchasePrice->setAmount(49.90);
            $testproduct9->PurchasePrice->setCurrency('EUR');
            $testproduct9->Price                        = new Money();
            $testproduct9->Price->setAmount(49.90);
            $testproduct9->Price->setCurrency('EUR');
            $testproduct9->MSRPrice                     = new Money();
            $testproduct9->MSRPrice->setAmount(49.90);
            $testproduct9->MSRPrice->setCurrency('EUR');
            $testproduct9->Weight                       = 500;
            $testproduct9->Quantity                     = 1000;
            $testproduct9->isFreeOfCharge               = false;
            $testproduct9->ProductNumberShop            = 'TEST00001';
            $testproduct9->ProductNumberManufacturer    = 'TEST99001';
            $testproduct9->EANCode                      = '';
            $testproduct9->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct9->SilvercartManufacturerID     = $testmanufacturer1->ID;
            $testproduct9->SilvercartProductGroupID     = $testgroup3->ID;
            $testproduct9->write();

            $testproduct10 = new SilvercartProduct();
            $testproduct10->Title                        = _t('TestProduct10.TITLE', 'TestProduct10');
            $testproduct10->ShortDescription             = _t('TestProduct10.SHORTDESCRIPTION', 'TestProduct10');
            $testproduct10->LongDescription              = _t('TestProduct10.LONGDESCRIPTION', 'TestProduct10');
            $testproduct10->MetaDescription              = _t('TestProduct10.METADESCRIPTION', 'TestProduct10');
            $testproduct10->MetaTitle                    = _t('TestProduct10.METATITLE', 'TestProduct10');
            $testproduct10->MetaKeywords                 = _t('TestProduct10.METAKEYWORDS', 'TestProduct10');
            $testproduct10->PurchasePrice                = new Money();
            $testproduct10->PurchasePrice->setAmount(49.90);
            $testproduct10->PurchasePrice->setCurrency('EUR');
            $testproduct10->Price                        = new Money();
            $testproduct10->Price->setAmount(49.90);
            $testproduct10->Price->setCurrency('EUR');
            $testproduct10->MSRPrice                     = new Money();
            $testproduct10->MSRPrice->setAmount(49.90);
            $testproduct10->MSRPrice->setCurrency('EUR');
            $testproduct10->Weight                       = 500;
            $testproduct10->Quantity                     = 1000;
            $testproduct10->isFreeOfCharge               = false;
            $testproduct10->ProductNumberShop            = 'TEST00001';
            $testproduct10->ProductNumberManufacturer    = 'TEST99001';
            $testproduct10->EANCode                      = '';
            $testproduct10->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct10->SilvercartManufacturerID     = $testmanufacturer1->ID;
            $testproduct10->SilvercartProductGroupID     = $testgroup3->ID;
            $testproduct10->write();

            $testproduct11 = new SilvercartProduct();
            $testproduct11->Title                        = _t('TestProduct11.TITLE', 'TestProduct11');
            $testproduct11->ShortDescription             = _t('TestProduct11.SHORTDESCRIPTION', 'TestProduct11');
            $testproduct11->LongDescription              = _t('TestProduct11.LONGDESCRIPTION', 'TestProduct11');
            $testproduct11->MetaDescription              = _t('TestProduct11.METADESCRIPTION', 'TestProduct11');
            $testproduct11->MetaTitle                    = _t('TestProduct11.METATITLE', 'TestProduct11');
            $testproduct11->MetaKeywords                 = _t('TestProduct11.METAKEYWORDS', 'TestProduct11');
            $testproduct11->PurchasePrice                = new Money();
            $testproduct11->PurchasePrice->setAmount(49.90);
            $testproduct11->PurchasePrice->setCurrency('EUR');
            $testproduct11->Price                        = new Money();
            $testproduct11->Price->setAmount(49.90);
            $testproduct11->Price->setCurrency('EUR');
            $testproduct11->MSRPrice                     = new Money();
            $testproduct11->MSRPrice->setAmount(49.90);
            $testproduct11->MSRPrice->setCurrency('EUR');
            $testproduct11->Weight                       = 500;
            $testproduct11->Quantity                     = 1000;
            $testproduct11->isFreeOfCharge               = false;
            $testproduct11->ProductNumberShop            = 'TEST00001';
            $testproduct11->ProductNumberManufacturer    = 'TEST99001';
            $testproduct11->EANCode                      = '';
            $testproduct11->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct11->SilvercartManufacturerID     = $testmanufacturer2->ID;
            $testproduct11->SilvercartProductGroupID     = $testgroup3->ID;
            $testproduct11->write();

            $testproduct12 = new SilvercartProduct();
            $testproduct12->Title                        = _t('TestProduct12.TITLE', 'TestProduct12');
            $testproduct12->ShortDescription             = _t('TestProduct12.SHORTDESCRIPTION', 'TestProduct12');
            $testproduct12->LongDescription              = _t('TestProduct12.LONGDESCRIPTION', 'TestProduct12');
            $testproduct12->MetaDescription              = _t('TestProduct12.METADESCRIPTION', 'TestProduct12');
            $testproduct12->MetaTitle                    = _t('TestProduct12.METATITLE', 'TestProduct12');
            $testproduct12->MetaKeywords                 = _t('TestProduct12.METAKEYWORDS', 'TestProduct12');
            $testproduct12->PurchasePrice                = new Money();
            $testproduct12->PurchasePrice->setAmount(49.90);
            $testproduct12->PurchasePrice->setCurrency('EUR');
            $testproduct12->Price                        = new Money();
            $testproduct12->Price->setAmount(49.90);
            $testproduct12->Price->setCurrency('EUR');
            $testproduct12->MSRPrice                     = new Money();
            $testproduct12->MSRPrice->setAmount(49.90);
            $testproduct12->MSRPrice->setCurrency('EUR');
            $testproduct12->Weight                       = 500;
            $testproduct12->Quantity                     = 1000;
            $testproduct12->isFreeOfCharge               = false;
            $testproduct12->ProductNumberShop            = 'TEST00001';
            $testproduct12->ProductNumberManufacturer    = 'TEST99001';
            $testproduct12->EANCode                      = '';
            $testproduct12->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct12->SilvercartManufacturerID     = $testmanufacturer2->ID;
            $testproduct12->SilvercartProductGroupID     = $testgroup3->ID;
            $testproduct12->write();

            $testproduct13 = new SilvercartProduct();
            $testproduct13->Title                        = _t('TestProduct13.TITLE', 'TestProduct13');
            $testproduct13->ShortDescription             = _t('TestProduct13.SHORTDESCRIPTION', 'TestProduct13');
            $testproduct13->LongDescription              = _t('TestProduct13.LONGDESCRIPTION', 'TestProduct13');
            $testproduct13->MetaDescription              = _t('TestProduct13.METADESCRIPTION', 'TestProduct13');
            $testproduct13->MetaTitle                    = _t('TestProduct13.METATITLE', 'TestProduct13');
            $testproduct13->MetaKeywords                 = _t('TestProduct13.METAKEYWORDS', 'TestProduct13');
            $testproduct13->PurchasePrice                = new Money();
            $testproduct13->PurchasePrice->setAmount(49.90);
            $testproduct13->PurchasePrice->setCurrency('EUR');
            $testproduct13->Price                        = new Money();
            $testproduct13->Price->setAmount(49.90);
            $testproduct13->Price->setCurrency('EUR');
            $testproduct13->MSRPrice                     = new Money();
            $testproduct13->MSRPrice->setAmount(49.90);
            $testproduct13->MSRPrice->setCurrency('EUR');
            $testproduct13->Weight                       = 500;
            $testproduct13->Quantity                     = 1000;
            $testproduct13->isFreeOfCharge               = false;
            $testproduct13->ProductNumberShop            = 'TEST00001';
            $testproduct13->ProductNumberManufacturer    = 'TEST99001';
            $testproduct13->EANCode                      = '';
            $testproduct13->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct13->SilvercartManufacturerID     = $testmanufacturer1->ID;
            $testproduct13->SilvercartProductGroupID     = $testgroup4->ID;
            $testproduct13->write();

            $testproduct14 = new SilvercartProduct();
            $testproduct14->Title                        = _t('TestProduct14.TITLE', 'TestProduct14');
            $testproduct14->ShortDescription             = _t('TestProduct14.SHORTDESCRIPTION', 'TestProduct14');
            $testproduct14->LongDescription              = _t('TestProduct14.LONGDESCRIPTION', 'TestProduct14');
            $testproduct14->MetaDescription              = _t('TestProduct14.METADESCRIPTION', 'TestProduct14');
            $testproduct14->MetaTitle                    = _t('TestProduct14.METATITLE', 'TestProduct14');
            $testproduct14->MetaKeywords                 = _t('TestProduct14.METAKEYWORDS', 'TestProduct14');
            $testproduct14->PurchasePrice                = new Money();
            $testproduct14->PurchasePrice->setAmount(49.90);
            $testproduct14->PurchasePrice->setCurrency('EUR');
            $testproduct14->Price                        = new Money();
            $testproduct14->Price->setAmount(49.90);
            $testproduct14->Price->setCurrency('EUR');
            $testproduct14->MSRPrice                     = new Money();
            $testproduct14->MSRPrice->setAmount(49.90);
            $testproduct14->MSRPrice->setCurrency('EUR');
            $testproduct14->Weight                       = 500;
            $testproduct14->Quantity                     = 1000;
            $testproduct14->isFreeOfCharge               = false;
            $testproduct14->ProductNumberShop            = 'TEST00001';
            $testproduct14->ProductNumberManufacturer    = 'TEST99001';
            $testproduct14->EANCode                      = '';
            $testproduct14->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct14->SilvercartManufacturerID     = $testmanufacturer1->ID;
            $testproduct14->SilvercartProductGroupID     = $testgroup4->ID;
            $testproduct14->write();

            $testproduct15 = new SilvercartProduct();
            $testproduct15->Title                        = _t('TestProduct15.TITLE', 'TestProduct15');
            $testproduct15->ShortDescription             = _t('TestProduct15.SHORTDESCRIPTION', 'TestProduct15');
            $testproduct15->LongDescription              = _t('TestProduct15.LONGDESCRIPTION', 'TestProduct15');
            $testproduct15->MetaDescription              = _t('TestProduct15.METADESCRIPTION', 'TestProduct15');
            $testproduct15->MetaTitle                    = _t('TestProduct15.METATITLE', 'TestProduct15');
            $testproduct15->MetaKeywords                 = _t('TestProduct15.METAKEYWORDS', 'TestProduct15');
            $testproduct15->PurchasePrice                = new Money();
            $testproduct15->PurchasePrice->setAmount(49.90);
            $testproduct15->PurchasePrice->setCurrency('EUR');
            $testproduct15->Price                        = new Money();
            $testproduct15->Price->setAmount(49.90);
            $testproduct15->Price->setCurrency('EUR');
            $testproduct15->MSRPrice                     = new Money();
            $testproduct15->MSRPrice->setAmount(49.90);
            $testproduct15->MSRPrice->setCurrency('EUR');
            $testproduct15->Weight                       = 500;
            $testproduct15->Quantity                     = 1000;
            $testproduct15->isFreeOfCharge               = false;
            $testproduct15->ProductNumberShop            = 'TEST00001';
            $testproduct15->ProductNumberManufacturer    = 'TEST99001';
            $testproduct15->EANCode                      = '';
            $testproduct15->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct15->SilvercartManufacturerID     = $testmanufacturer2->ID;
            $testproduct15->SilvercartProductGroupID     = $testgroup4->ID;
            $testproduct15->write();

            $testproduct16 = new SilvercartProduct();
            $testproduct16->Title                        = _t('TestProduct16.TITLE', 'TestProduct16');
            $testproduct16->ShortDescription             = _t('TestProduct16.SHORTDESCRIPTION', 'TestProduct16');
            $testproduct16->LongDescription              = _t('TestProduct16.LONGDESCRIPTION', 'TestProduct16');
            $testproduct16->MetaDescription              = _t('TestProduct16.METADESCRIPTION', 'TestProduct16');
            $testproduct16->MetaTitle                    = _t('TestProduct16.METATITLE', 'TestProduct16');
            $testproduct16->MetaKeywords                 = _t('TestProduct16.METAKEYWORDS', 'TestProduct16');
            $testproduct16->PurchasePrice                = new Money();
            $testproduct16->PurchasePrice->setAmount(49.90);
            $testproduct16->PurchasePrice->setCurrency('EUR');
            $testproduct16->Price                        = new Money();
            $testproduct16->Price->setAmount(49.90);
            $testproduct16->Price->setCurrency('EUR');
            $testproduct16->MSRPrice                     = new Money();
            $testproduct16->MSRPrice->setAmount(49.90);
            $testproduct16->MSRPrice->setCurrency('EUR');
            $testproduct16->Weight                       = 500;
            $testproduct16->Quantity                     = 1000;
            $testproduct16->isFreeOfCharge               = false;
            $testproduct16->ProductNumberShop            = 'TEST00001';
            $testproduct16->ProductNumberManufacturer    = 'TEST99001';
            $testproduct16->EANCode                      = '';
            $testproduct16->SilvercartTaxID              = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;
            $testproduct16->SilvercartManufacturerID     = $testmanufacturer2->ID;
            $testproduct16->SilvercartProductGroupID     = $testgroup4->ID;
            $testproduct16->write();
        }
    }

}