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
     * Singular name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    static $singular_name = "Steuer";

    /**
     * Plural name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    static $plural_name = "Steuern";

    /**
     * Attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public static $db = array(
        'Title'             => 'VarChar',
        'Rate'              => 'VarChar(3)'
    );

    /**
     * Has-many relationships
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

