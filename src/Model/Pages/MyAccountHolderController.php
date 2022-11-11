<?php

namespace SilverCart\Model\Pages;

use PageController;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\LoginForm;
use SilverCart\Model\Pages\Page;
use SilverStripe\Control\Director;

/**
 * MyAccountHolder Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MyAccountHolderController extends PageController
{
    /**
     * statements to be called on object initialisation
     *
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.11.2010
     */
    protected function init()
    {
        if (Config::EnableSSL()) {
            Director::forceSSL();
        }
        Tools::Session()->clear("redirect"); //if customer has been to the checkout yet this is set to direct him back to the checkout after address editing
        parent::init();
    }

    /**
     * returns the link to the order detail page (without orderID)
     *
     * @param sting $orderID OrderID
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.07.2018
     */
    public function OrderDetailLink($orderID = '') : string
    {
        return Tools::PageByIdentifierCode(Page::IDENTIFIER_ORDER_HOLDER)->Link("detail/{$orderID}");
    }
    
    /**
     * Returns the LoginForm.
     * 
     * @return LoginForm
     */
    public function LoginForm() : LoginForm
    {
        return LoginForm::create($this);
    }
}