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
 * English (GB) language pack
 *
 * @package Silvercart
 * @subpackage i18n
 * @ignore
 */
i18n::include_locale_file('silvercart', 'en_US');

global $lang;

if (array_key_exists('en_GB', $lang) && is_array($lang['en_GB'])) {
    $lang['en_GB'] = array_merge($lang['en_US'], $lang['en_GB']);
} else {
    $lang['en_GB'] = $lang['en_US'];
}

$lang['en_GB']['Silvercart']['DATE'] = 'Date';
$lang['en_GB']['Silvercart']['DAY'] = 'day';
$lang['en_GB']['Silvercart']['DAYS'] = 'days';
$lang['en_GB']['Silvercart']['WEEK'] = 'week';
$lang['en_GB']['Silvercart']['WEEKS'] = 'weeks';
$lang['en_GB']['Silvercart']['MONTH'] = 'month';
$lang['en_GB']['Silvercart']['MONTHS'] = 'months';
$lang['en_GB']['Silvercart']['SEO'] = 'SEO';
$lang['en_GB']['Silvercart']['YES'] = 'Yes';
$lang['en_GB']['Silvercart']['NO'] = 'No';

$lang['en_GB']['SilvercartAddress']['InvoiceAddressAsShippingAddress'] = 'Use invoice address as shipping address';
$lang['en_GB']['SilvercartAddress']['ADDITION'] = 'Addition';
$lang['en_GB']['SilvercartAddress']['CITY'] = 'City';
$lang['en_GB']['SilvercartAddress']['EDITINVOICEADDRESS'] = 'Edit invoice address';
$lang['en_GB']['SilvercartAddress']['EDITSHIPPINGADDRESS'] = 'Edit shipping address';
$lang['en_GB']['SilvercartAddress']['EMAIL'] = 'Email address';
$lang['en_GB']['SilvercartAddress']['EMAIL_CHECK'] = 'Email address check';
$lang['en_GB']['SilvercartAddress']['FIRSTNAME'] = 'First name';
$lang['en_GB']['SilvercartAddress']['MISSES'] = 'Mrs';
$lang['en_GB']['SilvercartAddress']['MISTER'] = 'Mr';
$lang['en_GB']['SilvercartAddress']['PHONE'] = 'Phone';
$lang['en_GB']['SilvercartAddress']['PHONE_SHORT'] = 'Phone';
$lang['en_GB']['SilvercartAddress']['PHONEAREACODE'] = 'Phone area code';
$lang['en_GB']['SilvercartAddress']['PLURALNAME'] = 'Addresses';
$lang['en_GB']['SilvercartAddress']['POSTCODE'] = 'Postcode';
$lang['en_GB']['SilvercartAddress']['SALUTATION'] = 'Salutation';
$lang['en_GB']['SilvercartAddress']['SINGULARNAME'] = 'address';
$lang['en_GB']['SilvercartAddress']['STREET'] = 'Street';
$lang['en_GB']['SilvercartAddress']['STREETNUMBER'] = 'Street number';
$lang['en_GB']['SilvercartAddress']['SURNAME'] = 'Surname';

$lang['en_GB']['SilvercartAddressHolder']['ADD'] = 'Add new address';
$lang['en_GB']['SilvercartAddressHolder']['ADDED_ADDRESS_SUCCESS'] = 'Your address was successfully saved.';
$lang['en_GB']['SilvercartAddressHolder']['ADDED_ADDRESS_FAILURE'] = 'Your address could not be saved.';
$lang['en_GB']['SilvercartAddressHolder']['ADDITIONALADDRESS'] = 'Additional address';
$lang['en_GB']['SilvercartAddressHolder']['ADDITIONALADDRESSES'] = 'Additional addresses';
$lang['en_GB']['SilvercartAddressHolder']['ADDRESS_CANT_BE_DELETED'] = "Sorry, but you can't delete your only address.";
$lang['en_GB']['SilvercartAddressHolder']['ADDRESS_NOT_FOUND'] = 'Sorry, but the given address was not found.';
$lang['en_GB']['SilvercartAddressHolder']['ADDRESS_SUCCESSFULLY_DELETED'] = 'Your address was successfully deleted.';
$lang['en_GB']['SilvercartAddressHolder']['CURRENT_DEFAULT_ADDRESSES'] = 'Your default invoice and shipping addresses';
$lang['en_GB']['SilvercartAddressHolder']['DEFAULT_INVOICE'] = 'This is your invoice address';
$lang['en_GB']['SilvercartAddressHolder']['DEFAULT_SHIPPING'] = 'This is your shipping address';
$lang['en_GB']['SilvercartAddressHolder']['DELETE'] = 'Delete';
$lang['en_GB']['SilvercartAddressHolder']['EDIT'] = 'Edit';
$lang['en_GB']['SilvercartAddressHolder']['EXCUSE_INVOICEADDRESS'] = 'Excuse us, but you have not added an invoice address yet.';
$lang['en_GB']['SilvercartAddressHolder']['EXCUSE_SHIPPINGADDRESS'] = 'Excuse us, but you have not added a delivery address yet.';
$lang['en_GB']['SilvercartAddressHolder']['INVOICEADDRESS'] = 'Invoice address';
$lang['en_GB']['SilvercartAddressHolder']['INVOICEADDRESS_TAB'] = 'Invoiceaddress';
$lang['en_GB']['SilvercartAddressHolder']['PLURALNAME'] = 'Address Holders';
$lang['en_GB']['SilvercartAddressHolder']['SET_DEFAULT_INVOICE'] = 'Set as invoice address';
$lang['en_GB']['SilvercartAddressHolder']['SET_DEFAULT_SHIPPING'] = 'Set as shipping address';
$lang['en_GB']['SilvercartAddressHolder']['SHIPPINGADDRESS'] = 'Shipping address';
$lang['en_GB']['SilvercartAddressHolder']['SHIPPINGADDRESS_TAB'] = 'Shippingaddress';
$lang['en_GB']['SilvercartAddressHolder']['SINGULARNAME'] = 'Address Holder';
$lang['en_GB']['SilvercartAddressHolder']['TITLE'] = 'Address overview';
$lang['en_GB']['SilvercartAddressHolder']['UPDATED_INVOICE_ADDRESS'] = 'Your invoice address was successfully updated.';
$lang['en_GB']['SilvercartAddressHolder']['UPDATED_SHIPPING_ADDRESS'] = 'Your shipping address was successfully updated.';
$lang['en_GB']['SilvercartAddressHolder']['URL_SEGMENT'] = 'address-overview';

$lang['en_GB']['SilvercartAddressPage']['PLURALNAME'] = 'Address Pages';
$lang['en_GB']['SilvercartAddressPage']['SINGULARNAME'] = 'Address Page';
$lang['en_GB']['SilvercartAddressPage']['TITLE'] = 'Address details';
$lang['en_GB']['SilvercartAddressPage']['URL_SEGMENT'] = 'address-details';

$lang['en_GB']['SilvercartAnonymousCustomer']['ANONYMOUSCUSTOMER'] = 'Anonymous customer';
$lang['en_GB']['SilvercartAnonymousCustomer']['PLURALNAME'] = 'Anonymous Customers';
$lang['en_GB']['SilvercartAnonymousCustomer']['SINGULARNAME'] = 'Anonymous Customer';

$lang['en_GB']['SilvercartAvailabilityStatus']['PLURALNAME'] = 'Availability';
$lang['en_GB']['SilvercartAvailabilityStatus']['SINGULARNAME'] = 'Availability';
$lang['en_GB']['SilvercartAvailabilityStatus']['TITLE'] = 'Title';
$lang['en_GB']['SilvercartAvailabilityStatus']['STATUS_AVAILABLE'] = 'available';
$lang['en_GB']['SilvercartAvailabilityStatus']['STATUS_NOT_AVAILABLE'] = 'not available';
$lang['en_GB']['SilvercartAvailabilityStatus']['STATUS_AVAILABLE_IN'] = 'available in %s %s';
$lang['en_GB']['SilvercartAvailabilityStatus']['STATUS_AVAILABLE_IN_MIN_MAX'] = 'available within %s to %s %s';

$lang['en_GB']['SilvercartNewsletter']['SUBSCRIBED']                    = 'You are subscribed to the newsletter';
$lang['en_GB']['SilvercartNewsletter']['UNSUBSCRIBED']                  = 'You are not subscribed to the newsletter';
$lang['en_GB']['SilvercartNewsletterPage']['TITLE']                     = 'Newsletter';
$lang['en_GB']['SilvercartNewsletterPage']['URL_SEGMENT']               = 'newsletter_en_us';
$lang['en_GB']['SilvercartNewsletterResponsePage']['TITLE']             = 'Newsletter Status';
$lang['en_GB']['SilvercartNewsletterResponsePage']['URL_SEGMENT']       = 'newsletter_status_en_us';
$lang['en_GB']['SilvercartNewsletterResponsePage']['STATUS_TITLE']      = 'Your newsletter settings';
$lang['en_GB']['SilvercartNewsletterForm']['ACTIONFIELD_TITLE']         = 'What do you want to do?';
$lang['en_GB']['SilvercartNewsletterForm']['ACTIONFIELD_SUBSCRIBE']     = 'I want to subscribe to the newsletter';
$lang['en_GB']['SilvercartNewsletterForm']['ACTIONFIELD_UNSUBSCRIBE']   = 'I want to unsubscribe from the newsletter';
$lang['en_GB']['SilvercartNewsletterStatus']['ALREADY_SUBSCRIBED']          = 'The email address "%s" is already subscribed.';
$lang['en_GB']['SilvercartNewsletterStatus']['REGULAR_CUSTOMER_WITH_SAME_EMAIL_EXISTS'] = 'There\'s already a registered customer with the email address "%s". Please log in first and proceed then with the newsletter preferences: <a href="%s">Go to the login page</a>.';
$lang['en_GB']['SilvercartNewsletterStatus']['NO_EMAIL_FOUND']              = 'We could not find the email address "%s".';
$lang['en_GB']['SilvercartNewsletterStatus']['UNSUBSCRIBED_SUCCESSFULLY']   = 'The email address "%s" was unsubscribed successfully.';
$lang['en_GB']['SilvercartNewsletterStatus']['SUBSCRIBED_SUCCESSFULLY']     = 'The email address "%s" was subscribed successfully.';

$lang['en_GB']['SilvercartNumberRange']['ACTUAL'] = 'Actual';
$lang['en_GB']['SilvercartNumberRange']['ACTUALCOUNT'] = 'Actual';
$lang['en_GB']['SilvercartNumberRange']['CUSTOMERNUMBER'] = 'Customer number';
$lang['en_GB']['SilvercartNumberRange']['END'] = 'End';
$lang['en_GB']['SilvercartNumberRange']['ENDCOUNT'] = 'End';
$lang['en_GB']['SilvercartNumberRange']['IDENTIFIER'] = 'Identifier';
$lang['en_GB']['SilvercartNumberRange']['ORDERNUMBER'] = 'Order number';
$lang['en_GB']['SilvercartNumberRange']['PLURALNAME'] = 'Number ranges';
$lang['en_GB']['SilvercartNumberRange']['PREFIX'] = 'Prefix';
$lang['en_GB']['SilvercartNumberRange']['SINGULARNAME'] = 'Number range';
$lang['en_GB']['SilvercartNumberRange']['START'] = 'Start';
$lang['en_GB']['SilvercartNumberRange']['STARTCOUNT'] = 'Start';
$lang['en_GB']['SilvercartNumberRange']['SUFFIX'] = 'Suffix';
$lang['en_GB']['SilvercartNumberRange']['TITLE'] = 'Title';

$lang['en_GB']['SilvercartProduct']['IS_ACTIVE'] = 'is active';
$lang['en_GB']['SilvercartProduct']['ADD_TO_CART'] = 'Add to cart';
$lang['en_GB']['SilvercartProduct']['AMOUNT_UNIT'] = 'purchase unit';
$lang['en_GB']['SilvercartProduct']['CHOOSE_MASTER'] = '-- choose master --';
$lang['en_GB']['SilvercartProduct']['COLUMN_TITLE'] = 'Name';
$lang['en_GB']['SilvercartProduct']['DESCRIPTION'] = 'Product description';
$lang['en_GB']['SilvercartProduct']['EAN'] = 'EAN';
$lang['en_GB']['SilvercartProduct']['FREE_OF_CHARGE'] = 'Free of charge';
$lang['en_GB']['SilvercartProduct']['IMAGE'] = 'Product image';
$lang['en_GB']['SilvercartProduct']['IMAGE_NOT_AVAILABLE'] = 'Product image not available';
$lang['en_GB']['SilvercartProduct']['MASTERPRODUCT'] = 'Master product';
$lang['en_GB']['SilvercartProduct']['METADESCRIPTION'] = 'Meta description for search engines';
$lang['en_GB']['SilvercartProduct']['METAKEYWORDS'] = 'Meta keywords for search engines';
$lang['en_GB']['SilvercartProduct']['METATITLE'] = 'Meta title for search engines';
$lang['en_GB']['SilvercartProduct']['MSRP'] = 'RRP';
$lang['en_GB']['SilvercartProduct']['PACKAGING_UNIT'] = 'packaging unit';
$lang['en_GB']['SilvercartProduct']['PLURALNAME'] = 'Products';
$lang['en_GB']['SilvercartProduct']['PRICE'] = 'Price';
$lang['en_GB']['SilvercartProduct']['PRICE_GROSS'] = 'Price (gross)';
$lang['en_GB']['SilvercartProduct']['PRICE_NET'] = 'Price (net)';
$lang['en_GB']['SilvercartProduct']['PRICE_SINGLE'] = 'Price single';
$lang['en_GB']['SilvercartProduct']['PRODUCTNUMBER'] = 'Item number';
$lang['en_GB']['SilvercartProduct']['PRODUCTNUMBER_SHORT'] = 'Item number';
$lang['en_GB']['SilvercartProduct']['PRODUCTNUMBER_MANUFACTURER'] = 'Item number (manufacturer)';
$lang['en_GB']['SilvercartProduct']['PURCHASEPRICE'] = 'Purchase price';
$lang['en_GB']['SilvercartProduct']['PURCHASE_MIN_DURATION'] = 'Min. purchase duration';
$lang['en_GB']['SilvercartProduct']['PURCHASE_MAX_DURATION'] = 'Max. purchase duration';
$lang['en_GB']['SilvercartProduct']['PURCHASE_TIME_UNIT'] = 'Purchase time unit';
$lang['en_GB']['SilvercartProduct']['QUANTITY'] = 'Quantity';
$lang['en_GB']['SilvercartProduct']['QUANTITY_SHORT'] = 'Qty.';
$lang['en_GB']['SilvercartProduct']['SHORTDESCRIPTION'] = 'List description';
$lang['en_GB']['SilvercartProduct']['SINGULARNAME'] = 'Product';
$lang['en_GB']['SilvercartProduct']['TITLE'] = 'Product';
$lang['en_GB']['SilvercartProduct']['VAT'] = 'VAT';
$lang['en_GB']['SilvercartProduct']['WEIGHT'] = 'Weight';

$lang['en_GB']['SilvercartProductGroupHolder']['PAGE_TITLE'] = 'Product groups';
$lang['en_GB']['SilvercartProductGroupHolder']['PLURALNAME'] = 'Product Group Holders';
$lang['en_GB']['SilvercartProductGroupHolder']['SHOW_PRODUCTS_WITH_COUNT_PLURAL'] = 'Show %s products';
$lang['en_GB']['SilvercartProductGroupHolder']['SHOW_PRODUCTS_WITH_COUNT_SINGULAR'] = 'Show %s product';
$lang['en_GB']['SilvercartProductGroupHolder']['SINGULARNAME'] = 'Product Group Holder';
$lang['en_GB']['SilvercartProductGroupHolder']['SUBGROUPS_OF'] = 'Subgroups of ';
$lang['en_GB']['SilvercartProductGroupHolder']['URL_SEGMENT'] = 'productgroups';

$lang['en_GB']['SilvercartProductGroupMirrorPage']['SINGULARNAME']  = 'Mirror-Productgroup';
$lang['en_GB']['SilvercartProductGroupMirrorPage']['PLURALNAME']    = 'Mirror-Productgroups';

$lang['en_GB']['SilvercartProductGroupPage']['ATTRIBUTES'] = 'Attributes';
$lang['en_GB']['SilvercartProductGroupPage']['GROUP_PICTURE'] = 'Group picture';
$lang['en_GB']['SilvercartProductGroupPage']['MANUFACTURER_LINK'] = 'manufacturer';
$lang['en_GB']['SilvercartProductGroupPage']['PLURALNAME'] = 'Product groups';
$lang['en_GB']['SilvercartProductGroupPage']['PRODUCTSPERPAGE'] = 'Products per page (overrides global setting)';
$lang['en_GB']['SilvercartProductGroupPage']['PRODUCTGROUPSPERPAGE'] = 'Product groups per page (overrides global setting)';
$lang['en_GB']['SilvercartProductGroupPage']['SINGULARNAME'] = 'Product group';

$lang['en_GB']['SilvercartProductImageGallery']['PLURALNAME'] = 'Galleries';
$lang['en_GB']['SilvercartProductImageGallery']['SINGULARNAME'] = 'Gallery';

$lang['en_GB']['SilvercartProductPage']['ADD_TO_CART'] = 'Add to cart';
$lang['en_GB']['SilvercartProductPage']['PLURALNAME'] = 'Product details pages';
$lang['en_GB']['SilvercartProductPage']['QUANTITY'] = 'Quantity';
$lang['en_GB']['SilvercartProductPage']['SINGULARNAME'] = 'Product details page';
$lang['en_GB']['SilvercartProductPage']['URL_SEGMENT'] = 'Productdetails';

$lang['en_GB']['SilvercartProductTexts']['PLURALNAME'] = 'Product translation texts';
$lang['en_GB']['SilvercartProductTexts']['SINGULARNAME'] = 'Product translation text';

$lang['en_GB']['SilvercartAttribute']['PLURALNAME'] = 'Attributes';
$lang['en_GB']['SilvercartAttribute']['SINGULARNAME'] = 'Attribute';

$lang['en_GB']['SilvercartBusinessCustomer']['BUSINESSCUSTOMER'] = 'Business customer';
$lang['en_GB']['SilvercartBusinessCustomer']['PLURALNAME'] = 'Business Customers';
$lang['en_GB']['SilvercartBusinessCustomer']['SINGULARNAME'] = 'Business Customer';

$lang['en_GB']['SilvercartCarrier']['ATTRIBUTED_SHIPPINGMETHODS'] = 'Attributed shipping methods';
$lang['en_GB']['SilvercartCarrier']['FULL_NAME'] = 'Full name';
$lang['en_GB']['SilvercartCarrier']['PLURALNAME'] = 'Carriers';
$lang['en_GB']['SilvercartCarrier']['SINGULARNAME'] = 'Carrier';

$lang['en_GB']['SilvercartCartPage']['CART_EMPTY'] = 'Your cart is empty.';
$lang['en_GB']['SilvercartCartPage']['PLURALNAME'] = 'Cart pages';
$lang['en_GB']['SilvercartCartPage']['SINGULARNAME'] = 'Cart page';
$lang['en_GB']['SilvercartCartPage']['URL_SEGMENT'] = 'cart';

$lang['en_GB']['SilvercartCheckoutFormStep']['CHOOSEN_PAYMENT'] = 'Chosen payment method';
$lang['en_GB']['SilvercartCheckoutFormStep']['CHOOSEN_SHIPPING'] = 'Chosen shipping method';
$lang['en_GB']['SilvercartCheckoutFormStep']['FORWARD'] = 'Next';
$lang['en_GB']['SilvercartCheckoutFormStep']['I_ACCEPT_REVOCATION'] = 'I accept the revocation instructions';
$lang['en_GB']['SilvercartCheckoutFormStep']['I_ACCEPT_TERMS'] = 'I accept the terms and conditions.';
$lang['en_GB']['SilvercartCheckoutFormStep']['I_SUBSCRIBE_NEWSLETTER'] = 'I want to receive the newsletter';
$lang['en_GB']['SilvercartCheckoutFormStep']['ORDER'] = 'Order';
$lang['en_GB']['SilvercartCheckoutFormStep']['ORDER_NOW'] = 'Order now';
$lang['en_GB']['SilvercartCheckoutFormStep']['OVERVIEW'] = 'Overview';

$lang['en_GB']['SilvercartCheckoutFormStep1']['LOGIN'] = 'Login';
$lang['en_GB']['SilvercartCheckoutFormStep1']['NEWCUSTOMER'] = 'Are you a new customer?';
$lang['en_GB']['SilvercartCheckoutFormStep1']['PROCEED_WITH_REGISTRATION'] = 'Yes, I want to register so I can reuse my data on my next purchase.';
$lang['en_GB']['SilvercartCheckoutFormStep1']['PROCEED_WITHOUT_REGISTRATION'] = 'No, I don\'t want to register.';
$lang['en_GB']['SilvercartCheckoutFormStep1']['REGISTERTEXT'] = 'Do you want to register so you can reuse your data on your next purchase?';
$lang['en_GB']['SilvercartCheckoutFormStep1']['TITLE'] = 'Registration';
$lang['en_GB']['SilvercartCheckoutFormStep1LoginForm']['TITLE'] = 'Login and continue';
$lang['en_GB']['SilvercartCheckoutFormStep1NewCustomerForm']['CONTINUE_WITH_CHECKOUT'] = 'Continue with checkout';
$lang['en_GB']['SilvercartCheckoutFormStep1NewCustomerForm']['OPTIN_TEMP_TEXT'] = 'After activating your customer account you\'ll be provided a link to proceed with your checkout.';
$lang['en_GB']['SilvercartCheckoutFormStep1NewCustomerForm']['TITLE'] = 'Continue';
$lang['en_GB']['SilvercartCheckoutFormStep2']['EMPTYSTRING_COUNTRY'] = '--country--';
$lang['en_GB']['SilvercartCheckoutFormStep2']['TITLE'] = 'Addresses';
$lang['en_GB']['SilvercartCheckoutFormStep3']['EMPTYSTRING_PAYMENTMETHOD'] = '--choose payment method--';
$lang['en_GB']['SilvercartCheckoutFormStep2']['ERROR_ADDRESS_NOT_FOUND'] = 'The given address could not be found.';
$lang['en_GB']['SilvercartCheckoutFormStep3']['FIELDLABEL'] = 'Please choose your prefered payment method:';
$lang['en_GB']['SilvercartCheckoutFormStep3']['TITLE'] = 'Payment';
$lang['en_GB']['SilvercartCheckoutFormStep4']['EMPTYSTRING_SHIPPINGMETHOD'] = '--choose shipping method--';
$lang['en_GB']['SilvercartCheckoutFormStep4']['TITLE'] = 'Shipment';
$lang['en_GB']['SilvercartCheckoutFormStep5']['TITLE'] = 'Overview';

$lang['en_GB']['SilvercartCheckoutStep']['PLURALNAME'] = 'Checkout Steps';
$lang['en_GB']['SilvercartCheckoutStep']['SINGULARNAME'] = 'Checkout Step';
$lang['en_GB']['SilvercartCheckoutStep']['URL_SEGMENT'] = 'checkout';

$lang['en_GB']['SilvercartConfig']['ADD_EXAMPLE_DATA'] = 'Add Example Data';
$lang['en_GB']['SilvercartConfig']['ADD_EXAMPLE_DATA_DESCRIPTION'] = 'The action "Add Example Data" will create an example manufaturer and four productgroups with 50 products.<br/><strong>CAUTION: This action can take a few minutes!</strong>';
$lang['en_GB']['SilvercartConfig']['ADD_EXAMPLE_CONFIGURATION'] = 'Add Example Configuration';
$lang['en_GB']['SilvercartConfig']['ADD_EXAMPLE_CONFIGURATION_DESCRIPTION'] = 'The action "Add Example Configuration" will preconfigure SilverCart. After that, the checkout process can be completly executed. The data to be configured are: payment option, carrier, shipping option, shipping fee, activation of a country and its relation to a zone.<br/><strong>CAUTION: This action can take a few minutes!</strong>';
$lang['en_GB']['SilvercartConfig']['ADDED_EXAMPLE_DATA'] = 'Added Example Data';
$lang['en_GB']['SilvercartConfig']['ADDED_EXAMPLE_CONFIGURATION'] = 'Added Example Configuration';
$lang['en_GB']['SilvercartConfig']['ALLOW_CART_WEIGHT_TO_BE_ZERO'] = 'Allow cart weight to be zero.';
$lang['en_GB']['SilvercartConfig']['DEFAULTCURRENCY'] = 'Default currency';
$lang['en_GB']['SilvercartConfig']['EMAILSENDER'] = 'Email sender';
$lang['en_GB']['SilvercartConfig']['ENABLESSL'] = 'Enable SSL';
$lang['en_GB']['SilvercartConfig']['EXAMPLE_DATA_ALREADY_ADDED'] = 'Example Data already added';
$lang['en_GB']['SilvercartConfig']['EXAMPLE_CONFIGURATION_ALREADY_ADDED'] = 'Example Configuration already added';
$lang['en_GB']['SilvercartConfig']['GENERAL'] = 'General';
$lang['en_GB']['SilvercartConfig']['GENERAL_MAIN'] = 'Main';
$lang['en_GB']['SilvercartConfig']['GENERAL_TEST_DATA'] = 'Test Data';
$lang['en_GB']['SilvercartConfig']['INTERFACES'] = 'Interfaces';
$lang['en_GB']['SilvercartConfig']['INTERFACES_GEONAMES'] = 'GeoNames';
$lang['en_GB']['SilvercartConfig']['PRICETYPEANONYMOUSCUSTOMERS'] = 'Price type for anonymous customers';
$lang['en_GB']['SilvercartConfig']['PRICETYPEREGULARCUSTOMERS'] = 'Price type for regular customers';
$lang['en_GB']['SilvercartConfig']['PRICETYPEBUSINESSCUSTOMERS'] = 'Price type for business customers';
$lang['en_GB']['SilvercartConfig']['PRICETYPEADMINS'] = 'Price type for administrators';
$lang['en_GB']['SilvercartConfig']['EMAILSENDER_INFO'] = 'The email sender will be the sender address of all emails sent by SilverCart.';
$lang['en_GB']['SilvercartConfig']['ERROR_TITLE'] = 'An error occured!';
$lang['en_GB']['SilvercartConfig']['ERROR_MESSAGE'] = 'Required configuration for "%s" is missing.<br/>Please <a href="%sadmin/silvercart-configuration/">log in</a> and choose "SilverCart Configuration -> general configuration" to edit the missing field.';
$lang['en_GB']['SilvercartConfig']['ERROR_MESSAGE_NO_ACTIVATED_COUNTRY'] = 'No active country found. Please <a href="%s/admin/silvercart-configuration/">log in</a> and choose "SilverCart Configuration -> countries" to activate a country.';
$lang['en_GB']['SilvercartConfig']['GLOBALEMAILRECIPIENT'] = 'Global email recipient';
$lang['en_GB']['SilvercartConfig']['GLOBALEMAILRECIPIENT_INFO'] = 'The global email recipient can be set optionally. The global email recipient will get ALL emails sent by SilverCart (order notifications, contact emails, etc.). The recipients set directly at the email templates will not be replaced, but appended.';
$lang['en_GB']['SilvercartConfig']['MINIMUMORDERVALUE'] = 'Minimum order value';
$lang['en_GB']['SilvercartConfig']['PLURALNAME'] = 'General configurations';
$lang['en_GB']['SilvercartConfig']['PRICETYPE_ANONYMOUS'] = 'Price type for anonymous customers';
$lang['en_GB']['SilvercartConfig']['PRICETYPE_REGULAR'] = 'Price type for regular customers';
$lang['en_GB']['SilvercartConfig']['PRICETYPE_BUSINESS'] = 'Price type for business customers';
$lang['en_GB']['SilvercartConfig']['PRICETYPE_ADMINS'] = 'Price type for administrators';
$lang['en_GB']['SilvercartConfig']['PRODUCTSPERPAGE'] = 'Products per page';
$lang['en_GB']['SilvercartConfig']['PRODUCTGROUPSPERPAGE'] = 'Product groups per page';
$lang['en_GB']['SilvercartConfig']['SINGULARNAME'] = 'General configuration';
$lang['en_GB']['SilvercartConfig']['SHOW_CONFIG'] = 'Show configuration';
$lang['en_GB']['SilvercartConfig']['USEMINIMUMORDERVALUE'] = 'Activate minimum order value';

$lang['en_GB']['SilvercartContactFormPage']['PLURALNAME'] = 'Contact form pages';
$lang['en_GB']['SilvercartContactFormPage']['REQUEST'] = 'Request via contact form';
$lang['en_GB']['SilvercartContactFormPage']['SINGULARNAME'] = 'Contact form page';
$lang['en_GB']['SilvercartContactFormPage']['TITLE'] = 'Contact';
$lang['en_GB']['SilvercartContactFormPage']['URL_SEGMENT'] = 'contact';

$lang['en_GB']['SilvercartContactFormResponsePage']['CONTACT_CONFIRMATION'] = 'Contact confirmation';
$lang['en_GB']['SilvercartContactFormResponsePage']['CONTENT'] = 'Many thanks for your message. Your request will be answered as soon as possible.';
$lang['en_GB']['SilvercartContactFormResponsePage']['PLURALNAME'] = 'Contact form response pages';
$lang['en_GB']['SilvercartContactFormResponsePage']['SINGULARNAME'] = 'Contact form response page';
$lang['en_GB']['SilvercartContactFormResponsePage']['URL_SEGMENT'] = 'contactconfirmation';

$lang['en_GB']['SilvercartContactMessage']['PLURALNAME'] = 'Contactmessages';
$lang['en_GB']['SilvercartContactMessage']['SINGULARNAME'] = 'Contactmessage';
$lang['en_GB']['SilvercartContactMessage']['TEXT'] = "<h1>Request via contact form</h1>\n<h2>Hello,</h2>\n<p>The customer <strong>\"\$FirstName \$Surname\"</strong> with the email address <strong>\"\$Email\"</strong> sent the following message:<br/>\n\n\$Message</p>\n";

$lang['en_GB']['SilvercartContactMessageAdmin']['MENU_TITLE'] = 'Contactmessages';

$lang['en_GB']['SilvercartCountry']['ACTIVE'] = 'Active';
$lang['en_GB']['SilvercartCountry']['ATTRIBUTED_PAYMENTMETHOD'] = 'Attributed payment method';
$lang['en_GB']['SilvercartCountry']['ATTRIBUTED_ZONES'] = 'Attributed zones';
$lang['en_GB']['SilvercartCountry']['CONTINENT'] = 'Continent';
$lang['en_GB']['SilvercartCountry']['CURRENCY'] = 'Currency';
$lang['en_GB']['SilvercartCountry']['FIPS'] = 'FIPS code';
$lang['en_GB']['SilvercartCountry']['ISO2'] = 'ISO Alpha2';
$lang['en_GB']['SilvercartCountry']['ISO3'] = 'ISO Alpha3';
$lang['en_GB']['SilvercartCountry']['ISON'] = 'ISO numeric';
$lang['en_GB']['SilvercartCountry']['PLURALNAME'] = 'Countries';
$lang['en_GB']['SilvercartCountry']['SINGULARNAME'] = 'Country';

$lang['en_GB']['SilvercartCountry']['TITLE_AD'] = 'Andorra';
$lang['en_GB']['SilvercartCountry']['TITLE_AE'] = 'United Arab Emirates';
$lang['en_GB']['SilvercartCountry']['TITLE_AF'] = 'Afghanistan';
$lang['en_GB']['SilvercartCountry']['TITLE_AG'] = 'Antigua and Barbuda';
$lang['en_GB']['SilvercartCountry']['TITLE_AI'] = 'Anguilla';
$lang['en_GB']['SilvercartCountry']['TITLE_AL'] = 'Albania';
$lang['en_GB']['SilvercartCountry']['TITLE_AM'] = 'Armenia';
$lang['en_GB']['SilvercartCountry']['TITLE_AN'] = 'Netherlands Antilles';
$lang['en_GB']['SilvercartCountry']['TITLE_AO'] = 'Angola';
$lang['en_GB']['SilvercartCountry']['TITLE_AQ'] = 'Antarctica';
$lang['en_GB']['SilvercartCountry']['TITLE_AR'] = 'Argentina';
$lang['en_GB']['SilvercartCountry']['TITLE_AS'] = 'American Samoa';
$lang['en_GB']['SilvercartCountry']['TITLE_AT'] = 'Austria';
$lang['en_GB']['SilvercartCountry']['TITLE_AU'] = 'Australia';
$lang['en_GB']['SilvercartCountry']['TITLE_AW'] = 'Aruba';
$lang['en_GB']['SilvercartCountry']['TITLE_AX'] = 'Åland Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_AZ'] = 'Azerbaijan';
$lang['en_GB']['SilvercartCountry']['TITLE_BA'] = 'Bosnia and Herzegovina';
$lang['en_GB']['SilvercartCountry']['TITLE_BB'] = 'Barbados';
$lang['en_GB']['SilvercartCountry']['TITLE_BD'] = 'Bangladesh';
$lang['en_GB']['SilvercartCountry']['TITLE_BE'] = 'Belgium';
$lang['en_GB']['SilvercartCountry']['TITLE_BF'] = 'Burkina Faso';
$lang['en_GB']['SilvercartCountry']['TITLE_BG'] = 'Bulgaria';
$lang['en_GB']['SilvercartCountry']['TITLE_BH'] = 'Bahrain';
$lang['en_GB']['SilvercartCountry']['TITLE_BI'] = 'Burundi';
$lang['en_GB']['SilvercartCountry']['TITLE_BJ'] = 'Benin';
$lang['en_GB']['SilvercartCountry']['TITLE_BL'] = 'Saint Barthélemy';
$lang['en_GB']['SilvercartCountry']['TITLE_BM'] = 'Bermuda';
$lang['en_GB']['SilvercartCountry']['TITLE_BN'] = 'Brunei';
$lang['en_GB']['SilvercartCountry']['TITLE_BO'] = 'Bolivia';
$lang['en_GB']['SilvercartCountry']['TITLE_BQ'] = 'Bonaire, Saint Eustatius and Saba';
$lang['en_GB']['SilvercartCountry']['TITLE_BR'] = 'Brazil';
$lang['en_GB']['SilvercartCountry']['TITLE_BS'] = 'Bahamas';
$lang['en_GB']['SilvercartCountry']['TITLE_BT'] = 'Bhutan';
$lang['en_GB']['SilvercartCountry']['TITLE_BV'] = 'Bouvet Island';
$lang['en_GB']['SilvercartCountry']['TITLE_BW'] = 'Botswana';
$lang['en_GB']['SilvercartCountry']['TITLE_BY'] = 'Belarus';
$lang['en_GB']['SilvercartCountry']['TITLE_BZ'] = 'Belize';
$lang['en_GB']['SilvercartCountry']['TITLE_CA'] = 'Canada';
$lang['en_GB']['SilvercartCountry']['TITLE_CC'] = 'Cocos [Keeling] Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_CD'] = 'Congo [DRC]';
$lang['en_GB']['SilvercartCountry']['TITLE_CF'] = 'Central African Republic';
$lang['en_GB']['SilvercartCountry']['TITLE_CG'] = 'Congo [Republic]';
$lang['en_GB']['SilvercartCountry']['TITLE_CH'] = 'Switzerland';
$lang['en_GB']['SilvercartCountry']['TITLE_CI'] = 'Ivory Coast';
$lang['en_GB']['SilvercartCountry']['TITLE_CK'] = 'Cook Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_CL'] = 'Chile';
$lang['en_GB']['SilvercartCountry']['TITLE_CM'] = 'Cameroon';
$lang['en_GB']['SilvercartCountry']['TITLE_CN'] = 'China';
$lang['en_GB']['SilvercartCountry']['TITLE_CO'] = 'Colombia';
$lang['en_GB']['SilvercartCountry']['TITLE_CR'] = 'Costa Rica';
$lang['en_GB']['SilvercartCountry']['TITLE_CS'] = 'Serbia and Montenegro';
$lang['en_GB']['SilvercartCountry']['TITLE_CU'] = 'Cuba';
$lang['en_GB']['SilvercartCountry']['TITLE_CV'] = 'Cape Verde';
$lang['en_GB']['SilvercartCountry']['TITLE_CW'] = 'Curacao';
$lang['en_GB']['SilvercartCountry']['TITLE_CX'] = 'Christmas Island';
$lang['en_GB']['SilvercartCountry']['TITLE_CY'] = 'Cyprus';
$lang['en_GB']['SilvercartCountry']['TITLE_CZ'] = 'Czech Republic';
$lang['en_GB']['SilvercartCountry']['TITLE_DE'] = 'Germany';
$lang['en_GB']['SilvercartCountry']['TITLE_DJ'] = 'Djibouti';
$lang['en_GB']['SilvercartCountry']['TITLE_DK'] = 'Denmark';
$lang['en_GB']['SilvercartCountry']['TITLE_DM'] = 'Dominica';
$lang['en_GB']['SilvercartCountry']['TITLE_DO'] = 'Dominican Republic';
$lang['en_GB']['SilvercartCountry']['TITLE_DZ'] = 'Algeria';
$lang['en_GB']['SilvercartCountry']['TITLE_EC'] = 'Ecuador';
$lang['en_GB']['SilvercartCountry']['TITLE_EE'] = 'Estonia';
$lang['en_GB']['SilvercartCountry']['TITLE_EG'] = 'Egypt';
$lang['en_GB']['SilvercartCountry']['TITLE_EH'] = 'Western Sahara';
$lang['en_GB']['SilvercartCountry']['TITLE_ER'] = 'Eritrea';
$lang['en_GB']['SilvercartCountry']['TITLE_ES'] = 'Spain';
$lang['en_GB']['SilvercartCountry']['TITLE_ET'] = 'Ethiopia';
$lang['en_GB']['SilvercartCountry']['TITLE_FI'] = 'Finland';
$lang['en_GB']['SilvercartCountry']['TITLE_FJ'] = 'Fiji';
$lang['en_GB']['SilvercartCountry']['TITLE_FK'] = 'Falkland Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_FM'] = 'Micronesia';
$lang['en_GB']['SilvercartCountry']['TITLE_FO'] = 'Faroe Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_FR'] = 'France';
$lang['en_GB']['SilvercartCountry']['TITLE_GA'] = 'Gabon';
$lang['en_GB']['SilvercartCountry']['TITLE_GB'] = 'United Kingdom';
$lang['en_GB']['SilvercartCountry']['TITLE_GD'] = 'Grenada';
$lang['en_GB']['SilvercartCountry']['TITLE_GE'] = 'Georgia';
$lang['en_GB']['SilvercartCountry']['TITLE_GF'] = 'French Guiana';
$lang['en_GB']['SilvercartCountry']['TITLE_GG'] = 'Guernsey';
$lang['en_GB']['SilvercartCountry']['TITLE_GH'] = 'Ghana';
$lang['en_GB']['SilvercartCountry']['TITLE_GI'] = 'Gibraltar';
$lang['en_GB']['SilvercartCountry']['TITLE_GL'] = 'Greenland';
$lang['en_GB']['SilvercartCountry']['TITLE_GM'] = 'Gambia';
$lang['en_GB']['SilvercartCountry']['TITLE_GN'] = 'Guinea';
$lang['en_GB']['SilvercartCountry']['TITLE_GP'] = 'Guadeloupe';
$lang['en_GB']['SilvercartCountry']['TITLE_GQ'] = 'Equatorial Guinea';
$lang['en_GB']['SilvercartCountry']['TITLE_GR'] = 'Greece';
$lang['en_GB']['SilvercartCountry']['TITLE_GS'] = 'South Georgia and the South Sandwich Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_GT'] = 'Guatemala';
$lang['en_GB']['SilvercartCountry']['TITLE_GU'] = 'Guam';
$lang['en_GB']['SilvercartCountry']['TITLE_GW'] = 'Guinea-Bissau';
$lang['en_GB']['SilvercartCountry']['TITLE_GY'] = 'Guyana';
$lang['en_GB']['SilvercartCountry']['TITLE_HK'] = 'Hong Kong';
$lang['en_GB']['SilvercartCountry']['TITLE_HM'] = 'Heard Island and McDonald Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_HN'] = 'Honduras';
$lang['en_GB']['SilvercartCountry']['TITLE_HR'] = 'Croatia';
$lang['en_GB']['SilvercartCountry']['TITLE_HT'] = 'Haiti';
$lang['en_GB']['SilvercartCountry']['TITLE_HU'] = 'Hungary';
$lang['en_GB']['SilvercartCountry']['TITLE_ID'] = 'Indonesia';
$lang['en_GB']['SilvercartCountry']['TITLE_IE'] = 'Ireland';
$lang['en_GB']['SilvercartCountry']['TITLE_IL'] = 'Israel';
$lang['en_GB']['SilvercartCountry']['TITLE_IM'] = 'Isle of Man';
$lang['en_GB']['SilvercartCountry']['TITLE_IN'] = 'India';
$lang['en_GB']['SilvercartCountry']['TITLE_IO'] = 'British Indian Ocean Territory';
$lang['en_GB']['SilvercartCountry']['TITLE_IQ'] = 'Iraq';
$lang['en_GB']['SilvercartCountry']['TITLE_IR'] = 'Iran';
$lang['en_GB']['SilvercartCountry']['TITLE_IS'] = 'Iceland';
$lang['en_GB']['SilvercartCountry']['TITLE_IT'] = 'Italy';
$lang['en_GB']['SilvercartCountry']['TITLE_JE'] = 'Jersey';
$lang['en_GB']['SilvercartCountry']['TITLE_JM'] = 'Jamaica';
$lang['en_GB']['SilvercartCountry']['TITLE_JO'] = 'Jordan';
$lang['en_GB']['SilvercartCountry']['TITLE_JP'] = 'Japan';
$lang['en_GB']['SilvercartCountry']['TITLE_KE'] = 'Kenya';
$lang['en_GB']['SilvercartCountry']['TITLE_KG'] = 'Kyrgyzstan';
$lang['en_GB']['SilvercartCountry']['TITLE_KH'] = 'Cambodia';
$lang['en_GB']['SilvercartCountry']['TITLE_KI'] = 'Kiribati';
$lang['en_GB']['SilvercartCountry']['TITLE_KM'] = 'Comoros';
$lang['en_GB']['SilvercartCountry']['TITLE_KN'] = 'Saint Kitts and Nevis';
$lang['en_GB']['SilvercartCountry']['TITLE_KP'] = 'North Korea';
$lang['en_GB']['SilvercartCountry']['TITLE_KR'] = 'South Korea';
$lang['en_GB']['SilvercartCountry']['TITLE_KW'] = 'Kuwait';
$lang['en_GB']['SilvercartCountry']['TITLE_KY'] = 'Cayman Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_KZ'] = 'Kazakhstan';
$lang['en_GB']['SilvercartCountry']['TITLE_LA'] = 'Laos';
$lang['en_GB']['SilvercartCountry']['TITLE_LB'] = 'Lebanon';
$lang['en_GB']['SilvercartCountry']['TITLE_LC'] = 'Saint Lucia';
$lang['en_GB']['SilvercartCountry']['TITLE_LI'] = 'Liechtenstein';
$lang['en_GB']['SilvercartCountry']['TITLE_LK'] = 'Sri Lanka';
$lang['en_GB']['SilvercartCountry']['TITLE_LR'] = 'Liberia';
$lang['en_GB']['SilvercartCountry']['TITLE_LS'] = 'Lesotho';
$lang['en_GB']['SilvercartCountry']['TITLE_LT'] = 'Lithuania';
$lang['en_GB']['SilvercartCountry']['TITLE_LU'] = 'Luxembourg';
$lang['en_GB']['SilvercartCountry']['TITLE_LV'] = 'Latvia';
$lang['en_GB']['SilvercartCountry']['TITLE_LY'] = 'Libya';
$lang['en_GB']['SilvercartCountry']['TITLE_MA'] = 'Morocco';
$lang['en_GB']['SilvercartCountry']['TITLE_MC'] = 'Monaco';
$lang['en_GB']['SilvercartCountry']['TITLE_MD'] = 'Moldova';
$lang['en_GB']['SilvercartCountry']['TITLE_ME'] = 'Montenegro';
$lang['en_GB']['SilvercartCountry']['TITLE_MF'] = 'Saint Martin';
$lang['en_GB']['SilvercartCountry']['TITLE_MG'] = 'Madagascar';
$lang['en_GB']['SilvercartCountry']['TITLE_MH'] = 'Marshall Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_MK'] = 'Macedonia';
$lang['en_GB']['SilvercartCountry']['TITLE_ML'] = 'Mali';
$lang['en_GB']['SilvercartCountry']['TITLE_MM'] = 'Myanmar [Burma]';
$lang['en_GB']['SilvercartCountry']['TITLE_MN'] = 'Mongolia';
$lang['en_GB']['SilvercartCountry']['TITLE_MO'] = 'Macau';
$lang['en_GB']['SilvercartCountry']['TITLE_MP'] = 'Northern Mariana Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_MQ'] = 'Martinique';
$lang['en_GB']['SilvercartCountry']['TITLE_MR'] = 'Mauritania';
$lang['en_GB']['SilvercartCountry']['TITLE_MS'] = 'Montserrat';
$lang['en_GB']['SilvercartCountry']['TITLE_MT'] = 'Malta';
$lang['en_GB']['SilvercartCountry']['TITLE_MU'] = 'Mauritius';
$lang['en_GB']['SilvercartCountry']['TITLE_MV'] = 'Maldives';
$lang['en_GB']['SilvercartCountry']['TITLE_MW'] = 'Malawi';
$lang['en_GB']['SilvercartCountry']['TITLE_MX'] = 'Mexico';
$lang['en_GB']['SilvercartCountry']['TITLE_MY'] = 'Malaysia';
$lang['en_GB']['SilvercartCountry']['TITLE_MZ'] = 'Mozambique';
$lang['en_GB']['SilvercartCountry']['TITLE_NA'] = 'Namibia';
$lang['en_GB']['SilvercartCountry']['TITLE_NC'] = 'New Caledonia';
$lang['en_GB']['SilvercartCountry']['TITLE_NE'] = 'Niger';
$lang['en_GB']['SilvercartCountry']['TITLE_NF'] = 'Norfolk Island';
$lang['en_GB']['SilvercartCountry']['TITLE_NG'] = 'Nigeria';
$lang['en_GB']['SilvercartCountry']['TITLE_NI'] = 'Nicaragua';
$lang['en_GB']['SilvercartCountry']['TITLE_NL'] = 'Netherlands';
$lang['en_GB']['SilvercartCountry']['TITLE_NO'] = 'Norway';
$lang['en_GB']['SilvercartCountry']['TITLE_NP'] = 'Nepal';
$lang['en_GB']['SilvercartCountry']['TITLE_NR'] = 'Nauru';
$lang['en_GB']['SilvercartCountry']['TITLE_NU'] = 'Niue';
$lang['en_GB']['SilvercartCountry']['TITLE_NZ'] = 'New Zealand';
$lang['en_GB']['SilvercartCountry']['TITLE_OM'] = 'Oman';
$lang['en_GB']['SilvercartCountry']['TITLE_PA'] = 'Panama';
$lang['en_GB']['SilvercartCountry']['TITLE_PE'] = 'Peru';
$lang['en_GB']['SilvercartCountry']['TITLE_PF'] = 'French Polynesia';
$lang['en_GB']['SilvercartCountry']['TITLE_PG'] = 'Papua New Guinea';
$lang['en_GB']['SilvercartCountry']['TITLE_PH'] = 'Philippines';
$lang['en_GB']['SilvercartCountry']['TITLE_PK'] = 'Pakistan';
$lang['en_GB']['SilvercartCountry']['TITLE_PL'] = 'Poland';
$lang['en_GB']['SilvercartCountry']['TITLE_PM'] = 'Saint Pierre and Miquelon';
$lang['en_GB']['SilvercartCountry']['TITLE_PN'] = 'Pitcairn Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_PR'] = 'Puerto Rico';
$lang['en_GB']['SilvercartCountry']['TITLE_PS'] = 'Palestinian Territories';
$lang['en_GB']['SilvercartCountry']['TITLE_PT'] = 'Portugal';
$lang['en_GB']['SilvercartCountry']['TITLE_PW'] = 'Palau';
$lang['en_GB']['SilvercartCountry']['TITLE_PY'] = 'Paraguay';
$lang['en_GB']['SilvercartCountry']['TITLE_QA'] = 'Qatar';
$lang['en_GB']['SilvercartCountry']['TITLE_RE'] = 'Réunion';
$lang['en_GB']['SilvercartCountry']['TITLE_RO'] = 'Romania';
$lang['en_GB']['SilvercartCountry']['TITLE_RS'] = 'Serbia';
$lang['en_GB']['SilvercartCountry']['TITLE_RU'] = 'Russia';
$lang['en_GB']['SilvercartCountry']['TITLE_RW'] = 'Rwanda';
$lang['en_GB']['SilvercartCountry']['TITLE_SA'] = 'Saudi Arabia';
$lang['en_GB']['SilvercartCountry']['TITLE_SB'] = 'Solomon Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_SC'] = 'Seychelles';
$lang['en_GB']['SilvercartCountry']['TITLE_SD'] = 'Sudan';
$lang['en_GB']['SilvercartCountry']['TITLE_SE'] = 'Sweden';
$lang['en_GB']['SilvercartCountry']['TITLE_SG'] = 'Singapore';
$lang['en_GB']['SilvercartCountry']['TITLE_SH'] = 'Saint Helena';
$lang['en_GB']['SilvercartCountry']['TITLE_SI'] = 'Slovenia';
$lang['en_GB']['SilvercartCountry']['TITLE_SJ'] = 'Svalbard and Jan Mayen';
$lang['en_GB']['SilvercartCountry']['TITLE_SK'] = 'Slovakia';
$lang['en_GB']['SilvercartCountry']['TITLE_SL'] = 'Sierra Leone';
$lang['en_GB']['SilvercartCountry']['TITLE_SM'] = 'San Marino';
$lang['en_GB']['SilvercartCountry']['TITLE_SN'] = 'Senegal';
$lang['en_GB']['SilvercartCountry']['TITLE_SO'] = 'Somalia';
$lang['en_GB']['SilvercartCountry']['TITLE_SR'] = 'Suriname';
$lang['en_GB']['SilvercartCountry']['TITLE_ST'] = 'São Tomé and Príncipe';
$lang['en_GB']['SilvercartCountry']['TITLE_SV'] = 'El Salvador';
$lang['en_GB']['SilvercartCountry']['TITLE_SX'] = 'Sint Maarten';
$lang['en_GB']['SilvercartCountry']['TITLE_SY'] = 'Syria';
$lang['en_GB']['SilvercartCountry']['TITLE_SZ'] = 'Swaziland';
$lang['en_GB']['SilvercartCountry']['TITLE_TC'] = 'Turks and Caicos Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_TD'] = 'Chad';
$lang['en_GB']['SilvercartCountry']['TITLE_TF'] = 'French Southern Territories';
$lang['en_GB']['SilvercartCountry']['TITLE_TG'] = 'Togo';
$lang['en_GB']['SilvercartCountry']['TITLE_TH'] = 'Thailand';
$lang['en_GB']['SilvercartCountry']['TITLE_TJ'] = 'Tajikistan';
$lang['en_GB']['SilvercartCountry']['TITLE_TK'] = 'Tokelau';
$lang['en_GB']['SilvercartCountry']['TITLE_TL'] = 'East Timor';
$lang['en_GB']['SilvercartCountry']['TITLE_TM'] = 'Turkmenistan';
$lang['en_GB']['SilvercartCountry']['TITLE_TN'] = 'Tunisia';
$lang['en_GB']['SilvercartCountry']['TITLE_TO'] = 'Tonga';
$lang['en_GB']['SilvercartCountry']['TITLE_TR'] = 'Turkey';
$lang['en_GB']['SilvercartCountry']['TITLE_TT'] = 'Trinidad and Tobago';
$lang['en_GB']['SilvercartCountry']['TITLE_TV'] = 'Tuvalu';
$lang['en_GB']['SilvercartCountry']['TITLE_TW'] = 'Taiwan';
$lang['en_GB']['SilvercartCountry']['TITLE_TZ'] = 'Tanzania';
$lang['en_GB']['SilvercartCountry']['TITLE_UA'] = 'Ukraine';
$lang['en_GB']['SilvercartCountry']['TITLE_UG'] = 'Uganda';
$lang['en_GB']['SilvercartCountry']['TITLE_UM'] = 'U.S. Minor Outlying Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_US'] = 'United States';
$lang['en_GB']['SilvercartCountry']['TITLE_UY'] = 'Uruguay';
$lang['en_GB']['SilvercartCountry']['TITLE_UZ'] = 'Uzbekistan';
$lang['en_GB']['SilvercartCountry']['TITLE_VA'] = 'Vatican City';
$lang['en_GB']['SilvercartCountry']['TITLE_VC'] = 'Saint Vincent and the Grenadines';
$lang['en_GB']['SilvercartCountry']['TITLE_VE'] = 'Venezuela';
$lang['en_GB']['SilvercartCountry']['TITLE_VG'] = 'British Virgin Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_VI'] = 'U.S. Virgin Islands';
$lang['en_GB']['SilvercartCountry']['TITLE_VN'] = 'Vietnam';
$lang['en_GB']['SilvercartCountry']['TITLE_VU'] = 'Vanuatu';
$lang['en_GB']['SilvercartCountry']['TITLE_WF'] = 'Wallis and Futuna';
$lang['en_GB']['SilvercartCountry']['TITLE_WS'] = 'Samoa';
$lang['en_GB']['SilvercartCountry']['TITLE_XK'] = 'Kosovo';
$lang['en_GB']['SilvercartCountry']['TITLE_YE'] = 'Yemen';
$lang['en_GB']['SilvercartCountry']['TITLE_YT'] = 'Mayotte';
$lang['en_GB']['SilvercartCountry']['TITLE_ZA'] = 'South Africa';
$lang['en_GB']['SilvercartCountry']['TITLE_ZM'] = 'Zambia';
$lang['en_GB']['SilvercartCountry']['TITLE_ZW'] = 'Zimbabwe';

$lang['en_GB']['SilvercartCustomerAdmin']['customers'] = 'Customers';

$lang['en_GB']['SilvercartCustomerCategory']['EXISTING_CUSTOMER'] = 'existing customer';
$lang['en_GB']['SilvercartCustomerCategory']['NEW_CUSTOMER'] = 'new customer';
$lang['en_GB']['SilvercartCustomerCategory']['PLURALNAME'] = 'Customer categories';
$lang['en_GB']['SilvercartCustomerCategory']['SINGULARNAME'] = 'Customer category';

$lang['en_GB']['SilvercartCustomerRole']['CUSTOMERNUMBER'] = 'Customer number';
$lang['en_GB']['SilvercartCustomerRole']['GROSS'] = 'gross';
$lang['en_GB']['SilvercartCustomerRole']['NET'] = 'net';
$lang['en_GB']['SilvercartCustomerRole']['PRICING'] = 'Pricing';
$lang['en_GB']['SilvercartCustomerRole']['SALUTATION'] = 'Salutation';
$lang['en_GB']['SilvercartCustomerRole']['SUBSCRIBEDTONEWSLETTER'] = 'Subscribed to newsletter';
$lang['en_GB']['SilvercartCustomerRole']['HASACCEPTEDTERMSANDCONDITIONS'] = 'Has accepted terms and conditions';
$lang['en_GB']['SilvercartCustomerRole']['HASACCEPTEDREVOCATIONINSTRUCTION'] = 'Has accepted revocation instruction';
$lang['en_GB']['SilvercartCustomerRole']['CONFIRMATIONDATE'] = 'Confirmation date';
$lang['en_GB']['SilvercartCustomerRole']['CONFIRMATIONHASH'] = 'Confirmation code';
$lang['en_GB']['SilvercartCustomerRole']['OPTINSTATUS'] = 'Opt-in status';
$lang['en_GB']['SilvercartCustomerRole']['BIRTHDAY'] = 'Birthday';
$lang['en_GB']['SilvercartCustomerRole']['TYPE'] = 'Type';

$lang['en_GB']['SilvercartDataPage']['PLURALNAME'] = 'Data pages';
$lang['en_GB']['SilvercartDataPage']['SINGULARNAME'] = 'Data Page';
$lang['en_GB']['SilvercartDataPage']['TITLE'] = 'My data';
$lang['en_GB']['SilvercartDataPage']['URL_SEGMENT'] = 'my-data';

$lang['en_GB']['SilvercartDataPrivacyStatementPage']['PLURALNAME'] = 'Privacy policy pages';
$lang['en_GB']['SilvercartDataPrivacyStatementPage']['SINGULARNAME'] = 'Privacy policy page';
$lang['en_GB']['SilvercartDataPrivacyStatementPage']['TITLE'] = 'Data privacy statement';
$lang['en_GB']['SilvercartDataPrivacyStatementPage']['URL_SEGMENT'] = 'data-privacy-statement';

$lang['en_GB']['SilvercartEditAddressForm']['EMPTYSTRING_PLEASECHOOSE'] = '--please choose--';

$lang['en_GB']['SilvercartEmailTemplates']['PLURALNAME'] = 'Email templates';
$lang['en_GB']['SilvercartEmailTemplates']['SINGULARNAME'] = 'Email template';

$lang['en_GB']['SilvercartFile']['DESCRIPTION'] = 'Description';
$lang['en_GB']['SilvercartFile']['PLURALNAME'] = 'Files';
$lang['en_GB']['SilvercartFile']['SINGULARNAME'] = 'File';
$lang['en_GB']['SilvercartFile']['TITLE'] = 'Display name';

$lang['en_GB']['SilvercartFooterNavigationHolder']['PLURALNAME'] = 'Footer Navigation Holders';
$lang['en_GB']['SilvercartFooterNavigationHolder']['SINGULARNAME'] = 'Footer Navigation Holder';
$lang['en_GB']['SilvercartFooterNavigationHolder']['URL_SEGMENT'] = 'footernavigation';

$lang['en_GB']['SilvercartFrontPage']['DEFAULT_CONTENT'] = '<h2>Welcome to <strong>SilverCart</strong> Webshop!</h2>';
$lang['en_GB']['SilvercartFrontPage']['PLURALNAME'] = 'Front pages';
$lang['en_GB']['SilvercartFrontPage']['SINGULARNAME'] = 'Front page';

$lang['en_GB']['SilvercartHandlingCost']['PLURALNAME'] = 'Handling Costs';
$lang['en_GB']['SilvercartHandlingCost']['SINGULARNAME'] = 'Handling Cost';

$lang['en_GB']['SilvercartImage']['DESCRIPTION'] = 'Description';
$lang['en_GB']['SilvercartImage']['PLURALNAME'] = 'Images';
$lang['en_GB']['SilvercartImage']['SINGULARNAME'] = 'Image';
$lang['en_GB']['SilvercartImage']['TITLE'] = 'Display name';

$lang['en_GB']['SilvercartInvoiceAddress']['PLURALNAME'] = 'Invoice addresses';
$lang['en_GB']['SilvercartInvoiceAddress']['SINGULARNAME'] = 'Invoice address';

$lang['en_GB']['SilvercartManufacturer']['PLURALNAME'] = 'Manufacturers';
$lang['en_GB']['SilvercartManufacturer']['SINGULARNAME'] = 'Manufacturer';

$lang['en_GB']['SilvercartMetaNavigationHolder']['PLURALNAME'] = 'Metanavigations';
$lang['en_GB']['SilvercartMetaNavigationHolder']['SINGULARNAME'] = 'Metanavigation';
$lang['en_GB']['SilvercartMetaNavigationHolder']['URL_SEGMENT'] = 'metanavigation';

$lang['en_GB']['SilvercartMyAccountHolder']['ALREADY_HAVE_AN_ACCOUNT'] = 'Do you already have an account?';
$lang['en_GB']['SilvercartMyAccountHolder']['GOTO_REGISTRATION'] = 'Go to the registration form';
$lang['en_GB']['SilvercartMyAccountHolder']['PLURALNAME'] = 'Account holders';
$lang['en_GB']['SilvercartMyAccountHolder']['REGISTER_ADVANTAGES_TEXT'] = 'By registering you can reuse your data like invoice or delivery addresses on your next purchase.';
$lang['en_GB']['SilvercartMyAccountHolder']['SINGULARNAME'] = 'Account holder';
$lang['en_GB']['SilvercartMyAccountHolder']['TITLE'] = 'My account';
$lang['en_GB']['SilvercartMyAccountHolder']['URL_SEGMENT'] = 'my-account';
$lang['en_GB']['SilvercartMyAccountHolder']['WANTTOREGISTER'] = 'Do you want to register?';
$lang['en_GB']['SilvercartMyAccountHolder']['YOUR_CUSTOMERNUMBER'] = 'Your customer number';

$lang['en_GB']['SilvercartOrder']['AMOUNTGROSSTOTAL'] = 'Total gross amount';
$lang['en_GB']['SilvercartOrder']['AMOUNTTOTAL'] = 'Total amount';
$lang['en_GB']['SilvercartOrder']['CARRIERANDSHIPPINGMETHODTITLE'] = 'Shipping method';
$lang['en_GB']['SilvercartOrder']['CUSTOMER'] = 'Customer';
$lang['en_GB']['SilvercartOrder']['CUSTOMERSEMAIL'] = 'Customers email address';
$lang['en_GB']['SilvercartOrder']['HANDLINGCOSTPAYMENT'] = 'Payment handling costs';
$lang['en_GB']['SilvercartOrder']['HANDLINGCOSTSHIPMENT'] = 'Shipping handling costs';
$lang['en_GB']['SilvercartOrder']['INCLUDED_SHIPPINGRATE'] = 'Included shipping rate';
$lang['en_GB']['SilvercartOrder']['NOTE'] = 'Note';
$lang['en_GB']['SilvercartOrder']['ORDER_ID'] = 'Order number';
$lang['en_GB']['SilvercartOrder']['ORDERNUMBER'] = 'Order number';
$lang['en_GB']['SilvercartOrder']['ORDER_VALUE'] = 'Order amount';
$lang['en_GB']['SilvercartOrder']['PAYMENTMETHODTITLE'] = 'Payment method';
$lang['en_GB']['SilvercartOrder']['PLURALNAME'] = 'Orders';
$lang['en_GB']['SilvercartOrder']['SHIPPINGRATE'] = 'Shipping rate';
$lang['en_GB']['SilvercartOrder']['SINGULARNAME'] = 'Order';
$lang['en_GB']['SilvercartOrder']['STATUS'] = 'Order status';
$lang['en_GB']['SilvercartOrder']['TAXAMOUNTPAYMENT'] = 'Payment tax amount';
$lang['en_GB']['SilvercartOrder']['TAXAMOUNTSHIPMENT'] = 'Shipping tax amount';
$lang['en_GB']['SilvercartOrder']['TAXRATEPAYMENT'] = 'Payment tax rate';
$lang['en_GB']['SilvercartOrder']['TAXRATESHIPMENT'] = 'Shipping tax rate';
$lang['en_GB']['SilvercartOrder']['WEIGHTTOTAL'] = 'Total weight';
$lang['en_GB']['SilvercartOrder']['YOUR_REMARK'] = 'Your remark';

$lang['en_GB']['SilvercartOrderAddress']['PLURALNAME'] = 'Order addresses';
$lang['en_GB']['SilvercartOrderAddress']['SINGULARNAME'] = 'Order address';

$lang['en_GB']['SilvercartOrderConfirmationPage']['PLURALNAME'] = 'Order Confirmation pages';
$lang['en_GB']['SilvercartOrderConfirmationPage']['SINGULARNAME'] = 'Order Confirmation Page';
$lang['en_GB']['SilvercartOrderConfirmationPage']['URL_SEGMENT'] = 'order-conirmation';
$lang['en_GB']['SilvercartOrderConfirmationPage']['ORDERCONFIRMATION'] = 'order confirmation';

$lang['en_GB']['SilvercartOrderDetailPage']['PLURALNAME'] = 'Order Detail pages';
$lang['en_GB']['SilvercartOrderDetailPage']['SINGULARNAME'] = 'Order Detail Page';
$lang['en_GB']['SilvercartOrderDetailPage']['TITLE'] = 'Order details';
$lang['en_GB']['SilvercartOrderDetailPage']['URL_SEGMENT'] = 'order-details';

$lang['en_GB']['SilvercartOrderHolder']['PLURALNAME'] = 'Order overview';
$lang['en_GB']['SilvercartOrderHolder']['SINGULARNAME'] = 'Order overview';
$lang['en_GB']['SilvercartOrderHolder']['TITLE'] = 'My orders';
$lang['en_GB']['SilvercartOrderHolder']['URL_SEGMENT'] = 'my-orders';

$lang['en_GB']['SilvercartOrderInvoiceAddress']['PLURALNAME'] = 'Order invoice addresses';
$lang['en_GB']['SilvercartOrderInvoiceAddress']['SINGULARNAME'] = 'Order invoice address';

$lang['en_GB']['SilvercartOrderPosition']['PLURALNAME'] = 'Order positions';
$lang['en_GB']['SilvercartOrderPosition']['SINGULARNAME'] = 'Order Position';

$lang['en_GB']['SilvercartOrderSearchForm']['PLEASECHOOSE'] = 'Please choose';

$lang['en_GB']['SilvercartOrderShippingAddress']['PLURALNAME'] = 'Order shipping addresses';
$lang['en_GB']['SilvercartOrderShippingAddress']['SINGULARNAME'] = 'Order shipping address';

$lang['en_GB']['SilvercartOrderStatus']['CODE'] = 'Code';
$lang['en_GB']['SilvercartOrderStatus']['PAYED'] = 'Paid';
$lang['en_GB']['SilvercartOrderStatus']['PLURALNAME'] = 'Order status';
$lang['en_GB']['SilvercartOrderStatus']['SINGULARNAME'] = 'Order status';
$lang['en_GB']['SilvercartOrderStatus']['WAITING_FOR_PAYMENT'] = 'Waiting for payment';

$lang['en_GB']['SilvercartOrderStatusTexts']['PLURALNAME'] = 'Order Status Text';
$lang['en_GB']['SilvercartOrderStatusTexts']['SINGULARNAME'] = 'Order Status Text';

$lang['en_GB']['SilvercartPage']['ABOUT_US'] = 'About us';
$lang['en_GB']['SilvercartPage']['ABOUT_US_URL_SEGMENT'] = 'about-us';
$lang['en_GB']['SilvercartPage']['ACCESS_CREDENTIALS_CALL'] = 'Please fill in your access credentials:';
$lang['en_GB']['SilvercartPage']['ADDRESS'] = 'Address';
$lang['en_GB']['SilvercartPage']['ADDRESSINFORMATION'] = 'Address information';
$lang['en_GB']['SilvercartPage']['ADDRESS_DATA'] = 'Address data';
$lang['en_GB']['SilvercartPage']['ALREADY_REGISTERED'] = 'Hello %s, You have already registered.';
$lang['en_GB']['SilvercartPage']['API_CREATE'] = 'Can create objects via the API';
$lang['en_GB']['SilvercartPage']['API_DELETE'] = 'Can delete objects via the API';
$lang['en_GB']['SilvercartPage']['API_EDIT'] = 'Can edit objects via the API';
$lang['en_GB']['SilvercartPage']['API_VIEW'] = 'Can read objects via the API';
$lang['en_GB']['SilvercartPage']['APRIL'] = 'April';
$lang['en_GB']['SilvercartPage']['PRODUCTNAME'] = 'Product name';
$lang['en_GB']['SilvercartPage']['AUGUST'] = 'August';
$lang['en_GB']['SilvercartPage']['BILLING_ADDRESS'] = 'Billing address';
$lang['en_GB']['SilvercartPage']['BIRTHDAY'] = 'Birthday';
$lang['en_GB']['SilvercartPage']['CANCEL'] = 'Cancel';
$lang['en_GB']['SilvercartPage']['CART'] = 'Cart';
$lang['en_GB']['SilvercartPage']['CATALOG'] = 'Catalog';
$lang['en_GB']['SilvercartPage']['CHANGE_PAYMENTMETHOD_CALL'] = 'Please choose another payment method or contact the shop owner.';
$lang['en_GB']['SilvercartPage']['CHANGE_PAYMENTMETHOD_LINK'] = 'Choose another payment method';
$lang['en_GB']['SilvercartPage']['CHECKOUT'] = 'Checkout';
$lang['en_GB']['SilvercartPage']['CHECK_FIELDS_CALL'] = 'Please check your input on the following fields:';
$lang['en_GB']['SilvercartPage']['CONTACT_FORM'] = 'Contact form';
$lang['en_GB']['SilvercartPage']['CREDENTIALS_WRONG'] = 'Your credentials are incorrect.';
$lang['en_GB']['SilvercartPage']['DAY'] = 'Day';
$lang['en_GB']['SilvercartPage']['DECEMBER'] = 'December';
$lang['en_GB']['SilvercartPage']['DETAILS'] = 'Details';
$lang['en_GB']['SilvercartPage']['DETAILS_FOR'] = 'Details for %s';
$lang['en_GB']['SilvercartPage']['DIDNOT_RETURN_RESULTS'] = 'did not return any results in our shop.';
$lang['en_GB']['SilvercartPage']['DO_NOT_EDIT'] = 'Do not edit this field unless you know exactly what you are doing!';
$lang['en_GB']['SilvercartPage']['EMAIL_ADDRESS'] = 'Email address';
$lang['en_GB']['SilvercartPage']['EMAIL_ALREADY_REGISTERED'] = 'This email address is already registered';
$lang['en_GB']['SilvercartPage']['EMAIL_NOT_FOUND'] = 'This email address could not be found.';
$lang['en_GB']['SilvercartPage']['EMPTY_CART'] = 'Empty';
$lang['en_GB']['SilvercartPage']['ERROR_LISTING'] = 'The following errors have occured:';
$lang['en_GB']['SilvercartPage']['ERROR_OCCURED'] = 'An error has occured.';
$lang['en_GB']['SilvercartPage']['FEBRUARY'] = 'February';
$lang['en_GB']['SilvercartPage']['FIND'] = 'Find:';
$lang['en_GB']['SilvercartPage']['FORWARD'] = 'Next';
$lang['en_GB']['SilvercartPage']['GOTO'] = 'Go to %s page';
$lang['en_GB']['SilvercartPage']['GOTO_CART'] = 'Go to cart';
$lang['en_GB']['SilvercartPage']['GOTO_CONTACT_LINK'] = 'Go to contact page';
$lang['en_GB']['SilvercartPage']['HEADERPICTURE'] = 'Header picture';
$lang['en_GB']['SilvercartPage']['INCLUDED_VAT'] = 'included VAT';
$lang['en_GB']['SilvercartPage']['I_ACCEPT'] = 'I accept the';
$lang['en_GB']['SilvercartPage']['I_HAVE_READ'] = 'I have read the';
$lang['en_GB']['SilvercartPage']['ISACTIVE'] = 'Active';
$lang['en_GB']['SilvercartPage']['JANUARY'] = 'January';
$lang['en_GB']['SilvercartPage']['JUNE'] = 'June';
$lang['en_GB']['SilvercartPage']['JULY'] = 'July';
$lang['en_GB']['SilvercartPage']['LOGIN'] = 'Login';
$lang['en_GB']['SilvercartPage']['LOGO'] = 'Logo';
$lang['en_GB']['SilvercartPage']['MARCH'] = 'March';
$lang['en_GB']['SilvercartPage']['MAY'] = 'May';
$lang['en_GB']['SilvercartPage']['MESSAGE'] = 'Message';
$lang['en_GB']['SilvercartPage']['MONTH'] = 'Month';
$lang['en_GB']['SilvercartPage']['MYACCOUNT'] = 'My account';
$lang['en_GB']['SilvercartPage']['NAME'] = 'Name';
$lang['en_GB']['SilvercartPage']['NEWSLETTER'] = 'Newsletter';
$lang['en_GB']['SilvercartPage']['NEWSLETTER_FORM'] = 'Newsletter settings';
$lang['en_GB']['SilvercartPage']['NEXT'] = 'Next';
$lang['en_GB']['SilvercartPage']['NOVEMBER'] = 'November';
$lang['en_GB']['SilvercartPage']['NO_ORDERS'] = 'You do not have any orders yet';
$lang['en_GB']['SilvercartPage']['NO_RESULTS'] = 'Sorry, but your query did not return any results.';
$lang['en_GB']['SilvercartPage']['OCTOBER'] = 'October';
$lang['en_GB']['SilvercartPage']['ORDERD_PRODUCTS'] = 'Ordered products';
$lang['en_GB']['SilvercartPage']['ORDER_COMPLETED'] = 'Your order is complete';
$lang['en_GB']['SilvercartPage']['ORDER_DATE'] = 'Order date';
$lang['en_GB']['SilvercartPage']['ORDERS_EMAIL_INFORMATION_TEXT'] = 'Please check your email inbox for the order confirmation.';
$lang['en_GB']['SilvercartPage']['ORDER_THANKS'] = 'Many thanks for your order.';
$lang['en_GB']['SilvercartPage']['PASSWORD'] = 'Password';
$lang['en_GB']['SilvercartPage']['PASSWORD_CASE_EMPTY'] = 'If you leave this field empty, your password will not be changed.';
$lang['en_GB']['SilvercartPage']['PASSWORD_CHECK'] = 'Password check';
$lang['en_GB']['SilvercartPage']['PASSWORD_WRONG'] = 'This password is wrong.';
$lang['en_GB']['SilvercartPage']['PAYMENT_NOT_WORKING'] = 'The chosen payment module does not work.';
$lang['en_GB']['SilvercartPage']['PLUS_SHIPPING'] = 'plus shipping';
$lang['en_GB']['SilvercartPage']['PREV'] = 'Previous';
$lang['en_GB']['SilvercartPage']['REGISTER'] = 'Register';
$lang['en_GB']['SilvercartPage']['REMARKS'] = 'Remarks';
$lang['en_GB']['SilvercartPage']['REMOVE_FROM_CART'] = 'Remove';
$lang['en_GB']['SilvercartPage']['REVOCATION'] = 'revocation instructions';
$lang['en_GB']['SilvercartPage']['REVOCATIONREAD'] = 'revocation instructions';
$lang['en_GB']['SilvercartPage']['SAVE'] = 'Save';
$lang['en_GB']['SilvercartPage']['SEARCH_RESULTS'] = 'results';
$lang['en_GB']['SilvercartPage']['SEPTEMBER'] = 'September';
$lang['en_GB']['SilvercartPage']['SESSION_EXPIRED'] = 'Your session has expired.';
$lang['en_GB']['SilvercartPage']['SHIPPING_ADDRESS'] = 'Shipping address';
$lang['en_GB']['SilvercartPage']['SHIPPING_AND_BILLING'] = 'Shipping and billing address';
$lang['en_GB']['SilvercartPage']['SHOP_WITHOUT_REGISTRATION'] = 'Shop without registration';
$lang['en_GB']['SilvercartPage']['SHOW_DETAILS'] = 'Show details';
$lang['en_GB']['SilvercartPage']['SHOW_DETAILS_FOR'] = 'Show details for %s';
$lang['en_GB']['SilvercartPage']['SHOWINPAGE'] = 'Set language to %s';
$lang['en_GB']['SilvercartPage']['SITMAP_HERE'] = 'Here You can see the complete directory to our site.';
$lang['en_GB']['SilvercartPage']['STEPS'] = 'Steps';
$lang['en_GB']['SilvercartPage']['SUBMIT'] = 'Send';
$lang['en_GB']['SilvercartPage']['SUBMIT_MESSAGE'] = 'Submit message';
$lang['en_GB']['SilvercartPage']['SUBTOTAL'] = 'Subtotal';
$lang['en_GB']['SilvercartPage']['SUM'] = 'Sum';
$lang['en_GB']['SilvercartPage']['INCLUDING_TAX'] = 'incl. %s%% VAT';
$lang['en_GB']['SilvercartPage']['EXCLUDING_TAX'] = 'plus VAT';
$lang['en_GB']['SilvercartPage']['TAX'] = 'incl. %d%% VAT';
$lang['en_GB']['SilvercartPage']['TERMSOFSERVICE_PRIVACY'] = 'Terms of service and privacy statement';
$lang['en_GB']['SilvercartPage']['THE_QUERY'] = 'The query';
$lang['en_GB']['SilvercartPage']['TITLE'] = 'Title';
$lang['en_GB']['SilvercartPage']['TITLE_IMPRINT'] = 'Imprint';
$lang['en_GB']['SilvercartPage']['TITLE_TERMS'] = 'terms of service';
$lang['en_GB']['SilvercartPage']['TOTAL'] = 'Total';
$lang['en_GB']['SilvercartPage']['URL_SEGMENT_IMPRINT'] = 'imprint';
$lang['en_GB']['SilvercartPage']['URL_SEGMENT_TERMS'] = 'terms-of-service';
$lang['en_GB']['SilvercartPage']['USER_NOT_EXISTING'] = 'This user does not exist.';
$lang['en_GB']['SilvercartPage']['VIEW_ORDERS_TEXT'] = 'You can check the status of your order any time in your';
$lang['en_GB']['SilvercartPage']['WELCOME_PAGE_TITLE'] = 'Welcome';
$lang['en_GB']['SilvercartPage']['WELCOME_PAGE_URL_SEGMENT'] = 'Welcome';
$lang['en_GB']['SilvercartPage']['YEAR'] = 'Year';

$lang['en_GB']['SilvercartPaymentMethod']['ACCESS_MANAGEMENT_BASIC_LABEL'] = 'General';
$lang['en_GB']['SilvercartPaymentMethod']['ACCESS_MANAGEMENT_GROUP_LABEL'] = 'By group(s)';
$lang['en_GB']['SilvercartPaymentMethod']['ACCESS_MANAGEMENT_USER_LABEL'] = 'By user(s)';
$lang['en_GB']['SilvercartPaymentMethod']['ACCESS_SETTINGS'] = 'Access management';
$lang['en_GB']['SilvercartPaymentMethod']['ATTRIBUTED_COUNTRIES'] = 'Attributed countries';
$lang['en_GB']['SilvercartPaymentMethod']['BASIC_SETTINGS'] = 'Basic settings';
$lang['en_GB']['SilvercartPaymentMethod']['ENABLE_RESTRICTION_BY_ORDER_LABEL'] = 'Use the following rule';
$lang['en_GB']['SilvercartPaymentMethod']['FROM_PURCHASE_VALUE'] = 'From purchase value';
$lang['en_GB']['SilvercartPaymentMethod']['MODE'] = 'mode';
$lang['en_GB']['SilvercartPaymentMethod']['NAME'] = 'Name';
$lang['en_GB']['SilvercartPaymentMethod']['NO_PAYMENT_METHOD_AVAILABLE'] = 'No payment method available.';
$lang['en_GB']['SilvercartPaymentMethod']['PAYMENT_LOGOS'] = 'Logos';
$lang['en_GB']['SilvercartPaymentMethod']['PLURALNAME'] = 'Payment methods';
$lang['en_GB']['SilvercartPaymentMethod']['RESTRICT_BY_ORDER_QUANTITY'] = 'The customer must have completed the following number of orders';
$lang['en_GB']['SilvercartPaymentMethod']['RESTRICT_BY_ORDER_STATUS'] = 'whose order status is marked in the following list';
$lang['en_GB']['SilvercartPaymentMethod']['RESTRICTION_LABEL'] = 'Activate only, when the following criteria are met';
$lang['en_GB']['SilvercartPaymentMethod']['SHIPPINGMETHOD'] = 'Shipping method';
$lang['en_GB']['SilvercartPaymentMethod']['SHOW_NOT_FOR_GROUPS_LABEL'] = 'Deactivate for the following groups';
$lang['en_GB']['SilvercartPaymentMethod']['SHOW_ONLY_FOR_GROUPS_LABEL'] = 'Activate for the following groups';
$lang['en_GB']['SilvercartPaymentMethod']['SHOW_NOT_FOR_USERS_LABEL'] = 'Deactivate for the following users';
$lang['en_GB']['SilvercartPaymentMethod']['SHOW_ONLY_FOR_USERS_LABEL'] = 'Activate for the following users';
$lang['en_GB']['SilvercartPaymentMethod']['SINGULARNAME'] = 'Payment method';
$lang['en_GB']['SilvercartPaymentMethod']['STANDARD_ORDER_STATUS'] = 'Standard order status for this payment method';
$lang['en_GB']['SilvercartPaymentMethod']['TILL_PURCHASE_VALUE'] = 'till purchase value';
$lang['en_GB']['SilvercartPaymentMethod']['TITLE'] = 'Payment method';

$lang['en_GB']['SilvercartPaymentMethodTexts']['PLURALNAME'] = 'Payment translations';
$lang['en_GB']['SilvercartPaymentMethodTexts']['SINGULARNAME'] = 'Payment translation';

$lang['en_GB']['SilvercartPaymentNotification']['PLURALNAME'] = 'Payment Notifications';
$lang['en_GB']['SilvercartPaymentNotification']['SINGULARNAME'] = 'Payment Notification';
$lang['en_GB']['SilvercartPaymentNotification']['TITLE'] = 'Payment notification';
$lang['en_GB']['SilvercartPaymentNotification']['URL_SEGMENT'] = 'payment-notification';

$lang['en_GB']['SilvercartPrice']['PLURALNAME'] = 'Prices';
$lang['en_GB']['SilvercartPrice']['SINGULARNAME'] = 'Price';

$lang['en_GB']['SilvercartQuickSearchForm']['SUBMITBUTTONTITLE'] = 'Search';

$lang['en_GB']['SilvercartRegisterConfirmationPage']['ALREADY_REGISTERES_MESSAGE_TEXT'] = 'Message: user already registered';
$lang['en_GB']['SilvercartRegisterConfirmationPage']['CONFIRMATIONMAIL_SUBJECT'] = 'Confirmation mail: subject';
$lang['en_GB']['SilvercartRegisterConfirmationPage']['CONFIRMATIONMAIL_TEXT'] = 'Confirmation mail: text';
$lang['en_GB']['SilvercartRegisterConfirmationPage']['CONFIRMATION_MAIL'] = 'Confirmation mail';
$lang['en_GB']['SilvercartRegisterConfirmationPage']['CONTENT'] = '<p>Dear customer,</p>';
$lang['en_GB']['SilvercartRegisterConfirmationPage']['CONFIRMATIONFAILUREMESSAGE'] = '<p>Your account could not be activated.</p>';
$lang['en_GB']['SilvercartRegisterConfirmationPage']['CONFIRMATIONSUCCESSMESSAGE'] = '<p>Your registration is complete! For your comfort you are already logged in.</p><p>Have fun!</p>';
$lang['en_GB']['SilvercartRegisterConfirmationPage']['ALREADYCONFIRMEDMESSAGE'] = '<p>Your account is already activated.</p>';
$lang['en_GB']['SilvercartRegisterConfirmationPage']['FAILURE_MESSAGE_TEXT'] = 'Failure message';
$lang['en_GB']['SilvercartRegisterConfirmationPage']['PLURALNAME'] = 'Register confirmation pages';
$lang['en_GB']['SilvercartRegisterConfirmationPage']['SINGULARNAME'] = 'Register confirmation page';
$lang['en_GB']['SilvercartRegisterConfirmationPage']['SUCCESS_MESSAGE_TEXT'] = 'Success message';
$lang['en_GB']['SilvercartRegisterConfirmationPage']['TITLE'] = 'Register confirmation page';
$lang['en_GB']['SilvercartRegisterConfirmationPage']['URL_SEGMENT'] = 'register-confirmation';

$lang['en_GB']['SilvercartRegisterWelcomePage']['CONTENT'] = '<p>Thank you for registering at our ecommerce shop. We sent you an email with directions to activate your account. Please check your inbox.</p><p>Thank you!</p>';
$lang['en_GB']['SilvercartRegisterWelcomePage']['PLURALNAME'] = 'Register Welcome pages';
$lang['en_GB']['SilvercartRegisterWelcomePage']['SINGULARNAME'] = 'Register Welcome Page';

$lang['en_GB']['SilvercartRegistrationPage']['ACTIVATION_MAIL_TAB'] = 'Activationmail';
$lang['en_GB']['SilvercartRegistrationPage']['ACTIVATION_MAIL_SUBJECT'] = 'Activation mail subject';
$lang['en_GB']['SilvercartRegistrationPage']['ACTIVATION_MAIL_TEXT'] = 'Activation mail text';
$lang['en_GB']['SilvercartRegistrationPage']['CONFIRMATION_TEXT'] = '<h1>Complete registration</h1><p>Please confirm your activation or copy the link to your Browser.</p><p><a href="$ConfirmationLink">Confirm registration</a></p><p>In case You did not register please ignore this mail.</p><p>Your shop team</p>';
$lang['en_GB']['SilvercartRegistrationPage']['CUSTOMER_SALUTATION'] = 'Dear customer\,';
$lang['en_GB']['SilvercartRegistrationPage']['EMAIL_EXISTS_ALREADY'] = 'This email address already exists.';
$lang['en_GB']['SilvercartRegistrationPage']['PLEASE_COFIRM'] = 'Please confirm your registration';
$lang['en_GB']['SilvercartRegistrationPage']['PLURALNAME'] = 'Registration pages';
$lang['en_GB']['SilvercartRegistrationPage']['SINGULARNAME'] = 'Registration Page';
$lang['en_GB']['SilvercartRegistrationPage']['SUCCESS_TEXT'] = '<h1>Registration completed successfully!</h1><p>Many thanks for your registration.</p><p>Have a nice time on our website!</p><p>Your webshop team</p>';
$lang['en_GB']['SilvercartRegistrationPage']['THANKS'] = 'Many thanks for your registration';
$lang['en_GB']['SilvercartRegistrationPage']['TITLE'] = 'Registration page';
$lang['en_GB']['SilvercartRegistrationPage']['URL_SEGMENT'] = 'registration';
$lang['en_GB']['SilvercartRegistrationPage']['YOUR_REGISTRATION'] = 'Your registration';

$lang['en_GB']['SilvercartRegularCustomer']['PLURALNAME'] = 'Regular Customers';
$lang['en_GB']['SilvercartRegularCustomer']['REGULARCUSTOMER'] = 'Regular customer';
$lang['en_GB']['SilvercartRegularCustomer']['REGULARCUSTOMER_OPTIN'] = 'Regular customer unconfirmed';
$lang['en_GB']['SilvercartRegularCustomer']['SINGULARNAME'] = 'Regular Customer';

$lang['en_GB']['SilvercartSearchResultsPage']['PLURALNAME'] = 'Search Results pages';
$lang['en_GB']['SilvercartSearchResultsPage']['SINGULARNAME'] = 'Search Results Page';
$lang['en_GB']['SilvercartSearchResultsPage']['TITLE'] = 'Search results';
$lang['en_GB']['SilvercartSearchResultsPage']['URL_SEGMENT'] = 'search-results';
$lang['en_GB']['SilvercartSearchResultsPage']['RESULTTEXT'] = 'Search results for query <b>&rdquo;%s&rdquo;</b>';

$lang['en_GB']['SilvercartShippingAddress']['PLURALNAME'] = 'Shipping addresses';
$lang['en_GB']['SilvercartShippingAddress']['SINGULARNAME'] = 'Shipping address';

$lang['en_GB']['SilvercartShippingFee']['ATTRIBUTED_SHIPPINGMETHOD'] = 'Attributed shipping method';
$lang['en_GB']['SilvercartShippingFee']['COSTS'] = 'Costs';
$lang['en_GB']['SilvercartShippingFee']['EMPTYSTRING_CHOOSEZONE'] = '--choose zone--';
$lang['en_GB']['SilvercartShippingFee']['FOR_SHIPPINGMETHOD'] = 'For shipping method';
$lang['en_GB']['SilvercartShippingFee']['MAXIMUM_WEIGHT'] = 'Maximum weight (g)';
$lang['en_GB']['SilvercartShippingFee']['PLURALNAME'] = 'Shipping fees';
$lang['en_GB']['SilvercartShippingFee']['SINGULARNAME'] = 'Shipping fee';
$lang['en_GB']['SilvercartShippingFee']['UNLIMITED_WEIGHT'] = 'unlimited';
$lang['en_GB']['SilvercartShippingFee']['UNLIMITED_WEIGHT_LABEL'] = 'Unlimited Maximum Weight';
$lang['en_GB']['SilvercartShippingFee']['ZONE_WITH_DESCRIPTION'] = 'Zone (only carrier\'s zones available)';

$lang['en_GB']['SilvercartShippingFeesPage']['PLURALNAME'] = 'Shipping Fees pages';
$lang['en_GB']['SilvercartShippingFeesPage']['SINGULARNAME'] = 'Shipping Fees Page';
$lang['en_GB']['SilvercartShippingFeesPage']['TITLE'] = 'Shipping fees';
$lang['en_GB']['SilvercartShippingFeesPage']['URL_SEGMENT'] = 'shipping-fees';

$lang['en_GB']['SilvercartShippingMethod']['FOR_PAYMENTMETHODS'] = 'For payment methods';
$lang['en_GB']['SilvercartShippingMethod']['FOR_ZONES'] = 'For zones';
$lang['en_GB']['SilvercartShippingMethod']['PACKAGE'] = 'Package';
$lang['en_GB']['SilvercartShippingMethod']['PLURALNAME'] = 'Shipping methods';
$lang['en_GB']['SilvercartShippingMethod']['SINGULARNAME'] = 'Shipping method';

$lang['en_GB']['SilvercartShippingMethodTexts']['PLURALNAME'] = 'Shipping method translations';
$lang['en_GB']['SilvercartShippingMethodTexts']['SINGULARNAME'] = 'Shipping method translation';

$lang['en_GB']['SilvercartShopAdmin']['PAYMENT_DESCRIPTION'] = 'Description';
$lang['en_GB']['SilvercartShopAdmin']['PAYMENT_ISACTIVE'] = 'Activated';
$lang['en_GB']['SilvercartShopAdmin']['PAYMENT_MAXAMOUNTFORACTIVATION'] = 'Maximum amount';
$lang['en_GB']['SilvercartShopAdmin']['PAYMENT_MINAMOUNTFORACTIVATION'] = 'Minimum amount';
$lang['en_GB']['SilvercartShopAdmin']['PAYMENT_MODE_DEV'] = 'Dev';
$lang['en_GB']['SilvercartShopAdmin']['PAYMENT_MODE_LIVE'] = 'Live';
$lang['en_GB']['SilvercartShopAdmin']['SHOW_PAYMENT_LOGOS'] = 'Show logos in frontend';

$lang['en_GB']['SilvercartShopConfigurationAdmin']['SILVERCART_CONFIG'] = 'SilverCart Configuration';

$lang['en_GB']['SilvercartShopEmail']['EMAILTEXT'] = 'Message';
$lang['en_GB']['SilvercartShopEmail']['IDENTIFIER'] = 'Identifier';
$lang['en_GB']['SilvercartShopEmail']['PLURALNAME'] = 'Shop Emails';
$lang['en_GB']['SilvercartShopEmail']['SINGULARNAME'] = 'Shop Email';
$lang['en_GB']['SilvercartShopEmail']['SUBJECT'] = 'Subject';
$lang['en_GB']['SilvercartShopEmail']['VARIABLES'] = 'Variables';
$lang['en_GB']['SilvercartShopEmail']['REGARDS'] = 'Best regards';
$lang['en_GB']['SilvercartShopEmail']['YOUR_TEAM'] = 'Your SilverCart ecommerce team';
$lang['en_GB']['SilvercartShopEmail']['HELLO'] = 'Hello';
$lang['en_GB']['SilvercartShopEmail']['ORDER_ARRIVED'] = 'We just received your order, many thanks.';

$lang['en_GB']['SilvercartShoppingCart']['ERROR_MINIMUMORDERVALUE_NOT_REACHED'] = 'The minimum order value is %s';
$lang['en_GB']['SilvercartShoppingCart']['PLURALNAME'] = 'Carts';
$lang['en_GB']['SilvercartShoppingCart']['SINGULARNAME'] = 'Cart';

$lang['en_GB']['SilvercartShoppingCartPosition']['PLURALNAME'] = 'Cart positions';
$lang['en_GB']['SilvercartShoppingCartPosition']['SINGULARNAME'] = 'Cart position';

$lang['en_GB']['SilvercartTax']['LABEL'] = 'label';
$lang['en_GB']['SilvercartTax']['PLURALNAME'] = 'Rates';
$lang['en_GB']['SilvercartTax']['RATE_IN_PERCENT'] = 'Rate in %';
$lang['en_GB']['SilvercartTax']['SINGULARNAME'] = 'Rate';

$lang['en_GB']['SilvercartTermsAndConditionsPage']['PLURALNAME'] = 'Terms and conditions pages';
$lang['en_GB']['SilvercartTermsAndConditionsPage']['SINGULARNAME'] = 'Terms and conditions page';

$lang['en_GB']['SilvercartUpdate']['DESCRIPTION'] = 'Description';
$lang['en_GB']['SilvercartUpdate']['SILVERCARTVERSION'] = 'Version';
$lang['en_GB']['SilvercartUpdate']['SILVERCARTUPDATEVERSION'] = 'Update';
$lang['en_GB']['SilvercartUpdate']['STATUS'] = 'Status';
$lang['en_GB']['SilvercartUpdate']['STATUSMESSAGE'] = 'Status message';
$lang['en_GB']['SilvercartUpdate']['STATUS_DONE'] = 'Completed';
$lang['en_GB']['SilvercartUpdate']['STATUS_REMAINING'] = 'Remaining';
$lang['en_GB']['SilvercartUpdate']['STATUS_SKIPPED'] = 'Skipped';
$lang['en_GB']['SilvercartUpdate']['STATUSMESSAGE_DONE'] = 'This update has successfully completed.';
$lang['en_GB']['SilvercartUpdate']['STATUSMESSAGE_REMAINING'] = 'This update is available.';
$lang['en_GB']['SilvercartUpdate']['STATUSMESSAGE_SKIPPED'] = 'This update is already integrated.';
$lang['en_GB']['SilvercartUpdate']['STATUSMESSAGE_SKIPPED_TO_PREVENT_DAMAGE'] = 'Manual changes detected. This update was skipped to prevent damage to existing data.';
$lang['en_GB']['SilvercartUpdate']['STATUSMESSAGE_ERROR'] = 'An unknown error occured.';

$lang['en_GB']['SilvercartUpdateAdmin']['SILVERCART_UPDATE'] = 'SilverCart Updates';

$lang['en_GB']['SilvercartWidgets']['WIDGETSET_CONTENT_FIELD_LABEL'] = 'Widgets for the content area';
$lang['en_GB']['SilvercartWidgets']['WIDGETSET_SIDEBAR_FIELD_LABEL'] = 'Widgets for the sidebar';

$lang['en_GB']['SilvercartWidgetSet']['PLURALNAME'] = 'Widget Sets';
$lang['en_GB']['SilvercartWidgetSet']['SINGULARNAME'] = 'Widget Set';

$lang['en_GB']['SilvercartZone']['ATTRIBUTED_COUNTRIES'] = 'Attributed countries';
$lang['en_GB']['SilvercartZone']['ATTRIBUTED_SHIPPINGMETHODS'] = 'Attributed shipping methods';
$lang['en_GB']['SilvercartZone']['COUNTRIES'] = 'Countries';
$lang['en_GB']['SilvercartZone']['DOMESTIC'] = 'Domestic';
$lang['en_GB']['SilvercartZone']['FOR_COUNTRIES'] = 'For countries';
$lang['en_GB']['SilvercartZone']['PLURALNAME'] = 'Zones';
$lang['en_GB']['SilvercartZone']['SINGULARNAME'] = 'Zone';

$lang['en_GB']['SilvercartAmountUnit']['NAME'] = 'Name';
$lang['en_GB']['SilvercartAmountUnit']['ABBREVIATION'] = 'Abbreviation';
$lang['en_GB']['SilvercartAmountUnit']['SINGULARNAME'] = 'amount unit';
$lang['en_GB']['SilvercartAmountUnit']['PLURALNAME'] = 'amount units';

// Widgets ----------------------------------------------------------------- */

$lang['en_GB']['SilvercartLoginWidget']['TITLE']                    = 'Login';
$lang['en_GB']['SilvercartLoginWidget']['TITLE_LOGGED_IN']          = 'My account';
$lang['en_GB']['SilvercartLoginWidget']['TITLE_NOT_LOGGED_IN']      = 'Login';
$lang['en_GB']['SilvercartLoginWidget']['CMSTITLE']                 = 'Silvercart login';
$lang['en_GB']['SilvercartLoginWidget']['DESCRIPTION']              = 'This widget shows a login form and links to the registration page. If the customer is logged in already links to their account section are shown instead.';

$lang['en_GB']['SilvercartProductGroupItemsWidget']['TITLE']                        = 'Product group';
$lang['en_GB']['SilvercartProductGroupItemsWidget']['CMSTITLE']                     = 'Silvercart product group items';
$lang['en_GB']['SilvercartProductGroupItemsWidget']['DESCRIPTION']                  = 'This widget displays products of a definable productgroup. You can define how many products from which product group should be shown.';
$lang['en_GB']['SilvercartProductGroupItemsWidget']['STOREADMIN_FIELDLABEL']        = 'Please choose the product group to display:';
$lang['en_GB']['SilvercartProductGroupItemsWidget']['STOREADMIN_NUMBEROFPRODUCTS']  = 'Number of products to show:';

$lang['en_GB']['SilvercartSearchWidget']['TITLE']                   = 'Are you looking for something?';
$lang['en_GB']['SilvercartSearchWidget']['CMSTITLE']                = 'Silvercart search';
$lang['en_GB']['SilvercartSearchWidget']['DESCRIPTION']             = 'This widget shows the product search form.';

$lang['en_GB']['SilvercartSearchWidgetForm']['SEARCHLABEL']         = 'Enter your search term:';
$lang['en_GB']['SilvercartSearchWidgetForm']['SUBMITBUTTONTITLE']   = 'Search';

$lang['en_GB']['SilvercartShoppingcartWidget']['TITLE']                 = 'Shoppingc art';
$lang['en_GB']['SilvercartShoppingcartWidget']['CMSTITLE']              = 'Silvercart shopping cart';
$lang['en_GB']['SilvercartShoppingcartWidget']['DESCRIPTION']           = 'This widget shows the content of the customers\' shopping cart. Additionally it provides links to the shopping cart and (if there are products in the cart) the checkout pages.';

$lang['en_GB']['SilvercartText']['TITLE']               = 'Free text';
$lang['en_GB']['SilvercartText']['DESCRIPTION']         = 'Enter any text you want.';
$lang['en_GB']['SilvercartText']['FREETEXTFIELD_LABEL'] = 'Your text:';

$lang['en_GB']['SilvercartTopsellerProductsWidget']['TITLE']                    = 'Top sellers';
$lang['en_GB']['SilvercartTopsellerProductsWidget']['CMSTITLE']                 = 'Silvercart top selling products';
$lang['en_GB']['SilvercartTopsellerProductsWidget']['DESCRIPTION']              = 'This widget shows a configurable number of top selling products.';
$lang['en_GB']['SilvercartTopsellerProductsWidget']['STOREADMIN_FIELDLABEL']    = 'Number of products to show:';

$lang['en_GB']['SilvercartProductGroupNavigationWidget']['TITLE']           = 'Product group navigation';
$lang['en_GB']['SilvercartProductGroupNavigationWidget']['CMSTITLE']        = 'Silvercart product group navigation';
$lang['en_GB']['SilvercartProductGroupNavigationWidget']['DESCRIPTION']     = 'This widget creates a hierarchical navigation for product groups. You can define what productgroup should be used as root.';

