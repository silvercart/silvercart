<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
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
 * @copyright Pixeltricks GmbH
 * @since 06.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartDataObjectMultilingualDecorator extends DataExtension {
    
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
                            "(`%s`.`ID` = `%s`.`%s`)",
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
                                "(`%s`.`ID` = `%s`.`ID`)",
                                $this->getLanguageClassName(),
                                $this->getBaseLanguageClassName()
                        )
                );
                $addToWhere = sprintf(
                        "AND `%s`.`ID` = `%s`.`ID`",
                        $this->getBaseLanguageClassName(),
                        $this->getLanguageClassName()
                );
            }
            if (SilvercartConfig::useDefaultLanguageAsFallback() &&
                Translatable::get_current_locale() != $silvercartDefaultLocale &&
                !empty ($silvercartDefaultLocale)) {
                $query->where(
                        sprintf(
                                "`%s`.`Locale` = IFNULL((%s), (%s)) %s",
                                $this->getBaseLanguageClassName(),
                                $this->getLocaleDependantSelect(Translatable::get_current_locale()),
                                $this->getLocaleDependantSelect($silvercartDefaultLocale),
                                $addToWhere
                        )
                );
            } elseif (!empty ($silvercartDefaultLocale)) {
                $query->addWhere(
                        sprintf(
                                "`%s`.`Locale` = '%s' %s",
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
    public function getLocaleDependantSelect($locale) {
        $localeDependantSelect = sprintf(
                "SELECT `%s`.`Locale` FROM `%s` WHERE `%s`.`Locale` = '%s' AND `%s`.`ID` = `%s`.`%s` LIMIT 0,1",
                $this->getBaseLanguageClassName(),
                $this->getBaseLanguageClassName(),
                $this->getBaseLanguageClassName(),
                $locale,
                $this->getBaseClassName(),
                $this->getLanguageClassName(),
                $this->getRelationFieldName()
        );
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
     *
     * @return Language object for the decorated class, by convention class name + 'Language';
     *         always returns an object
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.05.2012
     */
    public function getLanguage($force = false) {
        if ($this->owner->ID === 0) {
            if ($force) {
                $languageClassName          = $this->getLanguageClassName();
                $this->languageObj          = new $languageClassName();
                $this->languageObj->Locale  = Translatable::get_current_locale();
            } else {
                $this->languageObj = null;
            }
        } elseif (is_null($this->languageObj)) {
            $relationFieldName      = $this->getRelationFieldName();
            $languageClassName      = $this->getLanguageClassName();
            $languageRelationName   = $this->getLanguageRelationName();
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
     */
    public function getTableIndicator() {
        return _t('SilvercartConfig.OPEN_RECORD');
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

