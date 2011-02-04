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
class Tax extends DataObject {

    /**
     * singular name for backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    static $singular_name = "Steuersatz";

    /**
     * plural name for backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    static $plural_name = "Steuersätze";

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
        'Title'             => 'VarChar',
        'Rate'              => 'Int'
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
        'articles' => 'Article'
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
        'Title'                     => 'Label',
        'Rate'                      => 'Steuersatz in %'
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
        'Title'                     => 'Label',
        'Rate'                      => 'Steuersatz in %'
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
            'Tax',
            "Rate = 7"
        );

        if (!$lowerTaxRate) {
            $lowerTaxRate = new Tax();
            $lowerTaxRate->setField('Rate', 7);
            $lowerTaxRate->setField('Title', '7%');
            $lowerTaxRate->write();
        }

        $higherTaxRate = DataObject::get_one(
            'Tax',
            "Rate = 19"
        );

        if (!$higherTaxRate) {
            $higherTaxRate = new Tax();
            $higherTaxRate->setField('Rate', 19);
            $higherTaxRate->setField('Title', '19%');
            $higherTaxRate->write();
        }
    }
}

