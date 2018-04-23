<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\MetaNavigationHolder;

/**
 * Page to display available payment methods.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PaymentMethodsPage extends MetaNavigationHolder {

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartPaymentMethodsPage';
    
    /**
     * Allowed children
     *
     * @var array
     */
    private static $allowed_children = 'none';
    
    /**
     * Page type icon
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/metanavigation_page-file.gif";
    
    /**
     * i18n singular name of this object
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
    }
    
    /**
     * i18n plural name of this object
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this);
    }
}