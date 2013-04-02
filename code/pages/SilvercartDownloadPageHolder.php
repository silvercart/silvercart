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
 * SilvercartDownloadPageHolder 
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 12.07.2012
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory 
 */
class SilvercartDownloadPageHolder extends Page {
    
    /**
     * allowed child pages in site tree
     *
     * @var array
     */
    public static $allowed_children = array(
      'SilvercartDownloadPage',  
    );
    
    /**
     * returns the singular name
     * 
     * @return string 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 12.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }
    
    /**
     * returns the plural name
     * 
     * @return string 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 12.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }
    
}

/**
 * SilvercartDownloadPageHolder_Controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 12.07.2012
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory 
 */
class SilvercartDownloadPageHolder_Controller extends Page_Controller {
    
}