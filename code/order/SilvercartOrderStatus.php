<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
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
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartOrderStatus extends DataObject {

    /**
     * Singular-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    static $singular_name = "order status";

    /**
     * Plural-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    static $plural_name = "order status";

    /**
     * attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $db = array(
        'Title' => 'VarChar',
        'Code' => 'VarChar'
    );

    /**
     * 1:n relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $has_many = array(
        'SilvercartOrders'      => 'SilvercartOrder'
    );

    /**
     * n:m relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 27.10.2011
     */
    public static $many_many = array(
        'SilvercartShopEmails'  => 'SilvercartShopEmail'
    );

    /**
     * n:m relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.07.2011
     */
    public static $belongs_many_many = array(
        'SilvercartPaymentMethodRestrictions' => 'SilvercartPaymentMethod'
    );

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function singular_name() {
        if (_t('SilvercartOrderStatus.SINGULARNAME')) {
            return _t('SilvercartOrderStatus.SINGULARNAME');
        } else {
            return parent::singular_name();
        } 
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011 
     */
    public function plural_name() {
        if (_t('SilvercartOrderStatus.PLURALNAME')) {
            return _t('SilvercartOrderStatus.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
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
        $fieldLabels = parent::fieldLabels($includerelations);
        $fieldLabels['Title']               = _t('SilvercartPage.TITLE', 'title');
        $fieldLabels['Code']                = _t('SilvercartOrderStatus.CODE', 'code');
        $fieldLabels['SilvercartOrders']    = _t('SilvercartOrder.PLURALNAME', 'orders');
        return $fieldLabels;
    }

    /**
     * remove attribute Code from the CMS fields
     *
     * @return FieldSet all CMS fields related
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('Code'); //Code values are used for some getters and must not be changed via backend
        $fields->removeByName('SilvercartShopEmails');

        // Add shop email field
        $shopEmailLabelField = new LiteralField(
            'shopEmailLabelField',
            sprintf(
                "<br /><h2>%s</h2><p>%s</p>",
                _t('SilvercartOrderStatus.ATTRIBUTED_SHOPEMAILS_LABEL_TITLE'),
                _t('SilvercartOrderStatus.ATTRIBUTED_SHOPEMAILS_LABEL_DESC')
            )
        );
        $shopEmailField = new ManyManyComplexTableField(
            $this,
            'SilvercartShopEmails',
            'SilvercartShopEmail'
        );
        
        $fields->insertAfter($shopEmailLabelField, 'Title');
        $fields->insertAfter($shopEmailField, 'shopEmailLabelField');

        return $fields;
    }
    
    /**
     * Sends a mail with the given SilvercartOrder object as data provider.
     *
     * @return void
     *
     * @param SilvercartOrder $order The order object that is used to fill the
     *                               mail template variables.
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
                        'FirstName'         => $order->SilvercartInvoiceAddress()->FirstName,
                        'Surname'           => $order->SilvercartInvoiceAddress()->Surname,
                        'Salutation'        => $order->SilvercartInvoiceAddress()->Salutation
                    )
                );
            }
        }
        
    }

    /**
     * returns array with StatusCode => StatusText
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 23.11.2010
     */
    public static function getStatusList() {
        $statusList = DataObject::get(
            'SilvercartOrderStatus'
        );

        return $statusList;
    }
}
