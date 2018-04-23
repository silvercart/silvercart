<?php

namespace SilverCart\ORM;

use SilverCart\Model\Translation\TranslationExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;

/**
 * Extension for every DataObject.
 *
 * @package SilverCart
 * @subpackage ORM
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DataObjectCacheExtension extends DataExtension {
    
    /**
     * Determines whether self::onAfterWrite() is in progress to prevent a
     * potential endless loop.
     *
     * @var bool
     */
    protected $onAfterWriteInProgress = false;
    
    /**
     * DB attributes
     *
     * @return array
     */
    private static $db = array(
        'LastEditedForCache' => 'DBDatetime',
    );
    
    /**
     * Removes the LastEditedForCache field.
     * 
     * @param FieldList $fields Fields to update
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.03.2013
     */
    public function updateCMSFields(FieldList $fields) {
        if ($fields->dataFieldByName('LastEditedForCache') instanceof FormField) {
            $fields->removeByName('LastEditedForCache');
        }
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
        if ($this->owner->hasMethod('extendMarkForCacheRefresh')) {
            $this->owner->extendMarkForCacheRefresh();
        }
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
                    if ($this->owner->has_extension($this->owner->ClassName, TranslationExtension::class)) {
                        $relation = DataObject::get_by_id(
                                $this->owner->getRelationClassName(),
                                $this->owner->{$this->owner->getRelationFieldName()}
                        );
                        if ($relation instanceof DataObject &&
                            $relation->exists()) {
                            $this->owner->markForCacheRefresh();
                            $relation->write();
                        }
                    } else {
                        $this->owner->markForCacheRefresh();
                        $this->owner->write();
                    }
                }
            }
            $this->onAfterWriteInProgress = false;
        }
    }
    
}
