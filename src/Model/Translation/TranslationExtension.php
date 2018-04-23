<?php

namespace SilverCart\Model\Translation;

use ReflectionClass;
use SilverCart\Admin\Model\Config;
use SilverCart\Model\Product\ProductTranslation;
use SilverCart\Model\Translation\TranslationTools;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

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
class TranslationExtension extends DataExtension {
    
    /**
     * Extends the database fields
     *
     * @var array
     */
    private static $db = array(
        'Locale' => \SilverCart\ORM\FieldType\DBLocale::class,
    );
    
    /**
     * Extends the db indexes
     *
     * @var array
     */
    private static $indexes = array(
        'Locale' => '("Locale")',
    );
    
    /**
     * Field lable for Locale should always be multilingual
     *
     * @param array &$labels Lables to update
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.05.2012
     */
    public function updateFieldLabels(&$labels) {
        parent::updateFieldLabels($labels);
        $labels['Locale'] = _t(ProductTranslation::class . '.LOCALE', 'Language');
    }
    
    /**
     * must return true for the LanguageDropdown field to work properly
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.05.2012
     */
    public function canTranslate() {
        return true;
    }
    
    /**
     * The summary fields should at least show the locale
     * 
     * @param array &$fields Fields to update
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.05.2012
     */
    public function updateSummaryFields(&$fields) {
        $fields = array_merge(
                array(
                    'NativeNameForLocale' => _t(Config::class . '.TRANSLATION', 'Translation'),
                ),
                $fields
        );
    }
    
    /**
     * adjust CMS fields for display in the popup window
     *
     * @param FieldList $fields the FieldList from getCMSFields()
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 06.01.2012
     */
    public function updateCMSFields(FieldList $fields) {
        $fields = TranslationTools::prepare_cms_fields(get_class($this->owner));
        $localeDropdown = TranslationTools::prepare_translation_dropdown_field($this->owner);
        $fields->push($localeDropdown);
    }
    
    /**
     * return the locale as native name
     *
     * @return string native name for the locale 
     */
    public function getNativeNameForLocale() {
        return $this->owner->dbObject('Locale')->getNativeName();
    }
    
    /**
     * Returns the language class relation field name
     *
     * @return string 
     */
    public function getRelationClassName() {
        $relationClassName = substr($this->owner->ClassName, 0, -11);
        return $relationClassName;
    }
    
    /**
     * Returns the language class relation field name
     *
     * @return string 
     */
    public function getRelationFieldName() {
        $reflection = new ReflectionClass($this->owner->ClassName);
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
    public function getTranslatedLocales() {
        $langs        = array();
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
    public function getTranslations() {
        $relationFieldName  = $this->getRelationFieldName();
        $translations       = DataObject::get(
                $this->owner->ClassName,
                sprintf(
                        "\"%s\" = '%s'",
                        $relationFieldName,
                        $this->owner->{$relationFieldName}
                )
        );
        return $translations;
    }
}

