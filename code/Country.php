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

    public static $singular_name = "Land";
    public static $plural_name = "Länder";
    public static $db = array(
        'Title' => 'VarChar',
        'ISO2' => 'VarChar',
        'ISO3' => 'VarChar'
    );
    public static $many_many = array(
        'paymentMethods' => 'PaymentMethod'
    );
    public static $belongs_many_many = array(
        'zones' => 'Zone'
    );
    public static $summary_fields = array(
        'Title' => 'Land'
    );
    public static $field_labels = array(
        'Title' => 'Land'
    );

    /**
     * default instances, our beloved Germany is always with us
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @return void
     * @since 20.10.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();
        $className = $this->ClassName;
        if (!DataObject::get($className)) {
            $obj = new $className();
            $obj->Title = 'Deutschland';
            $obj->write();
        }
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     * 
     * @return FieldSet the fields for the backend
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
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
