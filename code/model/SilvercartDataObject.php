<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Model
 */

/**
 * Extension for every DataObject
 *
 * @package Silvercart
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 29.10.2012
 * @license see license file in modules root directory
 */
class SilvercartDataObject extends DataExtension {
    
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
    public static $db = array(
        'LastEditedForCache' => 'SS_DateTime',
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
     * Handles UseAsRootForMainNavigation property (can only be set for a single 
     * page).
     * 
     * @param string  $fromStage        Stage to publish from
     * @param string  $toStage          Stage to publish to
     * @param boolean $createNewVersion Create new version or not?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.10.2014
     */
    public function onBeforeVersionedPublish($fromStage, $toStage, $createNewVersion) {
        if ($toStage == 'Live') {
            if ($this->owner instanceof SilvercartPage &&
                $this->owner->UseAsRootForMainNavigation) {
                DB::query('UPDATE SilvercartPage_Live SET UseAsRootForMainNavigation = 0 WHERE ID != ' . $this->owner->ID);
            }
        }
    }
    
    /**
     * Checks whether the current visited page is a child of the context
     * RedirectionPage.
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.10.2014
     */
    public function IsRedirectedChild() {
        $isRedirectedChild = false;
        if ($this->owner instanceof RedirectorPage &&
            Controller::curr()->hasMethod('data')) {
            if ($this->owner->LinkToID == Controller::curr()->data()->ID) {
                $isRedirectedChild = true;
            } else {
                $parentStack = Controller::curr()->data()->parentStack();
                foreach ($parentStack as $parent) {
                    if ($this->owner->LinkToID == $parent->ID) {
                        $isRedirectedChild = true;
                        break;
                    }
                }
            }
        }
        return $isRedirectedChild;
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
     * @since 13.02.2013
     */
    public function toRawMap($toDisplayWithinHtml = false) {
        $record = $this->owner->toMap();
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
            }
            $this->onAfterWriteInProgress = false;
        }
    }
    
    /**
     * Checks whether the given field is changed.
     * 
     * @param string $fieldName Field name to check change for
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.10.2014
     */
    public function fieldValueIsChanged($fieldName) {
        $isChanged = false;
        if ($this->owner->isChanged($fieldName)) {
            $changed  = $this->owner->getChangedFields(false, 1);
            $original = $changed[$fieldName]['before'];
            $new      = $changed[$fieldName]['after'];
            if ($new != $original) {
                $isChanged = true;
            }
        }
        return $isChanged;
    }
    
    /**
     * Checks whether the money field with the given fieldname is changed.
     * 
     * @param string $fieldName Field name to check change for
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.10.2014
     */
    public function moneyFieldIsChanged($fieldName) {
        $isChanged  = false;
        $amountName = $fieldName . 'Amount';
        if ($this->owner->isChanged($fieldName)) {
            $changed  = $this->owner->getChangedFields(false, 1);
            $original = $changed[$fieldName]['before'];
            $new      = $changed[$fieldName]['after'];
            $originalAmount = $this->owner->{$amountName};
            $newAmount      = 0;
            if (!is_null($original)) {
                $originalAmount = $original->getAmount();
            }
            if (!is_null($new)) {
                $newAmount = $new->getAmount();
            }
            if ($newAmount != $originalAmount) {
                $isChanged = true;
            }
        } elseif ($this->owner->isChanged($amountName)) {
            $changed  = $this->owner->getChangedFields(false, 1);
            $originalAmount = $changed[$amountName]['before'];
            $newAmount      = $changed[$amountName]['after'];
            if ($newAmount != $originalAmount) {
                $isChanged = true;
            }
        }
        return $isChanged;
    }
    
    /**
     * Checks whether the given has one relation is changed.
     * 
     * @param string $relationName Relation name to check change for
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.10.2014
     */
    public function hasOneRelationIsChanged($relationName) {
        $isChanged  = false;
        $relationID = $relationName . 'ID';
        if ($this->owner->isChanged($relationID)) {
            $changed  = $this->owner->getChangedFields(false, 1);
            $original = (int)$changed[$relationID]['before'];
            $new      = (int)$changed[$relationID]['after'];
            if ($new != $original) {
                $isChanged = true;
            }
        }
        return $isChanged;
    }
    
    /**
     * Clone of DataObject::getCMSFields() with some additional SilverCart
     * related features.
     * <ul>
     *  <li>Restricted fields can be updated by DataExtension (updateRestrictCMSFields).</li>
     *  <li>Translation fields of DataObjects with SilverCart based translation model will be scaffolded.</li>
     * </ul>
     * 
     * @param DataObject $dataObject                     DataObject to get CMS fields for
     * @param string     $neighbourFieldOfLanguageFields Name of the field to insert language fields after or before
     * @param bool       $insertLangugeFieldsAfter       Determines whether to add language fields before or after the given neighbour field
     * @param bool       $tabbed                         Determines whether get tabbed fields or not
     * 
     * @return FieldList
     */
    public static function getCMSFields(DataObject $dataObject, $neighbourFieldOfLanguageFields = null, $insertLangugeFieldsAfter = true, $tabbed = true) {
        $params = array(
            'includeRelations'  => $dataObject->isInDB(),
            'tabbed'            => $tabbed,
            'ajaxSafe'          => true,
        );
        $restrictFields = array();
        $dataObject->extend('updateRestrictCMSFields', $restrictFields);
        if (!empty($restrictFields)) {
            $params['restrictFields'] = $restrictFields;
        }

        $tabbedFields = self::scaffoldFormFields($dataObject, $params);
        
        if ($dataObject->has_extension($dataObject->class, 'SilvercartDataObjectMultilingualDecorator')) {
            $languageFields = SilvercartLanguageHelper::prepareCMSFields($dataObject->getLanguageClassName());
            foreach ($languageFields as $languageField) {
                if (!is_null($neighbourFieldOfLanguageFields)) {
                    if ($insertLangugeFieldsAfter) {
                        $tabbedFields->insertAfter($languageField, $neighbourFieldOfLanguageFields);
                        
                        /*
                         * Change the name of the field the insert the next field
                         * Otherwise the sort order would be inverted
                         */
                        $neighbourFieldOfLanguageFields = $languageField->getName();
                    } else {
                        $tabbedFields->insertBefore($languageField, $neighbourFieldOfLanguageFields);
                    }
                } else {
                    $tabbedFields->addFieldToTab('Root.Main', $languageField);
                }
            }
        }

        $dataObject->extend('updateCMSFields', $tabbedFields);

        return $tabbedFields;
    }

    /**
     * Scaffold a simple edit form for all properties on this dataobject,
     * based on default {@link FormField} mapping in {@link DBField::scaffoldFormField()}.
     * Field labels/titles will be auto generated from {@link DataObject::fieldLabels()}.
     * 
     * @param DataObject $dataObject DataObject to scaffold form fields for
     * @param array      $_params    Associative array passing through properties to {@link FormScaffolder}.
     * 
     * @return FieldList
     * 
     * @uses SilvercartFormScaffolder
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public static function scaffoldFormFields(DataObject $dataObject, $_params = null) {
        $params = array_merge(
                array(
                    'tabbed' => false,
                    'includeRelations' => false,
                    'restrictFields' => false,
                    'fieldClasses' => false,
                    'ajaxSafe' => false
                ),
                (array) $_params
        );

        $fs = new SilvercartFormScaffolder($dataObject);
        $fs->tabbed             = $params['tabbed'];
        $fs->includeRelations   = $params['includeRelations'];
        $fs->restrictFields     = $params['restrictFields'];
        $fs->fieldClasses       = $params['fieldClasses'];
        $fs->ajaxSafe           = $params['ajaxSafe'];

        return $fs->getFieldList();
    }

}