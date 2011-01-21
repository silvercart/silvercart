<?php

/**
 * Die Payment Basisklasse.
 *
 * Jedes Zahlungsmodul muss diese Klasse erweitern, um verfuegbar zu sein.
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 07.11.2010
 * @license none
 */
class PaymentMethod extends DataObject {
    // ------------------------------------------------------------------------
    // Klassenvariablen
    // ------------------------------------------------------------------------

    /**
     * Enthaelt den Link, der bei Abbruch durch den Benutzer oder Sessionablauf
     * angesprungen werden soll.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    protected $cancelLink = '';
    /**
     * Enthaelt den Link, der fuer den Ruecksprung in den Shop benutzt werden
     * soll.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    protected $returnLink = '';
    /**
     * Gibt an, ob ein Fehler aufgetreten ist.
     *
     * @var bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    protected $errorOccured;
    /**
     * Enthaelt eine Liste mit Fehlern.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    protected $errorList = array();
    // ------------------------------------------------------------------------
    // Attribute und Beziehungen
    // ------------------------------------------------------------------------
    /**
     * Definiert die Attribute der Klasse.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public static $db = array(
        'isActive' => 'Boolean',
        'minAmountForActivation' => 'Float',
        'maxAmountForActivation' => 'Float',
        'Name' => 'Varchar(150)',
        'Description' => 'Text',
        'mode' => "Enum('Live,Dev','Dev')",
        'orderStatus' => "Varchar(50)"
    );
    /**
     * Definiert die 1:1 Beziehungen der Klasse.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public static $has_one = array(
        'HandlingCost' => 'HandlingCost',
        'Zone' => 'Zone'
    );
    /**
     * Defines 1:n relations
     *
     * @var array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     */
    public static $has_many = array(
        'orders' => 'Order'
    );
    /**
     * Definiert die n:m Beziehungen der Klasse.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public static $many_many = array(
        'ShippingMethods' => 'ShippingMethod'
    );
    /**
     * Defines m:n relations
     *
     * @var array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     */
    public static $belongs_many_many = array(
        'countries' => 'Country'
    );
    /**
     * Legt fest, welche Spalten in der Tabellenuebersicht angezeigt werden.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 08.11.2010
     */
    public static $summary_fields = array(
        'isActive'=> 'aktiviert?',
        'Name' => 'Bezeichnung',
        'minAmountForActivation' => 'Mindestbetrag',
        'maxAmountForActivation' => 'Hoechstbetrag'
    );
    /**
     * Legt die Bezeichnungen fuer die Felder fest.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 08.11.2010
     */
    public static $field_labels = array(
        'isActive' => 'Aktiviert',
        'Name' => 'Name',
        'minAmountForActivation' => 'Ab Einkaufswert',
        'maxAmountForActivation' => 'Bis Einkaufswert'
    );
    /**
     * Enthaelt Informationen, die fuer die Kaufabwicklung von Belang sein
     * koennen.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 03.12.2010
     */
    protected $data = array(
        'customer' => array(
            'details' => array(
                'Salutation' => '',
                'FirstName' => '',
                'SurName' => '',
                'Email' => '',
                'Phone' => ''
            ),
            'deliveryAddress' => array(
                'Salutation' => '',
                'FirstName' => '',
                'SurName' => '',
                'Street' => '',
                'StreetNumber' => '',
                'PostCode' => '',
                'City' => '',
                'State' => '',
                'Country' => ''
            ),
            'shippingAddress' => array(
                'Salutation' => '',
                'FirstName' => '',
                'SurName' => '',
                'Street' => '',
                'StreetNumber' => '',
                'PostCode' => '',
                'City' => '',
                'State' => '',
                'Country' => ''
            )
        ),
        'order' => array(
            'amount_net' => 0.0,
            'amount_gross' => 0.0,
            'tax_amount_net' => 0.0,
            'tax_amount_gross' => 0.0,
            'tax_rate' => 0.0,
            'positions' => array()
        ),
        'handlingCosts' => array(
            'amount_net' => 0.0,
            'amount_gross' => 0.0,
            'tax_amount_net' => 0.0,
            'tax_amount_gross' => 0.0,
            'tax_rate' => 0.0,
        ),
        'shippingCosts' => array(
            'amount' => 0.0
        )
    );
    /**
     * Enthaelt den Modulname zur Anzeige in der Adminoberflaeche.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 03.12.2010
     */
    protected $moduleName = '';
    /**
     * Enthaelt eine Referenz auf das Controller-Objekt.
     *
     * @var Controller
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 19.11.2010
     */
    protected $controller;

    // ------------------------------------------------------------------------
    // Methoden
    // ------------------------------------------------------------------------

    /**
     * Liefert den Titel der Zahlungsart.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function getTitle() {
        return $this->moduleName;
    }

    /**
     * Liefert den Status den die Bestellung annehmen soll, wenn dieses
     * Modul als Bezahlart gewaehlt wird.
     *
     * @return string Code des Bestellstatus
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 23.11.2010
     */
    public function getDefaultOrderStatus() {
        return $this->orderStatus;
    }

    /**
     * Liefert die Beschreibung der Zahlungsart.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function getDescription() {

    }

    /**
     * Liefert den Pfad zum Logo der Zahlungsart.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function getLogo() {

    }

    /**
     * Liefert den Link, der bei Abbruch durch den Benutzer oder Sessionablauf
     * angesprungen werden soll.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getCancelLink() {
        return $this->cancelLink;
    }

    /**
     * Liefert den Link, der fuer den Ruecksprung in den Shop benutzt werden
     * soll.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getReturnLink() {
        return $this->returnLink;
    }

    /**
     * Liefert die Bearbeitungskosten fuer die Zahlungsart.
     *
     * @return float
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function getHandlingCost() {
        $handlingCosts = new Money;
        $handlingCosts->setAmount(0);

        return $handlingCosts;
    }

    /**
     * Liefert den Pfad zu einem ergaenzenden Bild mit Informationen zu der
     * Zahlungsart.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function getDescriptionImage() {

    }

    /**
     * Gibt zurueck, ob ein Fehler aufgetreten ist.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getErrorOccured() {
        return $this->errorOccured;
    }

    /**
     * Gibt eine Liste mit Fehlern zurueck.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getErrorList() {
        $errorList = array();
        $errorIdx = 0;

        foreach ($this->errorList as $error) {
            $errorList['error' . $errorIdx] = array(
                'error' => $error
            );
            $errorIdx++;
        }

        return new DataObjectSet($errorList);
    }

    /**
     * Gibt zurueck, ob die Zahlungsart fuer die angegebene Zone verfuegbar
     * ist.
     *
     * @param int $zoneId Die ID der Zone, fuer die die Zahlungsart geprueft
     * 					  werden soll.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function isAvailableForZone($zoneId) {

    }

    /**
     * Gibt zurueck, ob die Zahlungsart fuer die angegebene Versandart
     * verfuegbar ist.
     *
     * @param int $shippingMethodId Die ID der Versandart, fuer die die
     * 								Zahlungsart geprueft werden soll.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function isAvailableForShippingMethod($shippingMethodId) {

    }

    /**
     * Gibt zurueck, ob die Zahlungsart fuer den angegebenen Betrag verfuegbar
     * ist.
     *
     * @param int $amount Der Betrag, fuer den die Zahlungsart geprueft werden
     * 					  soll.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function isAvailableForAmount($amount) {
        $isAvailable = false;
        $amount = (float) $amount;
        $minAmount = (float) $this->minAmountForActivation;
        $maxAmount = (float) $this->maxAmountForActivation;

        if (($minAmount != 0.0 &&
                $maxAmount != 0.0)) {

            if ($amount >= $minAmount &&
                    $amount <= $maxAmount) {

                $isAvailable = true;
            }
        } else if ($minAmount != 0.0) {
            if ($amount >= $minAmount) {
                $isAvailable = true;
            }
        } else if ($maxAmount != 0.0) {
            if ($amount <= $maxAmount) {
                $isAvailable = true;
            }
        }

        return $isAvailable;
    }

    /**
     * Bietet die Moeglichkeit, Code vor dem Anlegen der Bestellung
     * auszufuehren.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function processPaymentBeforeOrder() {
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }

    /**
     * Bietet die Moeglichkeit, Code nach dem Anlegen der Bestellung
     * auszufuehren.
     *
     * @param Order $orderObj Das Order-Objekt, mit dessen Daten die Abwicklung
     * erfolgen soll.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function processPaymentAfterOrder($orderObj) {
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }

    /**
     * Bietet die Moeglichkeit, Code nach dem Ruecksprung vom Payment
     * Provider auszufuehren.
     * Diese Methode wird vor dem Anlegen der Bestellung durchgefuehrt.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 19.11.2010
     */
    public function processReturnJumpFromPaymentProvider() {
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }
    
    /**
     * Bietet die Moeglichkeit, nach dem Ende der Bestellung noch einen Text
     * auszugeben.
     * Diese Methode wird nach dem Ende der Bestellung aufgerufen.
     *
     * @param Order $orderObj Das Order-Objekt, mit dessen Daten die Abwicklung
     * erfolgen soll.
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 06.01.2011
     */
    public function processPaymentConfirmationText($orderObj) {
    }

    /**
     * Legt die Zahlungsart in der Datenbank an, wenn noch kein Eintrag
     * existiert.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.11.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        // Es handelt sich nicht um die Basisklasse
        if ($this->moduleName !== '') {

            // Eintrag existiert noch nicht
            $checkObj = DataObject::get_one($this->ClassName);

            if (!$checkObj) {
                $this->setField('isActive', 0);
                $this->setField('Name', $this->moduleName);
                $this->setField('Title', $this->moduleName);
                $this->write();
            }
        }
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.10.10
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('ShippingMethods'); //not needed because relations can not be set this way
        $fields->removeByName('countries');
        $fields->removeByName('orders');

        /*
         * set the relation to ShippingMethod with checkboxes
         */
        $shippingMethodsTable = new ManyManyComplexTableField(
                        $this,
                        'ShippingMethods',
                        'ShippingMethod',
                        array('Title' => 'Title'),
                        'getCMSFields_forPopup'
        );
        $shippingMethodsTable->setAddTitle('Versandart');
        $fields->addFieldToTab('Root.Versandart', $shippingMethodsTable);
        return $fields;
    }

    /**
     * Liefert die Eingabefelder zum Bearbeiten des Datensatzes.
     *
     * @param mixed $params Optionale Parameter
     *
     * @return FieldSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 12.11.2010
     */
    public function getCmsFields_forPopup($params = null) {

        $tabset = new TabSet('Sections');
        $tabBasic = new Tab('Basic', 'Grundeinstellungen');
        $tabset->push($tabBasic);

        // Popupfelder fuers Bearbeiten der Zahlungsart
        $tabBasic->setChildren(
                new FieldSet(
                        new CheckboxField('isActive', _t('ShopAdmin.PAYMENT_ISACTIVE', 'Aktiviert')),
                        new DropdownField('mode', 'Modus', array('Live' => 'Live', 'Dev' => 'Entwicklung'), $this->mode),
                        new TextField('minAmountForActivation', _t('ShopAdmin.PAYMENT_MINAMOUNTFORACTIVATION', 'Mindestbetrag für Modul')),
                        new TextField('maxAmountForActivation', _t('ShopAdmin.PAYMENT_MAXAMOUNTFORACTIVATION', 'Höchstbetrag für Modul')),
                        new DropdownField('orderStatus', 'Standard Bestellstatus für diese Zahlungsart', OrderStatus::getStatusList()->map('Code', 'Title'))
                )
        );

        return new FieldSet($tabset);
    }

    /**
     * Liefert die fuer die Zahlung relevante Informationen zurueck, die dem
     * Modul uebergeben worden sind.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.11.2010
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Setzt eine Information, die fuer die Abwicklung der Zahlung relevant
     * sein kann.
     *
     * @param string       $section    Die Informationssektion
     * @param string|array $subSection Untergliederungspfad der Sektion
     * @param mixed        $value      Der Wert, der gesetzt werden soll
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.11.2010
     */
    public function setData($section, $subSection, $value) {

        if (isset($this->data[$section])) {

            if (is_array($subSection)) {

                $dataEvalStr = '$this->data["' . $section . '"]';
                $dataSectionReference = $this->data[$section];

                while (is_array($subSection)) {

                    $subSectionReference = array_shift($subSection);

                    if (isset($dataSectionReference[$subSectionReference])) {

                        if (is_array($dataSectionReference[$subSectionReference])) {
                            $dataSectionReference = $dataSectionReference[$subSectionReference];
                            $dataEvalStr .= '["' . $subSectionReference . '"]';
                        } else {
                            $subSection = false;
                        }
                    } else {
                        throw new Exception('Tried to set data field "' . $subSectionReference . '" which is not defined.');
                    }
                }
                $dataEvalStr .= '["' . $subSectionReference . '"] = "' . $value . '";';

                eval($dataEvalStr);
            } else {
                if (isset($this->data[$section][$subSection])) {
                    $this->data[$section][$subSection] = $value;
                }
            }
        }
    }

    /**
     * Nimmt den Link entgegen, der bei Abbruch durch den Kunde oder
     * Sessionablauf angesprungen werden soll.
     *
     * @param string $link Der Link
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function setCancelLink($link) {
        $this->cancelLink = $link;
    }

    /**
     * Nimmt den Link entgegen, der fuer den Ruecksprung in den Shop
     * benutzt werden soll.
     *
     * @param string $link Der Link
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function setReturnLink($link) {
        $this->returnLink = $link;
    }

    /**
     * Uebergibt den Controller an das Payment-Modul.
     *
     * @param Controller $controller Das Controller-Objekt
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 19.11.2010
     */
    public function setController($controller) {
        $this->controller = $controller;
    }

    /**
     * Schreibt einen Logeintrag.
     *
     * @param string $context Der Kontext fuer den Logeintrag
     * @param string $text    Der Text fuer den Logeintrag
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.11.2010
     */
    public function Log($context, $text) {
        if ($this->mode == 'Live') {
            $path = PIX_LOGFILE;
        } else {
            $path = PIX_LOGFILE;
        }

        if ($fp = fopen($path, 'a+')) {
            $text = sprintf(
                "%s | Module: \"%s\" | Method: \"%s\"\n%s\n--------------------------------------------------------------------------------\n",
                date('d.m.Y H:i:s'),
                $this->ClassName,
                $context,
                $text
            );

            fwrite($fp, $text);
            fclose($fp);
        }
    }

    /**
     * Registriert einen Fehler.
     *
     * @param string $errorText Der Text fuer die Fehlermeldung
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    protected function addError($errorText) {
        array_push($this->errorList, $errorText);
    }

}
