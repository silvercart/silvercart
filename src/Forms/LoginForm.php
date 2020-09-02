<?php

namespace SilverCart\Forms;

use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\TextField;
use SilverCart\Model\Pages\Page;
use SilverStripe\Control\Director;
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
     * URL to redirect to after logging in.
     *
     * @var string
     */
    protected $redirectTo = null;
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
                HiddenField::create('redirect_to', '', $this->getRedirectTo()),
                HiddenField::create('cn', '', static::class),
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
    
    /**
     * Returns the URL to redirect to after logging in.
     * 
     * @return string
     */
    public function getRedirectTo() : string
    {
        if (is_null($this->redirectTo)) {
            $baseURL    = Director::baseURL();
            $requestURL = $_SERVER['REQUEST_URI'];
            if (strpos($requestURL, $baseURL) !== 0) {
                $requestURL = $baseURL . substr($requestURL, 1);
            }
            if (strpos($requestURL, '?') !== false) {
                $urlParts = explode('?', $requestURL);
                $query    = array_pop($urlParts);
                if (strpos($query, 'BackURL=') !== false) {
                    $queryParts = explode('&', $query);
                    foreach ($queryParts as $queryPart) {
                        if (strpos($queryPart, 'BackURL=') === 0) {
                            list($name, $value) = explode('=', $queryPart);
                            $backURL = urldecode($value);
                            if (strpos($backURL, $baseURL) !== 0) {
                                $backURL = $baseURL . substr($backURL, 1);
                            }
                            $newValue     = urlencode($backURL);
                            $newQueryPart = "{$name}={$newValue}";
                            $requestURL   = str_replace($queryPart, $newQueryPart, $requestURL);
                            break;
                        }
                    }
                }
            }
            $this->redirectTo = $requestURL;
        }
        return (string) $this->redirectTo;
    }

    /**
     * Sets the URL to redirect to after logging in.
     * 
     * @param string $redirectTo URL to redirect to after logging in
     * 
     * @return \SilverCart\Forms\LoginForm
     */
    public function setRedirectTo(string $redirectTo) : LoginForm
    {
        $this->redirectTo = $redirectTo;
        return $this;
    }
}