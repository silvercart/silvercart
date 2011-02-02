<?php

/**
 * show the shipping fee matrix
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 18.11.2010
 * @license BSD
 */
class ShippingFeesPage extends Page {
    public static $singular_name = "Versandkostenseite";
    public static $allowed_children = array(
        'none'
    );

    /**
     * Constructor
     *
     * @param array|null $record      This will be null for a new database record.  Alternatively, you can pass an array of
     *                                field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                don't have their defaults set.
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 2.2.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        self::$singular_name = _t('ShippingFeesPage.SINGULARNAME', 'shipping fees page');
        parent::__construct($record, $isSingleton);
    }
}

/**
 * corresponding controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 18.11.2010
 * @license BSD
 */
class ShippingFeesPage_Controller extends Page_Controller {

    /**
     * get all carriers; for the frontend
     *
     * @return DataObjectSet all carrier objects
     * @since 18.11.10
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     */
    public function Carriers() {
        $carriers = DataObject::get('Carrier');
        return $carriers;
    }
}

