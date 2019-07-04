<?php

namespace SilverCart\Model\Pages;

use Page;
use SilverCart\Dev\Tools;

/**
 * shows and processes a registration form;
 * configuration of registration mails;
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class RegistrationPage extends Page
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartRegistrationPage';
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/metanavigation_page-file.gif";
    
    /**
     * Returns the translated singular name.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }
}