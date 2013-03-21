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
 * @subpackage Base
 */

/**
 * Extension for every DataObject
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 29.10.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartDataObject extends DataObjectDecorator {
    
    /**
     * Determines whether self::onAfterWrite() is in progress to prevent a
     * potential endless loop.
     *
     * @var bool
     */
    protected $onAfterWriteInProgress = false;
    
    /**
     * Extra statics
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2013
     */
    public function extraStatics() {
        return array(
            'db' => array(
                'LastEditedForCache'    => 'SS_DateTime',
            ),
        );
    }
    
    /**
     * Removes the LastEditedForCache field.
     * 
     * @param FieldSet &$fields Fields to update
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.03.2013
     */
    public function updateCMSFields(FieldSet &$fields) {
        if ($fields->dataFieldByName('LastEditedForCache') instanceof FormField) {
            $fields->removeByName('LastEditedForCache');
        }
    }
    
    /**
     * Returns a quick preview to use in a related models admin form
     * 
     * @return string
     */
    public function getAdminQuickPreview() {
        return $this->owner->renderWith($this->owner->ClassName . 'AdminQuickPreview');
    }
    
    /**
     * Returns the record as a array map with non escaped values
     * 
     * @param bool $toDisplayWithinHtml Set this to true to replace html special chars with its entities
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.02.2013
     */
    public function toRawMap($toDisplayWithinHtml = false) {
        $record = $this->owner->getAllFields();
        $rawMap = array();
        foreach ($record as $field => $value) {
            if ($toDisplayWithinHtml) {
                $value = htmlspecialchars($value);
            }
            $rawValue = stripslashes($value);
            $rawMap[$field] = $rawValue;
        }
        return $rawMap;
    }
    
    /**
     * Updates LastEditedForCache to the current date and time.
     * This will triger the cache to be rewritten.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.02.2013
     */
    public function markForCacheRefresh() {
        $this->owner->LastEditedForCache = date('Y-m-d H:i:s');
    }

    /**
     * Returns a list of cache relevant fields.
     * 
     * Format:
     * <pre>
     * // manipulates LastEditedForCache in every change case
     * array(
     *     'PropertyName',
     *     'Title',
     *     'Description'
     * );
     * 
     * // manipulates LastEditedForCache only when PropertyName got or had the 'ValueToMatchOrDiffer'
     * array(
     *     'PropertyName' => 'ValueToMatchOrDiffer',
     *     'StockQuantity' => 0
     * );
     * </pre>
     * 
     * @return array
     */
    public function getCacheRelevantFields() {
        $cacheRelevantFields = array();
        $this->owner->extend('updateCacheRelevantFields', $cacheRelevantFields);
        return $cacheRelevantFields;
    }

    /**
     * Changes the LastEditedForCache property if a cache relevant field is 
     * changed.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.03.2013
     */
    public function onAfterWrite() {
        if (!$this->onAfterWriteInProgress) {
            $this->onAfterWriteInProgress   = true;
            if ($this->owner->hasMethod('getCacheRelevantFields')) {
                $cacheRelevantFieldIsChanged    = false;
                $cacheRelevantFields            = $this->owner->getCacheRelevantFields();
                foreach ($cacheRelevantFields as $cacheRelevantFieldIndex => $cacheRelevantFieldValue) {
                    if (is_numeric($cacheRelevantFieldIndex)) {
                        $cacheRelevantFieldIsChanged = $this->owner->isChanged($cacheRelevantFieldValue, 2);
                    } else {
                        if ($this->owner->isChanged($cacheRelevantFieldIndex, 2)) {
                            $changedFields = $this->owner->getChangedFields();
                            if ($changedFields[$cacheRelevantFieldIndex]['before'] != $changedFields[$cacheRelevantFieldIndex]['after'] &&
                                ($this->owner->{$cacheRelevantFieldIndex} == $cacheRelevantFieldValue ||
                                 $changedFields[$cacheRelevantFieldIndex]['before'] == $cacheRelevantFieldValue)) {
                                $cacheRelevantFieldIsChanged = true;
                            }
                        }
                    }
                    if ($cacheRelevantFieldIsChanged) {
                        break;
                    }
                }

                if ($cacheRelevantFieldIsChanged) {
                    if ($this->owner->has_extension($this->owner->ClassName, 'SilvercartLanguageDecorator')) {
                        $relation = DataObject::get_by_id(
                                $this->owner->getRelationClassName(),
                                $this->owner->{$this->owner->getRelationFieldName()}
                        );
                        if ($relation) {
                            $this->owner->markForCacheRefresh();
                            $relation->write();
                        }
                    } else {
                        $this->owner->markForCacheRefresh();
                        $this->owner->write();
                    }
                }
                $this->onAfterWriteInProgress = false;
            }
        }
    }
}