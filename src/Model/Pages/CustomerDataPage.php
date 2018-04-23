<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\MyAccountHolder;

/**
 * Shows customerdata + edit.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CustomerDataPage extends MyAccountHolder {

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartCustomerDataPage';
    
    /**
     * Indicates whether this page type can be root
     *
     * @var bool
     */
    private static $can_be_root = false;
    
    /**
     * allowed children on site tree
     *
     * @var array
     */
    private static $allowed_children = 'none';
    
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
        return $this->renderWith('SilverCart/Model/Pages/Includes/CustomerDataSummary');
    }
    
    /**
     * Returns the summary of this page.
     * 
     * @return string
     */
    public function getSummaryTitle() {
        return _t(MyAccountHolder::class . '.YOUR_PERSONAL_DATA', 'Your personal data');
    }
}