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
class OrderPosition extends DataObject {

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
        'Price'              => 'Money',
        'PriceTotal'         => 'Money',
        'Tax'                => 'Float',
        'TaxTotal'           => 'Float',
        'TaxRate'            => 'Float',
        'ArticleDescription' => 'Text',
        'Quantity'           => 'Int',
        'Title'              => 'VarChar'
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
        'order'   => 'Order',
        'article' => 'Article'
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
}
