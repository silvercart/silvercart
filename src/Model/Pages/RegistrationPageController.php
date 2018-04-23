<?php

namespace SilverCart\Model\Pages;

use SilverCart\Admin\Model\Config;
use SilverCart\Forms\RegisterRegularCustomerForm;
use SilverStripe\Control\Director;

/**
 * RegistrationPage Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class RegistrationPageController extends \PageController {
    
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = array(
        'RegisterRegularCustomerForm',
        'welcome',
    );

    /**
     * initialisation of the form object
     * logged in members get logged out
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2017
     */
    public function init() {
        if (Config::EnableSSL()) {
            Director::forceSSL();
        }
        parent::init();
    }
    
    /**
     * Returns the RegisterRegularCustomerForm.
     * 
     * @return RegisterRegularCustomerForm
     */
    public function RegisterRegularCustomerForm() {
        $form = new RegisterRegularCustomerForm($this);
        return $form;
    }
    
}