<?php

namespace SilverCart\Model\Translation;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Model\Translation\TranslationTools;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\HasManyList;
use SilverStripe\ORM\Queries\SQLSelect;
use SilverStripe\Versioned\ReadingMode;
use SilverStripe\Versioned\Versioned;
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
 * 
 * @property DataObject $owner Owner
 */
class TranslatableDataObjectExtension extends DataExtension
{
    /**
     * The translation object list
     *
     * @var array
     */
    protected $translationCache = [];
    
    /**
     * Updates the CMS fields.
     * 
     * @param FieldList $fields Fields
     * 
     * @return void
     */
    public function updateCMSFields(FieldList $fields) : void
    {
        $insertFields = (bool) $this->owner->config()->insert_translation_cms_fields;
        if (!$insertFields) {
            return;
        }
        $insertBefore = $this->owner->config()->insert_translation_cms_fields_before;
        $insertAfter  = $this->owner->config()->insert_translation_cms_fields_after;
        $languageFields = TranslationTools::prepare_cms_fields($this->owner->getTranslationClassName());
        foreach ($languageFields as $languageField) {
            /* @var $languageField \SilverStripe\Forms\FormField */
            if ($insertBefore === null
             && $insertAfter === null
            ) {
                $fields->addFieldToTab('Root.Main', $languageField);
            } elseif ($insertBefore !== null) {
                $fields->insertBefore($languageField, $insertBefore);
            } else {
                $fields->insertAfter($languageField, $insertAfter);
                /*
                 * Change the name of the field the insert the next field
                 * Otherwise the sort order would be inverted
                 */
                $insertAfter = $languageField->getName();
            }
            foreach (['Desc' => 'Description', 'RightTitle'] as $label => $setter) {
                if (is_numeric($label)) {
                    $label = $setter;
                }
                $labelKey   = "{$languageField->getName()}{$label}";
                $fieldLabel = $this->owner->fieldLabel($labelKey);
                if ($fieldLabel !== FormField::name_to_label($labelKey)) {
                    $languageField->{"set{$setter}"}($fieldLabel);
                }
            }
        }
    }
    
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
    public function augmentSQL(SQLSelect $query, DataQuery $dataQuery = null) : void
    {
        $translationTableName = $this->getTranslationTableName();
        if (!$query->isJoinedTo($translationTableName)) {
            $tableName                = $this->getTableName();
            $baseTableName            = $this->getBaseTableName();
            $baseTranslationTableName = $this->getBaseTranslationTableName();
            $relationFieldName        = $this->getRelationFieldName();
            $currentLocale            = Tools::current_locale();
            $silvercartDefaultLocale  = Config::Locale();
            if ($this->owner->hasExtension(Versioned::class)) {
                $versionedMode  = $dataQuery->getQueryParam('Versioned.mode');
                $versionedStage = $dataQuery->getQueryParam('Versioned.stage');
                ReadingMode::validateStage($versionedStage);
                if (in_array($versionedMode, ['archive', 'latest_versions', 'version', 'all_versions'])) {
                    $baseTableName = "{$baseTableName}_Versions";
                } elseif ($versionedStage === Versioned::LIVE) {
                    $baseTableName = "{$baseTableName}_Live";
                }
            }
            $query->addLeftJoin(
                    $translationTableName,
                    "({$baseTableName}.ID = {$translationTableName}.{$relationFieldName})"
            );
            $addToWhere = '';
            if ($baseTranslationTableName != $translationTableName) {
                $query->addLeftJoin(
                        $baseTranslationTableName,
                        "({$translationTableName}.ID = {$baseTranslationTableName}.ID)"
                );
                $addToWhere = "AND {$baseTranslationTableName}.ID = {$translationTableName}.ID";
            }
            if (Config::useDefaultLanguageAsFallback()
             && $currentLocale != $silvercartDefaultLocale
             && !empty($silvercartDefaultLocale)
            ) {
                $currentLocaleSelect = $this->getLocaleDependentSelect($currentLocale, $query);
                $defaultLocaleSelect = $this->getLocaleDependentSelect($silvercartDefaultLocale, $query);
                $query->addWhere("{$baseTranslationTableName}.Locale = IFNULL(({$currentLocaleSelect}), ({$defaultLocaleSelect})) {$addToWhere}");
            } elseif (!empty($silvercartDefaultLocale)) {
                $query->addWhere("{$baseTranslationTableName}.Locale = '{$currentLocale}' {$addToWhere}");
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
    public function getLocaleDependentSelect(string $locale, SQLSelect $query) : string
    {
        $id                       = 0;
        $where                    = $query->getWhere();
        $translationTableName     = $this->getTranslationTableName();
        $tableName                = $this->getTableName();
        $baseTableName            = $this->getBaseTableName();
        $baseTranslationTableName = $this->getBaseTranslationTableName();
        $relationFieldName        = $this->getRelationFieldName();
        if (is_array($where)) {
            $stringPart = "{$tableName}.ID = ";
            foreach ($where as $wherePart => $value) {
                if (strpos($wherePart, $stringPart) === 0) {
                    $id = $value;
                }
            }
        }
        if ($id > 0) {
            $relationSelect        = "SELECT \"{$translationTableName}\".\"ID\" FROM \"{$translationTableName}\" WHERE \"{$translationTableName}\".\"{$tableName}ID\" = {$id}";
            $localeDependentSelect = "SELECT \"{$baseTranslationTableName}\".\"Locale\" FROM \"{$baseTranslationTableName}\" WHERE \"{$baseTranslationTableName}\".\"Locale\" = '{$locale}' AND \"{$baseTranslationTableName}\".\"ID\" IN ({$relationSelect}) LIMIT 0,1";
        } else {
            $localeDependentSelect = "SELECT \"{$baseTranslationTableName}\".\"Locale\" FROM \"{$baseTranslationTableName}\" WHERE \"{$baseTranslationTableName}\".\"Locale\" = '{$locale}' AND \"{$baseTableName}\".\"ID\" = \"{$translationTableName}\".\"{$relationFieldName}\" LIMIT 0,1";
        }
        return $localeDependentSelect;
    }
    
    /**
     * Returns the current translation context field value
     * 
     * @param string $fieldName The name of the field to get out of translation context
     *
     * @return string
     */
    public function getTranslationFieldValue(string $fieldName) : ?string
    {
        $fieldValue = '';
        $translation = $this->getTranslation();
        if ($translation) {
            $fieldValue = $translation->{$fieldName};
        }
        return $fieldValue;
    }
    
    /**
     * Getter for the related translation object depending on the set translation
     * 
     * @param bool $force Force the creation of an translation object?
     *                    This is needed to scaffold form fields 
     *
     * @return DataObject
     */
    public function getTranslation(bool $force = false)
    {
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
            if (!($translation instanceof $translationClassName)
             || !$translation->exists()
            ) {
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
    public function getBaseClassName() : string
    {
        $tableClasses   = ClassInfo::dataClassesFor($this->getClassName());
        $baseClassName  = array_shift($tableClasses);
        return $baseClassName;
    }
    
    /**
     * Returns the base table name of the owner used for SQL
     *
     * @return string
     */
    public function getBaseTableName() : string
    {
        return Tools::get_table_name($this->getBaseClassName());
    }


    /**
     * Returns the class name
     *
     * @return string
     */
    public function getClassName() : string
    {
        $className = get_class($this->owner);
        return $className;
    }
    
    /**
     * Returns the table name
     *
     * @return string
     */
    public function getTableName() : string
    {
        return Tools::get_table_name($this->getClassName());
    }

    /**
     * Returns the translation class name
     *
     * @return string
     */
    public function getTranslationClassName() : string
    {
        $className = "{$this->getClassName()}Translation";
        if (!class_exists($className)) {
            $currentObject = $this->owner;
            while (!class_exists($className)) {
                $parentClass   = get_parent_class($currentObject);
                $className     = "{$parentClass}Translation";
                $currentObject = singleton($parentClass);
            }
        }
        return $className;
    }
    
    /**
     * Returns the translation table name
     *
     * @return string
     */
    public function getTranslationTableName() : string
    {
        $className = $this->getTranslationClassName();
        return Tools::get_table_name($className);
    }
    
    /**
     * Returns the translation base class 
     *
     * @param string $translationClassName Class name to check
     * 
     * @return string
     */
    public function getBaseTranslationClassName(string $translationClassName = '') : string
    {
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
    public function getBaseTranslationTableName(string $translationClassName = '') : string
    {
        return Tools::get_table_name($this->getBaseTranslationClassName($translationClassName));
    }


    /**
     * Returns the translation relation as a ComponentSet
     *
     * @return HasManyList
     */
    public function getTranslationRelation() : HasManyList
    {
        return $this->owner->{$this->getTranslationRelationName()}();
    }
    
    /**
     * Returns the translation class relation name
     *
     * @return string 
     */
    public function getTranslationRelationName() : string
    {
        $reflection = new ReflectionClass($this->getTranslationClassName());
        $translationRelationName = "{$reflection->getShortName()}s";
        return $translationRelationName;
    }
    
    /**
     * Returns the translation class relation field name
     *
     * @return string 
     */
    public function getRelationFieldName() : string
    {
        $className     = "{$this->getClassName()}Translation";
        $currentObject = $this->owner;
        if (!class_exists($className)) {
            while (!class_exists($className)) {
                $parentClass   = get_parent_class($currentObject);
                $className     = "{$parentClass}Translation";
                $currentObject = singleton($parentClass);
            }
        }
        $reflection        = new ReflectionClass($currentObject);
        $relationFieldName = "{$reflection->getShortName()}ID";
        return $relationFieldName;
    }
    
    /**
     * Checks whether the given translation field is changed.
     * 
     * @param string $fieldName Field name to check change for
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.10.2014
     */
    public function translationFieldValueIsChanged($fieldName) : bool
    {
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
     */
    public function hasTranslation($locale) : bool
    {
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
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2017
     */
    public function hasCurrentTranslation() : bool
    {
        $hasTranslation = false;
        $translation    = $this->getTranslationFor(Tools::current_locale());
        if ($translation->exists()) {
            $hasTranslation = true;
        }
        return $hasTranslation;
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
    public function getTranslationFor($locale)
    {
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
    public function getTranslations() : HasManyList
    {
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
    public function onBeforeWrite() : void
    {
        $translation = $this->getTranslation();
        if ($translation instanceof DataObject
         && $translation->exists()
        ) {
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
    public function onAfterWrite() : void
    {
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
    public function onBeforeDelete() : void
    {
        foreach ($this->getTranslationRelation() as $translation) {
            $translation->delete();
        }
    }
    
    /**
     * Sets the property isActive to false.
     * 
     * @param DataObject $original DataObject to add clone for
     * @param bool       &$doWrite Write clone to database?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function onBeforeDuplicate(DataObject $original, bool &$doWrite) : void
    {
        $this->owner->isActive = false;
    }
    
    /**
     * Clones the translation data.
     * 
     * @param DataObject $original DataObject to add clone for
     * @param bool       &$doWrite Write clone to database?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.01.2015
     */
    public function onAfterDuplicate(DataObject $original, bool &$doWrite) : void
    {
        $translationClassName = $this->getTranslationClassName();
        $emptyTranslation     = $this->owner->getTranslationRelation()->first();
        if ($emptyTranslation instanceof DataObject
         && $emptyTranslation->exists()
        ) {
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
     * @return bool 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.07.2012
     */
    public function isEmptyMultilingualAttributes() : bool
    {
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