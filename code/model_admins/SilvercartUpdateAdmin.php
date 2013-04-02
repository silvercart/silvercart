<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Backend
 */

/**
 * The Silvercart configuration backend.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.03.2011
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartUpdateAdmin extends ModelAdmin {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'config';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSortIndex = 30;

    /**
     * Managed models
     *
     * @var array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public static $managed_models = array(
        'SilvercartUpdate'
    );
    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-update';
    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Silvercart Updates';

    
    /**
     * Class name of the results table to use
     *
     * @var string
     */
    protected $resultsTableClassName = 'SilvercartUpdateTableListField';

    /**
     * The priority for backend menu
     *
     * @var int 
     */
    public static $menu_priority = -1;
    
    /**
     * title in the top bar of the CMS
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.08.2012
     */
    public function SectionTitle() {
        $sectionTitle = _t('SilvercartUpdateAdmin.SILVERCART_UPDATE');
        if (SilvercartUpdate::get()->filter("Status", "remaining")->exists()) {
        $sectionTitle .= ' (' . SilvercartUpdate::get()->filter("Status", "remaining")->count() . ')';
        }
        return $sectionTitle;
    }

}
