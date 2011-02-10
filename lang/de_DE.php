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

$lang['de_DE']['Address']['ADDITION'] = 'Addresszusatz';
$lang['de_DE']['Address']['CITY'] = 'Ort';
$lang['de_DE']['Address']['EMAIL'] = 'Emailadresse';
$lang['de_DE']['Address']['FIRSTNAME'] = 'Vorname';
$lang['de_DE']['Address']['MISSIS'] = 'Frau';
$lang['de_DE']['Address']['MISTER'] = 'Herr';
$lang['de_DE']['Address']['PHONE'] = 'Telefonnummer';
$lang['de_DE']['Address']['PHONEAREACODE'] = 'Vorwahl';
$lang['de_DE']['Address']['PLURALNAME'] = array(
    'Adressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Address']['POSTCODE'] = 'PLZ';
$lang['de_DE']['Address']['SALUTATION'] = 'Anrede';
$lang['de_DE']['Address']['SINGULARNAME'] = array(
    'Adresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Address']['STREET'] = 'Straße';
$lang['de_DE']['Address']['STREETNUMBER'] = 'Hausnummer';
$lang['de_DE']['Address']['SURNAME'] = 'Nachname';
$lang['de_DE']['AddressHolder']['EDIT'] = 'bearbeiten';
$lang['de_DE']['AddressHolder']['INVOICEADDRESS'] = 'Rechnungsadresse';
$lang['de_DE']['AddressHolder']['PLURALNAME'] = array(
    'Adressübersichtseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['AddressHolder']['SHIPPINGADDRESS'] = 'Versandadresse';
$lang['de_DE']['AddressHolder']['SINGULARNAME'] = array(
    'Adressübersichtseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['AddressHolder']['TITLE'] = 'Adressübersicht';
$lang['de_DE']['AddressHolder']['URL_SEGMENT'] = 'addressuebersicht';
$lang['de_DE']['AddressPage']['PLURALNAME'] = array(
    'Adressseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['AddressPage']['SINGULARNAME'] = array(
    'Adressseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['AddressPage']['TITLE'] = 'Adressdetails';
$lang['de_DE']['AddressPage']['URL_SEGMENT'] = 'adressdetails';
$lang['de_DE']['AnonymousCustomer']['ANONYMOUSCUSTOMER'] = 'Anonymer Kunde';
$lang['de_DE']['AnonymousCustomer']['PLURALNAME'] = array(
    'Anonyme Kunden',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['AnonymousCustomer']['SINGULARNAME'] = array(
    'Anonymer Kunde',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Article']['ADD_TO_CART'] = 'in den Warenkorb';
$lang['de_DE']['Article']['CHOOSE_MASTER'] = '-- Master wählen --';
$lang['de_DE']['Article']['DESCRIPTION'] = 'Artikelbeschreibung';
$lang['de_DE']['Article']['FREE_OF_CHARGE'] = 'Masterartikel';
$lang['de_DE']['Article']['MSRP'] = 'UVP';
$lang['de_DE']['Article']['PLURALNAME'] = array(
    'Artikel',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Article']['PRICE'] = 'Preis';
$lang['de_DE']['Article']['PRICE_SINGLE'] = 'Einzelpreis';
$lang['de_DE']['Article']['PURCHASEPRICE'] = 'Einkaufspreis';
$lang['de_DE']['Article']['QUANTITY'] = 'Anzahl';
$lang['de_DE']['Article']['SINGULARNAME'] = array(
    'Artikel',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Article']['TITLE'] = 'Artikel';
$lang['de_DE']['Article']['VAT'] = 'MwSt';
$lang['de_DE']['Article']['WEIGHT'] = 'Gewicht';
$lang['de_DE']['ArticleCategoryHolder']['PLURALNAME'] = array(
    'Artikelkategorieübersichten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticleCategoryHolder']['SINGULARNAME'] = array(
    'Artikelkategorieübersicht',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ArticleCategoryHolder']['TITLE'] = 'Kategorieübersicht';
$lang['de_DE']['ArticleCategoryHolder']['URL_SEGMENT'] = 'kategorieuebersicht';
$lang['de_DE']['ArticleCategoryPage']['ARTICLES'] = 'Artikel';
$lang['de_DE']['ArticleCategoryPage']['CATEGORY_PICTURE'] = 'Bild der Kategorie';
$lang['de_DE']['ArticleCategoryPage']['COLUMN_TITLE'] = 'Name';
$lang['de_DE']['ArticleCategoryPage']['PLURALNAME'] = array(
    'Artikelkategorieseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticleCategoryPage']['SINGULARNAME'] = array(
    'Artikelkategorieseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ArticleGroupHolder']['PLURALNAME'] = array(
    'Artikelgruppenübersichten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticleGroupHolder']['SINGULARNAME'] = array(
    'Artikelgruppenübersicht',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ArticleGroupHolder']['URL_SEGMENT'] = 'artikelgruppen';
$lang['de_DE']['ArticleGroupPage']['ATTRIBUTES'] = 'Attribut';
$lang['de_DE']['ArticleGroupPage']['GROUP_PICTURE'] = 'Bild der Gruppe';
$lang['de_DE']['ArticleGroupPage']['PLURALNAME'] = array(
    'Warengruppen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticleGroupPage']['SINGULARNAME'] = array(
    'Warengruppe',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ArticleImageGallery']['PLURALNAME'] = array(
    'Gallerien',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticleImageGallery']['SINGULARNAME'] = array(
    'Gallerie',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ArticlePage']['ADD_TO_CART'] = 'in den Warenkorb';
$lang['de_DE']['ArticlePage']['PLURALNAME'] = array(
    'Artikeldetailseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticlePage']['QUANTITY'] = 'Anzahl';
$lang['de_DE']['ArticlePage']['SINGULARNAME'] = array(
    'Artikeldetailseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ArticlePage']['URL_SEGMENT'] = 'artikeldetails';
$lang['de_DE']['ArticleTexts']['PLURALNAME'] = array(
    'article translation texts',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticleTexts']['SINGULARNAME'] = array(
    'article translation text',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Attribute']['PLURALNAME'] = array(
    'Attribute',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Attribute']['SINGULARNAME'] = array(
    'Attribut',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['BusinessCustomer']['BUSINESSCUSTOMER'] = 'Geschäftskunde';
$lang['de_DE']['BusinessCustomer']['PLURALNAME'] = array(
    'Geschäftskunden',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['BusinessCustomer']['SINGULARNAME'] = array(
    'Geschäftskunde',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Carrier']['ATTRIBUTED_SHIPPINGMETHODS'] = 'zugeordnete Versandart';
$lang['de_DE']['Carrier']['FULL_NAME'] = 'voller Name';
$lang['de_DE']['Carrier']['PLURALNAME'] = array(
    'Frachtführer',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Carrier']['SINGULARNAME'] = array(
    'Frachtführer',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['CartPage']['PLURALNAME'] = array(
    'Warenkorbseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['CartPage']['SINGULARNAME'] = array(
    'Warenkorbseiten',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['CartPage']['URL_SEGMENT'] = 'warenkorb';
$lang['de_DE']['CheckoutFormStep']['CHOOSEN_PAYMENT'] = 'gewählte Bezahlart';
$lang['de_DE']['CheckoutFormStep']['CHOOSEN_SHIPPING'] = 'gewählte Versandart';
$lang['de_DE']['CheckoutFormStep']['I_ACCEPT_REVOCATION'] = 'Ich akzeptiere die Wiederufsbelehrung';
$lang['de_DE']['CheckoutFormStep']['I_ACCEPT_TERMS'] = 'Ich akzeptiere die Allgemeinen Geschäftsbedingungen.';
$lang['de_DE']['CheckoutFormStep']['I_SUBSCRIBE_NEWSLETTER'] = 'Ich möchte den Newsletter abonnieren.';
$lang['de_DE']['CheckoutFormStep']['ORDER'] = 'Bestellung';
$lang['de_DE']['CheckoutFormStep']['OVERVIEW'] = 'Übersicht';
$lang['de_DE']['CheckoutFormStep1']['EMPTYSTRING_COUNTRY'] = '--Land--';
$lang['de_DE']['CheckoutFormStep2']['EMPTYSTRING_PAYMENTMETHOD'] = '--Zahlart wählen--';
$lang['de_DE']['CheckoutFormStep3']['EMPTYSTRING_SHIPPINGMETHOD'] = '--Versandart wählen--';
$lang['de_DE']['CheckoutStep']['PLURALNAME'] = array(
    'Checkout Schritte',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['CheckoutStep']['SINGULARNAME'] = array(
    'Checkout Schritt',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['CheckoutStep']['URL_SEGMENT'] = 'checkout';
$lang['de_DE']['ContactFormPage']['PLURALNAME'] = array(
    'Kontaktformularseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ContactFormPage']['SINGULARNAME'] = array(
    'Kontaktformularseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ContactFormPage']['TITLE'] = 'Kontakt';
$lang['de_DE']['ContactFormPage']['URL_SEGMENT'] = 'kontakt';
$lang['de_DE']['ContactFormResponsePage']['CONTACT_CONFIRMATION'] = 'Kontaktbestätigung';
$lang['de_DE']['ContactFormResponsePage']['PLURALNAME'] = array(
    'Kontaktformularantwortseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ContactFormResponsePage']['SINGULARNAME'] = array(
    'Kontaktformularantwortseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ContactFormResponsePage']['URL_SEGMENT'] = 'kontaktbestaetigung';
$lang['de_DE']['Country']['ATTRIBUTED_PAYMENTMETHOD'] = 'zugeordnete Bezahlart';
$lang['de_DE']['Country']['ATTRIBUTED_ZONES'] = 'zugeordnete Zonen';
$lang['de_DE']['Country']['PLURALNAME'] = array(
    'Länder',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Country']['SINGULARNAME'] = array(
    'Land',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['CustomerAdmin']['customers'] = 'Kunden';
$lang['de_DE']['CustomerCategory']['PLURALNAME'] = array(
    'Kundengruppen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['CustomerCategory']['SINGULARNAME'] = array(
    'Kundengruppe',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['DataPage']['PLURALNAME'] = array(
    'Datenseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['DataPage']['SINGULARNAME'] = array(
    'Datenseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['DataPage']['TITLE'] = 'meine Daten';
$lang['de_DE']['DataPage']['URL_SEGMENT'] = 'meine-daten';
$lang['de_DE']['DataPrivacyStatementPage']['PLURALNAME'] = array(
    'Datenschutzseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['DataPrivacyStatementPage']['SINGULARNAME'] = array(
    'Datenschutzseiten',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['DataPrivacyStatementPage']['TITLE'] = 'Datenschutzerklärung';
$lang['de_DE']['DataPrivacyStatementPage']['URL_SEGMENT'] = 'datenschutzerklaerung';
$lang['de_DE']['EditAddressForm']['EMPTYSTRING_PLEASECHOOSE'] = '--bitte wählen--';
$lang['de_DE']['EmailTemplates']['PLURALNAME'] = array(
    'Emailvorlagen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['EmailTemplates']['SINGULARNAME'] = array(
    'Emailvorlage',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['FooterNavigationHolder']['PLURALNAME'] = array(
    'Footernavigationsübersichten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['FooterNavigationHolder']['SINGULARNAME'] = array(
    'Footernavigationsübersichten',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['FooterNavigationHolder']['URL_SEGMENT'] = 'footernavigation';
$lang['de_DE']['FrontPage']['DEFAULT_CONTENT'] = '<h2>Willkommen im <strong>SilverCart</strong> Webshop!</h2>';
$lang['de_DE']['FrontPage']['PLURALNAME'] = array(
    'Frontseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['FrontPage']['SINGULARNAME'] = array(
    'Frontseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['HandlingCost']['PLURALNAME'] = array(
    'Bearbeitungskosten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['HandlingCost']['SINGULARNAME'] = array(
    'Bearbeitungskosten',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['InvoiceAddress']['PLURALNAME'] = array(
    'Rechnungsadressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['InvoiceAddress']['SINGULARNAME'] = array(
    'Rechnungsadresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Manufacturer']['PLURALNAME'] = array(
    'Hersteller',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Manufacturer']['SINGULARNAME'] = array(
    'Hersteller',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['MetaNavigationHolder']['PLURALNAME'] = array(
    'Metanavigationsübersichten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['MetaNavigationHolder']['SINGULARNAME'] = array(
    'Metanavigationsübersicht',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['MetaNavigationHolder']['URL_SEGMENT'] = 'metanavigation';
$lang['de_DE']['MyAccountHolder']['PLURALNAME'] = array(
    'Accountübersichten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['MyAccountHolder']['SINGULARNAME'] = array(
    'Accountübersicht',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['MyAccountHolder']['TITLE'] = 'mein Konto';
$lang['de_DE']['MyAccountHolder']['URL_SEGMENT'] = 'myaccount';
$lang['de_DE']['Order']['CONFIRMED'] = 'confirmed?';
$lang['de_DE']['Order']['CUSTOMER'] = 'customer';
$lang['de_DE']['Order']['ORDER_ID'] = 'order id';
$lang['de_DE']['Order']['ORDER_VALUE'] = 'order value';
$lang['de_DE']['Order']['PLURALNAME'] = array(
    'Bestellung',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Order']['SHIPPINGRATE'] = 'Versandkosten';
$lang['de_DE']['Order']['SINGULARNAME'] = array(
    'Bestellung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Order']['STATUS'] = 'order status';
$lang['de_DE']['OrderAddress']['PLURALNAME'] = array(
    'Bestelladressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderAddress']['SINGULARNAME'] = array(
    'Bestelladresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderConfirmationPage']['PLURALNAME'] = array(
    'Bestellbestätigungsseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderConfirmationPage']['SINGULARNAME'] = array(
    'Bestellbestätigungsseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderConfirmationPage']['URL_SEGMENT'] = 'bestellbestaetigung';
$lang['de_DE']['OrderDetailPage']['PLURALNAME'] = array(
    'Bestelldetailsseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderDetailPage']['SINGULARNAME'] = array(
    'Bestelldetailsseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderDetailPage']['TITLE'] = 'Bestelldetails';
$lang['de_DE']['OrderDetailPage']['URL_SEGMENT'] = 'bestelldetails';
$lang['de_DE']['OrderHolder']['PLURALNAME'] = array(
    'Bestellübersichten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderHolder']['SINGULARNAME'] = array(
    'Bestellübersicht',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderHolder']['TITLE'] = 'meine Bestellungen';
$lang['de_DE']['OrderHolder']['URL_SEGMENT'] = 'meine-bestellungen';
$lang['de_DE']['OrderInvoiceAddress']['PLURALNAME'] = array(
    'Rechnungsadressen der Bestellungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderInvoiceAddress']['SINGULARNAME'] = array(
    'Rechnungsadresse der Bestellung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderPosition']['PLURALNAME'] = array(
    'Bestellpositionen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderPosition']['SINGULARNAME'] = array(
    'Bestellposition',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderShippingAddress']['PLURALNAME'] = array(
    'Versandadressen derBestellungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderShippingAddress']['SINGULARNAME'] = array(
    'Versandadresse der Bestellung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderStatus']['PAYED'] = 'payed';
$lang['de_DE']['OrderStatus']['PLURALNAME'] = array(
    'Bestellstati',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderStatus']['SINGULARNAME'] = array(
    'Bestellstatus',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderStatus']['WAITING_FOR_PAYMENT'] = array(
    'Auf Zahlungseingang wird gewartet',
    null,
    'Auf Zahlungseingang wird gewartet'
);
$lang['de_DE']['OrderStatusTexts']['PLURALNAME'] = array(
    'Bestellstatustexte',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderStatusTexts']['SINGULARNAME'] = array(
    'Bestellstatustext',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Page']['ABOUT_US'] = 'über uns';
$lang['de_DE']['Page']['ABOUT_US_URL_SEGMENT'] = 'ueber-uns';
$lang['de_DE']['Page']['ACCESS_CREDENTIALS_CALL'] = 'Bitte geben Sie Ihre Zugangsdaten ein:';
$lang['de_DE']['Page']['ADDRESS'] = 'Adresse';
$lang['de_DE']['Page']['ADDRESSINFORMATION'] = 'Adressinformationen';
$lang['de_DE']['Page']['ADDRESS_DATA'] = 'Adressdaten';
$lang['de_DE']['Page']['ALREADY_REGISTERED'] = 'Hallo %s, Sie haben sich schon registriert.';
$lang['de_DE']['Page']['API_CREATE'] = 'kann über die API Objekte erstellen';
$lang['de_DE']['Page']['API_DELETE'] = 'kann über die API Objekte löschen';
$lang['de_DE']['Page']['API_EDIT'] = 'kann über die API Objete verändern';
$lang['de_DE']['Page']['API_VIEW'] = 'kann Objekte über die API lesen';
$lang['de_DE']['Page']['APRIL'] = 'April';
$lang['de_DE']['Page']['ARTICLENAME'] = 'Artikelbezeichnung';
$lang['de_DE']['Page']['AUGUST'] = 'August';
$lang['de_DE']['Page']['BILLING_ADDRESS'] = 'Rechnungsadresse';
$lang['de_DE']['Page']['BIRTHDAY'] = 'Geburtstag';
$lang['de_DE']['Page']['CANCEL'] = 'abbrechen';
$lang['de_DE']['Page']['CART'] = 'Warenkorb';
$lang['de_DE']['Page']['CATALOG'] = 'Katalog';
$lang['de_DE']['Page']['CHANGE_PAYMENTMETHOD_CALL'] = 'Bitte wählen Sie eine andere Bezahlart oder kontaktieren sie den Shopbetreiber.';
$lang['de_DE']['Page']['CHANGE_PAYMENTMETHOD_LINK'] = 'andere Zahlart wählen';
$lang['de_DE']['Page']['CHECKOUT'] = 'zur Kasse';
$lang['de_DE']['Page']['CHECK_FIELDS_CALL'] = 'Bitte überprüfen Sie Ihre Eingaben in den folgenden Feldern:';
$lang['de_DE']['Page']['CONTACT_FORM'] = 'Kontaktformular';
$lang['de_DE']['Page']['CREDENTIALS_WRONG'] = 'Ihre Zugangsdaten sind falsch.';
$lang['de_DE']['Page']['DAY'] = 'Tag';
$lang['de_DE']['Page']['DECEMBER'] = 'Dezember';
$lang['de_DE']['Page']['DETAILS'] = 'Details';
$lang['de_DE']['Page']['DIDNOT_RETURN_RESULTS'] = 'hat in unserem Shop keine Ergebnisse geliefert.';
$lang['de_DE']['Page']['EMAIL_ADDRESS'] = 'Email-Adresse';
$lang['de_DE']['Page']['EMAIL_ALREADY_REGISTERED'] = 'Ein Nutzer hat sich bereits mit dieser Email registriert.';
$lang['de_DE']['Page']['EMPTY_CART'] = 'leeren';
$lang['de_DE']['Page']['ERROR_LISTING'] = 'Folgende Fehler sind aufgetreten:';
$lang['de_DE']['Page']['ERROR_OCCURED'] = 'Es ist ein Fehler aufgetreten.';
$lang['de_DE']['Page']['FEBRUARY'] = 'Februar';
$lang['de_DE']['Page']['FIND'] = 'finden:';
$lang['de_DE']['Page']['GOTO'] = 'gehe zur %s Seite';
$lang['de_DE']['Page']['GOTO_CART'] = 'zum Warenkorb';
$lang['de_DE']['Page']['GOTO_CONTACT_LINK'] = 'Zur Kontakt Seite';
$lang['de_DE']['Page']['HEADERPICTURE'] = 'Header Bild';
$lang['de_DE']['Page']['INCLUDED_VAT'] = 'enthaltene MwSt.';
$lang['de_DE']['Page']['I_ACCEPT'] = 'Ich akzeptiere die';
$lang['de_DE']['Page']['I_HAVE_READ'] = 'Ich habe die ';
$lang['de_DE']['Page']['JANUARY'] = 'Januar';
$lang['de_DE']['Page']['JUNE'] = 'Juni';
$lang['de_DE']['Page']['JULY'] = 'Juli';
$lang['de_DE']['Page']['MARCH'] = 'März';
$lang['de_DE']['Page']['MAY'] = 'Mai';
$lang['de_DE']['Page']['MESSAGE'] = 'Nachricht';
$lang['de_DE']['Page']['MONTH'] = 'Monat';
$lang['de_DE']['Page']['MYACCOUNT'] = 'mein Konto';
$lang['de_DE']['Page']['NAME'] = 'Name';
$lang['de_DE']['Page']['NEWSLETTER'] = 'Newsletter';
$lang['de_DE']['Page']['NEXT'] = 'Vor';
$lang['de_DE']['Page']['NOVEMBER'] = 'November';
$lang['de_DE']['Page']['NO_ORDERS'] = 'Sie haben noch keine Bestellungen abgeschlossen.';
$lang['de_DE']['Page']['NO_RESULTS'] = 'Entschuldigung aber zu Ihrem Suchbegriff gibt es kein Ergebnisse.';
$lang['de_DE']['Page']['OCTOBER'] = 'Oktober';
$lang['de_DE']['Page']['ORDERD_ARTICLES'] = 'Bestellte Artikel';
$lang['de_DE']['Page']['ORDER_COMPLETED'] = 'Ihre Bestellung ist abgeschlossen.';
$lang['de_DE']['Page']['ORDER_DATE'] = 'Bestelldatum';
$lang['de_DE']['Page']['ORDER_THANKS'] = 'Vielen Dank für Ihre Bestellung';
$lang['de_DE']['Page']['PASSWORD'] = 'Passwort';
$lang['de_DE']['Page']['PASSWORD_CASE_EMPTY'] = 'Wenn Sie dieses Feld leer lassen, wird Ihr Passwort nicht geändert.';
$lang['de_DE']['Page']['PASSWORD_CHECK'] = 'Passwortkontrolle';
$lang['de_DE']['Page']['PAYMENT_NOT_WORKING'] = 'Das gewählte Zahlungsmodul funktioniert nicht.';
$lang['de_DE']['Page']['PLUS_SHIPPING'] = 'zzgl. Versand';
$lang['de_DE']['Page']['PREV'] = 'Zurück';
$lang['de_DE']['Page']['REMARKS'] = 'Bemerkungen';
$lang['de_DE']['Page']['REMOVE_FROM_CART'] = 'entfernen';
$lang['de_DE']['Page']['REVOCATION'] = 'Wiederrufsbelehrung';
$lang['de_DE']['Page']['SAVE'] = 'speichern';
$lang['de_DE']['Page']['SEPTEMBER'] = 'September';
$lang['de_DE']['Page']['SESSION_EXPIRED'] = 'Ihre Sitzung ist abgelaufen.';
$lang['de_DE']['Page']['SHIPPING_ADDRESS'] = 'Versandadresse';
$lang['de_DE']['Page']['SHIPPING_AND_BILLING'] = 'Versand- und Rechnungsadresse';
$lang['de_DE']['Page']['SHOP_WITHOUT_REGISTRATION'] = 'Shop ohne Registrierung';
$lang['de_DE']['Page']['SHOWINPAGE'] = 'Sprache auf %s stellen';
$lang['de_DE']['Page']['SITMAP_HERE'] = 'Hier können Sie eine Übersicht über unsere Seite sehen.';
$lang['de_DE']['Page']['STEPS'] = 'Schritte';
$lang['de_DE']['Page']['SUBMIT_MESSAGE'] = 'Nachricht absenden';
$lang['de_DE']['Page']['SUBTOTAL'] = 'Zwischensumme';
$lang['de_DE']['Page']['SUM'] = 'Summe';
$lang['de_DE']['Page']['TAX'] = 'inkl. %s%% MwSt.';
$lang['de_DE']['Page']['TERMSOFSERVICE_PRIVACY'] = 'Allgemeine Geschäftsbedingungen und Datenschutz';
$lang['de_DE']['Page']['THE_QUERY'] = 'Der Begriff';
$lang['de_DE']['Page']['TITLE_IMPRINT'] = 'Impressum';
$lang['de_DE']['Page']['TITLE_TERMS'] = 'Allgemeine Geschäftsbedingungen';
$lang['de_DE']['Page']['TOTAL'] = 'Gesamtsumme';
$lang['de_DE']['Page']['URL_SEGMENT_IMPRINT'] = 'impressum';
$lang['de_DE']['Page']['URL_SEGMENT_TERMS'] = 'allgemeine-geschaeftsbedingungen-kaeuferinformationen';
$lang['de_DE']['Page']['USER_NOT_EXISTING'] = 'Diesen Benutzer gibt es nicht.';
$lang['de_DE']['Page']['VIEW_ORDERS_TEXT'] = 'Überprüfen Sie den Status Ihrer Bestellung in der';
$lang['de_DE']['Page']['WELCOME_PAGE_TITLE'] = 'Willkommen';
$lang['de_DE']['Page']['WELCOME_PAGE_URL_SEGMENT'] = 'willkommen';
$lang['de_DE']['Page']['YEAR'] = 'Jahr';
$lang['de_DE']['PaymentMethod']['ATTRIBUTED_COUNTRIES'] = 'zugeordnete Länder';
$lang['de_DE']['PaymentMethod']['BASIC_SETTINGS'] = 'Grundeinstellungen';
$lang['de_DE']['PaymentMethod']['FROM_PURCHASE_VALUE'] = 'ab Warenwert';
$lang['de_DE']['PaymentMethod']['PLURALNAME'] = array(
    'Bezahlarten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['PaymentMethod']['SHIPPINGMETHOD'] = 'Versandart';
$lang['de_DE']['PaymentMethod']['SINGULARNAME'] = array(
    'Zahlart',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['PaymentMethod']['STANDARD_ORDER_STATUS'] = 'standart Bestellstatus für diese Zahlart';
$lang['de_DE']['PaymentMethod']['TILL_PURCHASE_VALUE'] = 'bis Warenwert';
$lang['de_DE']['PaymentMethod']['TITLE'] = 'Zahlart';
$lang['de_DE']['PaymentMethodTexts']['PLURALNAME'] = array(
    'Bezahlartübersetzungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['PaymentMethodTexts']['SINGULARNAME'] = array(
    'Bezahlartübersetzung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['PaymentNotification']['PLURALNAME'] = array(
    'Zahlungsbenachrichtigungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['PaymentNotification']['SINGULARNAME'] = array(
    'Zahlungsbenachrichtigung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['PaymentNotification']['TITLE'] = 'Zahlungsbenachrichtigung';
$lang['de_DE']['PaymentNotification']['URL_SEGMENT'] = 'zahlungsbenachrichtigung';
$lang['de_DE']['Price']['PLURALNAME'] = array(
    'Preise',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Price']['SINGULARNAME'] = array(
    'Preis',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['RegisterConfirmationPage']['ALREADY_REGISTERES_MESSAGE_TEXT'] = 'Nachricht: Benutzer bereits registriert';
$lang['de_DE']['RegisterConfirmationPage']['CONFIRMATIONMAIL_SUBJECT'] = 'Bestätigungsmail: Betreff';
$lang['de_DE']['RegisterConfirmationPage']['CONFIRMATIONMAIL_TEXT'] = 'Bestätigungsmail: Nachricht';
$lang['de_DE']['RegisterConfirmationPage']['CONFIRMATION_MAIL'] = 'Bestätigungsmail';
$lang['de_DE']['RegisterConfirmationPage']['FAILURE_MESSAGE_TEXT'] = 'Fehlermeldung';
$lang['de_DE']['RegisterConfirmationPage']['PLURALNAME'] = array(
    'Registrierungsbestätigungsseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['RegisterConfirmationPage']['SINGULARNAME'] = array(
    'Registrierungsbestätigungsseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['RegisterConfirmationPage']['SUCCESS_MESSAGE_TEXT'] = 'success message';
$lang['de_DE']['RegisterConfirmationPage']['TITLE'] = 'register confirmation page';
$lang['de_DE']['RegisterConfirmationPage']['URL_SEGMENT'] = 'register-confirmation';
$lang['de_DE']['RegisterWelcomePage']['PLURALNAME'] = array(
    'Registrierungsbegrüßungsseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['RegisterWelcomePage']['SINGULARNAME'] = array(
    'Registrierungsbegrüßungsseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['RegistrationPage']['ACTIVATION_MAIL'] = 'Aktivierungsmail';
$lang['de_DE']['RegistrationPage']['ACTIVATION_MAIL_SUBJECT'] = 'Betreff der Aktivierungsmail';
$lang['de_DE']['RegistrationPage']['ACTIVATION_MAIL_TEXT'] = 'Nachricht der Aktivierungsmail';
$lang['de_DE']['RegistrationPage']['CONFIRMATION_TEXT'] = '<h1>Registrierung abschließen</h1><p>Bitte klicken Sie auf den Aktivierungslink oder kopieren Sie den Link in den Browser.</p><p><a href="$ConfirmationLink">Registrierung bestätigen</a></p><p>Sollten Sie sich nicht registriert haben, ignorieren Sie diese Mail einfach.</p><p>Ihr Webshop Team</p>';
$lang['de_DE']['RegistrationPage']['CUSTOMER_SALUTATION'] = 'Sehr geehrter Kunde\,';
$lang['de_DE']['RegistrationPage']['PLEASE_COFIRM'] = 'Bitte bestätigen Sie Ihre Registrierung.';
$lang['de_DE']['RegistrationPage']['PLURALNAME'] = array(
    'Registrierungsseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['RegistrationPage']['SINGULARNAME'] = array(
    'Registrierungsseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['RegistrationPage']['SUCCESS_TEXT'] = '<h1>Registrierung erfolgreich abgeschlossen!</h1><p>Vielen Dank für Ihre Registrierung.</p><p>Viel Spass in unserem Shop!</p><p>Ihr Webshop Team</p>';
$lang['de_DE']['RegistrationPage']['THANKS'] = 'Vielen Dank für Ihre Registrierung.';
$lang['de_DE']['RegistrationPage']['TITLE'] = 'Registrierungsseite';
$lang['de_DE']['RegistrationPage']['URL_SEGMENT'] = 'registrieren';
$lang['de_DE']['RegistrationPage']['YOUR_REGISTRATION'] = 'Ihre Registrierung';
$lang['de_DE']['RegularCustomer']['PLURALNAME'] = array(
    'Endkunden',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['RegularCustomer']['REGULARCUSTOMER'] = 'Endkunde';
$lang['de_DE']['RegularCustomer']['SINGULARNAME'] = array(
    'Endkunde',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SearchResultsPage']['PLURALNAME'] = array(
    'Suchergebnisseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SearchResultsPage']['SINGULARNAME'] = array(
    'Suchergebnisseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SearchResultsPage']['TITLE'] = 'Suchergebnisse';
$lang['de_DE']['SearchResultsPage']['URL_SEGMENT'] = 'suchergebnisse';
$lang['de_DE']['ShippingAddress']['PLURALNAME'] = array(
    'Versandadressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShippingAddress']['SINGULARNAME'] = array(
    'Versandadresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShippingFee']['ATTRIBUTED_SHIPPINGMETHOD'] = 'zugeordnete Versandarten';
$lang['de_DE']['ShippingFee']['COSTS'] = 'Kosten';
$lang['de_DE']['ShippingFee']['EMPTYSTRING_CHOOSEZONE'] = '--Zone wählen--';
$lang['de_DE']['ShippingFee']['FOR_SHIPPINGMETHOD'] = array(
    'für Versandart',
    null,
    'Für Versandart'
);
$lang['de_DE']['ShippingFee']['MAXIMUM_WEIGHT'] = array(
    'Maximalgewicht (g)',
    null,
    'Maximalgewicht (g)'
);
$lang['de_DE']['ShippingFee']['PLURALNAME'] = array(
    'Versandgebühren',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShippingFee']['SINGULARNAME'] = array(
    'Versandgebühr',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShippingFee']['ZONE_WITH_DESCRIPTION'] = 'zone (only carrier\'s zones available)';
$lang['de_DE']['ShippingFeesPage']['PLURALNAME'] = array(
    'Versandgebührenseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShippingFeesPage']['SINGULARNAME'] = array(
    'Versandgebührenseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShippingFeesPage']['TITLE'] = 'Versandgebühren';
$lang['de_DE']['ShippingFeesPage']['URL_SEGMENT'] = 'versandgebuehren';
$lang['de_DE']['ShippingMethod']['FOR_PAYMENTMETHODS'] = 'für Bezahlart';
$lang['de_DE']['ShippingMethod']['FOR_ZONES'] = 'für Zonen';
$lang['de_DE']['ShippingMethod']['PLURALNAME'] = array(
    'Versandarten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShippingMethod']['SINGULARNAME'] = array(
    'Versandart',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShippingMethodTexts']['PLURALNAME'] = array(
    'Lieferartübersetzungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShippingMethodTexts']['SINGULARNAME'] = array(
    'Lieferartübersetzung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShopAdmin']['PAYMENT_ISACTIVE'] = 'activated';
$lang['de_DE']['ShopAdmin']['PAYMENT_MAXAMOUNTFORACTIVATION'] = 'Höchstbetrag für Modul';
$lang['de_DE']['ShopAdmin']['PAYMENT_MINAMOUNTFORACTIVATION'] = 'Mindestbetrag für Modul';
$lang['de_DE']['ShopConfigurationAdmin']['SILVERCART_CONFIG'] = 'Silvercart Konfiguration';
$lang['de_DE']['ShopEmail']['PLURALNAME'] = array(
    'Shop Emails',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShopEmail']['SINGULARNAME'] = array(
    'Shop Email',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShoppingCart']['PLURALNAME'] = array(
    'carts',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShoppingCart']['SINGULARNAME'] = array(
    'Warenkorb',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShoppingCartPosition']['PLURALNAME'] = array(
    'Warenkorbpositionen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShoppingCartPosition']['SINGULARNAME'] = array(
    'Warenkorbposition',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Tax']['LABEL'] = 'Bezeichnung';
$lang['de_DE']['Tax']['PLURALNAME'] = array(
    'Steuersätze',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Tax']['RATE_IN_PERCENT'] = 'Steuersatz in %%';
$lang['de_DE']['Tax']['SINGULARNAME'] = array(
    'Steuersatz',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['TermsAndConditionsPage']['PLURALNAME'] = array(
    'AGB Seiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['TermsAndConditionsPage']['SINGULARNAME'] = array(
    'AGB Seite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Zone']['ATTRIBUTED_COUNTRIES'] = 'zugeordnete Länder';
$lang['de_DE']['Zone']['ATTRIBUTED_SHIPPINGMETHODS'] = 'zugeordnete Versandart';
$lang['de_DE']['Zone']['COUNTRIES'] = 'Länder';
$lang['de_DE']['Zone']['FOR_COUNTRIES'] = 'für Länder';
$lang['de_DE']['Zone']['PLURALNAME'] = array(
    'Zonen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Zone']['SINGULARNAME'] = array(
    'Zone',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);