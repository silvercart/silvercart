<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * show the shipping fee matrix
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 18.11.2010
 * @license see license file in modules root directory
 */
class SilvercartShippingFeesPage extends SilvercartMetaNavigationHolder {

    /**
     * allowed children on site tree
     *
     * @var array
     */
    public static $allowed_children = 'none';
    
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    public static $icon = "silvercart/img/page_icons/metanavigation_page";
    
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
        return SilvercartTools::singular_name_for($this);
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
        return SilvercartTools::plural_name_for($this); 
    }
}

/**
 * corresponding controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 18.11.2010
 * @license see license file in modules root directory
 */
class SilvercartShippingFeesPage_Controller extends SilvercartMetaNavigationHolder_Controller {

    /**
     * get all carriers; for the frontend
     *
     * @return DataList all carriers or empty DataList
     * 
     * @since 18.11.10
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     */
    public function Carriers() {
        $carriers = SilvercartCarrier::get()->sort(array('priority' => 'DESC'));
        return $carriers;
    }
}

