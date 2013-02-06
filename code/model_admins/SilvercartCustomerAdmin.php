<?php
/**
 * Copyright 2012 pixeltricks GmbH
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
 * @subpackage ModelAdmins
 */

/**
 * ModelAdmin for Members.
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 05.09.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCustomerAdmin extends ModelAdmin {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'config';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSortIndex = 119;

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSection = 'others';

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
        'Member' => array(
            'collection_controller' => 'SilvercartCustomerAdmin_CollectionController'
        )
    );

    /**
     * Constructor
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2012
     */
    public function __construct() {
        self::$menu_title = _t('SilvercartCustomerAdmin.MENUTITLE');
        
        parent::__construct();
    }
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2012
     */
    public function init() {
        parent::init();
        $this->extend('updateInit');
    }
}

/**
 * ModelAdmin CollectionController for SilvercartCustomerAdmin.
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 05.09.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCustomerAdmin_CollectionController extends ModelAdmin_CollectionController {

    /**
     * Hide the import form
     *
     * @var boolean
     */
    public $showImportForm = false;

    /**
     * Modifies the search query for image types.
     *
     * @param array $searchCriteria The search criteria
     *
     * @return SQLQuery
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2012
     */
    public function getSearchQuery($searchCriteria) {
        $query = parent::getSearchQuery($searchCriteria);
        $anonymousGroup = DataObject::get_one('Group', "Code = 'anonymous'");
        if ($anonymousGroup) {
            $query->where[] = sprintf(
                    "Member.ID NOT IN (SELECT MemberID FROM Group_Members WHERE GroupID = '%s')",
                    $anonymousGroup->ID
            );
        }
        
        $searchedForSilvercartAddresses         = false;
        $searchedForSilvercartInvoiceAddress    = false;
        $searchedForSilvercartShippingAddress   = false;
        foreach ($searchCriteria as $key => $value) {
            if (strpos($key, 'SilvercartAddresses__') === 0) {
                $searchedForSilvercartAddresses = true;
            } elseif (strpos($key, 'SilvercartInvoiceAddress__') === 0) {
                $searchedForSilvercartInvoiceAddress = true;
            } elseif (strpos($key, 'SilvercartShippingAddress__') === 0) {
                $searchedForSilvercartShippingAddress = true;
            }
        }
        if ($searchedForSilvercartAddresses) {
            $query->leftJoin('SilvercartAddress', "`Member`.`ID` = `SilvercartAddress`.`MemberID`"
            );
        }
        if ($searchedForSilvercartInvoiceAddress) {
            $query->leftJoin('SilvercartAddress', 'SilvercartInvoiceAddress.ID = Member.SilvercartInvoiceAddressID', 'SilvercartInvoiceAddress');
        }
        if ($searchedForSilvercartShippingAddress) {
            $query->leftJoin('SilvercartAddress', 'SilvercartShippingAddress.ID = Member.SilvercartShippingAddressID', 'SilvercartShippingAddress');
        }
        
        return $query;
    }
    
    /**
     * Replace the OrderStatus textfield with a dropdown field.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2012
     */
    public function SearchForm() {
        $searchForm             = parent::SearchForm();
        $fields                 = $searchForm->Fields();
        $customer               = singleton('Member');
        
        $basicLabelField        = new HeaderField(  'BasicLabelField',          $customer->fieldLabel('BasicData'));
        $addressLabelField      = new HeaderField(  'AddressLabelField',        $customer->fieldLabel('AddressData'));
        $invoiceLabelField      = new HeaderField(  'InvoiceLabelField',        $customer->fieldLabel('InvoiceData'));
        $shippingLabelField     = new HeaderField(  'ShippingLabelField',       $customer->fieldLabel('ShippingData'));
        
        $fields->insertBefore($basicLabelField,                                         'FirstName');
        $fields->insertBefore($fields->dataFieldByName('CustomerNumber'),               'FirstName');
        $fields->insertBefore($fields->dataFieldByName('Email'),                        'FirstName');
        $fields->insertAfter($addressLabelField,                                        'SubscribedToNewsletter');
        $fields->insertAfter($invoiceLabelField,                                        'SilvercartAddresses__SilvercartCountry__ID');
        $fields->insertAfter($shippingLabelField,                                       'SilvercartInvoiceAddress__SilvercartCountry__ID');

        $fields->dataFieldByName('Groups__ID')->setEmptyString(                                         '(' . _t('Boolean.ANY') . ')');
        $fields->dataFieldByName('SilvercartAddresses__SilvercartCountry__ID')->setEmptyString(         '(' . _t('Boolean.ANY') . ')');
        $fields->dataFieldByName('SilvercartInvoiceAddress__SilvercartCountry__ID')->setEmptyString(    '(' . _t('Boolean.ANY') . ')');
        $fields->dataFieldByName('SilvercartShippingAddress__SilvercartCountry__ID')->setEmptyString(   '(' . _t('Boolean.ANY') . ')');
        
        $this->extend('updateSearchForm', $searchForm);
        
        return $searchForm;
    }

}