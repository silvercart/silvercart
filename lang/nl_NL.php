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
 * Dutch (NL) language pack
 *
 * @package Silvercart
 * @subpackage i18n
 * @ignore
 */
i18n::include_locale_file('silvercart', 'en_US');

global $lang;

if (array_key_exists('nl_NL', $lang) && is_array($lang['nl_NL'])) {
    $lang['nl_NL'] = array_merge($lang['en_US'], $lang['nl_NL']);
} else {
    $lang['nl_NL'] = $lang['en_US'];
}







$lang['nl_NL']['Silvercart']['CONTENT'] = 'Inhoud';
$lang['nl_NL']['Silvercart']['CROSSSELLING'] = 'Cross-Selling';
$lang['nl_NL']['Silvercart']['DATA'] = 'Data';
$lang['nl_NL']['Silvercart']['DEEPLINKS'] = 'Dieptelinks';
$lang['nl_NL']['Silvercart']['LINKS'] = 'Links';
$lang['nl_NL']['Silvercart']['MISC_CONFIG'] = 'Overige Configuraties';
$lang['nl_NL']['Silvercart']['TIMES'] = 'Tijd';
$lang['nl_NL']['Silvercart']['DATE'] = 'Datum';
$lang['nl_NL']['Silvercart']['DAY'] = 'dag';
$lang['nl_NL']['Silvercart']['DAYS'] = 'dagen';
$lang['nl_NL']['Silvercart']['WEEK'] = 'week';
$lang['nl_NL']['Silvercart']['WEEKS'] = 'weken';
$lang['nl_NL']['Silvercart']['MONTH'] = 'maand';
$lang['nl_NL']['Silvercart']['MONTHS'] = 'maanden';
$lang['nl_NL']['Silvercart']['MIN'] = 'minuut';
$lang['nl_NL']['Silvercart']['MINS'] = 'minuten';
$lang['nl_NL']['Silvercart']['SEC'] = 'seconde';
$lang['nl_NL']['Silvercart']['SECS'] = 'seconden';
$lang['nl_NL']['Silvercart']['MORE'] = 'Meer';
$lang['nl_NL']['Silvercart']['SEO'] = 'SEO';
$lang['nl_NL']['Silvercart']['YES'] = 'Ja';
$lang['nl_NL']['Silvercart']['NO'] = 'Nee';
$lang['nl_NL']['Silvercart']['PRINT'] = 'Afdrukken';

$lang['nl_NL']['SilvercartAddress']['InvoiceAddressAsShippingAddress'] = 'Gebruik factuuradres als verzendadres';
$lang['nl_NL']['SilvercartAddress']['ADDITION'] = 'Toevoeging';
$lang['nl_NL']['SilvercartAddress']['CITY'] = 'Woonplaats';
$lang['nl_NL']['SilvercartAddress']['EDITADDRESS'] = 'Bewerk adres';
$lang['nl_NL']['SilvercartAddress']['EDITINVOICEADDRESS'] = 'Bewerk factuur adres';
$lang['nl_NL']['SilvercartAddress']['EDITSHIPPINGADDRESS'] = 'Bewerk verzend adres';
$lang['nl_NL']['SilvercartAddress']['EMAIL'] = 'Email adres';
$lang['nl_NL']['SilvercartAddress']['EMAIL_CHECK'] = 'Herhalen';
$lang['nl_NL']['SilvercartAddress']['FIRSTNAME'] = 'Voornaam';
$lang['nl_NL']['SilvercartAddress']['MISSES'] = 'Mw';
$lang['nl_NL']['SilvercartAddress']['MISTER'] = 'Dhr';
$lang['nl_NL']['SilvercartAddress']['NO_ADDRESS_AVAILABLE'] = 'Geen adres beschikbaar';
$lang['nl_NL']['SilvercartAddress']['PHONE'] = 'Telefoon';
$lang['nl_NL']['SilvercartAddress']['PHONE_SHORT'] = 'Tel.';
$lang['nl_NL']['SilvercartAddress']['PHONEAREACODE'] = 'Netnummer';
$lang['nl_NL']['SilvercartAddress']['PLURALNAME'] = 'Adressen';
$lang['nl_NL']['SilvercartAddress']['POSTCODE'] = 'Postcode';
$lang['nl_NL']['SilvercartAddress']['SALUTATION'] = 'Titel';
$lang['nl_NL']['SilvercartAddress']['SINGULARNAME'] = 'adres';
$lang['nl_NL']['SilvercartAddress']['STREET'] = 'Straat';
$lang['nl_NL']['SilvercartAddress']['STREETNUMBER'] = 'Huisnummer';
$lang['nl_NL']['SilvercartAddress']['SURNAME'] = 'Achternaam';

$lang['nl_NL']['SilvercartAddressHolder']['ADD'] = 'Voeg nieuw adres toe';
$lang['nl_NL']['SilvercartAddressHolder']['ADDED_ADDRESS_SUCCESS'] = 'Uw adres is opgeslagen.';
$lang['nl_NL']['SilvercartAddressHolder']['ADDED_ADDRESS_FAILURE'] = 'Uw adres kan niet worden opgeslagen.';
$lang['nl_NL']['SilvercartAddressHolder']['ADDITIONALADDRESS'] = 'Ander adres';
$lang['nl_NL']['SilvercartAddressHolder']['ADDITIONALADDRESSES'] = 'Andere adressen';
$lang['nl_NL']['SilvercartAddressHolder']['ADDRESS_CANT_BE_DELETED'] = "Sorry, uw eigen adres kan niet worder verwijderd.";
$lang['nl_NL']['SilvercartAddressHolder']['ADDRESS_NOT_FOUND'] = 'Sorry, maar het opgegeven adres is niet gevonden.';
$lang['nl_NL']['SilvercartAddressHolder']['ADDRESS_SUCCESSFULLY_DELETED'] = 'Uw adres is verwijderd.';
$lang['nl_NL']['SilvercartAddressHolder']['CURRENT_DEFAULT_ADDRESSES'] = 'Uw standaard factuur- en verzendadres';
$lang['nl_NL']['SilvercartAddressHolder']['DEFAULT_TITLE'] = 'Adres overzicht';
$lang['nl_NL']['SilvercartAddressHolder']['DEFAULT_URLSEGMENT'] = 'adres-overzicht';
$lang['nl_NL']['SilvercartAddressHolder']['DEFAULT_INVOICE'] = 'Dit is uw factuuradres';
$lang['nl_NL']['SilvercartAddressHolder']['DEFAULT_SHIPPING'] = 'Dit is uw verzendadres';
$lang['nl_NL']['SilvercartAddressHolder']['DEFAULT_INVOICEADDRESS'] = 'Standaard factuuradres';
$lang['nl_NL']['SilvercartAddressHolder']['DEFAULT_SHIPPINGADDRESS'] = 'Standaard verzendadres';
$lang['nl_NL']['SilvercartAddressHolder']['DELETE'] = 'Verwijder';
$lang['nl_NL']['SilvercartAddressHolder']['EDIT'] = 'Bewerken';
$lang['nl_NL']['SilvercartAddressHolder']['EXCUSE_INVOICEADDRESS'] = 'Excuseer ons, u heeft nog geen factuuradres toegevoegd.';
$lang['nl_NL']['SilvercartAddressHolder']['EXCUSE_SHIPPINGADDRESS'] = 'Excuseer ons, u heeft nog geen verzendadres toegevoegd.';
$lang['nl_NL']['SilvercartAddressHolder']['INVOICEADDRESS'] = 'Factuuradres';
$lang['nl_NL']['SilvercartAddressHolder']['INVOICEADDRESS_TAB'] = 'Factuuradres';
$lang['nl_NL']['SilvercartAddressHolder']['INVOICEANDSHIPPINGADDRESS'] = 'Factuur-en verzendadres';
$lang['nl_NL']['SilvercartAddressHolder']['NOT_DEFINED'] = 'Nog niet bepaald';
$lang['nl_NL']['SilvercartAddressHolder']['PLURALNAME'] = 'Adres Houders';
$lang['nl_NL']['SilvercartAddressHolder']['SET_AS'] = 'Instellen als';
$lang['nl_NL']['SilvercartAddressHolder']['SET_DEFAULT_INVOICE'] = 'Instellen als factuuradres';
$lang['nl_NL']['SilvercartAddressHolder']['SET_DEFAULT_SHIPPING'] = 'Instellen als verzendadres';
$lang['nl_NL']['SilvercartAddressHolder']['SHIPPINGADDRESS'] = 'Verzendadres';
$lang['nl_NL']['SilvercartAddressHolder']['SHIPPINGADDRESS_TAB'] = 'Verzendadres';
$lang['nl_NL']['SilvercartAddressHolder']['SINGULARNAME'] = 'Adres Houder';
$lang['nl_NL']['SilvercartAddressHolder']['TITLE'] = 'Adres overzicht';
$lang['nl_NL']['SilvercartAddressHolder']['UPDATED_INVOICE_ADDRESS'] = 'Uw factuuradres is bijgewerkt.';
$lang['nl_NL']['SilvercartAddressHolder']['UPDATED_SHIPPING_ADDRESS'] = 'Uw verzendadres is bijgewerkt.';
$lang['nl_NL']['SilvercartAddressHolder']['URL_SEGMENT'] = 'adres-overzicht';

$lang['nl_NL']['SilvercartAddressPage']['DEFAULT_TITLE'] = 'Adresgegevens';
$lang['nl_NL']['SilvercartAddressPage']['DEFAULT_URLSEGMENT'] = 'adres-gegevens';
$lang['nl_NL']['SilvercartAddressPage']['PLURALNAME'] = 'Adres Pagina\'s';
$lang['nl_NL']['SilvercartAddressPage']['SINGULARNAME'] = 'Adres pagina';
$lang['nl_NL']['SilvercartAddressPage']['TITLE'] = 'adresgegevens';
$lang['nl_NL']['SilvercartAddressPage']['URL_SEGMENT'] = 'adres-gegevens';

$lang['nl_NL']['SilvercartAvailabilityStatus']['PLURALNAME'] = 'Beschikbaarheid';
$lang['nl_NL']['SilvercartAvailabilityStatus']['SINGULARNAME'] = 'Beschikbaarheid';
$lang['nl_NL']['SilvercartAvailabilityStatus']['TITLE'] = 'Titel';
$lang['nl_NL']['SilvercartAvailabilityStatus']['STATUS_AVAILABLE'] = 'beschikbaar';
$lang['nl_NL']['SilvercartAvailabilityStatus']['STATUS_NOT_AVAILABLE'] = 'niet beschikbaar';
$lang['nl_NL']['SilvercartAvailabilityStatus']['STATUS_AVAILABLE_IN'] = 'beschikbaar in %s %s';
$lang['nl_NL']['SilvercartAvailabilityStatus']['STATUS_AVAILABLE_IN_MIN_MAX'] = 'beschikbaar van %s tot %s %s';

$lang['nl_NL']['SilvercartDeeplinkAttribute']['PLURALNAME'] = 'Eigenschappen';
$lang['nl_NL']['SilvercartDeeplinkAttribute']['SINGULARNAME'] = 'Eigenschap';

$lang['nl_NL']['SilvercartGoogleMerchantTaxonomy']['LEVEL1']    = 'Niveau 1';
$lang['nl_NL']['SilvercartGoogleMerchantTaxonomy']['LEVEL2']    = 'Niveau 2';
$lang['nl_NL']['SilvercartGoogleMerchantTaxonomy']['LEVEL3']    = 'Niveau 3';
$lang['nl_NL']['SilvercartGoogleMerchantTaxonomy']['LEVEL4']    = 'Niveau 4';
$lang['nl_NL']['SilvercartGoogleMerchantTaxonomy']['LEVEL5']    = 'Niveau 5';
$lang['nl_NL']['SilvercartGoogleMerchantTaxonomy']['LEVEL6']    = 'Niveau 6';
$lang['nl_NL']['SilvercartGoogleMerchantTaxonomy']['SINGULARNAME'] = 'Google systematiek';
$lang['nl_NL']['SilvercartGoogleMerchantTaxonomy']['PLURALNAME']   = 'Google systematiek';

$lang['nl_NL']['SilvercartImageSliderImage']['LINKPAGE'] = 'Pagina die is gekoppeld aan';

$lang['nl_NL']['SilvercartImageSliderWidget']['TITLE']          = 'Diavoorstelling met afbeeldingen';
$lang['nl_NL']['SilvercartImageSliderWidget']['CMSTITLE']       = 'Diavoorstelling';
$lang['nl_NL']['SilvercartImageSliderWidget']['DESCRIPTION']    = 'Geeft een diavoorstelling voor het weergeven van meerdere afbeeldingen.';

$lang['nl_NL']['SilvercartMultiSelectAndOrderField']['ADD_CALLBACK_FIELD']      = 'Voeg terugbelveld toe';
$lang['nl_NL']['SilvercartMultiSelectAndOrderField']['ATTRIBUTED_FIELDS']       = 'Toegekende velden';
$lang['nl_NL']['SilvercartMultiSelectAndOrderField']['CSV_SEPARATOR_LABEL']     = 'CSV scheidingsteken';
$lang['nl_NL']['SilvercartMultiSelectAndOrderField']['FIELD_NAME']              = 'Veldnaam';
$lang['nl_NL']['SilvercartMultiSelectAndOrderField']['MOVE_DOWN']               = 'Naar beneden';
$lang['nl_NL']['SilvercartMultiSelectAndOrderField']['MOVE_UP']                 = 'Naar boven';
$lang['nl_NL']['SilvercartMultiSelectAndOrderField']['NOT_ATTRIBUTED_FIELDS']   = 'Geen toegekende velden';

$lang['nl_NL']['SilvercartNewsletter']['OPTIN_NOT_FINISHED_MESSAGE']        = 'U ontvangt een bericht dat u moet bevestigen, waarna u toegevoegd wordt aan onze nieuwsbrief.';
$lang['nl_NL']['SilvercartNewsletter']['SUBSCRIBED']                        = 'U bent geabonneerd op de nieuwsbrief';
$lang['nl_NL']['SilvercartNewsletter']['UNSUBSCRIBED']                      = 'U bent niet geabonneerd op de nieuwsbrief';
$lang['nl_NL']['SilvercartNewsletterPage']['DEFAULT_TITLE']                 = 'Nieuwsbrief';
$lang['nl_NL']['SilvercartNewsletterPage']['DEFAULT_URLSEGMENT']            = 'nieuwsbrief_nl_nl';
$lang['nl_NL']['SilvercartNewsletterPage']['TITLE']                         = 'Nieuwsbrief';
$lang['nl_NL']['SilvercartNewsletterPage']['URL_SEGMENT']                   = 'nieuwsbrief_nl_nl';
$lang['nl_NL']['SilvercartNewsletterResponsePage']['DEFAULT_TITLE']         = 'Nieuwsbrief Status';
$lang['nl_NL']['SilvercartNewsletterResponsePage']['DEFAULT_URLSEGMENT']    = 'nieuwsbrief_status_nl_nl';
$lang['nl_NL']['SilvercartNewsletterResponsePage']['TITLE']                 = 'Nieuwsbrief Status';
$lang['nl_NL']['SilvercartNewsletterResponsePage']['URL_SEGMENT']           = 'nieuwsbrief_status_nl_nl';
$lang['nl_NL']['SilvercartNewsletterResponsePage']['STATUS_TITLE']          = 'Uw nieuwsbrief instellingen';
$lang['nl_NL']['SilvercartNewsletterForm']['ACTIONFIELD_TITLE']             = 'Wat wilt u doen?';
$lang['nl_NL']['SilvercartNewsletterForm']['ACTIONFIELD_SUBSCRIBE']         = 'Ik wil mij inschrijven voor de nieuwsbrief';
$lang['nl_NL']['SilvercartNewsletterForm']['ACTIONFIELD_UNSUBSCRIBE']       = 'Ik wil me uitschrijven voor de nieuwsbrief';
$lang['nl_NL']['SilvercartNewsletterStatus']['ALREADY_SUBSCRIBED']          = 'Het e-mailadres "%s" is al ingeschreven.';
$lang['nl_NL']['SilvercartNewsletterStatus']['REGULAR_CUSTOMER_WITH_SAME_EMAIL_EXISTS'] = 'Er is al een geregistreerde klant met het e-mailadres "%s". Log eerst in om vervolgens met de voorkeursinstellingen voor de nieuwsbrief verder te gaan: <a href="%s"> Ga naar de login pagina </ a>.';
$lang['nl_NL']['SilvercartNewsletterStatus']['NO_EMAIL_FOUND']              = 'Wij kunnen het emailadres "%s" niet vinden.';
$lang['nl_NL']['SilvercartNewsletterStatus']['UNSUBSCRIBED_SUCCESSFULLY']   = 'Het e-mailadres "%s" is met succes uitgeschreven.';
$lang['nl_NL']['SilvercartNewsletterStatus']['SUBSCRIBED_SUCCESSFULLY']     = 'Het e-mailadres "%s" is met succes ingeschreven.';
$lang['nl_NL']['SilvercartNewsletterStatus']['SUBSCRIBED_SUCCESSFULLY_FOR_OPT_IN'] = 'Een e-mail is verzonden naar het adres "%s" met verdere instructies voor de bevestiging.';

$lang['nl_NL']['SilvercartNumberRange']['ACTUAL'] = 'Actueel';
$lang['nl_NL']['SilvercartNumberRange']['ACTUALCOUNT'] = 'Actueel';
$lang['nl_NL']['SilvercartNumberRange']['CUSTOMERNUMBER'] = 'Klantnummer';
$lang['nl_NL']['SilvercartNumberRange']['END'] = 'Einde';
$lang['nl_NL']['SilvercartNumberRange']['ENDCOUNT'] = 'Einde';
$lang['nl_NL']['SilvercartNumberRange']['IDENTIFIER'] = 'Identificatie';
$lang['nl_NL']['SilvercartNumberRange']['INVOICENUMBER'] = 'Factuurnummer';
$lang['nl_NL']['SilvercartNumberRange']['ORDERNUMBER'] = 'Ordernummer';
$lang['nl_NL']['SilvercartNumberRange']['PLURALNAME'] = 'Nummerreeksen';
$lang['nl_NL']['SilvercartNumberRange']['PREFIX'] = 'Voorvoegsel';
$lang['nl_NL']['SilvercartNumberRange']['SINGULARNAME'] = 'Nummerreeks';
$lang['nl_NL']['SilvercartNumberRange']['START'] = 'Start';
$lang['nl_NL']['SilvercartNumberRange']['STARTCOUNT'] = 'Start';
$lang['nl_NL']['SilvercartNumberRange']['SUFFIX'] = 'Achtervoegsel';
$lang['nl_NL']['SilvercartNumberRange']['TITLE'] = 'Titel';

$lang['nl_NL']['SilvercartProduct']['IS_ACTIVE'] = 'is actief';
$lang['nl_NL']['SilvercartProduct']['ADD_TO_CART'] = 'in Mandje';
$lang['nl_NL']['SilvercartProduct']['AMOUNT_UNIT'] = 'aankoop-eenheid';
$lang['nl_NL']['SilvercartProduct']['DEEPLINK_FOR'] = 'Dieptelink voor het attribuut "%s"';
$lang['nl_NL']['SilvercartProduct']['DEEPLINK_TEXT'] = 'Als er een dieptelink is gedefinieerd, worden alle gerelateerde producten zichtbaar';
$lang['nl_NL']['SilvercartProduct']['CHOOSE_MASTER'] = '-- kies Origineel --';
$lang['nl_NL']['SilvercartProduct']['COLUMN_TITLE'] = 'Naam';
$lang['nl_NL']['SilvercartProduct']['DESCRIPTION'] = 'Product omschrijving';
$lang['nl_NL']['SilvercartProduct']['EAN'] = 'EAN';
$lang['nl_NL']['SilvercartProduct']['STOCKQUANTITY'] = 'voorraad';
$lang['nl_NL']['SilvercartProduct']['FREE_OF_CHARGE'] = 'Gratis';
$lang['nl_NL']['SilvercartProduct']['IMAGE'] = 'Productfoto';
$lang['nl_NL']['SilvercartProduct']['IMAGE_NOT_AVAILABLE'] = 'Productfoto niet beschikbaar';
$lang['nl_NL']['SilvercartProduct']['IMPORTIMAGESFORM_ACTION'] = 'Foto importeren';
$lang['nl_NL']['SilvercartProduct']['IMPORTIMAGESFORM_ERROR_DIRECTORYNOTVALID'] = 'Map kon niet worden gevonden';
$lang['nl_NL']['SilvercartProduct']['IMPORTIMAGESFORM_ERROR_NOIMAGEDIRECTORYGIVEN'] = 'Geen map gespecificeerd';
$lang['nl_NL']['SilvercartProduct']['IMPORTIMAGESFORM_HEADLINE'] = 'Foto\'s achteraf importeren';
$lang['nl_NL']['SilvercartProduct']['IMPORTIMAGESFORM_IMAGEDIRECTORY'] = 'Map op de webserver ';
$lang['nl_NL']['SilvercartProduct']['IMPORTIMAGESFORM_IMAGEDIRECTORY_DESC'] = 'Absolute pad naar de map op de webserver waar de beelden zich bevinden (bv. /var/www/silvercart/images/)';
$lang['nl_NL']['SilvercartProduct']['IMPORTIMAGESFORM_REPORT'] = '<p>%d gevonden bestanden.</p><p>%d konden worden toegewezen aan producten en werden geïmporteerd.</p>';
$lang['nl_NL']['SilvercartProduct']['LIST_PRICE'] = 'catalogusprijs';
$lang['nl_NL']['SilvercartProduct']['MASTERPRODUCT'] = 'Hoofd artikel';
$lang['nl_NL']['SilvercartProduct']['METADATA'] = 'Meta Data';
$lang['nl_NL']['SilvercartProduct']['METADESCRIPTION'] = 'Meta omschrijving voor zoekmachines';
$lang['nl_NL']['SilvercartProduct']['METAKEYWORDS'] = 'Meta keywords voor zoekmachines';
$lang['nl_NL']['SilvercartProduct']['METATITLE'] = 'Meta titel voor zoekmachines';
$lang['nl_NL']['SilvercartProduct']['MSRP'] = 'Adviesprijs';
$lang['nl_NL']['SilvercartProduct']['NAME_DESCRIPTION'] = 'Naam & Omschrijving';
$lang['nl_NL']['SilvercartProduct']['PACKAGING_QUANTITY'] = 'Artikel(en) in verpakking';
$lang['nl_NL']['SilvercartProduct']['PACKAGING_UNIT'] = 'verpakkingseenheid';
$lang['nl_NL']['SilvercartProduct']['PLURALNAME'] = 'Producten';
$lang['nl_NL']['SilvercartProduct']['PRICE'] = 'Prijs';
$lang['nl_NL']['SilvercartProduct']['PRICE_GROSS'] = 'Prijs (bruto)';
$lang['nl_NL']['SilvercartProduct']['PRICE_NET'] = 'Prijs (netto)';
$lang['nl_NL']['SilvercartProduct']['PRICE_SINGLE'] = 'Eenheidsprijs';
$lang['nl_NL']['SilvercartProduct']['PRICE_SINGLE_NET'] = 'Eenheidsprijs (netto)';
$lang['nl_NL']['SilvercartProduct']['PRICE_ENTIRE'] = 'Totoaalprijs';
$lang['nl_NL']['SilvercartProduct']['PRODUCTNUMBER'] = 'Artikelnummer';
$lang['nl_NL']['SilvercartProduct']['PRODUCTNUMBER_SHORT'] = 'Artikelnr.';
$lang['nl_NL']['SilvercartProduct']['PRODUCTNUMBER_MANUFACTURER'] = 'Artikelnr. (fabrikant)';
$lang['nl_NL']['SilvercartProduct']['PURCHASEPRICE'] = 'Inkoop Prijs';
$lang['nl_NL']['SilvercartProduct']['PURCHASE_MIN_DURATION'] = 'Min. looptijd aankoop';
$lang['nl_NL']['SilvercartProduct']['PURCHASE_MAX_DURATION'] = 'Max. looptijd aankoop';
$lang['nl_NL']['SilvercartProduct']['PURCHASE_TIME_UNIT'] = 'Tijdeenheid aankoop';
$lang['nl_NL']['SilvercartProduct']['QUANTITY'] = 'Aantal';
$lang['nl_NL']['SilvercartProduct']['QUANTITY_SHORT'] = 'Aant.';
$lang['nl_NL']['SilvercartProduct']['SHORTDESCRIPTION'] = 'Lijst beschrijving';
$lang['nl_NL']['SilvercartProduct']['SINGULARNAME'] = 'Product';
$lang['nl_NL']['SilvercartProduct']['STOCK_QUANTITY'] = 'Is de voorraad van dit product te overboeken?';
$lang['nl_NL']['SilvercartProduct']['TITLE'] = 'Product';
$lang['nl_NL']['SilvercartProduct']['VAT'] = 'BTW';
$lang['nl_NL']['SilvercartProduct']['WEIGHT'] = 'Gewicht';

$lang['nl_NL']['SilvercartProductExport']['ACTIVATE_CSV_HEADERS']                           = 'Activeer CSV headers';
$lang['nl_NL']['SilvercartProductExport']['ATTRIBUTE_EXPORT_FIELDS_LABEL']                  = 'Stel export velden in';
$lang['nl_NL']['SilvercartProductExport']['CREATE_TIMESTAMP_FILE']                          = 'Maak tijdstempel bestand';
$lang['nl_NL']['SilvercartProductExport']['FIELD_ATTRIBUTED_EXPORT_FIELDS']                 = 'Toegekend export velden';
$lang['nl_NL']['SilvercartProductExport']['FIELD_AVAILABLE_EXPORT_FIELDS']                  = 'Beschikbare export velden';
$lang['nl_NL']['SilvercartProductExport']['FIELD_CSV_SEPARATOR']                            = 'CSV scheidingsteken';
$lang['nl_NL']['SilvercartProductExport']['IS_ACTIVE']                                      = 'Is actief';
$lang['nl_NL']['SilvercartProductExport']['FIELD_LAST_EXPORT_DATE_TIME']                    = 'Laatste export';
$lang['nl_NL']['SilvercartProductExport']['FIELD_NAME']                                     = 'Naam';
$lang['nl_NL']['SilvercartProductExport']['FIELD_PUSH_ENABLED']                             = 'Activeer overdracht';
$lang['nl_NL']['SilvercartProductExport']['FIELD_PUSH_TO_URL']                              = 'Overdracht naar URL';
$lang['nl_NL']['SilvercartProductExport']['FIELD_SELECT_ONLY_HEADLINE']                     = 'Exporteer alleen producten die';
$lang['nl_NL']['SilvercartProductExport']['FIELD_SELECT_ONLY_PRODUCTS_QUANTITY']            = 'meer dan';
$lang['nl_NL']['SilvercartProductExport']['FIELD_SELECT_ONLY_PRODUCTS_WITH_GOUP']           = 'zijn gekoppeld aan een productgroep';
$lang['nl_NL']['SilvercartProductExport']['FIELD_SELECT_ONLY_PRODUCTS_WITH_IMAGE']          = 'een product foto hebben';
$lang['nl_NL']['SilvercartProductExport']['FIELD_SELECT_ONLY_PRODUCTS_WITH_MANUFACTURER']   = 'zijn gekoppeld aan een fabrikant';
$lang['nl_NL']['SilvercartProductExport']['FIELD_SELECT_ONLY_PRODUCTS_WITH_QUANTITY']       = 'beschikbaar zijn in de volgende hoeveelheid';
$lang['nl_NL']['SilvercartProductExport']['FIELD_UPDATE_INTERVAL']                          = 'Update interval';
$lang['nl_NL']['SilvercartProductExport']['FIELD_UPDATE_INTERVAL_PERIOD']                   = 'Update periode';
$lang['nl_NL']['SilvercartProductExport']['PLURAL_NAME']                                    = 'Product exporters';
$lang['nl_NL']['SilvercartProductExport']['SINGULARNAME']                                   = 'Product exporter';
$lang['nl_NL']['SilvercartProductExporter']['PLURALNAME']                                   = 'Product exporters';
$lang['nl_NL']['SilvercartProductExporter']['SINGULARNAME']                                 = 'Product exporter';

$lang['nl_NL']['SilvercartProductExportAdmin']['PUSH_ENABLED_LABEL']                    = 'Overdracth aan';
$lang['nl_NL']['SilvercartProductExportAdmin']['UPDATE_INTERVAL_LABEL']                 = 'Update interval';
$lang['nl_NL']['SilvercartProductExportAdmin']['UPDATE_INTERVAL_PERIOD_LABEL']          = 'Update interval periode';
$lang['nl_NL']['SilvercartProductExportAdmin']['SILVERCART_PRODUCT_EXPORT_ADMIN_LABEL'] = 'SilverCart product export';
$lang['nl_NL']['SilvercartProductExportAdmin']['TAB_BASIC_SETTINGS']                    = 'Basis settings';
$lang['nl_NL']['SilvercartProductExportAdmin']['TAB_PRODUCT_SELECTION']                 = 'Geselecteerde producten';
$lang['nl_NL']['SilvercartProductExportAdmin']['TAB_EXPORT_FIELD_DEFINITIONS']          = 'CSV veld defenities';
$lang['nl_NL']['SilvercartProductExportAdmin']['TAB_HEADER_CONFIGURATION']              = 'CSV headers';

$lang['nl_NL']['SilvercartProductGroupHolder']['DEFAULT_TITLE']                     = 'Productgroepen';
$lang['nl_NL']['SilvercartProductGroupHolder']['DEFAULT_URLSEGMENT']                = 'productgroepen';
$lang['nl_NL']['SilvercartProductGroupHolder']['PAGE_TITLE']                        = 'Productgroepen';
$lang['nl_NL']['SilvercartProductGroupHolder']['PLURALNAME']                        = 'Productgroep Houders';
$lang['nl_NL']['SilvercartProductGroupHolder']['SHOW_PRODUCTS_WITH_COUNT_PLURAL']   = 'Laat %s producten zien';
$lang['nl_NL']['SilvercartProductGroupHolder']['SHOW_PRODUCTS_WITH_COUNT_SINGULAR'] = 'Laat %s producten zien';
$lang['nl_NL']['SilvercartProductGroupHolder']['SINGULARNAME']                      = 'Productgroep houder';
$lang['nl_NL']['SilvercartProductGroupHolder']['SUBGROUPS_OF']                      = 'Subgroepen van ';
$lang['nl_NL']['SilvercartProductGroupHolder']['URL_SEGMENT']                       = 'productgroepen';

$lang['nl_NL']['SilvercartProductGroupMirrorPage']['SINGULARNAME']  = 'Gekoppelde-Productgroep';
$lang['nl_NL']['SilvercartProductGroupMirrorPage']['PLURALNAME']    = 'Gekoppelde-Productgroepen';

$lang['nl_NL']['SilvercartProductGroupPage']['ATTRIBUTES'] = 'Eigenschappen';
$lang['nl_NL']['SilvercartProductGroupPage']['GROUP_PICTURE'] = 'Groep afbeelding';
$lang['nl_NL']['SilvercartProductGroupPage']['MANUFACTURER_LINK'] = 'fabrikant';
$lang['nl_NL']['SilvercartProductGroupPage']['PLURALNAME'] = 'Product groepen';
$lang['nl_NL']['SilvercartProductGroupPage']['PRODUCTSPERPAGE'] = 'Producten per pagina';
$lang['nl_NL']['SilvercartProductGroupPage']['PRODUCTGROUPSPERPAGE'] = 'Productgroepen per pagina';
$lang['nl_NL']['SilvercartProductGroupPage']['SINGULARNAME'] = 'Product groep';
$lang['nl_NL']['SilvercartProductGroupPage']['USE_CONTENT_FROM_PARENT'] = 'Gebruik de inhoud van de bovenliggende pagina\'s';

$lang['nl_NL']['SilvercartProductGroupPageSelector']['OK'] = 'Ok';
$lang['nl_NL']['SilvercartProductGroupPageSelector']['PRODUCTS_PER_PAGE'] = 'Producten per pagina';

$lang['nl_NL']['SilvercartProductImageGallery']['PLURALNAME'] = 'Galerijen';
$lang['nl_NL']['SilvercartProductImageGallery']['SINGULARNAME'] = 'Galerij';

$lang['nl_NL']['SilvercartProductPage']['ADD_TO_CART'] = 'Voeg toe aan winkelwagen';
$lang['nl_NL']['SilvercartProductPage']['OUT_OF_STOCK'] = 'Dit product is niet op voorraad.';
$lang['nl_NL']['SilvercartProductPage']['PACKAGING_CONTENT'] = 'Inhoud';
$lang['nl_NL']['SilvercartProductPage']['PLURALNAME'] = 'Productdetails pagina\'s';
$lang['nl_NL']['SilvercartProductPage']['QUANTITY'] = 'Hoeveelheid';
$lang['nl_NL']['SilvercartProductPage']['SINGULARNAME'] = 'Productdetails pagina';
$lang['nl_NL']['SilvercartProductPage']['URL_SEGMENT'] = 'Productdetails';

$lang['nl_NL']['SilvercartProductTexts']['PLURALNAME'] = 'Product vertalingen';
$lang['nl_NL']['SilvercartProductTexts']['SINGULARNAME'] = 'Product vertaling';

$lang['nl_NL']['SilvercartCarrier']['ATTRIBUTED_SHIPPINGMETHODS'] = 'Toegekende verzendmethoden';
$lang['nl_NL']['SilvercartCarrier']['FULL_NAME'] = 'Volledige naam';
$lang['nl_NL']['SilvercartCarrier']['PLURALNAME'] = 'Vervoerders';
$lang['nl_NL']['SilvercartCarrier']['SINGULARNAME'] = 'Vervoerder';

$lang['nl_NL']['SilvercartCartPage']['DEFAULT_TITLE']                           = 'Winkelwagen';
$lang['nl_NL']['SilvercartCartPage']['DEFAULT_URLSEGMENT']                      = 'winkelwagen';
$lang['nl_NL']['SilvercartCartPage']['CART_EMPTY']                              = 'Uw winkelwagen is nog leeg.';
$lang['nl_NL']['SilvercartCartPage']['PLURALNAME']                              = 'Winkelwagen pagina\'s';
$lang['nl_NL']['SilvercartCartPage']['SINGULARNAME']                            = 'Winkelwagen pagina';
$lang['nl_NL']['SilvercartCartPage']['URL_SEGMENT']                             = 'winkelwagen';

$lang['nl_NL']['SilvercartCheckoutFormStep']['CHOOSEN_PAYMENT'] = 'Gekozen betaalmethode';
$lang['nl_NL']['SilvercartCheckoutFormStep']['CHOOSEN_SHIPPING'] = 'Gekozen verzendmethode';
$lang['nl_NL']['SilvercartCheckoutFormStep']['FORWARD'] = 'Volgende';
$lang['nl_NL']['SilvercartCheckoutFormStep']['I_ACCEPT_REVOCATION'] = 'Ik ga akkoord met de nietigverklaring';
$lang['nl_NL']['SilvercartCheckoutFormStep']['I_ACCEPT_TERMS'] = 'Ik ga akkoord met de voorwaarden.';
$lang['nl_NL']['SilvercartCheckoutFormStep']['I_SUBSCRIBE_NEWSLETTER'] = 'Ik wil de nieuwsbrief ontvangen';
$lang['nl_NL']['SilvercartCheckoutFormStep']['ORDER'] = 'Bestelling';
$lang['nl_NL']['SilvercartCheckoutFormStep']['ORDER_NOW'] = 'Bestellen';
$lang['nl_NL']['SilvercartCheckoutFormStep']['OVERVIEW'] = 'Overzicht';

$lang['nl_NL']['SilvercartCheckoutFormStep1']['LOGIN'] = 'Login';
$lang['nl_NL']['SilvercartCheckoutFormStep1']['NEWCUSTOMER'] = 'Bent u een nieuwe klant?';
$lang['nl_NL']['SilvercartCheckoutFormStep1']['PROCEED_WITH_REGISTRATION'] = 'Ja, ik wil mij registreren.';
$lang['nl_NL']['SilvercartCheckoutFormStep1']['PROCEED_WITHOUT_REGISTRATION'] = 'Nee, ik wil mij niet registreren.';
$lang['nl_NL']['SilvercartCheckoutFormStep1']['REGISTERTEXT'] = '<h3>Wilt u zich registreren?</h3><ul><li>U kan bij een volgende aankop uw gegevens opnieuw gebruiken</li><li>Actuele informatie over uw bestelling inzien</li><li>Verschillende adressen opgeven en bewaren</li><li>Uw persoonlijke gegevens aanpassen</li></ul><br/><br/></p>';
$lang['nl_NL']['SilvercartCheckoutFormStep1']['TITLE'] = 'Registratie';
$lang['nl_NL']['SilvercartCheckoutFormStep1LoginForm']['TITLE'] = 'Log in en ga verder';
$lang['nl_NL']['SilvercartCheckoutFormStep1NewCustomerForm']['CONTINUE_WITH_CHECKOUT'] = 'Ga verder met Afrekenen';
$lang['nl_NL']['SilvercartCheckoutFormStep1NewCustomerForm']['OPTIN_TEMP_TEXT'] = 'Na het activeren van uw registratie krijgt u een link om door te gaan met Afrekenen';
$lang['nl_NL']['SilvercartCheckoutFormStep1NewCustomerForm']['TITLE'] = 'Doorgaan';
$lang['nl_NL']['SilvercartCheckoutFormStep2']['EMPTYSTRING_COUNTRY'] = '--land--';
$lang['nl_NL']['SilvercartCheckoutFormStep2']['TITLE'] = 'Adressen';
$lang['nl_NL']['SilvercartCheckoutFormStep2']['ERROR_ADDRESS_NOT_FOUND'] = 'Het opgegeven adres kon niet worden gevonden.';
$lang['nl_NL']['SilvercartCheckoutFormStep3']['EMPTYSTRING_SHIPPINGMETHOD'] = '--kies verzendmethode--';
$lang['nl_NL']['SilvercartCheckoutFormStep3']['TITLE'] = 'Verzending';
$lang['nl_NL']['SilvercartCheckoutFormStep4']['CHOOSE_PAYMENT_METHOD'] = 'Ik wil betalen met %s';
$lang['nl_NL']['SilvercartCheckoutFormStep4']['EMPTYSTRING_PAYMENTMETHOD'] = '--kies betaalmethode--';
$lang['nl_NL']['SilvercartCheckoutFormStep4']['FIELDLABEL'] = 'Kies de gewenste betaalmethode:';
$lang['nl_NL']['SilvercartCheckoutFormStep4']['TITLE'] = 'Betalen';
$lang['nl_NL']['SilvercartCheckoutFormStep5']['TITLE'] = 'Overzicht';

$lang['nl_NL']['SilvercartCheckoutStep']['DEFAULT_TITLE'] = 'Afrekenen';
$lang['nl_NL']['SilvercartCheckoutStep']['DEFAULT_URLSEGMENT'] = 'afrekenen';
$lang['nl_NL']['SilvercartCheckoutStep']['BACK_TO_SHOPPINGCART'] = 'Terug naar de winkelwagen';
$lang['nl_NL']['SilvercartCheckoutStep']['PLURALNAME'] = 'Afrekenen Stappen';
$lang['nl_NL']['SilvercartCheckoutStep']['SINGULARNAME'] = 'Afrekenen Stap';
$lang['nl_NL']['SilvercartCheckoutStep']['URL_SEGMENT'] = 'afrekenen';

$lang['nl_NL']['SilvercartConfig']['ADDTOCARTMAXQUANTITY'] = 'Maximaal toegestane hoeveelheid van een enkel product in de winkelwagen';
$lang['nl_NL']['SilvercartConfig']['ADD_EXAMPLE_DATA'] = 'Voorbeeld data toevoegen';
$lang['nl_NL']['SilvercartConfig']['ADD_EXAMPLE_DATA_DESCRIPTION'] = 'De actie "Toevoegen Voorbeeld Data" zal een voorbeeld fabrikant en vier productgroepen met 50 producten.<br/><strong>LET OP: Deze actie kan een paar minuten duren!</strong>';
$lang['nl_NL']['SilvercartConfig']['ADD_EXAMPLE_CONFIGURATION'] = 'Voorbeeldconfiguratie toevoegen';
$lang['nl_NL']['SilvercartConfig']['ADD_EXAMPLE_CONFIGURATION_DESCRIPTION'] = 'De actie "Toevoegen Voorbeeld Configuratie" zal SilverCart configureren. Daarna kan het betalingsproces compleet worden uitgevoerd. De gegevens die moeten worden geconfigureerd zijn: betaling optie, vervoerder, verzend optie, verzendkosten, de activering van een land en zijn relatie tot een zone.<br/><strong>LET OP: Deze actie kan een paar minuten duren!</strong>';
$lang['nl_NL']['SilvercartConfig']['ADDED_EXAMPLE_DATA'] = 'Voorbeeld data toegevoegd';
$lang['nl_NL']['SilvercartConfig']['ADDED_EXAMPLE_CONFIGURATION'] = 'Voorbeeld Configuratie toegevoegd';
$lang['nl_NL']['SilvercartConfig']['APACHE_SOLR_PORT'] = 'Poort voor aanvragen tot Apache Solr';
$lang['nl_NL']['SilvercartConfig']['APACHE_SOLR_URL'] = 'URL voor verzoeken om Apache Solr';
$lang['nl_NL']['SilvercartConfig']['ALLOW_CART_WEIGHT_TO_BE_ZERO'] = 'Toestaan winkelwagen gewicht nul te zijn.';
$lang['nl_NL']['SilvercartConfig']['CLEAN'] = 'Optimalisatie';
$lang['nl_NL']['SilvercartConfig']['CLEAN_DATABASE'] = 'Optimaliseer databank';
$lang['nl_NL']['SilvercartConfig']['CLEAN_DATABASE_START_INDEX'] = 'StartIndexx';
$lang['nl_NL']['SilvercartConfig']['CLEAN_DATABASE_DESCRIPTION'] = 'De actie "Optimize database" zoekt vernietigd DataObjects en probeert opnieuw toe te wijzen zijn. In geval van een tekortkoming wordt het object worden verwijderd.<br/><strong>LET OP: Deze actie kan een paar minuten duren!</strong>';
$lang['nl_NL']['SilvercartConfig']['CLEAN_DATABASE_INPROGRESS'] = 'Optimalisatie in uitvoering... (%s/%s) (%s%% voltooid, %s resterend)';
$lang['nl_NL']['SilvercartConfig']['CLEANED_DATABASE'] = 'Database is geoptimaliseerd.';
$lang['nl_NL']['SilvercartConfig']['CLEANED_DATABASE_REPORT'] = '<br/><hr/><br/><h3>%s</h3><strong><br/>%s beelden werden verwijderd.<br/>&nbsp;&nbsp;%s als gevolg van een valse product relatie<br/>&nbsp;&nbsp;%s als gevolg van een ontbrekende image-bestand<br/>&nbsp;&nbsp;%s als gevolg van een vernielde beeld relatie<br/>%s beelden werden opnieuw toegewezen.</strong><br/><br/><hr/>';
$lang['nl_NL']['SilvercartConfig']['DEFAULTCURRENCY'] = 'Standaard valuta';
$lang['nl_NL']['SilvercartConfig']['DEFAULT_IMAGE'] = 'Standaard product afbeelding';
$lang['nl_NL']['SilvercartConfig']['DEMAND_BIRTHDAY_DATE_ON_REGISTRATION'] = 'Vereisen verjaardag datum bij registratie?';
$lang['nl_NL']['SilvercartConfig']['DISPLAY_TYPE_OF_PRODUCT_ADMIN'] = 'Weergavetype van product administratie';
$lang['nl_NL']['SilvercartConfig']['EMAILSENDER'] = 'E-mail afzender';
$lang['nl_NL']['SilvercartConfig']['ENABLESSL'] = 'Schakel SSL in';
$lang['nl_NL']['SilvercartConfig']['ENABLESTOCKMANAGEMENT'] = 'Inschakelen voorraadbeheer';
$lang['nl_NL']['SilvercartConfig']['EXAMPLE_DATA_ALREADY_ADDED'] = 'Voorbeeld van gegevens al toegevoegd';
$lang['nl_NL']['SilvercartConfig']['EXAMPLE_CONFIGURATION_ALREADY_ADDED'] = 'Voorbeeld configuratie al toegevoegd';
$lang['nl_NL']['SilvercartConfig']['GENERAL'] = 'Algemeen';
$lang['nl_NL']['SilvercartConfig']['GENERAL_MAIN'] = 'Algemene';
$lang['nl_NL']['SilvercartConfig']['GENERAL_TEST_DATA'] = 'Test Data';
$lang['nl_NL']['SilvercartConfig']['GEONAMES_DESCRIPTION'] = '<h3>Omschrijving</h3><p>Geonames geeft een gedetailleerde database van geo-informatie. Het kan worden gebruikt om actuele informatie land te verkrijgen (name, ISO2, ISO3, etc.).<br/> m deze functie te gebruiken, moet u een account maken op <a href="http://www.geonames.org/" target="blank">http://www.geonames.org/</a>, bevestigen van de registratie en activering van de webservice.<br/> Stel vervolgens geonames om actief te zijn, zet je gebruikersnaam in het veld en sla de configuratie op.<br/>Daarna zal SilverCart de landen synchroniseren  met de geonames database bij elke /dev/build, eventueel in meerdere talen.</p>';
$lang['nl_NL']['SilvercartConfig']['GEONAMES_ACTIVE'] = 'Activeer GeoNames';
$lang['nl_NL']['SilvercartConfig']['GEONAMES_USERNAME'] = 'GeoNames gebruikersnaam';
$lang['nl_NL']['SilvercartConfig']['GEONAMES_API'] = 'GeoNames API URL';
$lang['nl_NL']['SilvercartConfig']['INTERFACES'] = 'Interfaces';
$lang['nl_NL']['SilvercartConfig']['INTERFACES_GEONAMES'] = 'GeoNames';
$lang['nl_NL']['SilvercartConfig']['LAYOUT'] = 'Lay-out';
$lang['nl_NL']['SilvercartConfig']['PRICETYPEANONYMOUSCUSTOMERS'] = 'Prijs type voor anonieme klanten';
$lang['nl_NL']['SilvercartConfig']['PRICETYPEREGULARCUSTOMERS'] = 'Prijs type voor vaste klanten';
$lang['nl_NL']['SilvercartConfig']['PRICETYPEBUSINESSCUSTOMERS'] = 'Prijs type voor zakelijke klanten';
$lang['nl_NL']['SilvercartConfig']['PRICETYPEADMINS'] = 'Prijs type voor beheerders';
$lang['nl_NL']['SilvercartConfig']['EMAILSENDER_INFO'] = 'Het verzend emailadres is het emailadres van alle email berichten verstuurd door SilverCart.';
$lang['nl_NL']['SilvercartConfig']['ERROR_TITLE'] = 'Er is een fout opgetreden!';
$lang['nl_NL']['SilvercartConfig']['ERROR_MESSAGE'] = 'Vereiste configuratie voor "%s" ontbreekt.<br/>Gelieve <a href="%sadmin/silvercart-configuration/">log in</a> en kies "SC Config -> algemene configuratie" om het ontbrekende veld te bewerken.';
$lang['nl_NL']['SilvercartConfig']['ERROR_MESSAGE_NO_ACTIVATED_COUNTRY'] = 'Geen actief land gevonden. Gelieve <a href="%s/admin/silvercart-configuration/">log in</a> and choose "SC Config -> landen" om een land te activeren.';
$lang['nl_NL']['SilvercartConfig']['GLOBALEMAILRECIPIENT'] = 'Globale e-mail ontvanger';
$lang['nl_NL']['SilvercartConfig']['GLOBALEMAILRECIPIENT_INFO'] = 'De globale e-mail ontvanger kan naar keuze worden ingesteld. De globale e-mail ontvanger krijgt alle e-mails verstuurd door SilverCart (bestel notificaties, contact emails, etc.). De geadresseerden op de e-mail templates worden niet vervangen, maar toegevoegd.';
$lang['nl_NL']['SilvercartConfig']['MINIMUMORDERVALUE'] = 'Minimale orderwaarde';
$lang['nl_NL']['SilvercartConfig']['PLURALNAME'] = 'Algemene configuraties';
$lang['nl_NL']['SilvercartConfig']['PRICETYPE_ANONYMOUS'] = 'Prijs type voor anonieme klanten';
$lang['nl_NL']['SilvercartConfig']['PRICETYPE_REGULAR'] = 'Prijs type voor vaste klanten';
$lang['nl_NL']['SilvercartConfig']['PRICETYPE_BUSINESS'] = 'Prijs type voor zakelijke klanten';
$lang['nl_NL']['SilvercartConfig']['PRICETYPE_ADMINS'] = 'Prijs type voor beheerders';
$lang['nl_NL']['SilvercartConfig']['PRICETYPES_HEADLINE'] = 'Prijs types';
$lang['nl_NL']['SilvercartConfig']['PRODUCTSPERPAGE'] = 'Producten per pagina';
$lang['nl_NL']['SilvercartConfig']['PRODUCTSPERPAGE_ALL'] = 'Toon alle';
$lang['nl_NL']['SilvercartConfig']['PRODUCTGROUPSPERPAGE'] = 'Productgroepen per pagina';
$lang['nl_NL']['SilvercartConfig']['REDIRECTTOCARTAFTERADDTOCART'] = 'Doorsturen klant naar winkelwagen na "Toevoegen winkelwagen actie';
$lang['nl_NL']['SilvercartConfig']['SEARCH'] = 'Zoeken';
$lang['nl_NL']['SilvercartConfig']['SERVER'] = 'Server';
$lang['nl_NL']['SilvercartConfig']['SINGULARNAME'] = 'Algemene configuratie';
$lang['nl_NL']['SilvercartConfig']['SHOW_CONFIG'] = 'Toon configuratie';
$lang['nl_NL']['SilvercartConfig']['STOCK'] = 'Voorraad';
$lang['nl_NL']['SilvercartConfig']['TABBED'] = 'met tabbladen';
$lang['nl_NL']['SilvercartConfig']['FLAT'] = 'plat';
$lang['nl_NL']['SilvercartConfig']['QUANTITY_OVERBOOKABLE'] = 'Is de voorraad hoeveelheid van een product over het algemeen overboekbaar?';
$lang['nl_NL']['SilvercartConfig']['USE_APACHE_SOLR_SEARCH'] = 'Gebruik Apache Solr zoeken';
$lang['nl_NL']['SilvercartConfig']['USEMINIMUMORDERVALUE'] = 'Activeer minimale orderwaarde';
$lang['nl_NL']['SilvercartConfig']['DISREGARD_MINIMUM_ORDER_VALUE'] = 'Negeer minimale orderwaarde';
$lang['nl_NL']['SilvercartConfig']['MINIMUMORDERVALUE_HEADLINE'] = 'Minimale orderwaarde';

$lang['nl_NL']['SilvercartContactFormPage']['DEFAULT_TITLE'] = 'Contact';
$lang['nl_NL']['SilvercartContactFormPage']['DEFAULT_URLSEGMENT'] = 'contact';
$lang['nl_NL']['SilvercartContactFormPage']['PLURALNAME'] = 'Contactformulier pagina\'s';
$lang['nl_NL']['SilvercartContactFormPage']['REQUEST'] = 'Vraag via het contact formulier';
$lang['nl_NL']['SilvercartContactFormPage']['SINGULARNAME'] = 'Contactformulier pagina';
$lang['nl_NL']['SilvercartContactFormPage']['TITLE'] = 'Contact';
$lang['nl_NL']['SilvercartContactFormPage']['URL_SEGMENT'] = 'contact';

$lang['nl_NL']['SilvercartContactFormResponsePage']['DEFAULT_TITLE'] = 'Contact bevestiging';
$lang['nl_NL']['SilvercartContactFormResponsePage']['DEFAULT_CONTENT'] = 'Hartelijk dank voor uw bericht. Uw aanvraag zal zo snel mogelijk worden beantwoord.';
$lang['nl_NL']['SilvercartContactFormResponsePage']['DEFAULT_URLSEGMENT'] = 'contactbevestiging';
$lang['nl_NL']['SilvercartContactFormResponsePage']['CONTACT_CONFIRMATION'] = 'Contact bevestiging';
$lang['nl_NL']['SilvercartContactFormResponsePage']['CONTENT'] = 'Hartelijk dank voor uw bericht. Uw aanvraag zal zo snel mogelijk worden beantwoord.';
$lang['nl_NL']['SilvercartContactFormResponsePage']['PLURALNAME'] = 'Contact formulier reactie\'s';
$lang['nl_NL']['SilvercartContactFormResponsePage']['SINGULARNAME'] = 'Contact formulier reactie';
$lang['nl_NL']['SilvercartContactFormResponsePage']['URL_SEGMENT'] = 'contactbevestiging';

$lang['nl_NL']['SilvercartContactMessage']['PLURALNAME'] = 'Contact berichten';
$lang['nl_NL']['SilvercartContactMessage']['SINGULARNAME'] = 'Contact bericht';
$lang['nl_NL']['SilvercartContactMessage']['MESSAGE'] = 'bericht';
$lang['nl_NL']['SilvercartContactMessage']['TEXT'] = "<h1>Vraag via het contact formulier</h1>\n<h2>Hallo,</h2>\n<p>De klant <strong>\"\$FirstName \$Surname\"</strong> met het e-mailadres <strong>\"\$Email\"</strong> stuurde het volgende bericht:<br/>\n\n\$Message</p>\n";

$lang['nl_NL']['SilvercartContactMessageAdmin']['MENU_TITLE'] = 'Contact berichten';

$lang['nl_NL']['SilvercartCountry']['ACTIVE'] = 'Actief';
$lang['nl_NL']['SilvercartCountry']['ATTRIBUTED_PAYMENTMETHOD'] = 'Toegewezen betaalmethode';
$lang['nl_NL']['SilvercartCountry']['ATTRIBUTED_ZONES'] = 'Toegewezen zones';
$lang['nl_NL']['SilvercartCountry']['CONTINENT'] = 'Werelddeel';
$lang['nl_NL']['SilvercartCountry']['CURRENCY'] = 'Valuta';
$lang['nl_NL']['SilvercartCountry']['FIPS'] = 'FIPS code';
$lang['nl_NL']['SilvercartCountry']['ISO2'] = 'ISO Alpha2';
$lang['nl_NL']['SilvercartCountry']['ISO3'] = 'ISO Alpha3';
$lang['nl_NL']['SilvercartCountry']['ISON'] = 'ISO numeric';
$lang['nl_NL']['SilvercartCountry']['PLURALNAME'] = 'Landen';
$lang['nl_NL']['SilvercartCountry']['SINGULARNAME'] = 'Land';

$lang['nl_NL']['SilvercartCountry']['TITLE_AD'] = 'Andorra';
$lang['nl_NL']['SilvercartCountry']['TITLE_AE'] = 'Verenigde Arabische Emiraten';
$lang['nl_NL']['SilvercartCountry']['TITLE_AF'] = 'Afghanistan';
$lang['nl_NL']['SilvercartCountry']['TITLE_AG'] = 'Antigua en Barbuda';
$lang['nl_NL']['SilvercartCountry']['TITLE_AI'] = 'Anguilla';
$lang['nl_NL']['SilvercartCountry']['TITLE_AL'] = 'Albanië';
$lang['nl_NL']['SilvercartCountry']['TITLE_AM'] = 'Armenië';
$lang['nl_NL']['SilvercartCountry']['TITLE_AN'] = 'Nederlandse Antillen';
$lang['nl_NL']['SilvercartCountry']['TITLE_AO'] = 'Angola';
$lang['nl_NL']['SilvercartCountry']['TITLE_AQ'] = 'Antarctica';
$lang['nl_NL']['SilvercartCountry']['TITLE_AR'] = 'Argentinië';
$lang['nl_NL']['SilvercartCountry']['TITLE_AS'] = 'Amerikaans-Samoa';
$lang['nl_NL']['SilvercartCountry']['TITLE_AT'] = 'Oostenrijk';
$lang['nl_NL']['SilvercartCountry']['TITLE_AU'] = 'Australië';
$lang['nl_NL']['SilvercartCountry']['TITLE_AW'] = 'Aruba';
$lang['nl_NL']['SilvercartCountry']['TITLE_AX'] = 'Åland-eilanden';
$lang['nl_NL']['SilvercartCountry']['TITLE_AZ'] = 'Azerbaijan';
$lang['nl_NL']['SilvercartCountry']['TITLE_BA'] = 'Bosnië-Herzegovina';
$lang['nl_NL']['SilvercartCountry']['TITLE_BB'] = 'Barbados';
$lang['nl_NL']['SilvercartCountry']['TITLE_BD'] = 'Bangladesh';
$lang['nl_NL']['SilvercartCountry']['TITLE_BE'] = 'België';
$lang['nl_NL']['SilvercartCountry']['TITLE_BF'] = 'Burkina Faso';
$lang['nl_NL']['SilvercartCountry']['TITLE_BG'] = 'Bulgarije';
$lang['nl_NL']['SilvercartCountry']['TITLE_BH'] = 'Bahrein';
$lang['nl_NL']['SilvercartCountry']['TITLE_BI'] = 'Boeroendi';
$lang['nl_NL']['SilvercartCountry']['TITLE_BJ'] = 'Benin';
$lang['nl_NL']['SilvercartCountry']['TITLE_BL'] = 'Saint Barthélemy';
$lang['nl_NL']['SilvercartCountry']['TITLE_BM'] = 'Bermuda';
$lang['nl_NL']['SilvercartCountry']['TITLE_BN'] = 'Brunei';
$lang['nl_NL']['SilvercartCountry']['TITLE_BO'] = 'Bolivia';
$lang['nl_NL']['SilvercartCountry']['TITLE_BQ'] = 'Bonaire, Sint Eustatius en Saba';
$lang['nl_NL']['SilvercartCountry']['TITLE_BR'] = 'Brazilië';
$lang['nl_NL']['SilvercartCountry']['TITLE_BS'] = 'Bahamas';
$lang['nl_NL']['SilvercartCountry']['TITLE_BT'] = 'Bhutan';
$lang['nl_NL']['SilvercartCountry']['TITLE_BV'] = 'Bouvet Island';
$lang['nl_NL']['SilvercartCountry']['TITLE_BW'] = 'Botswana';
$lang['nl_NL']['SilvercartCountry']['TITLE_BY'] = 'Wit-Rusland';
$lang['nl_NL']['SilvercartCountry']['TITLE_BZ'] = 'Belize';
$lang['nl_NL']['SilvercartCountry']['TITLE_CA'] = 'Canada';
$lang['nl_NL']['SilvercartCountry']['TITLE_CC'] = 'Cocos [Keeling] Islands';
$lang['nl_NL']['SilvercartCountry']['TITLE_CD'] = 'Congo [DRC]';
$lang['nl_NL']['SilvercartCountry']['TITLE_CF'] = 'Centraal-Afrikaanse Republiek';
$lang['nl_NL']['SilvercartCountry']['TITLE_CG'] = 'Congo [Republic]';
$lang['nl_NL']['SilvercartCountry']['TITLE_CH'] = 'Zwitserland';
$lang['nl_NL']['SilvercartCountry']['TITLE_CI'] = 'Ivoorkust';
$lang['nl_NL']['SilvercartCountry']['TITLE_CK'] = 'Cook Eilanden';
$lang['nl_NL']['SilvercartCountry']['TITLE_CL'] = 'Chili';
$lang['nl_NL']['SilvercartCountry']['TITLE_CM'] = 'Kameroen';
$lang['nl_NL']['SilvercartCountry']['TITLE_CN'] = 'China';
$lang['nl_NL']['SilvercartCountry']['TITLE_CO'] = 'Colombia';
$lang['nl_NL']['SilvercartCountry']['TITLE_CR'] = 'Costa Rica';
$lang['nl_NL']['SilvercartCountry']['TITLE_CS'] = 'Servië en Montenegro';
$lang['nl_NL']['SilvercartCountry']['TITLE_CU'] = 'Cuba';
$lang['nl_NL']['SilvercartCountry']['TITLE_CV'] = 'Kaapverdië';
$lang['nl_NL']['SilvercartCountry']['TITLE_CW'] = 'Curacao';
$lang['nl_NL']['SilvercartCountry']['TITLE_CX'] = 'Christmas Island';
$lang['nl_NL']['SilvercartCountry']['TITLE_CY'] = 'Cyprus';
$lang['nl_NL']['SilvercartCountry']['TITLE_CZ'] = 'Tsjechische Republiek';
$lang['nl_NL']['SilvercartCountry']['TITLE_DE'] = 'Duitsland';
$lang['nl_NL']['SilvercartCountry']['TITLE_DJ'] = 'Djibouti';
$lang['nl_NL']['SilvercartCountry']['TITLE_DK'] = 'Denemarken';
$lang['nl_NL']['SilvercartCountry']['TITLE_DM'] = 'Dominica';
$lang['nl_NL']['SilvercartCountry']['TITLE_DO'] = 'Dominicaanse Republiek';
$lang['nl_NL']['SilvercartCountry']['TITLE_DZ'] = 'Algerije';
$lang['nl_NL']['SilvercartCountry']['TITLE_EC'] = 'Ecuador';
$lang['nl_NL']['SilvercartCountry']['TITLE_EE'] = 'Estland';
$lang['nl_NL']['SilvercartCountry']['TITLE_EG'] = 'Egypte';
$lang['nl_NL']['SilvercartCountry']['TITLE_EH'] = 'Westelijke Sahara';
$lang['nl_NL']['SilvercartCountry']['TITLE_ER'] = 'Eritrea';
$lang['nl_NL']['SilvercartCountry']['TITLE_ES'] = 'Spanje';
$lang['nl_NL']['SilvercartCountry']['TITLE_ET'] = 'Ethiopië';
$lang['nl_NL']['SilvercartCountry']['TITLE_FI'] = 'Finland';
$lang['nl_NL']['SilvercartCountry']['TITLE_FJ'] = 'Fiji';
$lang['nl_NL']['SilvercartCountry']['TITLE_FK'] = 'Falkland Islands';
$lang['nl_NL']['SilvercartCountry']['TITLE_FM'] = 'Micronesië';
$lang['nl_NL']['SilvercartCountry']['TITLE_FO'] = 'Faeröer';
$lang['nl_NL']['SilvercartCountry']['TITLE_FR'] = 'Frankrijk';
$lang['nl_NL']['SilvercartCountry']['TITLE_GA'] = 'Gabon';
$lang['nl_NL']['SilvercartCountry']['TITLE_GB'] = 'Verenigd Koninkrijk';
$lang['nl_NL']['SilvercartCountry']['TITLE_GD'] = 'Grenada';
$lang['nl_NL']['SilvercartCountry']['TITLE_GE'] = 'Georgië';
$lang['nl_NL']['SilvercartCountry']['TITLE_GF'] = 'Frans-Guyana';
$lang['nl_NL']['SilvercartCountry']['TITLE_GG'] = 'Guernsey';
$lang['nl_NL']['SilvercartCountry']['TITLE_GH'] = 'Ghana';
$lang['nl_NL']['SilvercartCountry']['TITLE_GI'] = 'Gibraltar';
$lang['nl_NL']['SilvercartCountry']['TITLE_GL'] = 'Groenland';
$lang['nl_NL']['SilvercartCountry']['TITLE_GM'] = 'Gambia';
$lang['nl_NL']['SilvercartCountry']['TITLE_GN'] = 'Guinea';
$lang['nl_NL']['SilvercartCountry']['TITLE_GP'] = 'Guadeloupe';
$lang['nl_NL']['SilvercartCountry']['TITLE_GQ'] = 'Equatoriaal-Guinea';
$lang['nl_NL']['SilvercartCountry']['TITLE_GR'] = 'Griekenland';
$lang['nl_NL']['SilvercartCountry']['TITLE_GS'] = 'Zuid-Georgië en de Zuidelijke Sandwicheilanden';
$lang['nl_NL']['SilvercartCountry']['TITLE_GT'] = 'Guatemala';
$lang['nl_NL']['SilvercartCountry']['TITLE_GU'] = 'Guam';
$lang['nl_NL']['SilvercartCountry']['TITLE_GW'] = 'Guinea-Bissau';
$lang['nl_NL']['SilvercartCountry']['TITLE_GY'] = 'Guyana';
$lang['nl_NL']['SilvercartCountry']['TITLE_HK'] = 'Hong Kong';
$lang['nl_NL']['SilvercartCountry']['TITLE_HM'] = 'Heard-en McDonald-eilanden';
$lang['nl_NL']['SilvercartCountry']['TITLE_HN'] = 'Honduras';
$lang['nl_NL']['SilvercartCountry']['TITLE_HR'] = 'Kroatië';
$lang['nl_NL']['SilvercartCountry']['TITLE_HT'] = 'Haïti';
$lang['nl_NL']['SilvercartCountry']['TITLE_HU'] = 'Hongarije';
$lang['nl_NL']['SilvercartCountry']['TITLE_ID'] = 'Indonesië';
$lang['nl_NL']['SilvercartCountry']['TITLE_IE'] = 'Ierland';
$lang['nl_NL']['SilvercartCountry']['TITLE_IL'] = 'Israël';
$lang['nl_NL']['SilvercartCountry']['TITLE_IM'] = 'Isle of Man';
$lang['nl_NL']['SilvercartCountry']['TITLE_IN'] = 'Indië';
$lang['nl_NL']['SilvercartCountry']['TITLE_IO'] = 'British Indian Ocean Territory';
$lang['nl_NL']['SilvercartCountry']['TITLE_IQ'] = 'Irak';
$lang['nl_NL']['SilvercartCountry']['TITLE_IR'] = 'Iran';
$lang['nl_NL']['SilvercartCountry']['TITLE_IS'] = 'IJsland';
$lang['nl_NL']['SilvercartCountry']['TITLE_IT'] = 'Italië';
$lang['nl_NL']['SilvercartCountry']['TITLE_JE'] = 'Jersey';
$lang['nl_NL']['SilvercartCountry']['TITLE_JM'] = 'Jamaica';
$lang['nl_NL']['SilvercartCountry']['TITLE_JO'] = 'Jordanië';
$lang['nl_NL']['SilvercartCountry']['TITLE_JP'] = 'Japan';
$lang['nl_NL']['SilvercartCountry']['TITLE_KE'] = 'Kenia';
$lang['nl_NL']['SilvercartCountry']['TITLE_KG'] = 'Kirgizië';
$lang['nl_NL']['SilvercartCountry']['TITLE_KH'] = 'Cambodja';
$lang['nl_NL']['SilvercartCountry']['TITLE_KI'] = 'Kiribati';
$lang['nl_NL']['SilvercartCountry']['TITLE_KM'] = 'Comoren';
$lang['nl_NL']['SilvercartCountry']['TITLE_KN'] = 'Saint Kitts and Nevis';
$lang['nl_NL']['SilvercartCountry']['TITLE_KP'] = 'Noord-Korea';
$lang['nl_NL']['SilvercartCountry']['TITLE_KR'] = 'Zuid-Korea';
$lang['nl_NL']['SilvercartCountry']['TITLE_KW'] = 'Koeweit';
$lang['nl_NL']['SilvercartCountry']['TITLE_KY'] = 'Cayman Islands';
$lang['nl_NL']['SilvercartCountry']['TITLE_KZ'] = 'Kazachstan';
$lang['nl_NL']['SilvercartCountry']['TITLE_LA'] = 'Laos';
$lang['nl_NL']['SilvercartCountry']['TITLE_LB'] = 'Libanon';
$lang['nl_NL']['SilvercartCountry']['TITLE_LC'] = 'Saint Lucia';
$lang['nl_NL']['SilvercartCountry']['TITLE_LI'] = 'Liechtenstein';
$lang['nl_NL']['SilvercartCountry']['TITLE_LK'] = 'Sri Lanka';
$lang['nl_NL']['SilvercartCountry']['TITLE_LR'] = 'Liberia';
$lang['nl_NL']['SilvercartCountry']['TITLE_LS'] = 'Lesotho';
$lang['nl_NL']['SilvercartCountry']['TITLE_LT'] = 'Litouwen';
$lang['nl_NL']['SilvercartCountry']['TITLE_LU'] = 'Luxemburg';
$lang['nl_NL']['SilvercartCountry']['TITLE_LV'] = 'Letland';
$lang['nl_NL']['SilvercartCountry']['TITLE_LY'] = 'Libië';
$lang['nl_NL']['SilvercartCountry']['TITLE_MA'] = 'Marokko';
$lang['nl_NL']['SilvercartCountry']['TITLE_MC'] = 'Monaco';
$lang['nl_NL']['SilvercartCountry']['TITLE_MD'] = 'Moldavië';
$lang['nl_NL']['SilvercartCountry']['TITLE_ME'] = 'Montenegro';
$lang['nl_NL']['SilvercartCountry']['TITLE_MF'] = 'Saint Martin';
$lang['nl_NL']['SilvercartCountry']['TITLE_MG'] = 'Madagascar';
$lang['nl_NL']['SilvercartCountry']['TITLE_MH'] = 'Marshall Islands';
$lang['nl_NL']['SilvercartCountry']['TITLE_MK'] = 'Macedonië';
$lang['nl_NL']['SilvercartCountry']['TITLE_ML'] = 'Mali';
$lang['nl_NL']['SilvercartCountry']['TITLE_MM'] = 'Myanmar [Burma]';
$lang['nl_NL']['SilvercartCountry']['TITLE_MN'] = 'Mongolië';
$lang['nl_NL']['SilvercartCountry']['TITLE_MO'] = 'Macau';
$lang['nl_NL']['SilvercartCountry']['TITLE_MP'] = 'Noordelijke Marianen';
$lang['nl_NL']['SilvercartCountry']['TITLE_MQ'] = 'Martinique';
$lang['nl_NL']['SilvercartCountry']['TITLE_MR'] = 'Mauritanië';
$lang['nl_NL']['SilvercartCountry']['TITLE_MS'] = 'Montserrat';
$lang['nl_NL']['SilvercartCountry']['TITLE_MT'] = 'Malta';
$lang['nl_NL']['SilvercartCountry']['TITLE_MU'] = 'Mauritius';
$lang['nl_NL']['SilvercartCountry']['TITLE_MV'] = 'Maldiven';
$lang['nl_NL']['SilvercartCountry']['TITLE_MW'] = 'Malawi';
$lang['nl_NL']['SilvercartCountry']['TITLE_MX'] = 'Mexico';
$lang['nl_NL']['SilvercartCountry']['TITLE_MY'] = 'Maleisië';
$lang['nl_NL']['SilvercartCountry']['TITLE_MZ'] = 'Mozambique';
$lang['nl_NL']['SilvercartCountry']['TITLE_NA'] = 'Namibië';
$lang['nl_NL']['SilvercartCountry']['TITLE_NC'] = 'New Caledonia';
$lang['nl_NL']['SilvercartCountry']['TITLE_NE'] = 'Niger';
$lang['nl_NL']['SilvercartCountry']['TITLE_NF'] = 'Norfolk Island';
$lang['nl_NL']['SilvercartCountry']['TITLE_NG'] = 'Nigeria';
$lang['nl_NL']['SilvercartCountry']['TITLE_NI'] = 'Nicaragua';
$lang['nl_NL']['SilvercartCountry']['TITLE_NL'] = 'Nederland';
$lang['nl_NL']['SilvercartCountry']['TITLE_NO'] = 'Noorwegen';
$lang['nl_NL']['SilvercartCountry']['TITLE_NP'] = 'Nepal';
$lang['nl_NL']['SilvercartCountry']['TITLE_NR'] = 'Nauru';
$lang['nl_NL']['SilvercartCountry']['TITLE_NU'] = 'Niue';
$lang['nl_NL']['SilvercartCountry']['TITLE_NZ'] = 'Nieuw-Zeeland';
$lang['nl_NL']['SilvercartCountry']['TITLE_OM'] = 'Oman';
$lang['nl_NL']['SilvercartCountry']['TITLE_PA'] = 'Panama';
$lang['nl_NL']['SilvercartCountry']['TITLE_PE'] = 'Peru';
$lang['nl_NL']['SilvercartCountry']['TITLE_PF'] = 'Frans-Polynesië';
$lang['nl_NL']['SilvercartCountry']['TITLE_PG'] = 'Papua New Guinea';
$lang['nl_NL']['SilvercartCountry']['TITLE_PH'] = 'Filippijnen';
$lang['nl_NL']['SilvercartCountry']['TITLE_PK'] = 'Pakistan';
$lang['nl_NL']['SilvercartCountry']['TITLE_PL'] = 'Polen';
$lang['nl_NL']['SilvercartCountry']['TITLE_PM'] = 'Saint Pierre and Miquelon';
$lang['nl_NL']['SilvercartCountry']['TITLE_PN'] = 'Pitcairn Islands';
$lang['nl_NL']['SilvercartCountry']['TITLE_PR'] = 'Puerto Rico';
$lang['nl_NL']['SilvercartCountry']['TITLE_PS'] = 'Palestijnse gebieden';
$lang['nl_NL']['SilvercartCountry']['TITLE_PT'] = 'Portugal';
$lang['nl_NL']['SilvercartCountry']['TITLE_PW'] = 'Palau';
$lang['nl_NL']['SilvercartCountry']['TITLE_PY'] = 'Paraguay';
$lang['nl_NL']['SilvercartCountry']['TITLE_QA'] = 'Katar';
$lang['nl_NL']['SilvercartCountry']['TITLE_RE'] = 'Reunion';
$lang['nl_NL']['SilvercartCountry']['TITLE_RO'] = 'Roemenië';
$lang['nl_NL']['SilvercartCountry']['TITLE_RS'] = 'Servië';
$lang['nl_NL']['SilvercartCountry']['TITLE_RU'] = 'Rusland';
$lang['nl_NL']['SilvercartCountry']['TITLE_RW'] = 'Rwanda';
$lang['nl_NL']['SilvercartCountry']['TITLE_SA'] = 'Saudi-Arabië';
$lang['nl_NL']['SilvercartCountry']['TITLE_SB'] = 'Salomonseilanden';
$lang['nl_NL']['SilvercartCountry']['TITLE_SC'] = 'Seychelles';
$lang['nl_NL']['SilvercartCountry']['TITLE_SD'] = 'Soedan';
$lang['nl_NL']['SilvercartCountry']['TITLE_SE'] = 'Zweden';
$lang['nl_NL']['SilvercartCountry']['TITLE_SG'] = 'Singapore';
$lang['nl_NL']['SilvercartCountry']['TITLE_SH'] = 'Saint Helena';
$lang['nl_NL']['SilvercartCountry']['TITLE_SI'] = 'Slovenië';
$lang['nl_NL']['SilvercartCountry']['TITLE_SJ'] = 'Svalbard and Jan Mayen';
$lang['nl_NL']['SilvercartCountry']['TITLE_SK'] = 'Slowakije';
$lang['nl_NL']['SilvercartCountry']['TITLE_SL'] = 'Sierra Leone';
$lang['nl_NL']['SilvercartCountry']['TITLE_SM'] = 'San Marino';
$lang['nl_NL']['SilvercartCountry']['TITLE_SN'] = 'Senegal';
$lang['nl_NL']['SilvercartCountry']['TITLE_SO'] = 'Somalië';
$lang['nl_NL']['SilvercartCountry']['TITLE_SR'] = 'Suriname';
$lang['nl_NL']['SilvercartCountry']['TITLE_ST'] = 'São Tomé and Príncipe';
$lang['nl_NL']['SilvercartCountry']['TITLE_SV'] = 'El Salvador';
$lang['nl_NL']['SilvercartCountry']['TITLE_SX'] = 'Sint Maarten';
$lang['nl_NL']['SilvercartCountry']['TITLE_SY'] = 'Syrië';
$lang['nl_NL']['SilvercartCountry']['TITLE_SZ'] = 'Swaziland';
$lang['nl_NL']['SilvercartCountry']['TITLE_TC'] = 'Turks-en Caicoseilanden';
$lang['nl_NL']['SilvercartCountry']['TITLE_TD'] = 'Chad';
$lang['nl_NL']['SilvercartCountry']['TITLE_TF'] = 'Franse Zuidelijke Gebieden';
$lang['nl_NL']['SilvercartCountry']['TITLE_TG'] = 'Togo';
$lang['nl_NL']['SilvercartCountry']['TITLE_TH'] = 'Thailand';
$lang['nl_NL']['SilvercartCountry']['TITLE_TJ'] = 'Tadzjikistan';
$lang['nl_NL']['SilvercartCountry']['TITLE_TK'] = 'Tokelau';
$lang['nl_NL']['SilvercartCountry']['TITLE_TL'] = 'Oost-Timor';
$lang['nl_NL']['SilvercartCountry']['TITLE_TM'] = 'Turkmenistan';
$lang['nl_NL']['SilvercartCountry']['TITLE_TN'] = 'Tunesië';
$lang['nl_NL']['SilvercartCountry']['TITLE_TO'] = 'Tonga';
$lang['nl_NL']['SilvercartCountry']['TITLE_TR'] = 'Turkije';
$lang['nl_NL']['SilvercartCountry']['TITLE_TT'] = 'Trinidad and Tobago';
$lang['nl_NL']['SilvercartCountry']['TITLE_TV'] = 'Tuvalu';
$lang['nl_NL']['SilvercartCountry']['TITLE_TW'] = 'Taiwan';
$lang['nl_NL']['SilvercartCountry']['TITLE_TZ'] = 'Tanzania';
$lang['nl_NL']['SilvercartCountry']['TITLE_UA'] = 'Oekraïne';
$lang['nl_NL']['SilvercartCountry']['TITLE_UG'] = 'Uganda';
$lang['nl_NL']['SilvercartCountry']['TITLE_UM'] = 'Amerikaanse Kleine afgelegen eilanden';
$lang['nl_NL']['SilvercartCountry']['TITLE_US'] = 'Verenigde Staten';
$lang['nl_NL']['SilvercartCountry']['TITLE_UY'] = 'Uruguay';
$lang['nl_NL']['SilvercartCountry']['TITLE_UZ'] = 'Oezbekistan';
$lang['nl_NL']['SilvercartCountry']['TITLE_VA'] = 'Vaticaanstad';
$lang['nl_NL']['SilvercartCountry']['TITLE_VC'] = 'Saint Vincent and the Grenadines';
$lang['nl_NL']['SilvercartCountry']['TITLE_VE'] = 'Venezuela';
$lang['nl_NL']['SilvercartCountry']['TITLE_VG'] = 'Britse Maagdeneilanden';
$lang['nl_NL']['SilvercartCountry']['TITLE_VI'] = 'Amerikaanse Maagdeneilanden';
$lang['nl_NL']['SilvercartCountry']['TITLE_VN'] = 'Vietnam';
$lang['nl_NL']['SilvercartCountry']['TITLE_VU'] = 'Vanuatu';
$lang['nl_NL']['SilvercartCountry']['TITLE_WF'] = 'Wallis and Futuna';
$lang['nl_NL']['SilvercartCountry']['TITLE_WS'] = 'Samoa';
$lang['nl_NL']['SilvercartCountry']['TITLE_XK'] = 'Kosovo';
$lang['nl_NL']['SilvercartCountry']['TITLE_YE'] = 'Yemen';
$lang['nl_NL']['SilvercartCountry']['TITLE_YT'] = 'Mayotte';
$lang['nl_NL']['SilvercartCountry']['TITLE_ZA'] = 'Zuid-Afrika';
$lang['nl_NL']['SilvercartCountry']['TITLE_ZM'] = 'Zambia';
$lang['nl_NL']['SilvercartCountry']['TITLE_ZW'] = 'Zimbabwe';

$lang['nl_NL']['SilvercartCustomerAdmin']['customers'] = 'Klanten';

$lang['nl_NL']['SilvercartCustomer']['ANONYMOUSCUSTOMER'] = 'Anonieme klant';
$lang['nl_NL']['SilvercartCustomer']['BUSINESSCUSTOMER'] = 'Zakelijke klant';
$lang['nl_NL']['SilvercartCustomer']['CUSTOMERNUMBER'] = 'Klantnummer';
$lang['nl_NL']['SilvercartCustomer']['CUSTOMERNUMBER_SHORT'] = 'Klant-nr.';
$lang['nl_NL']['SilvercartCustomer']['GROSS'] = 'bruto';
$lang['nl_NL']['SilvercartCustomer']['NET'] = 'netto';
$lang['nl_NL']['SilvercartCustomer']['PRICING'] = 'Prijzen';
$lang['nl_NL']['SilvercartCustomer']['SALUTATION'] = 'Aanhef';
$lang['nl_NL']['SilvercartCustomer']['SUBSCRIBEDTONEWSLETTER'] = 'Geabonneerd op de nieuwsbrief';
$lang['nl_NL']['SilvercartCustomer']['HASACCEPTEDTERMSANDCONDITIONS'] = 'Heeft voorwaarden aanvaard';
$lang['nl_NL']['SilvercartCustomer']['HASACCEPTEDREVOCATIONINSTRUCTION'] = 'Heeft voorwaarden NIET aanvaard';
$lang['nl_NL']['SilvercartCustomer']['BIRTHDAY'] = 'Geboortedag';
$lang['nl_NL']['SilvercartCustomer']['REGULARCUSTOMER'] = 'Vaste klant';
$lang['nl_NL']['SilvercartCustomer']['TYPE'] = 'Type';

$lang['nl_NL']['SilvercartDataPage']['DEFAULT_TITLE'] = 'Mijn Gegevens';
$lang['nl_NL']['SilvercartDataPage']['DEFAULT_URLSEGMENT'] = 'mijn-gegevens';
$lang['nl_NL']['SilvercartDataPage']['PLURALNAME'] = 'Gegevens pagina\'s';
$lang['nl_NL']['SilvercartDataPage']['SINGULARNAME'] = 'Gegevens pagina';
$lang['nl_NL']['SilvercartDataPage']['TITLE'] = 'Mijn Gegevens';
$lang['nl_NL']['SilvercartDataPage']['URL_SEGMENT'] = 'mijn-gegevens';

$lang['nl_NL']['SilvercartDataPrivacyStatementPage']['PLURALNAME'] = 'Privacybeleid pagina\'s';
$lang['nl_NL']['SilvercartDataPrivacyStatementPage']['SINGULARNAME'] = 'Privacybeleid pagina';
$lang['nl_NL']['SilvercartDataPrivacyStatementPage']['TITLE'] = 'Verklaring inzake gegevensbeveiliging';
$lang['nl_NL']['SilvercartDataPrivacyStatementPage']['URL_SEGMENT'] = 'verklaring-gegevensbeveiliging';

$lang['nl_NL']['SilvercartDeeplinkPage']['SINGULARNAME'] = 'doellink pagina';
$lang['nl_NL']['SilvercartDeeplinkPage']['PLURALNAME'] = 'doellink pagina\'s';
$lang['nl_NL']['SilvercartDeeplinkPage']['DEFAULT_TITLE'] = 'deeplink page';

$lang['nl_NL']['SilvercartEditAddressForm']['EMPTYSTRING_PLEASECHOOSE'] = '--kies aub--';

$lang['nl_NL']['SilvercartEmailTemplates']['PLURALNAME'] = 'Email templates';
$lang['nl_NL']['SilvercartEmailTemplates']['SINGULARNAME'] = 'Email template';

$lang['nl_NL']['SilvercartFile']['DESCRIPTION'] = 'Omschrijving';
$lang['nl_NL']['SilvercartFile']['FILE_ATTACHMENTS'] = 'Bestandsbijlagen';
$lang['nl_NL']['SilvercartFile']['PLURALNAME'] = 'Bestanden';
$lang['nl_NL']['SilvercartFile']['SINGULARNAME'] = 'Bestand';
$lang['nl_NL']['SilvercartFile']['TITLE'] = 'Toon naam';

$lang['nl_NL']['SilvercartFrontPage']['CONTENT'] = '<h2>Welkom bij <strong>SilverCart</strong> Webshop!</h2><br/><img src="/silvercart/images/silvercart_passion_teaser.jpg" alt="" title="SilverCart - created with passion for eCommerce"/>';
$lang['nl_NL']['SilvercartFrontPage']['DEFAULT_CONTENT'] = $lang['nl_NL']['SilvercartFrontPage']['CONTENT'];
$lang['nl_NL']['SilvercartFrontPage']['PLURALNAME'] = 'Voorpagina\'s';
$lang['nl_NL']['SilvercartFrontPage']['SINGULARNAME'] = 'voorpagina';

$lang['nl_NL']['SilvercartGroupView']['LIST'] = 'Lijst';
$lang['nl_NL']['SilvercartGroupView']['TILE'] = 'Tegel';

$lang['nl_NL']['SilvercartHandlingCost']['PLURALNAME'] = 'Afhandelingskosten';
$lang['nl_NL']['SilvercartHandlingCost']['SINGULARNAME'] = 'Afhandelingskosten';
$lang['nl_NL']['SilvercartHandlingCost']['AMOUNT'] = 'bedrag';

$lang['nl_NL']['SilvercartHasManyOrderField']['ATTRIBUTED_FIELDS']          = 'Gekoppelde widgets';
$lang['nl_NL']['SilvercartHasManyOrderField']['MOVE_DOWN']                  = 'Naar beneden';
$lang['nl_NL']['SilvercartHasManyOrderField']['MOVE_UP']                    = 'Naar boven';
$lang['nl_NL']['SilvercartHasManyOrderField']['AVAILABLE_RELATION_OBJECTS'] = 'Beschikbare widgets';
$lang['nl_NL']['SilvercartHasManyOrderField']['EDIT']                       = 'Bewerken';

$lang['nl_NL']['SilvercartImage']['DESCRIPTION'] = 'Omschrijving';
$lang['nl_NL']['SilvercartImage']['PLURALNAME'] = 'Afbeeldingen';
$lang['nl_NL']['SilvercartImage']['SINGULARNAME'] = 'Afbeelding';
$lang['nl_NL']['SilvercartImage']['TITLE'] = 'Weergave naam';

$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['COMBINED_STRING']                       = 'Alle informatie in een tekenreeks met scheidingstekens';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['COMBINED_STRING_KEY']                   = 'Vraag variabele voor gecombineerde string methode';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['COMBINED_STRING_ENTITY_SEPARATOR']      = 'Entiteit scheidingsteken voor gecombineerde string methode';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['COMBINED_STRING_QUANTITY_SEPARATOR']    = 'Aantal scheidingsteken voor gecombineerde string methode';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['KEY_VALUE']                             = 'Informatie over sleutel-waarde-paren';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['KEY_VALUE_PRODUCT_IDENTIFIER']          = 'Vraag variabele naam op voor product-ID';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['KEY_VALUE_QUANTITY_IDENTIFIER']         = 'Vraag variabele naam op voor aantal-ID';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['PLURALNAME']                            = 'Binnenkomende Winkelwagen Overdracht';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['PRODUCT_MATCHING_FIELD']                = 'Bijpassend product veld';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['REFERER_IDENTIFIER']                    = 'Referentie identificatie';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['SHARED_SECRET']                         = 'Gedeelde geheim';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['SHARED_SECRET_ACTIVATION']              = 'activeren gedeeld geheim';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['SHARED_SECRET_IDENTIFIER']              = 'Vraag variabele naam op voor gedeeld geheim';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['SINGULARNAME']                          = 'Binnenkomende Winkelwagen Overdracht';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['TITLE']                                 = 'Titel';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['TRANSFER_METHOD']                       = 'Overdracht methode';
$lang['nl_NL']['SilvercartInboundShoppingCartTransfer']['USE_SHARED_SECRET']                     = 'Gebruik gedeeld geheim';

$lang['nl_NL']['SilvercartInboundShoppingCartTransferPage']['ERROR_COMBINED_STRING_KEY_NOT_FOUND']              = 'Parameters werden niet verstuurd';
$lang['nl_NL']['SilvercartInboundShoppingCartTransferPage']['ERROR_KEY_VALUE_PRODUCT_IDENTIFIER_NOT_FOUND']     = 'Parameters werden niet verstuurd (key-value product-ID ontbreekt)';
$lang['nl_NL']['SilvercartInboundShoppingCartTransferPage']['ERROR_KEY_VALUE_QUANTITY_IDENTIFIER_NOT_FOUND']    = 'Parameters werden niet verstuurd (key-value hoeveelheid-ID ontbreekt)';
$lang['nl_NL']['SilvercartInboundShoppingCartTransferPage']['ERROR_REFERER_NOT_FOUND']                          = 'Referer is niet geldig';
$lang['nl_NL']['SilvercartInboundShoppingCartTransferPage']['ERROR_SHARED_SECRET_INVALID']                      = 'Autorisatie ontbreekt';

$lang['nl_NL']['SilvercartInvoiceAddress']['PLURALNAME'] = 'Factuur adressen';
$lang['nl_NL']['SilvercartInvoiceAddress']['SINGULARNAME'] = 'Factuur adres';

$lang['nl_NL']['SilvercartManufacturer']['PLURALNAME'] = 'Fabrikanten';
$lang['nl_NL']['SilvercartManufacturer']['SINGULARNAME'] = 'Fabrikant';

$lang['nl_NL']['SilvercartMetaNavigationHolder']['DEFAULT_TITLE'] = 'Meta navigatie';
$lang['nl_NL']['SilvercartMetaNavigationHolder']['DEFAULT_URLSEGMENT'] = 'metanavigatie';
$lang['nl_NL']['SilvercartMetaNavigationHolder']['PLURALNAME'] = 'Meta navigatie';
$lang['nl_NL']['SilvercartMetaNavigationHolder']['SINGULARNAME'] = 'Meta navigatie';
$lang['nl_NL']['SilvercartMetaNavigationHolder']['URL_SEGMENT'] = 'metanavigatie';

$lang['nl_NL']['SilvercartMyAccountHolder']['ALREADY_HAVE_AN_ACCOUNT']          = 'Heeft u al een account?';
$lang['nl_NL']['SilvercartMyAccountHolder']['DEFAULT_TITLE']                    = 'Mijn account';
$lang['nl_NL']['SilvercartMyAccountHolder']['DEFAULT_URLSEGMENT']               = 'mijn-account';
$lang['nl_NL']['SilvercartMyAccountHolder']['GOTO_REGISTRATION']                = 'Ga naar het aanmeldformulier';
$lang['nl_NL']['SilvercartMyAccountHolder']['PLURALNAME']                       = 'Accounthouders';
$lang['nl_NL']['SilvercartMyAccountHolder']['REGISTER_ADVANTAGES_TEXT']         = 'Door te registreren kunt u opnieuw uw gegevens, zoals factuur of levering adressen gebruiken bij uw volgende aankoop.';
$lang['nl_NL']['SilvercartMyAccountHolder']['SINGULARNAME']                     = 'Accounthouder';
$lang['nl_NL']['SilvercartMyAccountHolder']['TITLE']                            = 'Mijn account';
$lang['nl_NL']['SilvercartMyAccountHolder']['URL_SEGMENT']                      = 'mijn-account';
$lang['nl_NL']['SilvercartMyAccountHolder']['WANTTOREGISTER']                   = 'Wilt u zich registreren?';
$lang['nl_NL']['SilvercartMyAccountHolder']['YOUR_CUSTOMERNUMBER']              = 'Uw klantnummer';
$lang['nl_NL']['SilvercartMyAccountHolder']['YOUR_CURRENT_ADDRESSES']           = 'Uw huidige factuur en verzendadres';
$lang['nl_NL']['SilvercartMyAccountHolder']['YOUR_MOST_CURRENT_ORDERS']         = 'Uw meest recente opdrachten';
$lang['nl_NL']['SilvercartMyAccountHolder']['YOUR_PERSONAL_DATA']               = 'Uw persoonlijke gegevens';

$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['DEFAULT_CONFIRMATIONFAILUREMESSAGE']   = '<p>Uw nieuwsbrief registratie kan niet worden voltooid.</p>';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['DEFAULT_CONFIRMATIONSUCCESSMESSAGE']   = '<p>Uw nieuwsbrief registratie is geslaagd!</p><p>Hopelijk zijn onze aanbiedingen nuttig voor u.</p>';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['DEFAULT_ALREADYCONFIRMEDMESSAGE']      = '<p>Uw nieuwsbrief inschrijving is reeds afgerond.</p>';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['DEFAULT_CONTENT']                      = '';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['DEFAULT_URLSEGMENT']                   = 'nieuwsbrief-opt-in-bevestiging';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['DEFAULT_TITLE']                        = 'Volledige nieuwsbrief registratie';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['ALREADY_CONFIRMED_MESSAGE_TEXT']   = 'Bericht: de gebruiker voltooide opt-in reeds';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['FAILURE_MESSAGE_TEXT']             = 'Foutmelding';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['PLURALNAME']                       = 'Newsletter opt-in bevestigingspagina';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['SINGULARNAME']                     = 'Newsletter opt-in bevestigingspagina\'s';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['SUCCESS_MESSAGE_TEXT']             = 'Succes bericht';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['CONFIRMATIONFAILUREMESSAGE']       = '<p>Uw nieuwsbrief registratie kan niet worden voltooid.</p>';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['CONFIRMATIONSUCCESSMESSAGE']       = '<p>Uw nieuwsbrief registratie is geslaagd!</p><p>Hopelijk zijn onze aanbiedingen nuttig voor u.</p>';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['ALREADYCONFIRMEDMESSAGE']          = '<p>Uw nieuwsbrief inschrijving is reeds afgerond.</p>';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['CONTENT']                          = '';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['URL_SEGMENT']                      = 'nieuwsbrief-opt-in-bevestiging';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['TITLE']                            = 'Volledige nieuwsbrief registratie';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['TITLE_THANKS']                     = 'Nieuwsbrief registratie succesvol afgerond';
$lang['nl_NL']['SilvercartNewsletterOptInConfirmationPage']['EMAIL_CONFIRMATION_TEXT']          = '<h1>Volledige nieuwsbrief registratie </h1><p> Klik op de activatie link of kopieer de link naar uw de browser van uw keuze</P><a href="$ConfirmationLink">Bevestig nieuwsbrief registratie</a></p><p>Als u nog geen nieuwsbrief wilt ontvangen negeer dan dit email bericht</p><p>Uw webshop team</p>';

$lang['nl_NL']['SilvercartOrder']['AMOUNTGROSSTOTAL'] = 'Bedrag totaal bruto';
$lang['nl_NL']['SilvercartOrder']['AMOUNTTOTAL'] = 'Totaal bedrag';
$lang['nl_NL']['SilvercartOrder']['CUSTOMER'] = 'Klant';
$lang['nl_NL']['SilvercartOrder']['CUSTOMERSEMAIL'] = 'Email adres klant';
$lang['nl_NL']['SilvercartOrder']['HANDLINGCOSTPAYMENT'] = 'Behandelingskosten';
$lang['nl_NL']['SilvercartOrder']['HANDLINGCOSTSHIPMENT'] = 'Verpakkingskosten';
$lang['nl_NL']['SilvercartOrder']['HASACCEPTEDTERMSANDCONDITIONS'] = 'Heeft voorwaarden aanvaard';
$lang['nl_NL']['SilvercartOrder']['HASACCEPTEDREVOCATIONINSTRUCTION'] = 'Heeft nietigverklaringsinstructie aanvaard';
$lang['nl_NL']['SilvercartOrder']['INCLUDED_SHIPPINGRATE'] = 'Inclusief verzendkosten';
$lang['nl_NL']['SilvercartOrder']['INVOICENUMBER'] = 'Factuurnummer';
$lang['nl_NL']['SilvercartOrder']['INVOICENUMBER_SHORT'] = 'Factuur-nr.';
$lang['nl_NL']['SilvercartOrder']['NOTE'] = 'Notitie';
$lang['nl_NL']['SilvercartOrder']['ORDER_ID'] = 'Bestelnummer';
$lang['nl_NL']['SilvercartOrder']['ORDERNUMBER'] = 'Bestelnummer';
$lang['nl_NL']['SilvercartOrder']['ORDER_VALUE'] = 'Totaal opdracht';
$lang['nl_NL']['SilvercartOrder']['PAYMENTMETHODTITLE'] = 'Betaal methode';
$lang['nl_NL']['SilvercartOrder']['PLURALNAME'] = 'Bestellingen';
$lang['nl_NL']['SilvercartOrder']['PRINT'] = 'Afdrukken';
$lang['nl_NL']['SilvercartOrder']['SHIPPINGRATE'] = 'Verzendtarief';
$lang['nl_NL']['SilvercartOrder']['SINGULARNAME'] = 'Bestelling';
$lang['nl_NL']['SilvercartOrder']['STATUS'] = 'Status bestelling';
$lang['nl_NL']['SilvercartOrder']['TAXAMOUNTPAYMENT'] = 'BTW bedrag';
$lang['nl_NL']['SilvercartOrder']['TAXAMOUNTSHIPMENT'] = 'BTW bedrag verzendkosten';
$lang['nl_NL']['SilvercartOrder']['TAXRATEPAYMENT'] = 'BTW tarief';
$lang['nl_NL']['SilvercartOrder']['TAXRATESHIPMENT'] = 'BTW tarief verzendkosten';
$lang['nl_NL']['SilvercartOrder']['WEIGHTTOTAL'] = 'Totaal gewicht';
$lang['nl_NL']['SilvercartOrder']['YOUR_REMARK'] = 'Uw kenmerk';

$lang['nl_NL']['SilvercartOrderAddress']['PLURALNAME'] = 'Bestel adressen';
$lang['nl_NL']['SilvercartOrderAddress']['SINGULARNAME'] = 'Bestel adres';

$lang['nl_NL']['SilvercartOrderConfirmationPage']['DEFAULT_TITLE'] = 'Bestelbevestiging';
$lang['nl_NL']['SilvercartOrderConfirmationPage']['DEFAULT_URLSEGMENT'] = 'bestelbevestiging';
$lang['nl_NL']['SilvercartOrderConfirmationPage']['PLURALNAME'] = 'Bestelbevestiging Pagina\'s';
$lang['nl_NL']['SilvercartOrderConfirmationPage']['SINGULARNAME'] = 'Bestelbevestiging Pagina';
$lang['nl_NL']['SilvercartOrderConfirmationPage']['URL_SEGMENT'] = 'bestelbevestiging';
$lang['nl_NL']['SilvercartOrderConfirmationPage']['ORDERCONFIRMATION'] = 'Bestelbevestiging';

$lang['nl_NL']['SilvercartOrderDetailPage']['DEFAULT_TITLE'] = 'Bestelling details';
$lang['nl_NL']['SilvercartOrderDetailPage']['DEFAULT_URLSEGMENT'] = 'bestelling-details';
$lang['nl_NL']['SilvercartOrderDetailPage']['PLURALNAME'] = 'Detailpagina\'s bestellingen';
$lang['nl_NL']['SilvercartOrderDetailPage']['SINGULARNAME'] = 'Detailpagina bestelling';
$lang['nl_NL']['SilvercartOrderDetailPage']['TITLE'] = 'Bestelling details';
$lang['nl_NL']['SilvercartOrderDetailPage']['URL_SEGMENT'] = 'bestelling-details';

$lang['nl_NL']['SilvercartOrderHolder']['DEFAULT_TITLE'] = 'Mijn bestellingen';
$lang['nl_NL']['SilvercartOrderHolder']['DEFAULT_URLSEGMENT'] = 'mijn-bestellingen';
$lang['nl_NL']['SilvercartOrderHolder']['PLURALNAME'] = 'Overzicht bestellingen';
$lang['nl_NL']['SilvercartOrderHolder']['SINGULARNAME'] = 'Overzicht bestelling';
$lang['nl_NL']['SilvercartOrderHolder']['TITLE'] = 'Mijn bestellingen';
$lang['nl_NL']['SilvercartOrderHolder']['URL_SEGMENT'] = 'mijn-bestellingen';

$lang['nl_NL']['SilvercartOrderInvoiceAddress']['PLURALNAME'] = 'Factuuradressen';
$lang['nl_NL']['SilvercartOrderInvoiceAddress']['SINGULARNAME'] = 'Factuuradres';

$lang['nl_NL']['SilvercartOrderPosition']['PLURALNAME'] = 'Bestel posities';
$lang['nl_NL']['SilvercartOrderPosition']['SINGULARNAME'] = 'Bestel positie';
$lang['nl_NL']['SilvercartOrderPosition']['SHORT'] = 'Pos.';

$lang['nl_NL']['SilvercartOrderSearchForm']['PLEASECHOOSE'] = 'Maak een keuze';

$lang['nl_NL']['SilvercartOrderShippingAddress']['PLURALNAME'] = 'Verzendadressen bestelling';
$lang['nl_NL']['SilvercartOrderShippingAddress']['SINGULARNAME'] = 'Verzendadres bestelling';

$lang['nl_NL']['SilvercartOrderStatus']['ATTRIBUTED_SHOPEMAILS_LABEL_DESC'] = 'De onderstaande e-mail adressen ontvangen een bericht als deze bestelstatus is ingesteld:';
$lang['nl_NL']['SilvercartOrderStatus']['ATTRIBUTED_SHOPEMAILS_LABEL_TITLE'] = 'Geselecteerde e-mail adressen';
$lang['nl_NL']['SilvercartOrderStatus']['CODE'] = 'Code';
$lang['nl_NL']['SilvercartOrderStatus']['PAYED'] = 'Betaald';
$lang['nl_NL']['SilvercartOrderStatus']['PLURALNAME'] = 'Bestel status';
$lang['nl_NL']['SilvercartOrderStatus']['SHIPPED'] = 'Bestelling verstuurd';
$lang['nl_NL']['SilvercartOrderStatus']['SINGULARNAME'] = 'Bestel status';
$lang['nl_NL']['SilvercartOrderStatus']['WAITING_FOR_PAYMENT'] = 'Wacht op betaling';

$lang['nl_NL']['SilvercartOrderStatusTexts']['PLURALNAME'] = 'Bestelstatus teksten';
$lang['nl_NL']['SilvercartOrderStatusTexts']['SINGULARNAME'] = 'Bestelstatus tekst';

$lang['nl_NL']['SilvercartPage']['ABOUT_US'] = 'Over ons';
$lang['nl_NL']['SilvercartPage']['ABOUT_US_URL_SEGMENT'] = 'over-ons';
$lang['nl_NL']['SilvercartPage']['ACCESS_CREDENTIALS_CALL'] = 'Vul uw toegansgangs gegevens in:';
$lang['nl_NL']['SilvercartPage']['ADDRESS'] = 'Adres';
$lang['nl_NL']['SilvercartPage']['ADDRESSINFORMATION'] = 'Adres informatie';
$lang['nl_NL']['SilvercartPage']['ADDRESS_DATA'] = 'Persoonlijke gegevens';
$lang['nl_NL']['SilvercartPage']['ADMIN_AREA'] = 'Beheer Toegang';
$lang['nl_NL']['SilvercartPage']['ALREADY_REGISTERED'] = 'Hallo %s, je bent al geregistreerd.';
$lang['nl_NL']['SilvercartPage']['API_CREATE'] = 'Kan objecten maken, via de API';
$lang['nl_NL']['SilvercartPage']['API_DELETE'] = 'Kan objecten verwijderen via de API';
$lang['nl_NL']['SilvercartPage']['API_EDIT'] = 'Kan objecten bewerken via de API';
$lang['nl_NL']['SilvercartPage']['API_VIEW'] = 'Kan objecten lezen via de API';
$lang['nl_NL']['SilvercartPage']['APRIL'] = 'April';
$lang['nl_NL']['SilvercartPage']['PRODUCTNAME'] = 'Product naam';
$lang['nl_NL']['SilvercartPage']['AUGUST'] = 'Augustus';
$lang['nl_NL']['SilvercartPage']['BILLING_ADDRESS'] = 'Factuur adres';
$lang['nl_NL']['SilvercartPage']['BIRTHDAY'] = 'Geboorte dag';
$lang['nl_NL']['SilvercartPage']['CANCEL'] = 'Annuleren';
$lang['nl_NL']['SilvercartPage']['CART'] = 'Winkelwagen';
$lang['nl_NL']['SilvercartPage']['CATALOG'] = 'Catalogus';
$lang['nl_NL']['SilvercartPage']['CHANGE_PAYMENTMETHOD_CALL'] = 'Kies een andere betaalmethode neem contact op met de winkel eigenaar.';
$lang['nl_NL']['SilvercartPage']['CHANGE_PAYMENTMETHOD_LINK'] = 'Kies een andere betaalmethode';
$lang['nl_NL']['SilvercartPage']['CHECKOUT'] = 'Afrekenen';
$lang['nl_NL']['SilvercartPage']['CHECK_FIELDS_CALL'] = 'Controleer uw invoer op de volgende gebieden:';
$lang['nl_NL']['SilvercartPage']['CONTACT_FORM'] = 'Contact formulier';
$lang['nl_NL']['SilvercartPage']['CONTINUESHOPPING'] = 'Verder met winkelen';
$lang['nl_NL']['SilvercartPage']['CREDENTIALS_WRONG'] = 'Uw gegevens zijn onjuist.';
$lang['nl_NL']['SilvercartPage']['DAY'] = 'Dag';
$lang['nl_NL']['SilvercartPage']['DECEMBER'] = 'December';
$lang['nl_NL']['SilvercartPage']['DETAILS'] = 'Details';
$lang['nl_NL']['SilvercartPage']['DETAILS_FOR'] = 'Details voor %s';
$lang['nl_NL']['SilvercartPage']['DIDNOT_RETURN_RESULTS'] = 'heeft geen resultaten opgeleverd in onze winkel.';
$lang['nl_NL']['SilvercartPage']['DO_NOT_EDIT'] = 'Bewerk dit veld niet, tenzij je precies weet wat je doet!';
$lang['nl_NL']['SilvercartPage']['EMAIL_ADDRESS'] = 'E-mail adres';
$lang['nl_NL']['SilvercartPage']['EMAIL_ALREADY_REGISTERED'] = 'Dit e-mailadres is al geregistreerd!';
$lang['nl_NL']['SilvercartPage']['EMAIL_NOT_FOUND'] = 'Dit e-mailadres kon niet worden gevonden.';
$lang['nl_NL']['SilvercartPage']['EMPTY_CART'] = 'Leeg';
$lang['nl_NL']['SilvercartPage']['ERROR_LISTING'] = 'De volgende fouten zijn opgetreden:';
$lang['nl_NL']['SilvercartPage']['ERROR_OCCURED'] = 'Er is een fout opgetreden.';
$lang['nl_NL']['SilvercartPage']['FEBRUARY'] = 'Februari';
$lang['nl_NL']['SilvercartPage']['FIND'] = 'Zoeken:';
$lang['nl_NL']['SilvercartPage']['FORWARD'] = 'Volgende';
$lang['nl_NL']['SilvercartPage']['GOTO'] = 'Ga naar %s pagina';
$lang['nl_NL']['SilvercartPage']['GOTO_CART'] = 'Ga naar winkelwagen';
$lang['nl_NL']['SilvercartPage']['GOTO_CART_SHORT'] = 'Winkelwagen';
$lang['nl_NL']['SilvercartPage']['GOTO_CONTACT_LINK'] = 'Ga naar contact pagina';
$lang['nl_NL']['SilvercartPage']['HEADERPICTURE'] = 'Header foto';
$lang['nl_NL']['SilvercartPage']['INCLUDED_VAT'] = 'inclusief BTW';
$lang['nl_NL']['SilvercartPage']['ADDITIONAL_VAT'] = 'bijkomende BTW';
$lang['nl_NL']['SilvercartPage']['I_ACCEPT'] = 'Ik accpeteer de';
$lang['nl_NL']['SilvercartPage']['I_HAVE_READ'] = 'Ik heb de';
$lang['nl_NL']['SilvercartPage']['ISACTIVE'] = 'Actief';
$lang['nl_NL']['SilvercartPage']['JANUARY'] = 'Januari';
$lang['nl_NL']['SilvercartPage']['JUNE'] = 'Juni';
$lang['nl_NL']['SilvercartPage']['JULY'] = 'Juli';
$lang['nl_NL']['SilvercartPage']['LOGIN'] = 'Login';
$lang['nl_NL']['SilvercartPage']['LOGOUT'] = 'Logout';
$lang['nl_NL']['SilvercartPage']['LOGO'] = 'Logo';
$lang['nl_NL']['SilvercartPage']['MARCH'] = 'Maart';
$lang['nl_NL']['SilvercartPage']['MAY'] = 'Mei';
$lang['nl_NL']['SilvercartPage']['MESSAGE'] = 'Bericht';
$lang['nl_NL']['SilvercartPage']['MONTH'] = 'Maand';
$lang['nl_NL']['SilvercartPage']['MYACCOUNT'] = 'Mijn account';
$lang['nl_NL']['SilvercartPage']['NAME'] = 'Naam';
$lang['nl_NL']['SilvercartPage']['NEWSLETTER'] = 'Nieuwsbrief';
$lang['nl_NL']['SilvercartPage']['NEWSLETTER_FORM'] = 'Nieuwsbrief instellingen';
$lang['nl_NL']['SilvercartPage']['NEXT'] = 'Volgende';
$lang['nl_NL']['SilvercartPage']['NOVEMBER'] = 'November';
$lang['nl_NL']['SilvercartPage']['NO_ORDERS'] = 'U heeft nog geen bestellingen';
$lang['nl_NL']['SilvercartPage']['NO_RESULTS'] = 'Sorry, maar uw zoekopdracht heeft geen resultaten opgeleverd.';
$lang['nl_NL']['SilvercartPage']['OCTOBER'] = 'Oktober';
$lang['nl_NL']['SilvercartPage']['ORDERED_PRODUCTS'] = 'Bestelde producten';
$lang['nl_NL']['SilvercartPage']['ORDER_COMPLETED'] = 'Uw bestelling is voltooid';
$lang['nl_NL']['SilvercartPage']['ORDER_DATE'] = 'Bestel datum';
$lang['nl_NL']['SilvercartPage']['ORDERS_EMAIL_INFORMATION_TEXT'] = 'Controleer uw e-mail inbox voor de opdrachtbevestiging.';
$lang['nl_NL']['SilvercartPage']['ORDER_THANKS'] = 'Hartelijk dank voor uw bestelling.';
$lang['nl_NL']['SilvercartPage']['PASSWORD'] = 'Wachtwoord';
$lang['nl_NL']['SilvercartPage']['PASSWORD_CASE_EMPTY'] = 'Als u dit veld leeg laat wordt uw wachtwoord niet gewijzigd.';
$lang['nl_NL']['SilvercartPage']['PASSWORD_CHECK'] = 'Herhalen';
$lang['nl_NL']['SilvercartPage']['PASSWORD_WRONG'] = 'Dit wachtwoord is onjuist.';
$lang['nl_NL']['SilvercartPage']['PAYMENT_NOT_WORKING'] = 'De gekozen betaalmodule werkt niet.';
$lang['nl_NL']['SilvercartPage']['PLUS_SHIPPING'] = 'excl. verzendkosten';
$lang['nl_NL']['SilvercartPage']['PREV'] = 'Vorige';
$lang['nl_NL']['SilvercartPage']['REGISTER'] = 'Registreer';
$lang['nl_NL']['SilvercartPage']['REMARKS'] = 'Opmerkingen';
$lang['nl_NL']['SilvercartPage']['REMOVE_FROM_CART'] = 'Verwijderen';
$lang['nl_NL']['SilvercartPage']['RETURNTOPRODUCTGROUP'] = 'Keer terug naar "%s"';
$lang['nl_NL']['SilvercartPage']['REVOCATION'] = 'nietigverklaring instructies';
$lang['nl_NL']['SilvercartPage']['REVOCATIONREAD'] = 'nietigverklaring instructies';
$lang['nl_NL']['SilvercartPage']['SAVE'] = 'Opslaan';
$lang['nl_NL']['SilvercartPage']['SEARCH_RESULTS'] = 'resultaten';
$lang['nl_NL']['SilvercartPage']['SEPTEMBER'] = 'September';
$lang['nl_NL']['SilvercartPage']['SESSION_EXPIRED'] = 'Uw sessie is verlopen.';
$lang['nl_NL']['SilvercartPage']['SHIPPING_ADDRESS'] = 'Verzendadres';
$lang['nl_NL']['SilvercartPage']['SHIPPING_AND_BILLING'] = 'Verzend- en factuuradres';
$lang['nl_NL']['SilvercartPage']['SHOP_WITHOUT_REGISTRATION'] = 'Winkel zonder aanmelding';
$lang['nl_NL']['SilvercartPage']['SHOW_DETAILS'] = 'Toon details';
$lang['nl_NL']['SilvercartPage']['SHOW_DETAILS_FOR'] = 'Toon details voor %s';
$lang['nl_NL']['SilvercartPage']['SHOWINPAGE'] = 'Wijzig taal in %s';
$lang['nl_NL']['SilvercartPage']['SITMAP_HERE'] = 'Hier zie je de complete mappen structuur op onze site.';
$lang['nl_NL']['SilvercartPage']['STEPS'] = 'Stappen';
$lang['nl_NL']['SilvercartPage']['SUBMIT'] = 'Verzenden';
$lang['nl_NL']['SilvercartPage']['SUBMIT_MESSAGE'] = 'Verzend bericht';
$lang['nl_NL']['SilvercartPage']['SUBTOTAL'] = 'Subtotaal';
$lang['nl_NL']['SilvercartPage']['SUBTOTAL_NET'] = 'Subtotaal (Netto)';
$lang['nl_NL']['SilvercartPage']['SUM'] = 'Totaal';
$lang['nl_NL']['SilvercartPage']['INCLUDING_TAX'] = 'incl. %s%% BTW';
$lang['nl_NL']['SilvercartPage']['EXCLUDING_TAX'] = 'excl. BTW';
$lang['nl_NL']['SilvercartPage']['TAX'] = 'incl. %d%% BTW';
$lang['nl_NL']['SilvercartPage']['TERMSOFSERVICE_PRIVACY'] = 'Algemene Voorwaarden en privacy statement';
$lang['nl_NL']['SilvercartPage']['THE_QUERY'] = 'De zoekopdracht';
$lang['nl_NL']['SilvercartPage']['TITLE'] = 'Titel';
$lang['nl_NL']['SilvercartPage']['TITLE_IMPRINT'] = 'Afdrukken';
$lang['nl_NL']['SilvercartPage']['TITLE_TERMS'] = 'Algemene voorwaarden';
$lang['nl_NL']['SilvercartPage']['TOTAL'] = 'Totaal';
$lang['nl_NL']['SilvercartPage']['URL_SEGMENT_IMPRINT'] = 'afdrukken';
$lang['nl_NL']['SilvercartPage']['URL_SEGMENT_TERMS'] = 'algemene-voorwaarden';
$lang['nl_NL']['SilvercartPage']['USER_NOT_EXISTING'] = 'Deze gebruiker bestaat niet.';
$lang['nl_NL']['SilvercartPage']['VALUE_OF_GOODS'] = 'Waarde van de goederen';
$lang['nl_NL']['SilvercartPage']['VIEW_ORDERS_TEXT'] = 'U kunt de status van uw bestelling te allen tijde bekijken in uw';
$lang['nl_NL']['SilvercartPage']['WELCOME_PAGE_TITLE'] = 'Welkom';
$lang['nl_NL']['SilvercartPage']['WELCOME_PAGE_URL_SEGMENT'] = 'Welkom';
$lang['nl_NL']['SilvercartPage']['YEAR'] = 'Jaar';

$lang['nl_NL']['SilvercartPaymentMethod']['ACCESS_MANAGEMENT_BASIC_LABEL'] = 'Algemeen';
$lang['nl_NL']['SilvercartPaymentMethod']['ACCESS_MANAGEMENT_GROUP_LABEL'] = 'Via groep(en)';
$lang['nl_NL']['SilvercartPaymentMethod']['ACCESS_MANAGEMENT_USER_LABEL'] = 'Via gebruiker(s)';
$lang['nl_NL']['SilvercartPaymentMethod']['ACCESS_SETTINGS'] = 'Toegangsbeheer';
$lang['nl_NL']['SilvercartPaymentMethod']['ATTRIBUTED_COUNTRIES'] = 'Toegewezen landen';
$lang['nl_NL']['SilvercartPaymentMethod']['BASIC_SETTINGS'] = 'Basisinstellingen';
$lang['nl_NL']['SilvercartPaymentMethod']['ENABLE_RESTRICTION_BY_ORDER_LABEL'] = 'Gebruik de volgende instelling';
$lang['nl_NL']['SilvercartPaymentMethod']['FROM_PURCHASE_VALUE'] = 'Van aankoopwaarde';
$lang['nl_NL']['SilvercartPaymentMethod']['MODE'] = 'vorm';
$lang['nl_NL']['SilvercartPaymentMethod']['NAME'] = 'Naam';
$lang['nl_NL']['SilvercartPaymentMethod']['NO_PAYMENT_METHOD_AVAILABLE'] = 'Geen betaalmethode beschikbaar.';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_SUMMODIFICATIONIMPACTVALUETYPE'] = 'De waarde is';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_IMPACT_TYPE_ABSOLUTE'] = 'Geheel getal';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_IMPACT_TYPE_PERCENT'] = 'In procenten';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_LOGOS'] = 'Logos';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_MODIFY_PRODUCTVALUE'] = 'Productwaarde';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_MODIFY_TOTALVALUE'] = 'Totale waarde';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_MODIFY_TYPE_CHARGE'] = 'Toeslag';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_MODIFY_TYPE_DISCOUNT'] = 'Kosten';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_SUMMODIFICATIONIMPACT'] = 'Korting';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_SUMMODIFICATIONLABELFIELD'] = 'Etiket voor winkelwagen / bestelling';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_SUMMODIFICATIONIMPACTTYPE'] = 'Type';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_SUMMODIFICATIONVALUE'] = 'Waarde';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_SUMMODIFIERS'] = 'Kosten/Kortingen';
$lang['nl_NL']['SilvercartPaymentMethod']['PAYMENT_USE_SUMMODIFICATION'] = 'Activeren';
$lang['nl_NL']['SilvercartPaymentMethod']['PLURALNAME'] = 'Betaalmogelijkheden';
$lang['nl_NL']['SilvercartPaymentMethod']['RESTRICT_BY_ORDER_QUANTITY'] = 'De klant moet het volgende aantal bestellingen hebben voltooid';
$lang['nl_NL']['SilvercartPaymentMethod']['RESTRICT_BY_ORDER_STATUS'] = 'de status van bestellingen is gemarkeerd in de volgende lijst';
$lang['nl_NL']['SilvercartPaymentMethod']['RESTRICTION_LABEL'] = 'Activeer alleen wanneer aan de volgende criteria wordt voldaan';
$lang['nl_NL']['SilvercartPaymentMethod']['SHIPPINGMETHOD'] = 'verzendmethode';
$lang['nl_NL']['SilvercartPaymentMethod']['SHOW_NOT_FOR_GROUPS_LABEL'] = 'Deactiveren van de volgende groepen';
$lang['nl_NL']['SilvercartPaymentMethod']['SHOW_ONLY_FOR_GROUPS_LABEL'] = 'Activeren voor de volgende groepen';
$lang['nl_NL']['SilvercartPaymentMethod']['SHOW_NOT_FOR_USERS_LABEL'] = 'Deactiveer voor de volgende gebruikers';
$lang['nl_NL']['SilvercartPaymentMethod']['SHOW_ONLY_FOR_USERS_LABEL'] = 'Activeer voor de volgende gebruikers';
$lang['nl_NL']['SilvercartPaymentMethod']['SHOW_FORM_FIELDS_ON_PAYMENT_SELECTION'] = 'Toon selectie van formuliervelden betreffende betalingsdiensten';
$lang['nl_NL']['SilvercartPaymentMethod']['SINGULARNAME'] = 'Betaalwijze';
$lang['nl_NL']['SilvercartPaymentMethod']['STANDARD_ORDER_STATUS'] = 'Standaard orderstatus voor deze betaalmethode';
$lang['nl_NL']['SilvercartPaymentMethod']['TILL_PURCHASE_VALUE'] = 'tot aanschafwaarde';
$lang['nl_NL']['SilvercartPaymentMethod']['TITLE'] = 'Betaalwijze';

$lang['nl_NL']['SilvercartPaymentMethodTexts']['PLURALNAME'] = 'Betaalmogelijkheden vertalingen';
$lang['nl_NL']['SilvercartPaymentMethodTexts']['SINGULARNAME'] = 'Betaalmogelijkheden vertaling';

$lang['nl_NL']['SilvercartPaymentNotification']['DEFAULT_TITLE'] = 'Betaalmogelijkheden melding';
$lang['nl_NL']['SilvercartPaymentNotification']['DEFAULT_URLSEGMENT'] = 'betaalmogelijkheden-melding';
$lang['nl_NL']['SilvercartPaymentNotification']['PLURALNAME'] = 'Betaalmogelijkheden Meldingen';
$lang['nl_NL']['SilvercartPaymentNotification']['SINGULARNAME'] = 'Betaalmogelijkheden Melding';
$lang['nl_NL']['SilvercartPaymentNotification']['TITLE'] = 'Betaalmogelijkheden melding';
$lang['nl_NL']['SilvercartPaymentNotification']['URL_SEGMENT'] = 'betaalmogelijkheden-melding';

$lang['nl_NL']['SilvercartPrice']['PLURALNAME'] = 'Tarieven';
$lang['nl_NL']['SilvercartPrice']['SINGULARNAME'] = 'Terief';

$lang['nl_NL']['SilvercartProductCondition']['PLEASECHOOSE']    = 'Maak een keuze';
$lang['nl_NL']['SilvercartProductCondition']['PLURALNAME']      = 'Product condities';
$lang['nl_NL']['SilvercartProductCondition']['SINGULARNAME']    = 'Product conditie';
$lang['nl_NL']['SilvercartProductCondition']['TITLE']           = 'Conditie';
$lang['nl_NL']['SilvercartProductCondition']['USE_AS_STANDARD_CONDITION']   = 'Gebruik als standaard conditie indien niet bepaald op het product';

$lang['nl_NL']['SilvercartQuickSearchForm']['SUBMITBUTTONTITLE'] = 'Zoeken';

$lang['nl_NL']['SilvercartRating']['SINGULARNAME'] = 'waardering';
$lang['nl_NL']['SilvercartRating']['PLURALNAME'] = 'waarderingen';
$lang['nl_NL']['SilvercartRating']['TEXT'] = 'waardering tekst';
$lang['nl_NL']['SilvercartRating']['GRADE'] = 'waarderingsklasse';

$lang['nl_NL']['SilvercartRegisterConfirmationPage']['ALREADY_REGISTERES_MESSAGE_TEXT'] = 'Bericht: gebruiker reeds ingeschreven';
$lang['nl_NL']['SilvercartRegisterConfirmationPage']['CONFIRMATIONMAIL_SUBJECT'] = 'Bevestigings e-mail: onderwerp';
$lang['nl_NL']['SilvercartRegisterConfirmationPage']['CONFIRMATIONMAIL_TEXT'] = 'Bevestigings e-mail: tekst';
$lang['nl_NL']['SilvercartRegisterConfirmationPage']['CONFIRMATION_MAIL'] = 'Bevestigings e-mail';
$lang['nl_NL']['SilvercartRegisterConfirmationPage']['CONTENT'] = '<p>Beste klant,</p><p>voor uw gemak, u bent al ingelogd</p><p>Veel plezier! </p>';
$lang['nl_NL']['SilvercartRegisterConfirmationPage']['DEFAULT_CONTENT'] = '<p>Beste klant,</p><p>voor uw gemak, u bent al ingelogd</p><p>Veel plezier! </p>';
$lang['nl_NL']['SilvercartRegisterConfirmationPage']['DEFAULT_TITLE'] = 'Registreer bevestiging';
$lang['nl_NL']['SilvercartRegisterConfirmationPage']['DEFAULT_URLSEGMENT'] = 'registreer-bevestiging';
$lang['nl_NL']['SilvercartRegisterConfirmationPage']['FAILURE_MESSAGE_TEXT'] = 'Foutmelding';
$lang['nl_NL']['SilvercartRegisterConfirmationPage']['PLURALNAME'] = 'Registreer bevestigingspagina\'s';
$lang['nl_NL']['SilvercartRegisterConfirmationPage']['SINGULARNAME'] = 'Registreer bevestigingspagina';
$lang['nl_NL']['SilvercartRegisterConfirmationPage']['SUCCESS_MESSAGE_TEXT'] = 'Succesvol melding';
$lang['nl_NL']['SilvercartRegisterConfirmationPage']['TITLE'] = 'Registreren bevestigingspagina';
$lang['nl_NL']['SilvercartRegisterConfirmationPage']['URL_SEGMENT'] = 'registreer-bevestiging';

$lang['nl_NL']['SilvercartRegistrationPage']['ACTIVATION_MAIL_TAB'] = 'Activatie email';
$lang['nl_NL']['SilvercartRegistrationPage']['ACTIVATION_MAIL_SUBJECT'] = 'Actievatie email onderwerp';
$lang['nl_NL']['SilvercartRegistrationPage']['ACTIVATION_MAIL_TEXT'] = 'Activatie email tekst';
$lang['nl_NL']['SilvercartRegistrationPage']['CONFIRMATION_TEXT'] = '<h1>Volledige registratie</h1><p>Bevestig de activering of kopiëer de link naar uw browser.</p><p><a href="$ConfirmationLink">Bevestig de registratie</a></p><p>In het geval dat u zich niet hebt geregistreerd, kunt u deze mail. negeren</p><p>Uw webwinkel team</p>';
$lang['nl_NL']['SilvercartRegistrationPage']['CUSTOMER_SALUTATION'] = 'Geachte klant\,';
$lang['nl_NL']['SilvercartRegistrationPage']['DEFAULT_TITLE'] = 'Registratie';
$lang['nl_NL']['SilvercartRegistrationPage']['DEFAULT_URLSEGMENT'] = 'registratie';
$lang['nl_NL']['SilvercartRegistrationPage']['EMAIL_EXISTS_ALREADY'] = 'Dit email adres bestaat reeds.';
$lang['nl_NL']['SilvercartRegistrationPage']['OTHERITEMS'] = 'Diversen';
$lang['nl_NL']['SilvercartRegistrationPage']['PLEASE_COFIRM'] = 'AUB bevestig uw registratie';
$lang['nl_NL']['SilvercartRegistrationPage']['PLURALNAME'] = 'Registratie Pagina\'s';
$lang['nl_NL']['SilvercartRegistrationPage']['SINGULARNAME'] = 'Registratie Pagina';
$lang['nl_NL']['SilvercartRegistrationPage']['SUCCESS_TEXT'] = '<h1>Registratie is voltooid!</h1><p>Hartelijk dank voor uw registratie.</p><p>Veel plezier op onze website!</p><p>Uw webwinkel team</p>';
$lang['nl_NL']['SilvercartRegistrationPage']['THANKS'] = 'Hartelijk dank voor uw registratie.';
$lang['nl_NL']['SilvercartRegistrationPage']['TITLE'] = 'Registratie';
$lang['nl_NL']['SilvercartRegistrationPage']['URL_SEGMENT'] = 'registratie';
$lang['nl_NL']['SilvercartRegistrationPage']['YOUR_REGISTRATION'] = 'Uw registratie';

$lang['nl_NL']['SilvercartSearchResultsPage']['DEFAULT_TITLE'] = 'Zoekresultaten';
$lang['nl_NL']['SilvercartSearchResultsPage']['DEFAULT_URLSEGMENT'] = 'zoekresultaten';
$lang['nl_NL']['SilvercartSearchResultsPage']['PLURALNAME'] = 'Zoekresultaten Pagina';
$lang['nl_NL']['SilvercartSearchResultsPage']['SINGULARNAME'] = 'Pagina met zoekresultaten';
$lang['nl_NL']['SilvercartSearchResultsPage']['TITLE'] = 'Zoekresultaten';
$lang['nl_NL']['SilvercartSearchResultsPage']['URL_SEGMENT'] = 'zoekresultaten';
$lang['nl_NL']['SilvercartSearchResultsPage']['RESULTTEXT'] = 'Zoekresultaten voor zoekopdracht <b>&rdquo;%s&rdquo;</b>';

$lang['nl_NL']['SilvercartShippingAddress']['PLURALNAME'] = 'Verzenden adressen';
$lang['nl_NL']['SilvercartShippingAddress']['SINGULARNAME'] = 'Verzenden adres';

$lang['nl_NL']['SilvercartShippingFee']['ATTRIBUTED_SHIPPINGMETHOD'] = 'Toegewezen verzendmethode';
$lang['nl_NL']['SilvercartShippingFee']['COSTS'] = 'Kosten';
$lang['nl_NL']['SilvercartShippingFee']['EMPTYSTRING_CHOOSEZONE'] = '--kies zone--';
$lang['nl_NL']['SilvercartShippingFee']['FOR_SHIPPINGMETHOD'] = 'Voor verzendmethode';
$lang['nl_NL']['SilvercartShippingFee']['MAXIMUM_WEIGHT'] = 'Maximaal gewicht (g)';
$lang['nl_NL']['SilvercartShippingFee']['PLURALNAME'] = 'Verzendkosten';
$lang['nl_NL']['SilvercartShippingFee']['SINGULARNAME'] = 'Verzendkosten';
$lang['nl_NL']['SilvercartShippingFee']['UNLIMITED_WEIGHT'] = 'onbeperkt';
$lang['nl_NL']['SilvercartShippingFee']['UNLIMITED_WEIGHT_LABEL'] = 'Onbeperkt Maximaal Gewicht';
$lang['nl_NL']['SilvercartShippingFee']['ZONE_WITH_DESCRIPTION'] = 'Zone (alleen vervoerders zones beschikbaar)';

$lang['nl_NL']['SilvercartShippingFeesPage']['DEFAULT_TITLE'] = 'Verzendkosten';
$lang['nl_NL']['SilvercartShippingFeesPage']['DEFAULT_URLSEGMENT'] = 'verzendkosten';
$lang['nl_NL']['SilvercartShippingFeesPage']['PLURALNAME'] = 'Verzendkosten Pagina\'s';
$lang['nl_NL']['SilvercartShippingFeesPage']['SINGULARNAME'] = 'Verzendkosten Pagina';
$lang['nl_NL']['SilvercartShippingFeesPage']['TITLE'] = 'Verzendkosten';
$lang['nl_NL']['SilvercartShippingFeesPage']['URL_SEGMENT'] = 'verzendkosten';

$lang['nl_NL']['SilvercartShippingMethod']['FOR_PAYMENTMETHODS'] = 'Voor betalingsmethoden';
$lang['nl_NL']['SilvercartShippingMethod']['FOR_ZONES'] = 'Voor zones';
$lang['nl_NL']['SilvercartShippingMethod']['PACKAGE'] = 'Vrpakking';
$lang['nl_NL']['SilvercartShippingMethod']['PLURALNAME'] = 'Verzendmethoden';
$lang['nl_NL']['SilvercartShippingMethod']['SINGULARNAME'] = 'Verzendmethode';
$lang['nl_NL']['SilvercartShippingMethod']['CHOOSE_DATAOBJECT_TO_IMPORT'] = 'Wat wil je importeren?';

$lang['nl_NL']['SilvercartShippingMethodTexts']['PLURALNAME'] = 'Verzendwijze vertalingen';
$lang['nl_NL']['SilvercartShippingMethodTexts']['SINGULARNAME'] = 'Verzendwijze vertaling';

$lang['nl_NL']['SilvercartShopAdmin']['PAYMENT_DESCRIPTION'] = 'Omschrijving';
$lang['nl_NL']['SilvercartShopAdmin']['PAYMENT_ISACTIVE'] = 'Geactiveerd';
$lang['nl_NL']['SilvercartShopAdmin']['PAYMENT_MAXAMOUNTFORACTIVATION'] = 'Maximaal bedrag';
$lang['nl_NL']['SilvercartShopAdmin']['PAYMENT_MINAMOUNTFORACTIVATION'] = 'Minimaal bedrag';
$lang['nl_NL']['SilvercartShopAdmin']['PAYMENT_MODE_DEV'] = 'Ontw.';
$lang['nl_NL']['SilvercartShopAdmin']['PAYMENT_MODE_LIVE'] = 'Live';
$lang['nl_NL']['SilvercartShopAdmin']['SHOW_PAYMENT_LOGOS'] = 'Toon logo\'s in frontend';

$lang['nl_NL']['SilvercartShopAdministrationAdmin']['TITLE'] = 'SC Admin';

$lang['nl_NL']['SilvercartShopConfigurationAdmin']['SILVERCART_CONFIG'] = 'SC Config';

$lang['nl_NL']['SilvercartShopEmail']['EMAILTEXT'] = 'Bericht';
$lang['nl_NL']['SilvercartShopEmail']['IDENTIFIER'] = 'Identificatie';
$lang['nl_NL']['SilvercartShopEmail']['PLURALNAME'] = 'Winkel Emails';
$lang['nl_NL']['SilvercartShopEmail']['SINGULARNAME'] = 'Winkel Email';
$lang['nl_NL']['SilvercartShopEmail']['SUBJECT'] = 'Onderwerp';
$lang['nl_NL']['SilvercartShopEmail']['VARIABLES'] = 'Variabele';
$lang['nl_NL']['SilvercartShopEmail']['REGARDS'] = 'Met vriendelijk groet';
$lang['nl_NL']['SilvercartShopEmail']['YOUR_TEAM'] = 'Uw SilverCart CART Team';
$lang['nl_NL']['SilvercartShopEmail']['HELLO'] = 'Hallo';
$lang['nl_NL']['SilvercartShopEmail']['ADDITIONALS_RECEIPIENTS'] = 'extra ontvangers';
$lang['nl_NL']['SilvercartShopEmail']['ORDER_ARRIVED'] = 'We hebben net uw bestelling ontvangen, hartelijk dank.';
$lang['nl_NL']['SilvercartShopEmail']['ORDER_ARRIVED_EMAIL_SUBJECT'] = 'Uw bestelling in onze online winkel';
$lang['nl_NL']['SilvercartShopEmail']['ORDER_SHIPPED_MESSAGE'] = 'Uw bestelling is net verzonden.';
$lang['nl_NL']['SilvercartShopEmail']['ORDER_SHIPPED_NOTIFICATION_SUBJECT'] = 'Uw bestelling is net verzonden.';
$lang['nl_NL']['SilvercartShopEmail']['NEW_ORDER_PLACED'] = 'Een nieuwe bestelling is geplaatst';


// SilvercartTestData no Translation --------------------------------------- */

$lang['nl_NL']['SilvercartUpdate']['DESCRIPTION'] = 'Omschrijving';
$lang['nl_NL']['SilvercartUpdate']['SILVERCARTVERSION'] = 'Versie';
$lang['nl_NL']['SilvercartUpdate']['SILVERCARTUPDATEVERSION'] = 'Update';
$lang['nl_NL']['SilvercartUpdate']['STATUS'] = 'Status';
$lang['nl_NL']['SilvercartUpdate']['STATUSMESSAGE'] = 'Statusbericht';
$lang['nl_NL']['SilvercartUpdate']['STATUS_DONE'] = 'Voltooid';
$lang['nl_NL']['SilvercartUpdate']['STATUS_REMAINING'] = 'Resterende';
$lang['nl_NL']['SilvercartUpdate']['STATUS_SKIPPED'] = 'Overgeslagen';
$lang['nl_NL']['SilvercartUpdate']['STATUSMESSAGE_DONE'] = 'Deze update is met succes afgerond.';
$lang['nl_NL']['SilvercartUpdate']['STATUSMESSAGE_REMAINING'] = 'Deze update is overgebleven.';
$lang['nl_NL']['SilvercartUpdate']['STATUSMESSAGE_SKIPPED'] = 'Deze update is al geïntegreerd.';
$lang['nl_NL']['SilvercartUpdate']['STATUSMESSAGE_SKIPPED_TO_PREVENT_DAMAGE'] = 'Manual veranderingen gedetecteerd. Deze update werd overgeslagen om schade op de bestaande gegevens te voorkomen.';
$lang['nl_NL']['SilvercartUpdate']['STATUSMESSAGE_ERROR'] = 'Er is een onbekende fout opgetreden.';

$lang['nl_NL']['SilvercartUpdateAdmin']['SILVERCART_UPDATE'] = 'SC Updates';

$lang['nl_NL']['SilvercartWidget']['SORT_ORDER_LABEL'] = 'Sorteervolgorde';

$lang['nl_NL']['SilvercartWidgets']['WIDGETSET_CONTENT_FIELD_LABEL'] = 'Widgets voor het content gebied';
$lang['nl_NL']['SilvercartWidgets']['WIDGETSET_SIDEBAR_FIELD_LABEL'] = 'Widgets voor de sidebar';

$lang['nl_NL']['SilvercartWidgetSet']['PLURALNAME'] = 'Widget Sets';
$lang['nl_NL']['SilvercartWidgetSet']['SINGULARNAME'] = 'Widget Set';
$lang['nl_NL']['SilvercartWidgetSet']['PAGES'] = 'toegekende pagina\'s';
$lang['nl_NL']['SilvercartWidgetSet']['INFO'] = '<strong>Let OP:</strong><br/>Toevoegen of bewerken van een Widget, kies "SC Config" in het hoofdmenu. Aldaar, kies "Widget Set" uit de dropdown lijst aan de linker kant om de formulieren toe te voegen of te bewerken.';

$lang['nl_NL']['SilvercartZone']['ATTRIBUTED_COUNTRIES'] = 'Toegewezen landen';
$lang['nl_NL']['SilvercartZone']['ATTRIBUTED_SHIPPINGMETHODS'] = 'Toegewezen verzendmethodes';
$lang['nl_NL']['SilvercartZone']['COUNTRIES'] = 'Landen';
$lang['nl_NL']['SilvercartZone']['DOMESTIC'] = 'Binnenland';
$lang['nl_NL']['SilvercartZone']['FOR_COUNTRIES'] = 'Voor landen';
$lang['nl_NL']['SilvercartZone']['PLURALNAME'] = 'Zones';
$lang['nl_NL']['SilvercartZone']['SINGULARNAME'] = 'Zone';

$lang['nl_NL']['SilvercartQuantityUnit']['NAME'] = 'Naam';
$lang['nl_NL']['SilvercartQuantityUnit']['ABBREVIATION'] = 'Afkorting';
$lang['nl_NL']['SilvercartQuantityUnit']['SINGULARNAME'] = 'aantal eenheid';
$lang['nl_NL']['SilvercartQuantityUnit']['PLURALNAME'] = 'aantal eenheden';

// Widgets ----------------------------------------------------------------- */

$lang['nl_NL']['SilvercartLatestBlogPostsWidget']['CMSTITLE']                   = 'Toon laatste blog-berichten';
$lang['nl_NL']['SilvercartLatestBlogPostsWidget']['DESCRIPTION']                = 'Toont de meest recente blog-berichten.';
$lang['nl_NL']['SilvercartLatestBlogPostsWidget']['IS_CONTENT_VIEW']            = 'Gebruik het normale overzicht in plaats van de widgetview';
$lang['nl_NL']['SilvercartLatestBlogPostsWidget']['SHOW_ENTRY']                 = 'Lees bericht';
$lang['nl_NL']['SilvercartLatestBlogPostsWidget']['STOREADMIN_NUMBEROFPOSTS']   = 'Toon het aantal blog-berichten';
$lang['nl_NL']['SilvercartLatestBlogPostsWidget']['TITLE']                      = 'Toon laatste blog-berichten';
$lang['nl_NL']['SilvercartLatestBlogPostsWidget']['WIDGET_TITLE']               = 'Widget titel';

$lang['nl_NL']['SilvercartLoginWidget']['TITLE']                    = 'Login';
$lang['nl_NL']['SilvercartLoginWidget']['TITLE_LOGGED_IN']          = 'Uw klantgegevens';
$lang['nl_NL']['SilvercartLoginWidget']['TITLE_NOT_LOGGED_IN']      = 'Login';
$lang['nl_NL']['SilvercartLoginWidget']['CMSTITLE']                 = 'SilverCart login';
$lang['nl_NL']['SilvercartLoginWidget']['DESCRIPTION']              = 'Deze widget toont een login formulier en links naar de registratiepagina. Als de klant al is ingelogd, worden in plaats daarvan links naar hun KLantgegevens weergegeven.';

$lang['nl_NL']['SilvercartProductGroupItemsWidget']['AUTOPLAY']                             = 'Activeer de automatische diashow';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['AUTOPLAYDELAYED']                      = 'Activeer vertraging voor de automatische diashow';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['AUTOPLAYLOCKED']                       = 'Deactiveer de automatische diashow als een gebruiker handmatig navigeert';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['BUILDARROWS']                          = 'Toon Volgende/Vorige knoppen';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['BUILDNAVIGATION']                      = 'Toon paginanavigatie';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['BUILDSTARTSTOP']                       = 'Toon start/stop-knoppen';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['CMSTITLE']                             = 'SilverCart productgroep onderdelen';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['DESCRIPTION']                          = 'Deze widget toont de producten van een definieerbare productgroep. U kunt aangeven hoe veel producten uit die groep van producten moet worden weergegeven.';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['FETCHMETHOD']                          = 'Selectiemethode voor producten';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['FETCHMETHOD_RANDOM']                   = 'Willekeurig';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['FETCHMETHOD_SORTORDERASC']             = 'Oplopend';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['FETCHMETHOD_SORTORDERDESC']            = 'Aflopend';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['FRONTTITLE']                           = 'Headline';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['FRONTCONTENT']                         = 'Content';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['IS_CONTENT_VIEW']                      = 'Gebruik het normale overzicht in plaats van de widgetview';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['SLIDEDELAY']                           = 'Duur van de vertoning voor de automatische diashow';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['CMS_BASICTABNAME']                     = 'Basis voorkeuren';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['CMS_ROUNDABOUTTABNAME']                = 'Roundabout';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['CMS_SLIDERTABNAME']                    = 'Slideshow';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['STOPATEND']                            = 'Stop automatische diashow na het laatste vertoning';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['STOREADMIN_FIELDLABEL']                = 'Kies de productgroep om te laten zien:';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['STOREADMIN_NUMBEROFPRODUCTSTOFETCH']   = 'Aantal producten ophalen:';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['STOREADMIN_NUMBEROFPRODUCTSTOSHOW']    = 'Aantal producten tonen:';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['TITLE']                                = 'Product groep';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['TRANSITIONEFFECT']                     = 'Overgangseffect';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['TRANSITION_FADE']                      = 'Fade';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['TRANSITION_HORIZONTALSLIDE']           = 'Horizontale diashow';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['TRANSITION_VERTICALSLIDE']             = 'Verticale diashow';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['USE_LISTVIEW']                         = 'Gebruik lijstweergave';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['USE_ROUNDABOUT']                       = 'Gebruik roundabout';
$lang['nl_NL']['SilvercartProductGroupItemsWidget']['USE_SLIDER']                           = 'Gebruik diashow';

$lang['nl_NL']['SilvercartProductGroupSliderWidget']['CMSTITLE']                            = 'Diashow voor productgroepen';
$lang['nl_NL']['SilvercartProductGroupSliderWidget']['DESCRIPTION']                         = 'Maak een diashow voor alle productgroepen.';
$lang['nl_NL']['SilvercartProductGroupSliderWidget']['TITLE']                               = 'Diashow voor productgroepen';

$lang['nl_NL']['SilvercartSearchWidget']['TITLE']                   = 'Wat zoekt u?';
$lang['nl_NL']['SilvercartSearchWidget']['CMSTITLE']                = 'SilverCartCart zoeken';
$lang['nl_NL']['SilvercartSearchWidget']['DESCRIPTION']             = 'Deze widget toont het product zoekformulier.';

$lang['nl_NL']['SilvercartSearchWidgetForm']['SEARCHLABEL']         = 'Geef uw zoekterm:';
$lang['nl_NL']['SilvercartSearchWidgetForm']['SUBMITBUTTONTITLE']   = 'Zoeken';

$lang['nl_NL']['SilvercartShoppingcartWidget']['TITLE']                 = 'Winkelwagen';
$lang['nl_NL']['SilvercartShoppingcartWidget']['CMSTITLE']              = 'SilverCart winkelwagen';
$lang['nl_NL']['SilvercartShoppingcartWidget']['DESCRIPTION']           = 'Deze widget toont de inhoud van de winkelwagen. Daarnaast de links naar de winkelwagen en (als er producten in de winkelwagen) de Afrekenen pagina\'s.';

$lang['nl_NL']['SilvercartSubNavigationWidget']['TITLE']                = 'Subnavigatie';
$lang['nl_NL']['SilvercartSubNavigationWidget']['CMSTITLE']             = 'SilverCart subnavigatie';
$lang['nl_NL']['SilvercartSubNavigationWidget']['DESCRIPTION']          = 'Deze widget toont een subnavigatie van de huidige sectie en zijn onderliggende pagina\'s.';

$lang['nl_NL']['SilvercartText']['TITLE']               = 'Vrije tekst';
$lang['nl_NL']['SilvercartText']['DESCRIPTION']         = 'Voer een willekeurige gewenste tekst in.';
$lang['nl_NL']['SilvercartText']['FREETEXTFIELD_LABEL'] = 'Uw tekst:';

$lang['nl_NL']['SilvercartTopsellerProductsWidget']['TITLE']                    = 'Best verkocht';
$lang['nl_NL']['SilvercartTopsellerProductsWidget']['CMSTITLE']                 = 'SilverCart Best verkochte producten';
$lang['nl_NL']['SilvercartTopsellerProductsWidget']['DESCRIPTION']              = 'Deze widget toont een instelbare aantal van de beste verkopende producten.';
$lang['nl_NL']['SilvercartTopsellerProductsWidget']['STOREADMIN_FIELDLABEL']    = 'Aantalte tonen producten:';

$lang['nl_NL']['SilvercartProductGroupNavigationWidget']['TITLE']           = 'Productgroep navigatie';
$lang['nl_NL']['SilvercartProductGroupNavigationWidget']['CMSTITLE']        = 'SilverCart productgroep navigatie';
$lang['nl_NL']['SilvercartProductGroupNavigationWidget']['DESCRIPTION']     = 'Deze widget maakt een hiërarchische navigatie voor de productgroepen. U kunt bepalen wat productgroep dient te worden gebruikt als root.';

$lang['nl_NL']['SilvercartSiteConfig']['DASHBOARD_TAB']             = 'SilverCart Dashboard';
$lang['nl_NL']['SilvercartSiteConfig']['WELCOME_TO_SILVERCART']     = 'Welkom bij SilverCart';
$lang['nl_NL']['SilvercartSiteConfig']['TESTDATA_HEADLINE']         = 'Testdata';
$lang['nl_NL']['SilvercartSiteConfig']['TESTDATA_TEXT']             = 'Er zijn nog geen producten, als je wat testdata wilt maken klik je op de volgende link:';
$lang['nl_NL']['SilvercartSiteConfig']['TESTDATA_LINKTEXT']         = 'Spring naar de testdata sectie';

$lang['nl_NL']['SiteConfig']['SITENAMEDEFAULT'] = 'SilverCart';
$lang['nl_NL']['SiteConfig']['TAGLINEDEFAULT']  = 'eCommerce software. Open-source. Je zult het geweldig vinden.';

$lang['nl_NL']['TermsOfServicePage']['DEFAULT_TITLE']                           = $lang['nl_NL']['SilvercartPage']['TITLE_TERMS'];
$lang['nl_NL']['TermsOfServicePage']['DEFAULT_URLSEGMENT']                      = $lang['nl_NL']['SilvercartPage']['URL_SEGMENT_TERMS'];

$lang['nl_NL']['ImprintPage']['DEFAULT_TITLE']                                  = $lang['nl_NL']['SilvercartPage']['TITLE_IMPRINT'];
$lang['nl_NL']['ImprintPage']['DEFAULT_URLSEGMENT']                             = $lang['nl_NL']['SilvercartPage']['URL_SEGMENT_IMPRINT'];

$lang['nl_NL']['SilvercartDataPrivacyStatementPage']['DEFAULT_TITLE']           = $lang['nl_NL']['SilvercartDataPrivacyStatementPage']['TITLE'];
$lang['nl_NL']['SilvercartDataPrivacyStatementPage']['DEFAULT_URLSEGMENT']      = $lang['nl_NL']['SilvercartDataPrivacyStatementPage']['URL_SEGMENT'];





