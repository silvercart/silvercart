<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\DeletedCustomerReason;
use SilverCart\Model\Pages\MyAccountHolder;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Shows customerdata + edit.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CustomerDataPage extends MyAccountHolder
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartCustomerDataPage';
    /**
     * DB attributes.
     * 
     * @var string[]
     */
    private static $db = [
        'DeleteAccountContent' => 'HTMLText',
        'LoginAttemptContent'  => 'HTMLText',
    ];
    /**
     * Indicates whether this page type can be root
     *
     * @var bool
     */
    private static $can_be_root = false;
    /**
     * The icon to use for this page in the storeadmin sitetree.
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/my_account_holder-file.gif";
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        $this->beforeUpdateFieldLabels(function(array &$fields) {
            $fields['DeleteAccountContent'] = _t(self::class . '.DeleteAccountContent', 'Information to show at the form to delete a customer account');
            $fields['LoginAttemptContent']  = _t(self::class . '.LoginAttemptContent', 'Information to show below the login attempt information');
        });
        return parent::fieldLabels($includerelations);
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fields->addFieldToTab('Root.Main', HTMLEditorField::create('LoginAttemptContent', $this->fieldLabel('LoginAttemptContent'), $this->LoginAttemptContent)->setRows(4));
            $fields->findOrMakeTab('Root.CustomerDeletion', DeletedCustomerReason::singleton()->i18n_plural_name());
            $fields->addFieldToTab('Root.CustomerDeletion', HTMLEditorField::create('DeleteAccountContent', $this->fieldLabel('DeleteAccountContent'), $this->DeleteAccountContent)->setRows(4));
            $fields->addFieldToTab('Root.CustomerDeletion', GridField::create('DeletedCustomerReason', DeletedCustomerReason::singleton()->i18n_plural_name(), DeletedCustomerReason::get(), GridFieldConfig_RecordEditor::create()));
            if (class_exists(GridFieldOrderableRows::class)) {
                $gridDeletedCustomerReason = $fields->dataFieldByName('DeletedCustomerReason');
                /* @var $gridDeletedCustomerReason \SilverStripe\Forms\GridField\GridField */
                if ($gridDeletedCustomerReason !== null) {
                    $gridDeletedCustomerReason->getConfig()->addComponent(GridFieldOrderableRows::create('Sort'));
                }
            }
        });
        return parent::getCMSFields();
    }
    
    /**
     * Returns whether this page has a summary.
     * 
     * @return bool
     */
    public function hasSummary() : bool
    {
        return true;
    }
    
    /**
     * Returns the summary of this page.
     * 
     * @return DBHTMLText
     */
    public function getSummary() : DBHTMLText
    {
        return $this->renderWith('SilverCart/Model/Pages/Includes/CustomerDataSummary');
    }
    
    /**
     * Returns the summary of this page.
     * 
     * @return string
     */
    public function getSummaryTitle() : string
    {
        return _t(MyAccountHolder::class . '.YOUR_PERSONAL_DATA', 'Your personal data');
    }
}