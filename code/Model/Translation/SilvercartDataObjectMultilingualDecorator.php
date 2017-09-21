<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Translation
 */

/**
 * decorates DataObjects to make them multilingual eg SilvercartProduct
 *
 * @package Silvercart
 * @subpackage Translation
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 06.01.2012
 * @license see license file in modules root directory
 */
class SilvercartDataObjectMultilingualDecorator extends DataExtension {
    
    /**
     * The language object
     *
     * @var Object
     */
    protected $languageObj = null;
    
    /**
     * Manipulates the SQL query
     *
     * @param SQLQuery &$query Query to manipulate
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.05.2012
     */
    public function augmentSQL(SQLQuery &$query) {
        if (!$query->isJoinedTo($this->getLanguageClassName()) &&
            !$query->getDelete()) {
            $silvercartDefaultLocale = SilvercartConfig::Locale();
            $query->addLeftJoin(
                    $this->getLanguageClassName(),
                    sprintf(
                            "(\"%s\".\"ID\" = \"%s\".\"%s\")",
                            $this->getBaseClassName(),
                            $this->getLanguageClassName(),
                            $this->getRelationFieldName()
                    )
            );
            $addToWhere = '';
            if ($this->getBaseLanguageClassName() != $this->getLanguageClassName()) {
                $query->addLeftJoin(
                        $this->getBaseLanguageClassName(),
                        sprintf(
                                "(\"%s\".\"ID\" = \"%s\".\"ID\")",
                                $this->getLanguageClassName(),
                                $this->getBaseLanguageClassName()
                        )
                );
                $addToWhere = sprintf(
                        "AND \"%s\".\"ID\" = \"%s\".\"ID\"",
                        $this->getBaseLanguageClassName(),
                        $this->getLanguageClassName()
                );
            }
            if (SilvercartConfig::useDefaultLanguageAsFallback() &&
                Translatable::get_current_locale() != $silvercartDefaultLocale &&
                !empty ($silvercartDefaultLocale)) {
                $query->addWhere(
                        sprintf(
                                "\"%s\".\"Locale\" = IFNULL((%s), (%s)) %s",
                                $this->getBaseLanguageClassName(),
                                $this->getLocaleDependantSelect(Translatable::get_current_locale(), $query),
                                $this->getLocaleDependantSelect($silvercartDefaultLocale, $query),
                                $addToWhere
                        )
                );
            } elseif (!empty ($silvercartDefaultLocale)) {
                $query->addWhere(
                        sprintf(
                                "\"%s\".\"Locale\" = '%s' %s",
                                $this->getBaseLanguageClassName(),
                                Translatable::get_current_locale(),
                                $addToWhere
                        )
                );
            }
        }
    }
    
    /**
     * Returns a locale dependant select statement (SQL)
     *
     * @param string $locale Locale to get statement for
     * 
     * @return string
     */
    public function getLocaleDependantSelect($locale, SQLQuery $query) {
        $id    = 0;
        $where = $query->getWhere();
        if (is_array($where)) {
            $stringPart = '"' . $this->getClassName() . '"."ID" = ';
            foreach ($where as $wherePart) {
                if (strpos($wherePart, $stringPart) === 0) {
                    $id = (int) trim(substr($wherePart, strlen($stringPart)));
                }
            }
        }
        if ($id > 0) {
            $relationSelect = sprintf(
                    "SELECT \"%s\".\"ID\" FROM \"%s\" WHERE \"%s\".\"%sID\" = %d",
                    $this->getLanguageClassName(),
                    $this->getLanguageClassName(),
                    $this->getLanguageClassName(),
                    $this->getClassName(),
                    $id
            );
            $localeDependantSelect = sprintf(
                    "SELECT \"%s\".\"Locale\" FROM \"%s\" WHERE \"%s\".\"Locale\" = '%s' AND \"%s\".\"ID\" IN (%s) LIMIT 0,1",
                    $this->getBaseLanguageClassName(),
                    $this->getBaseLanguageClassName(),
                    $this->getBaseLanguageClassName(),
                    $locale,
                    $this->getBaseLanguageClassName(),
                    $relationSelect
            );
        } else {
            $localeDependantSelect = sprintf(
                    "SELECT \"%s\".\"Locale\" FROM \"%s\" WHERE \"%s\".\"Locale\" = '%s' AND \"%s\".\"ID\" = \"%s\".\"%s\" LIMIT 0,1",
                    $this->getBaseLanguageClassName(),
                    $this->getBaseLanguageClassName(),
                    $this->getBaseLanguageClassName(),
                    $locale,
                    $this->getBaseClassName(),
                    $this->getLanguageClassName(),
                    $this->getRelationFieldName()
            );
        }
        return $localeDependantSelect;
    }
    
    /**
     * Returns the current language context field value
     * 
     * @param string $fieldName The name of the field to get out of language context
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.05.2012
     */
    public function getLanguageFieldValue($fieldName) {
        $fieldValue = '';
        if ($this->getLanguage()) {
            $fieldValue = $this->getLanguage()->{$fieldName};
        }
        return $fieldValue;
    }
    
    /**
     * Getter for the related language object depending on the set language
     * 
     * @param bool $force Force the creation of an language object?
     *                    This is needed to scaffold form fields 
     *
     * @return Language object for the decorated class, by convention class name + 'Language';
     *         always returns an object
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.05.2012
     */
    public function getLanguage($force = false) {
        $relationFieldName      = $this->getRelationFieldName();
        $languageClassName      = $this->getLanguageClassName();
        if (!$this->owner->isInDB()) {
            if ($force) {
                $this->languageObj          = new $languageClassName();
                $this->languageObj->Locale  = Translatable::get_current_locale();
            } else {
                $this->languageObj = null;
                
            }
        } elseif (is_null($this->languageObj)) {
            $this->languageObj = SilvercartLanguageHelper::getLanguage($this->getLanguageRelation());
            if (!$this->languageObj) {
                $this->languageObj = new $languageClassName();
                $this->languageObj->Locale = Translatable::get_current_locale();
                $this->languageObj->{$relationFieldName} = $this->owner->ID;
            }
        }
        return $this->languageObj;
    }
    
    /**
     * Returns the base class name of the owner used for SQL
     *
     * @return string
     */
    public function getBaseClassName() {
        $tableClasses   = ClassInfo::dataClassesFor($this->owner->class);
        $baseClassName  = array_shift($tableClasses);
        return $baseClassName;
    }


    /**
     * Returns the class name
     *
     * @return string
     */
    public function getClassName() {
        $className = $this->owner->class;
        return $className;
    }

    /**
     * Returns the language class name
     *
     * @return string
     */
    public function getLanguageClassName() {
        $languageClassName = $this->owner->class . 'Language';
        return $languageClassName;
    }
    
    /**
     * Returns the language base class 
     *
     * @param string $languageClassName Class name to check
     * 
     * @return string
     */
    public function getBaseLanguageClassName($languageClassName = '') {
        if (empty($languageClassName)) {
            $languageClassName = $this->getLanguageClassName();
        }
        $parents = class_parents($languageClassName);
        if ($parents !== false) {
            $directParent   = array_shift($parents);
            if ($directParent != 'DataObject') {
                $languageClassName = $this->getBaseLanguageClassName($directParent);
            }
        }
        return $languageClassName;
    }


    /**
     * Returns the language relation as a ComponentSet
     *
     * @return ComponentSet
     */
    public function getLanguageRelation() {
        return $this->owner->{$this->getLanguageRelationName()}();
    }
    
    /**
     * Returns the language class relation name
     *
     * @return string 
     */
    public function getLanguageRelationName() {
        $languageRelationName   = $this->getLanguageClassName() . 's';
        return $languageRelationName;
    }
    
    /**
     * Returns the language class relation field name
     *
     * @return string 
     */
    public function getRelationFieldName() {
        $relationFieldName = $this->owner->class . 'ID';
        return $relationFieldName;
    }
    
    /**
     * helper attribute for table fields
     *
     * @return string
     * @deprecated since version 2.0 we do not need this value for the grid field any more.
     */
    public function getTableIndicator() {
        return _t('SilvercartConfig.OPEN_RECORD');
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
     */
    public function languageFieldValueIsChanged($fieldName) {
        $isChanged = false;
        if ($this->owner->isChanged($fieldName)) {
            $changed  = $this->owner->getChangedFields(false, 1);
            $original = $this->owner->getLanguageFieldValue($fieldName);
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
    public function hasLanguage($locale) {
        $hasLanguage    = false;
        $language       = $this->getLanguageFor($locale);
        if ($language) {
            $hasLanguage = true;
        }
        return $hasLanguage;
    }

    /**
     * Returns if there's a translation for the current locale.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.12.2012
     */
    public function hasCurrentLanguage() {
        $hasLanguage    = false;
        $language       = $this->getLanguageFor(Translatable::get_current_locale());
        if ($language) {
            $hasLanguage = true;
        }
        return $hasLanguage;
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
     */
    public function getLanguageFor($locale) {
        $useDefaultLanguageAsFallback                   = SilvercartConfig::$useDefaultLanguageAsFallback;
        SilvercartConfig::$useDefaultLanguageAsFallback = false;
        $language                                       = SilvercartLanguageHelper::getLanguage($this->getLanguageRelation(), $locale);
        SilvercartConfig::$useDefaultLanguageAsFallback = $useDefaultLanguageAsFallback;
        return $language;
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
        if ($this->owner->isInDB() && !is_null($this->languageObj)) {
            $relationFieldName = $this->getRelationFieldName();
            $this->languageObj->{$relationFieldName} = $this->owner->ID;
        }
        $language = $this->getLanguage();
        if ($language instanceof DataObject &&
            $language->exists()) {
            SilvercartLanguageHelper::writeLanguageObject($this->getLanguage(), $this->owner->toMap());
        }
    }
    
    /**
     * augments the hook of the decorated object so that the input in the fields
     * that are multilingual gets written to the related language object
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 06.01.2012
     */
    public function onAfterWrite() {
        SilvercartLanguageHelper::writeLanguageObject($this->getLanguage(), $this->owner->toMap());
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
        
        foreach ($this->getLanguageRelation() as $language) {
            $language->delete();
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
        $languageClassName = $this->getLanguageClassName();
        $emptyLanguage     = $this->owner->getLanguageRelation()->first();
        if ($emptyLanguage instanceof DataObject &&
            $emptyLanguage->exists()) {
            $emptyLanguage->delete();
        }
        foreach ($original->getLanguageRelation() as $language) {
            $clonedLanguage = new $languageClassName();
            $clonedLanguage->castedUpdate($language->toMap());
            $clonedLanguage->ID = 0;
            $clonedLanguage->write();
            $this->owner->getLanguageRelation()->add($language);
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
        $languageClassName = $this->getLanguageClassName();
        $multilingualAttributes = $languageClassName::$db;
        foreach ($this->getLanguageRelation() as $languageObj) {
            foreach ($multilingualAttributes as $key => $value) {
                if ($languageObj->{$key} !== null) {
                    $result = false;
                    return $result;
                }
            }
        }
        return $result;
    }
}

