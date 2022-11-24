<?php

namespace SilverCart\Model\Translation;

use ReflectionClass;
use SilverCart\Admin\Model\Config;
use SilverCart\Model\Product\ProductTranslation;
use SilverCart\Model\Translation\TranslationTools;
use SilverCart\ORM\FieldType\DBLocale;
use SilverStripe\Core\Config\Config as Config2;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\FieldList;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use function _t;

/** 
 * Adds methods that are common to all language classes e.g. ProductTranslation
 * Updates CMS fields and brings the common attribute Locale.
 *
 * @package SilverCart
 * @subpackage Model_Translation
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class TranslationExtension extends DataExtension
{
    /**
     * Extends the database fields
     *
     * @var array
     */
    private static $db = [
        'Locale' => DBLocale::class,
    ];
    /**
     * Extends the db indexes
     *
     * @var array
     */
    private static $indexes = [
        'Locale' => '("Locale")',
    ];
    
    /**
     * Field lable for Locale should always be multilingual
     *
     * @param array &$labels Lables to update
     *
     * @return void
     */
    public function updateFieldLabels(&$labels) : void
    {
        parent::updateFieldLabels($labels);
        $labels['Locale']              = _t(ProductTranslation::class . '.LOCALE', 'Language');
        $labels['NativeNameForLocale'] = _t(Config::class . '.TRANSLATION', 'Translation');
    }
    
    /**
     * must return true for the LanguageDropdown field to work properly
     *
     * @return bool
     */
    public function canTranslate() : bool
    {
        return true;
    }
    
    /**
     * The summary fields should at least show the locale
     * 
     * @param array &$fields Fields to update
     *
     * @return void
     */
    public function updateSummaryFields(&$fields) : void
    {
        $fields = array_merge([
            'NativeNameForLocale' => _t(Config::class . '.TRANSLATION', 'Translation'),
        ], $fields);
    }
    
    /**
     * adjust CMS fields for display in the popup window
     *
     * @param FieldList $fields the FieldList from getCMSFields()
     *
     * @return void
     */
    public function updateCMSFields(FieldList $fields) : void
    {
        $fields         = TranslationTools::prepare_cms_fields(get_class($this->owner));
        $localeDropdown = TranslationTools::prepare_translation_dropdown_field($this->owner);
        $fields->push($localeDropdown);
    }
    
    /**
     * return the locale as native name
     *
     * @return string native name for the locale 
     */
    public function getNativeNameForLocale() : string
    {
        return (string) $this->owner->dbObject('Locale')->getNativeName();
    }
    
    /**
     * Returns the language class relation field name
     *
     * @return string 
     */
    public function getRelationClassName() : string
    {
        return substr($this->owner->ClassName, 0, -11);
    }
    
    /**
     * Returns the language class relation field name
     *
     * @return string 
     */
    public function getRelationFieldName() : string
    {
        $reflection        = new ReflectionClass($this->owner->ClassName);
        $relationFieldName = substr($reflection->getShortName(), 0, -11) . 'ID';
        return $relationFieldName;
    }
    
    /**
     * Returns all translations for this DataObject as an array.
     * Example:
     * <code>
     * array(
     *      'de_DE' => 'de_DE',
     *      'en_US' => 'en_US',
     *      'en_GB' => 'en_GB'
     * );
     * </code>
     * 
     * @return array
     */
    public function getTranslatedLocales() : array
    {
        $langs        = [];
        $translations = $this->getTranslations();
        if ($translations) {
            foreach ($translations as $translation) {
                $langs[$translation->Locale] = $translation->Locale;
            }
        }
        return $langs;
    }
    
    /**
     * Returns all translations for this DataObject as DataList
     *
     * @return DataList 
     */
    public function getTranslations() : DataList
    {
        $value              = $this->owner->{$relationFieldName};
        $relationFieldName  = $this->getRelationFieldName();
        $translations       = DataObject::get(
                $this->owner->ClassName,
                "{$relationFieldName} = '{$value}'",
        );
        return $translations;
    }
    
    /**
     * Will add missing translations and therefore repair broken objects.
     * 
     * @return void
     */
    public function requireDefaultTranslations() : void
    {
        $tableName            = Config2::inst()->get($this->getRelationClassName(), 'table_name');
        $translationTableName = $this->owner->config()->table_name;
        $relationIDName       = $this->getRelationFieldName();
        $objects              = DB::query("SELECT {$tableName}.ID as OID, {$translationTableName}.{$relationIDName} as TOID FROM {$tableName} LEFT JOIN {$translationTableName} ON ({$tableName}.ID = {$translationTableName}.{$relationIDName}) WHERE {$translationTableName}.{$relationIDName} IS NULL");
        if ($objects->numRecords() === 0) {
            return;
        }
        foreach ($objects as $object) {
            if ($object['TOID'] !== null) {
                continue;
            }
            $translation = Injector::inst()->createWithArgs($this->owner->ClassName, []);
            $translation->{$relationIDName} = $object['OID'];
            $translation->Locale            = i18n::get_locale();
            $translation->write();
        }
    }
    
    /**
     * Will delete broken objects (DataObjects without any translation object).
     * 
     * @return void
     */
    public function deleteBrokenDataObjects() : void
    {
        $tableName            = Config2::inst()->get($this->getRelationClassName(), 'table_name');
        $translationTableName = $this->owner->config()->table_name;
        $relationIDName       = $this->getRelationFieldName();
        DB::query("DELETE {$tableName} FROM {$tableName} LEFT JOIN {$translationTableName} ON ({$tableName}.ID = {$translationTableName}.{$relationIDName}) WHERE {$translationTableName}.{$relationIDName} IS NULL");
    }
}

