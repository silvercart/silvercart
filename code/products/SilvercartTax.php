<?php
/**
 * Defines Taxrates.
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 24.11.2010
 * @license none
 */
class SilvercartTax extends DataObject {

    /**
     * singular name for backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    static $singular_name = "rate";

    /**
     * plural name for backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    static $plural_name = "rates";

    /**
     * attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public static $db = array(
        'Title' => 'VarChar',
        'Rate'  => 'Int'
    );

    /**
     * n:m relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public static $has_many = array(
        'SilvercartProducts' => 'SilvercartProduct'
    );

    /**
     * Summaryfields for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $summary_fields = array(
        'Title' => 'Label',
        'Rate'  => 'Steuersatz in %'
    );

    /**
     * Column labels for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $field_labels = array(
        'Title' => 'Label',
        'Rate'  => 'Steuersatz in %'
    );

    /**
     * List of searchable fields for the model admin
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $searchable_fields = array(
        'Rate'
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 2.02.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$summary_fields = array(
            'Title' => _t('SilvercartTax.LABEL', 'label'),
            'Rate'  => _t('SilvercartTax.RATE_IN_PERCENT', 'rate in %%')
        );
        self::$field_labels = array(
            'Title' => _t('SilvercartTax.LABEL'),
            'Rate'  => _t('SilvercartTax.RATE_IN_PERCENT')
            );
        parent::__construct($record, $isSingleton);
    }

    /**
     * Inserts the two german standard tax rates into the database.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $lowerTaxRate = DataObject::get_one(
            'SilvercartTax',
            "Rate = 7"
        );

        if (!$lowerTaxRate) {
            $lowerTaxRate = new SilvercartTax();
            $lowerTaxRate->setField('Rate',  7);
            $lowerTaxRate->setField('Title', '7%');
            $lowerTaxRate->write();
        }

        $higherTaxRate = DataObject::get_one(
            'SilvercartTax',
            "Rate = 19"
        );

        if (!$higherTaxRate) {
            $higherTaxRate = new SilvercartTax();
            $higherTaxRate->setField('Rate',  19);
            $higherTaxRate->setField('Title', '19%');
            $higherTaxRate->write();
            // relate to ShippingFee (if exists)
            $silvercartShippingFee = DataObject::get_one("SilvercartShippingFee");
            if ($silvercartShippingFee) {
                $silvercartShippingFee->SilvercartTaxID = $higherTaxRate->ID;
                $silvercartShippingFee->write();
            }
        }
    }
}
