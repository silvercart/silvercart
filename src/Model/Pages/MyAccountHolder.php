<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

/**
 * holder for customers private area.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MyAccountHolder extends \Page
{
    const SESSION_KEY               = 'SilverCart.MyAccountHolder';
    const INFO_MESSAGE_SESSION_KEY  = 'SilverCart.MyAccountHolder.InfoMessages';
    const INFO_MESSAGE_TYPE_INFO    = 'info';
    const INFO_MESSAGE_TYPE_SUCCESS = 'success';
    const INFO_MESSAGE_TYPE_WARNING = 'warning';
    const INFO_MESSAGE_TYPE_DANGER  = 'danger';
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartMyAccountHolder';
    /**
     * Icon to display in CMS site tree
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/my_account_holder-file.gif";
    /**
     * Optional list of info messages to show above the login form.
     *
     * @var array
     */
    private static $info_messages = [];

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name()
    {
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
    public function plural_name()
    {
        return Tools::plural_name_for($this); 
    }

    /**
     * Adds the given message to the info messages.
     * 
     * The optional $infoMessageType can be one of:
     * 
     * self::INFO_MESSAGE_TYPE_INFO
     * self::INFO_MESSAGE_TYPE_SUCCESS
     * self::INFO_MESSAGE_TYPE_WARNING
     * self::INFO_MESSAGE_TYPE_DANGER
     * 
     * Default is self::INFO_MESSAGE_TYPE_INFO.
     * 
     * @param string $info_message      Info message
     * @param string $info_message_type Info message type
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.08.2018
     */
    public static function add_info_message($info_message, $info_message_type = self::INFO_MESSAGE_TYPE_INFO)
    {
        self::$info_messages[] = ArrayData::create([
            'Text' => $info_message,
            'Type' => $info_message_type,
        ]);
        self::save_info_messages();
    }
    
    /**
     * Returns the info messages.
     * 
     * @return array
     */
    public static function get_info_messages()
    {
        if (empty(self::$info_messages)) {
            self::$info_messages = Tools::Session()->get(self::INFO_MESSAGE_SESSION_KEY);
        }
        return (array) self::$info_messages;
    }
    
    /**
     * Sets the info messages.
     * 
     * @param array $info_messages Info messages
     * 
     * @return void
     */
    public static function set_info_messages($info_messages)
    {
        self::$info_messages = $info_messages;
        self::save_info_messages();
    }
    
    /**
     * Saves the info messages in session.
     * 
     * @return void
     */
    public static function save_info_messages()
    {
        Tools::Session()->set(self::INFO_MESSAGE_SESSION_KEY, self::$info_messages);
        Tools::saveSession();
    }
    
    /**
     * Clears the info message data out of session.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.08.2018
     */
    public static function reset_info_messages() {
        Tools::Session()->set(self::INFO_MESSAGE_SESSION_KEY, null);
        Tools::saveSession();
    }
    
    /**
     * Returns the info messages to render in template.
     * 
     * @return ArrayList
     */
    public function InfoMessages()
    {
        return ArrayList::create(self::get_info_messages());
    }
}