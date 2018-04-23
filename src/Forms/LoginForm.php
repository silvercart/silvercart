<?php

namespace SilverCart\Forms;

use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\TextField;
use SilverCart\Model\Pages\Page;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\PasswordField;

/**
 * A form login to ones' account.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class LoginForm extends CustomForm {
    
    /**
     * Custom form action path, if not linking to itself.
     * E.g. could be used to post to an external link
     *
     * @var string
     */
    protected $formActionPath = 'sc-action/doLogin';
    
    /**
     * Custom extra CSS classes.
     *
     * @var array
     */
    protected $customExtraClasses = [
        'page',
        'form',
        'form-vertical',
    ];
    
    /**
     * List of required fields.
     *
     * @var array
     */
    private static $requiredFields = [
        'emailaddress',
        'password',
    ];
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $fields += [
                HiddenField::create('redirect_to', '', $_SERVER['REQUEST_URI']),
                TextField::create('emailaddress', Page::singleton()->fieldLabel('EmailAddress')),
                PasswordField::create('password', Page::singleton()->fieldLabel('Password')),
            ];
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions() {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $actions += [
                FormAction::create('dologin', Page::singleton()->fieldLabel('Login'))
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            ];
        });
        return parent::getCustomActions();
    }

}