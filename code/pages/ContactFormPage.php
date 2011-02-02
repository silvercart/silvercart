<?php

/**
 * show an process a contact form
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class ContactFormPage extends Page {

    public static $singular_name = "";
    public static $allowed_children = array(
        'ContactFormResponsePage'
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
        self::$singular_name = _t('ContactFormPage.SINGULARNAME', 'contact form page');
        parent::__construct($record, $isSingleton);
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
class ContactFormPage_Controller extends Page_Controller {

    /**
     * initialisation of the form object
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.10.2010
     * @return void
     */
    public function init() {
        $this->registerCustomHtmlForm('ContactForm', new ContactForm($this));
        parent::init();
    }
}