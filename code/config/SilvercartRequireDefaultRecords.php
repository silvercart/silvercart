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
 * collects all default records to avoid redundant code when it comes to relations
 * you do not need to search for other default records, they are all here
 *
 * @package Silvercart
 * @subpackage Config
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

        //create customer categories existingCustomer and newCustomer
        $newCustomer = DataObject::get_one('SilvercartCustomerCategory', "`Code` = 'newCustomer'");
        if (!$newCustomer) {
            $newCustomer = new SilvercartCustomerCategory();
            $newCustomer->Title = _t('SilvercartCustomerCategory.NEW_CUSTOMER', 'new customer');
            $newCustomer->Code = 'newCustomer';
            $newCustomer->write();
        }
        $existingCustomer = DataObject::get_one('SilvercartCustomerCategory', "`Code` = 'existingCustomer'");
        if (!$existingCustomer) {
            $existingCustomer = new SilvercartCustomerCategory();
            $existingCustomer->Title = _t('SilvercartCustomerCategory.EXISTING_CUSTOMER', 'existing customer');
            $existingCustomer->Code = 'existingCustomer';
            $existingCustomer->write();
        }

        // create a SilvercartConfig if not exist
        if (!DataObject::get_one('SilvercartConfig')) {
            $silvercartConfig = new SilvercartConfig();
            $silvercartConfig->DefaultCurrency = 'EUR';
            $email = Email::getAdminEmail();
            if ($email != '') {
                $silvercartConfig->EmailSender = $email;
                $silvercartConfig->GlobalEmailRecipient = $email;
            }
            $silvercartConfig->write();
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
                $shippingMethod->isActive = 1;
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
     * disables the creation of test data on /dev/build. This is set by default,
     * so you do not have to disable creation of test data if it was not enabled
     * before.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2011
     */
    public static function disableTestData() {
        self::$enableTestData = false;
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
            if (SiteTree::get_by_link('testgroup1')) {
                // test data already created
                return;
            }
            // get SilvercartProductGroupHolder and tax rate
            $silvercartProductGroupHolder = DataObject::get_one('SilvercartProductGroupHolder');
            $taxRateID = DataObject::get_one('SilvercartTax', "`Rate`='19'")->ID;

            //create a manufacturer
            $manufacturer = new SilvercartManufacturer();
            $manufacturer->Title = 'Testmanufacturer';
            $manufacturer->URL = 'http://www.silvercart.org/';
            $manufacturer->write();
            //create product groups
            for ($i = 1; $i <= 4; $i++) {
                $productGroup = new SilvercartProductGroupPage();
                $productGroup->Title = 'TestProductGroup' . $i;
                $productGroup->URLSegment = 'testgroup' . $i;
                $productGroup->Status = "Published";
                $productGroup->ParentID = $silvercartProductGroupHolder->ID;
                $productGroup->ShowInMenus = true;
                $productGroup->ShowInSearch = true;
                $productGroup->write();
                $productGroup->publish("Stage", "Live");
                //create products
                for ($idx = 1; $idx <= 50; $idx++) {
                    $product = new SilvercartProduct();
                    //relate product to tax
                    $product->SilvercartTaxID = $taxRateID;
                    $product->SilvercartManufacturerID = $manufacturer->ID;
                    $product->Title = 'Testproduct' . $idx;
                    $product->PriceGross->setAmount($idx * 9 + 0.99);
                    $product->PriceGross->setCurrency('EUR');
                    $product->PriceNet->setAmount($idx * 9 + 0.94);
                    $product->PriceNet->setCurrency('EUR');
                    $product->MSRPrice->setAmount($idx * 9 + 0.99);
                    $product->MSRPrice->setCurrency('EUR');
                    $product->PurchasePrice->setAmount($idx * 9 + 0.99);
                    $product->PurchasePrice->setCurrency('EUR');
                    $product->ShortDescription = "This is short description of product $idx";
                    $product->LongDescription = "This is the long description of product $idx. It is in fact not very long, because I do not know what to write. Perhaps I should copy some lorem ipsum?";
                    $product->MetaDescription = "This is the long description of product $idx. It is in fact not very long, because I do not know what to write. Perhaps I should copy some lorem ipsum?";
                    $product->MetaTitle = 'Testproduct' . $idx;
                    $product->MetaKeywords = 'Testproduct' . $idx;
                    $product->Weight = 500;
                    $product->Quantity = 1;
                    $product->ProductNumberShop = "1000" . $idx;
                    $product->ProductNumberManufacturer = "123000" . $idx;
                    $product->SilvercartProductGroupID = $productGroup->ID;
                    $product->write();
                }
            }
        }
    }

}
