<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\MetaNavigationHolder;

/**
 * page type display of terms and conditions or other meta information.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MetaNavigationPage extends MetaNavigationHolder {

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartMetaNavigationPage';
    
    /**
     * list of allowed children page types
     *
     * @var array
     */
    private static $allowed_children = array(
        MetaNavigationPage::class,
    );

    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/metanavigation_page-file.gif";
    
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
}