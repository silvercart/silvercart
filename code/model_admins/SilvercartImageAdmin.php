<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
*
* @package Silvercart
* @subpackage ModelAdmins
*/

/**
* ModelAdmin for SilvercartImage.
* 
* @package Silvercart
* @subpackage ModelAdmins
* @author Sascha Koehler <skoehler@pixeltricks.de>
* @copyright 2013 pixeltricks GmbH
* @since 31.05.2012
* @license see license file in modules root directory
*/
class SilvercartImageAdmin extends ModelAdmin {

    /**
    * The code of the menu under which this admin should be shown.
    * 
    * @var string
    */
    public static $menuCode = 'files';

    /**
    * The section of the menu under which this admin should be grouped.
    * 
    * @var string
    */
    public static $menuSortIndex = 20;

    /**
    * The URL segment
    *
    * @var string
    */
    public static $url_segment = 'silvercart-silvercart-image';

    /**
    * The menu title
    *
    * @var string
    */
    public static $menu_title = 'Silvercart Images';

    /**
    * Managed models
    *
    * @var array
    */
    public static $managed_models = array(
        'SilvercartImage'
    );

    /**
    * Provides hook for decorators, so that they can overwrite css
    * and other definitions.
    * 
    * @return void
    *
    * @author Sascha Koehler <skoehler@pixeltricks.de>
    * @since 31.05.2012
    */
    public function init() {
        parent::init();
        $this->extend('updateInit');
    }
    
    /**
     * title in the top bar of the CMS
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.08.2012
     */
    public function SectionTitle() {
        return _t('SilvercartImage.PLURALNAME');
    }
}