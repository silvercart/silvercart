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
     * @var bool[]
     */
    protected $onAfterWriteInProgress = [];
    
    /**
     * Can be set to true by each DataObject to prevent a cache refresh.
     *
     * @var boolean[]
     */
    protected $skipCacheRefresh = [];
    
    /**
     * Can be set to true by each DataObject to prevent a cache refresh.
     *
     * @var boolean[]
     */
    protected $skipExtendedCacheRefresh = [];
    
    /**
     * DB attributes
     *
     * @return array
     */
    private static $db = [
        'LastEditedForCache' => 'DBDatetime',
    ];
    
    /**
     * Returns the skip cache refresh setting.
     * 
     * @return boolean
     */
    public function getSkipCacheRefresh() {
        if (!array_key_exists($this->owner->ID, $this->skipCacheRefresh)) {
            $this->setSkipCacheRefresh(false);
        }
        return $this->skipCacheRefresh[$this->owner->ID];
    }

    /**
     * Sets the skip cache refresh setting.
     * 
     * @param boolean $skipCacheRefresh Skip cache refresh setting
     * 
     * @return $this->owner
     */
    public function setSkipCacheRefresh($skipCacheRefresh) {
        $this->skipCacheRefresh[$this->owner->ID] = $skipCacheRefresh;
        return $this->owner;
    }
    
    /**
     * Returns the skip cache refresh setting.
     * Alias for {@link $this->getSkipCacheRefresh()}.
     * 
     * @return boolean
     */
    public function SkipCacheRefresh() {
        return $this->getSkipCacheRefresh();
    }
    
    /**
     * Returns the skip extended cache refresh setting.
     * 
     * @return boolean
     */
    public function getSkipExtendedCacheRefresh() {
        if (!array_key_exists($this->owner->ID, $this->skipExtendedCacheRefresh)) {
            $this->setSkipExtendedCacheRefresh(false);
        }
        return $this->skipExtendedCacheRefresh[$this->owner->ID];
    }

    /**
     * Sets the skip extended cache refresh setting.
     * 
     * @param boolean $skipExtendedCacheRefresh Skip extended cache refresh setting
     * 
     * @return $this->owner
     */
    public function setSkipExtendedCacheRefresh($skipExtendedCacheRefresh) {
        $this->skipExtendedCacheRefresh[$this->owner->ID] = $skipExtendedCacheRefresh;
        return $this->owner;
    }
    
    /**
     * Returns the skip extended cache refresh setting.
     * Alias for {@link $this->getSkipExtendedCacheRefresh()}.
     * 
     * @return boolean
     */
    public function SkipExtendedCacheRefresh() {
        return $this->getSkipExtendedCacheRefresh();
    }
    
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
     * @since 14.06.2018
     */
    public function markForCacheRefresh() {
        if ($this->SkipCacheRefresh()) {
            return;
        }
        $this->owner->LastEditedForCache = date('Y-m-d H:i:s');
        if ($this->SkipExtendedCacheRefresh()) {
            return;
        }
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
     * [
     *     'PropertyName',
     *     'Title',
     *     'Description'
     * ];
     * 
     * // manipulates LastEditedForCache only when PropertyName got or had the 'ValueToMatchOrDiffer'
     * [
     *     'PropertyName' => 'ValueToMatchOrDiffer',
     *     'StockQuantity' => 0
     * ];
     * </pre>
     * 
     * @return array
     */
    public function getCacheRelevantFields() {
        $cacheRelevantFields = [];
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
        if (!array_key_exists($this->owner->ID, $this->onAfterWriteInProgress) ||
            $this->onAfterWriteInProgress[$this->owner->ID] === false) {
            $this->onAfterWriteInProgress[$this->owner->ID] = true;
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
            $this->onAfterWriteInProgress[$this->owner->ID] = false;
        }
    }
    
}
