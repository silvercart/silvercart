<?php

/**
 * abstract for a single position of an order
 * they are not changeable after creation and serve as a history
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license none
 */
class SilvercartOrderPosition extends DataObject {

    /**
     * attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $db = array(
        'Price' => 'Money',
        'PriceTotal' => 'Money',
        'Tax' => 'Float',
        'TaxTotal' => 'Float',
        'TaxRate' => 'Float',
        'ArticleDescription' => 'Text',
        'Quantity' => 'Int',
        'Title' => 'VarChar'
    );
    /**
     * 1:n relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $has_one = array(
        'SilvercartOrder' => 'SilvercartOrder',
        'SilvercartProduct' => 'SilvercartProduct'
    );
    /**
     *
     * @var array configuration for attributes=>label
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     */
    public static $summary_fields = array(
        'Title' => 'Artikelbezeichnung',
        'ArticleDescription' => 'Artikelbeschreibung',
        'Price' => 'Preis',
        'Quantity' => 'Anzahl'
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
     * @since 02.02.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$summary_fields = array(
            'Title' => _t('SilvercartPage.ARTICLENAME'),
            'ArticleDescription' => _t('SilvercartArticle.DESCRIPTION'),
            'Price' => _t('SilvercartArticle.PRICE'),
            'Quantity' => _t('SilvercartArticle.QUANTITY')
        );
        parent::__construct($record, $isSingleton);
    }

}
