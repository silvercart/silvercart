<?php
/**
 * abstract for destinguishing customers that may have special prices
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright Pixeltricks GmbH
 * @since 23.10.2010
 */
class CustomerCategory extends DataObject {
    public static $singular_name = "customer category";
    public static $plural_name = "customer categories";
    public static $db = array(
        'Title' => 'VarChar'
    );
    public static $has_many = array(
        'prices' => 'Price',
        'customers' => 'Member'
    );
    public static $summary_fields = array(
        "Title" => 'Name'
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
    public function  __construct($record = null, $isSingleton = false) {
        self::$summary_fields = array(
        "Title" => _t('ArticleCategoryPage.COLUMN_TITLE')
    );
        parent::__construct($record, $isSingleton);
    }
}
