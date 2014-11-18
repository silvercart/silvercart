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
 * page type display of terms and conditions
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 15.11.10
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartRegisterWelcomePage extends Page {
    
    public static $allowed_children = 'none';
    
    /**
     * Icon to display in CMS site tree
     *
     * @var string
     */
    public static $icon = "silvercart/images/page_icons/registration_welcome";

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
 * controller peer
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 * @since 15.11.2010
 */
class SilvercartRegisterWelcomePage_Controller extends Page_Controller {
    
    /**
     * Provide additional information if availabel
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.11.2014
     */
    public function AdditionalInformation() {
        $member = SilvercartCustomer::currentUser();
        $text   = '';

        if ($member) {
            if (!empty($member->OptInTempText)) {
                $text = $member->OptInTempText;
            }
        }

        return $text;
    }
}
