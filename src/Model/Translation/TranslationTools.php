<?php

namespace SilverCart\Model\Translation;

use Locale;
use SilverCart\Dev\Tools;
use SilverCart\Admin\Model\Config;
use SilverCart\Model\Translation\TranslatableDataObjectExtension;
use SilverStripe\Core\Config\Config as SilverStripeConfig;
use SilverStripe\Dev\Deprecation;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;

/** 
 * Helper class to combine language object specific methods.
 *
 * @package SilverCart
 * @subpackage Model_Translation
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class TranslationTools {
    
    /**
     * Returns the translated singular name ("Translation") of a translation object.
     * 
     * @return string
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.10.2017
     */
    public static function singular_name() {
        return _t(static::class . '.TRANSLATION', 'Translation');
    }
    
    /**
     * Returns the translated plural name ("Translations") of a translation object.
     * 
     * @return string
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.10.2017
     */
    public static function plural_name() {
        return _t(static::class . '.TRANSLATIONS', 'Translations');
    }

    /**
     * Getter for the language object for an object that has translations
     * I impemented it a a static method because it would be redundantly declared
     * in any multilanguage DataObject
     *
     * @param HasManyList $componentset has_many relation to be searched for the right translation
     * @param string      $locale       locale eg. de_DE, en_NZ, ...
     *
     * @return DataObject|false
     * 
     * @deprecated since version 4.0
     */
    public static function getLanguage($componentset, $locale = false) {
        Deprecation::notice(
            '4.0',
            'TranslationTools::getLanguage() is deprecated. Use TranslationTools::get_translation() instead.'
        );
        return self::get_translation($componentset, $locale);
    }

    /**
     * Getter for the language object for an object that has translations
     * I impemented it a a static method because it would be redundantly declared
     * in any multilanguage DataObject
     *
     * @param HasManyList $componentset has_many relation to be searched for the right translation
     * @param string      $locale       locale eg. de_DE, en_NZ, ...
     *
     * @return DataObject
     */
    public static function get_translation($componentset, $locale = false) {
        $lang = false;
        if ($locale == false) {
            $locale = Tools::current_locale();
        }
        if ($componentset->find('Locale', $locale)) {
            $lang = $componentset->find('Locale', $locale);
        } elseif (Config::useDefaultLanguageAsFallback()) {
            if ($componentset->find('Locale', Config::DefaultLanguage())) {
                $lang = $componentset->find('Locale', Config::DefaultLanguage());
            }
        }
        return $lang;
    }

    /**
     * Default scaffolding for language objects.
     *
     * @param string $className      class name of the language class
     * @param array  $restrictFields List of restrict fields
     * 
     * @return FieldList a field list with all multilingual attributes
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 09.07.2014
     * @deprecated since version 4.0
     */
    public static function prepareCMSFields($className, $restrictFields = false) {
        Deprecation::notice(
            '4.0',
            'TranslationTools::prepareCMSFields() is deprecated. Use TranslationTools::prepare_cms_fields() instead.'
        );
        return self::prepare_cms_fields($className, $restrictFields);
    }

    /**
     * Default scaffolding for language objects.
     *
     * @param string $className      class name of the language class
     * @param array  $restrictFields List of restrict fields
     * 
     * @return FieldList a field list with all multilingual attributes
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 09.07.2014
     */
    public static function prepare_cms_fields($className, $restrictFields = false) {
        if (is_object($className)) {
            $className = get_class($className);
        }
        $dataobject = singleton($className);
        if (!$dataobject) {
            return new FieldList();
        }
        $languageFields = $dataobject->scaffoldFormFields(
                array(
                    'includeRelations'  => false,
                    'tabbed'            => false,
                    'ajaxSafe'          => true,
                    'restrictFields'    => $restrictFields,
                )
        );
        $dataobject->extend('updateLanguageCMSFields', $languageFields);
        $languageFields->removeByName('Locale');
        foreach ($dataobject->hasOne() as $hasOneName => $hasOneObject) {
            $hasOneFieldName = $hasOneName . 'ID';
            $languageFields->removeByName($hasOneFieldName);
        }
        return $languageFields;
    }
    
    /**
     * Creates and returns the language dropdown field
     *
     * @param DataObject $dataObj          DataObject to get dropdown for
     * @param string     $translatingClass Context class of the LanguageDropdownField
     * @param string     $fieldName        Name of the LanguageDropdownField
     * 
     * @return LanguageDropdownField 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.12.2015
     * @deprecated since version 4.0
     */
    public static function prepareLanguageDropdownField($dataObj, $translatingClass = null, $fieldName = 'Locale') {
        Deprecation::notice(
            '4.0',
            'TranslationTools::prepareLanguageDropdownField() is deprecated. Use TranslationTools::prepare_translation_dropdown_field() instead.'
        );
        return self::prepare_translation_dropdown_field($dataObj, $translatingClass, $fieldName);
    }
    
    /**
     * Creates and returns the language dropdown field
     *
     * @param DataObject $dataObj          DataObject to get dropdown for
     * @param string     $translatingClass Context class of the LanguageDropdownField
     * @param string     $fieldName        Name of the LanguageDropdownField
     * 
     * @return DropdownField 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.12.2015
     */
    public static function prepare_translation_dropdown_field($dataObj, $translatingClass = null, $fieldName = 'Locale') {
        $localeDropdown = DropdownField::create(
                $fieldName,
                _t(Config::class . '.TRANSLATION', 'Translation'),
                i18n::getData()->getLocales()
        );
        $currentLocale          = Tools::current_locale();
        $localesWithTitle       = $localeDropdown->getSource();
        $usedLocalesWithTitle   = Tools::content_locales()->toArray();
        $languageList           = array();
        $usedLanguageList       = array();
        foreach ($localesWithTitle as $locale => $title) {
            if (is_array($title)) {
                foreach ($title as $locale2 => $title2) {
                    $title2 = self::get_translation_display_title($locale2, $currentLocale);
                    if (array_key_exists($locale2, $usedLocalesWithTitle)) {
                        $usedLanguageList[$locale2] = $title2;
                        unset($languageList[$locale2]);
                    } else {
                        $languageList[$locale2] = $title2;
                    }
                }
            } else {
                $title = self::get_translation_display_title($locale, $currentLocale);
                if (array_key_exists($locale, $usedLocalesWithTitle)) {
                    $usedLanguageList[$locale] = $title;
                    unset($languageList[$locale]);
                } else {
                    $languageList[$locale] = $title;
                }
            }
        }
        asort($languageList);
        
        if (count($usedLanguageList)) {
            asort($usedLanguageList);
            $languageList = array(
                _t('Form.LANGAVAIL', "Available languages") => $usedLanguageList,
                _t('Form.LANGAOTHER', "Other languages")    => $languageList
            );
        }
        
        $localeDropdown->setSource($languageList);
        $localeDropdown->setValue($dataObj->Locale);
        return $localeDropdown;
    }
    
    /**
     * Returns the display title of a LanguageDropdownFields option
     *
     * @param string $locale        Locale to get title for
     * @param string $currentLocale Locale to get translated title for
     * 
     * @return string
     * 
     * @deprecated since version 4.0
     */
    public static function getLanguageDisplayTitle($locale, $currentLocale) {
        Deprecation::notice(
            '4.0',
            'TranslationTools::getLanguageDisplayTitle() is deprecated. Use TranslationTools::get_translation_display_title() instead.'
        );
        return self::get_translation_display_title($locale, $currentLocale);
    }
    
    /**
     * Returns the display title of a LanguageDropdownFields option
     *
     * @param string $locale        Locale to get title for
     * @param string $currentLocale Locale to get translated title for
     * 
     * @return string
     */
    public static function get_translation_display_title($locale, $currentLocale) {
        $displayTitle = sprintf(
            "%s  [ %s ]",
            self::get_translation_name($locale, $currentLocale),
            self::get_translation_name($locale, $locale)
        );
        return $displayTitle;
    }
    
    /**
     * Returns the language name
     *
     * @param string $locale    Locale to get name for
     * @param string $in_locale Locale to display name in (when PHP intl is not installed, this is the indicator for native displaying)
     * 
     * @return string
     * 
     * @deprecated since version 4.0
     */
    public static function getLanguageName($locale, $in_locale) {
        Deprecation::notice(
            '4.0',
            'TranslationTools::getLanguageName() is deprecated. Use TranslationTools::get_translation_name() instead.'
        );
        return self::get_translation_name($locale, $in_locale);
    }
    
    /**
     * Returns the language name
     *
     * @param string $locale    Locale to get name for
     * @param string $in_locale Locale to display name in (when PHP intl is not installed, this is the indicator for native displaying)
     * 
     * @return string
     */
    public static function get_translation_name($locale, $in_locale) {
        if (class_exists('Locale')) {
            $languageName = Locale::getDisplayName($locale, $in_locale);
        } else {
            if ($locale == $in_locale) {
                $languageName = i18n::with_locale($locale, function () use ($locale) {
                    return $this->getShortName();
                });
            } else {
                $languageName = i18n::getData()->languageName($locale);
            }
            if (empty($languageName)) {
                $knownLocales = i18n::getSources()->getKnownLocales();
                if (array_key_exists($locale, $knownLocales)) {
                    $languageName = $knownLocales[$locale];
                }
            }
        }
        return $languageName;
    }
    
    /**
     * Returns the language name
     *
     * @param string $locale    Locale to get name for
     * @param string $in_locale Locale to display name in (when PHP intl is not installed, this is the indicator for native displaying)
     * 
     * @return string
     * 
     * @deprecated since version 4.0
     */
    public static function getDisplayLanguage($locale, $in_locale) {
        Deprecation::notice(
            '4.0',
            'TranslationTools::getDisplayLanguage() is deprecated. Use TranslationTools::get_display_language() instead.'
        );
        return self::get_display_language($locale, $in_locale);
    }
    
    /**
     * Returns the language name
     *
     * @param string $locale    Locale to get name for
     * @param string $in_locale Locale to display name in (when PHP intl is not installed, this is the indicator for native displaying)
     * 
     * @return string
     */
    public static function get_display_language($locale, $in_locale) {
        if (class_exists('Locale')) {
            $languageName = Locale::getDisplayLanguage($locale, $in_locale);
        } else {
            if ($locale == $in_locale) {
                $languageName = i18n::with_locale($locale, function () use ($locale) {
                    return $this->getShortName();
                });
            } else {
                $languageName = i18n::getData()->languageName($locale);
            }
            if (empty($languageName)) {
                $knownLocales = i18n::getSources()->getKnownLocales();
                if (array_key_exists($locale, $knownLocales)) {
                    $languageName = $knownLocales[$locale];
                }
            }
        }
        return $languageName;
    }

    /**
     * Writes the given language object
     *
     * @param DataObject $languageObj Language object to write
     * @param array      $mainRecord  Main record data of the multilingual DataObject
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     * @deprecated since version 4.0
     */
    public static function writeLanguageObject($languageObj, $mainRecord) {
        Deprecation::notice(
            '4.0',
            'TranslationTools::writeLanguageObject() is deprecated. Use TranslationTools::write_translation_object() instead.'
        );
        return self::write_translation_object($languageObj, $mainRecord);
    }

    /**
     * Writes the given language object
     *
     * @param DataObject $translationObj Translation object to write
     * @param array      $mainRecord     Main record data of the multilingual DataObject
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     */
    public static function write_translation_object($translationObj, $mainRecord) {
        $record = array();
        $translationDbFields = (array) $translationObj->config()->get('db');
        foreach ($translationDbFields as $dbFieldName => $dbFieldType) {
            if (array_key_exists($dbFieldName, $mainRecord)) {
                $record[$dbFieldName] = $mainRecord[$dbFieldName];
            }
        }
        $translationObj->update($record);
        $translationObj->write();
    }
    
    /**
     * Returns all translatable DataObjects as an array
     * 
     * @return array
     * 
     * @global array $_ALL_CLASSES A map of all classes, their type and ancestry
     * @deprecated since version 4.0
     */
    public static function getTranslatableDataObjects() {
        Deprecation::notice(
            '4.0',
            'TranslationTools::getTranslatableDataObjects() is deprecated. Use TranslationTools::get_translatable_data_objects() instead.'
        );
        return self::get_translatable_data_objects();
    }
    
    /**
     * Returns all translatable DataObjects as an array
     * 
     * @return array
     * 
     * @global array $_ALL_CLASSES A map of all classes, their type and ancestry
     */
    public static function get_translatable_data_objects() {
        global $_ALL_CLASSES;
        $translatableDataObjects = array();
        foreach ($_ALL_CLASSES['parents'] as $className => $ancestry) {

            if (strpos($className, 'SilverCart') === false) {
                continue;
            }

            $extensions = SilverStripeConfig::inst()->get($className, 'extensions');
            if (!is_null($extensions) &&
                is_array($extensions) &&
                in_array(TranslatableDataObjectExtension::class, $extensions)) {
                $translatableDataObjects[] = $className;
            }
        }
        return $translatableDataObjects;
    }
    
}

