<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\MetaNavigationHolder;

/**
 * Show and process a revocation form.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class RevocationFormPage extends MetaNavigationHolder {

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartRevocationFormPage';
    
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2014
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2014
     */
    public function plural_name() {
        return Tools::plural_name_for($this); 
    }

}