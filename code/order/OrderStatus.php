<?php

/**
 * Bestellstatus
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license none
 */
class OrderStatus extends DataObject {

    /**
     * Singular-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    static $singular_name = "Bestellstatus";
    /**
     * Plural-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    static $plural_name = "Bestellstatus";
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
        'Title' => 'VarChar',
        'Code' => 'VarChar'
    );
    /**
     * n:m Beziehungen
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $has_many = array(
        'orders' => 'Order',
        'payments' => 'PaymentMethod'
    );

    // -----------------------------------------------------------------------
    // Methoden
    // -----------------------------------------------------------------------

    /**
     * remove attribute Code from the CMS fields
     *
     * @return FieldSet all CMS fields related
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('Code'); //Code values are used for some getters and must not be changed via backend
        return $fields;
    }

    /**
     * Default Statuseintraege anlegen.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $className = $this->ClassName;

        if (!DataObject::get($className)) {

            $defaultStatusEntries = array(
                'pending' => 'Auf Zahlungseingang wird gewartet',
                'payed' => 'bezahlt'
            );

            foreach ($defaultStatusEntries as $code => $title) {
                $obj = new $className();
                $obj->Title = $title;
                $obj->Code = $code;
                $obj->write();
            }
        }
    }

    /**
     * Liefert ein assoziatives Array mit StatusCode => StatusText zurueck.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 23.11.2010
     */
    public static function getStatusList() {
        $statusList = DataObject::get(
                        'OrderStatus'
        );

        return $statusList;
    }

}
