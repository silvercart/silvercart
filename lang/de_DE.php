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

$lang['de_DE']['SilvercartAddress']['ADDITION'] = 'Addresszusatz';
$lang['de_DE']['SilvercartAddress']['CITY'] = 'Ort';
$lang['de_DE']['SilvercartAddress']['EMAIL'] = 'Emailadresse';
$lang['de_DE']['SilvercartAddress']['FIRSTNAME'] = 'Vorname';
$lang['de_DE']['SilvercartAddress']['MISSIS'] = 'Frau';
$lang['de_DE']['SilvercartAddress']['MISTER'] = 'Herr';
$lang['de_DE']['SilvercartAddress']['PHONE'] = 'Telefonnummer';
$lang['de_DE']['SilvercartAddress']['PHONEAREACODE'] = 'Vorwahl';
$lang['de_DE']['SilvercartAddress']['PLURALNAME'] = array(
    'Adressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartAddress']['POSTCODE'] = 'PLZ';
$lang['de_DE']['SilvercartAddress']['SALUTATION'] = 'Anrede';
$lang['de_DE']['SilvercartAddress']['SINGULARNAME'] = array(
    'Adresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartAddress']['STREET'] = 'Straße';
$lang['de_DE']['SilvercartAddress']['STREETNUMBER'] = 'Hausnummer';
$lang['de_DE']['SilvercartAddress']['SURNAME'] = 'Nachname';
$lang['de_DE']['SilvercartAddressHolder']['EDIT'] = 'bearbeiten';
$lang['de_DE']['SilvercartAddressHolder']['INVOICEADDRESS'] = 'Rechnungsadresse';
$lang['de_DE']['SilvercartAddressHolder']['PLURALNAME'] = array(
    'Adressübersichtseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartAddressHolder']['SHIPPINGADDRESS'] = 'Versandadresse';
$lang['de_DE']['SilvercartAddressHolder']['SINGULARNAME'] = array(
    'Adressübersichtseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartAddressHolder']['TITLE'] = 'Adressübersicht';
$lang['de_DE']['SilvercartAddressHolder']['URL_SEGMENT'] = 'adressuebersicht';
$lang['de_DE']['SilvercartAddressPage']['PLURALNAME'] = array(
    'Adressseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartAddressPage']['SINGULARNAME'] = array(
    'Adressseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartAddressPage']['TITLE'] = 'Adressdetails';
$lang['de_DE']['SilvercartAddressPage']['URL_SEGMENT'] = 'adressdetails';
$lang['de_DE']['SilvercartAnonymousCustomer']['ANONYMOUSCUSTOMER'] = 'Anonymer Kunde';
$lang['de_DE']['SilvercartAnonymousCustomer']['PLURALNAME'] = array(
    'Anonyme Kunden',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartAnonymousCustomer']['SINGULARNAME'] = array(
    'Anonymer Kunde',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartProduct']['ADD_TO_CART'] = 'in den Warenkorb';
$lang['de_DE']['SilvercartProduct']['CHOOSE_MASTER'] = '-- Master wählen --';
$lang['de_DE']['SilvercartProduct']['DESCRIPTION'] = 'Artikelbeschreibung';
$lang['de_DE']['SilvercartProduct']['EAN'] = 'EAN';
$lang['de_DE']['SilvercartProduct']['FREE_OF_CHARGE'] = 'Versandkostenfrei';
$lang['de_DE']['SilvercartProduct']['IMAGE'] = 'Artikelbild';
$lang['de_DE']['SilvercartProduct']['MASTERPRODUCT'] = 'Basisartikel';
$lang['de_DE']['SilvercartProduct']['METADESCRIPTION'] = 'Meta Beschreibung für Suchmaschinen';
$lang['de_DE']['SilvercartProduct']['METAKEYWORDS'] = 'Meta Schlagworte für Suchmaschinen';
$lang['de_DE']['SilvercartProduct']['METATITLE'] = 'Meta Titel für Suchmaschinen';
$lang['de_DE']['SilvercartProduct']['MSRP'] = 'UVP';
$lang['de_DE']['SilvercartProduct']['PLURALNAME'] = array(
    'Artikel',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartProduct']['PRICE'] = 'Preis';
$lang['de_DE']['SilvercartProduct']['PRICE_SINGLE'] = 'Einzelpreis';
$lang['de_DE']['SilvercartProduct']['PRODUCTNUMBER'] = 'Artikelnummer';
$lang['de_DE']['SilvercartProduct']['PRODUCTNUMBER_MANUFACTURER'] = 'Artikelnummer (Hersteller)';
$lang['de_DE']['SilvercartProduct']['PURCHASEPRICE'] = 'Einkaufspreis';
$lang['de_DE']['SilvercartProduct']['QUANTITY'] = 'Anzahl';
$lang['de_DE']['SilvercartProduct']['QUANTITY_SHORT'] = 'Anz.';
$lang['de_DE']['SilvercartProduct']['SHORTDESCRIPTION'] = 'Listenbeschreibung';
$lang['de_DE']['SilvercartProduct']['SINGULARNAME'] = array(
    'Artikel',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartProduct']['TITLE'] = 'Artikel';
$lang['de_DE']['SilvercartProduct']['VAT'] = 'MwSt';
$lang['de_DE']['SilvercartProduct']['WEIGHT'] = 'Gewicht';
$lang['de_DE']['SilvercartProductGroupHolder']['PAGE_TITLE'] = 'Warengruppen';
$lang['de_DE']['SilvercartProductGroupHolder']['PLURALNAME'] = array(
    'Artikelgruppenübersichten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartProductGroupHolder']['SHOW_PRODUCTS_WITH_COUNT_PLURAL'] = '%s Artikel anzeigen';
$lang['de_DE']['SilvercartProductGroupHolder']['SHOW_PRODUCTS_WITH_COUNT_SINGULAR'] = '%s Artikel anzeigen';
$lang['de_DE']['SilvercartProductGroupHolder']['SINGULARNAME'] = array(
    'Artikelgruppenübersicht',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartProductGroupHolder']['URL_SEGMENT'] = 'warengruppen';
$lang['de_DE']['SilvercartProductGroupPage']['ATTRIBUTES'] = 'Attribut';
$lang['de_DE']['SilvercartProductGroupPage']['GROUP_PICTURE'] = 'Bild der Gruppe';
$lang['de_DE']['SilvercartProductGroupPage']['PLURALNAME'] = array(
    'Warengruppen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartProductGroupPage']['SINGULARNAME'] = array(
    'Warengruppe',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartProductImageGallery']['PLURALNAME'] = array(
    'Gallerien',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartProductImageGallery']['SINGULARNAME'] = array(
    'Gallerie',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartProductPage']['ADD_TO_CART'] = 'in den Warenkorb';
$lang['de_DE']['SilvercartProductPage']['PLURALNAME'] = array(
    'Artikeldetailseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartProductPage']['QUANTITY'] = 'Anzahl';
$lang['de_DE']['SilvercartProductPage']['SINGULARNAME'] = array(
    'Artikeldetailseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartProductPage']['URL_SEGMENT'] = 'artikeldetails';
$lang['de_DE']['SilvercartProductTexts']['PLURALNAME'] = array(
    'Artikelübersetzungstexte',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartProductTexts']['SINGULARNAME'] = array(
    'Artikelübersetzungstext',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartAttribute']['PLURALNAME'] = array(
    'Attribute',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartAttribute']['SINGULARNAME'] = array(
    'Attribut',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartBusinessCustomer']['BUSINESSCUSTOMER'] = 'Geschäftskunde';
$lang['de_DE']['SilvercartBusinessCustomer']['PLURALNAME'] = array(
    'Geschäftskunden',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartBusinessCustomer']['SINGULARNAME'] = array(
    'Geschäftskunde',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartCarrier']['ATTRIBUTED_SHIPPINGMETHODS'] = 'zugeordnete Versandart';
$lang['de_DE']['SilvercartCarrier']['FULL_NAME'] = 'voller Name';
$lang['de_DE']['SilvercartCarrier']['PLURALNAME'] = array(
    'Frachtführer',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartCarrier']['SINGULARNAME'] = array(
    'Frachtführer',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartCartPage']['CART_EMPTY'] = 'Der Warenkorb ist leer.';
$lang['de_DE']['SilvercartCartPage']['PLURALNAME'] = array(
    'Warenkorbseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartCartPage']['SINGULARNAME'] = array(
    'Warenkorbseiten',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartCartPage']['URL_SEGMENT'] = 'warenkorb';
$lang['de_DE']['SilvercartCheckoutFormStep']['CHOOSEN_PAYMENT'] = 'gewählte Bezahlart';
$lang['de_DE']['SilvercartCheckoutFormStep']['CHOOSEN_SHIPPING'] = 'gewählte Versandart';
$lang['de_DE']['SilvercartCheckoutFormStep']['I_ACCEPT_REVOCATION'] = 'Ich akzeptiere die Wiederufsbelehrung';
$lang['de_DE']['SilvercartCheckoutFormStep']['I_ACCEPT_TERMS'] = 'Ich akzeptiere die Allgemeinen Geschäftsbedingungen.';
$lang['de_DE']['SilvercartCheckoutFormStep']['I_SUBSCRIBE_NEWSLETTER'] = 'Ich möchte den Newsletter abonnieren.';
$lang['de_DE']['SilvercartCheckoutFormStep']['ORDER'] = 'Bestellung';
$lang['de_DE']['SilvercartCheckoutFormStep']['OVERVIEW'] = 'Übersicht';
$lang['de_DE']['SilvercartCheckoutFormStep1']['EMPTYSTRING_COUNTRY'] = '--Land--';
$lang['de_DE']['SilvercartCheckoutFormStep2']['EMPTYSTRING_PAYMENTMETHOD'] = '--Zahlart wählen--';
$lang['de_DE']['SilvercartCheckoutFormStep3']['EMPTYSTRING_SHIPPINGMETHOD'] = '--Versandart wählen--';
$lang['de_DE']['SilvercartCheckoutStep']['PLURALNAME'] = array(
    'Checkout Schritte',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartCheckoutStep']['SINGULARNAME'] = array(
    'Checkout Schritt',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartCheckoutStep']['URL_SEGMENT'] = 'checkout';
$lang['de_DE']['SilvercartContactFormPage']['PLURALNAME'] = array(
    'Kontaktformularseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartContactFormPage']['REQUEST'] = 'Anfrage über das Kontaktformular';
$lang['de_DE']['SilvercartContactFormPage']['SINGULARNAME'] = array(
    'Kontaktformularseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartContactFormPage']['TITLE'] = 'Kontakt';
$lang['de_DE']['SilvercartContactFormPage']['URL_SEGMENT'] = 'kontakt';
$lang['de_DE']['SilvercartContactFormResponsePage']['CONTACT_CONFIRMATION'] = 'Kontaktbestätigung';
$lang['de_DE']['SilvercartContactFormResponsePage']['CONTENT'] = 'Vielen Dank für Ihre Nachricht. Wir werden Ihnen in Kürze antworten.';
$lang['de_DE']['SilvercartContactFormResponsePage']['PLURALNAME'] = array(
    'Kontaktformularantwortseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartContactFormResponsePage']['SINGULARNAME'] = array(
    'Kontaktformularantwortseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartContactFormResponsePage']['URL_SEGMENT'] = 'kontaktbestaetigung';
$lang['de_DE']['SilvercartCountry']['ATTRIBUTED_PAYMENTMETHOD'] = 'zugeordnete Bezahlart';
$lang['de_DE']['SilvercartCountry']['ATTRIBUTED_ZONES'] = 'zugeordnete Zonen';
$lang['de_DE']['SilvercartCountry']['PLURALNAME'] = array(
    'Länder',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartCountry']['SINGULARNAME'] = array(
    'Land',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartCustomerAdmin']['customers'] = 'Kunden';
$lang['de_DE']['SilvercartCustomerCategory']['PLURALNAME'] = array(
    'Kundengruppen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartCustomerCategory']['SINGULARNAME'] = array(
    'Kundengruppe',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartCustomerRole']['SALUTATION'] = 'Anrede';
$lang['de_DE']['SilvercartCustomerRole']['SUBSCRIBEDTONEWSLETTER'] = 'Hat Newsletter aboniert';
$lang['de_DE']['SilvercartCustomerRole']['HASACCEPTEDTERMSANDCONDITIONS'] = 'Hat die AGB akzeptiert';
$lang['de_DE']['SilvercartCustomerRole']['HASACCEPTEDREVOCATIONINSTRUCTION'] = 'Hat die Widerrufsbelehrung akzeptiert';
$lang['de_DE']['SilvercartCustomerRole']['CONFIRMATIONDATE'] = 'Bestätigungsdatum';
$lang['de_DE']['SilvercartCustomerRole']['CONFIRMATIONHASH'] = 'Bestätigungscode';
$lang['de_DE']['SilvercartCustomerRole']['OPTINSTATUS'] = 'Opt-In Status';
$lang['de_DE']['SilvercartCustomerRole']['BIRTHDAY'] = 'Geburtstag';
$lang['de_DE']['SilvercartCustomerRole']['TYPE'] = 'Typ';
$lang['de_DE']['SilvercartDataPage']['PLURALNAME'] = array(
    'Datenseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartDataPage']['SINGULARNAME'] = array(
    'Datenseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartDataPage']['TITLE'] = 'meine Daten';
$lang['de_DE']['SilvercartDataPage']['URL_SEGMENT'] = 'meine-daten';
$lang['de_DE']['SilvercartDataPrivacyStatementPage']['PLURALNAME'] = array(
    'Datenschutzseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartDataPrivacyStatementPage']['SINGULARNAME'] = array(
    'Datenschutzseiten',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartDataPrivacyStatementPage']['TITLE'] = 'Datenschutzerklärung';
$lang['de_DE']['SilvercartDataPrivacyStatementPage']['URL_SEGMENT'] = 'datenschutzerklaerung';
$lang['de_DE']['SilvercartEditAddressForm']['EMPTYSTRING_PLEASECHOOSE'] = '--bitte wählen--';
$lang['de_DE']['SilvercartEmailTemplates']['PLURALNAME'] = array(
    'Emailvorlagen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartEmailTemplates']['SINGULARNAME'] = array(
    'Emailvorlage',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartFooterNavigationHolder']['PLURALNAME'] = array(
    'Footernavigationsübersichten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartFooterNavigationHolder']['SINGULARNAME'] = array(
    'Footernavigationsübersichten',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartFooterNavigationHolder']['URL_SEGMENT'] = 'footernavigation';
$lang['de_DE']['SilvercartFrontPage']['DEFAULT_CONTENT'] = '<h2>Willkommen im <strong>SilverCart</strong> Webshop!</h2>';
$lang['de_DE']['SilvercartFrontPage']['PLURALNAME'] = array(
    'Frontseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartFrontPage']['SINGULARNAME'] = array(
    'Frontseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartHandlingCost']['PLURALNAME'] = array(
    'Bearbeitungskosten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartHandlingCost']['SINGULARNAME'] = array(
    'Bearbeitungskosten',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartInvoiceAddress']['PLURALNAME'] = array(
    'Rechnungsadressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartInvoiceAddress']['SINGULARNAME'] = array(
    'Rechnungsadresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartManufacturer']['PLURALNAME'] = array(
    'Hersteller',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartManufacturer']['SINGULARNAME'] = array(
    'Hersteller',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartMetaNavigationHolder']['PLURALNAME'] = array(
    'Metanavigationsübersichten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartMetaNavigationHolder']['SINGULARNAME'] = array(
    'Metanavigationsübersicht',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartMetaNavigationHolder']['URL_SEGMENT'] = 'metanavigation';
$lang['de_DE']['SilvercartMyAccountHolder']['PLURALNAME'] = array(
    'Accountübersichten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartMyAccountHolder']['SINGULARNAME'] = array(
    'Accountübersicht',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartMyAccountHolder']['TITLE'] = 'mein Konto';
$lang['de_DE']['SilvercartMyAccountHolder']['URL_SEGMENT'] = 'mein-konto';
$lang['de_DE']['SilvercartOrder']['CONFIRMED'] = 'bestätigt?';
$lang['de_DE']['SilvercartOrder']['CUSTOMER'] = 'Kunde';
$lang['de_DE']['SilvercartOrder']['ORDER_ID'] = 'Bestellnummer';
$lang['de_DE']['SilvercartOrder']['ORDER_VALUE'] = 'Bestellwert';
$lang['de_DE']['SilvercartOrder']['PLURALNAME'] = array(
    'Bestellungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartOrder']['SHIPPINGRATE'] = 'Versandkosten';
$lang['de_DE']['SilvercartOrder']['SINGULARNAME'] = array(
    'Bestellung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartOrder']['STATUS'] = 'order status';
$lang['de_DE']['SilvercartOrderAddress']['PLURALNAME'] = array(
    'Bestelladressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartOrderAddress']['SINGULARNAME'] = array(
    'Bestelladresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartOrderConfirmationPage']['PLURALNAME'] = array(
    'Bestellbestätigungsseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartOrderConfirmationPage']['SINGULARNAME'] = array(
    'Bestellbestätigungsseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartOrderConfirmationPage']['URL_SEGMENT'] = 'bestellbestaetigung';
$lang['de_DE']['SilvercartOrderDetailPage']['PLURALNAME'] = array(
    'Bestelldetailsseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartOrderDetailPage']['SINGULARNAME'] = array(
    'Bestelldetailsseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartOrderDetailPage']['TITLE'] = 'Bestelldetails';
$lang['de_DE']['SilvercartOrderDetailPage']['URL_SEGMENT'] = 'bestelldetails';
$lang['de_DE']['SilvercartOrderHolder']['PLURALNAME'] = array(
    'Bestellübersichten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartOrderHolder']['SINGULARNAME'] = array(
    'Bestellübersicht',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartOrderHolder']['TITLE'] = 'meine Bestellungen';
$lang['de_DE']['SilvercartOrderHolder']['URL_SEGMENT'] = 'meine-bestellungen';
$lang['de_DE']['SilvercartOrderInvoiceAddress']['PLURALNAME'] = array(
    'Rechnungsadressen der Bestellungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartOrderInvoiceAddress']['SINGULARNAME'] = array(
    'Rechnungsadresse der Bestellung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartOrderPosition']['PLURALNAME'] = array(
    'Bestellpositionen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartOrderPosition']['SINGULARNAME'] = array(
    'Bestellposition',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartOrderShippingAddress']['PLURALNAME'] = array(
    'Versandadressen derBestellungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartOrderShippingAddress']['SINGULARNAME'] = array(
    'Versandadresse der Bestellung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartOrderStatus']['CODE'] = 'Code';
$lang['de_DE']['SilvercartOrderStatus']['PAYED'] = 'payed';
$lang['de_DE']['SilvercartOrderStatus']['PLURALNAME'] = array(
    'Bestellstati',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartOrderStatus']['SINGULARNAME'] = array(
    'Bestellstatus',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartOrderStatus']['WAITING_FOR_PAYMENT'] = array(
    'Auf Zahlungseingang wird gewartet',
    null,
    'Auf Zahlungseingang wird gewartet'
);
$lang['de_DE']['SilvercartOrderStatusTexts']['PLURALNAME'] = array(
    'Bestellstatustexte',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartOrderStatusTexts']['SINGULARNAME'] = array(
    'Bestellstatustext',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartPage']['ABOUT_US'] = 'über uns';
$lang['de_DE']['SilvercartPage']['ABOUT_US_URL_SEGMENT'] = 'ueber-uns';
$lang['de_DE']['SilvercartPage']['ACCESS_CREDENTIALS_CALL'] = 'Bitte geben Sie Ihre Zugangsdaten ein:';
$lang['de_DE']['SilvercartPage']['ADDRESS'] = 'Adresse';
$lang['de_DE']['SilvercartPage']['ADDRESSINFORMATION'] = 'Adressinformationen';
$lang['de_DE']['SilvercartPage']['ADDRESS_DATA'] = 'Adressdaten';
$lang['de_DE']['SilvercartPage']['ALREADY_REGISTERED'] = 'Hallo %s, Sie haben sich schon registriert.';
$lang['de_DE']['SilvercartPage']['API_CREATE'] = 'kann über die API Objekte erstellen';
$lang['de_DE']['SilvercartPage']['API_DELETE'] = 'kann über die API Objekte löschen';
$lang['de_DE']['SilvercartPage']['API_EDIT'] = 'kann über die API Objete verändern';
$lang['de_DE']['SilvercartPage']['API_VIEW'] = 'kann Objekte über die API lesen';
$lang['de_DE']['SilvercartPage']['APRIL'] = 'April';
$lang['de_DE']['SilvercartPage']['PRODUCTNAME'] = 'Artikelbezeichnung';
$lang['de_DE']['SilvercartPage']['AUGUST'] = 'August';
$lang['de_DE']['SilvercartPage']['BILLING_ADDRESS'] = 'Rechnungsadresse';
$lang['de_DE']['SilvercartPage']['BIRTHDAY'] = 'Geburtstag';
$lang['de_DE']['SilvercartPage']['CANCEL'] = 'abbrechen';
$lang['de_DE']['SilvercartPage']['CART'] = 'Warenkorb';
$lang['de_DE']['SilvercartPage']['CATALOG'] = 'Katalog';
$lang['de_DE']['SilvercartPage']['CHANGE_PAYMENTMETHOD_CALL'] = 'Bitte wählen Sie eine andere Bezahlart oder kontaktieren sie den Shopbetreiber.';
$lang['de_DE']['SilvercartPage']['CHANGE_PAYMENTMETHOD_LINK'] = 'andere Zahlart wählen';
$lang['de_DE']['SilvercartPage']['CHECKOUT'] = 'zur Kasse';
$lang['de_DE']['SilvercartPage']['CHECK_FIELDS_CALL'] = 'Bitte überprüfen Sie Ihre Eingaben in den folgenden Feldern:';
$lang['de_DE']['SilvercartPage']['CONTACT_FORM'] = 'Kontaktformular';
$lang['de_DE']['SilvercartPage']['CREDENTIALS_WRONG'] = 'Ihre Zugangsdaten sind falsch.';
$lang['de_DE']['SilvercartPage']['DAY'] = 'Tag';
$lang['de_DE']['SilvercartPage']['DECEMBER'] = 'Dezember';
$lang['de_DE']['SilvercartPage']['DETAILS'] = 'Details';
$lang['de_DE']['SilvercartPage']['DETAILS_FOR'] = 'Details zu %s';
$lang['de_DE']['SilvercartPage']['DIDNOT_RETURN_RESULTS'] = 'hat in unserem Shop keine Ergebnisse geliefert.';
$lang['de_DE']['SilvercartPage']['EMAIL_ADDRESS'] = 'Email-Adresse';
$lang['de_DE']['SilvercartPage']['EMAIL_ALREADY_REGISTERED'] = 'Ein Nutzer hat sich bereits mit dieser Email registriert.';
$lang['de_DE']['SilvercartPage']['EMPTY_CART'] = 'leeren';
$lang['de_DE']['SilvercartPage']['ERROR_LISTING'] = 'Folgende Fehler sind aufgetreten:';
$lang['de_DE']['SilvercartPage']['ERROR_OCCURED'] = 'Es ist ein Fehler aufgetreten.';
$lang['de_DE']['SilvercartPage']['FEBRUARY'] = 'Februar';
$lang['de_DE']['SilvercartPage']['FIND'] = 'finden:';
$lang['de_DE']['SilvercartPage']['GOTO'] = 'gehe zur %s Seite';
$lang['de_DE']['SilvercartPage']['GOTO_CART'] = 'zum Warenkorb';
$lang['de_DE']['SilvercartPage']['GOTO_CONTACT_LINK'] = 'Zur Kontakt Seite';
$lang['de_DE']['SilvercartPage']['HEADERPICTURE'] = 'Header Bild';
$lang['de_DE']['SilvercartPage']['INCLUDED_VAT'] = 'enthaltene MwSt.';
$lang['de_DE']['SilvercartPage']['I_ACCEPT'] = 'Ich akzeptiere die';
$lang['de_DE']['SilvercartPage']['I_HAVE_READ'] = 'Ich habe die ';
$lang['de_DE']['SilvercartPage']['ISACTIVE'] = 'Aktiv';
$lang['de_DE']['SilvercartPage']['JANUARY'] = 'Januar';
$lang['de_DE']['SilvercartPage']['JUNE'] = 'Juni';
$lang['de_DE']['SilvercartPage']['JULY'] = 'Juli';
$lang['de_DE']['SilvercartPage']['LOGO'] = 'Logo';
$lang['de_DE']['SilvercartPage']['MARCH'] = 'März';
$lang['de_DE']['SilvercartPage']['MAY'] = 'Mai';
$lang['de_DE']['SilvercartPage']['MESSAGE'] = 'Nachricht';
$lang['de_DE']['SilvercartPage']['MONTH'] = 'Monat';
$lang['de_DE']['SilvercartPage']['MYACCOUNT'] = 'mein Konto';
$lang['de_DE']['SilvercartPage']['NAME'] = 'Name';
$lang['de_DE']['SilvercartPage']['NEWSLETTER'] = 'Newsletter';
$lang['de_DE']['SilvercartPage']['NEXT'] = 'Vor';
$lang['de_DE']['SilvercartPage']['NOVEMBER'] = 'November';
$lang['de_DE']['SilvercartPage']['NO_ORDERS'] = 'Sie haben noch keine Bestellungen abgeschlossen.';
$lang['de_DE']['SilvercartPage']['NO_RESULTS'] = 'Entschuldigung aber zu Ihrem Suchbegriff gibt es kein Ergebnisse.';
$lang['de_DE']['SilvercartPage']['OCTOBER'] = 'Oktober';
$lang['de_DE']['SilvercartPage']['ORDERD_PRODUCTS'] = 'Bestellte Artikel';
$lang['de_DE']['SilvercartPage']['ORDER_COMPLETED'] = 'Ihre Bestellung ist abgeschlossen.';
$lang['de_DE']['SilvercartPage']['ORDER_DATE'] = 'Bestelldatum';
$lang['de_DE']['SilvercartPage']['ORDER_THANKS'] = 'Vielen Dank für Ihre Bestellung';
$lang['de_DE']['SilvercartPage']['PASSWORD'] = 'Passwort';
$lang['de_DE']['SilvercartPage']['PASSWORD_CASE_EMPTY'] = 'Wenn Sie dieses Feld leer lassen, wird Ihr Passwort nicht geändert.';
$lang['de_DE']['SilvercartPage']['PASSWORD_CHECK'] = 'Passwortkontrolle';
$lang['de_DE']['SilvercartPage']['PAYMENT_NOT_WORKING'] = 'Das gewählte Zahlungsmodul funktioniert nicht.';
$lang['de_DE']['SilvercartPage']['PLUS_SHIPPING'] = 'zzgl. Versand';
$lang['de_DE']['SilvercartPage']['PREV'] = 'Zurück';
$lang['de_DE']['SilvercartPage']['REMARKS'] = 'Bemerkungen';
$lang['de_DE']['SilvercartPage']['REMOVE_FROM_CART'] = 'entfernen';
$lang['de_DE']['SilvercartPage']['REVOCATION'] = 'Wiederrufsbelehrung';
$lang['de_DE']['SilvercartPage']['SAVE'] = 'speichern';
$lang['de_DE']['SilvercartPage']['SEPTEMBER'] = 'September';
$lang['de_DE']['SilvercartPage']['SESSION_EXPIRED'] = 'Ihre Sitzung ist abgelaufen.';
$lang['de_DE']['SilvercartPage']['SHIPPING_ADDRESS'] = 'Versandadresse';
$lang['de_DE']['SilvercartPage']['SHIPPING_AND_BILLING'] = 'Versand- und Rechnungsadresse';
$lang['de_DE']['SilvercartPage']['SHOP_WITHOUT_REGISTRATION'] = 'Shop ohne Registrierung';
$lang['de_DE']['SilvercartPage']['SHOW_DETAILS'] = 'Details anzeigen';
$lang['de_DE']['SilvercartPage']['SHOW_DETAILS_FOR'] = 'Details zu %s anzeigen';
$lang['de_DE']['SilvercartPage']['SHOWINPAGE'] = 'Sprache auf %s stellen';
$lang['de_DE']['SilvercartPage']['SITMAP_HERE'] = 'Hier können Sie eine Übersicht über unsere Seite sehen.';
$lang['de_DE']['SilvercartPage']['STEPS'] = 'Schritte';
$lang['de_DE']['SilvercartPage']['SUBMIT_MESSAGE'] = 'Nachricht absenden';
$lang['de_DE']['SilvercartPage']['SUBTOTAL'] = 'Zwischensumme';
$lang['de_DE']['SilvercartPage']['SUM'] = 'Summe';
$lang['de_DE']['SilvercartPage']['TAX'] = 'inkl. %s%% MwSt.';
$lang['de_DE']['SilvercartPage']['TERMSOFSERVICE_PRIVACY'] = 'Allgemeine Geschäftsbedingungen und Datenschutz';
$lang['de_DE']['SilvercartPage']['THE_QUERY'] = 'Der Begriff';
$lang['de_DE']['SilvercartPage']['TITLE'] = 'Titel';
$lang['de_DE']['SilvercartPage']['TITLE_IMPRINT'] = 'Impressum';
$lang['de_DE']['SilvercartPage']['TITLE_TERMS'] = 'Allgemeine Geschäftsbedingungen';
$lang['de_DE']['SilvercartPage']['TOTAL'] = 'Gesamtsumme';
$lang['de_DE']['SilvercartPage']['URL_SEGMENT_IMPRINT'] = 'impressum';
$lang['de_DE']['SilvercartPage']['URL_SEGMENT_TERMS'] = 'allgemeine-geschaeftsbedingungen-kaeuferinformationen';
$lang['de_DE']['SilvercartPage']['USER_NOT_EXISTING'] = 'Diesen Benutzer gibt es nicht.';
$lang['de_DE']['SilvercartPage']['VIEW_ORDERS_TEXT'] = 'Überprüfen Sie den Status Ihrer Bestellung in der';
$lang['de_DE']['SilvercartPage']['WELCOME_PAGE_TITLE'] = 'Willkommen';
$lang['de_DE']['SilvercartPage']['WELCOME_PAGE_URL_SEGMENT'] = 'willkommen';
$lang['de_DE']['SilvercartPage']['YEAR'] = 'Jahr';
$lang['de_DE']['SilvercartPaymentMethod']['ATTRIBUTED_COUNTRIES'] = 'zugeordnete Länder';
$lang['de_DE']['SilvercartPaymentMethod']['BASIC_SETTINGS'] = 'Grundeinstellungen';
$lang['de_DE']['SilvercartPaymentMethod']['FROM_PURCHASE_VALUE'] = 'ab Warenwert';
$lang['de_DE']['SilvercartPaymentMethod']['PLURALNAME'] = array(
    'Bezahlarten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartPaymentMethod']['SHIPPINGMETHOD'] = 'Versandart';
$lang['de_DE']['SilvercartPaymentMethod']['SINGULARNAME'] = array(
    'Zahlart',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartPaymentMethod']['STANDARD_ORDER_STATUS'] = 'standart Bestellstatus für diese Zahlart';
$lang['de_DE']['SilvercartPaymentMethod']['TILL_PURCHASE_VALUE'] = 'bis Warenwert';
$lang['de_DE']['SilvercartPaymentMethod']['TITLE'] = 'Zahlart';
$lang['de_DE']['SilvercartPaymentMethodTexts']['PLURALNAME'] = array(
    'Bezahlartübersetzungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartPaymentMethodTexts']['SINGULARNAME'] = array(
    'Bezahlartübersetzung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartPaymentNotification']['PLURALNAME'] = array(
    'Zahlungsbenachrichtigungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartPaymentNotification']['SINGULARNAME'] = array(
    'Zahlungsbenachrichtigung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartPaymentNotification']['TITLE'] = 'Zahlungsbenachrichtigung';
$lang['de_DE']['SilvercartPaymentNotification']['URL_SEGMENT'] = 'zahlungsbenachrichtigung';
$lang['de_DE']['SilvercartPrice']['PLURALNAME'] = array(
    'Preise',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartPrice']['SINGULARNAME'] = array(
    'Preis',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartRegisterConfirmationPage']['ALREADY_REGISTERES_MESSAGE_TEXT'] = 'Nachricht: Benutzer bereits registriert';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONFIRMATIONMAIL_SUBJECT'] = 'Bestätigungsmail: Betreff';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONFIRMATIONMAIL_TEXT'] = 'Bestätigungsmail: Nachricht';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONFIRMATION_MAIL'] = 'Bestätigungsmail';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONTENT'] = '<p>Lieber Kunde,</p>';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONFIRMATIONFAILUREMESSAGE'] = '<p>Ihr Account konnte nicht aktiviert werden.</p>';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONFIRMATIONSUCCESSMESSAGE'] = '<p>Ihre Registrierung war erfolgreich! Um Ihnen Arbeit zu ersparen, haben wir Sie bereits automatisch eingeloggt.</p><p>Viel Spass beim Einkaufen in unserem Shop!</p>';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['ALREADYCONFIRMEDMESSAGE'] = '<p>Sie hatten Ihren Account bereits aktiviert.</p>';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['FAILURE_MESSAGE_TEXT'] = 'Fehlermeldung';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['PLURALNAME'] = array(
    'Registrierungsbestätigungsseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartRegisterConfirmationPage']['SINGULARNAME'] = array(
    'Registrierungsbestätigungsseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartRegisterConfirmationPage']['SUCCESS_MESSAGE_TEXT'] = 'Erfolgsmeldung';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['TITLE'] = 'Registrierungsbestätigungsseite';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['URL_SEGMENT'] = 'register-confirmation';
$lang['de_DE']['SilvercartRegisterWelcomePage']['CONTENT'] = '<p>Vielen Dank f&uuml;r Ihre Registrierung. Wir haben Ihnen eine Email mit Anweisungen geschickt, wie Ihr Benutzerkonto aktiviert wird.</p><p>Vielen Dank!</p>';
$lang['de_DE']['SilvercartRegisterWelcomePage']['PLURALNAME'] = array(
    'Registrierungsbegrüßungsseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartRegisterWelcomePage']['SINGULARNAME'] = array(
    'Registrierungsbegrüßungsseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartRegistrationPage']['ACTIVATION_MAIL'] = 'Aktivierungsmail';
$lang['de_DE']['SilvercartRegistrationPage']['ACTIVATION_MAIL_SUBJECT'] = 'Betreff der Aktivierungsmail';
$lang['de_DE']['SilvercartRegistrationPage']['ACTIVATION_MAIL_TEXT'] = 'Nachricht der Aktivierungsmail';
$lang['de_DE']['SilvercartRegistrationPage']['CONFIRMATION_TEXT'] = '<h1>Registrierung abschließen</h1><p>Bitte klicken Sie auf den Aktivierungslink oder kopieren Sie den Link in den Browser.</p><p><a href="$ConfirmationLink">Registrierung bestätigen</a></p><p>Sollten Sie sich nicht registriert haben, ignorieren Sie diese Mail einfach.</p><p>Ihr Webshop Team</p>';
$lang['de_DE']['SilvercartRegistrationPage']['CUSTOMER_SALUTATION'] = 'Sehr geehrter Kunde\,';
$lang['de_DE']['SilvercartRegistrationPage']['PLEASE_COFIRM'] = 'Bitte bestätigen Sie Ihre Registrierung.';
$lang['de_DE']['SilvercartRegistrationPage']['PLURALNAME'] = array(
    'Registrierungsseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartRegistrationPage']['SINGULARNAME'] = array(
    'Registrierungsseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartRegistrationPage']['SUCCESS_TEXT'] = '<h1>Registrierung erfolgreich abgeschlossen!</h1><p>Vielen Dank für Ihre Registrierung.</p><p>Viel Spass in unserem Shop!</p><p>Ihr Webshop Team</p>';
$lang['de_DE']['SilvercartRegistrationPage']['THANKS'] = 'Vielen Dank für Ihre Registrierung.';
$lang['de_DE']['SilvercartRegistrationPage']['TITLE'] = 'Registrierungsseite';
$lang['de_DE']['SilvercartRegistrationPage']['URL_SEGMENT'] = 'registrieren';
$lang['de_DE']['SilvercartRegistrationPage']['YOUR_REGISTRATION'] = 'Ihre Registrierung';
$lang['de_DE']['SilvercartRegularCustomer']['PLURALNAME'] = array(
    'Endkunden',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartRegularCustomer']['REGULARCUSTOMER'] = 'Endkunde';
$lang['de_DE']['SilvercartRegularCustomer']['REGULARCUSTOMER_OPTIN'] = 'Endkunde unbestätigt';
$lang['de_DE']['SilvercartRegularCustomer']['SINGULARNAME'] = array(
    'Endkunde',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartSearchResultsPage']['PLURALNAME'] = array(
    'Suchergebnisseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartSearchResultsPage']['SINGULARNAME'] = array(
    'Suchergebnisseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartSearchResultsPage']['TITLE'] = 'Suchergebnisse';
$lang['de_DE']['SilvercartSearchResultsPage']['URL_SEGMENT'] = 'suchergebnisse';
$lang['de_DE']['SilvercartShippingAddress']['PLURALNAME'] = array(
    'Versandadressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartShippingAddress']['SINGULARNAME'] = array(
    'Versandadresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartShippingFee']['ATTRIBUTED_SHIPPINGMETHOD'] = 'zugeordnete Versandarten';
$lang['de_DE']['SilvercartShippingFee']['COSTS'] = 'Kosten';
$lang['de_DE']['SilvercartShippingFee']['EMPTYSTRING_CHOOSEZONE'] = '--Zone wählen--';
$lang['de_DE']['SilvercartShippingFee']['FOR_SHIPPINGMETHOD'] = array(
    'für Versandart',
    null,
    'Für Versandart'
);
$lang['de_DE']['SilvercartShippingFee']['MAXIMUM_WEIGHT'] = array(
    'Maximalgewicht (g)',
    null,
    'Maximalgewicht (g)'
);
$lang['de_DE']['SilvercartShippingFee']['PLURALNAME'] = array(
    'Versandgebühren',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartShippingFee']['SINGULARNAME'] = array(
    'Versandgebühr',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartShippingFee']['ZONE_WITH_DESCRIPTION'] = 'Zone (nur Zonen des Frachtführers verfügbar)';
$lang['de_DE']['SilvercartShippingFeesPage']['PLURALNAME'] = array(
    'Versandgebührenseiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartShippingFeesPage']['SINGULARNAME'] = array(
    'Versandgebührenseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartShippingFeesPage']['TITLE'] = 'Versandgebühren';
$lang['de_DE']['SilvercartShippingFeesPage']['URL_SEGMENT'] = 'versandgebuehren';
$lang['de_DE']['SilvercartShippingMethod']['FOR_PAYMENTMETHODS'] = 'für Bezahlart';
$lang['de_DE']['SilvercartShippingMethod']['FOR_ZONES'] = 'für Zonen';
$lang['de_DE']['SilvercartShippingMethod']['PACKAGE'] = 'Paket';
$lang['de_DE']['SilvercartShippingMethod']['PLURALNAME'] = array(
    'Versandarten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartShippingMethod']['SINGULARNAME'] = array(
    'Versandart',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartShippingMethodTexts']['PLURALNAME'] = array(
    'Versandartübersetzungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartShippingMethodTexts']['SINGULARNAME'] = array(
    'Versandartübersetzung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartShopAdmin']['PAYMENT_ISACTIVE'] = 'aktiviert';
$lang['de_DE']['SilvercartShopAdmin']['PAYMENT_MAXAMOUNTFORACTIVATION'] = 'Höchstbetrag für Modul';
$lang['de_DE']['SilvercartShopAdmin']['PAYMENT_MINAMOUNTFORACTIVATION'] = 'Mindestbetrag für Modul';
$lang['de_DE']['SilvercartShopConfigurationAdmin']['SILVERCART_CONFIG'] = 'Silvercart Konfiguration';
$lang['de_DE']['SilvercartShopEmail']['EMAILTEXT'] = 'Nachricht';
$lang['de_DE']['SilvercartShopEmail']['IDENTIFIER'] = 'Bezeichner';
$lang['de_DE']['SilvercartShopEmail']['PLURALNAME'] = array(
    'Shop Emails',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartShopEmail']['SINGULARNAME'] = array(
    'Shop Email',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartShopEmail']['SUBJECT'] = 'Betreff';
$lang['de_DE']['SilvercartShopEmail']['VARIABLES'] = 'Variablen';
$lang['de_DE']['SilvercartShoppingCart']['PLURALNAME'] = array(
    'carts',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartShoppingCart']['SINGULARNAME'] = array(
    'Warenkorb',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartShoppingCartPosition']['PLURALNAME'] = array(
    'Warenkorbpositionen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartShoppingCartPosition']['SINGULARNAME'] = array(
    'Warenkorbposition',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartTax']['LABEL'] = 'Bezeichnung';
$lang['de_DE']['SilvercartTax']['PLURALNAME'] = array(
    'Steuersätze',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartTax']['RATE_IN_PERCENT'] = 'Steuersatz in %';
$lang['de_DE']['SilvercartTax']['SINGULARNAME'] = array(
    'Steuersatz',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartTermsAndConditionsPage']['PLURALNAME'] = array(
    'AGB Seiten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartTermsAndConditionsPage']['SINGULARNAME'] = array(
    'AGB Seite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartZone']['ATTRIBUTED_COUNTRIES'] = 'zugeordnete Länder';
$lang['de_DE']['SilvercartZone']['ATTRIBUTED_SHIPPINGMETHODS'] = 'zugeordnete Versandart';
$lang['de_DE']['SilvercartZone']['COUNTRIES'] = 'Länder';
$lang['de_DE']['SilvercartZone']['DOMESTIC'] = 'Inland';
$lang['de_DE']['SilvercartZone']['FOR_COUNTRIES'] = 'für Länder';
$lang['de_DE']['SilvercartZone']['PLURALNAME'] = array(
    'Zonen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartZone']['SINGULARNAME'] = array(
    'Zone',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);

/**
 * put translations for test content here
 */
$lang['de_DE']['TestGroup1']['TITLE'] = 'Testgruppe 1';
$lang['de_DE']['TestGroup1']['URL_SEGMENT'] = 'testgruppe1';
$lang['de_DE']['TestGroup1']['CONTENT'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p>';

$lang['de_DE']['TestGroup2']['TITLE'] = 'Testgruppe 2';
$lang['de_DE']['TestGroup2']['URL_SEGMENT'] = 'testgruppe2';
$lang['de_DE']['TestGroup2']['CONTENT'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p>';

$lang['de_DE']['TestGroup3']['TITLE'] = 'Testgruppe 3';
$lang['de_DE']['TestGroup3']['URL_SEGMENT'] = 'testgruppe3';
$lang['de_DE']['TestGroup3']['CONTENT'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p>';

$lang['de_DE']['TestGroup4']['TITLE'] = 'Testgruppe 4';
$lang['de_DE']['TestGroup4']['URL_SEGMENT'] = 'testgruppe4';
$lang['de_DE']['TestGroup4']['CONTENT'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p>';

$lang['de_DE']['TestManufacturer1']['TITLE'] = 'SilverCart';
$lang['de_DE']['TestManufacturer1']['URL'] = 'http://www.silvercart.org/';

$lang['de_DE']['TestManufacturer2']['TITLE'] = 'pixeltricks';
$lang['de_DE']['TestManufacturer2']['URL'] = 'http://www.silvercart.org/';

$lang['de_DE']['TestProduct1']['TITLE'] = 'Test Artikel 1';
$lang['de_DE']['TestProduct1']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct1']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct1']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct1']['METATITLE'] = 'Test Artikel 1';
$lang['de_DE']['TestProduct1']['METAKEYWORDS'] = 'Test Artikel 1';

$lang['de_DE']['TestProduct2']['TITLE'] = 'Test Artikel 2';
$lang['de_DE']['TestProduct2']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct2']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct2']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct2']['METATITLE'] = 'Test Artikel 2';
$lang['de_DE']['TestProduct2']['METAKEYWORDS'] = 'Test Artikel 2';

$lang['de_DE']['TestProduct3']['TITLE'] = 'Test Artikel 3';
$lang['de_DE']['TestProduct3']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct3']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct3']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct3']['METATITLE'] = 'Test Artikel 3';
$lang['de_DE']['TestProduct3']['METAKEYWORDS'] = 'Test Artikel 3';

$lang['de_DE']['TestProduct4']['TITLE'] = 'Test Artikel 4';
$lang['de_DE']['TestProduct4']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct4']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct4']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct4']['METATITLE'] = 'Test Artikel 4';
$lang['de_DE']['TestProduct4']['METAKEYWORDS'] = 'Test Artikel 4';

$lang['de_DE']['TestProduct5']['TITLE'] = 'Test Artikel 5';
$lang['de_DE']['TestProduct5']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct5']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct5']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct5']['METATITLE'] = 'Test Artikel 5';
$lang['de_DE']['TestProduct5']['METAKEYWORDS'] = 'Test Artikel 5';

$lang['de_DE']['TestProduct6']['TITLE'] = 'Test Artikel 6';
$lang['de_DE']['TestProduct6']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct6']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct6']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct6']['METATITLE'] = 'Test Artikel 6';
$lang['de_DE']['TestProduct6']['METAKEYWORDS'] = 'Test Artikel 6';

$lang['de_DE']['TestProduct7']['TITLE'] = 'Test Artikel 7';
$lang['de_DE']['TestProduct7']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct7']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct7']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct7']['METATITLE'] = 'Test Artikel 7';
$lang['de_DE']['TestProduct7']['METAKEYWORDS'] = 'Test Artikel 7';

$lang['de_DE']['TestProduct8']['TITLE'] = 'Test Artikel 8';
$lang['de_DE']['TestProduct8']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct8']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct8']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct8']['METATITLE'] = 'Test Artikel 8';
$lang['de_DE']['TestProduct8']['METAKEYWORDS'] = 'Test Artikel 8';

$lang['de_DE']['TestProduct9']['TITLE'] = 'Test Artikel 9';
$lang['de_DE']['TestProduct9']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct9']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct9']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct9']['METATITLE'] = 'Test Artikel 9';
$lang['de_DE']['TestProduct9']['METAKEYWORDS'] = 'Test Artikel 9';

$lang['de_DE']['TestProduct10']['TITLE'] = 'Test Artikel 10';
$lang['de_DE']['TestProduct10']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct10']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct10']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct10']['METATITLE'] = 'Test Artikel 10';
$lang['de_DE']['TestProduct10']['METAKEYWORDS'] = 'Test Artikel 10';

$lang['de_DE']['TestProduct11']['TITLE'] = 'Test Artikel 11';
$lang['de_DE']['TestProduct11']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct11']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct11']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct11']['METATITLE'] = 'Test Artikel 11';
$lang['de_DE']['TestProduct11']['METAKEYWORDS'] = 'Test Artikel 11';

$lang['de_DE']['TestProduct12']['TITLE'] = 'Test Artikel 12';
$lang['de_DE']['TestProduct12']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct12']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct12']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct12']['METATITLE'] = 'Test Artikel 12';
$lang['de_DE']['TestProduct12']['METAKEYWORDS'] = 'Test Artikel 12';

$lang['de_DE']['TestProduct13']['TITLE'] = 'Test Artikel 13';
$lang['de_DE']['TestProduct13']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct13']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct13']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct13']['METATITLE'] = 'Test Artikel 13';
$lang['de_DE']['TestProduct13']['METAKEYWORDS'] = 'Test Artikel 13';

$lang['de_DE']['TestProduct14']['TITLE'] = 'Test Artikel 14';
$lang['de_DE']['TestProduct14']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct14']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct14']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct14']['METATITLE'] = 'Test Artikel 14';
$lang['de_DE']['TestProduct14']['METAKEYWORDS'] = 'Test Artikel 14';

$lang['de_DE']['TestProduct15']['TITLE'] = 'Test Artikel 15';
$lang['de_DE']['TestProduct15']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct15']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct15']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct15']['METATITLE'] = 'Test Artikel 15';
$lang['de_DE']['TestProduct15']['METAKEYWORDS'] = 'Test Artikel 15';

$lang['de_DE']['TestProduct16']['TITLE'] = 'Test Artikel 16';
$lang['de_DE']['TestProduct16']['SHORTDESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct16']['LONGDESCRIPTION'] = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p><p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>';
$lang['de_DE']['TestProduct16']['METADESCRIPTION'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.';
$lang['de_DE']['TestProduct16']['METATITLE'] = 'Test Artikel 16';
$lang['de_DE']['TestProduct16']['METAKEYWORDS'] = 'Test Artikel 16';