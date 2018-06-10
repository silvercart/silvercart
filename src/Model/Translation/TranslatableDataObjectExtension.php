<?php

namespace SilverCart\Model\Translation;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Model\Translation\TranslationTools;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Dev\Deprecation;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\Queries\SQLSelect;
use ReflectionClass;

/** 
 * Extends DataObjects to make them multilingual.
 *
 * @package SilverCart
 * @subpackage Model_Translation
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class TranslatableDataObjectExtension extends DataExtension {
    
    /**
     * The translation object list
     *
     * @var array
     */
    protected $translationCache = [];
    
    /**
     * Manipulates the SQL query
     *
     * @param SQLSelect $query Query to manipulate
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.05.2012
     */
    public function augmentSQL(SQLSelect $query, DataQuery $dataQuery = null) {
        if (!$query->isJoinedTo($this->getTranslationTableName())) {
//        if (!$query->isJoinedTo($this->getTranslationTableName()) &&
//            !$query->getDelete()) {
            $silvercartDefaultLocale = Config::Locale();
            $query->addLeftJoin(
                    $this->getTranslationTableName(),
                    sprintf(
                            "(\"%s\".\"ID\" = \"%s\".\"%s\")",
                            $this->getBaseTableName(),
                            $this->getTranslationTableName(),
                            $this->getRelationFieldName()
                    )
            );
            $addToWhere = '';
            if ($this->getBaseTranslationTableName() != $this->getTranslationTableName()) {
                $query->addLeftJoin(
                        $this->getBaseTranslationTableName(),
                        sprintf(
                                "(\"%s\".\"ID\" = \"%s\".\"ID\")",
                                $this->getTranslationTableName(),
                                $this->getBaseTranslationTableName()
                        )
                );
                $addToWhere = sprintf(
                        "AND \"%s\".\"ID\" = \"%s\".\"ID\"",
                        $this->getBaseTranslationTableName(),
                        $this->getTranslationTableName()
                );
            }
            if (Config::useDefaultLanguageAsFallback() &&
                Tools::current_locale() != $silvercartDefaultLocale &&
                !empty ($silvercartDefaultLocale)) {
                $query->addWhere(
                        sprintf(
                                "\"%s\".\"Locale\" = IFNULL((%s), (%s)) %s",
                                $this->getBaseTranslationTableName(),
                                $this->getLocaleDependentSelect(Tools::current_locale(), $query),
                                $this->getLocaleDependentSelect($silvercartDefaultLocale, $query),
                                $addToWhere
                        )
                );
            } elseif (!empty ($silvercartDefaultLocale)) {
                $query->addWhere(
                        sprintf(
                                "\"%s\".\"Locale\" = '%s' %s",
                                $this->getBaseTranslationTableName(),
                                Tools::current_locale(),
                                $addToWhere
                        )
                );
            }
        }
    }
    
    /**
     * Returns a locale dependent select statement (SQL)
     *
     * @param string    $locale Locale to get statement for
     * @param SQLSelect $query  Query to manipulate
     * 
     * @return string
     */
    public function getLocaleDependentSelect($locale, SQLSelect $query) {
        $id    = 0;
        $where = $query->getWhere();
        if (is_array($where)) {
            $stringPart = '"' . $this->getTableName() . '"."ID" = ';
            foreach ($where as $wherePart => $value) {
                if (strpos($wherePart, $stringPart) === 0) {
                    $id = $value;
                }
            }
        }
        if ($id > 0) {
            $relationSelect = sprintf(
                    "SELECT \"%s\".\"ID\" FROM \"%s\" WHERE \"%s\".\"%sID\" = %d",
                    $this->getTranslationTableName(),
                    $this->getTranslationTableName(),
                    $this->getTranslationTableName(),
                    $this->getTableName(),
                    $id
            );
            $localeDependentSelect = sprintf(
                    "SELECT \"%s\".\"Locale\" FROM \"%s\" WHERE \"%s\".\"Locale\" = '%s' AND \"%s\".\"ID\" IN (%s) LIMIT 0,1",
                    $this->getBaseTranslationTableName(),
                    $this->getBaseTranslationTableName(),
                    $this->getBaseTranslationTableName(),
                    $locale,
                    $this->getBaseTranslationTableName(),
                    $relationSelect
            );
        } else {
            $localeDependentSelect = sprintf(
                    "SELECT \"%s\".\"Locale\" FROM \"%s\" WHERE \"%s\".\"Locale\" = '%s' AND \"%s\".\"ID\" = \"%s\".\"%s\" LIMIT 0,1",
                    $this->getBaseTranslationTableName(),
                    $this->getBaseTranslationTableName(),
                    $this->getBaseTranslationTableName(),
                    $locale,
                    $this->getBaseTableName(),
                    $this->getTranslationTableName(),
                    $this->getRelationFieldName()
            );
        }
        return $localeDependentSelect;
    }
    
    /**
     * Returns the current language context field value
     * 
     * @param string $fieldName The name of the field to get out of language context
     *
     * @return string
     * 
     * @deprecated since version 4.0
     */
    public function getLanguageFieldValue($fieldName) {
        Deprecation::notice(
            '4.0',
            'TranslatableDataObjectExtension::getLanguageFieldValue() is deprecated. Use TranslatableDataObjectExtension::getTranslationFieldValue() instead.'
        );
        return $this->getTranslationFieldValue($fieldName);
    }
    
    /**
     * Returns the current translation context field value
     * 
     * @param string $fieldName The name of the field to get out of translation context
     *
     * @return string
     */
    public function getTranslationFieldValue($fieldName) {
        $fieldValue = '';
        $translation = $this->getTranslation();
        if ($translation) {
            $fieldValue = $translation->{$fieldName};
        }
        return $fieldValue;
    }
    
    /**
     * Getter for the related language object depending on the set language
     * 
     * @param bool $force Force the creation of an language object?
     *                    This is needed to scaffold form fields 
     *
     * @return DataObject
     * 
     * @deprecated since version 4.0
     */
    public function getLanguage($force = false) {
        Deprecation::notice(
            '4.0',
            'TranslatableDataObjectExtension::getLanguage() is deprecated. Use TranslatableDataObjectExtension::getTranslation() instead.'
        );
        return $this->getTranslation($force);
    }
    
    /**
     * Getter for the related translation object depending on the set translation
     * 
     * @param bool $force Force the creation of an translation object?
     *                    This is needed to scaffold form fields 
     *
     * @return DataObject
     */
    public function getTranslation($force = false) {
        $relationFieldName    = $this->getRelationFieldName();
        $translationClassName = $this->getTranslationClassName();
        $translationCache     = $this->translationCache;
        $translationCacheKey  = get_class($this->owner) . '-' . $this->owner->ID;
        $translation          = null;
        if (array_key_exists($translationCacheKey, $translationCache)) {
            $translation = $translationCache[$translationCacheKey];
        }
        if (!$this->owner->isInDB()) {
            if ($force) {
                $translation         = new $translationClassName();
                $translation->Locale = Tools::current_locale();
            }
            $this->translationCache[$translationCacheKey] = $translation;
        } elseif (is_null($translation)) {
            $translation = TranslationTools::get_translation($this->getTranslationRelation());
            if (!($translation instanceof $translationClassName) ||
                !$translation->exists()) {
                $translation = new $translationClassName();
                $translation->Locale = Tools::current_locale();
                $translation->{$relationFieldName} = $this->owner->ID;
            }
            $this->translationCache[$translationCacheKey] = $translation;
        }
        return $this->translationCache[$translationCacheKey];
    }
    
    /**
     * Returns the base class name of the owner used for SQL
     *
     * @return string
     */
    public function getBaseClassName() {
        $tableClasses   = ClassInfo::dataClassesFor($this->getClassName());
        $baseClassName  = array_shift($tableClasses);
        return $baseClassName;
    }
    
    /**
     * Returns the base table name of the owner used for SQL
     *
     * @return string
     */
    public function getBaseTableName() {
        return Tools::get_table_name($this->getBaseClassName());
    }


    /**
     * Returns the class name
     *
     * @return string
     */
    public function getClassName() {
        $className = get_class($this->owner);
        return $className;
    }
    
    /**
     * Returns the table name
     *
     * @return string
     */
    public function getTableName() {
        return Tools::get_table_name($this->getClassName());
    }

    /**
     * Returns the language class name
     *
     * @return string
     * 
     * @deprecated since version 4.0
     */
    public function getLanguageClassName() {
        Deprecation::notice(
            '4.0',
            'TranslatableDataObjectExtension::getLanguageClassName() is deprecated. Use TranslatableDataObjectExtension::getTranslationClassName() instead.'
        );
        return $this->getTranslationClassName();
    }

    /**
     * Returns the translation class name
     *
     * @return string
     */
    public function getTranslationClassName() {
        $translationClassName = $this->getClassName() . 'Translation';
        return $translationClassName;
    }
    
    /**
     * Returns the translation table name
     *
     * @return string
     */
    public function getTranslationTableName() {
        return Tools::get_table_name($this->getClassName() . 'Translation');
    }
    
    /**
     * Returns the language base class 
     *
     * @param string $languageClassName Class name to check
     * 
     * @return string
     * 
     * @deprecated since version 4.0
     */
    public function getBaseLanguageClassName($languageClassName = '') {
        Deprecation::notice(
            '4.0',
            'TranslatableDataObjectExtension::getBaseLanguageClassName() is deprecated. Use TranslatableDataObjectExtension::getBaseTranslationClassName() instead.'
        );
        return $this->getBaseTranslationClassName($languageClassName);
    }
    
    /**
     * Returns the translation base class 
     *
     * @param string $translationClassName Class name to check
     * 
     * @return string
     */
    public function getBaseTranslationClassName($translationClassName = '') {
        if (empty($translationClassName)) {
            $translationClassName = $this->getTranslationClassName();
        }
        if (class_exists($translationClassName)) {
            $parents = class_parents($translationClassName);
            if ($parents !== false) {
                $directParent   = array_shift($parents);
                if ($directParent != DataObject::class) {
                    $translationClassName = $this->getBaseTranslationClassName($directParent);
                }
            }
        } else {
            $translationClassName = '';
        }
        return $translationClassName;
    }
    
    /**
     * Returns the translation base table name. 
     *
     * @param string $translationClassName Class name to check
     * 
     * @return string
     */
    public function getBaseTranslationTableName($translationClassName = '') {
        return Tools::get_table_name($this->getBaseTranslationClassName($translationClassName));
    }


    /**
     * Returns the language relation as a ComponentSet
     *
     * @return ComponentSet
     * 
     * @deprecated since version 4.0
     */
    public function getLanguageRelation() {
        Deprecation::notice(
            '4.0',
            'TranslatableDataObjectExtension::getLanguageRelation() is deprecated. Use TranslatableDataObjectExtension::getTranslationRelation() instead.'
        );
        return $this->getTranslationRelation();
    }


    /**
     * Returns the translation relation as a ComponentSet
     *
     * @return HasManyList
     */
    public function getTranslationRelation() {
        return $this->owner->{$this->getTranslationRelationName()}();
    }
    
    /**
     * Returns the language class relation name
     *
     * @return string 
     * 
     * @deprecated since version 4.0
     */
    public function getLanguageRelationName() {
        Deprecation::notice(
            '4.0',
            'TranslatableDataObjectExtension::getLanguageRelationName() is deprecated. Use TranslatableDataObjectExtension::getTranslationRelationName() instead.'
        );
        return $this->getTranslationRelationName();
    }
    
    /**
     * Returns the translation class relation name
     *
     * @return string 
     */
    public function getTranslationRelationName() {
        $reflection = new ReflectionClass($this->getTranslationClassName());
        $translationRelationName = $reflection->getShortName() . 's';
        return $translationRelationName;
    }
    
    /**
     * Returns the translation class relation field name
     *
     * @return string 
     */
    public function getRelationFieldName() {
        $reflection = new ReflectionClass($this->owner);
        $relationFieldName = $reflection->getShortName() . 'ID';
        return $relationFieldName;
    }
    
    /**
     * helper attribute for table fields
     *
     * @return string
     * @deprecated since version 2.0 we do not need this value for the grid field any more.
     */
    public function getTableIndicator() {
        return _t(Config::class . '.OPEN_RECORD', 'open record');
    }
    
    /**
     * Checks whether the given language field is changed.
     * 
     * @param string $fieldName Field name to check change for
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.10.2014
     * @deprecated since version 4.0
     */
    public function languageFieldValueIsChanged($fieldName) {
        Deprecation::notice(
            '4.0',
            'TranslatableDataObjectExtension::languageFieldValueIsChanged() is deprecated. Use TranslatableDataObjectExtension::translationFieldValueIsChanged() instead.'
        );
        return $this->translationFieldValueIsChanged($fieldName);
    }
    
    /**
     * Checks whether the given translation field is changed.
     * 
     * @param string $fieldName Field name to check change for
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.10.2014
     */
    public function translationFieldValueIsChanged($fieldName) {
        $isChanged = false;
        if ($this->owner->isChanged($fieldName)) {
            $changed  = $this->owner->getChangedFields(false, 1);
            $original = $this->owner->getTranslationFieldValue($fieldName);
            $new      = $changed[$fieldName]['after'];
            if ($new != $original) {
                $isChanged = true;
            }
        }
        return $isChanged;
    }
    
    /**
     * Checks whether the translation with the given locale exists
     *
     * @param string $locale Locale to check
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2012
     * @deprecated since version 4.0
     */
    public function hasLanguage($locale) {
        Deprecation::notice(
            '4.0',
            'TranslatableDataObjectExtension::hasLanguage() is deprecated. Use TranslatableDataObjectExtension::hasTranslation() instead.'
        );
        return $this->hasTranslation($locale);
    }
    
    /**
     * Checks whether the translation with the given locale exists
     *
     * @param string $locale Locale to check
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2012
     */
    public function hasTranslation($locale) {
        $hasTranslation = false;
        $translation    = $this->getTranslationFor($locale);
        if ($translation->exists()) {
            $hasTranslation = true;
        }
        return $hasTranslation;
    }

    /**
     * Returns if there's a translation for the current locale.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.12.2012
     * @deprecated since version 4.0
     */
    public function hasCurrentLanguage() {
        Deprecation::notice(
            '4.0',
            'TranslatableDataObjectExtension::hasCurrentLanguage() is deprecated. Use TranslatableDataObjectExtension::hasCurrentTranslation() instead.'
        );
        return $this->hasCurrentTranslation();
    }

    /**
     * Returns if there's a translation for the current locale.
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2017
     */
    public function hasCurrentTranslation() {
        $hasTranslation = false;
        $translation    = $this->getTranslationFor(Tools::current_locale());
        if ($translation->exists()) {
            $hasTranslation = true;
        }
        return $hasTranslation;
    }
    
    /**
     * Returns the language for the given locale if exists
     *
     * @param string $locale Locale to get language for
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2012
     * @deprecated since version 4.0
     */
    public function getLanguageFor($locale) {
        Deprecation::notice(
            '4.0',
            'TranslatableDataObjectExtension::getLanguageFor() is deprecated. Use TranslatableDataObjectExtension::getTranslationFor() instead.'
        );
        return $this->getTranslationFor($locale);
    }
    
    /**
     * Returns the translation for the given locale if exists
     *
     * @param string $locale Locale to get translation for
     * 
     * @return DataObject
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2017
     */
    public function getTranslationFor($locale) {
        $useDefaultTranslationAsFallback = Config::$useDefaultLanguageAsFallback;
        Config::$useDefaultLanguageAsFallback = false;
        $translation = TranslationTools::get_translation($this->getTranslationRelation(), $locale);
        Config::$useDefaultLanguageAsFallback = $useDefaultTranslationAsFallback;
        return $translation;
    }
    
    /**
     * Returns the translations for the extended object.
     * 
     * @return \SilverStripe\ORM\HasManyList
     */
    public function getTranslations() {
        return $this->getTranslationRelation();
    }
    
    /**
     * hook
     *
     * @return void 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 09.02.2017
     */
    public function onBeforeWrite() {
        $translation = $this->getTranslation();
        if ($translation instanceof DataObject &&
            $translation->exists()) {
            if ($this->owner->isInDB()) {
                $relationFieldName = $this->getRelationFieldName();
                $translation->{$relationFieldName} = $this->owner->ID;
            }
            TranslationTools::write_translation_object($this->getTranslation(), $this->owner->toMap());
        }
    }
    
    /**
     * augments the hook of the decorated object so that the input in the fields
     * that are multilingual gets written to the related translation object
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 06.01.2012
     */
    public function onAfterWrite() {
        TranslationTools::write_translation_object($this->getTranslation(), $this->owner->toMap());
    }
    
    /**
     * Deletes some relations
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.05.2012 
     */
    public function onBeforeDelete() {
        parent::onBeforeDelete();
        
        foreach ($this->getTranslationRelation() as $translation) {
            $translation->delete();
        }
    }
    
    /**
     * Sets the property isActive to false.
     * 
     * @param DataObject $original DataObject to add clone for
     * @param boolean    &$doWrite Write clone to database?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function onBeforeDuplicate($original, &$doWrite) {
        $this->owner->isActive = false;
    }
    
    /**
     * Clones the translation data.
     * 
     * @param DataObject $original DataObject to add clone for
     * @param boolean    &$doWrite Write clone to database?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.01.2015
     */
    public function onAfterDuplicate($original, &$doWrite) {
        $translationClassName = $this->getTranslationClassName();
        $emptyTranslation     = $this->owner->getTranslationRelation()->first();
        if ($emptyTranslation instanceof DataObject &&
            $emptyTranslation->exists()) {
            $emptyTranslation->delete();
        }
        foreach ($original->getTranslationRelation() as $translation) {
            $clonedTranslation = new $translationClassName();
            $clonedTranslation->castedUpdate($translation->toMap());
            $clonedTranslation->ID = 0;
            $clonedTranslation->write();
            $this->owner->getTranslationRelation()->add($translation);
        }
    }
    
    /**
     * determin wether all multilingual attributes for all existing translations
     * are empty
     *
     * @return bool $result 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.07.2012
     */
    public function isEmptyMultilingualAttributes() {
        $result = true;
        $translationClassName = $this->getTranslationClassName();
        $multilingualAttributes = $translationClassName::$db;
        foreach ($this->getTranslationRelation() as $translationRelation) {
            foreach ($multilingualAttributes as $key => $value) {
                if ($translationRelation->{$key} !== null) {
                    $result = false;
                    return $result;
                }
            }
        }
        return $result;
    }
}

