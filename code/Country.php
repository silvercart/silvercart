<?php

/**
 * Abstract for a country
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license BSD
 * @since 20.10.2010
 */
class Country extends DataObject {

    /**
     * Singular name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $singular_name = "Land";

    /**
     * Plural name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $plural_name = "Länder";

    /**
     * Attributes.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $db = array(
        'Title' => 'VarChar',
        'ISO2'  => 'VarChar',
        'ISO3'  => 'VarChar'
    );

    /**
     * Many-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $many_many = array(
        'paymentMethods' => 'PaymentMethod'
    );

    /**
     * Belongs-many-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $belongs_many_many = array(
        'zones' => 'Zone'
    );

    /**
     * Summaryfields for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $summary_fields = array(
        'Title' => 'Land'
    );

    /**
     * Column labels for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $field_labels = array(
        'Title' => 'Land'
    );

    /**
     * Default database records
     *
     * @return void
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 20.10.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $standardCountry = DataObject::get_one(
            'Country'
        );

        if (!standardCountry) {
            $obj        = new Country();
            $obj->Title = 'Deutschland';
            $obj->ISO2  = 'de';
            $obj->ISO3  = 'deu';
            $obj->write();
        }
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     * 
     * @return FieldSet the fields for the backend
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 28.10.10
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('paymentMethods');
        $fields->removeByName('zones');

        $paymentMethodsTable = new ManyManyComplexTableField(
            $this,
            'paymentMethods',
            'PaymentMethod',
            null,
            'getCmsFields_forPopup'
        );
        $paymentMethodsTable->setAddTitle('Zahlarten');
        $fields->addFieldToTab('Root.Zahlarten', $paymentMethodsTable);
        
        return $fields;
    }
}
