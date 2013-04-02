<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 * 
 * @package Silvercart
 * @subpackage Order
 */

/**
 * abstract for an order status
 *
 * @package Silvercart
 * @subpackage Order
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 22.11.2010
 * @license see license file in modules root directory
 */
class SilvercartOrderStatus extends DataObject {

    /**
     * attributes
     *
     * @var array
     */
    public static $db = array(
        'Code' => 'VarChar'
    );

    /**
     * 1:n relations
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartOrders'      => 'SilvercartOrder',
        'SilvercartOrderStatusLanguages' => 'SilvercartOrderStatusLanguage'
    );

    /**
     * n:m relations
     *
     * @var array
     */
    public static $many_many = array(
        'SilvercartShopEmails'  => 'SilvercartShopEmail'
    );

    /**
     * n:m relations
     *
     * @var array
     */
    public static $belongs_many_many = array(
        'SilvercartPaymentMethodRestrictions' => 'SilvercartPaymentMethod'
    );
    
    /**
     * Castings
     *
     * @var array 
     */
    public static $casting = array(
        'Title' => 'VarChar(255)'
    );
    
    /**
     * Default sort
     *
     * @var string 
     */
    public static $default_sort = "SilvercartOrderStatusLanguage.Title";

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }  
    
    /**
     * retirieves title from related language class depending on the set locale
     * Title is a very common attribute and is therefore located in the decorator
     *
     * @return string 
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }

    /**
     * Get any user defined searchable fields labels that
     * exist. Allows overriding of default field names in the form
     * interface actually presented to the user.
     *
     * The reason for keeping this separate from searchable_fields,
     * which would be a logical place for this functionality, is to
     * avoid bloating and complicating the configuration array. Currently
     * much of this system is based on sensible defaults, and this property
     * would generally only be set in the case of more complex relationships
     * between data object being required in the search interface.
     *
     * Generates labels based on name of the field itself, if no static property
     * {@link self::field_labels} exists.
     *
     * @param boolean $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array|string Array of all element labels if no argument given, otherwise the label of the field
     *
     * @uses $field_labels
     * @uses FormField::name_to_label()
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.02.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title'                                 => _t('SilvercartPage.TITLE'),
                'Code'                                  => _t('SilvercartOrderStatus.CODE'),
                'SilvercartOrders'                      => _t('SilvercartOrder.PLURALNAME'),
                'SilvercartPaymentMethodRestrictions'   => _t('SilvercartPaymentMethod.PLURALNAME'),
                'SilvercartOrderStatusLanguages'        => _t('Silvercart.TRANSLATIONS'),
                'ShopEmailsTab'                         => _t('SilvercartOrderStatus.ATTRIBUTED_SHOPEMAILS_LABEL_TITLE'),
                'ShopEmailLabelField'                   => _t('SilvercartOrderStatus.ATTRIBUTED_SHOPEMAILS_LABEL_DESC'),
                'SilvercartShopEmails'                  => _t('SilvercartShopEmail.PLURALNAME')
            )
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 10.02.2013
     */
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'SilvercartOrders'
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }

    /**
     * remove attribute Code from the CMS fields
     *
     * @return FieldList all CMS fields related
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.07.2012
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);

        // Add shop email field
        $shopEmailLabelField = new LiteralField(
            'ShopEmailLabelField',
            sprintf(
                "<br /><p>%s</p>",
                $this->fieldLabel('ShopEmailLabelField')
            )
        );
        $fields->addFieldToTab('Root.SilvercartShopEmails', $shopEmailLabelField, 'SilvercartShopEmails');

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }
    
    /**
     * Sends a mail with the given SilvercartOrder object as data provider.
     * 
     * @param SilvercartOrder $order The order object that is used to fill the
     *                               mail template variables.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.10.2011
     */
    public function sendMailFor(SilvercartOrder $order) {
        $shopEmails = $this->SilvercartShopEmails();
        
        if ($shopEmails) {
            foreach ($shopEmails as $shopEmail) {
                SilvercartShopEmail::send(
                    $shopEmail->Identifier,
                    $order->CustomersEmail,
                    array(
                        'SilvercartOrder'   => $order,
                        'OrderNumber'       => $order->OrderNumber,
                        'CustomersEmail'    => $order->CustomersEmail,
                        'FirstName'         => $order->SilvercartInvoiceAddress()->FirstName,
                        'Surname'           => $order->SilvercartInvoiceAddress()->Surname,
                        'Salutation'        => $order->SilvercartInvoiceAddress()->Salutation
                    )
                );
            }
        }
        
        $this->extend('updateSendMailFor', $order);
    }

    /**
     * returns array with StatusCode => StatusText
     *
     * @return DataList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.11.2010
     */
    public static function getStatusList() {
        $statusList = SilvercartOrderStatus::get();

        return $statusList;
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.07.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'Code'  => $this->fieldLabel('Code'),
            'Title' => $this->fieldLabel('Title'),
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
}
