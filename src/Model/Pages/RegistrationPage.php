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
    const SESSION_KEY                = 'SilverCart.RegistrationPage';
    const SESSION_KEY_IS_IN_CHECKOUT = self::SESSION_KEY . '.IsInCheckout';
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
     * Returns whether the customer is in checkout process while going through the
     * registration process.
     * 
     * @return bool
     */
    public static function getIsInCheckout() : bool
    {
        return (bool) Tools::Session()->get(self::SESSION_KEY_IS_IN_CHECKOUT);
    }
    
    /**
     * Sets whether the customer is in checkout process while going through the
     * registration process.
     * 
     * @param bool $is Customer is in checkout?
     * 
     * @return void
     */
    public static function setIsInCheckout(bool $is) : void
    {
        Tools::Session()->set(self::SESSION_KEY_IS_IN_CHECKOUT, $is);
        Tools::saveSession();
    }

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
    
    /**
     * Returns whether to show the my account link or not.
     * 
     * @return bool
     */
    public function ShowMyAccountLink() : bool
    {
        $show = true;
        $this->extend('updateShowMyAccountLink', $show);
        return $show;
    }
    
    /**
     * Returns the 'got to shop' button label.
     * 
     * @return string
     */
    public function BtnLabelGoToShop() : string
    {
        $label = _t(self::class . '.OptInGoToShop', 'Go to shop');
        $this->extend('updateBtnLabelGoToShop', $label);
        return $label;
    }
}