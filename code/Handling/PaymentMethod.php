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
     * Singular name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $singular_name = "payment method";

    /**
     * Plural name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $plural_name = "payment methods";

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
        'isActive'                  => 'Boolean',
        'minAmountForActivation'    => 'Float',
        'maxAmountForActivation'    => 'Float',
        'Name'                      => 'Varchar(150)',
        'Description'               => 'Text',
        'mode'                      => "Enum('Live,Dev','Dev')",
        'orderStatus'               => "Varchar(50)"
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
        'HandlingCost'              => 'HandlingCost',
        'Zone'                      => 'Zone'
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
     * define colums to be shown in a table
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 08.11.2010
     */
    public static $summary_fields = array(
        'Name'                      => 'Bezeichnung',
        'activatedStatus'           => 'aktiviert?',
        'AttributedZones'           => 'Zugeordnete Zone',
        'AttributedCountries'       => 'Zugeordnete Länder',
        'minAmountForActivation'    => 'Mindestbetrag',
        'maxAmountForActivation'    => 'Hoechstbetrag'
    );
    /**
     * define field labels
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 08.11.2010
     */
    public static $field_labels = array(
        'Name'                      => 'Name',
        'activatedStatus'           => 'Aktiviert',
        'AttributedZones'           => 'Zugeordnete Zone',
        'AttributedCountries'       => 'Zugeordnete Länder',
        'minAmountForActivation'    => 'Ab Einkaufswert',
        'maxAmountForActivation'    => 'Bis Einkaufswert'
    );

    /**
     * Virtual database columns.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $casting = array(
        'AttributedCountries'       => 'Varchar(255)',
        'AttributedZones'           => 'Varchar(255)',
        'activatedStatus'           => 'Varchar(255)'
    );

    /**
     * List of searchable fields for the model admin
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $searchable_fields = array(
        'Name',
        'isActive' => array(
            'title' => 'Aktiviert'
        ),
        'minAmountForActivation',
        'maxAmountForActivation',
        'Zone.ID' => array(
            'title' => 'Zugeordnete Zone'
        ),
        'countries.ID' => array(
            'title' => 'Zugeordnete Länder'
        )
    );

    /**
     * Contains inormation that might be interesting for the payment process
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
            'tax_rates' => array(),
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
     * Contains the module name for display in the admin backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 03.12.2010
     */
    protected $moduleName = '';

    /**
     * Contains a referer to the order object
     *
     * @var Controller
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 19.11.2010
     */
    protected $controller;

    // ------------------------------------------------------------------------
    // Methods
    // ------------------------------------------------------------------------

    /**
     * Constructor. We localize the static variables here.
     *
     * @param array|null $record      This will be null for a new database record.
     *                                  Alternatively, you can pass an array of
     *                                  field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                  don't have their defaults set.
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 2.02.2011
     */
    public function  __construct($record = null, $isSingleton = false) {
        self::$searchable_fields = array(
        'Name',
        'isActive' => array(
            'title' => _t('ShopAdmin.PAYMENT_ISACTIVE')
        ),
        'minAmountForActivation',
        'maxAmountForActivation',
        'Zone.ID' => array(
            'title' => _t('Country.ATTRIBUTED_ZONES')
        ),
        'countries.ID' => array(
            'title' => _t('PaymentMethod.ATTRIBUTED_COUNTRIES', 'attributed countries')
        )
    );
    self::$field_labels = array(
        'Name'                      => 'Name',
        'activatedStatus'           => _t('ShopAdmin.PAYMENT_ISACTIVE'),
        'AttributedZones'           => _t('Country.ATTRIBUTED_ZONES'),
        'AttributedCountries'       => _t('PaymentMethod.ATTRIBUTED_COUNTRIES'),
        'minAmountForActivation'    => _t('PaymentMethod.FROM_PURCHASE_VALUE', 'from purchase value'),
        'maxAmountForActivation'    => _t('PaymentMethod.TILL_PURCHASE_VALUE', 'till purchase value')
    );
        parent::__construct($record, $isSingleton);
    }

        /**
     * Set a custom search context for fields like "greater than", "less than",
     * etc.
     * 
     * @return SearchContext
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function getDefaultSearchContext() {
        $fields     = $this->scaffoldSearchFields();
        $filters    = array(
            'minAmountForActivation'    => new GreaterThanFilter('minAmountForActivation'),
            'maxAmountForActivation'    => new LessThanFilter('maxAmountForActivation'),
            'isActive'                  => new ExactMatchFilter('isActive')
        );
        return new SearchContext(
            $this->class,
            $fields,
            $filters
        );
    }

    /**
     * Returns the title of the payment method
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
     * Returns the status that for orders created with this payment method
     *
     * @return string orderstatus code
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 23.11.2010
     */
    public function getDefaultOrderStatus() {
        return $this->orderStatus;
    }

    /**
     * Returns the payment methods description
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
     * Returns the path to the payment methods logo
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
     * Returns the link for cancel action or end of session
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
     * Returns the link to get back in the shop
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
     * Returns handling costs for this payment method
     *
     * @return Money a money object
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
     * Retunrns a path to a picture with additional information for this payment method
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
     * Returns if an error has occured
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
     * Returns a DataObjectSet with errors
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
     * Returns weather this payment method is available for a zone specified by id or not
     *
     * @param int $zoneId Zone id to be checked
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
     * Is this payment method allowed for a shipping method?
     *
     * @param int $shippingMethodId Die ID id of shipping method to be checked
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
     * Is this payment method allowed for a total amount?
     *
     * @param int $amount Amount to be checked
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
     * Hook: processed before order creation
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
     * Hook: processed after order creation
     *
     * @param Order $orderObj created order object
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
     * Hook: called after jumpback from payment provider
     * processed before order creation
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
     * possibility to return a text at the end of the order process
     * processed after order creation
     *
     * @param Order $orderObj the order object
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
     * writes a payment method to the db in case none does exist yet
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
         * add ability to set the relation to ShippingMethod with checkboxes
         */
        $shippingMethodsTable = new ManyManyComplexTableField(
            $this,
            'ShippingMethods',
            'ShippingMethod',
            array('Title' => 'Title'),
            'getCMSFields_forPopup'
        );
        $shippingMethodsTable->setAddTitle(_t('PaymentMethod.SHIPPINGMETHOD', 'shipping method'));
        $tabParam = "Root."._t('PaymentMethod.SHIPPINGMETHOD', 'shipping method');
        $fields->addFieldToTab($tabParam, $shippingMethodsTable);
        return $fields;
    }

    /**
     * Returns the detail fields for $this
     *
     * @param mixed $params optional parameters
     *
     * @return FieldSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 12.11.2010
     */
    public function getCmsFields_forPopup($params = null) {

        $tabset = new TabSet('Sections');
        $tabBasic = new Tab('Basic', _t('PaymentMethod.BASIC_SETTINGS', 'basic settings'));
        $tabset->push($tabBasic);

        // Popupfelder fuers Bearbeiten der Zahlungsart
        $tabBasic->setChildren(
            new FieldSet(
                new CheckboxField('isActive', _t('ShopAdmin.PAYMENT_ISACTIVE', 'activated')),
                new DropdownField('mode', 'Modus', array('Live' => 'Live', 'Dev' => 'Entwicklung'), $this->mode),
                new TextField('minAmountForActivation', _t('ShopAdmin.PAYMENT_MINAMOUNTFORACTIVATION', 'Mindestbetrag für Modul')),
                new TextField('maxAmountForActivation', _t('ShopAdmin.PAYMENT_MAXAMOUNTFORACTIVATION', 'Höchstbetrag für Modul')),
                new DropdownField('orderStatus', _t('PaymentMethod.STANDARD_ORDER_STATUS', 'standard order status for this payment method'), OrderStatus::getStatusList()->map('Code', 'Title'))
            )
        );

        return new FieldSet($tabset);
    }

    /**
     * Returns the information relevant for payment suppied by this module
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
     * Set information relevant for payment
     *
     * @param string       $section    section of information
     * @param string|array $subSection subsection path
     * @param mixed        $value      value to be set
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
     * set the link to be visited on a cancel action
     *
     * @param string $link the url
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
     * sets the link to return to the shop
     *
     * @param string $link the url
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
     * set the controller
     *
     * @param Controller $controller the controller action
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
     * Returns the attributed countries as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedCountries() {
        $attributedCountriesStr = '';
        $attributedCountries    = array();
        $maxLength          = 150;

        foreach ($this->countries() as $country) {
            $attributedCountries[] = $country->Title;
        }

        if (!empty($attributedCountries)) {
            $attributedCountriesStr = implode(', ', $attributedCountries);

            if (strlen($attributedCountriesStr) > $maxLength) {
                $attributedCountriesStr = substr($attributedCountriesStr, 0, $maxLength).'...';
            }
        }

        return $attributedCountriesStr;
    }

    /**
     * Returns the attributed zones as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedZones() {
        $attributedZonesStr = '';
        $attributedZones    = array();
        $maxLength          = 150;

        foreach ($this->Zone() as $zone) {
            $attributedZones[] = $zone->Title;
        }

        if (!empty($attributedZones)) {
            $attributedZonesStr = implode(', ', $attributedZones);

            if (strlen($attributedZonesStr) > $maxLength) {
                $attributedZonesStr = substr($attributedZonesStr, 0, $maxLength).'...';
            }
        }

        return $attributedZonesStr;
    }

    /**
     * Returns the activation status as HTML-Checkbox-Tag.
     *
     * @return CheckboxField
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function activatedStatus() {
        $checkboxField = new CheckboxField('isActivated'.$this->ID, 'isActived', $this->isActive);

        return $checkboxField;
    }

    /**
     * writes a log entry
     *
     * @param string $context the context for the log entry
     * @param string $text    the text for the log entry
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
     * registers an error
     *
     * @param string $errorText text for the error message
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
