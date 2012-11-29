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
if (file_exists(Director::baseFolder() . '/dataobject_manager/lang/deDE3.php')) {
    require_once(Director::baseFolder() . '/dataobject_manager/lang/deDE3.php');
}

$lang['de_DE']['DataObjectManager']['DESELECTALL'] = 'Auswahl aufheben';
$lang['de_DE']['DataObjectManager']['ONLYRELATED'] = 'Nur verknüpfte Einträge anzeigen';

$lang['de_DE']['TableListField']['SELECT'] = 'Auswahl:';

$lang['de_DE']['Silvercart']['CHOOSE'] = 'wählen';
$lang['de_DE']['Silvercart']['CLEAR_CACHE'] = 'Cache leeren';
$lang['de_DE']['Silvercart']['CONTENT'] = 'Inhalt';
$lang['de_DE']['Silvercart']['CROSSSELLING'] = 'Cross-Selling';
$lang['de_DE']['Silvercart']['DATA'] = 'Daten';
$lang['de_DE']['Silvercart']['DATEFORMAT'] = 'd.m.Y';
$lang['de_DE']['Silvercart']['DEEPLINKS'] = 'Deeplinks';
$lang['de_DE']['Silvercart']['LINKS'] = 'Links';
$lang['de_DE']['Silvercart']['MISC_CONFIG'] = 'Sonstige Einstellungen';
$lang['de_DE']['Silvercart']['TIMES'] = 'Zeiten';
$lang['de_DE']['Silvercart']['DATE'] = 'Datum';
$lang['de_DE']['Silvercart']['DAY'] = 'Tag';
$lang['de_DE']['Silvercart']['DAYS'] = 'Tage';
$lang['de_DE']['Silvercart']['WEEK'] = 'Woche';
$lang['de_DE']['Silvercart']['WEEKS'] = 'Wochen';
$lang['de_DE']['Silvercart']['MONTH'] = 'Monat';
$lang['de_DE']['Silvercart']['MONTHS'] = 'Monate';
$lang['de_DE']['Silvercart']['MIN'] = 'Minute';
$lang['de_DE']['Silvercart']['MINS'] = 'Minuten';
$lang['de_DE']['Silvercart']['SEC'] = 'Sekunde';
$lang['de_DE']['Silvercart']['SECS'] = 'Sekunden';
$lang['de_DE']['Silvercart']['MORE'] = 'Mehr';
$lang['de_DE']['Silvercart']['SEO'] = 'SEO';
$lang['de_DE']['Silvercart']['YES'] = 'Ja';
$lang['de_DE']['Silvercart']['NO'] = 'Nein';
$lang['de_DE']['Silvercart']['PRINT'] = 'Drucken';
$lang['de_DE']['Silvercart']['LOADING_PRINT_VIEW'] = 'Druckansicht wird geladen';
$lang['de_DE']['Silvercart']['NOT_ALLOWED_TO_PRINT'] = 'Sie haben keine Berechtigung, um dieses Objekt zu drucken!';
$lang['de_DE']['Silvercart']['LANGUAGE'] = 'Sprache';
$lang['de_DE']['Silvercart']['TRANSLATION'] = 'Übersetzung';
$lang['de_DE']['Silvercart']['TRANSLATIONS'] = 'Übersetzungen';
$lang['de_DE']['Silvercart']['MARK_ALL'] = 'Alle markieren';
$lang['de_DE']['Silvercart']['UNMARK_ALL'] = 'Markierung aufheben';
$lang['de_DE']['Silvercart']['SORTORDER'] = 'Sortierreihenfolge';
$lang['de_DE']['Silvercart']['PAGE'] = 'Seite';
$lang['de_DE']['Silvercart']['X_OF_Y'] = '%s von %s';
$lang['de_DE']['Silvercart']['EXECUTE'] = 'Ausführen';

$lang['de_DE']['SilvercartAddress']['InvoiceAddressAsShippingAddress'] = 'Rechnungsadresse als Lieferadresse nutzen';
$lang['de_DE']['SilvercartAddress']['ADDITION'] = 'Adresszusatz';
$lang['de_DE']['SilvercartAddress']['CITY'] = 'Ort';
$lang['de_DE']['SilvercartAddress']['COMPANY'] = 'Firma';
$lang['de_DE']['SilvercartAddress']['EDITADDRESS'] = 'Adresse bearbeiten';
$lang['de_DE']['SilvercartAddress']['EDITINVOICEADDRESS'] = 'Rechnungsadresse bearbeiten';
$lang['de_DE']['SilvercartAddress']['EDITSHIPPINGADDRESS'] = 'Lieferadresse bearbeiten';
$lang['de_DE']['SilvercartAddress']['EMAIL'] = 'E-Mail-Adresse';
$lang['de_DE']['SilvercartAddress']['EMAIL_CHECK'] = 'E-Mail-Adresse Gegenprüfung';
$lang['de_DE']['SilvercartAddress']['FAX'] = 'Fax';
$lang['de_DE']['SilvercartAddress']['FIRSTNAME'] = 'Vorname';
$lang['de_DE']['SilvercartAddress']['MISSES'] = 'Frau';
$lang['de_DE']['SilvercartAddress']['MISTER'] = 'Herr';
$lang['de_DE']['SilvercartAddress']['NAME'] = 'Name';
$lang['de_DE']['SilvercartAddress']['NO_ADDRESS_AVAILABLE'] = 'Keine Adresse verfügbar';
$lang['de_DE']['SilvercartAddress']['PHONE'] = 'Telefonnummer';
$lang['de_DE']['SilvercartAddress']['PHONE_SHORT'] = 'Telefon';
$lang['de_DE']['SilvercartAddress']['PHONEAREACODE'] = 'Vorwahl';
$lang['de_DE']['SilvercartAddress']['PLURALNAME'] = 'Adressen';
$lang['de_DE']['SilvercartAddress']['POSTCODE'] = 'PLZ';
$lang['de_DE']['SilvercartAddress']['POSTNUMBER'] = 'Ihre PostNummer';
$lang['de_DE']['SilvercartAddress']['PACKSTATION'] = 'Packstation (z.B. "Packstation 105")';
$lang['de_DE']['SilvercartAddress']['PACKSTATION_PLAIN'] = 'Packstation';
$lang['de_DE']['SilvercartAddress']['PACKSTATION_LABEL'] = 'PACKSTATION';
$lang['de_DE']['SilvercartAddress']['SALUTATION'] = 'Anrede';
$lang['de_DE']['SilvercartAddress']['SINGULARNAME'] = 'Adresse';
$lang['de_DE']['SilvercartAddress']['STREET'] = 'Straße';
$lang['de_DE']['SilvercartAddress']['STREETNUMBER'] = 'Hausnummer';
$lang['de_DE']['SilvercartAddress']['SURNAME'] = 'Nachname';
$lang['de_DE']['SilvercartAddress']['TAXIDNUMBER'] = 'Steuernummer';
$lang['de_DE']['SilvercartAddress']['USE_ABSOLUTEADDRESS']      = 'Hausanschrift als Adresse verwenden';
$lang['de_DE']['SilvercartAddress']['USE_PACKSTATION']          = 'Packstation als Adresse verwenden';
$lang['de_DE']['SilvercartAddress']['IS_PACKSTATION']           = 'Adresse ist Packstation';
$lang['de_DE']['SilvercartAddress']['ADDRESSTYPE']              = 'Art der Adresse';

$lang['de_DE']['SilvercartAddressHolder']['ADD'] = 'Neue Adresse hinzuf&uuml;gen';
$lang['de_DE']['SilvercartAddressHolder']['ADDED_ADDRESS_SUCCESS'] = 'Ihre Adresse wurde gespeichert.';
$lang['de_DE']['SilvercartAddressHolder']['ADDED_ADDRESS_FAILURE'] = 'Ihre Adresse konnte nicht gespeichert werden.';
$lang['de_DE']['SilvercartAddressHolder']['ADDITIONALADDRESS'] = 'Zus&auml;tzliche Adresse';
$lang['de_DE']['SilvercartAddressHolder']['ADDITIONALADDRESSES'] = 'Zus&auml;tzliche Adressen';
$lang['de_DE']['SilvercartAddressHolder']['ADDRESS_CANT_BE_DELETED'] = 'Sie k&ouml;nnen Ihre einzige Adresse nicht l&ouml;schen.';
$lang['de_DE']['SilvercartAddressHolder']['ADDRESS_NOT_FOUND'] = 'Adresse konnte nicht gefunden werden.';
$lang['de_DE']['SilvercartAddressHolder']['ADDRESS_SUCCESSFULLY_DELETED'] = 'Ihre Adresse wurde erfolgreich gelöscht.';
$lang['de_DE']['SilvercartAddressHolder']['CURRENT_DEFAULT_ADDRESSES'] = 'Ihre Standard Rechnungs- &amp; Lieferadresse';
$lang['de_DE']['SilvercartAddressHolder']['DEFAULT_TITLE'] = 'Adressübersicht';
$lang['de_DE']['SilvercartAddressHolder']['DEFAULT_URLSEGMENT'] = 'adressuebersicht';
$lang['de_DE']['SilvercartAddressHolder']['DEFAULT_INVOICE'] = 'Aktive Rechnungsadresse';
$lang['de_DE']['SilvercartAddressHolder']['DEFAULT_SHIPPING'] = 'Aktive Lieferadresse';
$lang['de_DE']['SilvercartAddressHolder']['DEFAULT_INVOICEADDRESS'] = 'Standard Rechnungsadresse';
$lang['de_DE']['SilvercartAddressHolder']['DEFAULT_SHIPPINGADDRESS'] = 'Standard Lieferadresse';
$lang['de_DE']['SilvercartAddressHolder']['DELETE'] = 'Löschen';
$lang['de_DE']['SilvercartAddressHolder']['EDIT'] = 'Bearbeiten';
$lang['de_DE']['SilvercartAddressHolder']['EXCUSE_INVOICEADDRESS'] = 'Entschuldigen Sie, aber Sie haben noch keine Rechnungsadresse angelegt.';
$lang['de_DE']['SilvercartAddressHolder']['EXCUSE_SHIPPINGADDRESS'] = 'Entschuldigen Sie, aber Sie haben noch keine Lieferadresse angelegt.';
$lang['de_DE']['SilvercartAddressHolder']['INVOICEADDRESS'] = 'Rechnungsadresse';
$lang['de_DE']['SilvercartAddressHolder']['INVOICEADDRESS_TAB'] = 'Rechnungsadresse';
$lang['de_DE']['SilvercartAddressHolder']['INVOICEANDSHIPPINGADDRESS'] = 'Rechnungs- und Lieferadresse';
$lang['de_DE']['SilvercartAddressHolder']['NOT_DEFINED'] = 'Noch nicht definiert';
$lang['de_DE']['SilvercartAddressHolder']['PLURALNAME'] = 'Adressübersichtseiten';
$lang['de_DE']['SilvercartAddressHolder']['SET_AS'] = 'Verwenden als';
$lang['de_DE']['SilvercartAddressHolder']['SET_DEFAULT_INVOICE'] = 'Verwenden als Rechnungsadresse';
$lang['de_DE']['SilvercartAddressHolder']['SET_DEFAULT_SHIPPING'] = 'Verwenden als Lieferadresse';
$lang['de_DE']['SilvercartAddressHolder']['SHIPPINGADDRESS'] = 'Lieferadresse';
$lang['de_DE']['SilvercartAddressHolder']['SHIPPINGADDRESS_TAB'] = 'Lieferadresse';
$lang['de_DE']['SilvercartAddressHolder']['SINGULARNAME'] = 'Adressübersichtseite';
$lang['de_DE']['SilvercartAddressHolder']['TITLE'] = 'Adressübersicht';
$lang['de_DE']['SilvercartAddressHolder']['UPDATED_INVOICE_ADDRESS'] = 'Ihre Rechungsadresse wurde erfolgreich aktualisiert.';
$lang['de_DE']['SilvercartAddressHolder']['UPDATED_SHIPPING_ADDRESS'] = 'Ihre Lieferadresse wurde erfolgreich aktualisiert.';
$lang['de_DE']['SilvercartAddressHolder']['URL_SEGMENT'] = 'adressuebersicht';

$lang['de_DE']['SilvercartAddressPage']['DEFAULT_TITLE'] = 'Adressdetails';
$lang['de_DE']['SilvercartAddressPage']['DEFAULT_URLSEGMENT'] = 'adressdetails';
$lang['de_DE']['SilvercartAddressPage']['PLURALNAME'] = 'Adressseiten';
$lang['de_DE']['SilvercartAddressPage']['SINGULARNAME'] = 'Adressseite';
$lang['de_DE']['SilvercartAddressPage']['TITLE'] = 'Adressdetails';
$lang['de_DE']['SilvercartAddressPage']['URL_SEGMENT'] = 'adressdetails';

$lang['de_DE']['SilvercartAnonymousNewsletterRecipient']['SINGULARNAME'] = 'Anonymer Newsletterempfänger';
$lang['de_DE']['SilvercartAnonymousNewsletterRecipient']['PLURALNAME'] = 'Anonyme Newsletterempfänger';

$lang['de_DE']['SilvercartAvailabilityStatus']['PLURALNAME'] = 'Verfügbarkeiten';
$lang['de_DE']['SilvercartAvailabilityStatus']['SINGULARNAME'] = 'Verfügbarkeit';
$lang['de_DE']['SilvercartAvailabilityStatus']['TITLE'] = 'Bezeichnung';
$lang['de_DE']['SilvercartAvailabilityStatus']['STATUS_AVAILABLE'] = 'verfügbar';
$lang['de_DE']['SilvercartAvailabilityStatus']['STATUS_NOT_AVAILABLE'] = 'nicht verfügbar';
$lang['de_DE']['SilvercartAvailabilityStatus']['STATUS_AVAILABLE_IN'] = 'verfügbar in %s %sn';
$lang['de_DE']['SilvercartAvailabilityStatus']['STATUS_AVAILABLE_IN_MIN_MAX'] = 'verfügbar in %s bis %s %sn';

$lang['de_DE']['SilvercartAvailabilityStatusLanguage']['SINGULARNAME']          = _t('Silvercart.TRANSLATION');
$lang['de_DE']['SilvercartAvailabilityStatusLanguage']['PLURALNAME']            = _t('Silvercart.TRANSLATIONS');

$lang['de_DE']['SilvercartDashboard']['NEWS_HEADLINE'] = 'Neuigkeiten';
$lang['de_DE']['SilvercartDashboard']['NEWS_READ_MORE'] = 'Weiterlesen';

$lang['de_DE']['SilvercartDeeplink']['PLURALNAME'] = 'Deeplinks';
$lang['de_DE']['SilvercartDeeplink']['SINGULARNAME'] = 'Deeplink';

$lang['de_DE']['SilvercartDeeplinkAttribute']['PLURALNAME'] = 'Attribute';
$lang['de_DE']['SilvercartDeeplinkAttribute']['SINGULARNAME'] = 'Attribut';

$lang['de_DE']['SilvercartEmailAddress']['SINGULARNAME'] = 'Emailadressen';
$lang['de_DE']['SilvercartEmailAddress']['SINGULARNAME'] = 'Emailadresse';

$lang['de_DE']['SilvercartGoogleMerchantTaxonomy']['LEVEL1']    = 'Stufe 1';
$lang['de_DE']['SilvercartGoogleMerchantTaxonomy']['LEVEL2']    = 'Stufe 2';
$lang['de_DE']['SilvercartGoogleMerchantTaxonomy']['LEVEL3']    = 'Stufe 3';
$lang['de_DE']['SilvercartGoogleMerchantTaxonomy']['LEVEL4']    = 'Stufe 4';
$lang['de_DE']['SilvercartGoogleMerchantTaxonomy']['LEVEL5']    = 'Stufe 5';
$lang['de_DE']['SilvercartGoogleMerchantTaxonomy']['LEVEL6']    = 'Stufe 6';
$lang['de_DE']['SilvercartGoogleMerchantTaxonomy']['SINGULARNAME'] = 'Google Taxonomie';
$lang['de_DE']['SilvercartGoogleMerchantTaxonomy']['PLURALNAME']   = 'Google Taxonomie';

$lang['de_DE']['SilvercartImageAdmin']['SELECT_PRODUCT_IMAGES'] = 'Produktbilder';
$lang['de_DE']['SilvercartImageAdmin']['SELECT_PAYMENTMETHOD_IMAGES'] = 'Bilder für Zahlungsarten';
$lang['de_DE']['SilvercartImageAdmin']['SELECT_OTHER_IMAGES'] = 'Andere Bilder';
$lang['de_DE']['SilvercartImageAdmin']['SELECT_IMAGE_TYPE'] = 'Bildtyp wählen';

$lang['de_DE']['SilvercartImageSliderImage']['LINKPAGE'] = 'Seite, zu der verlinkt werden soll';
$lang['de_DE']['SilvercartImageSliderImage']['SINGULARNAME'] = 'Sliderbild';
$lang['de_DE']['SilvercartImageSliderImage']['PLURALNAME'] = 'Sliderbilder';

$lang['de_DE']['SilvercartImageSliderImageLanguage']['PLURALNAME']              = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartImageSliderImageLanguage']['SINGULARNAME']            = _t('Silvercart.TRANSLATION');

$lang['de_DE']['SilvercartImageSliderWidget']['TITLE']          = 'Bilder';
$lang['de_DE']['SilvercartImageSliderWidget']['CMSTITLE']       = 'Imageslider';
$lang['de_DE']['SilvercartImageSliderWidget']['DESCRIPTION']    = 'Stellt einen Imageslider zur Verfügung, das mehrere Bilder in einer Slideshow darstellt.';

$lang['de_DE']['SilvercartImageSliderWidgetLanguage']['SINGULARNAME']           = _t('Silvercart.TRANSLATION');
$lang['de_DE']['SilvercartImageSliderWidgetLanguage']['PLURALNAME']             = _t('Silvercart.TRANSLATIONS');

$lang['de_DE']['SilvercartMenu']['SECTION_payment']             = 'Zahlung';
$lang['de_DE']['SilvercartMenu']['SECTION_shipping']            = 'Versand';
$lang['de_DE']['SilvercartMenu']['SECTION_externalConnections'] = 'Externe Anbindung';
$lang['de_DE']['SilvercartMenu']['SECTION_others']              = 'Anderes';
$lang['de_DE']['SilvercartMenu']['SECTION_maintenance']         = 'Wartung';

$lang['de_DE']['SilvercartMetricsFieldOrdersByDay']['NO_ORDERS_YET']  = 'Es wurden noch keine Bestellungen aufgegeben.';
$lang['de_DE']['SilvercartMetricsFieldOrdersByDay']['CHART_HEADLINE'] = 'Bestellungen pro Tag';
$lang['de_DE']['SilvercartMetricsFieldOrdersByDay']['FIELD_HEADLINE'] = 'Bestellverlauf';

$lang['de_DE']['SilvercartMultiSelectAndOrderField']['ADD_CALLBACK_FIELD']      = 'Callback-Feld hinzufügen';
$lang['de_DE']['SilvercartMultiSelectAndOrderField']['ATTRIBUTED_FIELDS']       = 'Zugewiesene Felder';
$lang['de_DE']['SilvercartMultiSelectAndOrderField']['CSV_SEPARATOR_LABEL']     = 'CSV Trennzeichen';
$lang['de_DE']['SilvercartMultiSelectAndOrderField']['FIELD_NAME']              = 'Feldname';
$lang['de_DE']['SilvercartMultiSelectAndOrderField']['MOVE_DOWN']               = 'Nach unten schieben';
$lang['de_DE']['SilvercartMultiSelectAndOrderField']['MOVE_UP']                 = 'Nach oben schieben';
$lang['de_DE']['SilvercartMultiSelectAndOrderField']['NOT_ATTRIBUTED_FIELDS']   = 'Nicht zugewiesene Felder';

$lang['de_DE']['SilvercartNewsletter']['OPTIN_NOT_FINISHED_MESSAGE']        = 'Sie erhalten den Newsletter erst, wenn Sie den Bestätigungslink in der Ihnen zugeschickten Email aufrufen.';
$lang['de_DE']['SilvercartNewsletter']['SUBSCRIBED']                        = 'Sie haben den Newsletter abonniert';
$lang['de_DE']['SilvercartNewsletter']['UNSUBSCRIBED']                      = 'Sie haben den Newsletter nicht abonniert';
$lang['de_DE']['SilvercartNewsletter']['PLURALNAME']                        = 'Newsletter';
$lang['de_DE']['SilvercartNewsletter']['SINGULARNAME']                      = 'Newsletter';

$lang['de_DE']['SilvercartNewsletterPage']['DEFAULT_TITLE']                 = 'Newsletter';
$lang['de_DE']['SilvercartNewsletterPage']['DEFAULT_URLSEGMENT']            = 'newsletter';
$lang['de_DE']['SilvercartNewsletterPage']['TITLE']                         = 'Newsletter';
$lang['de_DE']['SilvercartNewsletterPage']['URL_SEGMENT']                   = 'newsletter';
$lang['de_DE']['SilvercartNewsletterPage']['PLURALNAME']                    = 'Newsletter-Seiten';
$lang['de_DE']['SilvercartNewsletterPage']['SINGULARNAME']                  = 'Newsletter-Seite';
$lang['de_DE']['SilvercartNewsletterResponsePage']['DEFAULT_TITLE']         = 'Newsletter Status';
$lang['de_DE']['SilvercartNewsletterResponsePage']['DEFAULT_URLSEGMENT']    = 'newsletter_status';
$lang['de_DE']['SilvercartNewsletterResponsePage']['TITLE']                 = 'Newsletter Status';
$lang['de_DE']['SilvercartNewsletterResponsePage']['URL_SEGMENT']           = 'newsletter_status';
$lang['de_DE']['SilvercartNewsletterResponsePage']['STATUS_TITLE']          = 'Ihre Newslettereinstellungen';
$lang['de_DE']['SilvercartNewsletterResponsePage']['PLURALNAME']            = 'Newsletter-Antwort-Seiten';
$lang['de_DE']['SilvercartNewsletterResponsePage']['SINGULARNAME']          = 'Newsletter-Antwort-Seite';
$lang['de_DE']['SilvercartNewsletterForm']['ACTIONFIELD_TITLE']             = 'Was wollen Sie tun?';
$lang['de_DE']['SilvercartNewsletterForm']['ACTIONFIELD_SUBSCRIBE']         = 'Ich möchte den Newsletter abonnieren';
$lang['de_DE']['SilvercartNewsletterForm']['ACTIONFIELD_UNSUBSCRIBE']       = 'Ich möchte den Newsletter abbestellen';
$lang['de_DE']['SilvercartNewsletterStatus']['ALREADY_SUBSCRIBED']          = 'Die Emailadresse "%s" ist schon für den Newsletterempfang registriert.';
$lang['de_DE']['SilvercartNewsletterStatus']['REGULAR_CUSTOMER_WITH_SAME_EMAIL_EXISTS'] = 'Es ist schon ein Kunde mit der Emailadresse "%s" registriert. Bitte loggen Sie sich zuerst ein und nehmen Sie dann die Einstellungen für den Newsletterempfang vor: <a href="%s">Zum Login</a>.';
$lang['de_DE']['SilvercartNewsletterStatus']['NO_EMAIL_FOUND']              = 'Die Emailadresse "%s" konnte nicht gefunden werden.';
$lang['de_DE']['SilvercartNewsletterStatus']['UNSUBSCRIBED_SUCCESSFULLY']   = 'Die Emailadresse "%s" wurde von der Liste der Newsletterempfänger entfernt.';
$lang['de_DE']['SilvercartNewsletterStatus']['SUBSCRIBED_SUCCESSFULLY']     = 'Die Emailadresse "%s" wurde zu der Liste der Newsletterempfänger hinzugefügt.';
$lang['de_DE']['SilvercartNewsletterStatus']['SUBSCRIBED_SUCCESSFULLY_FOR_OPT_IN'] = 'Es wurde eine Email mit Instruktionen zur Bestätigung an die Adresse "%s" geschickt.';

$lang['de_DE']['SilvercartNumberRange']['ACTUAL'] = 'Aktuell';
$lang['de_DE']['SilvercartNumberRange']['ACTUALCOUNT'] = 'Aktuell';
$lang['de_DE']['SilvercartNumberRange']['CUSTOMERNUMBER'] = 'Kundennummer';
$lang['de_DE']['SilvercartNumberRange']['END'] = 'Ende';
$lang['de_DE']['SilvercartNumberRange']['ENDCOUNT'] = 'Ende';
$lang['de_DE']['SilvercartNumberRange']['IDENTIFIER'] = 'Bezeichner';
$lang['de_DE']['SilvercartNumberRange']['INVOICENUMBER'] = 'Rechnungsnummer';
$lang['de_DE']['SilvercartNumberRange']['ORDERNUMBER'] = 'Bestellnummer';
$lang['de_DE']['SilvercartNumberRange']['PLURALNAME'] = 'Nummernkreise';
$lang['de_DE']['SilvercartNumberRange']['PREFIX'] = 'Prefix';
$lang['de_DE']['SilvercartNumberRange']['SINGULARNAME'] = 'Nummernkreis';
$lang['de_DE']['SilvercartNumberRange']['START'] = 'Start';
$lang['de_DE']['SilvercartNumberRange']['STARTCOUNT'] = 'Start';
$lang['de_DE']['SilvercartNumberRange']['SUFFIX'] = 'Suffix';
$lang['de_DE']['SilvercartNumberRange']['TITLE'] = 'Titel';

$lang['de_DE']['SilvercartProduct']['IS_ACTIVE'] = 'ist aktiv';
$lang['de_DE']['SilvercartProduct']['ADD_TO_CART'] = 'in den Warenkorb';
$lang['de_DE']['SilvercartProduct']['AMOUNT_UNIT'] = 'Verkaufsmengeneinheit';
$lang['de_DE']['SilvercartProduct']['DEEPLINK_FOR'] = 'Deeplink für das Attribut "%s"';
$lang['de_DE']['SilvercartProduct']['DEEPLINK_TEXT'] = 'Wenn Deeplinks definiert sind, werden alle Deeplinks zu diesem Artikel angezeigt.';
$lang['de_DE']['SilvercartProduct']['CATALOGSORT'] = 'Katalogsortierung';
$lang['de_DE']['SilvercartProduct']['CHOOSE_MASTER'] = '-- Master wählen --';
$lang['de_DE']['SilvercartProduct']['COLUMN_TITLE'] = 'Name';
$lang['de_DE']['SilvercartProduct']['DESCRIPTION'] = 'Artikelbeschreibung';
$lang['de_DE']['SilvercartProduct']['EAN'] = 'EAN';
$lang['de_DE']['SilvercartProduct']['STOCKQUANTITY'] = 'Lagerbestand';
$lang['de_DE']['SilvercartProduct']['FREE_OF_CHARGE'] = 'versandkostenfrei';
$lang['de_DE']['SilvercartProduct']['IMAGE'] = 'Artikelbild';
$lang['de_DE']['SilvercartProduct']['IMAGE_NOT_AVAILABLE'] = 'Kein Artikelbild zugeordnet';
$lang['de_DE']['SilvercartProduct']['IMPORTIMAGESFORM_ACTION'] = 'Bilder importieren';
$lang['de_DE']['SilvercartProduct']['IMPORTIMAGESFORM_ERROR_DIRECTORYNOTVALID'] = 'Das Verzeichnis existiert nicht';
$lang['de_DE']['SilvercartProduct']['IMPORTIMAGESFORM_ERROR_NOIMAGEDIRECTORYGIVEN'] = 'Es wurde kein Verzeichnis angegeben';
$lang['de_DE']['SilvercartProduct']['IMPORTIMAGESFORM_HEADLINE'] = 'Bilder nachträglich importieren';
$lang['de_DE']['SilvercartProduct']['IMPORTIMAGESFORM_IMAGEDIRECTORY'] = 'Verzeichnis';
$lang['de_DE']['SilvercartProduct']['IMPORTIMAGESFORM_IMAGEDIRECTORY_DESC'] = 'Absoluter Pfad zum Verzeichnis auf dem Webserver, in dem die Bilder liegen. (Beispiel: /var/www/silvercart/images/)';
$lang['de_DE']['SilvercartProduct']['IMPORTIMAGESFORM_REPORT'] = '<p>Es wurden %d Dateien gefunden.</p><p>%d davon konnten Produkten zugeordnet werden und wurden importiert.</p>';
$lang['de_DE']['SilvercartProduct']['LIST_PRICE'] = 'Listenpreis';
$lang['de_DE']['SilvercartProduct']['MASTERPRODUCT'] = 'Basisartikel';
$lang['de_DE']['SilvercartProduct']['METADATA'] = 'Meta Daten';
$lang['de_DE']['SilvercartProduct']['METADESCRIPTION'] = 'Meta Beschreibung für Suchmaschinen';
$lang['de_DE']['SilvercartProduct']['METAKEYWORDS'] = 'Meta Schlagworte für Suchmaschinen';
$lang['de_DE']['SilvercartProduct']['METATITLE'] = 'Meta Titel für Suchmaschinen';
$lang['de_DE']['SilvercartProduct']['MSRP'] = 'UVP';
$lang['de_DE']['SilvercartProduct']['MSRP_CURRENCY'] = 'Währung UVP';
$lang['de_DE']['SilvercartProduct']['NAME_DESCRIPTION'] = 'Name & Beschreibung';
$lang['de_DE']['SilvercartProduct']['PACKAGING_QUANTITY'] = 'Verkaufsmenge';
$lang['de_DE']['SilvercartProduct']['PACKAGING_UNIT'] = 'Verpackungseinheit';
$lang['de_DE']['SilvercartProduct']['PLURALNAME'] = 'Artikel';
$lang['de_DE']['SilvercartProduct']['PRICE'] = 'Preis';
$lang['de_DE']['SilvercartProduct']['PRICE_AMOUNT_ASC'] = 'Preis aufsteigend';
$lang['de_DE']['SilvercartProduct']['PRICE_AMOUNT_DESC'] = 'Preis absteigend';
$lang['de_DE']['SilvercartProduct']['PRICE_GROSS'] = 'Preis (Brutto)';
$lang['de_DE']['SilvercartProduct']['PRICE_GROSS_CURRENCY'] = 'Währung (Brutto)';
$lang['de_DE']['SilvercartProduct']['PRICE_NET'] = 'Preis (Netto)';
$lang['de_DE']['SilvercartProduct']['PRICE_NET_CURRENCY'] = 'Währung (Netto)';
$lang['de_DE']['SilvercartProduct']['PRICE_SINGLE'] = 'Einzelpreis';
$lang['de_DE']['SilvercartProduct']['PRICE_SINGLE_NET'] = 'Einzelpreis (Netto)';
$lang['de_DE']['SilvercartProduct']['PRICE_ENTIRE'] = 'Gesamtpreis';
$lang['de_DE']['SilvercartProduct']['PRODUCTNUMBER'] = 'Artikelnummer';
$lang['de_DE']['SilvercartProduct']['PRODUCTNUMBER_SHORT'] = 'Art.-Nr.';
$lang['de_DE']['SilvercartProduct']['PRODUCTNUMBER_MANUFACTURER'] = 'Artikelnummer (Hersteller)';
$lang['de_DE']['SilvercartProduct']['PURCHASEPRICE'] = 'Einkaufspreis';
$lang['de_DE']['SilvercartProduct']['PURCHASEPRICE_CURRENCY'] = 'Währung Einkaufspreis';
$lang['de_DE']['SilvercartProduct']['PURCHASE_MIN_DURATION'] = 'Min. Bezugsdauer';
$lang['de_DE']['SilvercartProduct']['PURCHASE_MAX_DURATION'] = 'Max. Bezugsdauer';
$lang['de_DE']['SilvercartProduct']['PURCHASE_TIME_UNIT'] = 'Einheit (WBZ)';
$lang['de_DE']['SilvercartProduct']['QUANTITY'] = 'Anzahl';
$lang['de_DE']['SilvercartProduct']['QUANTITY_SHORT'] = 'Anz.';
$lang['de_DE']['SilvercartProduct']['PRODUCT_QUESTION'] = 'Bitte beantworten Sie mir die folgenden Fragen zum Artikel %s (%s):';
$lang['de_DE']['SilvercartProduct']['PRODUCT_QUESTION_LABEL'] = 'Fragen zum Artikel';
$lang['de_DE']['SilvercartProduct']['SHORTDESCRIPTION'] = 'Listenbeschreibung';
$lang['de_DE']['SilvercartProduct']['SINGULARNAME'] = 'Artikel';
$lang['de_DE']['SilvercartProduct']['STOCK_QUANTITY'] = 'Ist der Lagerbestand dieses Artikels überbuchbar?';
$lang['de_DE']['SilvercartProduct']['STOCK_QUANTITY_SHORT'] = 'Ist überbuchbar?';
$lang['de_DE']['SilvercartProduct']['STOCK_QUANTITY_EXPIRATION_DATE'] = 'Datum, ab welchem Lagerbestand nicht mehr überbuchbar ist';
$lang['de_DE']['SilvercartProduct']['TITLE'] = 'Artikel';
$lang['de_DE']['SilvercartProduct']['TITLE_ASC'] = 'Bezeichnung aufsteigend';
$lang['de_DE']['SilvercartProduct']['TITLE_DESC'] = 'Bezeichnung absteigend';
$lang['de_DE']['SilvercartProduct']['VAT'] = 'MwSt';
$lang['de_DE']['SilvercartProduct']['WEIGHT'] = 'Gewicht';

$lang['de_DE']['SilvercartProductExport']['ACTIVATE_CSV_HEADERS']                           = 'CSV Header aktivieren';
$lang['de_DE']['SilvercartProductExport']['ATTRIBUTE_EXPORT_FIELDS_LABEL']                  = 'Exportfelder bestimmen';
$lang['de_DE']['SilvercartProductExport']['BASEURLFORLINKS']                                = 'Basis-URL für Links';
$lang['de_DE']['SilvercartProductExport']['BREADCRUMB_DELIMITER']                           = 'Trennzeichen für Breadcrumbs';
$lang['de_DE']['SilvercartProductExport']['BREADCRUMB_DELIMITER_DESCRIPTION']               = 'Wird für die Trennung der Einzelkomponenten aller Breadcrumb Felder verwendet';
$lang['de_DE']['SilvercartProductExport']['COUNTRY_DESCRIPTION']                            = 'Kontext-Land für Angaben wie Versandkosten';
$lang['de_DE']['SilvercartProductExport']['CREATE_TIMESTAMP_FILE']                          = 'Timestamp Datei erzeugen';
$lang['de_DE']['SilvercartProductExport']['FIELD_ATTRIBUTED_EXPORT_FIELDS']                 = 'Zugeordnete Exportfelder';
$lang['de_DE']['SilvercartProductExport']['FIELD_AVAILABLE_EXPORT_FIELDS']                  = 'Verfügbare Exportfelder';
$lang['de_DE']['SilvercartProductExport']['FIELD_CSV_SEPARATOR']                            = 'CSV Trennzeichen';
$lang['de_DE']['SilvercartProductExport']['IS_ACTIVE']                                      = 'Ist aktiviert';
$lang['de_DE']['SilvercartProductExport']['FIELD_LAST_EXPORT_DATE_TIME']                    = 'Letzter Export';
$lang['de_DE']['SilvercartProductExport']['FIELD_NAME']                                     = 'Name';
$lang['de_DE']['SilvercartProductExport']['FIELD_PUSH_ENABLED']                             = 'Push aktivieren';
$lang['de_DE']['SilvercartProductExport']['FIELD_PUSH_TO_URL']                              = 'Pushen an URL';
$lang['de_DE']['SilvercartProductExport']['FIELD_SELECT_ONLY_HEADLINE']                     = 'Exportiere Artikel, die ...';
$lang['de_DE']['SilvercartProductExport']['FIELD_SELECT_ONLY_PRODUCTS_QUANTITY']            = 'mehr als';
$lang['de_DE']['SilvercartProductExport']['FIELD_SELECT_ONLY_PRODUCTS_WITH_GOUP']           = '... einer Warengruppe zugeordnet sind';
$lang['de_DE']['SilvercartProductExport']['FIELD_SELECT_ONLY_PRODUCTS_WITH_IMAGE']          = '... ein Artikelbild besitzen';
$lang['de_DE']['SilvercartProductExport']['FIELD_SELECT_ONLY_PRODUCTS_WITH_MANUFACTURER']   = '... einem Hersteller zugeordnet sind';
$lang['de_DE']['SilvercartProductExport']['FIELD_SELECT_ONLY_PRODUCTS_WITH_QUANTITY']       = '... in einer Menge wie folgend angegeben verfügbar sind';
$lang['de_DE']['SilvercartProductExport']['FIELD_SELECT_ONLY_PRODUCTS_OF_RELATED_GROUPS']   = '... einer der hier verknüpften Warengruppen angehören oder darin gespiegelt sind';
$lang['de_DE']['SilvercartProductExport']['FIELD_UPDATE_INTERVAL']                          = 'Aktualisierungsrhythmus';
$lang['de_DE']['SilvercartProductExport']['FIELD_UPDATE_INTERVAL_PERIOD']                   = 'Aktualisierungszeitraum';
$lang['de_DE']['SilvercartProductExport']['PLURALNAME']                                     = 'Artikelexporte';
$lang['de_DE']['SilvercartProductExport']['PLURAL_NAME']                                    = 'Artikelexporte';
$lang['de_DE']['SilvercartProductExport']['PROTOCLOFORLINKS']                               = 'Protokoll für Links';
$lang['de_DE']['SilvercartProductExport']['SINGULARNAME']                                   = 'Artikelexport';
$lang['de_DE']['SilvercartProductExporter']['PLURALNAME']                                   = 'Preisportal Exporte';
$lang['de_DE']['SilvercartProductExporter']['SINGULARNAME']                                 = 'Preisportal Export';
$lang['de_DE']['SilvercartProductExporter']['URL']                                          = 'URL';

$lang['de_DE']['SilvercartProductExportAdmin']['PUSH_ENABLED_LABEL']                    = 'Push aktivieren';
$lang['de_DE']['SilvercartProductExportAdmin']['UPDATE_INTERVAL_LABEL']                 = 'Aktualisierungsinterval';
$lang['de_DE']['SilvercartProductExportAdmin']['UPDATE_INTERVAL_PERIOD_LABEL']          = 'Aktualisierungsinterval Periode';
$lang['de_DE']['SilvercartProductExportAdmin']['SILVERCART_PRODUCT_EXPORT_ADMIN_LABEL'] = 'SilverCart Artikelexport';
$lang['de_DE']['SilvercartProductExportAdmin']['TAB_BASIC_SETTINGS']                    = 'Grundeinstellungen';
$lang['de_DE']['SilvercartProductExportAdmin']['TAB_PRODUCT_SELECTION']                 = 'Artikelauswahl';
$lang['de_DE']['SilvercartProductExportAdmin']['TAB_EXPORT_FIELD_DEFINITIONS']          = 'CSV-Felddefinitionen';
$lang['de_DE']['SilvercartProductExportAdmin']['TAB_HEADER_CONFIGURATION']              = 'CSV-Kopfbereich';

$lang['de_DE']['SilvercartProductGroupHolder']['DEFAULT_TITLE']                     = 'Warengruppen';
$lang['de_DE']['SilvercartProductGroupHolder']['DEFAULT_URLSEGMENT']                = 'warengruppen';
$lang['de_DE']['SilvercartProductGroupHolder']['PAGE_TITLE']                        = 'Warengruppen';
$lang['de_DE']['SilvercartProductGroupHolder']['PLURALNAME']                        = 'Artikelgruppenübersichten';
$lang['de_DE']['SilvercartProductGroupHolder']['SHOW_PRODUCTS_WITH_COUNT_PLURAL']   = '%s Artikel anzeigen';
$lang['de_DE']['SilvercartProductGroupHolder']['SHOW_PRODUCTS_WITH_COUNT_SINGULAR'] = '%s Artikel anzeigen';
$lang['de_DE']['SilvercartProductGroupHolder']['SINGULARNAME']                      = 'Artikelgruppenübersicht';
$lang['de_DE']['SilvercartProductGroupHolder']['SUBGROUPS_OF']                      = 'Untergruppen von ';
$lang['de_DE']['SilvercartProductGroupHolder']['URL_SEGMENT']                       = 'warengruppen';

$lang['de_DE']['SilvercartProductGroupMirrorPage']['SINGULARNAME']  = 'Spiegel-Warengruppe';
$lang['de_DE']['SilvercartProductGroupMirrorPage']['PLURALNAME']    = 'Spiegel-Warengruppen';

$lang['de_DE']['SilvercartProductGroupPage']['ATTRIBUTES'] = 'Attribut';
$lang['de_DE']['SilvercartProductGroupPage']['BREADCRUMBS'] = 'Warengruppen-Breadcrumbs';
$lang['de_DE']['SilvercartProductGroupPage']['DONOTSHOWPRODUCTS'] = '<strong>keine</strong> Produkte in der Übersicht anzeigen';
$lang['de_DE']['SilvercartProductGroupPage']['GROUP_PICTURE'] = 'Bild der Gruppe';
$lang['de_DE']['SilvercartProductGroupPage']['MANAGE_PRODUCTS_BUTTON'] = 'Produkte verwalten';
$lang['de_DE']['SilvercartProductGroupPage']['MANUFACTURER_LINK'] = 'hersteller';
$lang['de_DE']['SilvercartProductGroupPage']['PLURALNAME'] = 'Warengruppen';
$lang['de_DE']['SilvercartProductGroupPage']['PRODUCTSPERPAGE'] = 'Artikel pro Seite';
$lang['de_DE']['SilvercartProductGroupPage']['PRODUCTSPERPAGEHINT'] = 'Geben Sie für Artikel oder Warengruppen pro Seite 0 (Null) an, um die Standard-Einstellung zu verwenden.';
$lang['de_DE']['SilvercartProductGroupPage']['PRODUCTGROUPSPERPAGE'] = 'Warengruppen pro Seite';
$lang['de_DE']['SilvercartProductGroupPage']['SINGULARNAME'] = 'Warengruppe';
$lang['de_DE']['SilvercartProductGroupPage']['USE_CONTENT_FROM_PARENT'] = 'Inhalte von übergeordneten Seiten übernehmen';
$lang['de_DE']['SilvercartProductGroupPage']['DEFAULTGROUPVIEW'] = 'Standard-Produktlisten-Ansicht';
$lang['de_DE']['SilvercartProductGroupPage']['DEFAULTGROUPVIEW_DEFAULT'] = 'Übernehmen von übergeordneter Gruppe/Standartkonfiguration';
$lang['de_DE']['SilvercartProductGroupPage']['DEFAULTGROUPHOLDERVIEW'] = 'Standard-Produktgruppen-Ansicht';
$lang['de_DE']['SilvercartProductGroupPage']['USEONLYDEFAULTGROUPVIEW'] = 'Ausschließlich die Standard-Produktlisten-Ansicht verwenden';
$lang['de_DE']['SilvercartProductGroupPage']['USEONLYDEFAULTGROUPHOLDERVIEW'] = 'Ausschließlich die Standard-Produktgruppen-Ansicht verwenden';
$lang['de_DE']['SilvercartProductGroupPage']['PRODUCT_ON_PAGE'] = '%s Produkt auf %s Seite';
$lang['de_DE']['SilvercartProductGroupPage']['PRODUCTS_ON_PAGE'] = '%s Produkte auf %s Seite';
$lang['de_DE']['SilvercartProductGroupPage']['PRODUCTS_ON_PAGES'] = '%s Produkte auf %s Seiten';

$lang['de_DE']['SilvercartProductGroupPageSelector']['OK']                      = 'Ok';
$lang['de_DE']['SilvercartProductGroupPageSelector']['PRODUCTS_PER_PAGE']       = 'Artikel pro Seite';
$lang['de_DE']['SilvercartProductGroupPageSelector']['SORT_ORDER']              = 'Sortierung';

$lang['de_DE']['SilvercartProductImageGallery']['PLURALNAME'] = 'Gallerien';
$lang['de_DE']['SilvercartProductImageGallery']['SINGULARNAME'] = 'Gallerie';

$lang['de_DE']['SilvercartProductPage']['ADD_TO_CART'] = 'in den Warenkorb';
$lang['de_DE']['SilvercartProductPage']['OUT_OF_STOCK'] = 'Dieser Artikel ist ausverkauft.';
$lang['de_DE']['SilvercartProductPage']['PACKAGING_CONTENT'] = 'Inhalt';
$lang['de_DE']['SilvercartProductPage']['PLURALNAME'] = 'Artikeldetailseiten';
$lang['de_DE']['SilvercartProductPage']['QUANTITY'] = 'Anzahl';
$lang['de_DE']['SilvercartProductPage']['SINGULARNAME'] = 'Artikeldetailseite';
$lang['de_DE']['SilvercartProductPage']['URL_SEGMENT'] = 'artikeldetails';

$lang['de_DE']['SilvercartProductTexts']['PLURALNAME'] = 'Artikelübersetzungstexte';
$lang['de_DE']['SilvercartProductTexts']['SINGULARNAME'] = 'Artikelübersetzungstext';

$lang['de_DE']['SilvercartCarrier']['ATTRIBUTED_SHIPPINGMETHODS'] = 'zugeordnete Versandart';
$lang['de_DE']['SilvercartCarrier']['FULL_NAME'] = 'voller Name';
$lang['de_DE']['SilvercartCarrier']['PLURALNAME'] = 'Frachtführer';
$lang['de_DE']['SilvercartCarrier']['SINGULARNAME'] = 'Frachtführer';

$lang['de_DE']['SilvercartCarrierLanguage']['SINGULARNAME']                     = _t('Silvercart.TRANSLATION');
$lang['de_DE']['SilvercartCarrierLanguage']['PLURALNAME']                       = _t('Silvercart.TRANSLATIONS');

$lang['de_DE']['SilvercartCartPage']['DEFAULT_TITLE']                           = 'Warenkorb';
$lang['de_DE']['SilvercartCartPage']['DEFAULT_URLSEGMENT']                      = 'warenkorb';
$lang['de_DE']['SilvercartCartPage']['CART_EMPTY']                              = 'Der Warenkorb ist leer.';
$lang['de_DE']['SilvercartCartPage']['PLURALNAME']                              = 'Warenkorbseiten';
$lang['de_DE']['SilvercartCartPage']['SINGULARNAME']                            = 'Warenkorbseiten';
$lang['de_DE']['SilvercartCartPage']['URL_SEGMENT']                             = 'warenkorb';

$lang['de_DE']['SilvercartCheckoutFormStep']['CHOOSEN_PAYMENT'] = 'gewählte Bezahlart';
$lang['de_DE']['SilvercartCheckoutFormStep']['CHOOSEN_SHIPPING'] = 'gewählte Versandart';
$lang['de_DE']['SilvercartCheckoutFormStep']['FORWARD'] = 'Weiter';
$lang['de_DE']['SilvercartCheckoutFormStep']['I_ACCEPT_REVOCATION'] = 'Ich akzeptiere die Wiederufsbelehrung';
$lang['de_DE']['SilvercartCheckoutFormStep']['I_ACCEPT_TERMS'] = 'Ich akzeptiere die Allgemeinen Geschäftsbedingungen.';
$lang['de_DE']['SilvercartCheckoutFormStep']['I_SUBSCRIBE_NEWSLETTER'] = 'Ich möchte den Newsletter abonnieren.';
$lang['de_DE']['SilvercartCheckoutFormStep']['ORDER'] = 'Bestellung';
$lang['de_DE']['SilvercartCheckoutFormStep']['ORDER_NOW'] = 'Kaufen';
$lang['de_DE']['SilvercartCheckoutFormStep']['OVERVIEW'] = 'Übersicht';

$lang['de_DE']['SilvercartCheckoutFormStep1']['LOGIN'] = 'Anmeldung';
$lang['de_DE']['SilvercartCheckoutFormStep1']['NEWCUSTOMER'] = 'Sie sind Neukunde?';
$lang['de_DE']['SilvercartCheckoutFormStep1']['PROCEED_WITH_REGISTRATION'] = 'Ja, ich möchte mich als Kunde registrieren, so dass ich beim erneuten Einkauf auf meine Daten zurückgreifen kann.';
$lang['de_DE']['SilvercartCheckoutFormStep1']['PROCEED_WITHOUT_REGISTRATION'] = 'Nein, ich möchte Einkaufen ohne Registrierung.';
$lang['de_DE']['SilvercartCheckoutFormStep1']['REGISTERTEXT'] = 'Möchten Sie sich registrieren, um bei einem erneuten Einkauf auf Ihre Daten, wie Rechnungs- und Lieferanschrift, zurückgreifen zu können?';
$lang['de_DE']['SilvercartCheckoutFormStep1']['TITLE'] = 'Anmeldung';
$lang['de_DE']['SilvercartCheckoutFormStep1LoginForm']['TITLE'] = 'Anmelden und Fortfahren';
$lang['de_DE']['SilvercartCheckoutFormStep1NewCustomerForm']['CONTINUE_WITH_CHECKOUT'] = 'Mit meinem Einkauf fortfahren';
$lang['de_DE']['SilvercartCheckoutFormStep1NewCustomerForm']['OPTIN_TEMP_TEXT'] = 'Nach der Aktivierung Ihres Benutzerkontos erhalten Sie einen Link, um mit Ihrem Einkauf fortzufahren.';
$lang['de_DE']['SilvercartCheckoutFormStep1NewCustomerForm']['TITLE'] = 'Weiter';
$lang['de_DE']['SilvercartCheckoutFormStep2']['EMPTYSTRING_COUNTRY'] = '--Land--';
$lang['de_DE']['SilvercartCheckoutFormStep2']['ERROR_ADDRESS_NOT_FOUND'] = 'Die von Ihnen gewählte Adresse konnte nicht gefunden werden.';
$lang['de_DE']['SilvercartCheckoutFormStep2']['TITLE'] = 'Liefer- & Rechnungsdaten';
$lang['de_DE']['SilvercartCheckoutFormStep3']['EMPTYSTRING_SHIPPINGMETHOD'] = '--Versandart wählen--';
$lang['de_DE']['SilvercartCheckoutFormStep3']['TITLE'] = 'Versandart';
$lang['de_DE']['SilvercartCheckoutFormStep4']['CHOOSE_PAYMENT_METHOD'] = 'Ich möchte per %s bezahlen';
$lang['de_DE']['SilvercartCheckoutFormStep4']['EMPTYSTRING_PAYMENTMETHOD'] = '--Zahlart wählen--';
$lang['de_DE']['SilvercartCheckoutFormStep4']['TITLE'] = 'Zahlungsinformationen';
$lang['de_DE']['SilvercartCheckoutFormStep4']['FIELDLABEL'] = 'Bitte wählen Sie, auf welche Weise Sie bezahlen wollen:';
$lang['de_DE']['SilvercartCheckoutFormStep5']['TITLE'] = 'Bestellung überprüfen';

$lang['de_DE']['SilvercartCheckoutStep']['DEFAULT_TITLE'] = 'zur Kasse';
$lang['de_DE']['SilvercartCheckoutStep']['DEFAULT_URLSEGMENT'] = 'checkout';
$lang['de_DE']['SilvercartCheckoutStep']['BACK_TO_SHOPPINGCART'] = 'Zurück zum Warenkorb';
$lang['de_DE']['SilvercartCheckoutStep']['PLURALNAME'] = 'Checkout Schritte';
$lang['de_DE']['SilvercartCheckoutStep']['SINGULARNAME'] = 'Checkout Schritt';
$lang['de_DE']['SilvercartCheckoutStep']['URL_SEGMENT'] = 'checkout';

$lang['de_DE']['SilvercartCheckoutFormStepDefaultOrderConfirmation']['TITLE'] = 'Bestellbestätigung';

$lang['de_DE']['SilvercartConfig']['ADDTOCARTMAXQUANTITY'] = 'Maximal erlaubte Anzahl eines Artikels im Warenkorb';
$lang['de_DE']['SilvercartConfig']['ADD_EXAMPLE_DATA'] = 'Beispieldaten hinzufügen';
$lang['de_DE']['SilvercartConfig']['ADD_EXAMPLE_DATA_DESCRIPTION'] = 'Die Aktion "Beispieldaten hinzufügen" wird einen Beispiel Hersteller und vier Warengruppen mit jeweils 50 Artikel anlegen.<br/><strong>ACHTUNG: Diese Aktion kann einige Minuten dauern!</strong>';
$lang['de_DE']['SilvercartConfig']['ADD_EXAMPLE_CONFIGURATION'] = 'Beispielkonfiguration anlegen';
$lang['de_DE']['SilvercartConfig']['ADD_EXAMPLE_CONFIGURATION_DESCRIPTION'] = 'Die Aktion "Beispielkonfiguration anlegen" wird SilverCart vorkonfigurieren, sodass der Checkout-Prozess komplett durchlaufen werden kann. Zu den Konfigurationsdaten gehören Zahlungsart, Frachtführer, Versandart, Versandartgebühren, Länderaktivierung und deren Zonenzuordnung.<br/><strong>ACHTUNG: Diese Aktion kann einige Minuten dauern!</strong>';
$lang['de_DE']['SilvercartConfig']['ADDED_EXAMPLE_DATA'] = 'Beispieldaten wurden hinzugefügt';
$lang['de_DE']['SilvercartConfig']['ADDED_EXAMPLE_CONFIGURATION'] = 'Beispielkonfiguration wurde angelegt';
$lang['de_DE']['SilvercartConfig']['APACHE_SOLR_PORT'] = 'Port für Anfragen an Apache Solr';
$lang['de_DE']['SilvercartConfig']['APACHE_SOLR_URL'] = 'URL für Anfragen an Apache Solr';
$lang['de_DE']['SilvercartConfig']['ALLOW_CART_WEIGHT_TO_BE_ZERO'] = 'Gewicht des Warenkorbs darf null sein.';
$lang['de_DE']['SilvercartConfig']['BASICCHECKOUT'] = 'Grundeinstellungen';
$lang['de_DE']['SilvercartConfig']['CLEAN'] = 'Optimierung';
$lang['de_DE']['SilvercartConfig']['CLEAN_DATABASE'] = 'Datenbank optimieren';
$lang['de_DE']['SilvercartConfig']['CLEAN_DATABASE_START_INDEX'] = 'Startindex';
$lang['de_DE']['SilvercartConfig']['CLEAN_DATABASE_DESCRIPTION'] = 'Die Aktion "Datenbank optimieren" durchsucht die Datenbank nach zerstörten Datensätzen und versucht diese neu zuzuordnen. Schlägt die Neuzuordnung fehl, wird der Datensatz gelöscht.<br/><strong>ACHTUNG: Diese Aktion kann einige Minuten dauern!</strong>';
$lang['de_DE']['SilvercartConfig']['CLEAN_DATABASE_INPROGRESS'] = 'Die Datenbank wird optimiert... (%s/%s) (%s%% abgeschlossen, etwa %s verbleibend)';
$lang['de_DE']['SilvercartConfig']['CLEANED_DATABASE'] = 'Die Datenbank wurde optimiert.';
$lang['de_DE']['SilvercartConfig']['CLEANED_DATABASE_REPORT'] = '<br/><hr/><br/><h3>%s</h3><strong><br/>%s Bilder wurden gelöscht.<br/>&nbsp;&nbsp;davon %s auf Grund einer fehlerhaften Artikelzuordnung<br/>&nbsp;&nbsp;davon %s auf Grund einer fehlerhaften Bilddatei<br/>&nbsp;&nbsp;davon %s auf Grund einer fehlerhaften Dateizuordnung<br/>%s Bilder wurden neu zugeordnet.</strong><br/><br/><hr/>';
$lang['de_DE']['SilvercartConfig']['DEFAULTCURRENCY'] = 'Standard Währung';
$lang['de_DE']['SilvercartConfig']['DEFAULTPRICETYPE'] = 'Standard Preistyp';
$lang['de_DE']['SilvercartConfig']['DEFAULT_IMAGE'] = 'Standard Artikelbild';
$lang['de_DE']['SilvercartConfig']['DEFAULT_MAIL_RECIPIENT'] = 'Standard E-Mail-Empfänger allgemein';
$lang['de_DE']['SilvercartConfig']['DEFAULT_MAIL_ORDER_NOTIFICATION_RECIPIENT'] = 'Standard E-Mail-Empfänger für Auftragsbenachrichtigungen';
$lang['de_DE']['SilvercartConfig']['DEFAULT_CONTACT_MESSAGE_RECIPIENT'] = 'Standard E-Mail-Empfänger für Kontaktanfragen';
$lang['de_DE']['SilvercartConfig']['DEMAND_BIRTHDAY_DATE_ON_REGISTRATION'] = 'Geburtsdatum bei Registrierung abfragen?';
$lang['de_DE']['SilvercartConfig']['DISPLAY_TYPE_OF_PRODUCT_ADMIN'] = 'Anzeigeart der Artikelverwaltung';
$lang['de_DE']['SilvercartConfig']['EMAILSENDER'] = 'E-Mail Absender';
$lang['de_DE']['SilvercartConfig']['ENABLEBUSINESSCUSTOMERS'] = 'Geschäftskunden erlauben';
$lang['de_DE']['SilvercartConfig']['ENABLEPACKSTATION'] = 'Adresseingabefelder für PACKSTATION aktivieren';
$lang['de_DE']['SilvercartConfig']['ENABLESSL'] = 'SSL verwenden';
$lang['de_DE']['SilvercartConfig']['ENABLESTOCKMANAGEMENT'] = 'Lagerbestandsverwaltung aktivieren';
$lang['de_DE']['SilvercartConfig']['EXAMPLE_DATA_ALREADY_ADDED'] = 'Beispieldaten wurden bereits hinzugefügt';
$lang['de_DE']['SilvercartConfig']['EXAMPLE_CONFIGURATION_ALREADY_ADDED'] = 'Beispielkonfiguration wurde bereits angelegt';
$lang['de_DE']['SilvercartConfig']['FREEOFSHIPPINGCOSTSFROM'] = 'Versandkostenfrei ab';
$lang['de_DE']['SilvercartConfig']['FREEOFSHIPPINGCOSTSTAB'] = 'Versandkostenfreiheit';
$lang['de_DE']['SilvercartConfig']['GENERAL'] = 'Allgemein';
$lang['de_DE']['SilvercartConfig']['GENERAL_MAIN'] = 'Hauptteil';
$lang['de_DE']['SilvercartConfig']['GENERAL_TEST_DATA'] = 'Beispieldaten';
$lang['de_DE']['SilvercartConfig']['GEONAMES_DESCRIPTION'] = '<h3>Beschreibung</h3><p>GeoNames stellt eine Datenbank mit detailierten Geodaten bereit. Sie kann benutzt werden, um aktuelle Länderinformation zu beziehen (Namen, ISO2, ISO3, usw.).<br/> Um diese nutzen zu können, müssen Sie sich einen Account bei <a href="http://www.geonames.org/" target="blank">http://www.geonames.org/</a> erstellen, die Registrierung bestätigen und den Webservice aktivieren.<br/> Aktivieren Sie dann GeoNames hier im Backend, setzen Ihren Benutzernamen ein und speichern die Änderungen.<br/> Danach wird SilverCart bei jedem dev/build die Länderdaten mit der GeoNames Datenbank synchronisieren, wahlweise mehrsprachig.</p>';
$lang['de_DE']['SilvercartConfig']['GEONAMES_ACTIVE'] = 'GeoNames aktivieren';
$lang['de_DE']['SilvercartConfig']['GEONAMES_USERNAME'] = 'GeoNames Benutzername';
$lang['de_DE']['SilvercartConfig']['GEONAMES_API'] = 'GeoNames API URL';
$lang['de_DE']['SilvercartConfig']['INTERFACES'] = 'Schnittstellen';
$lang['de_DE']['SilvercartConfig']['INTERFACES_GEONAMES'] = 'GeoNames';
$lang['de_DE']['SilvercartConfig']['LAYOUT'] = 'Layout';
$lang['de_DE']['SilvercartConfig']['PRICETYPEANONYMOUSCUSTOMERS'] = 'Preistyp für anonyme Kunden';
$lang['de_DE']['SilvercartConfig']['PRICETYPEREGULARCUSTOMERS'] = 'Preistyp für Endkunden';
$lang['de_DE']['SilvercartConfig']['PRICETYPEBUSINESSCUSTOMERS'] = 'Preistyp für Geschäftskunden';
$lang['de_DE']['SilvercartConfig']['EMAILSENDER_INFO'] = 'Der E-Mail Absender wird als Absenderadresse aller E-Mails verwendet, die von SilverCart gesendet werden.';
$lang['de_DE']['SilvercartConfig']['ERROR_TITLE'] = 'Es ist ein Fehler aufgetreten!';
$lang['de_DE']['SilvercartConfig']['ERROR_MESSAGE'] = 'Der Parameter "%s" wurde nicht konfiguriert.<br/>Bitte <a href="%sadmin/' . SilvercartConfigAdmin::$url_segment . '/">loggen Sie sich ein</a> und konfigurieren Sie den fehlenden Parameter unter "SC Konfig -> Allgemeine Konfiguration".';
$lang['de_DE']['SilvercartConfig']['ERROR_MESSAGE_NO_ACTIVATED_COUNTRY'] = 'Es wurde kein aktiviertes Land gefunden.<br/>Bitte <a href="%sadmin/' . SilvercartConfigAdmin::$url_segment . '/">loggen Sie sich ein</a> und konfigurieren Sie den fehlenden Parameter unter "SC Konfig -> Länder".';
$lang['de_DE']['SilvercartConfig']['GLOBALEMAILRECIPIENT'] = 'Globaler E-Mail Empfänger';
$lang['de_DE']['SilvercartConfig']['GLOBALEMAILRECIPIENT_INFO'] = 'Der globale E-Mail Empfänger kann optional gesetzt werden. An diese E-Mail-Adresse werden ALLE E-Mails (Bestellbestätigungen, Kontaktanfragen, etc.) gesendet. Die bei den E-Mail-Templates gesetzten Empfängeradressen bleiben davon unberührt. Diese werden nicht ersetzt, sondern nur ergänzt.';
$lang['de_DE']['SilvercartConfig']['MINIMUMORDERVALUE'] = 'Mindestbestellwert';
$lang['de_DE']['SilvercartConfig']['PLURALNAME'] = 'Allgemeine Konfigurationen';
$lang['de_DE']['SilvercartConfig']['PRICETYPE_ANONYMOUS'] = 'Preistyp für anonyme Kunden';
$lang['de_DE']['SilvercartConfig']['PRICETYPE_REGULAR'] = 'Preistyp für Endkunden';
$lang['de_DE']['SilvercartConfig']['PRICETYPE_BUSINESS'] = 'Preistyp für Geschäftskunden';
$lang['de_DE']['SilvercartConfig']['PRICETYPES_HEADLINE'] = 'Preistypen';
$lang['de_DE']['SilvercartConfig']['PRODUCT_DESCRIPTION_FIELD_FOR_CART'] = 'Feld für Artikelbeschreibung im Warenkorb';
$lang['de_DE']['SilvercartConfig']['PRODUCTSPERPAGE'] = 'Artikel pro Seite';
$lang['de_DE']['SilvercartConfig']['PRODUCTSPERPAGE_ALL'] = 'Alle anzeigen';
$lang['de_DE']['SilvercartConfig']['PRODUCTGROUPSPERPAGE'] = 'Warengruppen pro Seite';
$lang['de_DE']['SilvercartConfig']['REDIRECTTOCARTAFTERADDTOCART'] = 'Kunde zum Warenkorb umleiten nach Aktion "In den Warenkorb legen"';
$lang['de_DE']['SilvercartConfig']['SEARCH'] = 'Suche';
$lang['de_DE']['SilvercartConfig']['SERVER'] = 'Server';
$lang['de_DE']['SilvercartConfig']['SINGULARNAME'] = 'Allgemeine Konfiguration';
$lang['de_DE']['SilvercartConfig']['SHOW_CONFIG'] = 'Konfiguration anzeigen';
$lang['de_DE']['SilvercartConfig']['STOCK'] = 'Lager';
$lang['de_DE']['SilvercartConfig']['TABBED'] = 'verschachtelt';
$lang['de_DE']['SilvercartConfig']['FLAT'] = 'flach';
$lang['de_DE']['SilvercartConfig']['QUANTITY_OVERBOOKABLE'] = 'Ist der Lagerbestand generell überbuchbar?';
$lang['de_DE']['SilvercartConfig']['USE_APACHE_SOLR_SEARCH'] = 'Apache Solr Suche benutzen';
$lang['de_DE']['SilvercartConfig']['USEFREEOFSHIPPINGCOSTSFROM'] = 'Einstellungen zur Versandkostenfreiheit benutzen';
$lang['de_DE']['SilvercartConfig']['USEMINIMUMORDERVALUE'] = 'Mindestbestellwert aktivieren';
$lang['de_DE']['SilvercartConfig']['DISREGARD_MINIMUM_ORDER_VALUE'] = 'Mindestbestellwert ignorieren';
$lang['de_DE']['SilvercartConfig']['MINIMUMORDERVALUE_HEADLINE'] = 'Mindestbestellwert';
$lang['de_DE']['SilvercartConfig']['DEFAULT_LANGUAGE'] = 'Standardsprache';
$lang['de_DE']['SilvercartConfig']['USE_DEFAULT_LANGUAGE'] = 'Standardsprache benutzen, wenn die passende Sprache fehlt?';
$lang['de_DE']['SilvercartConfig']['USE_PRODUCT_DESCRIPTION_FIELD_FOR_CART'] = 'Artikelbeschreibung in Warenkorb anzeigen';
$lang['de_DE']['SilvercartConfig']['TRANSLATION'] = 'Übersetzung';
$lang['de_DE']['SilvercartConfig']['TRANSLATIONS'] = 'Übersetzungen';
$lang['de_DE']['SilvercartConfig']['OPEN_RECORD'] = 'Datensatz öffnen';
$lang['de_DE']['SilvercartConfig']['DISPLAYEDPAGINATION'] = 'Anzahl gleichzeitig angezeigter Seitenzahlen';
$lang['de_DE']['SilvercartConfig']['USE_STRICT_SEARCH_RELEVANCE'] = 'Strengere Suche verwenden. Zeigt ausschließlich exakte Treffer an.';

$lang['de_DE']['SilvercartContactFormPage']['DEFAULT_TITLE'] = 'Kontakt';
$lang['de_DE']['SilvercartContactFormPage']['DEFAULT_URLSEGMENT'] = 'kontakt';
$lang['de_DE']['SilvercartContactFormPage']['PLURALNAME'] = 'Kontaktformularseiten';
$lang['de_DE']['SilvercartContactFormPage']['REQUEST'] = 'Anfrage über das Kontaktformular';
$lang['de_DE']['SilvercartContactFormPage']['SINGULARNAME'] = 'Kontaktformularseite';
$lang['de_DE']['SilvercartContactFormPage']['TITLE'] = 'Kontakt';
$lang['de_DE']['SilvercartContactFormPage']['URL_SEGMENT'] = 'kontakt';

$lang['de_DE']['SilvercartContactFormResponsePage']['DEFAULT_TITLE'] = 'Kontaktbestätigung';
$lang['de_DE']['SilvercartContactFormResponsePage']['DEFAULT_CONTENT'] = 'Vielen Dank für Ihre Nachricht. Wir werden Ihnen in Kürze antworten.';
$lang['de_DE']['SilvercartContactFormResponsePage']['DEFAULT_URLSEGMENT'] = 'kontaktbestaetigung';
$lang['de_DE']['SilvercartContactFormResponsePage']['CONTACT_CONFIRMATION'] = 'Kontaktbestätigung';
$lang['de_DE']['SilvercartContactFormResponsePage']['CONTENT'] = 'Vielen Dank für Ihre Nachricht. Wir werden Ihnen in Kürze antworten.';
$lang['de_DE']['SilvercartContactFormResponsePage']['PLURALNAME'] = 'Kontaktformularantwortseiten';
$lang['de_DE']['SilvercartContactFormResponsePage']['SINGULARNAME'] = 'Kontaktformularantwortseite';
$lang['de_DE']['SilvercartContactFormResponsePage']['URL_SEGMENT'] = 'kontaktbestaetigung';

$lang['de_DE']['SilvercartContactMessage']['PLURALNAME'] = 'Kontaktanfragen';
$lang['de_DE']['SilvercartContactMessage']['SINGULARNAME'] = 'Kontaktanfrage';
$lang['de_DE']['SilvercartContactMessage']['MESSAGE'] = 'Nachricht';
$lang['de_DE']['SilvercartContactMessage']['TEXT'] = "<h1>Anfrage über Kontaktformular</h1>\n<h2>Hallo,</h2>\n<p>Über die Webseite wurde eine Anfrage an euch gestellt.<br />\nDer Kunde <strong>\"\$FirstName \$Surname\"</strong> hat die Email Adresse <strong>\"\$Email\"</strong> für Rückantworten angegeben.</p>\n<h2>Die Nachricht</h2>\n<p>\$Message</p>\n";

$lang['de_DE']['SilvercartContactMessageAdmin']['MENU_TITLE'] = 'Kontaktanfragen';

$lang['de_DE']['SilvercartCountry']['ACTIVE'] = 'Aktiv';
$lang['de_DE']['SilvercartCountry']['ATTRIBUTED_PAYMENTMETHOD'] = 'zugeordnete Bezahlart';
$lang['de_DE']['SilvercartCountry']['ATTRIBUTED_ZONES'] = 'zugeordnete Zonen';
$lang['de_DE']['SilvercartCountry']['CONTINENT'] = 'Kontinent';
$lang['de_DE']['SilvercartCountry']['CURRENCY'] = 'Währung';
$lang['de_DE']['SilvercartCountry']['FIPS'] = 'FIPS Code';
$lang['de_DE']['SilvercartCountry']['FREEOFSHIPPINGCOSTSFROM'] = 'Versandkostenfrei ab';
$lang['de_DE']['SilvercartCountry']['ISO2'] = 'ISO Alpha2';
$lang['de_DE']['SilvercartCountry']['ISO3'] = 'ISO Alpha3';
$lang['de_DE']['SilvercartCountry']['ISON'] = 'ISO numerisch';
$lang['de_DE']['SilvercartCountry']['PLURALNAME'] = 'Länder';
$lang['de_DE']['SilvercartCountry']['SINGULARNAME'] = 'Land';

$lang['de_DE']['SilvercartCountryLanguage']['PLURALNAME']                       = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartCountryLanguage']['SINGULARNAME']                     = _t('Silvercart.TRANSLATION');

$lang['de_DE']['SilvercartCountry']['TITLE_AD'] = 'Andorra';
$lang['de_DE']['SilvercartCountry']['TITLE_AE'] = 'Vereinigte Arabische Emirate';
$lang['de_DE']['SilvercartCountry']['TITLE_AF'] = 'Afghanistan';
$lang['de_DE']['SilvercartCountry']['TITLE_AG'] = 'Antigua und Barbuda';
$lang['de_DE']['SilvercartCountry']['TITLE_AI'] = 'Anguilla';
$lang['de_DE']['SilvercartCountry']['TITLE_AL'] = 'Albanien';
$lang['de_DE']['SilvercartCountry']['TITLE_AM'] = 'Armenien';
$lang['de_DE']['SilvercartCountry']['TITLE_AN'] = 'Niederländische Antillen';
$lang['de_DE']['SilvercartCountry']['TITLE_AO'] = 'Angola';
$lang['de_DE']['SilvercartCountry']['TITLE_AQ'] = 'Antarktis';
$lang['de_DE']['SilvercartCountry']['TITLE_AR'] = 'Argentinien';
$lang['de_DE']['SilvercartCountry']['TITLE_AS'] = 'Amerikanisch-Samoa';
$lang['de_DE']['SilvercartCountry']['TITLE_AT'] = 'Österreich';
$lang['de_DE']['SilvercartCountry']['TITLE_AU'] = 'Australien';
$lang['de_DE']['SilvercartCountry']['TITLE_AW'] = 'Aruba';
$lang['de_DE']['SilvercartCountry']['TITLE_AX'] = 'Alandinseln';
$lang['de_DE']['SilvercartCountry']['TITLE_AZ'] = 'Aserbaidschan';
$lang['de_DE']['SilvercartCountry']['TITLE_BA'] = 'Bosnien u. Herzegowina';
$lang['de_DE']['SilvercartCountry']['TITLE_BB'] = 'Barbados';
$lang['de_DE']['SilvercartCountry']['TITLE_BD'] = 'Bangladesch';
$lang['de_DE']['SilvercartCountry']['TITLE_BE'] = 'Belgien';
$lang['de_DE']['SilvercartCountry']['TITLE_BF'] = 'Burkina Faso';
$lang['de_DE']['SilvercartCountry']['TITLE_BG'] = 'Bulgarien';
$lang['de_DE']['SilvercartCountry']['TITLE_BH'] = 'Bahrain';
$lang['de_DE']['SilvercartCountry']['TITLE_BI'] = 'Burundi';
$lang['de_DE']['SilvercartCountry']['TITLE_BJ'] = 'Benin';
$lang['de_DE']['SilvercartCountry']['TITLE_BL'] = 'St. Barthélemy';
$lang['de_DE']['SilvercartCountry']['TITLE_BM'] = 'Bermuda';
$lang['de_DE']['SilvercartCountry']['TITLE_BN'] = 'Brunei Darussalam';
$lang['de_DE']['SilvercartCountry']['TITLE_BO'] = 'Bolivien';
$lang['de_DE']['SilvercartCountry']['TITLE_BQ'] = 'Bonaire, Saint Eustatius and Saba';
$lang['de_DE']['SilvercartCountry']['TITLE_BR'] = 'Brasilien';
$lang['de_DE']['SilvercartCountry']['TITLE_BS'] = 'Bahamas';
$lang['de_DE']['SilvercartCountry']['TITLE_BT'] = 'Bhutan';
$lang['de_DE']['SilvercartCountry']['TITLE_BV'] = 'Bouvetinsel';
$lang['de_DE']['SilvercartCountry']['TITLE_BW'] = 'Botsuana';
$lang['de_DE']['SilvercartCountry']['TITLE_BY'] = 'Belarus';
$lang['de_DE']['SilvercartCountry']['TITLE_BZ'] = 'Belize';
$lang['de_DE']['SilvercartCountry']['TITLE_CA'] = 'Kanada';
$lang['de_DE']['SilvercartCountry']['TITLE_CC'] = 'Kokosinseln';
$lang['de_DE']['SilvercartCountry']['TITLE_CD'] = 'Kongo-Kinshasa';
$lang['de_DE']['SilvercartCountry']['TITLE_CF'] = 'Zentralafrikanische Republik';
$lang['de_DE']['SilvercartCountry']['TITLE_CG'] = 'Kongo [Republik]';
$lang['de_DE']['SilvercartCountry']['TITLE_CH'] = 'Schweiz';
$lang['de_DE']['SilvercartCountry']['TITLE_CI'] = 'Côte d’Ivoire';
$lang['de_DE']['SilvercartCountry']['TITLE_CK'] = 'Cookinseln';
$lang['de_DE']['SilvercartCountry']['TITLE_CL'] = 'Chile';
$lang['de_DE']['SilvercartCountry']['TITLE_CM'] = 'Kamerun';
$lang['de_DE']['SilvercartCountry']['TITLE_CN'] = 'China';
$lang['de_DE']['SilvercartCountry']['TITLE_CO'] = 'Kolumbien';
$lang['de_DE']['SilvercartCountry']['TITLE_CR'] = 'Costa Rica';
$lang['de_DE']['SilvercartCountry']['TITLE_CS'] = 'Serbien und Montenegro';
$lang['de_DE']['SilvercartCountry']['TITLE_CU'] = 'Kuba';
$lang['de_DE']['SilvercartCountry']['TITLE_CV'] = 'Kap Verde';
$lang['de_DE']['SilvercartCountry']['TITLE_CW'] = 'Curacao';
$lang['de_DE']['SilvercartCountry']['TITLE_CX'] = 'Weihnachtsinsel';
$lang['de_DE']['SilvercartCountry']['TITLE_CY'] = 'Zypern';
$lang['de_DE']['SilvercartCountry']['TITLE_CZ'] = 'Tschechische Republik';
$lang['de_DE']['SilvercartCountry']['TITLE_DE'] = 'Deutschland';
$lang['de_DE']['SilvercartCountry']['TITLE_DJ'] = 'Dschibuti';
$lang['de_DE']['SilvercartCountry']['TITLE_DK'] = 'Dänemark';
$lang['de_DE']['SilvercartCountry']['TITLE_DM'] = 'Dominica';
$lang['de_DE']['SilvercartCountry']['TITLE_DO'] = 'Dominikanische Republik';
$lang['de_DE']['SilvercartCountry']['TITLE_DZ'] = 'Algerien';
$lang['de_DE']['SilvercartCountry']['TITLE_EC'] = 'Ecuador';
$lang['de_DE']['SilvercartCountry']['TITLE_EE'] = 'Estland';
$lang['de_DE']['SilvercartCountry']['TITLE_EG'] = 'Ägypten';
$lang['de_DE']['SilvercartCountry']['TITLE_EH'] = 'Westsahara';
$lang['de_DE']['SilvercartCountry']['TITLE_ER'] = 'Eritrea';
$lang['de_DE']['SilvercartCountry']['TITLE_ES'] = 'Spanien';
$lang['de_DE']['SilvercartCountry']['TITLE_ET'] = 'Äthiopien';
$lang['de_DE']['SilvercartCountry']['TITLE_FI'] = 'Finnland';
$lang['de_DE']['SilvercartCountry']['TITLE_FJ'] = 'Fidschi';
$lang['de_DE']['SilvercartCountry']['TITLE_FK'] = 'Falklandinseln';
$lang['de_DE']['SilvercartCountry']['TITLE_FM'] = 'Mikronesien';
$lang['de_DE']['SilvercartCountry']['TITLE_FO'] = 'Färöer';
$lang['de_DE']['SilvercartCountry']['TITLE_FR'] = 'Frankreich';
$lang['de_DE']['SilvercartCountry']['TITLE_GA'] = 'Gabun';
$lang['de_DE']['SilvercartCountry']['TITLE_GB'] = 'Vereinigtes Königreich';
$lang['de_DE']['SilvercartCountry']['TITLE_GD'] = 'Grenada';
$lang['de_DE']['SilvercartCountry']['TITLE_GE'] = 'Georgien';
$lang['de_DE']['SilvercartCountry']['TITLE_GF'] = 'Französisch-Guayana';
$lang['de_DE']['SilvercartCountry']['TITLE_GG'] = 'Guernsey';
$lang['de_DE']['SilvercartCountry']['TITLE_GH'] = 'Ghana';
$lang['de_DE']['SilvercartCountry']['TITLE_GI'] = 'Gibraltar';
$lang['de_DE']['SilvercartCountry']['TITLE_GL'] = 'Grönland';
$lang['de_DE']['SilvercartCountry']['TITLE_GM'] = 'Gambia';
$lang['de_DE']['SilvercartCountry']['TITLE_GN'] = 'Guinea';
$lang['de_DE']['SilvercartCountry']['TITLE_GP'] = 'Guadeloupe';
$lang['de_DE']['SilvercartCountry']['TITLE_GQ'] = 'Äquatorialguinea';
$lang['de_DE']['SilvercartCountry']['TITLE_GR'] = 'Griechenland';
$lang['de_DE']['SilvercartCountry']['TITLE_GS'] = 'Südgeorgien und die Südlichen Sandwichinseln';
$lang['de_DE']['SilvercartCountry']['TITLE_GT'] = 'Guatemala';
$lang['de_DE']['SilvercartCountry']['TITLE_GU'] = 'Guam';
$lang['de_DE']['SilvercartCountry']['TITLE_GW'] = 'Guinea-Bissau';
$lang['de_DE']['SilvercartCountry']['TITLE_GY'] = 'Guyana';
$lang['de_DE']['SilvercartCountry']['TITLE_HK'] = 'Hongkong';
$lang['de_DE']['SilvercartCountry']['TITLE_HM'] = 'Heard- und McDonald-Inseln';
$lang['de_DE']['SilvercartCountry']['TITLE_HN'] = 'Honduras';
$lang['de_DE']['SilvercartCountry']['TITLE_HR'] = 'Kroatien';
$lang['de_DE']['SilvercartCountry']['TITLE_HT'] = 'Haiti';
$lang['de_DE']['SilvercartCountry']['TITLE_HU'] = 'Ungarn';
$lang['de_DE']['SilvercartCountry']['TITLE_ID'] = 'Indonesien';
$lang['de_DE']['SilvercartCountry']['TITLE_IE'] = 'Irland';
$lang['de_DE']['SilvercartCountry']['TITLE_IL'] = 'Israel';
$lang['de_DE']['SilvercartCountry']['TITLE_IM'] = 'Isle of Man';
$lang['de_DE']['SilvercartCountry']['TITLE_IN'] = 'Indien';
$lang['de_DE']['SilvercartCountry']['TITLE_IO'] = 'Britisches Territorium im Indischen Ozean';
$lang['de_DE']['SilvercartCountry']['TITLE_IQ'] = 'Irak';
$lang['de_DE']['SilvercartCountry']['TITLE_IR'] = 'Iran';
$lang['de_DE']['SilvercartCountry']['TITLE_IS'] = 'Island';
$lang['de_DE']['SilvercartCountry']['TITLE_IT'] = 'Italien';
$lang['de_DE']['SilvercartCountry']['TITLE_JE'] = 'Jersey';
$lang['de_DE']['SilvercartCountry']['TITLE_JM'] = 'Jamaika';
$lang['de_DE']['SilvercartCountry']['TITLE_JO'] = 'Jordanien';
$lang['de_DE']['SilvercartCountry']['TITLE_JP'] = 'Japan';
$lang['de_DE']['SilvercartCountry']['TITLE_KE'] = 'Kenia';
$lang['de_DE']['SilvercartCountry']['TITLE_KG'] = 'Kirgisistan';
$lang['de_DE']['SilvercartCountry']['TITLE_KH'] = 'Kambodscha';
$lang['de_DE']['SilvercartCountry']['TITLE_KI'] = 'Kiribati';
$lang['de_DE']['SilvercartCountry']['TITLE_KM'] = 'Komoren';
$lang['de_DE']['SilvercartCountry']['TITLE_KN'] = 'St. Kitts und Nevis';
$lang['de_DE']['SilvercartCountry']['TITLE_KP'] = 'Nordkorea';
$lang['de_DE']['SilvercartCountry']['TITLE_KR'] = 'Südkorea';
$lang['de_DE']['SilvercartCountry']['TITLE_KW'] = 'Kuwait';
$lang['de_DE']['SilvercartCountry']['TITLE_KY'] = 'Kaimaninseln';
$lang['de_DE']['SilvercartCountry']['TITLE_KZ'] = 'Kasachstan';
$lang['de_DE']['SilvercartCountry']['TITLE_LA'] = 'Laos';
$lang['de_DE']['SilvercartCountry']['TITLE_LB'] = 'Libanon';
$lang['de_DE']['SilvercartCountry']['TITLE_LC'] = 'St. Lucia';
$lang['de_DE']['SilvercartCountry']['TITLE_LI'] = 'Liechtenstein';
$lang['de_DE']['SilvercartCountry']['TITLE_LK'] = 'Sri Lanka';
$lang['de_DE']['SilvercartCountry']['TITLE_LR'] = 'Liberia';
$lang['de_DE']['SilvercartCountry']['TITLE_LS'] = 'Lesotho';
$lang['de_DE']['SilvercartCountry']['TITLE_LT'] = 'Litauen';
$lang['de_DE']['SilvercartCountry']['TITLE_LU'] = 'Luxemburg';
$lang['de_DE']['SilvercartCountry']['TITLE_LV'] = 'Lettland';
$lang['de_DE']['SilvercartCountry']['TITLE_LY'] = 'Libyen';
$lang['de_DE']['SilvercartCountry']['TITLE_MA'] = 'Marokko';
$lang['de_DE']['SilvercartCountry']['TITLE_MC'] = 'Monaco';
$lang['de_DE']['SilvercartCountry']['TITLE_MD'] = 'Moldau';
$lang['de_DE']['SilvercartCountry']['TITLE_ME'] = 'Montenegro';
$lang['de_DE']['SilvercartCountry']['TITLE_MF'] = 'St. Martin';
$lang['de_DE']['SilvercartCountry']['TITLE_MG'] = 'Madagaskar';
$lang['de_DE']['SilvercartCountry']['TITLE_MH'] = 'Marshallinseln';
$lang['de_DE']['SilvercartCountry']['TITLE_MK'] = 'Mazedonien';
$lang['de_DE']['SilvercartCountry']['TITLE_ML'] = 'Mali';
$lang['de_DE']['SilvercartCountry']['TITLE_MM'] = 'Burma';
$lang['de_DE']['SilvercartCountry']['TITLE_MN'] = 'Mongolei';
$lang['de_DE']['SilvercartCountry']['TITLE_MO'] = 'Macao';
$lang['de_DE']['SilvercartCountry']['TITLE_MP'] = 'Nördliche Marianen';
$lang['de_DE']['SilvercartCountry']['TITLE_MQ'] = 'Martinique';
$lang['de_DE']['SilvercartCountry']['TITLE_MR'] = 'Mauretanien';
$lang['de_DE']['SilvercartCountry']['TITLE_MS'] = 'Montserrat';
$lang['de_DE']['SilvercartCountry']['TITLE_MT'] = 'Malta';
$lang['de_DE']['SilvercartCountry']['TITLE_MU'] = 'Mauritius';
$lang['de_DE']['SilvercartCountry']['TITLE_MV'] = 'Malediven';
$lang['de_DE']['SilvercartCountry']['TITLE_MW'] = 'Malawi';
$lang['de_DE']['SilvercartCountry']['TITLE_MX'] = 'Mexiko';
$lang['de_DE']['SilvercartCountry']['TITLE_MY'] = 'Malaysia';
$lang['de_DE']['SilvercartCountry']['TITLE_MZ'] = 'Mosambik';
$lang['de_DE']['SilvercartCountry']['TITLE_NA'] = 'Namibia';
$lang['de_DE']['SilvercartCountry']['TITLE_NC'] = 'Neukaledonien';
$lang['de_DE']['SilvercartCountry']['TITLE_NE'] = 'Niger';
$lang['de_DE']['SilvercartCountry']['TITLE_NF'] = 'Norfolkinsel';
$lang['de_DE']['SilvercartCountry']['TITLE_NG'] = 'Nigeria';
$lang['de_DE']['SilvercartCountry']['TITLE_NI'] = 'Nicaragua';
$lang['de_DE']['SilvercartCountry']['TITLE_NL'] = 'Niederlande';
$lang['de_DE']['SilvercartCountry']['TITLE_NO'] = 'Norwegen';
$lang['de_DE']['SilvercartCountry']['TITLE_NP'] = 'Nepal';
$lang['de_DE']['SilvercartCountry']['TITLE_NR'] = 'Nauru';
$lang['de_DE']['SilvercartCountry']['TITLE_NU'] = 'Niue';
$lang['de_DE']['SilvercartCountry']['TITLE_NZ'] = 'Neuseeland';
$lang['de_DE']['SilvercartCountry']['TITLE_OM'] = 'Oman';
$lang['de_DE']['SilvercartCountry']['TITLE_PA'] = 'Panama';
$lang['de_DE']['SilvercartCountry']['TITLE_PE'] = 'Peru';
$lang['de_DE']['SilvercartCountry']['TITLE_PF'] = 'Französisch-Polynesien';
$lang['de_DE']['SilvercartCountry']['TITLE_PG'] = 'Papua-Neuguinea';
$lang['de_DE']['SilvercartCountry']['TITLE_PH'] = 'Philippinen';
$lang['de_DE']['SilvercartCountry']['TITLE_PK'] = 'Pakistan';
$lang['de_DE']['SilvercartCountry']['TITLE_PL'] = 'Polen';
$lang['de_DE']['SilvercartCountry']['TITLE_PM'] = 'St. Pierre und Miquelon';
$lang['de_DE']['SilvercartCountry']['TITLE_PN'] = 'Pitcairn';
$lang['de_DE']['SilvercartCountry']['TITLE_PR'] = 'Puerto Rico';
$lang['de_DE']['SilvercartCountry']['TITLE_PS'] = 'Palästinensische Autonomiegebiete';
$lang['de_DE']['SilvercartCountry']['TITLE_PT'] = 'Portugal';
$lang['de_DE']['SilvercartCountry']['TITLE_PW'] = 'Palau';
$lang['de_DE']['SilvercartCountry']['TITLE_PY'] = 'Paraguay';
$lang['de_DE']['SilvercartCountry']['TITLE_QA'] = 'Katar';
$lang['de_DE']['SilvercartCountry']['TITLE_RE'] = 'Réunion';
$lang['de_DE']['SilvercartCountry']['TITLE_RO'] = 'Rumänien';
$lang['de_DE']['SilvercartCountry']['TITLE_RS'] = 'Serbien';
$lang['de_DE']['SilvercartCountry']['TITLE_RU'] = 'Russische Föderation';
$lang['de_DE']['SilvercartCountry']['TITLE_RW'] = 'Ruanda';
$lang['de_DE']['SilvercartCountry']['TITLE_SA'] = 'Saudi-Arabien';
$lang['de_DE']['SilvercartCountry']['TITLE_SB'] = 'Salomonen';
$lang['de_DE']['SilvercartCountry']['TITLE_SC'] = 'Seychellen';
$lang['de_DE']['SilvercartCountry']['TITLE_SD'] = 'Sudan';
$lang['de_DE']['SilvercartCountry']['TITLE_SE'] = 'Schweden';
$lang['de_DE']['SilvercartCountry']['TITLE_SG'] = 'Singapur';
$lang['de_DE']['SilvercartCountry']['TITLE_SH'] = 'St. Helena';
$lang['de_DE']['SilvercartCountry']['TITLE_SI'] = 'Slowenien';
$lang['de_DE']['SilvercartCountry']['TITLE_SJ'] = 'Svalbard und Jan Mayen';
$lang['de_DE']['SilvercartCountry']['TITLE_SK'] = 'Slowakei';
$lang['de_DE']['SilvercartCountry']['TITLE_SL'] = 'Sierra Leone';
$lang['de_DE']['SilvercartCountry']['TITLE_SM'] = 'San Marino';
$lang['de_DE']['SilvercartCountry']['TITLE_SN'] = 'Senegal';
$lang['de_DE']['SilvercartCountry']['TITLE_SO'] = 'Somalia';
$lang['de_DE']['SilvercartCountry']['TITLE_SR'] = 'Suriname';
$lang['de_DE']['SilvercartCountry']['TITLE_ST'] = 'São Tomé und Príncipe';
$lang['de_DE']['SilvercartCountry']['TITLE_SV'] = 'El Salvador';
$lang['de_DE']['SilvercartCountry']['TITLE_SX'] = 'Sint Maarten';
$lang['de_DE']['SilvercartCountry']['TITLE_SY'] = 'Syrien';
$lang['de_DE']['SilvercartCountry']['TITLE_SZ'] = 'Swasiland';
$lang['de_DE']['SilvercartCountry']['TITLE_TC'] = 'Turks- und Caicosinseln';
$lang['de_DE']['SilvercartCountry']['TITLE_TD'] = 'Tschad';
$lang['de_DE']['SilvercartCountry']['TITLE_TF'] = 'Französische Süd- und Antarktisgebiete';
$lang['de_DE']['SilvercartCountry']['TITLE_TG'] = 'Togo';
$lang['de_DE']['SilvercartCountry']['TITLE_TH'] = 'Thailand';
$lang['de_DE']['SilvercartCountry']['TITLE_TJ'] = 'Tadschikistan';
$lang['de_DE']['SilvercartCountry']['TITLE_TK'] = 'Tokelau';
$lang['de_DE']['SilvercartCountry']['TITLE_TL'] = 'Osttimor';
$lang['de_DE']['SilvercartCountry']['TITLE_TM'] = 'Turkmenistan';
$lang['de_DE']['SilvercartCountry']['TITLE_TN'] = 'Tunesien';
$lang['de_DE']['SilvercartCountry']['TITLE_TO'] = 'Tonga';
$lang['de_DE']['SilvercartCountry']['TITLE_TR'] = 'Türkei';
$lang['de_DE']['SilvercartCountry']['TITLE_TT'] = 'Trinidad und Tobago';
$lang['de_DE']['SilvercartCountry']['TITLE_TV'] = 'Tuvalu';
$lang['de_DE']['SilvercartCountry']['TITLE_TW'] = 'Taiwan';
$lang['de_DE']['SilvercartCountry']['TITLE_TZ'] = 'Tansania';
$lang['de_DE']['SilvercartCountry']['TITLE_UA'] = 'Ukraine';
$lang['de_DE']['SilvercartCountry']['TITLE_UG'] = 'Uganda';
$lang['de_DE']['SilvercartCountry']['TITLE_UM'] = 'Amerikanisch-Ozeanien';
$lang['de_DE']['SilvercartCountry']['TITLE_US'] = 'Vereinigte Staaten';
$lang['de_DE']['SilvercartCountry']['TITLE_UY'] = 'Uruguay';
$lang['de_DE']['SilvercartCountry']['TITLE_UZ'] = 'Usbekistan';
$lang['de_DE']['SilvercartCountry']['TITLE_VA'] = 'Vatikanstadt';
$lang['de_DE']['SilvercartCountry']['TITLE_VC'] = 'St. Vincent und die Grenadinen';
$lang['de_DE']['SilvercartCountry']['TITLE_VE'] = 'Venezuela';
$lang['de_DE']['SilvercartCountry']['TITLE_VG'] = 'Britische Jungferninseln';
$lang['de_DE']['SilvercartCountry']['TITLE_VI'] = 'Amerikanische Jungferninseln';
$lang['de_DE']['SilvercartCountry']['TITLE_VN'] = 'Vietnam';
$lang['de_DE']['SilvercartCountry']['TITLE_VU'] = 'Vanuatu';
$lang['de_DE']['SilvercartCountry']['TITLE_WF'] = 'Wallis und Futuna';
$lang['de_DE']['SilvercartCountry']['TITLE_WS'] = 'Samoa';
$lang['de_DE']['SilvercartCountry']['TITLE_XK'] = 'Kosovo';
$lang['de_DE']['SilvercartCountry']['TITLE_YE'] = 'Jemen';
$lang['de_DE']['SilvercartCountry']['TITLE_YT'] = 'Mayotte';
$lang['de_DE']['SilvercartCountry']['TITLE_ZA'] = 'Südafrika';
$lang['de_DE']['SilvercartCountry']['TITLE_ZM'] = 'Sambia';
$lang['de_DE']['SilvercartCountry']['TITLE_ZW'] = 'Simbabwe';

$lang['de_DE']['SilvercartCustomerAdmin']['customers'] = 'Kunden';

$lang['de_DE']['SilvercartCustomer']['ANONYMOUSCUSTOMER'] = 'Anonymer Kunde';
$lang['de_DE']['SilvercartCustomer']['BUSINESSCUSTOMER'] = 'Geschäftskunde';
$lang['de_DE']['SilvercartCustomer']['CUSTOMERNUMBER'] = 'Kundennummer';
$lang['de_DE']['SilvercartCustomer']['CUSTOMERNUMBER_SHORT'] = 'Kunden-Nr.';
$lang['de_DE']['SilvercartCustomer']['ERROR_MULTIPLE_PRICETYPES'] = 'Kundengruppen mit unterschiedlichen Preistypen sind nicht zulässig!';
$lang['de_DE']['SilvercartCustomer']['GROSS'] = 'Brutto';
$lang['de_DE']['SilvercartCustomer']['ISBUSINESSACCOUNT'] = 'Ist Geschäftskunden Konto';
$lang['de_DE']['SilvercartCustomer']['NET'] = 'Netto';
$lang['de_DE']['SilvercartCustomer']['PRICING'] = 'Preisangabe';
$lang['de_DE']['SilvercartCustomer']['SALUTATION'] = 'Anrede';
$lang['de_DE']['SilvercartCustomer']['SUBSCRIBEDTONEWSLETTER'] = 'Newsletter aboniert';
$lang['de_DE']['SilvercartCustomer']['HASACCEPTEDTERMSANDCONDITIONS'] = 'Hat die AGB akzeptiert';
$lang['de_DE']['SilvercartCustomer']['HASACCEPTEDREVOCATIONINSTRUCTION'] = 'Hat die Widerrufsbelehrung akzeptiert';
$lang['de_DE']['SilvercartCustomer']['BIRTHDAY'] = 'Geburtstag';
$lang['de_DE']['SilvercartCustomer']['TYPE'] = 'Typ';
$lang['de_DE']['SilvercartCustomer']['REGULARCUSTOMER'] = 'Endkunde';
$lang['de_DE']['SilvercartCustomer']['BASIC_DATA']      = 'Allgemein';
$lang['de_DE']['SilvercartCustomer']['ADDRESS_DATA']    = 'Adressdaten allgemein';
$lang['de_DE']['SilvercartCustomer']['INVOICE_DATA']    = 'Rechnungs-Adressdaten';
$lang['de_DE']['SilvercartCustomer']['SHIPPING_DATA']   = 'Liefer-Adressdaten';

$lang['de_DE']['SilvercartGroupDecorator']['PRICETYPE'] = 'Preistyp';
$lang['de_DE']['SilvercartGroupDecorator']['NO_PRICETYPE'] = '---';

$lang['de_DE']['SilvercartDataPage']['DEFAULT_TITLE'] = 'Meine Daten';
$lang['de_DE']['SilvercartDataPage']['DEFAULT_URLSEGMENT'] = 'meine-daten';
$lang['de_DE']['SilvercartDataPage']['PLURALNAME'] = 'Datenseiten';
$lang['de_DE']['SilvercartDataPage']['SINGULARNAME'] = 'Datenseite';
$lang['de_DE']['SilvercartDataPage']['TITLE'] = 'Meine Daten';
$lang['de_DE']['SilvercartDataPage']['URL_SEGMENT'] = 'meine-daten';

$lang['de_DE']['SilvercartDataPrivacyStatementPage']['PLURALNAME'] = 'Datenschutzseiten';
$lang['de_DE']['SilvercartDataPrivacyStatementPage']['SINGULARNAME'] = 'Datenschutzseiten';
$lang['de_DE']['SilvercartDataPrivacyStatementPage']['TITLE'] = 'Datenschutzerklärung';
$lang['de_DE']['SilvercartDataPrivacyStatementPage']['URL_SEGMENT'] = 'datenschutzerklaerung';

$lang['de_DE']['SilvercartDeeplinkPage']['SINGULARNAME'] = 'Deeplink Seite';
$lang['de_DE']['SilvercartDeeplinkPage']['PLURALNAME'] = 'Deeplink Seiten';
$lang['de_DE']['SilvercartDeeplinkPage']['DEFAULT_TITLE'] = 'Deeplink Seite';

$lang['de_DE']['SilvercartDownloadPage']['SINGULARNAME'] = 'Downloadseite';
$lang['de_DE']['SilvercartDownloadPage']['PLURALNAME'] = 'Downloadseiten';
$lang['de_DE']['SilvercartDownloadPageHolder']['SINGULARNAME'] = 'Downloadseitenübersicht';
$lang['de_DE']['SilvercartDownloadPageHolder']['PLURALNAME'] = 'Downloadseitenübersichten';

$lang['de_DE']['SilvercartEditAddressForm']['EMPTYSTRING_PLEASECHOOSE'] = '--bitte wählen--';

$lang['de_DE']['SilvercartEmailTemplates']['PLURALNAME'] = 'E-Mail Vorlagen';
$lang['de_DE']['SilvercartEmailTemplates']['SINGULARNAME'] = 'E-Mail Vorlage';

$lang['de_DE']['SilvercartFile']['DESCRIPTION'] = 'Beschreibung';
$lang['de_DE']['SilvercartFile']['FILE_ATTACHMENTS'] = 'Dateianhänge';
$lang['de_DE']['SilvercartFile']['PLURALNAME'] = 'Dateien';
$lang['de_DE']['SilvercartFile']['SINGULARNAME'] = 'Datei';
$lang['de_DE']['SilvercartFile']['TITLE'] = 'Anzeigename';
$lang['de_DE']['SilvercartFile']['TYPE'] = 'Typ';
$lang['de_DE']['SilvercartFile']['SIZE'] = 'Größe';

$lang['de_DE']['SilvercartFileLanguage']['PLURALNAME']                          = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartFileLanguage']['SINGULARNAME']                        = _t('Silvercart.TRANSLATION');

$lang['de_DE']['SilvercartFrontPage']['CONTENT'] = '<h2>Willkommen im <strong>SilverCart</strong> Webshop!</h2>';
$lang['de_DE']['SilvercartFrontPage']['DEFAULT_CONTENT'] = $lang['de_DE']['SilvercartFrontPage']['CONTENT'];
$lang['de_DE']['SilvercartFrontPage']['PLURALNAME'] = 'Frontseiten';
$lang['de_DE']['SilvercartFrontPage']['SINGULARNAME'] = 'Frontseite';

$lang['de_DE']['SilvercartGroupView']['LIST'] = 'Liste';
$lang['de_DE']['SilvercartGroupView']['TILE'] = 'Kacheln';

$lang['de_DE']['SilvercartHandlingCost']['PLURALNAME'] = 'Bearbeitungskosten';
$lang['de_DE']['SilvercartHandlingCost']['SINGULARNAME'] = 'Bearbeitungskosten';
$lang['de_DE']['SilvercartHandlingCost']['AMOUNT'] = 'Betrag';

$lang['de_DE']['SilvercartHasManyOrderField']['ATTRIBUTED_FIELDS']          = 'Zugewiesene Widgets';
$lang['de_DE']['SilvercartHasManyOrderField']['MOVE_DOWN']                  = 'Nach unten schieben';
$lang['de_DE']['SilvercartHasManyOrderField']['MOVE_UP']                    = 'Nach oben schieben';
$lang['de_DE']['SilvercartHasManyOrderField']['AVAILABLE_RELATION_OBJECTS'] = 'Verfügbare Widgets';
$lang['de_DE']['SilvercartHasManyOrderField']['EDIT']                       = 'Bearbeiten';

$lang['de_DE']['SilvercartManyManyOrderField']['ATTRIBUTED_FIELDS']          = 'Zugewiesen';
$lang['de_DE']['SilvercartManyManyOrderField']['MOVE_DOWN']                  = 'Nach unten schieben';
$lang['de_DE']['SilvercartManyManyOrderField']['MOVE_UP']                    = 'Nach oben schieben';
$lang['de_DE']['SilvercartManyManyOrderField']['AVAILABLE_RELATION_OBJECTS'] = 'Verfügbar';
$lang['de_DE']['SilvercartManyManyOrderField']['EDIT']                       = 'Bearbeiten';

$lang['de_DE']['SilvercartImage']['CONTENT'] = 'Text Inhalt';
$lang['de_DE']['SilvercartImage']['DESCRIPTION'] = 'Beschreibung (z.B. für Slidorion Textfeld)';
$lang['de_DE']['SilvercartImage']['PLURALNAME'] = 'Bilder';
$lang['de_DE']['SilvercartImage']['SINGULARNAME'] = 'Bild';
$lang['de_DE']['SilvercartImage']['THUMBNAIL'] = 'Vorschau';
$lang['de_DE']['SilvercartImage']['TITLE'] = 'Anzeigename';

$lang['de_DE']['SilvercartImageLanguage']['PLURALNAME']                         = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartImageLanguage']['SINGULARNAME']                       = _t('Silvercart.TRANSLATION');

$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['COMBINED_STRING']                       = 'Zeichenkette mit Trennern';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['COMBINED_STRING_KEY']                   = 'Name der Variable, in der die Zeichenkette gespeichert ist';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['COMBINED_STRING_ENTITY_SEPARATOR']      = 'Entitätentrennzeichen in der Zeichenkette';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['COMBINED_STRING_QUANTITY_SEPARATOR']    = 'Mengentrennzeichen in der Zeichenkette';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['KEY_VALUE']                             = 'Schlüssel-Wert Paare';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['KEY_VALUE_PRODUCT_IDENTIFIER']          = 'Name der Artikel-Variable';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['KEY_VALUE_QUANTITY_IDENTIFIER']         = 'Name der Mengen-Variable';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['PLURALNAME']                            = 'Externe Warenkorbbefüllung';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['PRODUCT_MATCHING_FIELD']                = 'Artikel-Bezugsfeld';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['REFERER_IDENTIFIER']                    = 'Kurzname des externen Partners';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['SHARED_SECRET']                         = 'Shared secret';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['SHARED_SECRET_ACTIVATION']                         = 'Shared secret aktivieren';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['SHARED_SECRET_IDENTIFIER']              = 'Name der Shared secret Variable';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['SINGULARNAME']                          = 'Externe Warenkorbbefüllung';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['TITLE']                                 = 'Bezeichnung';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['TRANSFER_METHOD']                       = 'Übertragungsmethode';
$lang['de_DE']['SilvercartInboundShoppingCartTransfer']['USE_SHARED_SECRET']                     = 'Shared secret benutzen';

$lang['de_DE']['SilvercartInboundShoppingCartTransferPage']['ERROR_COMBINED_STRING_KEY_NOT_FOUND']              = 'Parameter wurden nicht gesendet';
$lang['de_DE']['SilvercartInboundShoppingCartTransferPage']['ERROR_KEY_VALUE_PRODUCT_IDENTIFIER_NOT_FOUND']     = 'Parameter wurden nicht gesendet (Artikelbezeichner fehlt)';
$lang['de_DE']['SilvercartInboundShoppingCartTransferPage']['ERROR_KEY_VALUE_QUANTITY_IDENTIFIER_NOT_FOUND']    = 'Parameter wurden nicht gesendet (Mengenbezeichner fehlt)';
$lang['de_DE']['SilvercartInboundShoppingCartTransferPage']['ERROR_REFERER_NOT_FOUND']                          = 'Externer Aufrufer ist nicht gültig';
$lang['de_DE']['SilvercartInboundShoppingCartTransferPage']['ERROR_SHARED_SECRET_INVALID']                      = 'Keine Berechtigung';

$lang['de_DE']['SilvercartInvoiceAddress']['PLURALNAME'] = 'Rechnungsadressen';
$lang['de_DE']['SilvercartInvoiceAddress']['SINGULARNAME'] = 'Rechnungsadresse';

$lang['de_DE']['SilvercartManufacturer']['DESCRIPTION'] = 'Beschreibung';
$lang['de_DE']['SilvercartManufacturer']['PLURALNAME'] = 'Hersteller';
$lang['de_DE']['SilvercartManufacturer']['SINGULARNAME'] = 'Hersteller';

$lang['de_DE']['SilvercartManufacturerLanguage']['PLURALNAME']   = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartManufacturerLanguage']['SINGULARNAME'] = _t('Silvercart.TRANSLATION');

$lang['de_DE']['SilvercartMetaNavigationHolder']['DEFAULT_TITLE'] = 'Metanavigationsübersicht';
$lang['de_DE']['SilvercartMetaNavigationHolder']['DEFAULT_URLSEGMENT'] = 'metanavigation';
$lang['de_DE']['SilvercartMetaNavigationHolder']['PLURALNAME'] = 'Metanavigationsübersichten';
$lang['de_DE']['SilvercartMetaNavigationHolder']['SINGULARNAME'] = 'Metanavigationsübersicht';
$lang['de_DE']['SilvercartMetaNavigationHolder']['URL_SEGMENT'] = 'metanavigation';

$lang['de_DE']['SilvercartMetaNavigationPage']['PLURALNAME'] = 'Meta-Informations-Seiten';
$lang['de_DE']['SilvercartMetaNavigationPage']['SINGULARNAME'] = 'Meta-Informations-Seite';

$lang['de_DE']['SilvercartSiteMapPage']['PLURALNAME'] = 'SiteMaps';
$lang['de_DE']['SilvercartSiteMapPage']['SINGULARNAME'] = 'SiteMap';

$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['CMS_ADVANCEDTABNAME'] = 'Erweiterte Einstellungen';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['CMS_BASICTABNAME']    = 'Grundeinstellungen';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['CMSTITLE']            = 'Slidorion Akkordeon Slider';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['DESCRIPTION']         = 'Slidorion - eine Kombination aus Slider und Akkordeon';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['PLURALNAME']          = 'Slidorion Akkordeon';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['TITLE']               = 'Slidorion Akkordeon';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['SINGULARNAME']        = 'Slidorion Akkordeon';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['SCPRODUCTGROUPPAGES'] = 'Anzuzeigende Slides';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['FRONT_TITLE']         = 'Überschrift';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['FRONT_CONTENT']       = 'Inhalt';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['WIDGET_HEIGHT']       = 'Höhe des Widgets (in Pixel)';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['SPEED']               = 'Animationsgeschwindigkeit';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['INTERVAL']            = 'Dauer eines Übergangs';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['HOVERPAUSE']          = 'Pausieren, wenn sich der Mauszeiger über dem Widget befindet';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['AUTOPLAY']            = 'Automatisch starten';
$lang['de_DE']['SilvercartSlidorionProductGroupWidget']['EFFECT']              = 'Effekt';

$lang['de_DE']['SilvercartMailForgotPassword']['TITLE']                         = 'Passwort zurücksetzen';
$lang['de_DE']['SilvercartMailForgotPassword']['VISIT_TEXT']                    = 'Bitte besuchen Sie den folgenden Link, um Ihr Passwort zu ändern:';
$lang['de_DE']['SilvercartMailForgotPassword']['PASSWORT_RESET_LINK_HINT']      = 'Falls Sie den Link nicht anklicken können, kopieren Sie diesen bitte in Ihre Zwischenablage und fügen Sie ihn in die Adressleiste Ihres Browsers ein.';
$lang['de_DE']['SilvercartMailForgotPassword']['NO_CHANGE']                     = 'Wenn Sie Ihr Passwort nicht ändern möchten, ignorieren Sie diese E-Mail bitte.';

$lang['de_DE']['SilvercartMyAccountHolder']['ALREADY_HAVE_AN_ACCOUNT']          = 'Sie haben schon ein Konto?';
$lang['de_DE']['SilvercartMyAccountHolder']['DEFAULT_TITLE']                    = 'Mein Konto';
$lang['de_DE']['SilvercartMyAccountHolder']['DEFAULT_URLSEGMENT']               = 'mein-konto';
$lang['de_DE']['SilvercartMyAccountHolder']['GOTO_REGISTRATION']                = 'Zum Registrierungsformular';
$lang['de_DE']['SilvercartMyAccountHolder']['PLURALNAME']                       = 'Accountübersichten';
$lang['de_DE']['SilvercartMyAccountHolder']['REGISTER_ADVANTAGES_TEXT']         = 'Wenn Sie sich registrieren, können Sie bei einem Einkauf auf Ihre Daten, wie Rechnungs- und Lieferanschrift, zurückgreifen.';
$lang['de_DE']['SilvercartMyAccountHolder']['SINGULARNAME']                     = 'Accountübersicht';
$lang['de_DE']['SilvercartMyAccountHolder']['TITLE']                            = 'Mein Konto';
$lang['de_DE']['SilvercartMyAccountHolder']['URL_SEGMENT']                      = 'mein-konto';
$lang['de_DE']['SilvercartMyAccountHolder']['WANTTOREGISTER']                   = 'Wollen Sie sich registrieren?';
$lang['de_DE']['SilvercartMyAccountHolder']['YOUR_CUSTOMERNUMBER']              = 'Ihre Kundennummer';
$lang['de_DE']['SilvercartMyAccountHolder']['YOUR_CURRENT_ADDRESSES']           = 'Ihre aktuelle Rechnungs- und Lieferadresse';
$lang['de_DE']['SilvercartMyAccountHolder']['YOUR_MOST_CURRENT_ORDERS']         = 'Ihre aktuellsten Bestellungen';
$lang['de_DE']['SilvercartMyAccountHolder']['YOUR_PERSONAL_DATA']               = 'Ihre persönlichen Daten';

$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['DEFAULT_CONFIRMATIONFAILUREMESSAGE']   = '<p>Ihre Newsletteranmeldung konnte nicht abgeschlossen werden.</p>';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['DEFAULT_CONFIRMATIONSUCCESSMESSAGE']   = '<p>Ihre Newsletteranmeldung war erfolgreich!</p><p>Wir wünschen Ihnen viel Spaß und Erfolg mit unseren Angeboten.</p>';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['DEFAULT_ALREADYCONFIRMEDMESSAGE']      = '<p>Sie hatten die Newsletteranmeldung schon zuvor abgeschlossen.</p>';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['DEFAULT_CONTENT']                      = '';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['DEFAULT_URLSEGMENT']                   = 'newsletter-opt-in-confirmation';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['DEFAULT_TITLE']                        = 'Newsletteranmeldung abschließen';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['ALREADY_CONFIRMED_MESSAGE_TEXT']   = 'Nachricht: Benutzer hat Opt-In schon vollzogen';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['FAILURE_MESSAGE_TEXT']             = 'Fehlermeldung';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['PLURALNAME']                       = 'Newsletter Opt-In Seite';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['SINGULARNAME']                     = 'Newsletter Opt-In Seiten';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['SUCCESS_MESSAGE_TEXT']             = 'Erfolgsmeldung';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['CONFIRMATIONFAILUREMESSAGE']       = '<p>Ihre Newsletteranmeldung konnte nicht abgeschlossen werden.</p>';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['CONFIRMATIONSUCCESSMESSAGE']       = '<p>Ihre Newsletteranmeldung war erfolgreich!</p><p>Wir wünschen Ihnen viel Spaß und Erfolg mit unseren Angeboten.</p>';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['ALREADYCONFIRMEDMESSAGE']          = '<p>Sie hatten die Newsletteranmeldung schon zuvor abgeschlossen.</p>';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['CONTENT']                          = '';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['URL_SEGMENT']                      = 'newsletter-opt-in-confirmation';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['TITLE']                            = 'Newsletteranmeldung abschließen';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['TITLE_THANKS']                     = 'Newsletteranmeldung erfolgreich abgeschlossen';
$lang['de_DE']['SilvercartNewsletterOptInConfirmationPage']['EMAIL_CONFIRMATION_TEXT']          = '<h1>Newsletter-Anmeldung abschließen</h1><p>Bitte klicken Sie auf den Aktivierungslink oder kopieren Sie den Link in den Browser.</p><p><a href="$ConfirmationLink">Anmeldung bestätigen</a></p><p>Sollten Sie den Newsletter nicht angefordert haben, ignorieren Sie diese Mail einfach.</p><p>Ihr Webshop Team</p>';

$lang['de_DE']['SilvercartOrder']['AMOUNTGROSSTOTAL'] = 'Gesamtbetrag brutto';
$lang['de_DE']['SilvercartOrder']['AMOUNTTOTAL'] = 'Gesamtbetrag';
$lang['de_DE']['SilvercartOrder']['BASICDATA'] = 'Grunddaten';
$lang['de_DE']['SilvercartOrder']['BATCH_CHANGEORDERSTATUS']                    = 'Bestellstatus ändern auf...';
$lang['de_DE']['SilvercartOrder']['BATCH_PRINTORDERS']                          = 'Bestellungen drucken (HTML)';
$lang['de_DE']['SilvercartOrder']['BATCH_MARKASSEEN']                           = 'Als gesehen markieren';
$lang['de_DE']['SilvercartOrder']['BATCH_MARKASNOTSEEN']                        = 'Als nicht gesehen markieren';
$lang['de_DE']['SilvercartOrder']['CUSTOMER'] = 'Kunde';
$lang['de_DE']['SilvercartOrder']['CUSTOMERDATA'] = 'Kundendaten';
$lang['de_DE']['SilvercartOrder']['CUSTOMERSEMAIL'] = 'Emailadresse des Kunden';
$lang['de_DE']['SilvercartOrder']['HANDLINGCOSTPAYMENT'] = 'Gebühren der Bezahlart';
$lang['de_DE']['SilvercartOrder']['HANDLINGCOSTSHIPMENT'] = 'Gebühren der Versandart';
$lang['de_DE']['SilvercartOrder']['HASACCEPTEDTERMSANDCONDITIONS'] = 'Hat die AGB akzeptiert';
$lang['de_DE']['SilvercartOrder']['HASACCEPTEDREVOCATIONINSTRUCTION'] = 'Hat die Widerrufsbelehrung akzeptiert';
$lang['de_DE']['SilvercartOrder']['INCLUDED_SHIPPINGRATE'] = 'Enthaltene Versandkosten';
$lang['de_DE']['SilvercartOrder']['INVOICENUMBER'] = 'Rechnungsnummer';
$lang['de_DE']['SilvercartOrder']['INVOICENUMBER_SHORT'] = 'Rechnungs-Nr.';
$lang['de_DE']['SilvercartOrder']['IS_SEEN'] = 'Gesehen';
$lang['de_DE']['SilvercartOrder']['MISCDATA'] = 'Sonstiges';
$lang['de_DE']['SilvercartOrder']['NOTE'] = 'Bemerkung';
$lang['de_DE']['SilvercartOrder']['ORDER_ID'] = 'Bestellnummer';
$lang['de_DE']['SilvercartOrder']['ORDERNUMBER'] = 'Bestellnummer';
$lang['de_DE']['SilvercartOrder']['ORDERNUMBERSHORT'] = '-nummer';
$lang['de_DE']['SilvercartOrder']['ORDERPOSITIONDATA'] = 'Positionsdaten';
$lang['de_DE']['SilvercartOrder']['ORDERPOSITIONISLIMIT'] = 'Bestellung darf nur angegebene Position enthalten';
$lang['de_DE']['SilvercartOrder']['ORDERPOSITIONQUANTITY'] = 'Positionsmenge';
$lang['de_DE']['SilvercartOrder']['ORDER_VALUE'] = 'Gesamtbetrag';
$lang['de_DE']['SilvercartOrder']['PAYMENTMETHODTITLE'] = 'Bezahlart';
$lang['de_DE']['SilvercartOrder']['PLURALNAME'] = 'Bestellungen';
$lang['de_DE']['SilvercartOrder']['PRICETYPE'] = 'Preis-Anzeige-Typ';
$lang['de_DE']['SilvercartOrder']['PRINT'] = 'Bestellung drucken';
$lang['de_DE']['SilvercartOrder']['PRINT_PREVIEW'] = 'Druckvorschau';
$lang['de_DE']['SilvercartOrder']['SEARCHRESULTSLIMIT'] = 'Limit';
$lang['de_DE']['SilvercartOrder']['SHIPPINGRATE'] = 'Versandkosten';
$lang['de_DE']['SilvercartOrder']['SINGULARNAME'] = 'Bestellung';
$lang['de_DE']['SilvercartOrder']['SILVERCART_ORDER_DELETE'] = 'Bestellung löschen';
$lang['de_DE']['SilvercartOrder']['SILVERCART_ORDER_EDIT'] = 'Bestellung bearbeiten';
$lang['de_DE']['SilvercartOrder']['SILVERCART_ORDER_VIEW'] = 'Bestellung anschauen';
$lang['de_DE']['SilvercartOrder']['STATUS'] = 'Bestellstatus';
$lang['de_DE']['SilvercartOrder']['TAXAMOUNTPAYMENT'] = 'Steuer der Bezahlart';
$lang['de_DE']['SilvercartOrder']['TAXAMOUNTSHIPMENT'] = 'Steuer der Versandart';
$lang['de_DE']['SilvercartOrder']['TAXRATEPAYMENT'] = 'Steuersatz der Bezahlart';
$lang['de_DE']['SilvercartOrder']['TAXRATESHIPMENT'] = 'Steuersatz der Versandart';
$lang['de_DE']['SilvercartOrder']['WEIGHTTOTAL'] = 'Gesamtgewicht';
$lang['de_DE']['SilvercartOrder']['YOUR_REMARK'] = 'Ihre Bemerkung';

$lang['de_DE']['SilvercartOrderAddress']['PLURALNAME'] = 'Bestelladressen';
$lang['de_DE']['SilvercartOrderAddress']['SINGULARNAME'] = 'Bestelladresse';

$lang['de_DE']['SilvercartOrderConfirmationPage']['DEFAULT_TITLE'] = 'Bestellbestätigungsseite';
$lang['de_DE']['SilvercartOrderConfirmationPage']['DEFAULT_URLSEGMENT'] = 'bestellbestaetigung';
$lang['de_DE']['SilvercartOrderConfirmationPage']['PLURALNAME'] = 'Bestellbestätigungsseiten';
$lang['de_DE']['SilvercartOrderConfirmationPage']['SINGULARNAME'] = 'Bestellbestätigungsseite';
$lang['de_DE']['SilvercartOrderConfirmationPage']['URL_SEGMENT'] = 'bestellbestaetigung';
$lang['de_DE']['SilvercartOrderConfirmationPage']['ORDERCONFIRMATION'] = 'Bestellbestätigung';
$lang['de_DE']['SilvercartOrderConfirmationPage']['ORDERNOTIFICATION'] = 'Bestellbenachrichtigung';

$lang['de_DE']['SilvercartOrderDetailPage']['DEFAULT_TITLE'] = 'Bestelldetails';
$lang['de_DE']['SilvercartOrderDetailPage']['DEFAULT_URLSEGMENT'] = 'bestelldetails';
$lang['de_DE']['SilvercartOrderDetailPage']['PLURALNAME'] = 'Bestelldetailsseiten';
$lang['de_DE']['SilvercartOrderDetailPage']['SINGULARNAME'] = 'Bestelldetailsseite';
$lang['de_DE']['SilvercartOrderDetailPage']['TITLE'] = 'Bestelldetails';
$lang['de_DE']['SilvercartOrderDetailPage']['URL_SEGMENT'] = 'bestelldetails';

$lang['de_DE']['SilvercartOrderHolder']['DEFAULT_TITLE'] = 'Meine Bestellungen';
$lang['de_DE']['SilvercartOrderHolder']['DEFAULT_URLSEGMENT'] = 'meine-bestellungen';
$lang['de_DE']['SilvercartOrderHolder']['PLURALNAME'] = 'Bestellübersichten';
$lang['de_DE']['SilvercartOrderHolder']['SINGULARNAME'] = 'Bestellübersicht';
$lang['de_DE']['SilvercartOrderHolder']['TITLE'] = 'Meine Bestellungen';
$lang['de_DE']['SilvercartOrderHolder']['URL_SEGMENT'] = 'meine-bestellungen';

$lang['de_DE']['SilvercartOrderInvoiceAddress']['PLURALNAME'] = 'Rechnungsadressen der Bestellungen';
$lang['de_DE']['SilvercartOrderInvoiceAddress']['SINGULARNAME'] = 'Rechnungsadresse der Bestellung';

$lang['de_DE']['SilvercartOrderLog']['SINGULARNAME']                            = 'Bestellungs-Log';
$lang['de_DE']['SilvercartOrderLog']['PLURALNAME']                              = 'Bestellungs-Log';
$lang['de_DE']['SilvercartOrderLog']['CONTEXT']                                 = 'Kontext';
$lang['de_DE']['SilvercartOrderLog']['CREATED']                                 = 'Datum/Uhrzeit';
$lang['de_DE']['SilvercartOrderLog']['MESSAGE']                                 = 'Aktion';
$lang['de_DE']['SilvercartOrderLog']['MESSAGE_CHANGED']                         = 'Verändert: %s -> %s';
$lang['de_DE']['SilvercartOrderLog']['MESSAGE_CREATED']                         = 'Erstellt: %s wurde erstellt';
$lang['de_DE']['SilvercartOrderLog']['MESSAGE_MARKEDASSEEN']                    = 'Als gesehen markiert';

$lang['de_DE']['SilvercartOrderPosition']['PLURALNAME']                         = 'Bestellpositionen';
$lang['de_DE']['SilvercartOrderPosition']['SINGULARNAME']                       = 'Bestellposition';
$lang['de_DE']['SilvercartOrderPosition']['SHORT']                              = 'Pos.';
$lang['de_DE']['SilvercartOrderPosition']['SILVERCARTPRODUCT']                  = 'Artikel';
$lang['de_DE']['SilvercartOrderPosition']['PRICE']                              = 'Preis';
$lang['de_DE']['SilvercartOrderPosition']['PRICETOTAL']                         = 'Preis gesamt';
$lang['de_DE']['SilvercartOrderPosition']['ISCHARGEORDISCOUNT']                 = 'Ist Auf-/Abschlag';
$lang['de_DE']['SilvercartOrderPosition']['TAX']                                = 'MwSt.';
$lang['de_DE']['SilvercartOrderPosition']['TAXTOTAL']                           = 'MwSt. gesamt';
$lang['de_DE']['SilvercartOrderPosition']['TAXRATE']                            = 'Steuersatz';
$lang['de_DE']['SilvercartOrderPosition']['PRODUCTDESCRIPTION']                 = 'Beschreibung';
$lang['de_DE']['SilvercartOrderPosition']['QUANTITY']                           = 'Menge';
$lang['de_DE']['SilvercartOrderPosition']['TITLE']                              = 'Name';
$lang['de_DE']['SilvercartOrderPosition']['PRODUCTNUMBER']                      = 'Artikelnr.';
$lang['de_DE']['SilvercartOrderPosition']['CHARGEORDISCOUNTMODIFICATIONIMPACT'] = 'Auf-/Abschlags-Typ';

$lang['de_DE']['SilvercartOrderSearchForm']['PLEASECHOOSE'] = 'Bitte wählen';

$lang['de_DE']['SilvercartOrderShippingAddress']['PLURALNAME'] = 'Lieferadressen der Bestellung';
$lang['de_DE']['SilvercartOrderShippingAddress']['SINGULARNAME'] = 'Lieferadresse der Bestellung';

$lang['de_DE']['SilvercartOrderStatus']['ATTRIBUTED_SHOPEMAILS_LABEL_DESC'] = 'Die folgenden markierten Emails werden verschickt, wenn dieser Bestellstatus für eine Bestellung gesetzt wird:';
$lang['de_DE']['SilvercartOrderStatus']['ATTRIBUTED_SHOPEMAILS_LABEL_TITLE'] = 'Zugeordnete Emails';
$lang['de_DE']['SilvercartOrderStatus']['CODE'] = 'Code';
$lang['de_DE']['SilvercartOrderStatus']['INWORK'] = 'In Bearbeitung';
$lang['de_DE']['SilvercartOrderStatus']['PAYED'] = 'Bezahlt';
$lang['de_DE']['SilvercartOrderStatus']['PLURALNAME'] = 'Bestellstatus';
$lang['de_DE']['SilvercartOrderStatus']['SHIPPED'] = 'Bestellung versendet';
$lang['de_DE']['SilvercartOrderStatus']['SINGULARNAME'] = 'Bestellstatus';

$lang['de_DE']['SilvercartOrderStatusLanguage']['PLURALNAME']                   = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartOrderStatusLanguage']['SINGULARNAME']                 = _t('Silvercart.TRANSLATION');
$lang['de_DE']['SilvercartOrderStatusLanguage']['TITLE']                        = 'Bezeichnung';

$lang['de_DE']['SilvercartPage']['ABOUT_US'] = 'über uns';
$lang['de_DE']['SilvercartPage']['ABOUT_US_URL_SEGMENT'] = 'ueber-uns';
$lang['de_DE']['SilvercartPage']['ACCESS_CREDENTIALS_CALL'] = 'Bitte geben Sie Ihre Zugangsdaten ein:';
$lang['de_DE']['SilvercartPage']['ADDRESS'] = 'Adresse';
$lang['de_DE']['SilvercartPage']['ADDRESSINFORMATION'] = 'Adressinformationen';
$lang['de_DE']['SilvercartPage']['ADDRESS_DATA'] = 'Adressdaten';
$lang['de_DE']['SilvercartPage']['ADMIN_AREA'] = 'Admin Zugang';
$lang['de_DE']['SilvercartPage']['ALREADY_REGISTERED'] = 'Hallo %s, Sie haben sich schon registriert.';
$lang['de_DE']['SilvercartPage']['API_CREATE'] = 'kann über die API Objekte erstellen';
$lang['de_DE']['SilvercartPage']['API_DELETE'] = 'kann über die API Objekte löschen';
$lang['de_DE']['SilvercartPage']['API_EDIT'] = 'kann über die API Objete verändern';
$lang['de_DE']['SilvercartPage']['API_VIEW'] = 'kann Objekte über die API lesen';
$lang['de_DE']['SilvercartPage']['APRIL'] = 'April';
$lang['de_DE']['SilvercartPage']['BACK'] = 'Zurück';
$lang['de_DE']['SilvercartPage']['BACK_TO'] = 'Zurück zu &quot;%s&quot;';
$lang['de_DE']['SilvercartPage']['BACK_TO_DEFAULT'] = 'vorheriger Seite';
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
$lang['de_DE']['SilvercartPage']['CONTINUESHOPPING'] = 'Weiter einkaufen';
$lang['de_DE']['SilvercartPage']['CREDENTIALS_WRONG'] = 'Ihre Zugangsdaten sind falsch.';
$lang['de_DE']['SilvercartPage']['DAY'] = 'Tag';
$lang['de_DE']['SilvercartPage']['DECEMBER'] = 'Dezember';
$lang['de_DE']['SilvercartPage']['DETAILS'] = 'Details';
$lang['de_DE']['SilvercartPage']['DETAILS_FOR'] = 'Details zu %s';
$lang['de_DE']['SilvercartPage']['DIDNOT_RETURN_RESULTS'] = 'hat in unserem Shop keine Ergebnisse geliefert.';
$lang['de_DE']['SilvercartPage']['DO_NOT_EDIT'] = 'Bitte nicht ändern!';
$lang['de_DE']['SilvercartPage']['EMAIL_ADDRESS'] = 'E-Mail-Adresse';
$lang['de_DE']['SilvercartPage']['EMAIL_ALREADY_REGISTERED'] = 'Ein Nutzer hat sich bereits mit dieser E-Mail-Adresse registriert.';
$lang['de_DE']['SilvercartPage']['EMAIL_NOT_FOUND'] = 'Diese E-Mail-Adresse konnte nicht gefunden werden.';
$lang['de_DE']['SilvercartPage']['EMPTY_CART'] = 'leeren';
$lang['de_DE']['SilvercartPage']['ERROR_LISTING'] = 'Folgende Fehler sind aufgetreten:';
$lang['de_DE']['SilvercartPage']['ERROR_OCCURED'] = 'Es ist ein Fehler aufgetreten.';
$lang['de_DE']['SilvercartPage']['FEBRUARY'] = 'Februar';
$lang['de_DE']['SilvercartPage']['FIND'] = 'Finden:';
$lang['de_DE']['SilvercartPage']['FORWARD'] = 'Weiter';
$lang['de_DE']['SilvercartPage']['GOTO'] = 'gehe zur %s Seite';
$lang['de_DE']['SilvercartPage']['GOTO_CART'] = 'zum Warenkorb';
$lang['de_DE']['SilvercartPage']['GOTO_CART_SHORT'] = 'Warenkorb';
$lang['de_DE']['SilvercartPage']['GOTO_CONTACT_LINK'] = 'Zur Kontakt Seite';
$lang['de_DE']['SilvercartPage']['GOTO_PAGE'] = 'Gehe zu Seite %s ';
$lang['de_DE']['SilvercartPage']['HEADERPICTURE'] = 'Header Bild';
$lang['de_DE']['SilvercartPage']['INCLUDED_VAT'] = 'enthaltene MwSt.';
$lang['de_DE']['SilvercartPage']['ADDITIONAL_VAT'] = 'Zuzüglich MwSt.';
$lang['de_DE']['SilvercartPage']['I_ACCEPT'] = 'Ich akzeptiere die';
$lang['de_DE']['SilvercartPage']['I_HAVE_READ'] = 'Ich habe die ';
$lang['de_DE']['SilvercartPage']['ISACTIVE'] = 'Aktiv';
$lang['de_DE']['SilvercartPage']['JANUARY'] = 'Januar';
$lang['de_DE']['SilvercartPage']['JUNE'] = 'Juni';
$lang['de_DE']['SilvercartPage']['JULY'] = 'Juli';
$lang['de_DE']['SilvercartPage']['LOGIN'] = 'Anmelden';
$lang['de_DE']['SilvercartPage']['LOGOUT'] = 'Abmelden';
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
$lang['de_DE']['SilvercartPage']['NOT_FOUND'] = 'Die Seite "%s" konnte nicht gefunden werden.';
$lang['de_DE']['SilvercartPage']['OCTOBER'] = 'Oktober';
$lang['de_DE']['SilvercartPage']['ORDERED_PRODUCTS'] = 'Bestellte Artikel';
$lang['de_DE']['SilvercartPage']['ORDER_COMPLETED'] = 'Ihre Bestellung ist abgeschlossen.';
$lang['de_DE']['SilvercartPage']['ORDER_DATE'] = 'Bestelldatum';
$lang['de_DE']['SilvercartPage']['ORDERS_EMAIL_INFORMATION_TEXT'] = 'Sie werden in Kürze eine Bestellbestätigung per E-Mail erhalten. Bitte prüfen Sie Ihren Posteingang.';
$lang['de_DE']['SilvercartPage']['ORDER_THANKS'] = 'Vielen Dank für Ihre Bestellung';
$lang['de_DE']['SilvercartPage']['PASSWORD'] = 'Passwort';
$lang['de_DE']['SilvercartPage']['PASSWORD_CASE_EMPTY'] = 'Wenn Sie dieses Feld leer lassen, wird Ihr Passwort nicht geändert.';
$lang['de_DE']['SilvercartPage']['PASSWORD_CHECK'] = 'Passwortkontrolle';
$lang['de_DE']['SilvercartPage']['PASSWORD_WRONG'] = 'Dieses Passwort ist falsch.';
$lang['de_DE']['SilvercartPage']['PAYMENT_NOT_WORKING'] = 'Das gewählte Zahlungsmodul funktioniert nicht.';
$lang['de_DE']['SilvercartPage']['PLUS_SHIPPING'] = 'zzgl. Versand';
$lang['de_DE']['SilvercartPage']['PREV'] = 'Zurück';
$lang['de_DE']['SilvercartPage']['REGISTER'] = 'Registrieren';
$lang['de_DE']['SilvercartPage']['REMARKS'] = 'Bemerkungen';
$lang['de_DE']['SilvercartPage']['REMOVE_FROM_CART'] = 'entfernen';
$lang['de_DE']['SilvercartPage']['RETURNTOPRODUCTGROUP'] = 'Zurück zu "%s"';
$lang['de_DE']['SilvercartPage']['REVOCATION'] = 'Widerrufsbelehrung';
$lang['de_DE']['SilvercartPage']['REVOCATIONREAD'] = 'Widerrufsbelehrung gelesen';
$lang['de_DE']['SilvercartPage']['SAVE'] = 'Speichern';
$lang['de_DE']['SilvercartPage']['SEARCH_RESULTS'] = 'Treffer';
$lang['de_DE']['SilvercartPage']['SEPTEMBER'] = 'September';
$lang['de_DE']['SilvercartPage']['SESSION_EXPIRED'] = 'Ihre Sitzung ist abgelaufen.';
$lang['de_DE']['SilvercartPage']['SHIPPING_ADDRESS'] = 'Lieferadresse';
$lang['de_DE']['SilvercartPage']['SHIPPING_AND_BILLING'] = 'Liefer- und Rechnungsadresse';
$lang['de_DE']['SilvercartPage']['SHOP_WITHOUT_REGISTRATION'] = 'Shop ohne Registrierung';
$lang['de_DE']['SilvercartPage']['SHOW_DETAILS'] = 'Details anzeigen';
$lang['de_DE']['SilvercartPage']['SHOW_DETAILS_FOR'] = 'Details zu %s anzeigen';
$lang['de_DE']['SilvercartPage']['SHOWINPAGE'] = 'Sprache auf %s stellen';
$lang['de_DE']['SilvercartPage']['SITMAP_HERE'] = 'Hier können Sie eine Übersicht über unsere Seite sehen.';
$lang['de_DE']['SilvercartPage']['STEPS'] = 'Schritte';
$lang['de_DE']['SilvercartPage']['SUBMIT'] = 'Abschicken';
$lang['de_DE']['SilvercartPage']['SUBMIT_MESSAGE'] = 'Nachricht absenden';
$lang['de_DE']['SilvercartPage']['SUBTOTAL'] = 'Zwischensumme';
$lang['de_DE']['SilvercartPage']['SUBTOTAL_NET'] = 'Zwischensumme (Netto)';
$lang['de_DE']['SilvercartPage']['SUM'] = 'Summe';
$lang['de_DE']['SilvercartPage']['INCLUDING_TAX'] = 'inkl. %s%% MwSt.';
$lang['de_DE']['SilvercartPage']['EXCLUDING_TAX'] = 'exkl. MwSt.';
$lang['de_DE']['SilvercartPage']['TAX'] = 'inkl. %d%% MwSt.';
$lang['de_DE']['SilvercartPage']['TERMSOFSERVICE_PRIVACY'] = 'Allgemeine Geschäftsbedingungen und Datenschutz';
$lang['de_DE']['SilvercartPage']['THE_QUERY'] = 'Der Begriff';
$lang['de_DE']['SilvercartPage']['TITLE'] = 'Titel';
$lang['de_DE']['SilvercartPage']['TITLE_IMPRINT'] = 'Impressum';
$lang['de_DE']['SilvercartPage']['TITLE_TERMS'] = 'Allgemeine Geschäftsbedingungen';
$lang['de_DE']['SilvercartPage']['TOTAL'] = 'Gesamtsumme';
$lang['de_DE']['SilvercartPage']['URL_SEGMENT_IMPRINT'] = 'impressum';
$lang['de_DE']['SilvercartPage']['URL_SEGMENT_TERMS'] = 'allgemeine-geschaeftsbedingungen-kaeuferinformationen';
$lang['de_DE']['SilvercartPage']['USER_NOT_EXISTING'] = 'Diesen Benutzer gibt es nicht.';
$lang['de_DE']['SilvercartPage']['VALUE_OF_GOODS'] = 'Warenwert';
$lang['de_DE']['SilvercartPage']['VIEW_ORDERS_TEXT'] = 'Überprüfen Sie den Status Ihrer Bestellung in der';
$lang['de_DE']['SilvercartPage']['WELCOME_PAGE_TITLE'] = 'Willkommen';
$lang['de_DE']['SilvercartPage']['WELCOME_PAGE_URL_SEGMENT'] = 'willkommen';
$lang['de_DE']['SilvercartPage']['YEAR'] = 'Jahr';

$lang['de_DE']['SilvercartPaymentMethod']['ACCESS_MANAGEMENT_BASIC_LABEL'] = 'Allgemein';
$lang['de_DE']['SilvercartPaymentMethod']['ACCESS_MANAGEMENT_GROUP_LABEL'] = 'Nach Gruppe(n)';
$lang['de_DE']['SilvercartPaymentMethod']['ACCESS_MANAGEMENT_USER_LABEL'] = 'Nach Kunde(n)';
$lang['de_DE']['SilvercartPaymentMethod']['ACCESS_SETTINGS'] = 'Zugriffsverwaltung';
$lang['de_DE']['SilvercartPaymentMethod']['ATTRIBUTED_COUNTRIES'] = 'zugeordnete Länder';
$lang['de_DE']['SilvercartPaymentMethod']['BASIC_SETTINGS'] = 'Grundeinstellungen';
$lang['de_DE']['SilvercartPaymentMethod']['ENABLE_RESTRICTION_BY_ORDER_LABEL'] = 'Die folgende Regel anwenden';
$lang['de_DE']['SilvercartPaymentMethod']['FROM_PURCHASE_VALUE'] = 'ab Warenwert';
$lang['de_DE']['SilvercartPaymentMethod']['HANDLINGCOSTS_SETTINGS'] = 'Bearbeitungskosten';
$lang['de_DE']['SilvercartPaymentMethod']['LONG_PAYMENT_DESCRIPTION'] = 'Beschreibung zur Anzeige auf der Zahlungsartseite';
$lang['de_DE']['SilvercartPaymentMethod']['MODE'] = 'Modus';
$lang['de_DE']['SilvercartPaymentMethod']['NAME'] = 'Name';
$lang['de_DE']['SilvercartPaymentMethod']['NO_PAYMENT_METHOD_AVAILABLE'] = 'Keine Zahlarten verfügbar';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_SUMMODIFICATIONIMPACTVALUETYPE'] = 'Der Wert ist';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_IMPACT_TYPE_ABSOLUTE'] = 'Absolut';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_IMPACT_TYPE_PERCENT'] = 'Prozentual';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_LOGOS'] = 'Logos';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_MODIFY_PRODUCTVALUE'] = 'Warenwert';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_MODIFY_TOTALVALUE'] = 'Gesamtwert';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_MODIFY_TYPE_CHARGE'] = 'Aufschlag';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_MODIFY_TYPE_DISCOUNT'] = 'Abschlag';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_SUMMODIFICATIONIMPACTTYPE'] = 'Art';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_SUMMODIFICATIONIMPACT'] = 'Beeinflusst';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_SUMMODIFICATIONLABELFIELD'] = 'Beschriftung in Warenkorb/Bestellung';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_SUMMODIFICATIONVALUE'] = 'Wert';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_SUMMODIFIERS'] = 'Auf-/Abschläge';
$lang['de_DE']['SilvercartPaymentMethod']['PAYMENT_USE_SUMMODIFICATION'] = 'Aktivieren';
$lang['de_DE']['SilvercartPaymentMethod']['PLURALNAME'] = 'Bezahlarten';
$lang['de_DE']['SilvercartPaymentMethod']['RESTRICT_BY_ORDER_QUANTITY'] = 'Der Kunde muss mindestens die folgende Anzahl Bestellungen durchgeführt haben';
$lang['de_DE']['SilvercartPaymentMethod']['RESTRICT_BY_ORDER_STATUS'] = 'deren Bestellstatus in der folgenden Liste markiert ist';
$lang['de_DE']['SilvercartPaymentMethod']['RESTRICTION_LABEL'] = 'Nur für Kunden anzeigen, die die folgenden Kriterien erfüllen';
$lang['de_DE']['SilvercartPaymentMethod']['SHIPPINGMETHOD'] = 'Versandart';
$lang['de_DE']['SilvercartPaymentMethod']['SHIPPINGMETHOD_DESC'] = 'Zahlungsart an folgende Versandarten binden:';
$lang['de_DE']['SilvercartPaymentMethod']['SHOW_NOT_FOR_GROUPS_LABEL'] = 'Für folgende Gruppen deaktivieren';
$lang['de_DE']['SilvercartPaymentMethod']['SHOW_ONLY_FOR_GROUPS_LABEL'] = 'Für folgende Gruppen aktivieren';
$lang['de_DE']['SilvercartPaymentMethod']['SHOW_NOT_FOR_USERS_LABEL'] = 'Für folgende Benutzer deaktivieren';
$lang['de_DE']['SilvercartPaymentMethod']['SHOW_ONLY_FOR_USERS_LABEL'] = 'Für folgende Benutzer aktivieren';
$lang['de_DE']['SilvercartPaymentMethod']['SHOW_FORM_FIELDS_ON_PAYMENT_SELECTION'] = 'Eingabefelder bereits bei Zahlungs-Auswahl anzeigen';
$lang['de_DE']['SilvercartPaymentMethod']['SINGULARNAME'] = 'Zahlart';
$lang['de_DE']['SilvercartPaymentMethod']['STANDARD_ORDER_STATUS'] = 'Standard Bestellstatus für diese Zahlart';
$lang['de_DE']['SilvercartPaymentMethod']['TILL_PURCHASE_VALUE'] = 'bis Warenwert';
$lang['de_DE']['SilvercartPaymentMethod']['TITLE'] = 'Zahlart';

$lang['de_DE']['SilvercartPaymentMethodsPage']['DEFAULT_TITLE']                 = 'Zahlungsarten';
$lang['de_DE']['SilvercartPaymentMethodsPage']['DEFAULT_URLSEGMENT']            = 'zahlungsarten';
$lang['de_DE']['SilvercartPaymentMethodsPage']['PLURALNAME']                    = 'Zahlungsartseiten';
$lang['de_DE']['SilvercartPaymentMethodsPage']['SINGULARNAME']                  = 'Zahlungsartseite';

$lang['de_DE']['SilvercartPaymentMethodLanguage']['SINGULARNAME']               = _t('Silvercart.TRANSLATION');
$lang['de_DE']['SilvercartPaymentMethodLanguage']['PLURALNAME']                 = _t('Silvercart.TRANSLATIONS');

$lang['de_DE']['SilvercartPaymentPrepaymentLanguage']['SINGULARNAME']           = _t('Silvercart.TRANSLATION');
$lang['de_DE']['SilvercartPaymentPrepaymentLanguage']['PLURALNAME']             = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartPaymentPrepaymentLanguage']['TEXTBANKACCOUNTINFO']    = 'Informationen für Zahlart Vorkasse';
$lang['de_DE']['SilvercartPaymentPrepaymentLanguage']['INVOICEINFO']            = 'Informationen zur Zahlart Rechnung';

$lang['de_DE']['SilvercartPaymentNotification']['DEFAULT_TITLE'] = 'Zahlungsbenachrichtigung';
$lang['de_DE']['SilvercartPaymentNotification']['DEFAULT_URLSEGMENT'] = 'zahlungsbenachrichtigung';
$lang['de_DE']['SilvercartPaymentNotification']['PLURALNAME'] = 'Zahlungsbenachrichtigungen';
$lang['de_DE']['SilvercartPaymentNotification']['SINGULARNAME'] = 'Zahlungsbenachrichtigung';
$lang['de_DE']['SilvercartPaymentNotification']['TITLE'] = 'Zahlungsbenachrichtigung';
$lang['de_DE']['SilvercartPaymentNotification']['URL_SEGMENT'] = 'zahlungsbenachrichtigung';

$lang['de_DE']['SilvercartPrice']['PLURALNAME'] = 'Preise';
$lang['de_DE']['SilvercartPrice']['SINGULARNAME'] = 'Preis';

$lang['de_DE']['SilvercartProductCondition']['PLEASECHOOSE']                    = 'Bitte wählen';
$lang['de_DE']['SilvercartProductCondition']['PLURALNAME']                      = 'Artikelzustände';
$lang['de_DE']['SilvercartProductCondition']['SINGULARNAME']                    = 'Artikelzustand';
$lang['de_DE']['SilvercartProductCondition']['TITLE']                           = 'Zustand';
$lang['de_DE']['SilvercartProductCondition']['USE_AS_STANDARD_CONDITION']       = 'Als Vorgabe-Zustand verwenden, wenn nichts am Artikel definiert ist';

$lang['de_DE']['SilvercartProductConditionLanguage']['SINGULARNAME']            = _t('Silvercart.TRANSLATION');
$lang['de_DE']['SilvercartProductConditionLanguage']['PLURALNAME']              = _t('Silvercart.TRANSLATIONS');

$lang['de_DE']['SilvercartQuickSearchForm']['SUBMITBUTTONTITLE'] = 'Suchen';

$lang['de_DE']['SilvercartRating']['SINGULARNAME'] = 'Bewertung';
$lang['de_DE']['SilvercartRating']['PLURALNAME'] = 'Bewertungen';
$lang['de_DE']['SilvercartRating']['TEXT'] = 'Bewertungstext';
$lang['de_DE']['SilvercartRating']['GRADE'] = 'Bewertungsnote';

$lang['de_DE']['SilvercartRegisterConfirmationPage']['ALREADY_REGISTERES_MESSAGE_TEXT'] = 'Nachricht: Benutzer bereits registriert';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONFIRMATIONMAIL_SUBJECT'] = 'Bestätigungsmail: Betreff';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONFIRMATIONMAIL_TEXT'] = 'Bestätigungsmail: Nachricht';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONFIRMATION_MAIL'] = 'Bestätigungsmail';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['CONTENT'] = '<p>Lieber Kunde,</p><p>um Ihnen Arbeit zu ersparen, haben wir Sie bereits automatisch eingeloggt.</p><p>Viel Spass beim Einkaufen in unserem Shop!</p>';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['DEFAULT_CONTENT'] = '<p>Lieber Kunde,</p><p>um Ihnen Arbeit zu ersparen, haben wir Sie bereits automatisch eingeloggt.</p><p>Viel Spass beim Einkaufen in unserem Shop!</p>';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['DEFAULT_TITLE'] = 'Registrierungsbestätigungsseite';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['DEFAULT_URLSEGMENT'] = 'register-confirmation';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['FAILURE_MESSAGE_TEXT'] = 'Fehlermeldung';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['PLURALNAME'] = 'Registrierungsbestätigungsseiten';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['SINGULARNAME'] = 'Registrierungsbestätigungsseite';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['SUCCESS_MESSAGE_TEXT'] = 'Erfolgsmeldung';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['TITLE'] = 'Registrierungsbestätigungsseite';
$lang['de_DE']['SilvercartRegisterConfirmationPage']['URL_SEGMENT'] = 'register-confirmation';

$lang['de_DE']['SilvercartRegistrationPage']['ACTIVATION_MAIL_TAB'] = 'Aktivierungsmail';
$lang['de_DE']['SilvercartRegistrationPage']['ACTIVATION_MAIL_SUBJECT'] = 'Betreff der Aktivierungsmail';
$lang['de_DE']['SilvercartRegistrationPage']['ACTIVATION_MAIL_TEXT'] = 'Nachricht der Aktivierungsmail';
$lang['de_DE']['SilvercartRegistrationPage']['CONFIRMATION_TEXT'] = '<h1>Registrierung abschließen</h1><p>Bitte klicken Sie auf den Aktivierungslink oder kopieren Sie den Link in den Browser.</p><p><a href="$ConfirmationLink">Registrierung bestätigen</a></p><p>Sollten Sie sich nicht registriert haben, ignorieren Sie diese Mail einfach.</p><p>Ihr Webshop Team</p>';
$lang['de_DE']['SilvercartRegistrationPage']['CUSTOMER_SALUTATION'] = 'Sehr geehrter Kunde\,';
$lang['de_DE']['SilvercartRegistrationPage']['DEFAULT_TITLE'] = 'Registrierungsseite';
$lang['de_DE']['SilvercartRegistrationPage']['DEFAULT_URLSEGMENT'] = 'registrieren';
$lang['de_DE']['SilvercartRegistrationPage']['EMAIL_EXISTS_ALREADY'] = 'Diese E-Mail-Adresse ist schon vergeben.';
$lang['de_DE']['SilvercartRegistrationPage']['OTHERITEMS'] = 'Sonstiges';
$lang['de_DE']['SilvercartRegistrationPage']['PLEASE_COFIRM'] = 'Bitte bestätigen Sie Ihre Registrierung.';
$lang['de_DE']['SilvercartRegistrationPage']['PLURALNAME'] = 'Registrierungsseiten';
$lang['de_DE']['SilvercartRegistrationPage']['SINGULARNAME'] = 'Registrierungsseite';
$lang['de_DE']['SilvercartRegistrationPage']['SUCCESS_TEXT'] = '<h1>Registrierung erfolgreich abgeschlossen!</h1><p>Vielen Dank für Ihre Registrierung.</p><p>Viel Spass in unserem Shop!</p><p>Ihr Webshop Team</p>';
$lang['de_DE']['SilvercartRegistrationPage']['THANKS'] = 'Vielen Dank für Ihre Registrierung.';
$lang['de_DE']['SilvercartRegistrationPage']['TITLE'] = 'Registrierungsseite';
$lang['de_DE']['SilvercartRegistrationPage']['URL_SEGMENT'] = 'registrieren';
$lang['de_DE']['SilvercartRegistrationPage']['YOUR_REGISTRATION'] = 'Ihre Registrierung';

$lang['de_DE']['SilvercartSearchResultsPage']['DEFAULT_TITLE'] = 'Suchergebnisse';
$lang['de_DE']['SilvercartSearchResultsPage']['DEFAULT_URLSEGMENT'] = 'suchergebnisse';
$lang['de_DE']['SilvercartSearchResultsPage']['PLURALNAME'] = 'Suchergebnisseiten';
$lang['de_DE']['SilvercartSearchResultsPage']['SINGULARNAME'] = 'Suchergebnisseite';
$lang['de_DE']['SilvercartSearchResultsPage']['TITLE'] = 'Suchergebnisse';
$lang['de_DE']['SilvercartSearchResultsPage']['URL_SEGMENT'] = 'suchergebnisse';
$lang['de_DE']['SilvercartSearchResultsPage']['RESULTTEXT'] = 'Suchergebnisse f&uuml;r den Begriff <b>&rdquo;%s&rdquo;</b>';
$lang['de_DE']['SilvercartSearchResultsPage']['RELEVANCESORT'] = 'Relevanz';

$lang['de_DE']['SilvercartShippingAddress']['PLURALNAME'] = 'Lieferadressen';
$lang['de_DE']['SilvercartShippingAddress']['SINGULARNAME'] = 'Lieferadresse';

$lang['de_DE']['SilvercartShippingFee']['ATTRIBUTED_SHIPPINGMETHOD'] = 'zugeordnete Versandarten';
$lang['de_DE']['SilvercartShippingFee']['COSTS'] = 'Kosten';
$lang['de_DE']['SilvercartShippingFee']['EMPTYSTRING_CHOOSEZONE'] = '--Zone wählen--';
$lang['de_DE']['SilvercartShippingFee']['FOR_SHIPPINGMETHOD'] = 'für Versandart';
$lang['de_DE']['SilvercartShippingFee']['MAXIMUM_WEIGHT'] = 'Maximalgewicht (g)';
$lang['de_DE']['SilvercartShippingFee']['PLURALNAME'] = 'Versandgebühren';
$lang['de_DE']['SilvercartShippingFee']['POST_PRICING'] = 'Nachträgliche Preisermittlung';
$lang['de_DE']['SilvercartShippingFee']['POST_PRICING_INFO'] = 'Versandpreise können erst nach Auftragseingang manuell ermittelt werden.';
$lang['de_DE']['SilvercartShippingFee']['SINGULARNAME'] = 'Versandgebühr';
$lang['de_DE']['SilvercartShippingFee']['UNLIMITED_WEIGHT'] = 'unbegrenzt';
$lang['de_DE']['SilvercartShippingFee']['UNLIMITED_WEIGHT_LABEL'] = 'Unbegrenztes Maximalgewicht';
$lang['de_DE']['SilvercartShippingFee']['ZONE_WITH_DESCRIPTION'] = 'Zone (nur Zonen des Frachtführers verfügbar)';
$lang['de_DE']['SilvercartShippingFee']['FREEOFSHIPPINGCOSTSDISABLED']          = 'Versandkostenfreiheit für diese Versandgebühr deaktivieren';
$lang['de_DE']['SilvercartShippingFee']['FREEOFSHIPPINGCOSTSFROM']              = 'Versandkostenfrei ab (überschreibt Länderspeziefische und globale Einstellung)';

$lang['de_DE']['SilvercartShippingFeesPage']['DEFAULT_TITLE'] = 'Versandgebühren';
$lang['de_DE']['SilvercartShippingFeesPage']['DEFAULT_URLSEGMENT'] = 'versandgebuehren';
$lang['de_DE']['SilvercartShippingFeesPage']['PLURALNAME'] = 'Versandgebührenseiten';
$lang['de_DE']['SilvercartShippingFeesPage']['SINGULARNAME'] = 'Versandgebührenseite';
$lang['de_DE']['SilvercartShippingFeesPage']['TITLE'] = 'Versandgebühren';
$lang['de_DE']['SilvercartShippingFeesPage']['URL_SEGMENT'] = 'versandgebuehren';

$lang['de_DE']['SilvercartShippingMethod']['FOR_PAYMENTMETHODS'] = 'für Bezahlart';
$lang['de_DE']['SilvercartShippingMethod']['FOR_ZONES'] = 'für Zonen';
$lang['de_DE']['SilvercartShippingMethod']['DESCRIPTION'] = 'Beschreibung';
$lang['de_DE']['SilvercartShippingMethod']['PACKAGE'] = 'Paket';
$lang['de_DE']['SilvercartShippingMethod']['PLURALNAME'] = 'Versandarten';
$lang['de_DE']['SilvercartShippingMethod']['SINGULARNAME'] = 'Versandart';
$lang['de_DE']['SilvercartShippingMethod']['CHOOSE_DATAOBJECT_TO_IMPORT'] = 'Was wollen Sie importieren?';
$lang['de_DE']['SilvercartShippingMethod']['NO_SHIPPING_METHOD_AVAILABLE'] = 'Keine Versandart verfügbar';
$lang['de_DE']['SilvercartShippingMethod']['CHOOSE_SHIPPING_METHOD'] = 'Bitte wählen Sie Ihre Versandart für die Lieferung nach "%s"';

$lang['de_DE']['SilvercartShippingMethodLanguage']['PLURALNAME']                = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartShippingMethodLanguage']['SINGULARNAME']              = _t('Silvercart.TRANSLATION');

$lang['de_DE']['SilvercartShopAdmin']['PAYMENT_DESCRIPTION'] = 'Beschreibung';
$lang['de_DE']['SilvercartShopAdmin']['PAYMENT_ISACTIVE'] = 'aktiviert';
$lang['de_DE']['SilvercartShopAdmin']['PAYMENT_MAXAMOUNTFORACTIVATION'] = 'Höchstbetrag für Modul';
$lang['de_DE']['SilvercartShopAdmin']['PAYMENT_MINAMOUNTFORACTIVATION'] = 'Mindestbetrag für Modul';
$lang['de_DE']['SilvercartShopAdmin']['PAYMENT_MODE_DEV'] = 'Dev';
$lang['de_DE']['SilvercartShopAdmin']['PAYMENT_MODE_LIVE'] = 'Live';
$lang['de_DE']['SilvercartShopAdmin']['SHOW_PAYMENT_LOGOS'] = 'Logos anzeigen';

$lang['de_DE']['SilvercartShopAdministrationAdmin']['TITLE'] = 'SC Admin';

$lang['de_DE']['SilvercartShopConfigurationAdmin']['SILVERCART_CONFIG'] = 'SC Konfig';

$lang['de_DE']['SilvercartShopEmail']['SINGULARNAME'] = 'E-Mail des Shops';
$lang['de_DE']['SilvercartShopEmail']['PLURALNAME'] = 'E-Mails des Shops';
$lang['de_DE']['SilvercartShopEmail']['EMAILTEXT'] = 'Nachricht';
$lang['de_DE']['SilvercartShopEmail']['IDENTIFIER'] = 'Bezeichner';
$lang['de_DE']['SilvercartShopEmail']['PLURALNAME'] = 'Shop E-Mails';
$lang['de_DE']['SilvercartShopEmail']['SINGULARNAME'] = 'Shop E-Mail';
$lang['de_DE']['SilvercartShopEmail']['SUBJECT'] = 'Betreff';
$lang['de_DE']['SilvercartShopEmail']['VARIABLES'] = 'Variablen';
$lang['de_DE']['SilvercartShopEmail']['REGARDS'] = 'Mit freundlichen Grüßen';
$lang['de_DE']['SilvercartShopEmail']['YOUR_TEAM'] = 'Ihr SilverCart Webshop Team';
$lang['de_DE']['SilvercartShopEmail']['HELLO'] = 'Hallo';
$lang['de_DE']['SilvercartShopEmail']['ADDITIONALS_RECEIPIENTS'] = 'Zusätzliche Empfänger';
$lang['de_DE']['SilvercartShopEmail']['ORDER_ARRIVED'] = 'Ihre Bestellung ist soeben bei uns eingegangen, vielen Dank.';
$lang['de_DE']['SilvercartShopEmail']['ORDER_ARRIVED_EMAIL_SUBJECT'] = 'Ihre Bestellung in unserem Webshop';
$lang['de_DE']['SilvercartShopEmail']['ORDER_SHIPPED_MESSAGE'] = 'Ihre Bestellung wurde soeben von uns versendet.';
$lang['de_DE']['SilvercartShopEmail']['ORDER_SHIPPED_NOTIFICATION_SUBJECT'] = 'Ihre Bestellung wurde soeben von uns versendet.';
$lang['de_DE']['SilvercartShopEmail']['ORDER_SHIPPED_NOTIFICATION']             = 'Versandbenachrichtigung';
$lang['de_DE']['SilvercartShopEmail']['NEW_ORDER_PLACED'] = 'Eine neue Bestellung wurde aufgegeben';
$lang['de_DE']['SilvercartShopEmail']['FORGOT_PASSWORD_SUBJECT']                = 'Passwort zurücksetzen';

$lang['de_DE']['SilvercartShopEmailLanguage']['PLURALNAME']                     = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartShopEmailLanguage']['SINGULARNAME']                   = _t('Silvercart.TRANSLATION');

$lang['de_DE']['SilvercartShoppingCart']['ERROR_MINIMUMORDERVALUE_NOT_REACHED'] = 'Der Mindestbestellwert beträgt %s';
$lang['de_DE']['SilvercartShoppingCart']['PLURALNAME'] = 'Warenkörbe';
$lang['de_DE']['SilvercartShoppingCart']['SINGULARNAME'] = 'Warenkorb';

$lang['de_DE']['SilvercartShoppingCartPosition']['MAX_QUANTITY_REACHED_MESSAGE'] = 'Die maximale Anzahl an Artikeln für diese Position wurde erreicht.';
$lang['de_DE']['SilvercartShoppingCartPosition']['PLURALNAME'] = 'Warenkorbpositionen';
$lang['de_DE']['SilvercartShoppingCartPosition']['QUANTITY_ADDED_MESSAGE'] = 'Der Artikel wurde in den Warenkorb gelegt.';
$lang['de_DE']['SilvercartShoppingCartPosition']['QUANTITY_ADJUSTED_MESSAGE'] = 'Die Menge dieser Position wurde an den verfügbaren Lagerbestand angepasst.';
$lang['de_DE']['SilvercartShoppingCartPosition']['REMAINING_QUANTITY_ADDED_MESSAGE'] = 'Da wir die angeforderte Menge nicht mehr auf lager haben, haben wir die verfügbare Menge in Ihren Warenkorb gelegt.';
$lang['de_DE']['SilvercartShoppingCartPosition']['SINGULARNAME'] = 'Warenkorbposition';

$lang['de_DE']['SilvercartTax']['LABEL']                                        = 'Bezeichnung';
$lang['de_DE']['SilvercartTax']['PLURALNAME']                                   = 'Steuersätze';
$lang['de_DE']['SilvercartTax']['RATE_IN_PERCENT']                              = 'Steuersatz in %';
$lang['de_DE']['SilvercartTax']['SINGULARNAME']                                 = 'Steuersatz';
$lang['de_DE']['SilvercartTax']['ISDEFAULT']                                    = 'Ist Standard';

$lang['de_DE']['SilvercartTaxLanguage']['SINGULARNAME']                         = _t('Silvercart.TRANSLATION');
$lang['de_DE']['SilvercartTaxLanguage']['PLURALNAME']                           = _t('Silvercart.TRANSLATIONS');

$lang['de_DE']['SilvercartTestData']['CURRENCY']                                    = 'EUR';
$lang['de_DE']['SilvercartTestData']['IMAGEFOLDERNAME']                             = 'Beispieldaten';
$lang['de_DE']['SilvercartTestData']['WIDGETSET_FRONTPAGE_CONTENT_TITLE']           = 'Startseite Inhaltsbereich';
$lang['de_DE']['SilvercartTestData']['WIDGETSET_FRONTPAGE_SIDEBAR_TITLE']           = 'Startseite Seitenleiste';
$lang['de_DE']['SilvercartTestData']['WIDGETSET_PRODUCTGROUPPAGES_SIDEBAR_TITLE']   = 'Warengruppenseiten Seitenleiste';
$lang['de_DE']['SilvercartTestData']['WIDGETSET_FRONTPAGE_CONTENT1_TITLE']          = 'Zahlungsmodule';
$lang['de_DE']['SilvercartTestData']['WIDGETSET_FRONTPAGE_CONTENT1_CONTENT']        = '<p>Entdecken Sie die Zahlungsmodule von SilverCart.</p>';
$lang['de_DE']['SilvercartTestData']['WIDGETSET_FRONTPAGE_CONTENT2_TITLE']          = 'Sonstige Module';
$lang['de_DE']['SilvercartTestData']['WIDGETSET_FRONTPAGE_CONTENT2_CONTENT']        = '<p>Auch für viele andere Anwendungsfälle finden Sie Module in SilverCart.</p>';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUP_CONTENT']                        = '<div class="silvercart-message highlighted info32"><p><strong>Achtung:</strong></p><p>Die Module selbst sind kostenfrei. Die Preisangabe wird rein für Demo-Zwecke verwendet.</p></div>';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPPAYMENT_TITLE']                   = 'Zahlungsmodule';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPPAYMENT_URLSEGMENT']              = 'zahlungsmodule';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPMARKETING_TITLE']                 = 'Marketingmodule';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPMARKETING_URLSEGMENT']            = 'marketingmodule';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPOTHERS_TITLE']                    = 'Andere Module';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPOTHERS_URLSEGMENT']               = 'andere-module';
$lang['de_DE']['SilvercartTestData']['slidorion_productGroupHolder_TITLE']          = 'Vorteile von SilverCart';
$lang['de_DE']['SilvercartTestData']['slidorion_productGroupHolder_URLSEGMENT']     = 'vorteile-von-silvercart';
$lang['de_DE']['SilvercartTestData']['SLIDORION_TITLE']                             = 'Vorteile von SilverCart';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPCUSTOMISABLE_TITLE']              = 'Anpassbar';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPCUSTOMISABLE_URLSEGMENT']         = 'anpassbar';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPCUSTOMISABLE_CONTENT']            = 'Mit Hilfe von Widgets ist SilverCart ganz einfach anpassbar.';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPEXTENDABLE_TITLE']                = 'Erweiterbar';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPEXTENDABLE_URLSEGMENT']           = 'erweiterbar';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPEXTENDABLE_CONTENT']              = 'Durch Module ist SilverCart ganz einfach erweiterbar.';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPOPEN_TITLE']                      = 'Offen';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPOPEN_URLSEGMENT']                 = 'offen';
$lang['de_DE']['SilvercartTestData']['PRODUCTGROUPOPEN_CONTENT']                    = 'SilverCart ist Open-Source. Dadurch bezahlen Sie nur für die Implementierung und Anpassung des Shopsystems an Ihre Wünsche.';

$lang['de_DE']['SilvercartUpdate']['DESCRIPTION'] = 'Beschreibung';
$lang['de_DE']['SilvercartUpdate']['SILVERCARTVERSION'] = 'Version';
$lang['de_DE']['SilvercartUpdate']['SILVERCARTUPDATEVERSION'] = 'Update';
$lang['de_DE']['SilvercartUpdate']['STATUS'] = 'Status';
$lang['de_DE']['SilvercartUpdate']['STATUSMESSAGE'] = 'Statusmeldung';
$lang['de_DE']['SilvercartUpdate']['STATUS_DONE'] = 'Durchgeführt';
$lang['de_DE']['SilvercartUpdate']['STATUS_REMAINING'] = 'Ausstehend';
$lang['de_DE']['SilvercartUpdate']['STATUS_SKIPPED'] = 'Übersprungen';
$lang['de_DE']['SilvercartUpdate']['STATUSMESSAGE_DONE'] = 'Dieses Update wurde erfolgreich durchgeführt.';
$lang['de_DE']['SilvercartUpdate']['STATUSMESSAGE_REMAINING'] = 'Dieses Update ist noch ausstehend.';
$lang['de_DE']['SilvercartUpdate']['STATUSMESSAGE_SKIPPED'] = 'Dieses Update ist bereits integriert.';
$lang['de_DE']['SilvercartUpdate']['STATUSMESSAGE_SKIPPED_TO_PREVENT_DAMAGE'] = 'Manuelle Änderungen wurden gefunden. Dieses Update wurde übersprungen, um Schäden an der Datenhaltung zu vermeiden.';
$lang['de_DE']['SilvercartUpdate']['STATUSMESSAGE_ERROR'] = 'Es ist ein unbekannter Fehler aufgetreten.';

$lang['de_DE']['SilvercartUpdateAdmin']['SILVERCART_UPDATE'] = 'Updates';

$lang['de_DE']['SilvercartWidget']['SORT_ORDER_LABEL'] = 'Sortierung';

$lang['de_DE']['SilvercartWidgets']['WIDGETSET_CONTENT_FIELD_LABEL'] = 'Widgets für den Inhaltsbereich';
$lang['de_DE']['SilvercartWidgets']['WIDGETSET_SIDEBAR_FIELD_LABEL'] = 'Widgets für die Seitenleiste';

$lang['de_DE']['SilvercartWidgetSet']['MANAGE_WIDGETS_BUTTON'] = 'Widgets Sets verwalten';
$lang['de_DE']['SilvercartWidgetSet']['PLURALNAME'] = 'Widget Sets';
$lang['de_DE']['SilvercartWidgetSet']['SINGULARNAME'] = 'Widget Set';
$lang['de_DE']['SilvercartWidgetSet']['PAGES'] = 'zugeordnete Seiten';
$lang['de_DE']['SilvercartWidgetSet']['INFO'] = '<strong>Achtung:</strong><br/>Um ein Widget Set hinzuzufügen oder zu verändern, wählen Sie den "SC Konfig" Bereich im Hauptmenü. Dort gelangen Sie über die Auswahl "Widget Set" in der Drowdown-Liste zur Bearbeitungsmaske der Widget Sets.';

$lang['de_DE']['SilvercartZone']['ATTRIBUTED_COUNTRIES'] = 'zugeordnete Länder';
$lang['de_DE']['SilvercartZone']['ATTRIBUTED_SHIPPINGMETHODS'] = 'zugeordnete Versandart';
$lang['de_DE']['SilvercartZone']['COUNTRIES'] = 'Länder';
$lang['de_DE']['SilvercartZone']['DOMESTIC'] = 'Inland';
$lang['de_DE']['SilvercartZone']['FOR_COUNTRIES'] = 'für Länder';
$lang['de_DE']['SilvercartZone']['PLURALNAME'] = 'Zonen';
$lang['de_DE']['SilvercartZone']['SINGULARNAME'] = 'Zone';
$lang['de_DE']['SilvercartZone']['USE_ALL_COUNTRIES'] = 'Nach dem Speichern dynamisch alle Länder aufnehmen';
$lang['de_DE']['SilvercartZone']['VALID_FOR_ALL_AVAILABLE'] = 'Gilt für alle auswählbaren Länder';

$lang['de_DE']['SilvercartZoneLanguage']['SINGULARNAME']                        = _t('Silvercart.TRANSLATION');
$lang['de_DE']['SilvercartZoneLanguage']['PLURALNAME']                          = _t('Silvercart.TRANSLATIONS');

$lang['de_DE']['SilvercartQuantityUnit']['NAME']                        = 'Name';
$lang['de_DE']['SilvercartQuantityUnit']['ABBREVIATION']                = 'Abkürzung';
$lang['de_DE']['SilvercartQuantityUnit']['SINGULARNAME']                = 'Verkaufsmengeneinheit';
$lang['de_DE']['SilvercartQuantityUnit']['PLURALNAME']                  = 'Verkaufsmengeneinheiten';
$lang['de_DE']['SilvercartQuantityUnit']['EXPLANATION_TO_DECIMAL_PLACES'] = 'Auf 0 stellen oder leer lassen, um keine Dezimalstellen zu verwenden. Diese Einstellung wird z.B. für "in den Warenkorb legen" Formulare benutzt.';
$lang['de_DE']['SilvercartQuantityUnit']['NUMBER_OF_DECIMAL_PLACES']    = 'Anzahl Dezimalstellen';

$lang['de_DE']['SilvercartQuantityUnitLanguage']['PLURALNAME']                  = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartQuantityUnitLanguage']['SINGULARNAME']                = _t('Silvercart.TRANSLATION');

// Widgets ----------------------------------------------------------------- */

$lang['de_DE']['SilvercartLatestBlogPostsWidget']['CMSTITLE']                   = 'Neueste Blogeinträge anzeigen';
$lang['de_DE']['SilvercartLatestBlogPostsWidget']['DESCRIPTION']                = 'Zeigt die neuesten Blogeinträge an.';
$lang['de_DE']['SilvercartLatestBlogPostsWidget']['IS_CONTENT_VIEW']            = 'Normale Artikelansicht statt Widgetansicht verwenden';
$lang['de_DE']['SilvercartLatestBlogPostsWidget']['SHOW_ENTRY']                 = 'Meldung lesen';
$lang['de_DE']['SilvercartLatestBlogPostsWidget']['STOREADMIN_NUMBEROFPOSTS']   = 'Anzahl der Blogeinträge, die angezeigt werden sollen';
$lang['de_DE']['SilvercartLatestBlogPostsWidget']['TITLE']                      = 'Neueste Blogeinträge anzeigen';
$lang['de_DE']['SilvercartLatestBlogPostsWidget']['WIDGET_TITLE']               = 'Überschrift für das Widget';

$lang['de_DE']['SilvercartLatestBlogPostsWidgetLanguage']['SINGULARNAME']       = _t('Silvercart.TRANSLATION');
$lang['de_DE']['SilvercartLatestBlogPostsWidgetLanguage']['PLURALNAME']         = _t('Silvercart.TRANSLATIONS');

$lang['de_DE']['SilvercartLoginWidget']['TITLE']                    = 'Anmeldung';
$lang['de_DE']['SilvercartLoginWidget']['TITLE_LOGGED_IN']          = 'Mein Konto';
$lang['de_DE']['SilvercartLoginWidget']['TITLE_NOT_LOGGED_IN']      = 'Anmeldung';
$lang['de_DE']['SilvercartLoginWidget']['CMSTITLE']                 = 'SilverCart Anmeldung';
$lang['de_DE']['SilvercartLoginWidget']['DESCRIPTION']              = 'Dieses Widget zeigt ein Loginformular und Links zu der Registrierungsseite. Ist der Kunde eingeloggt, werden ihm stattdessen Links zu den Bereichen seines Kundenkontos angezeigt.';

$lang['de_DE']['SilvercartWidget']['FRONTTITLE']                                = 'Überschrift';
$lang['de_DE']['SilvercartWidget']['FRONTCONTENT']                              = 'Beschreibungstext';

$lang['de_DE']['SilvercartProductSliderWidget']['AUTOPLAY']                             = 'Automatische Slideshow aktivieren';
$lang['de_DE']['SilvercartProductSliderWidget']['AUTOPLAYDELAYED']                      = 'Verzögerung für automatische Slideshow aktivieren';
$lang['de_DE']['SilvercartProductSliderWidget']['AUTOPLAYLOCKED']                       = 'Automatische Slideshow deaktivieren, wenn Benutzer selbst navigiert';
$lang['de_DE']['SilvercartProductSliderWidget']['BUILDARROWS']                          = 'Vor-/Zurück Schaltflächen anzeigen';
$lang['de_DE']['SilvercartProductSliderWidget']['BUILDNAVIGATION']                      = 'Seitennavigation anzeigen';
$lang['de_DE']['SilvercartProductSliderWidget']['BUILDSTARTSTOP']                       = 'Start/Stop Schaltfläche anzeigen';
$lang['de_DE']['SilvercartProductSliderWidget']['CMS_BASICTABNAME']                     = 'Grundeinstellungen';
$lang['de_DE']['SilvercartProductSliderWidget']['CMS_DISPLAYTABNAME']                   = 'Darstellung';
$lang['de_DE']['SilvercartProductSliderWidget']['CMS_ROUNDABOUTTABNAME']                = 'Roundabout';
$lang['de_DE']['SilvercartProductSliderWidget']['CMS_SLIDERTABNAME']                    = 'Slideshow';
$lang['de_DE']['SilvercartProductSliderWidget']['FETCHMETHOD']                          = 'Auswahlmehode für Produkte';
$lang['de_DE']['SilvercartProductSliderWidget']['FETCHMETHOD_RANDOM']                   = 'Zufällig';
$lang['de_DE']['SilvercartProductSliderWidget']['FRONTTITLE']                           = 'Überschrift';
$lang['de_DE']['SilvercartProductSliderWidget']['FRONTCONTENT']                         = 'Beschreibungstext';
$lang['de_DE']['SilvercartProductSliderWidget']['GROUPVIEW']                            = 'Produktlisten-Ansicht';
$lang['de_DE']['SilvercartProductSliderWidget']['IS_CONTENT_VIEW']                      = 'Normale Artikelansicht statt Widgetansicht verwenden';
$lang['de_DE']['SilvercartProductSliderWidget']['NUMBEROFPRODUCTSTOFETCH']              = 'Anzahl der Artikel, die geladen werden sollen:';
$lang['de_DE']['SilvercartProductSliderWidget']['NUMBEROFPRODUCTSTOSHOW']               = 'Anzahl der Artikel, die angezeigt werden sollen (nur relevant, wenn Slider aktiviert wurde, ansonsten werden alle Artikel angezeigt, die geladen werden):';
$lang['de_DE']['SilvercartProductSliderWidget']['SLIDEDELAY']                           = 'Dauer der Anzeige pro Bild für die automatische Slideshow';
$lang['de_DE']['SilvercartProductSliderWidget']['STOPATEND']                            = 'Stoppt die automatische Slideshow nach dem letzten Panel';
$lang['de_DE']['SilvercartProductSliderWidget']['TRANSITIONEFFECT']                     = 'Übergangeffekt';
$lang['de_DE']['SilvercartProductSliderWidget']['TRANSITION_FADE']                      = 'Überblenden';
$lang['de_DE']['SilvercartProductSliderWidget']['TRANSITION_HORIZONTALSLIDE']           = 'Horizontal schieben';
$lang['de_DE']['SilvercartProductSliderWidget']['TRANSITION_VERTICALSLIDE']             = 'Vertikal schieben';
$lang['de_DE']['SilvercartProductSliderWidget']['USE_LISTVIEW']                         = 'Listendarstellung verwenden';
$lang['de_DE']['SilvercartProductSliderWidget']['USE_ROUNDABOUT']                       = 'Roundabout verwenden';
$lang['de_DE']['SilvercartProductSliderWidget']['USE_SLIDER']                           = 'Slider verwenden';

$lang['de_DE']['SilvercartProductGroupChildProductsWidget']['CMSTITLE']     = 'SilverCart Anzeige von Produkten aus Unterwarengruppen';
$lang['de_DE']['SilvercartProductGroupChildProductsWidget']['DESCRIPTION']  = 'Dieses Widget zeigt Artikel aus Unterwarengruppen, wenn der aktuellen Warengruppe keine Artikel zugeordnet sind.';
$lang['de_DE']['SilvercartProductGroupChildProductsWidget']['TITLE']        = 'Artikel aus Unterwarengruppen';

$lang['de_DE']['SilvercartProductGroupChildProductsWidgetLanguage']['PLURALNAME']   = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartProductGroupChildProductsWidgetLanguage']['SINGULARNAME'] = _t('Silvercart.TRANSLATION');

$lang['de_DE']['SilvercartProductGroupItemsWidget']['CMS_PRODUCTGROUPTABNAME']              = 'Warengruppe';
$lang['de_DE']['SilvercartProductGroupItemsWidget']['CMS_PRODUCTSTABNAME']                  = 'Artikel';
$lang['de_DE']['SilvercartProductGroupItemsWidget']['CMSTITLE']                             = 'SilverCart Slider für Produkte';
$lang['de_DE']['SilvercartProductGroupItemsWidget']['DESCRIPTION']                          = 'Dieses Widget zeigt Artikel aus einer Warengruppe an. Es kann definiert werden, aus welcher Warengruppe und wieviele Artikel angezeigt werden sollen.';
$lang['de_DE']['SilvercartProductGroupItemsWidget']['FETCHMETHOD_SORTORDERASC']             = 'Anordnung aufsteigend';
$lang['de_DE']['SilvercartProductGroupItemsWidget']['FETCHMETHOD_SORTORDERDESC']            = 'Anordnung absteigend';
$lang['de_DE']['SilvercartProductGroupItemsWidget']['SELECTIONMETHOD_PRODUCTGROUP']         = 'Aus Warengruppe';
$lang['de_DE']['SilvercartProductGroupItemsWidget']['SELECTIONMETHOD_PRODUCTS']             = 'Produkte von Hand wählen';
$lang['de_DE']['SilvercartProductGroupItemsWidget']['STOREADMIN_FIELDLABEL']                = 'Bitte wählen Sie die anzuzeigende Warengruppe:';
$lang['de_DE']['SilvercartProductGroupItemsWidget']['TITLE']                                = 'Produkte';
$lang['de_DE']['SilvercartProductGroupItemsWidget']['USE_SELECTIONMETHOD']                  = 'Auswahlmethode für Produkte';
$lang['de_DE']['SilvercartProductGroupItemsWidget']['SELECT_PRODUCT_DESCRIPTION']           = 'Bitte Produktnummern eingeben, durch Strichpunkt getrennt';

$lang['de_DE']['SilvercartProductGroupManufacturersWidget']['CMSTITLE']    = 'SilverCart Herstellerliste';
$lang['de_DE']['SilvercartProductGroupManufacturersWidget']['DESCRIPTION'] = 'Dieses Widget stellt eine Liste aller Hersteller der betreffenden Warengruppe dar.';
$lang['de_DE']['SilvercartProductGroupManufacturersWidget']['RESETFILTER'] = 'Alle anzeigen';
$lang['de_DE']['SilvercartProductGroupManufacturersWidget']['TITLE']       = 'Herstellerliste';

$lang['de_DE']['SilvercartBargainProductsWidget']['CMSTITLE']                   = 'SilverCart Slider für Schnäppchen';
$lang['de_DE']['SilvercartBargainProductsWidget']['DESCRIPTION']                = 'Dieses Widget zeigt eine konfigurierbare Anzahl der Artikel an, deren Differenz von UVP und Endpreis am größten ist.';
$lang['de_DE']['SilvercartBargainProductsWidget']['FETCHMETHOD_SORTORDERASC']   = 'Preisdifferenz aufsteigend';
$lang['de_DE']['SilvercartBargainProductsWidget']['FETCHMETHOD_SORTORDERDESC']  = 'Preisdifferenz absteigend';
$lang['de_DE']['SilvercartBargainProductsWidget']['TITLE']                      = 'Schnäppchen';

$lang['de_DE']['SilvercartBargainProductsWidgetLanguage']['PLURALNAME']         = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartBargainProductsWidgetLanguage']['SINGULARNAME']       = _t('Silvercart.TRANSLATION');

$lang['de_DE']['SilvercartProductGroupItemsWidgetLanguage']['SINGULARNAME']     = _t('Silvercart.TRANSLATION');
$lang['de_DE']['SilvercartProductGroupItemsWidgetLanguage']['PLURALNAME']       = _t('Silvercart.TRANSLATIONS');

$lang['de_DE']['SilvercartProductGroupSliderWidget']['CMSTITLE']                = 'Slider für Warengruppen';
$lang['de_DE']['SilvercartProductGroupSliderWidget']['DESCRIPTION']             = 'Erzeugt einen Slider, der alle Warengruppen anzeigt.';
$lang['de_DE']['SilvercartProductGroupSliderWidget']['TITLE']                   = 'Slider für Warengruppen';

$lang['de_DE']['SilvercartProductLanguage']['SINGULARNAME']                     = _t('Silvercart.TRANSLATION');
$lang['de_DE']['SilvercartProductLanguage']['PLURALNAME']                       = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartProductLanguage']['LOCALE']                           = 'Sprache';

$lang['de_DE']['SilvercartSearchWidget']['TITLE']                   = 'Suchen Sie etwas?';
$lang['de_DE']['SilvercartSearchWidget']['CMSTITLE']                = 'SilverCart Suche';
$lang['de_DE']['SilvercartSearchWidget']['DESCRIPTION']             = 'Dieses Widget zeigt ein Suchformular für die Artikelsuche an.';

$lang['de_DE']['SilvercartSearchWidgetForm']['SEARCHLABEL']         = 'Geben Sie bitte Ihren Suchbegriff ein:';
$lang['de_DE']['SilvercartSearchWidgetForm']['SUBMITBUTTONTITLE']   = 'Suchen';

$lang['de_DE']['SilvercartSearchCloudWidget']['TITLE']                          = 'Die häufigsten Suchbegriffe';
$lang['de_DE']['SilvercartSearchCloudWidget']['CMSTITLE']                       = 'Häufigste Suchbegriffe';
$lang['de_DE']['SilvercartSearchCloudWidget']['DESCRIPTION']                    = 'Dieses Widget zeigt ein TagCloud mit den am häufigsten vorkommenden Suchbegriffen an.';
$lang['de_DE']['SilvercartSearchCloudWidget']['TAGSPERCLOUD']                   = 'Anzahl der anzuzeigenden Suchbegriffe';
$lang['de_DE']['SilvercartSearchCloudWidget']['FONTSIZECOUNT']                  = 'Anzahl der Schriftgrade';

$lang['de_DE']['SilvercartShoppingcartWidget']['TITLE']                 = 'Warenkorb';
$lang['de_DE']['SilvercartShoppingcartWidget']['CMSTITLE']              = 'SilverCart Warenkorb';
$lang['de_DE']['SilvercartShoppingcartWidget']['DESCRIPTION']           = 'Dieses Widget zeigt den Inhalt des Warenkorbs. Zusätzlich werden Links zu den Warenkorb- und (falls sich Artikel im Warenkorb befinden) Checkoutseiten angezeigt';
$lang['de_DE']['SilvercartShoppingcartWidget']['SHOWONLYWHENFILLED']    = 'Widget nur Anzeigen, wenn Warenkorb gefüllt.';

$lang['de_DE']['SilvercartSubNavigationWidget']['TITLE']                = 'Subnavigation';
$lang['de_DE']['SilvercartSubNavigationWidget']['CMSTITLE']             = 'SilverCart Subnavigation';
$lang['de_DE']['SilvercartSubNavigationWidget']['DESCRIPTION']          = 'Dieses Widget zeigt eine Navigation des aktuellen Bereiches und dessen Unterseiten an.';
$lang['de_DE']['SilvercartSubNavigationWidget']['LABEL_TITEL']          = 'Titel';
$lang['de_DE']['SilvercartSubNavigationWidget']['STARTATLEVEL']         = 'Zeige Hierarchie ab folgender Stufe an';

$lang['de_DE']['SilvercartStoreAdminMenu']['CONFIG'] = 'Einstellungen';
$lang['de_DE']['SilvercartStoreAdminMenu']['MODULES'] = 'Module';
$lang['de_DE']['SilvercartStoreAdminMenu']['ORDERS'] = 'Bestellungen';
$lang['de_DE']['SilvercartStoreAdminMenu']['PRODUCTS'] = 'Artikel';

$lang['de_DE']['SilvercartText']['TITLE']               = 'Freitext';
$lang['de_DE']['SilvercartText']['DESCRIPTION']         = 'Geben Sie beliebigen Text ein.';
$lang['de_DE']['SilvercartText']['CSSFIELD_LABEL']      = 'Zusätzliche CSS Klassen (optional):';
$lang['de_DE']['SilvercartText']['FREETEXTFIELD_LABEL'] = 'Ihr Text:';
$lang['de_DE']['SilvercartText']['HEADLINEFIELD_LABEL'] = 'Überschrift (optional):';

$lang['de_DE']['SilvercartTextWidget']['IS_CONTENT_VIEW']                       = 'Inhaltsansicht statt Widgetansicht verwenden';

$lang['de_DE']['SilvercartTextWidgetLanguage']['PLURALNAME']                    = _t('Silvercart.TRANSLATIONS');
$lang['de_DE']['SilvercartTextWidgetLanguage']['SINGULARNAME']                  = _t('Silvercart.TRANSLATION');

$lang['de_DE']['SilvercartTopsellerProductsWidget']['TITLE']                    = 'Topseller';
$lang['de_DE']['SilvercartTopsellerProductsWidget']['CMSTITLE']                 = 'SilverCart Topseller';
$lang['de_DE']['SilvercartTopsellerProductsWidget']['DESCRIPTION']              = 'Dieses Widget zeigt eine konfigurierbare Anzahl der meistverkauften Artikel an.';
$lang['de_DE']['SilvercartTopsellerProductsWidget']['STOREADMIN_FIELDLABEL']    = 'Anzahl der Artikel, die angezeigt werden sollen:';

$lang['de_DE']['SilvercartProductGroupNavigationWidget']['TITLE']                       = 'Warengruppennavigation';
$lang['de_DE']['SilvercartProductGroupNavigationWidget']['CMSTITLE']                    = 'SilverCart Warengruppennavigation';
$lang['de_DE']['SilvercartProductGroupNavigationWidget']['DESCRIPTION']                 = 'Dieses Widget erstellt eine Navigationshierarchie für Warengruppen. Es kann angegeben werden, welche Warengruppe als Wurzel genutzt werden soll.';
$lang['de_DE']['SilvercartProductGroupNavigationWidget']['LEVELS_TO_SHOW']              = 'Ebenen bis zu welcher Tiefe anzeigen';
$lang['de_DE']['SilvercartProductGroupNavigationWidget']['SHOW_ALL_LEVELS']             = 'Alle Ebenen anzeigen';
$lang['de_DE']['SilvercartProductGroupNavigationWidget']['EXPAND_ACTIVE_SECTION_ONLY']  = 'Nur aktiven Zweig aufklappen';

$lang['de_DE']['SilvercartSiteConfig']['CREATE_TRANSLATION_DESC']   = 'Neue Übersetzungen werden für den gesamten Seitenbaum (unveröffentlicht) erstellt. Jede Seite wird als Übersetzungs-Vorlage angelegt und wenn vorhanden mit Standard-Inhalten der gewählten Sprache befüllt. Sind keine Standard-Inhalte vorhanden, werden die Inhalte der Sprache vorbelegt, die aktuell gewählt ist.';
$lang['de_DE']['SilvercartSiteConfig']['DASHBOARD_TAB']             = 'SilverCart Dashboard';
$lang['de_DE']['SilvercartSiteConfig']['PUBLISHBUTTON']             = 'Alle Seiten der aktuellen Sprache veröffentlichen';
$lang['de_DE']['SilvercartSiteConfig']['WELCOME_TO_SILVERCART']     = 'Willkommen bei SilverCart';
$lang['de_DE']['SilvercartSiteConfig']['TESTDATA_HEADLINE']         = 'Testdaten';
$lang['de_DE']['SilvercartSiteConfig']['TESTDATA_TEXT']             = 'Es sind noch keine Artikel hinterlegt; wenn Sie Testdaten anlegen wollen, klicken Sie bitte auf den folgenden Link:';
$lang['de_DE']['SilvercartSiteConfig']['TESTDATA_LINKTEXT']         = 'Zur Testdaten-Sektion springen';
$lang['de_DE']['SilvercartSiteConfig']['GOOGLE_ANALYTICS_TRACKING_CODE']    = 'Google Analytics Tracking Code';
$lang['de_DE']['SilvercartSiteConfig']['GOOGLE_WEBMASTER_CODE']             = 'Google Webmaster Tools Code';
$lang['de_DE']['SilvercartSiteConfig']['PIWIK_TRACKING_CODE']               = 'Piwik Tracking Code';
$lang['de_DE']['SilvercartSiteConfig']['FACEBOOK_LINK']                     = 'Facebook Link';
$lang['de_DE']['SilvercartSiteConfig']['TWITTER_LINK']                      = 'Twitter Link';
$lang['de_DE']['SilvercartSiteConfig']['XING_LINK']                         = 'Xing Link';

$lang['de_DE']['SiteConfig']['SITENAMEDEFAULT'] = 'SilverCart';
$lang['de_DE']['SiteConfig']['TAGLINEDEFAULT']  = 'eCommerce software. Open-source. You\'ll love it.';

$lang['de_DE']['TermsOfServicePage']['DEFAULT_TITLE']                           = $lang['de_DE']['SilvercartPage']['TITLE_TERMS'];
$lang['de_DE']['TermsOfServicePage']['DEFAULT_URLSEGMENT']                      = $lang['de_DE']['SilvercartPage']['URL_SEGMENT_TERMS'];

$lang['de_DE']['ImprintPage']['DEFAULT_TITLE']                                  = $lang['de_DE']['SilvercartPage']['TITLE_IMPRINT'];
$lang['de_DE']['ImprintPage']['DEFAULT_URLSEGMENT']                             = $lang['de_DE']['SilvercartPage']['URL_SEGMENT_IMPRINT'];

$lang['de_DE']['SilvercartDataPrivacyStatementPage']['DEFAULT_TITLE']           = $lang['de_DE']['SilvercartDataPrivacyStatementPage']['TITLE'];
$lang['de_DE']['SilvercartDataPrivacyStatementPage']['DEFAULT_URLSEGMENT']      = $lang['de_DE']['SilvercartDataPrivacyStatementPage']['URL_SEGMENT'];

$lang['de_DE']['SilvercartPriceType']['GROSS']                                  = 'Brutto';
$lang['de_DE']['SilvercartPriceType']['NET']                                    = 'Netto';

$lang['de_DE']['SilvercartEditableTableListField']['BATCH_OPTIONS_LABEL']       = 'Aktion auf Auswahl anwenden';
$lang['de_DE']['SilvercartEditableTableListField']['QUICK_ACCESS']              = 'Schnellansicht';

$lang['de_DE']['SilvercartDateRangePicker']['TODAY']                            = 'Heute';
$lang['de_DE']['SilvercartDateRangePicker']['LAST_7_DAYS']                      = 'Letzte 7 Tage';
$lang['de_DE']['SilvercartDateRangePicker']['THIS_MONTH']                       = 'Dieser Monat';
$lang['de_DE']['SilvercartDateRangePicker']['THIS_YEAR']                        = 'Dieses Jahr';
$lang['de_DE']['SilvercartDateRangePicker']['LAST_MONTH']                       = 'Vorheriger Monat';
$lang['de_DE']['SilvercartDateRangePicker']['DATE']                             = 'Datum';
$lang['de_DE']['SilvercartDateRangePicker']['ALL_BEFORE']                       = 'Alles vor';
$lang['de_DE']['SilvercartDateRangePicker']['ALL_AFTER']                        = 'Alles nach';
$lang['de_DE']['SilvercartDateRangePicker']['PERIOD']                           = 'Zeitraum';
$lang['de_DE']['SilvercartDateRangePicker']['START_DATE']                       = 'Startdatum';
$lang['de_DE']['SilvercartDateRangePicker']['END_DATE']                         = 'Enddatum';
$lang['de_DE']['SilvercartDateRangePicker']['NEXT']                             = 'Nächster';
$lang['de_DE']['SilvercartDateRangePicker']['PREVIOUS']                         = 'Vorheriger';