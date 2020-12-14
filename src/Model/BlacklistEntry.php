<?php

namespace SilverCart\Model;

use SilverCart\Dev\Tools;
use SilverCart\Model\ContactMessage;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBText;

/**
 * Represents a blacklist entry to prevent repeating spam.
 * 
 * @package SilverCart
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.12.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Content            Content
 * @property bool   $IsPartialMatch     Is Partial Match
 * @property string $Title              Title
 * @property string $IsPartialMatchNice Is Partial Match Nice
 */
class BlacklistEntry extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    /**
     * Adds the blacklist management fields to the given CMS $fields.
     * 
     * @param FieldList $fields CMS fields to add blacklis entry fields to
     * 
     * @return void
     */
    public static function getBlackListCMSFields(FieldList $fields) : void
    {
        $blacklistTab = $fields->findOrMakeTab('Root.Blacklist', self::singleton()->fieldLabel('Blacklist'));
        $blacklistTab->setTitle(self::singleton()->fieldLabel('Blacklist'));
        $fields->addFieldToTab('Root.Blacklist', GridField::create('Blacklist', 'Blacklist', self::get(), GridFieldConfig_RecordEditor::create()));
    }
    
    /**
     * Returns whether the given content is matching with any blacklist entry.
     * 
     * @param string $content Content to check
     * 
     * @return bool
     */
    public static function isSpam(string $content) : bool
    {
        $isSpam = self::get()->filter([
                    'Content'        => trim($content),
                    'IsPartialMatch' => false,
                ])->count() > 0;
        if (!$isSpam) {
            $partial = self::get()->filter('IsPartialMatch', true);
            foreach ($partial as $blacklistEntry) {
                /* @var $blacklistEntry BlacklistEntry */
                if (strpos($content, $blacklistEntry->Content) !== false) {
                    $isSpam = true;
                    break;
                }
            }
        }
        return $isSpam;
    }

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilverCart_BlackListEntry';
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $db = [
        'Content'        => 'Text',
        'IsPartialMatch' => 'Boolean',
    ];
    /**
     * Casted attributes.
     *
     * @var array
     */
    private static $casting = [
        'Title'              => 'Text',
        'IsPartialMatchNice' => 'Text',
    ];
    /**
     * Summary fields.
     *
     * @var array
     */
    private static $summary_fields = [
        'Title',
        'IsPartialMatchNice',
    ];
    
    /**
     * Returns the content limited to 5 words.
     * 
     * @return string
     */
    public function getIsPartialMatchNice() : string
    {
        return $this->IsPartialMatch ? Tools::field_label('Yes') : '';
    }
    
    /**
     * Returns the content limited to 5 words.
     * 
     * @return string
     */
    public function getTitle() : string
    {
        return DBText::create()->setValue($this->Content)->LimitWordCount(5);
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
        return $this->defaultFieldLabels($includerelations, [
            'Blacklist' => _t(ContactMessage::class . '.Blacklist', 'Blacklist'),
        ]);
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fields->dataFieldByName('Content')->setDescription($this->fieldLabel('ContentDesc'));
            $fields->dataFieldByName('IsPartialMatch')->setDescription($this->fieldLabel('IsPartialMatchDesc'));
        });
        return parent::getCMSFields();
    }
}