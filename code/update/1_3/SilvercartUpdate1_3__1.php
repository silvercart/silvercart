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
 * @subpackage Update
 */

/**
 * Prepares objects for the new multilingual feature
 *
 * @package Silvercart
 * @subpackage Update
 * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
 * @since 04.05.2012
 * @copyright pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate1_3__1 extends SilvercartUpdate {
    
    /**
     * Default properties
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion'         => '1.3',
        'SilvercartUpdateVersion'   => '1',
        'Description'               => 'This update adjust all multilingual objects to the new multilingual feature.'
    );
    
    /**
     * List of classes to clean after processing main update
     *
     * @var array
     */
    protected $postCleaningClasses = array();

    /**
     * Executes the update logic.
     *
     * @return boolean true
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.05.2012
     */
    public function executeUpdate() {
        foreach (SilvercartLanguageHelper::getTranslatableDataObjects() as $translatableDataObject) {
            $this->updateMultilingualObject($translatableDataObject);
        }
        foreach ($this->postCleaningClasses as $postCleaningClass) {
            $this->cleanDatabaseTable($postCleaningClass['className'], $postCleaningClass['attributes']);
        }
        
        $silvercartDefaultLocale = SilvercartConfig::Locale();
        if (empty($silvercartDefaultLocale)) {
            $config = SilvercartConfig::getConfig();
            $config->Locale = Translatable::get_current_locale();
            $config->write();
        }
        return true;
    }
    
    /**
     * encapsulate all updates regarding the multilingual feature
     * 
     * @param string $className The class to update
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.05.2012
     */
    public function updateMultilingualObject($className) {
        $this->cliOutput("Executing " . $className);
        $object         = singleton($className);
        $langClassName  = $object->getLanguageClassName();
        $langObject     = new $langClassName();
        $baseClass      = $object->getBaseClassName();
        $locale         = Translatable::get_current_locale();
        $fields         = array();
        $db             = $langObject->db();
        $attributes     = array();
        
        if ($className != $baseClass &&
            $baseClass != 'Widget') {
            $baseClassName = $baseClass;
        } else {
            $baseClassName = $className;
        }
        
        foreach ($db as $fieldName => $fieldType) {
            if ($fieldName == 'Locale') {
                continue;
            }
        
            $query = DB::query(
                sprintf(
                    "SELECT COUNT(*) AS ColumnCount FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = '%s' AND COLUMN_NAME = '%s'",
                    DB::getConn()->currentDatabase(),
                    $baseClassName,
                    $fieldName
                )
            );
            if ($query) {
                foreach ($query as $result) {
                    if ($result['ColumnCount'] > 0) {
                        $attributes[] = $fieldName;
                        break;
                    }
                }
            }
        }
        
        if (!empty($attributes)) {
            $this->cliOutput("found columns to update, going on...", 3);
            $selectAttributes   = array_merge(
                array(
                    'ID'
                ),
                $attributes
            );
            foreach ($selectAttributes as $attribute) {
                $fields[] = sprintf(
                    "`%s`.`%s`",
                    $baseClassName,
                    $attribute
                );
            }
            $fieldsAsString = implode(',', $fields);
            if ($className != $baseClass &&
                $baseClass != 'Widget') {
                $query = DB::query(
                    sprintf(
                        "SELECT %s FROM %s WHERE ClassName = '%s'",
                        $fieldsAsString,
                        $baseClassName,
                        $className
                    )
                );
            } else {
                $query = DB::query(
                    sprintf(
                        "SELECT %s FROM %s",
                        $fieldsAsString,
                        $baseClassName
                    )
                );
            }
            if ($query) {
                $count = 0;
                foreach ($query as $result) {
                    $count++;
                    $object = DataObject::get_by_id($className, $result['ID']);

                    if ($object) {
                        $languageClassName = $object->getLanguageClassName();
                        if (!$object->hasLanguage($locale)) {
                            $languageClass          = new $languageClassName();
                            $languageClass->Locale  = $locale;
                            foreach ($result as $fieldName => $fieldValue) {
                                if ($fieldName == 'ID') {
                                    continue;
                                }
                                $languageClass->{$fieldName} = $fieldValue;
                            }
                            $languageClass->write();
                            $object->getLanguageRelation()->add($languageClass);
                        }
                    }
                }
                $this->cliOutput("updated " . $count . " entries", 3);
            }

            if ($className == $baseClass ||
                $baseClass == 'Widget' &&
                !array_key_exists($baseClass, $this->postCleaningClasses)) {
                $this->cleanDatabaseTable($className, $attributes);
            } else {
                if (!array_key_exists($baseClass, $this->postCleaningClasses)) {
                    $this->postCleaningClasses[$baseClass] = array(
                        'className'     => $baseClass,
                        'attributes'    => $attributes,
                    );
                }
            }
        } else {
            $this->cliOutput("skipped, no more columns to update found", 3);
        }
    }
    
    /**
     * Removes all columns defined in $attributes out of the table $className
     *
     * @param string $className  Name of class/table to clean
     * @param array  $attributes Attributes/columns to remove
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.05.2012
     */
    public function cleanDatabaseTable($className, $attributes) {
        foreach ($attributes as $attribute) {
            DB::query(
                sprintf(
                    "ALTER TABLE %s DROP %s",
                    $className,
                    $attribute
                )
            );
        }
    }
}

