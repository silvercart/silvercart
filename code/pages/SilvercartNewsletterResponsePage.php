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
 * Page for the response of subscription status.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 * @since 22.03.2011
 */
class SilvercartNewsletterResponsePage extends SilvercartMetaNavigationHolder {
    
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    public static $icon = "silvercart/images/page_icons/metanavigation_page";
    
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
 * Page for the response of subscription status.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 * @since 22.03.2011
 */
class SilvercartNewsletterResponsePage_Controller extends SilvercartMetaNavigationHolder_Controller {

    /**
     * Return the status messages as DataList.
     *
     * @return SS_List|false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.03.2011
     */
    public function StatusMessages() {
        $status     = Session::get('SilvercartNewsletterStatus');
        $results   = false;

        if ($status &&
            isset($status['messages']) &&
            is_array($status['messages'])) {

            $messages = $status['messages'];
            $results = new ArrayList();
            foreach ($messages as $message) {
                $results->push(new DataObject($message));
            }
        }

        return $results;
    }
}
