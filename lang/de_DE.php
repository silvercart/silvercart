<?php

/**
 * German (Germany) language pack
 * @package modules: silvercart
 */
i18n::include_locale_file('silvercart', 'en_US');

global $lang;

if (array_key_exists('de_DE', $lang) && is_array($lang['de_DE'])) {
    $lang['de_DE'] = array_merge($lang['en_US'], $lang['de_DE']);
} else {
    $lang['de_DE'] = $lang['en_US'];
}

$lang['de_DE']['PaymentMethod']['SHIPPINGMETHOD'] = 'Versandart';
$lang['de_DE']['ShopAdmin']['PAYMENT_ISACTIVE'] = 'aktiviert';
$lang['de_DE']['ShopAdmin']['PAYMENT_MINAMOUNTFORACTIVATION'] = 'Mindestbetrag für das Modul';
$lang['de_DE']['ShopAdmin']['PAYMENT_MAXAMOUNTFORACTIVATION'] = 'Höchstbetrag für das Modul';
$lang['de_DE']['PaymentMethod']['STANDARD_ORDER_STATUS'] = 'Standartstatus für eine Bestellung mit dieser Zahlart';
$lang['de_DE']['PaymentMethod']['BASIC_SETTINGS'] = 'Grundeinstellungen';
$lang['de_DE']['Zone']['COUNTRIES'] = 'Laender';
$lang['de_DE']['Zone']['SINGULARNAME'] = 'Zone';
$lang['de_DE']['Zone']['PLURALNAME'] = 'Zonen';
$lang['de_DE']['Article']['MASTERARTICLE'] = 'Masterartikel';
$lang['de_DE']['Article']['CHOOSE_MASTER'] = '-- Master wählen --';
$lang['de_DE']['Article']['QUANTITY'] = 'Anzahl';
$lang['de_DE']['Article']['ADD_TO_CART'] = 'in den Warenkorb';
$lang['de_DE']['ShopAdmin']['EMAIL_IDENTIFIER'] = 'Bezeichner';
$lang['de_DE']['ShopAdmin']['PAYMENT_NAME'] = 'Name';
$lang['de_DE']['ShopAdmin']['PAYMENTMETHODS'] = 'Bezahlarten';
$lang['de_DE']['ShopAdmin']['SHIPPING_TITLE'] = 'Name';
$lang['de_DE']['ShopAdmin']['SHIPPING_ISACTIVE'] = 'aktiviert';
$lang['de_DE']['ShopAdmin']['SHIPPING_MINAMOUNTFORACTIVATION'] = 'Mindestbetrag für das Modul';
$lang['de_DE']['ShopAdmin']['SHIPPING_MAXAMOUNTFORACTIVATION'] = 'Höchstbetrag für das Modul';
$lang['de_DE']['ShopAdmin']['SHIPPINGMETHODS'] = 'Versandarten';
$lang['de_DE']['ShopAdmin']['ZONE_NAME'] = 'Name';
$lang['de_DE']['ShopAdmin']['ZONE_ISACTIVE'] = 'aktiviert';
$lang['de_DE']['ShopAdmin']['ZONE_MINAMOUNTFORACTIVATION'] = 'Mindestbetrag für das Modul';
$lang['de_DE']['ShopAdmin']['ZONE_MAXAMOUNTFORACTIVATION'] = 'Höchstbetrag für das Modul';
$lang['de_DE']['ShopAdmin']['ZONES'] = 'Zonen';
$lang['de_DE']['ShopAdmin']['TAX_TITLE'] = 'Titel';
$lang['de_DE']['ShopAdmin']['TAX_RATE'] = 'Steuersatz in Prozent';
$lang['de_DE']['ShopAdmin']['TAXRATES'] = 'Steuersätze';
$lang['de_DE']['ShopAdmin']['EMAIL_SUBJECT'] = 'Betreff';
$lang['de_DE']['ShopAdmin']['EMAIL_TEXT'] = 'Text';
$lang['de_DE']['ShopAdmin']['EMAIL_VARIABLES'] = 'Variablen';
$lang['de_DE']['ShopAdmin']['EMAILS'] = 'Emails';
$lang['de_DE']['ShopAdmin']['EMAIL_TITLE'] = 'Titel';
$lang['de_DE']['CheckoutFormStep1']['EMPTYSTRING_COUNTRY'] = '--Land wählen--';
$lang['de_DE']['CheckoutFormStep2']['EMPTYSTRING_PAYMENTMETHOD'] = '--Bezahlart wählen--';
$lang['de_DE']['CheckoutFormStep3']['EMPTYSTRING_SHIPPINGMETHOD'] = '--Versandart wählen--';
$lang['de_DE']['EditAddressForm']['EMPTYSTRING_PLEASECHOOSE'] = '--bitte wählen--';
$lang['de_DE']['Page']['FIND'] = 'finden:';
$lang['de_DE']['Page']['CART'] = 'Warenkorb';
$lang['de_DE']['Page']['CHECKOUT'] = 'Checkout';
$lang['de_DE']['Page']['SHOWINPAGE'] = 'Sprache auf %s stellen';
$lang['de_DE']['AnonymousCustomer']['ANONYMOUSCUSTOMER'] = 'Anonyme Kunden';
$lang['de_DE']['BusinessCustomer']['BUSINESSCUSTOMER'] = 'Geschäftskunden';
$lang['de_DE']['RegularCustomer']['REGULARCUSTOMER'] = 'Endkunden';
$lang['de_DE']['ShippingFee']['ZONE_WITH_DESCRIPTION'] = 'Zone (nur die des Frachtführers verfügbar)';
$lang['de_DE']['ShippingFee']['EMPTYSTRING_CHOOSEZONE'] = '--Zone wählen--';
