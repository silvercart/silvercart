<?php

/**
 * Definiert einen Preis und bietet Methoden fuer dessen Handhabung an.
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license none
 */
class Price extends DataObject {

    /**
     * Singular-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    static $singular_name = "Preis";
    /**
     * Plural-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    static $plural_name = "Preise";
    /**
     * Attribute
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $db = array(
        'Value' => 'Currency',
        'Amount' => 'Int'
    );
    /**
     * 1:1 Beziehungen
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $has_one = array(
        'customerCategory' => 'CustomerCategory',
        'owner' => 'Article'
    );
    public static $summary_fields = array(
        'customerCategory.Title' => 'Kundengruppe',
        'Value' => 'Einzelpreis',
        'Amount' => 'Mindestanzahl'
    );
}
