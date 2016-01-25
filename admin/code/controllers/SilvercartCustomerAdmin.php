<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage ModelAdmins
 */

/**
 * ModelAdmin for Members.
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 04.04.2013
 * @license see license file in modules root directory
 */
class SilvercartCustomerAdmin extends SilvercartModelAdmin {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'customer';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSortIndex = 10;

    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-customers';

    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Customers';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'Member'
    );
    
    /**
     * Manipulate search form to add some grouping.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.06.2014
     */
    public function SearchForm() {
        $searchForm             = parent::SearchForm();
        $fields                 = $searchForm->Fields();
        $customer               = singleton('Member');
        
        $basicLabelField        = new HeaderField(  'BasicLabelField',          $customer->fieldLabel('BasicData'));
        $addressLabelField      = new HeaderField(  'AddressLabelField',        $customer->fieldLabel('AddressData'));
        $invoiceLabelField      = new HeaderField(  'InvoiceLabelField',        $customer->fieldLabel('InvoiceData'));
        $shippingLabelField     = new HeaderField(  'ShippingLabelField',       $customer->fieldLabel('ShippingData'));
        
        $fields->insertBefore($basicLabelField,                              'q[FirstName]');
        $fields->insertBefore($fields->dataFieldByName('q[CustomerNumber]'), 'q[FirstName]');
        $fields->insertBefore($fields->dataFieldByName('q[Email]'),          'q[FirstName]');
        $fields->insertAfter($addressLabelField,                             'q[SubscribedToNewsletter]');
        $fields->insertAfter($invoiceLabelField,                             'q[SilvercartAddresses__SilvercartCountryID]');
        $fields->insertAfter($shippingLabelField,                            'q[SilvercartInvoiceAddress__SilvercartCountry__ID]');
        
        return $searchForm;
    }
    
}