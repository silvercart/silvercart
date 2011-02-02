<?php

/**
 * Child of AddressHolder, CRUD a single address
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @since 18.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class AddressPage extends Page {

    public static $singular_name = "";
    public static $can_be_root = false;

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
        self::$singular_name = _t('AddressPage.SINGULARNAME', 'address details page');
        parent::__construct($record, $isSingleton);
    }

    /**
     * configure the class name of the DataObjects to be shown on this page
     * this is needed to show correct breadcrumbs
     *
     * @return string class name of the DataObject to be shown on this page
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     */
    public function getSection() {
        return 'Address';
    }
}

/**
 * Controller of this page type
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class AddressPage_Controller extends Page_Controller {

    /**
     * statements to be called on instanciation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 25.10.2010
     * @return void
     */
    public function init() {
        if ($this->urlParams['ID']) {
            Session::set('addressID', $this->urlParams['ID']); //ID is saved to the session because otherwise it will be lost on form submit
        }
        $this->registerCustomHtmlForm('EditAddressForm', new EditAddressForm($this));
        parent::init();
    }
}