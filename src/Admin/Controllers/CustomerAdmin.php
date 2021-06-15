<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Controllers\ModelAdmin;
use SilverCart\Model\Customer\Customer;
use SilverStripe\Forms\HeaderField;
use SilverStripe\ORM\DataList;
use SilverStripe\Security\Member;

/**
 * ModelAdmin for Members.
 * 
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class CustomerAdmin extends ModelAdmin
{
    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    private static $menuCode = 'customer';
    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    private static $menuSortIndex = 10;
    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-customers';
    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Customers';
    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = array(
        Member::class,
    );
    
    /**
     * Manipulate search form to add some grouping.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.06.2014
     */
    public function SearchForm()
    {
        $searchForm             = parent::SearchForm();
        $fields                 = $searchForm->Fields();
        $customer               = Member::singleton();
        
        $basicLabelField        = HeaderField::create('BasicLabelField',    $customer->fieldLabel('BasicData'));
        $addressLabelField      = HeaderField::create('AddressLabelField',  $customer->fieldLabel('AddressData'));
        $invoiceLabelField      = HeaderField::create('InvoiceLabelField',  $customer->fieldLabel('InvoiceData'));
        $shippingLabelField     = HeaderField::create('ShippingLabelField', $customer->fieldLabel('ShippingData'));
        
        $fields->insertBefore($basicLabelField,                              'q[FirstName]');
        $fields->insertBefore($fields->dataFieldByName('q[CustomerNumber]'), 'q[FirstName]');
        $fields->insertBefore($fields->dataFieldByName('q[Email]'),          'q[FirstName]');
        $fields->insertAfter($addressLabelField,                             'q[SubscribedToNewsletter]');
        $fields->insertAfter($invoiceLabelField,                             'q[Addresses__CountryID]');
        $fields->insertAfter($shippingLabelField,                            'q[InvoiceAddress__Country__ID]');
        
        return $searchForm;
    }
    
    /**
     * Removes anonymous customers out of the list.
     * 
     * @return \SilverStripe\ORM\SS_List
     */
    public function getList() : DataList
    {
        /* @var $list DataList */
        $list = parent::getList();
        return $list->where('"Member"."ID" NOT IN (SELECT "Group_Members"."MemberID" FROM "Group_Members" WHERE "Group_Members"."GroupID" = (SELECT "Group"."ID" FROM "Group" WHERE "Group"."Code" = \'' . Customer::GROUP_CODE_ANONYMOUS . '\'))');
    }
}