<?php
/**
 * Copyright 2014 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * Show an process a revocation form
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.06.2014
 * @copyright 2014 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartRevocationFormPage extends SilvercartMetaNavigationHolder {
    
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2014
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2014
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }

}

/**
 * Controller of this page type
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.06.2014
 * @copyright 2014 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartRevocationFormPage_Controller extends SilvercartMetaNavigationHolder_Controller {
    
    /**
     * Allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = array(
        'successs',
    );

    /**
     * initialisation of the form object
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2014
     */
    public function init() {
        $this->registerCustomHtmlForm('SilvercartRevocationForm', new SilvercartRevocationForm($this));
        parent::init();
    }
}
