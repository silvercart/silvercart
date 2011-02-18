<?php

/**
 * ???
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright pixeltricks GmbH
 * @since 02.02.2011
 * @license none
 */
class SilvercartShippingMethodTexts extends DataObject {

    static $singular_name = "shipping method text";
    static $plural_name = "shipping method texts";
    public static $db = array(
        'Title' => 'VarChar',
        'Description' => 'Text'
    );
    /**
     * Enable translatable
     * @var <type> array
     * @author Roland Lehmann
     */
    static $extensions = array(
        "Translatable"
    );
    public static $has_one = array(
        'SilvercartShippingMethod' => 'SilvercartShippingMethod'
    );
    public static $has_many = array(
    );
    public static $many_many = array(
    );
    public static $belongs_many_many = array(
    );

    /**
     * Constructor. Extension to overwrite singular and plural name.
     *
     * @param array $record      Array of field values. Normally this contructor is only used by the internal systems that get objects from the database.
     * @param bool  $isSingleton This this to true if this is a singleton() object, a stub for calling methods. Singletons don't have their defaults set.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.02.2011
     */
    public function  __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);
        self::$singular_name = _t('SilvercartShippingMethodTexts.SINGULARNAME', 'shipping method text');
        self::$plural_name = _t('SilvercartShippingMethodTexts.PLURALNAME', 'shipping method texts');
    }
}
