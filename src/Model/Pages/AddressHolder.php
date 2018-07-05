<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Pages\MyAccountHolder;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\FieldType\DBText;
use SilverStripe\View\ArrayData;

/**
 * Child of customer area; overview of all addresses;
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class AddressHolder extends MyAccountHolder {

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartAddressHolder';
    
    /**
     * Indicates whether this page type can be root
     *
     * @var bool
     */
    private static $can_be_root = false;
    
    /**
     * The icon to use for this page in the storeadmin sitetree.
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/my_account_holder-file.gif";

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Returns whether this page has a summary.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.04.2013
     */
    public function hasSummary() {
        return true;
    }
    
    /**
     * Returns the summary of this page.
     * 
     * @return string
     */
    public function getSummary() {
        return $this->renderWith('SilverCart/Model/Pages/Includes/AddressSummary');
    }
    
    /**
     * Returns the summary of this page.
     * 
     * @return string
     */
    public function getSummaryTitle() {
        return _t(MyAccountHolder::class . '.YOUR_CURRENT_ADDRESSES', 'Your current invoice and delivery address');
    }

    /**
     * configure the class name of the DataObjects to be shown on this page
     * this is needed to show correct breadcrumbs
     *
     * @return string
     */
    public function getSection() {
        return Address::class;
    }
    
    /**
     * Adds the add/edit address title to the bradcrumbs by context.
     *
     * @param int    $maxDepth       maximum depth level of shown pages in breadcrumbs
     * @param string $stopAtPageType name of pagetype to stop at
     * @param bool   $showHidden     true, if hidden pages should be displayed in breadcrumbs
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2018
     */
    public function getBreadcrumbItems($maxDepth = 20, $stopAtPageType = false, $showHidden = false) {
        $items = parent::getBreadcrumbItems($maxDepth, $stopAtPageType, $showHidden);
        $breadcrumbItem = '';
        if (Controller::curr()->getAction() == 'addNewAddress') {
            $breadcrumbItem = _t(AddressHolder::class . '.ADD', 'Add new address');
            $link           = $this->Link('addNewAddress');
        } elseif (Controller::curr()->getAction() == 'edit') {
            $breadcrumbItem = _t(AddressHolder::class . '.EDIT_ADDRESS', 'Edit address');
            $ctrl      = Controller::curr();
            $addressID = $ctrl->getRequest()->postVar('AddressID');
            if (is_null($addressID)) {
                $addressID = $ctrl->getRequest()->param('ID');
            }
            $address = Address::get()->byID($addressID);
            $link    = $this->Link('edit/' . $address->ID);
        }
        if (!empty($breadcrumbItem)) {
            $title = new DBText();
            $title->setValue($breadcrumbItem);
            $items->push(new ArrayData([
                'MenuTitle' => $title,
                'Title'     => $title,
                'Link'      => $link,
            ]));
        }
        return $items;
    }

}