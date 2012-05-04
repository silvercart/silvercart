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
    
    public static $defaults = array(
        'SilvercartVersion'         => '1.3',
        'SilvercartUpdateVersion'   => '1',
        'Description'               => 'This update adjust all multilingual objects to the new multilingual feature.'
    );


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
        $locale             = Translatable::get_current_locale();
        $fields             = array();
        $db                 = Object::get_static($className . 'Language', 'db');
        $attributes         = array();
        foreach ($db as $fieldName => $fieldType) {
            if ($fieldName == 'Locale') {
                continue;
            }
        
            $query = DB::query(
                sprintf(
                    "SELECT COUNT(*) AS ColumnCount FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = '%s' AND COLUMN_NAME = '%s'",
                    DB::getConn()->currentDatabase(),
                    $className,
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
        
        $selectAttributes   = array_merge(
            array(
                'ID'
            ),
            $attributes
        );
        foreach ($selectAttributes as $attribute) {
            $fields[] = sprintf(
                "`%s`.`%s`",
                $className,
                $attribute
            );
        }
        $fieldsAsString = implode(',', $fields);
        $query = DB::query(
            sprintf(
                "SELECT %s FROM %s",
                $fieldsAsString,
                $className
            )
        );
        if ($query) {
            foreach ($query as $result) {
                $object = DataObject::get_by_id($className, $result['ID']);
                if ($object) {
                    $languageClassName = $object->getLanguageClassName();
                    if (!$object->hasLanguage($locale)) {
                        $languageClass          = new $languageClassName();
                        $languageClass->Locale  = $locale;
                        foreach ($result as $fieldName => $fieldValue) {
                            $languageClass->{$fieldName} = $fieldValue;
                        }
                        $languageClass->write();
                        $object->getLanguageRelation()->add($languageClass);
                    }
                }
            }
        }
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

