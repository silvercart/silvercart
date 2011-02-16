<?php

/**
 * Theses are the shipping methods the shop offers
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 20.10.2010
 * @license none
 */
class ShippingMethod extends DataObject {

    /**
     * Singular name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $singular_name = "shipping method";
    /**
     * Plural name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $plural_name = "shipping methods";
    /**
     * Attributes.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $db = array(
        'Title' => 'VarChar',
        'isActive' => 'Boolean'
    );
    /**
     * Has-one relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $has_one = array(
        'carrier'   => 'Carrier'
    );
    /**
     * Has-many relationship.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $has_many = array(
        'orders' => 'Order',
        'translations' => 'ShippingMethodTexts',
        'shippingFees' => 'ShippingFee'
    );
    /**
     * Many-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $many_many = array(
        'zones' => 'Zone'
    );
    /**
     * Belongs-many-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $belongs_many_many = array(
        'PaymentMethods' => 'PaymentMethod'
    );
    /**
     * Summaryfields for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $summary_fields = array(
        'Title' => 'Bezeichnung',
        'activatedStatus' => 'Aktiviert',
        'AttributedZones' => 'Für Zonen',
        'carrier.Title' => 'Frachtführer',
        'AttributedPaymentMethods' => 'Für Bezahlarten'
    );
    /**
     * Column labels for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $field_labels = array(
        'Title' => 'Bezeichnung',
        'activatedStatus' => 'Aktiviert',
        'AttributedZones' => 'Für Zonen',
        'AttributedPaymentMethods' => 'Für Bezahlarten'
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
        'AttributedCountries' => 'Varchar(255)',
        'AttributedPaymentMethods' => 'Varchar(255)',
        'activatedStatus' => 'Varchar(255)'
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
        'Title',
        'isActive',
        'carrier.ID' => array(
            'title' => 'Frachtführer'
        ),
        'zones.ID' => array(
            'title' => 'Für Zonen'
        ),
        'paymentMethods.ID' => array(
            'title' => 'Für Bezahlarten'
        )
    );

    /**
     * Constructor. We localize the static variables here.
     *
     * @param array|null $record      This will be null for a new database record.
     *                                  Alternatively, you can pass an array of
     *                                  field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                  don't have their defaults set.
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.01.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$summary_fields = array(
            'Title' => _t('ArticleCategoryPage.COLUMN_TITLE'),
            'activatedStatus' => _t('ShopAdmin.PAYMENT_ISACTIVE'),
            'AttributedZones' => _t('ShippingMethod.FOR_ZONES', 'for zones'),
            'carrier.Title' => _t('Carrier.SINGULARNAME'),
            'AttributedPaymentMethods' => _t('ShippingMethod.FOR_PAYMENTMETHODS', 'for payment methods')
        );
        self::$field_labels = array(
            'Title' => _t('ArticleCategoryPage.COLUMN_TITLE'),
            'activatedStatus' => _t('ShopAdmin.PAYMENT_ISACTIVE'),
            'AttributedZones' => _t('ShippingMethod.FOR_ZONES', 'for zones'),
            'AttributedPaymentMethods' => _t('ShippingMethod.FOR_PAYMENTMETHODS', 'for payment methods')
        );
        self::$searchable_fields = array(
            'Title',
            'isActive',
            'carrier.ID' => array(
                'title' => _t('Carrier.SINGULARNAME')
            ),
            'zones.ID' => array(
                'title' => _t('ShippingMethod.FOR_ZONES', 'for zones')
            ),
            'paymentMethods.ID' => array(
                'title' => _t('ShippingMethod.FOR_PAYMENTMETHODS', 'for payment methods')
            )
        );
        parent::__construct($record, $isSingleton);
    }

    /**
     * default instances will be created if no instance exists at all
     *
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 20.10.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();
        if (!DataObject::get('ShippingMethod')) {
            $shippingMethod = new ShippingMethod();
            $shippingMethod->Title = 'Paket';
            // relate to carrier (if exists)
            $carrier = DataObject::get_one("Carrier", "`Title` = 'DHL'");
            if ($carrier) {
                $shippingMethod->carrierID = $carrier->ID;
            }
            $shippingMethod->write();
        }
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 28.10.10
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->removeByName('countries');
        $fields->removeByName('PaymentMethods');
        $fields->removeByName('orders');
        $fields->removeByName('zones');

        $zonesTable = new ManyManyComplexTableField(
                        $this,
                        'zones',
                        'Zone',
                        null,
                        'getCMSFields_forPopup'
        );
        $fields->addFieldToTab('Root.Zone', $zonesTable);

        return $fields;
    }

    /**
     * determins the right shipping fee for a shipping method depending on the cart´s weight
     *
     * @return ShippingFee the most convenient shipping fee for this shipping method
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 9.11.2010
     */
    public function getShippingFee() {
        $cartWeightTotal = Member::currentUser()->shoppingCart()->getWeightTotal();

        if ($cartWeightTotal) {
            $fees = DataObject::get(
                            'ShippingFee',
                            sprintf(
                                    "`shippingMethodID` = '%s' AND `MaximumWeight` >= '%s'",
                                    $this->ID,
                                    $cartWeightTotal
                            )
            );

            if ($fees) {
                $fees->sort('PriceAmount');
                $fee = $fees->First();

                return $fee;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * pseudo attribute which can be called with $this->TitleWithCarrierAndFee
     *
     * @return string carrier + title + fee
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 15.11.2010
     */
    public function getTitleWithCarrierAndFee() {
        if ($this->getShippingFee()) {
            $titleWithCarrierAndFee = $this->carrier()->Title . "-" .
                    $this->Title . " (+" .
                    number_format($this->getShippingFee()->Price->getAmount(), 2, ',', '') .
                    $this->getShippingFee()->Price->getSymbol() .
                    ")";

            return $titleWithCarrierAndFee;
        }
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
        $attributedZones = array();
        $maxLength = 150;

        foreach ($this->zones() as $zone) {
            $attributedZones[] = $zone->Title;
        }

        if (!empty($attributedZones)) {
            $attributedZonesStr = implode(', ', $attributedZones);

            if (strlen($attributedZonesStr) > $maxLength) {
                $attributedZonesStr = substr($attributedZonesStr, 0, $maxLength) . '...';
            }
        }

        return $attributedZonesStr;
    }

    /**
     * Returns the attributed payment methods as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedPaymentMethods() {
        $attributedPaymentMethodsStr = '';
        $attributedPaymentMethods = array();
        $maxLength = 150;

        foreach ($this->paymentMethods() as $paymentMethod) {
            $attributedPaymentMethods[] = $paymentMethod->Title;
        }

        if (!empty($attributedPaymentMethods)) {
            $attributedPaymentMethodsStr = implode(', ', $attributedPaymentMethods);

            if (strlen($attributedPaymentMethodsStr) > $maxLength) {
                $attributedPaymentMethodsStr = substr($attributedPaymentMethodsStr, 0, $maxLength) . '...';
            }
        }

        return $attributedPaymentMethodsStr;
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
        $checkboxField = new CheckboxField('isActivated' . $this->ID, 'isActived', $this->isActive);

        return $checkboxField;
    }

}
