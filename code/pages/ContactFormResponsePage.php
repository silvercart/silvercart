<?php

/**
 * page type for a contact form response
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 21.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class ContactFormResponsePage extends Page {

    public static $singular_name = "";
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
        self::$singular_name = _t('ContactFormResponsePage.SINGULARNAME', 'contact form response page');
        parent::__construct($record, $isSingleton);
    }

    /**
     * default instance of this page type; You may change the URLSegment without any cause.
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.10.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $records = DataObject::get_one($this->ClassName);
        if (!$records) {
            $page = new $this->ClassName();
            $page->Title = _t('ContactFormResponsePage.CONTACT_CONFIRMATION', 'contact confirmation');
            $page->URLSegment = _t('ContactFormResponsePage.URL_SEGMENT', 'contactconfirmation');
            $page->Status = "Published";
            $page->ShowInMenus = false;
            $page->ShowInSearch = false;
            $page->write();
            $page->publish("Stage", "Live");
        }
    }

}

/**
 * controller peer
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 21.10.2010
 */
class ContactFormResponsePage_Controller extends Page_Controller {

}