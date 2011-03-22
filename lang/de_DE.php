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
 * German (Germany) language pack
 * 
 * @package Silvercart
 * @subpackage i18n
 * @ignore
 */
i18n::include_locale_file('silvercart', 'en_US');

global $lang;

if (array_key_exists('de_DE', $lang) && is_array($lang['de_DE'])) {
    $lang['de_DE'] = array_merge($lang['en_US'], $lang['de_DE']);
} else {
    $lang['de_DE'] = $lang['en_US'];
}

$lang['de_DE']['Silvercart']['DAY'] = 'Tag';
$lang['de_DE']['Silvercart']['DAYS'] = 'Tage';
$lang['de_DE']['Silvercart']['WEEK'] = 'Woche';
$lang['de_DE']['Silvercart']['WEEKS'] = 'Wochen';
$lang['de_DE']['Silvercart']['MONTH'] = 'Monat';
$lang['de_DE']['Silvercart']['MONTHS'] = 'Monate';

$lang['de_DE']['SilvercartAddress']['InvoiceAddressAsShippingAddress'] = 'Rechnungsadresse als Versandadresse nutzen';
$lang['de_DE']['SilvercartAddress']['ADDITION'] = 'Addresszusatz';
$lang['de_DE']['SilvercartAddress']['CITY'] = 'Ort';
$lang['de_DE']['SilvercartAddress']['EMAIL'] = 'E-Mail-Adresse';
$lang['de_DE']['SilvercartAddress']['EMAIL_CHECK'] = 'E-Mail-Adresse Gegenprüfung';
$lang['de_DE']['SilvercartAddress']['FIRSTNAME'] = 'Vorname';
$lang['de_DE']['SilvercartAddress']['MISSIS'] = 'Frau';
$lang['de_DE']['SilvercartAddress']['MISTER'] = 'Herr';
$lang['de_DE']['SilvercartAddress']['PHONE'] = 'Telefonnummer';
$lang['de_DE']['SilvercartAddress']['PHONEAREACODE'] = 'Vorwahl';
$lang['de_DE']['SilvercartAddress']['PLURALNAME'] = 'Adressen';
$lang['de_DE']['SilvercartAddress']['POSTCODE'] = 'PLZ';
$lang['de_DE']['SilvercartAddress']['SALUTATION'] = 'Anrede';
$lang['de_DE']['SilvercartAddress']['SINGULARNAME'] = 'Adresse';
$lang['de_DE']['SilvercartAddress']['STREET'] = 'Straße';
$lang['de_DE']['SilvercartAddress']['STREETNUMBER'] = 'Hausnummer';
$lang['de_DE']['SilvercartAddress']['SURNAME'] = 'Nachname';

$lang['de_DE']['SilvercartAddressHolder']['EDIT'] = 'Bearbeiten';
$lang['de_DE']['SilvercartAddressHolder']['EXCUSE_INVOICEADDRESS'] = 'Entschuldigen Sie, aber Sie haben noch keine Rechnungsadresse angelegt.';
$lang['de_DE']['SilvercartAddressHolder']['EXCUSE_SHIPPINGADDRESS'] = 'Entschuldigen Sie, aber Sie haben noch keine Lieferadresse angelegt.';
$lang['de_DE']['SilvercartAddressHolder']['INVOICEADDRESS'] = 'Rechnungsadresse';
$lang['de_DE']['SilvercartAddressHolder']['PLURALNAME'] = 'Adressübersichtseiten';
$lang['de_DE']['SilvercartAddressHolder']['SHIPPINGADDRESS'] = 'Versandadresse';
$lang['de_DE']['SilvercartAddressHolder']['SINGULARNAME'] = 'Adressübersichtseite';
$lang['de_DE']['SilvercartAddressHolder']['TITLE'] = 'Adressübersicht';
$lang['de_DE']['SilvercartAddressHolder']['URL_SEGMENT'] = 'adressuebersicht';

$lang['de_DE']['SilvercartAddressPage']['PLURALNAME'] = 'Adressseiten';
$lang['de_DE']['SilvercartAddressPage']['SINGULARNAME'] = 'Adressseite';
$lang['de_DE']['SilvercartAddressPage']['TITLE'] = 'Adressdetails';
$lang['de_DE']['SilvercartAddressPage']['URL_SEGMENT'] = 'adressdetails';

$lang['de_DE']['SilvercartAnonymousCustomer']['ANONYMOUSCUSTOMER'] = 'Anonymer Kunde';
$lang['de_DE']['SilvercartAnonymousCustomer']['PLURALNAME'] = 'Anonyme Kunden';
$lang['de_DE']['SilvercartAnonymousCustomer']['SINGULARNAME'] = 'Anonymer Kunde';

$lang['de_DE']['SilvercartAvailabilityStatus']['PLURALNAME'] = 'Verfügbarkeiten';
$lang['de_DE']['SilvercartAvailabilityStatus']['SINGULARNAME'] = 'Verfügbarkeit';
$lang['de_DE']['SilvercartAvailabilityStatus']['TITLE'] = 'Bezeichnung';
$lang['de_DE']['SilvercartAvailabilityStatus']['STATUS_AVAILABLE'] = 'verfügbar';
$lang['de_DE']['SilvercartAvailabilityStatus']['STATUS_NOT_AVAILABLE'] = 'nicht verfügbar';
$lang['de_DE']['SilvercartAvailabilityStatus']['STATUS_AVAILABLE_IN'] = 'verfügbar in %s %s';
$lang['de_DE']['SilvercartAvailabilityStatus']['STATUS_AVAILABLE_IN_MIN_MAX'] = 'verfügbar in %s bis %s %s';

$lang['de_DE']['SilvercartNewsletter']['SUBSCRIBED']                        = 'Sie haben den Newsletter abonniert';
$lang['de_DE']['SilvercartNewsletter']['UNSUBSCRIBED']                      = 'Sie haben den Newsletter nicht abonniert';
$lang['de_DE']['SilvercartNewsletterPage']['TITLE']                         = 'Newsletter';
$lang['de_DE']['SilvercartNewsletterPage']['URL_SEGMENT']                   = 'newsletter';
$lang['de_DE']['SilvercartNewsletterResponsePage']['TITLE']                 = 'Newsletter Status';
$lang['de_DE']['SilvercartNewsletterResponsePage']['URL_SEGMENT']           = 'newsletter_status';
$lang['de_DE']['SilvercartNewsletterResponsePage']['STATUS_TITLE']          = 'Ihre Newslettereinstellungen';
$lang['de_DE']['SilvercartNewsletterForm']['ACTIONFIELD_TITLE']             = 'Was wollen Sie tun?';
$lang['de_DE']['SilvercartNewsletterForm']['ACTIONFIELD_SUBSCRIBE']         = 'Ich möchte den Newsletter abonnieren';
$lang['de_DE']['SilvercartNewsletterForm']['ACTIONFIELD_UNSUBSCRIBE']       = 'Ich möchte den Newsletter abbestellen';
$lang['de_DE']['SilvercartNewsletterStatus']['ALREADY_SUBSCRIBED']          = 'Die Emailadresse "%s" ist schon für den Newsletterempfang registriert.';
$lang['de_DE']['SilvercartNewsletterStatus']['REGULAR_CUSTOMER_WITH_SAME_EMAIL_EXISTS'] = 'Es ist schon ein Kunde mit der Emailadresse "%s" registriert. Bitte loggen Sie sich zuerst ein und nehmen Sie dann die Einstellungen für den Newsletterempfang vor: <a href="%s">Zum Login</a>.';
$lang['de_DE']['SilvercartNewsletterStatus']['NO_EMAIL_FOUND']              = 'Die Emailadresse "%s" konnte nicht gefunden werden.';
$lang['de_DE']['SilvercartNewsletterStatus']['UNSUBSCRIBED_SUCCESSFULLY']   = 'Die Emailadresse "%s" wurde von der Liste der Newsletterempfänger entfernt.';
$lang['de_DE']['SilvercartNewsletterStatus']['SUBSCRIBED_SUCCESSFULLY']     = 'Die Emailadresse "%s" wurde zu der Liste der Newsletterempfänger hinzugefügt.';

$lang['de_DE']['SilvercartProduct']['IS_ACTIVE'] = 'ist aktiv';
$lang['de_DE']['SilvercartProduct']['ADD_TO_CART'] = 'in den Warenkorb';
$lang['de_DE']['SilvercartProduct']['CHOOSE_MASTER'] = '-- Master wählen --';
$lang['de_DE']['SilvercartProduct']['COLUMN_TITLE'] = 'Name';
$lang['de_DE']['SilvercartProduct']['DESCRIPTION'] = 'Artikelbeschreibung';
$lang['de_DE']['SilvercartProduct']['EAN'] = 'EAN';
$lang['de_DE']['SilvercartProduct']['FREE_OF_CHARGE'] = 'Versandkostenfrei';
$lang['de_DE']['SilvercartProduct']['IMAGE'] = 'Artikelbild';
$lang['de_DE']['SilvercartProduct']['MASTERPRODUCT'] = 'Basisartikel';
$lang['de_DE']['SilvercartProduct']['METADESCRIPTION'] = 'Meta Beschreibung für Suchmaschinen';
$lang['de_DE']['SilvercartProduct']['METAKEYWORDS'] = 'Meta Schlagworte für Suchmaschinen';
$lang['de_DE']['SilvercartProduct']['METATITLE'] = 'Meta Titel für Suchmaschinen';
$lang['de_DE']['SilvercartProduct']['MSRP'] = 'UVP';
$lang['de_DE']['SilvercartProduct']['PLURALNAME'] = 'Artikel';
$lang['de_DE']['SilvercartProduct']['PRICE'] = 'Preis';
$lang['de_DE']['SilvercartProduct']['PRICE_GROSS'] = 'Preis (Brutto)';
$lang['de_DE']['SilvercartProduct']['PRICE_NET'] = 'Preis (Netto)';
$lang['de_DE']['SilvercartProduct']['PRICE_SINGLE'] = 'Einzelpreis';
$lang['de_DE']['SilvercartProduct']['PRODUCTNUMBER'] = 'Artikelnummer';
$lang['de_DE']['SilvercartProduct']['PRODUCTNUMBER_MANUFACTURER'] = 'Artikelnummer (Hersteller)';
$lang['de_DE']['SilvercartProduct']['PURCHASEPRICE'] = 'Einkaufspreis';
$lang['de_DE']['SilvercartProduct']['PURCHASE_MIN_DURATION'] = 'Min. Bezugsdauer';
$lang['de_DE']['SilvercartProduct']['PURCHASE_MAX_DURATION'] = 'Max. Bezugsdauer';
$lang['de_DE']['SilvercartProduct']['PURCHASE_TIME_UNIT'] = 'Einheit (WBZ)';
$lang['de_DE']['SilvercartProduct']['QUANTITY'] = 'Anzahl';
$lang['de_DE']['SilvercartProduct']['QUANTITY_SHORT'] = 'Anz.';
$lang['de_DE']['SilvercartProduct']['SHORTDESCRIPTION'] = 'Listenbeschreibung';
$lang['de_DE']['SilvercartProduct']['SINGULARNAME'] = 'Artikel';
$lang['de_DE']['SilvercartProduct']['TITLE'] = 'Artikel';
$lang['de_DE']['SilvercartProduct']['VAT'] = 'MwSt';
$lang['de_DE']['SilvercartProduct']['WEIGHT'] = 'Gewicht';

$lang['de_DE']['SilvercartProductGroupHolder']['PAGE_TITLE'] = 'Warengruppen';
$lang['de_DE']['SilvercartProductGroupHolder']['PLURALNAME'] = 'Artikelgruppenübersichten';
$lang['de_DE']['SilvercartProductGroupHolder']['SHOW_PRODUCTS_WITH_COUNT_PLURAL'] = '%s Artikel anzeigen';
$lang['de_DE']['SilvercartProductGroupHolder']['SHOW_PRODUCTS_WITH_COUNT_SINGULAR'] = '%s Artikel anzeigen';
$lang['de_DE']['SilvercartProductGroupHolder']['SINGULARNAME'] = 'Artikelgruppenübersicht';
$lang['de_DE']['SilvercartProductGroupHolder']['SUBGROUPS_OF'] = 'Untergruppen von ';
$lang['de_DE']['SilvercartProductGroupHolder']['URL_SEGMENT'] = 'warengruppen';

$lang['de_DE']['SilvercartProductGroupPage']['ATTRIBUTES'] = 'Attribut';
$lang['de_DE']['SilvercartProductGroupPage']['GROUP_PICTURE'] = 'Bild der Gruppe';
$lang['de_DE']['SilvercartProductGroupPage']['MANUFACTURER_LINK'] = 'hersteller';
$lang['de_DE']['SilvercartProductGroupPage']['PLURALNAME'] = 'Warengruppen';
$lang['de_DE']['SilvercartProductGroupPage']['SINGULARNAME'] = 'Warengruppe';

$lang['de_DE']['SilvercartProductImageGallery']['PLURALNAME'] = 'Gallerien';
$lang['de_DE']['SilvercartProductImageGallery']['SINGULARNAME'] = 'Gallerie';

$lang['de_DE']['SilvercartProductPage']['ADD_TO_CART'] = 'in den Warenkorb';
$lang['de_DE']['SilvercartProductPage']['PLURALNAME'] = 'Artikeldetailseiten';
$lang['de_DE']['SilvercartProductPage']['QUANTITY'] = 'Anzahl';
$lang['de_DE']['SilvercartProductPage']['SINGULARNAME'] = 'Artikeldetailseite';
$lang['de_DE']['SilvercartProductPage']['URL_SEGMENT'] = 'artikeldetails';

$lang['de_DE']['SilvercartProductTexts']['PLURALNAME'] = 'Artikelübersetzungstexte';
$lang['de_DE']['SilvercartProductTexts']['SINGULARNAME'] = 'Artikelübersetzungstext';

$lang['de_DE']['SilvercartAttribute']['PLURALNAME'] = 'Attribute';
$lang['de_DE']['SilvercartAttribute']['SINGULARNAME'] = 'Attribut';

$lang['de_DE']['SilvercartBusinessCustomer']['BUSINESSCUSTOMER'] = 'Geschäftskunde';
$lang['de_DE']['SilvercartBusinessCustomer']['PLURALNAME'] = 'Geschäftskunden';
$lang['de_DE']['SilvercartBusinessCustomer']['SINGULARNAME'] = 'Geschäftskunde';

$lang['de_DE']['SilvercartCarrier']['ATTRIBUTED_SHIPPINGMETHODS'] = 'zugeordnete Versandart';
$lang['de_DE']['SilvercartCarrier']['FULL_NAME'] = 'voller Name';
$lang['de_DE']['SilvercartCarrier']['PLURALNAME'] = 'Frachtführer';
$lang['de_DE']['SilvercartCarrier']['SINGULARNAME'] = 'Frachtführer';

$lang['de_DE']['SilvercartCartPage']['CART_EMPTY'] = 'Der Warenkorb ist leer.';
$lang['de_DE']['SilvercartCartPage']['PLURALNAME'] = 'Warenkorbseiten';
$lang['de_DE']['SilvercartCartPage']['SINGULARNAME'] = 'Warenkorbseiten';
$lang['de_DE']['SilvercartCartPage']['URL_SEGMENT'] = 'warenkorb';

$lang['de_DE']['SilvercartCheckoutFormStep']['CHOOSEN_PAYMENT'] = 'gewählte Bezahlart';
$lang['de_DE']['SilvercartCheckoutFormStep']['CHOOSEN_SHIPPING'] = 'gewählte Versandart';
$lang['de_DE']['SilvercartCheckoutFormStep']['FORWARD'] = 'Weiter';
$lang['de_DE']['SilvercartCheckoutFormStep']['I_ACCEPT_REVOCATION'] = 'Ich akzeptiere die Wiederufsbelehrung';
$lang['de_DE']['SilvercartCheckoutFormStep']['I_ACCEPT_TERMS'] = 'Ich akzeptiere die Allgemeinen Geschäftsbedingungen.';
$lang['de_DE']['SilvercartCheckoutFormStep']['I_SUBSCRIBE_NEWSLETTER'] = 'Ich möchte den Newsletter abonnieren.';
$lang['de_DE']['SilvercartCheckoutFormStep']['ORDER'] = 'Bestellung';
$lang['de_DE']['SilvercartCheckoutFormStep']['ORDER_NOW'] = 'Bestellen';
$lang['de_DE']['SilvercartCheckoutFormStep']['OVERVIEW'] = 'Übersicht';

$lang['de_DE']['SilvercartCheckoutFormStep1']['EMPTYSTRING_COUNTRY'] = '--Land--';
$lang['de_DE']['SilvercartCheckoutFormStep1']['TITLE'] = 'Adressen';
$lang['de_DE']['SilvercartCheckoutFormStep2']['EMPTYSTRING_PAYMENTMETHOD'] = '--Zahlart wählen--';
$lang['de_DE']['SilvercartCheckoutFormStep2']['TITLE'] = 'Zahlart';
$lang['de_DE']['SilvercartCheckoutFormStep3']['EMPTYSTRING_SHIPPINGMETHOD'] = '--Versandart wählen--';
$lang['de_DE']['SilvercartCheckoutFormStep3']['TITLE'] = 'Versandart';
$lang['de_DE']['SilvercartCheckoutFormStep4']['TITLE'] = 'Übersicht';

$lang['de_DE']['SilvercartCheckoutStep']['PLURALNAME'] = 'Checkout Schritte';
$lang['de_DE']['SilvercartCheckoutStep']['SINGULARNAME'] = 'Checkout Schritt';
$lang['de_DE']['SilvercartCheckoutStep']['URL_SEGMENT'] = 'checkout';

$lang['de_DE']['SilvercartConfig']['ALLOW_CART_WEIGHT_TO_BE_ZERO'] = 'Gewicht des Warenkorbs darf null sein.';
$lang['de_DE']['SilvercartConfig']['DEFAULTCURRENCY'] = 'Standard Währung';
$lang['de_DE']['SilvercartConfig']['EMAILSENDER'] = 'E-Mail Absender';
$lang['de_DE']['SilvercartConfig']['PRICETYPEANONYMOUSCUSTOMERS'] = 'Preistyp für anonyme Kunden';
$lang['de_DE']['SilvercartConfig']['PRICETYPEREGULARCUSTOMERS'] = 'Preistyp für Endkunden';
$lang['de_DE']['SilvercartConfig']['PRICETYPEBUSINESSCUSTOMERS'] = 'Preistyp für Geschäftskunden';
$lang['de_DE']['SilvercartConfig']['PRICETYPEADMINS'] = 'Preistyp für Administratoren';
$lang['de_DE']['SilvercartConfig']['EMAILSENDER_INFO'] = 'Der E-Mail Absender wird als Absenderadresse aller E-Mails verwendet, die von SilverCart gesendet werden.';
$lang['de_DE']['SilvercartConfig']['ERROR_TITLE'] = 'Es ist ein Fehler aufgetreten!';
$lang['de_DE']['SilvercartConfig']['ERROR_MESSAGE'] = 'Der Parameter "%s" wurde nicht konfiguriert.<br/>Bitte <a href="/admin/silvercart-configuration/">loggen Sie sich ein</a> und konfigurieren Sie den fehlenden Parameter unter "SilverCart Konfiguration -> Allgemeine Konfiguration".';
$lang['de_DE']['SilvercartConfig']['GLOBALEMAILRECIPIENT'] = 'Globaler E-Mail Empfänger';
$lang['de_DE']['SilvercartConfig']['GLOBALEMAILRECIPIENT_INFO'] = 'Der globale E-Mail Empfänger kann optional gesetzt werden. An diese E-Mail-Adresse werden ALLE E-Mails (Bestellbestätigungen, Kontaktanfragen, etc.) gesendet. Die bei den E-Mail-Templates gesetzten Empfängeradressen bleiben davon unberührt. Diese werden nicht ersetzt, sondern nur ergänzt.';
$lang['de_DE']['SilvercartConfig']['PLURALNAME'] = 'Allgemeine Konfigurationen';
$lang['de_DE']['SilvercartConfig']['PRICETYPE_ANONYMOUS'] = 'Preistyp für anonyme Kunden';
$lang['de_DE']['SilvercartConfig']['PRICETYPE_REGULAR'] = 'Preistyp für Endkunden';
$lang['de_DE']['SilvercartConfig']['PRICETYPE_BUSINESS'] = 'Preistyp für Geschäftskunden';
$lang['de_DE']['SilvercartConfig']['PRICETYPE_ADMINS'] = 'Preistyp für Administratoren';
$lang['de_DE']['SilvercartConfig']['SINGULARNAME'] = 'Allgemeine Konfiguration';

$lang['de_DE']['SilvercartContactFormPage']['PLURALNAME'] = 'Kontaktformularseiten';
$lang['de_DE']['SilvercartContactFormPage']['REQUEST'] = 'Anfrage über das Kontaktformular';
$lang['de_DE']['SilvercartContactFormPage']['SINGULARNAME'] = 'Kontaktformularseite';
$lang['de_DE']['SilvercartContactFormPage']['TITLE'] = 'Kontakt';
$lang['de_DE']['SilvercartContactFormPage']['URL_SEGMENT'] = 'kontakt';

$lang['de_DE']['SilvercartContactFormResponsePage']['CONTACT_CONFIRMATION'] = 'Kontaktbestätigung';
$lang['de_DE']['SilvercartContactFormResponsePage']['CONTENT'] = 'Vielen Dank für Ihre Nachricht. Wir werden Ihnen in Kürze antworten.';
$lang['de_DE']['SilvercartContactFormResponsePage']['PLURALNAME'] = 'Kontaktformularantwortseiten';
$lang['de_DE']['SilvercartContactFormResponsePage']['SINGULARNAME'] = 'Kontaktformularantwortseite';
$lang['de_DE']['SilvercartContactFormResponsePage']['URL_SEGMENT'] = 'kontaktbestaetigung';

$lang['de_DE']['SilvercartCountry']['ATTRIBUTED_PAYMENTMETHOD'] = 'zugeordnete Bezahlart';
$lang['de_DE']['SilvercartCountry']['ATTRIBUTED_ZONES'] = 'zugeordnete Zonen';
$lang['de_DE']['SilvercartCountry']['PLURALNAME'] = 'Länder';
$lang['de_DE']['SilvercartCountry']['SINGULARNAME'] = 'Land';

$lang['de_DE']['SilvercartCustomerAdmin']['customers'] = 'Kunden';

$lang['de_DE']['SilvercartCustomerCategory']['EXISTING_CUSTOMER'] = 'Bestandskunde';
$lang['de_DE']['SilvercartCustomerCategory']['NEW_CUSTOMER'] = 'Neukunde';
$lang['de_DE']['SilvercartCustomerCategory']['PLURALNAME'] = 'Kundengruppen';
$lang['de_DE']['SilvercartCustomerCategory']['SINGULARNAME'] = 'Kundengruppe';

$lang['de_DE']['SilvercartCustomerRole']['GROSS'] = 'Brutto';
$lang['de_DE']['SilvercartCustomerRole']['NET'] = 'Netto';
$lang['de_DE']['SilvercartCustomerRole']['PRICING'] = 'Preisangabe';
$lang['de_DE']['SilvercartCustomerRole']['SALUTATION'] = 'Anrede';
$lang['de_DE']['SilvercartCustomerRole']['SUBSCRIBEDTONEWSLETTER'] = 'Hat Newsletter aboniert';
$lang['de_DE']['SilvercartCustomerRole']['HASACCEPTEDTERMSANDCONDITIONS'] = 'Hat die AGB akzeptiert';
$lang['de_DE']['SilvercartCustomerRole']['HASACCEPTEDREVOCATIONINSTRUCTION'] = 'Hat die Widerrufsbelehrung akzeptiert';
$lang['de_DE']['SilvercartCustomerRole']['CONFIRMATIONDATE'] = 'Bestätigungsdatum';
$lang['de_DE']['SilvercartCustomerRole']['CONFIRMATIONHASH'] = 'Bestätigungscode';
$lang['de_DE']['SilvercartCustomerRole']['OPTINSTATUS'] = 'Opt-In Status';
$lang['de_DE']['SilvercartCustomerRole']['BIRTHDAY'] = 'Geburtstag';
$lang['de_DE']['SilvercartCustomerRole']['TYPE'] = 'Typ';

$lang['de_DE']['SilvercartDataPage']['PLURALNAME'] = 'Datenseiten';
$lang['de_DE']['SilvercartDataPage']['SINGULARNAME'] = 'Datenseite';
$lang['de_DE']['SilvercartDataPage']['TITLE'] = 'Meine Daten';
$lang['de_DE']['SilvercartDataPage']['URL_SEGMENT'] = 'meine-daten';

$lang['de_DE']['SilvercartDataPrivacyStatementPage']['PLURALNAME'] = 'Datenschutzseiten';
$lang['de_DE']['SilvercartDataPrivacyStatementPage']['SINGULARNAME'] = 'Datenschutzseiten';
$lang['de_DE']['SilvercartDataPrivacyStatementPage']['TITLE'] = 'Datenschutzerklärung';
$lang['de_DE']['SilvercartDataPrivacyStatementPage']['URL_SEGMENT'] = 'datenschutzerklaerung';

$lang['de_DE']['SilvercartEditAddressForm']['EMPTYSTRING_PLEASECHOOSE'] = '--bitte wählen--';

$lang['de_DE']['SilvercartEmailTemplates']['PLURALNAME'] = 'E-Mail Vorlagen';
$lang['de_DE']['SilvercartEmailTemplates']['SINGULARNAME'] = 'E-Mail Vorlage';

$lang['de_DE']['SilvercartFile']['DESCRIPTION'] = 'Beschreibung';
$lang['de_DE']['SilvercartFile']['PLURALNAME'] = 'Dateien';
$lang['de_DE']['SilvercartFile']['SINGULARNAME'] = 'Datei';
$lang['de_DE']['SilvercartFile']['TITLE'] = 'Anzeigename';

$lang['de_DE']['SilvercartFooterNavigationHolder']['PLURALNAME'] = 'Footernavigationsübersichten';
$lang['de_DE']['SilvercartFooterNavigationHolder']['SINGULARNAME'] = 'Footernavigationsübersichten';
$lang['de_DE']['SilvercartFooterNavigationHolder']['URL_SEGMENT'] = 'footernavigation';

$lang['de_DE']['SilvercartFrontPage']['DEFAULT_CONTENT'] = '<h2>Willkommen im <strong>SilverCart</strong> Webshop!</h2>';
$lang['de_DE']['SilvercartFrontPage']['PLURALNAME'] = 'Frontseiten';
$lang['de_DE']['SilvercartFrontPage']['SINGULARNAME'] = 'Frontseite';

$lang['de_DE']['SilvercartHandlingCost']['PLURALNAME'] = 'Bearbeitungskosten';
$lang['de_DE']['SilvercartHandlingCost']['SINGULARNAME'] = 'Bearbeitungskosten';

$lang['de_DE']['SilvercartInvoiceAddress']['PLURALNAME'] = 'Rechnungsadressen';
$lang['de_DE']['SilvercartInvoiceAddress']['SINGULARNAME'] = 'Rechnungsadresse';

$lang['de_DE']['SilvercartManufacturer']['PLURALNAME'] = 'Hersteller';
$lang['de_DE']['SilvercartManufacturer']['SINGULARNAME'] = 'Hersteller';

$lang['de_DE']['SilvercartMetaNavigationHolder']['PLURALNAME'] = 'Metanavigationsübersichten';
$lang['de_DE']['SilvercartMetaNavigationHolder']['SINGULARNAME'] = 'Metanavigationsübersicht';
$lang['de_DE']['SilvercartMetaNavigationHolder']['URL_SEGMENT'] = 'metanavigation';

$lang['de_DE']['SilvercartMyAccountHolder']['PLURALNAME'] = 'Accountübersichten';
$lang['de_DE']['SilvercartMyAccountHolder']['SINGULARNAME'] = 'Accountübersicht';
$lang['de_DE']['SilvercartMyAccountHolder']['TITLE'] = 'Mein Konto';
$lang['de_DE']['SilvercartMyAccountHolder']['URL_SEGMENT'] = 'mein-konto';

$lang['de_DE']['SilvercartOrder']['AMOUNTGROSSTOTAL'] = 'Gesamtbetrag brutto';
$lang['de_DE']['SilvercartOrder']['AMOUNTTOTAL'] = 'Gesamtbetrag';
$lang['de_DE']['SilvercartOrder']['CARRIERANDSHIPPINGMETHODTITLE'] = 'Versandart';
$lang['de_DE']['SilvercartOrder']['CUSTOMER'] = 'Kunde';
$lang['de_DE']['SilvercartOrder']['CUSTOMERSEMAIL'] = 'Emailadresse des Kunden';
$lang['de_DE']['SilvercartOrder']['HANDLINGCOSTPAYMENT'] = 'Gebühren der Bezahlart';
$lang['de_DE']['SilvercartOrder']['HANDLINGCOSTSHIPMENT'] = 'Gebühren der Versandart';
$lang['de_DE']['SilvercartOrder']['NOTE'] = 'Bemerkung';
$lang['de_DE']['SilvercartOrder']['ORDER_ID'] = 'Bestellnummer';
$lang['de_DE']['SilvercartOrder']['ORDER_VALUE'] = 'Bestellwert';
$lang['de_DE']['SilvercartOrder']['PAYMENTMETHODTITLE'] = 'Bezahlart';
$lang['de_DE']['SilvercartOrder']['PLURALNAME'] = 'Bestellungen';
$lang['de_DE']['SilvercartOrder']['SHIPPINGRATE'] = 'Versandkosten';
$lang['de_DE']['SilvercartOrder']['SINGULARNAME'] = 'Bestellung';
$lang['de_DE']['SilvercartOrder']['STATUS'] = 'Bestellstatus';
$lang['de_DE']['SilvercartOrder']['TAXAMOUNTPAYMENT'] = 'Steuer der Bezahlart';
$lang['de_DE']['SilvercartOrder']['TAXAMOUNTSHIPMENT'] = 'Steuer der Versandart';
$lang['de_DE']['SilvercartOrder']['TAXRATEPAYMENT'] = 'Steuersatz der Bezahlart';
$lang['de_DE']['SilvercartOrder']['TAXRATESHIPMENT'] = 'Steuersatz der Versandart';
$lang['de_DE']['SilvercartOrder']['WEIGHTTOTAL'] = 'Gesamtgewicht';
$lang['de_DE']['SilvercartOrder']['YOUR_REMARK'] = 'Ihre Bemerkung';

$lang['de_DE']['SilvercartOrderAddress']['PLURALNAME'] = 'Bestelladressen';
$lang['de_DE']['SilvercartOrderAddress']['SINGULARNAME'] = 'Bestelladresse';

$lang['de_DE']['SilvercartOrderConfirmationPage']['PLURALNAME'] = 'Bestellbestätigungsseiten';
$lang['de_DE']['SilvercartOrderConfirmationPage']['SINGULARNAME'] = 'Bestellbestätigungsseite';
$lang['de_DE']['SilvercartOrderConfirmationPage']['URL_SEGMENT'] = 'bestellbestaetigung';

$lang['de_DE']['SilvercartOrderDetailPage']['PLURALNAME'] = 'Bestelldetailsseiten';
$lang['de_DE']['SilvercartOrderDetailPage']['SINGULARNAME'] = 'Bestelldetailsseite';
$lang['de_DE']['SilvercartOrderDetailPage']['TITLE'] = 'Bestelldetails';
$lang['de_DE']['SilvercartOrderDetailPage']['URL_SEGMENT'] = 'bestelldetails';

$lang['de_DE']['SilvercartOrderHolder']['PLURALNAME'] = 'Bestellübersichten';
$lang['de_DE']['SilvercartOrderHolder']['SINGULARNAME'] = 'Bestellübersicht';
$lang['de_DE']['SilvercartOrderHolder']['TITLE'] = 'Meine Bestellungen';
$lang['de_DE']['SilvercartOrderHolder']['URL_SEGMENT'] = 'meine-bestellungen';

$lang['de_DE']['SilvercartOrderInvoiceAddress']['PLURALNAME'] = 'Rechnungsadressen der Bestellungen';
$lang['de_DE']['SilvercartOrderInvoiceAddress']['SINGULARNAME'] = 'Rechnungsadresse der Bestellung';

$lang['de_DE']['SilvercartOrderPosition']['PLURALNAME'] = 'Bestellpositionen';
$lang['de_DE']['SilvercartOrderPosition']['SINGULARNAME'] = 'Bestellposition';

$lang['de_DE']['SilvercartOrderSearchForm']['PLEASECHOOSE'] = 'Bitte wählen';

$lang['de_DE']['SilvercartOrderShippingAddress']['PLURALNAME'] = 'Versandadressen der Bestellung';
$lang['de_DE']['SilvercartOrderShippingAddress']['SINGULARNAME'] = 'Versandadresse der Bestellung';

$lang['de_DE']['SilvercartOrderStatus']['CODE'] = 'Code';
$lang['de_DE']['SilvercartOrderStatus']['PAYED'] = 'payed';
$lang['de_DE']['SilvercartOrderStatus']['PLURALNAME'] = 'Bestellstati';
$lang['de_DE']['SilvercartOrderStatus']['SINGULARNAME'] = 'Bestellstatus';
$lang['de_DE']['SilvercartOrderStatus']['WAITING_FOR_PAYMENT'] = 'Auf Zahlungseingang wird gewartet';

$lang['de_DE']['SilvercartOrderStatusTexts']['PLURALNAME'] = 'Bestellstatustexte';
$lang['de_DE']['SilvercartOrderStatusTexts']['SINGULARNAME'] = 'Bestellstatustext';

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
$lang['de_DE']['SilvercartPage']['CANCEL'] = 'Abbrechen';
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
$lang['de_DE']['SilvercartPage']['EMAIL_ADDRESS'] = 'E-Mail-Adresse';
$lang['de_DE']['SilvercartPage']['EMAIL_ALREADY_REGISTERED'] = 'Ein Nutzer hat sich bereits mit dieser E-Mail-Adresse registriert.';
$lang['de_DE']['SilvercartPage']['EMPTY_CART'] = 'leeren';
$lang['de_DE']['SilvercartPage']['ERROR_LISTING'] = 'Folgende Fehler sind aufgetreten:';
$lang['de_DE']['SilvercartPage']['ERROR_OCCURED'] = 'Es ist ein Fehler aufgetreten.';
$lang['de_DE']['SilvercartPage']['FEBRUARY'] = 'Februar';
$lang['de_DE']['SilvercartPage']['FIND'] = 'Finden:';
$lang['de_DE']['SilvercartPage']['FORWARD'] = 'Weiter';
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
$lang['de_DE']['SilvercartPage']['LOGIN'] = 'Login';
$lang['de_DE']['SilvercartPage']['LOGO'] = 'Logo';
$lang['de_DE']['SilvercartPage']['MARCH'] = 'März';
$lang['de_DE']['SilvercartPage']['MAY'] = 'Mai';
$lang['de_DE']['SilvercartPage']['MESSAGE'] = 'Nachricht';
$lang['de_DE']['SilvercartPage']['MONTH'] = 'Monat';
$lang['de_DE']['SilvercartPage']['MYACCOUNT'] = 'Mein Konto';
$lang['de_DE']['SilvercartPage']['NAME'] = 'Name';
$lang['de_DE']['SilvercartPage']['NEWSLETTER'] = 'Newsletter';
$lang['de_DE']['SilvercartPage']['NEWSLETTER_FORM'] = 'Newsletter Einstellungen';
$lang['de_DE']['SilvercartPage']['NEXT'] = 'Vor';
$lang['de_DE']['SilvercartPage']['NOVEMBER'] = 'November';
$lang['de_DE']['SilvercartPage']['NO_ORDERS'] = 'Sie haben noch keine Bestellungen abgeschlossen.';
$lang['de_DE']['SilvercartPage']['NO_RESULTS'] = 'Entschuldigung aber zu Ihrem Suchbegriff gibt es kein Ergebnisse.';
$lang['de_DE']['SilvercartPage']['OCTOBER'] = 'Oktober';
$lang['de_DE']['SilvercartPage']['ORDERD_PRODUCTS'] = 'Bestellte Artikel';
$lang['de_DE']['SilvercartPage']['ORDER_COMPLETED'] = 'Ihre Bestellung ist abgeschlossen.';
$lang['de_DE']['SilvercartPage']['ORDER_DATE'] = 'Bestelldatum';
$lang['de_DE']['SilvercartPage']['ORDERS_EMAIL_INFORMATION_TEXT'] = 'Sie werden in Kürze eine Bestellbestätigung per E-Mail erhalten. Bitte prüfen Sie Ihren Posteingang.';
$lang['de_DE']['SilvercartPage']['ORDER_THANKS'] = 'Vielen Dank für Ihre Bestellung';
$lang['de_DE']['SilvercartPage']['PASSWORD'] = 'Passwort';
$lang['de_DE']['SilvercartPage']['PASSWORD_CASE_EMPTY'] = 'Wenn Sie dieses Feld leer lassen, wird Ihr Passwort nicht geändert.';
$lang['de_DE']['SilvercartPage']['PASSWORD_CHECK'] = 'Passwortkontrolle';
$lang['de_DE']['SilvercartPage']['PAYMENT_NOT_WORKING'] = 'Das gewählte Zahlungsmodul funktioniert nicht.';
$lang['de_DE']['SilvercartPage']['PLUS_SHIPPING'] = 'zzgl. Versand';
$lang['de_DE']['SilvercartPage']['PREV'] = 'Zurück';
$lang['de_DE']['SilvercartPage']['REGISTER'] = 'Registrieren';
$lang['de_DE']['SilvercartPage']['REMARKS'] = 'Bemerkungen';
$lang['de_DE']['SilvercartPage']['REMOVE_FROM_CART'] = 'entfernen';
$lang['de_DE']['SilvercartPage']['REVOCATION'] = 'Wiederrufsbelehrung';
$lang['de_DE']['SilvercartPage']['SAVE'] = 'speichern';
$lang['de_DE']['SilvercartPage']['SEARCH_RESULTS'] = 'Treffer';
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
$lang['de_DE']['SilvercartPage']['SUBMIT'] = 'Abschicken';
$lang['de_DE']['SilvercartPage']['SUBMIT_MESSAGE'] = 'Nachricht absenden';
$lang['de_DE']['SilvercartPage']['SUBTOTAL'] = 'Zwischensumme';
$lang['de_DE']['SilvercartPage']['SUM'] = 'Summe';
$lang['de_DE']['SilvercartPage']['INCLUDING_TAX'] = 'inkl. %s%% MwSt.';
$lang['de_DE']['SilvercartPage']['EXCLUDING_TAX'] = 'exkl. MwSt.';
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
$lang['de_DE']['SilvercartPaymentMethod']['MODE'] = 'Modus';
$lang['de_DE']['SilvercartPaymentMethod']['PLURALNAME'] = 'Bezahlarten';
$lang['de_DE']['SilvercartPaymentMethod']['SHIPPINGMETHOD'] = 'Versandart';
$lang['de_DE']['SilvercartPaymentMethod']['SINGULARNAME'] = 'Zahlart';
$lang['de_DE']['SilvercartPaymentMethod']['STANDARD_ORDER_STATUS'] = 'Standard Bestellstatus für diese Zahlart';
$lang['de_DE']['SilvercartPaymentMethod']['TILL_PURCHASE_VALUE'] = 'bis Warenwert';
$lang['de_DE']['SilvercartPaymentMethod']['TITLE'] = 'Zahlart';

$lang['de_DE']['SilvercartPaymentMethodTexts']['PLURALNAME'] = 'Bezahlartübersetzungen';
$lang['de_DE']['SilvercartPaymentMethodTexts']['SINGULARNAME'] = 'Bezahlartübersetzung';

$lang['de_DE']['SilvercartPaymentNotification']['PLURALNAME'] = 'Zahlungsbenachrichtigungen';
$lang['de_DE']['SilvercartPaymentNotification']['SINGULARNAME'] = 'Zahlungsbenachrichtigung';
$lang['de_DE']['SilvercartPaymentNotification']['TITLE'] = 'Zahlungsbenachrichtigung';
$lang['de_DE']['SilvercartPaymentNotification']['URL_SEGMENT'] = 'zahlungsbenachrichtigung';

$lang['de_DE']['SilvercartPrice']['PLURALNAME'] = 'Preise';
$lang['de_DE']['SilvercartPrice']['SINGULARNAME'] = 'Preis';

$lang['de_DE']['SilvercartRegisterConfirmationPage']['ALREADY_REGISTERES_MESSAGE_TEXT'] = 'Nachricht: Benutzer bereits registriert';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONFIRMATIONMAIL_SUBJECT'] = 'Bestätigungsmail: Betreff';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONFIRMATIONMAIL_TEXT'] = 'Bestätigungsmail: Nachricht';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONFIRMATION_MAIL'] = 'Bestätigungsmail';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONTENT'] = '<p>Lieber Kunde,</p>';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONFIRMATIONFAILUREMESSAGE'] = '<p>Ihr Account konnte nicht aktiviert werden.</p>';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONFIRMATIONSUCCESSMESSAGE'] = '<p>Ihre Registrierung war erfolgreich! Um Ihnen Arbeit zu ersparen, haben wir Sie bereits automatisch eingeloggt.</p><p>Viel Spass beim Einkaufen in unserem Shop!</p>';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['ALREADYCONFIRMEDMESSAGE'] = '<p>Sie hatten Ihren Account bereits aktiviert.</p>';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['FAILURE_MESSAGE_TEXT'] = 'Fehlermeldung';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['PLURALNAME'] = 'Registrierungsbestätigungsseiten';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['SINGULARNAME'] = 'Registrierungsbestätigungsseite';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['SUCCESS_MESSAGE_TEXT'] = 'Erfolgsmeldung';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['TITLE'] = 'Registrierungsbestätigungsseite';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['URL_SEGMENT'] = 'register-confirmation';

$lang['de_DE']['SilvercartRegisterWelcomePage']['CONTENT'] = '<p>Vielen Dank f&uuml;r Ihre Registrierung. Wir haben Ihnen eine E-Mail mit Anweisungen geschickt, wie Ihr Benutzerkonto aktiviert wird.</p><p>Vielen Dank!</p>';
$lang['de_DE']['SilvercartRegisterWelcomePage']['PLURALNAME'] = 'Registrierungsbegrüßungsseiten';
$lang['de_DE']['SilvercartRegisterWelcomePage']['SINGULARNAME'] = 'Registrierungsbegrüßungsseite';

$lang['de_DE']['SilvercartRegistrationPage']['ACTIVATION_MAIL'] = 'Aktivierungsmail';
$lang['de_DE']['SilvercartRegistrationPage']['ACTIVATION_MAIL_SUBJECT'] = 'Betreff der Aktivierungsmail';
$lang['de_DE']['SilvercartRegistrationPage']['ACTIVATION_MAIL_TEXT'] = 'Nachricht der Aktivierungsmail';
$lang['de_DE']['SilvercartRegistrationPage']['CONFIRMATION_TEXT'] = '<h1>Registrierung abschließen</h1><p>Bitte klicken Sie auf den Aktivierungslink oder kopieren Sie den Link in den Browser.</p><p><a href="$ConfirmationLink">Registrierung bestätigen</a></p><p>Sollten Sie sich nicht registriert haben, ignorieren Sie diese Mail einfach.</p><p>Ihr Webshop Team</p>';
$lang['de_DE']['SilvercartRegistrationPage']['CUSTOMER_SALUTATION'] = 'Sehr geehrter Kunde\,';
$lang['de_DE']['SilvercartRegistrationPage']['PLEASE_COFIRM'] = 'Bitte bestätigen Sie Ihre Registrierung.';
$lang['de_DE']['SilvercartRegistrationPage']['PLURALNAME'] = 'Registrierungsseiten';
$lang['de_DE']['SilvercartRegistrationPage']['SINGULARNAME'] = 'Registrierungsseite';
$lang['de_DE']['SilvercartRegistrationPage']['SUCCESS_TEXT'] = '<h1>Registrierung erfolgreich abgeschlossen!</h1><p>Vielen Dank für Ihre Registrierung.</p><p>Viel Spass in unserem Shop!</p><p>Ihr Webshop Team</p>';
$lang['de_DE']['SilvercartRegistrationPage']['THANKS'] = 'Vielen Dank für Ihre Registrierung.';
$lang['de_DE']['SilvercartRegistrationPage']['TITLE'] = 'Registrierungsseite';
$lang['de_DE']['SilvercartRegistrationPage']['URL_SEGMENT'] = 'registrieren';
$lang['de_DE']['SilvercartRegistrationPage']['YOUR_REGISTRATION'] = 'Ihre Registrierung';

$lang['de_DE']['SilvercartRegularCustomer']['PLURALNAME'] = 'Endkunden';
$lang['de_DE']['SilvercartRegularCustomer']['REGULARCUSTOMER'] = 'Endkunde';
$lang['de_DE']['SilvercartRegularCustomer']['REGULARCUSTOMER_OPTIN'] = 'Endkunde unbestätigt';
$lang['de_DE']['SilvercartRegularCustomer']['SINGULARNAME'] = 'Endkunde';

$lang['de_DE']['SilvercartSearchResultsPage']['PLURALNAME'] = 'Suchergebnisseiten';
$lang['de_DE']['SilvercartSearchResultsPage']['SINGULARNAME'] = 'Suchergebnisseite';
$lang['de_DE']['SilvercartSearchResultsPage']['TITLE'] = 'Suchergebnisse';
$lang['de_DE']['SilvercartSearchResultsPage']['URL_SEGMENT'] = 'suchergebnisse';

$lang['de_DE']['SilvercartShippingAddress']['PLURALNAME'] = 'Versandadressen';
$lang['de_DE']['SilvercartShippingAddress']['SINGULARNAME'] = 'Versandadresse';

$lang['de_DE']['SilvercartShippingFee']['ATTRIBUTED_SHIPPINGMETHOD'] = 'zugeordnete Versandarten';
$lang['de_DE']['SilvercartShippingFee']['COSTS'] = 'Kosten';
$lang['de_DE']['SilvercartShippingFee']['EMPTYSTRING_CHOOSEZONE'] = '--Zone wählen--';
$lang['de_DE']['SilvercartShippingFee']['FOR_SHIPPINGMETHOD'] = 'für Versandart';
$lang['de_DE']['SilvercartShippingFee']['MAXIMUM_WEIGHT'] = 'Maximalgewicht (g)';
$lang['de_DE']['SilvercartShippingFee']['PLURALNAME'] = 'Versandgebühren';
$lang['de_DE']['SilvercartShippingFee']['SINGULARNAME'] = 'Versandgebühr';
$lang['de_DE']['SilvercartShippingFee']['ZONE_WITH_DESCRIPTION'] = 'Zone (nur Zonen des Frachtführers verfügbar)';

$lang['de_DE']['SilvercartShippingFeesPage']['PLURALNAME'] = 'Versandgebührenseiten';
$lang['de_DE']['SilvercartShippingFeesPage']['SINGULARNAME'] = 'Versandgebührenseite';
$lang['de_DE']['SilvercartShippingFeesPage']['TITLE'] = 'Versandgebühren';
$lang['de_DE']['SilvercartShippingFeesPage']['URL_SEGMENT'] = 'versandgebuehren';

$lang['de_DE']['SilvercartShippingMethod']['FOR_PAYMENTMETHODS'] = 'für Bezahlart';
$lang['de_DE']['SilvercartShippingMethod']['FOR_ZONES'] = 'für Zonen';
$lang['de_DE']['SilvercartShippingMethod']['PACKAGE'] = 'Paket';
$lang['de_DE']['SilvercartShippingMethod']['PLURALNAME'] = 'Versandarten';
$lang['de_DE']['SilvercartShippingMethod']['SINGULARNAME'] = 'Versandart';

$lang['de_DE']['SilvercartShippingMethodTexts']['PLURALNAME'] = 'Versandartübersetzungen';
$lang['de_DE']['SilvercartShippingMethodTexts']['SINGULARNAME'] = 'Versandartübersetzung';

$lang['de_DE']['SilvercartShopAdmin']['PAYMENT_ISACTIVE'] = 'aktiviert';
$lang['de_DE']['SilvercartShopAdmin']['PAYMENT_MAXAMOUNTFORACTIVATION'] = 'Höchstbetrag für Modul';
$lang['de_DE']['SilvercartShopAdmin']['PAYMENT_MINAMOUNTFORACTIVATION'] = 'Mindestbetrag für Modul';

$lang['de_DE']['SilvercartShopConfigurationAdmin']['SILVERCART_CONFIG'] = 'Silvercart Konfiguration';

$lang['de_DE']['SilvercartShopEmail']['EMAILTEXT'] = 'Nachricht';
$lang['de_DE']['SilvercartShopEmail']['IDENTIFIER'] = 'Bezeichner';
$lang['de_DE']['SilvercartShopEmail']['PLURALNAME'] = 'Shop E-Mails';
$lang['de_DE']['SilvercartShopEmail']['SINGULARNAME'] = 'Shop E-Mail';
$lang['de_DE']['SilvercartShopEmail']['SUBJECT'] = 'Betreff';
$lang['de_DE']['SilvercartShopEmail']['VARIABLES'] = 'Variablen';

$lang['de_DE']['SilvercartShoppingCart']['PLURALNAME'] = 'Warenkörbe';
$lang['de_DE']['SilvercartShoppingCart']['SINGULARNAME'] = 'Warenkorb';

$lang['de_DE']['SilvercartShoppingCartPosition']['PLURALNAME'] = 'Warenkorbpositionen';
$lang['de_DE']['SilvercartShoppingCartPosition']['SINGULARNAME'] = 'Warenkorbposition';

$lang['de_DE']['SilvercartTax']['LABEL'] = 'Bezeichnung';
$lang['de_DE']['SilvercartTax']['PLURALNAME'] = 'Steuersätze';
$lang['de_DE']['SilvercartTax']['RATE_IN_PERCENT'] = 'Steuersatz in %';
$lang['de_DE']['SilvercartTax']['SINGULARNAME'] = 'Steuersatz';

$lang['de_DE']['SilvercartTermsAndConditionsPage']['PLURALNAME'] = 'AGB Seiten';
$lang['de_DE']['SilvercartTermsAndConditionsPage']['SINGULARNAME'] = 'AGB Seite';

$lang['de_DE']['SilvercartZone']['ATTRIBUTED_COUNTRIES'] = 'zugeordnete Länder';
$lang['de_DE']['SilvercartZone']['ATTRIBUTED_SHIPPINGMETHODS'] = 'zugeordnete Versandart';
$lang['de_DE']['SilvercartZone']['COUNTRIES'] = 'Länder';
$lang['de_DE']['SilvercartZone']['DOMESTIC'] = 'Inland';
$lang['de_DE']['SilvercartZone']['FOR_COUNTRIES'] = 'für Länder';
$lang['de_DE']['SilvercartZone']['PLURALNAME'] = 'Zonen';
$lang['de_DE']['SilvercartZone']['SINGULARNAME'] = 'Zone';