<?php

/**
 * page type display of terms and conditions
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 15.11.10
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class RegisterWelcomePage extends Page {

    public static $singular_name = "Regigistrierungsbegrüßungsseite";
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
        self::$singular_name = _t('RegisterWelcomePage.SINGULARNAME', 'register welcome page');
        parent::__construct($record, $isSingleton);
    }

}

/**
 * controller peer
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 15.11.2010
 */
class RegisterWelcomePage_Controller extends Page_Controller {

}