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
class AddressHolder extends MyAccountHolder
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
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
     * @return string
     */
    public function singular_name()
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name()
    {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.09.2018
     */
    public function fieldLabels($includerelations = true)
    {
        $this->beforeUpdateFieldLabels(function(&$labels) {
            $labels = array_merge(
                    $labels,
                    Tools::field_labels_for(self::class),
                    [
                        'AddNewAddress'        => _t(AddressHolder::class . '.ADD', 'Add new address'),
                        'EditAddress'          => _t(AddressHolder::class . '.EDIT_ADDRESS', 'Edit address'),
                        'YourCurrentAddresses' => _t(MyAccountHolder::class . '.YOUR_CURRENT_ADDRESSES', 'Your current invoice and delivery address'),
                    ]
            );
        });
        return parent::fieldLabels($includerelations);
    }
    
    /**
     * Returns whether this page has a summary.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.04.2013
     */
    public function hasSummary()
    {
        return true;
    }
    
    /**
     * Returns the summary of this page.
     * 
     * @return string
     */
    public function getSummary()
    {
        return $this->renderWith('SilverCart/Model/Pages/Includes/AddressSummary');
    }
    
    /**
     * Returns the summary of this page.
     * 
     * @return string
     */
    public function getSummaryTitle()
    {
        return $this->fieldLabel('YourCurrentAddresses');
    }

    /**
     * configure the class name of the DataObjects to be shown on this page
     * this is needed to show correct breadcrumbs
     *
     * @return string
     */
    public function getSection()
    {
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
     */
    public function getBreadcrumbItems($maxDepth = 20, $stopAtPageType = false, $showHidden = false)
    {
        $items = parent::getBreadcrumbItems($maxDepth, $stopAtPageType, $showHidden);
        $breadcrumbItem = '';
        if (Controller::curr()->getAction() == 'addNewAddress') {
            $breadcrumbItem = $this->fieldLabel('AddNewAddress');
            $link           = $this->Link('addNewAddress');
        } elseif (Controller::curr()->getAction() == 'edit') {
            $breadcrumbItem = $this->fieldLabel('EditAddress');
            $ctrl      = Controller::curr();
            $addressID = $ctrl->getRequest()->postVar('AddressID');
            if (is_null($addressID)) {
                $addressID = $ctrl->getRequest()->param('ID');
            }
            $address = Address::get()->byID($addressID);
            $link    = $this->Link('edit/' . $address->ID);
        }
        if (!empty($breadcrumbItem)) {
            $title = DBText::create();
            $title->setValue($breadcrumbItem);
            $items->push(ArrayData::create([
                'MenuTitle' => $title,
                'Title'     => $title,
                'Link'      => $link,
            ]));
        }
        return $items;
    }
}