<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Pages\MyAccountHolder;
use SilverCart\Model\Pages\OrderDetailPageController;
use SilverStripe\Control\Controller;

/**
 * show details of a customers orders.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class OrderDetailPage extends MyAccountHolder {

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartOrderDetailPage';
    
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
     * configure the class name of the DataObjects to be shown on this page
     *
     * @return string class name of the DataObject to be shown on this page
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     */
    
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
     * section identifier
     *
     * @return void
     */
    public function getSection() {
        return Order::class;
    }

    /**
     * Returns the link to this detail page.
     * 
     * @param string $action Optional controller action (method).
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.03.2014
     */
    public function Link($action = null) {
        $controller = Controller::curr();
        $link       = parent::Link($action);

        if ($controller instanceof OrderDetailPageController) {
            $link .= 'detail' . $controller->getOrderID();
        }

        return $link;
    }
}