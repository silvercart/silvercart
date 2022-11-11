<?php

namespace SilverCart\Model\Pages;

use Page;
use SilverCart\Dev\Tools;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ArrayData;
use function _t;

/**
 * holder for customers private area.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MyAccountHolder extends Page
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
     * DB properties.
     * 
     * @var string[]
     */
    private static $db = [
        'ChangePasswordContent' => 'HTMLText',
        'ChangePasswordInfo'    => 'HTMLText',
    ];
    /**
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     * 
     * @var string
     */
    private static $icon_class = 'font-icon-p-profile';
    /**
     * Optional list of info messages to show above the login form.
     *
     * @var array
     */
    private static $info_messages = [];

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
     */
    public static function add_info_message(string $info_message, string $info_message_type = self::INFO_MESSAGE_TYPE_INFO) : void
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
    public static function get_info_messages() : array
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
    public static function set_info_messages(array $info_messages) : void
    {
        self::$info_messages = $info_messages;
        self::save_info_messages();
    }
    
    /**
     * Saves the info messages in session.
     * 
     * @return void
     */
    public static function save_info_messages() : void
    {
        Tools::Session()->set(self::INFO_MESSAGE_SESSION_KEY, self::$info_messages);
        Tools::saveSession();
    }
    
    /**
     * Clears the info message data out of session.
     * 
     * @return void
     */
    public static function reset_info_messages() : void
    {
        Tools::Session()->set(self::INFO_MESSAGE_SESSION_KEY, null);
        Tools::saveSession();
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            if ($this->IdentifierCode !== self::IDENTIFIER_MY_ACCOUNT_HOLDER) {
                return;
            }
            $default = _t('SilverStripe\\Security\\Security.NOTERESETPASSWORD', 'Enter your e-mail address and we will send you a link with which you can reset your password');
            $fields->insertAfter('Content', HTMLEditorField::create('ChangePasswordContent', $this->fieldLabel('ChangePasswordContent'))->setDescription("{$this->fieldLabel('ChangePasswordContentDesc')} (\"{$default}\")")->addExtraClass('stacked')->setRows(3));
            $fields->insertAfter('ChangePasswordContent', HTMLEditorField::create('ChangePasswordInfo', $this->fieldLabel('ChangePasswordInfo'))->setDescription($this->fieldLabel('ChangePasswordInfoDesc'))->addExtraClass('stacked')->setRows(3));
        });
        return parent::getCMSFields();
    }

    /**
     * Field labels for display in tables.
     *
     * @param bool $includerelations Include relation fields?
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        $this->beforeUpdateFieldLabels(function(&$labels) {
            $labels = array_merge(
                    $labels,
                    Tools::field_labels_for(self::class),
            );
        });
        return parent::fieldLabels($includerelations);
    }
    
    /**
     * Returns the info messages to render in template.
     * 
     * @return ArrayList
     */
    public function InfoMessages() : ArrayList
    {
        return ArrayList::create(self::get_info_messages());
    }

    /**
     * Uses the children of MyAccountHolder to render a subnavigation
     * with the SilverCart/Model/Pages/Includes/SubNavigation.ss template.
     * 
     * @param string $identifierCode param only added because it exists on parent::getSubNavigation
     *                               to avoid strict notice
     *
     * @return DBHTMLText
     */
    public function getSubNavigation($identifierCode = Page::IDENTIFIER_MY_ACCOUNT_HOLDER) : DBHTMLText
    {
        $output = $this->customise($this->getSubNavigationElements())->renderWith('SilverCart/Model/Pages/Includes/SubNavigation');
        return DBHTMLText::create()->setValue($output);
    }
    
    /**
     * Returns the sub navigation elements.
     * 
     * @return array
     */
    public function getSubNavigationElements() : array
    {
        $elements = [
            'SubElementsTitle'     => Tools::PageByIdentifierCode(Page::IDENTIFIER_MY_ACCOUNT_HOLDER)->MenuTitle,
            'SubElementsTitleLink' => Tools::PageByIdentifierCode(Page::IDENTIFIER_MY_ACCOUNT_HOLDER)->Link(),
            'SubElements'          => Tools::PageByIdentifierCode(Page::IDENTIFIER_MY_ACCOUNT_HOLDER)->Children(),
        ];
        $this->extend('updateSubNavigation', $elements);
        return $elements;
    }
    
    /**
     * Returns the sub navigation elements for template.
     * 
     * @return ArrayData
     */
    public function getSubNavigationElementsForTemplate() : ArrayData
    {
        return ArrayData::create($this->getSubNavigationElements());
    }
}