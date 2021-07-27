<?php

namespace SilverCart\Model\Customer;

use SilverCart\Dev\Tools;
use SilverStripe\ORM\DataObject;
use SilverCart\Model\Translation\TranslatableDataObjectExtension;
use SilverStripe\i18n\i18n;
use TractorCow\Fluent\Model\Locale;

/**
 * 
 * @package SilverCart
 * @subpackage Model\Customer
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.17.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @mixin TranslatableDataObjectExtension
 * 
 * @property string $Reason Reason
 * @property int    $Sort   Sort
 */
class DeletedCustomerReason extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    const DEFAULT_NOT_USING_ANYMORE = 'NotUsingAnymore';
    const DEFAULT_MULTI_ACCOUNT     = 'MultiAccount';
    const DEFAULT_NEW_ACCOUNT       = 'NewAccount';
    const DEFAULT_SECURITY_MISTRUST = 'SecurityMistrust';
    const DEFAULT_NO_REASON         = 'NoReason';
    /**
     * DB table name.
     * 
     * @var string
     */
    private static $table_name = 'SilverCart_DeletedCustomerReason';
    /**
     * DB attributes.
     * 
     * @var string[]
     */
    private static $db = [
        'Sort'   => 'Int',
    ];
    /**
     * Casted attributes.
     * 
     * @var string[]
     */
    private static $casting = [
        'Reason' => 'Text',
    ];
    /**
     * Has many relations.
     * 
     * @var string[]
     */
    private static $has_many = [
        'DeletedCustomerReasonTranslations' => DeletedCustomerReasonTranslation::class,
    ];
    /**
     * Summary fields.
     * 
     * @var string[]
     */
    private static $summary_fields = [
        'Reason',
    ];
    /**
     * Extensions.
     * 
     * @var string[]
     */
    private static $extensions = [
        TranslatableDataObjectExtension::class,
    ];
    /**
     * Insert translation fields.
     * 
     * @var bool
     */
    private static $insert_translation_cms_fields = true;
    /**
     * Default reasons.
     * 
     * @var string[]
     */
    private static $default_reasons = [
        self::DEFAULT_NOT_USING_ANYMORE,
        self::DEFAULT_MULTI_ACCOUNT,
        self::DEFAULT_NEW_ACCOUNT,
        self::DEFAULT_SECURITY_MISTRUST,
        self::DEFAULT_NO_REASON,
    ];

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
     * i18n for field labels
     *
     * @param bool $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, []);
    }
    
    /**
     * Requires the default records.
     * 
     * @return void
     */
    public function requireDefaultRecords() : void
    {
        if (self::get()->exists()) {
            return;
        }
        $currentLocale = i18n::get_locale();
        $locales       = Locale::get()->exclude('Locale', $currentLocale);
        foreach ((array) $this->config()->default_reasons as $default) {
            $reason = _t(self::class . ".Default_{$default}", $default);
            if ($reason === $default) {
                continue;
            }
            $obj = self::create();
            $obj->Reason = $reason;
            $obj->write();
            foreach ($locales as $locale) {
                /* @var $locale Locale */
                i18n::set_locale($locale->Locale);
                $reason = _t(self::class . ".Default_{$default}", $default);
                if ($reason === $default) {
                    continue;
                }
                $translation = DeletedCustomerReasonTranslation::create();
                $translation->Locale                  = $locale->Locale;
                $translation->Reason                  = $reason;
                $translation->DeletedCustomerReasonID = $obj->ID;
                $translation->write();
                i18n::set_locale($currentLocale);
            }
        }
    }
    
    /**
     * Returns the title.
     * 
     * @return string
     */
    public function getTitle() : string
    {
        return $this->Reason;
    }
    
    /**
     * Returns the Reason
     *
     * @return string
     */
    public function getReason() : string
    {
        return (string) $this->getTranslationFieldValue('Reason');
    }
}