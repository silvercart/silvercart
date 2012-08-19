<?php
/**
 * Copyright 2011 pixeltricks GmbH
 *
 * This file is part of the SilverStripe modul FormFieldTools.
 *
 * FormFieldTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * FormFieldTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with FormFieldTools. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @package Silvercart
 * @subpackage FormFields
 */

/**
 * HasManyTextAutoCompleteField is an autocomplete form field for a DataObjects
 * has_many relation
 *
 * @package Silvercart
 * @subpackage FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.10.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartHasManyTextAutoCompleteField extends SilvercartTextAutoCompleteField {
    
    protected $className = 'SilvercartHasManyTextAutoCompleteField';
    
    /**
     * Returns the field tag.
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    public function Field() {
        $value = $this->getAutoCompleteValue();
        if (empty ($value)) {
            $fieldname = $this->name;
            $controller = $this->getController();
            $relations = $controller->$fieldname();
            if ($relations->Count() > 0) {
                $values = array();
                foreach ($relations as $dataObject) {
                    $attribute = $this->getAutoCompleteSourceAttribute();
                    if (is_array($attribute)) {
                        $valueParts = array();
                        foreach ($attribute as $key => $fieldName) {
                            $valueParts[] = $this->prepareValue($dataObject->{$fieldName});
                        }
                        $values[] = implode($this->getFieldDelimiter(), $valueParts);
                    } else {
                        $values[] = $this->prepareValue($dataObject->{$attribute});
                    }
                }
                $value = implode($this->getEntryDelimiter(), $values);
            }
        }
        $this->setValue($value);
        return parent::Field();
    }
    
    /**
     * Executes the common field holder routine and returns the custom
     * javascript code
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.06.2012
     */
    public function FieldHolderScript() {
        Requirements::javascript(SilvercartTools::getBaseURLSegment() . 'silvercart/script/SilvercartHasManyTextAutoCompleteField.js');
        return parent::FieldHolderScript();
    }
    
    /**
     * Saves the submited data into the managed DataObject.
     *
     * @param DataObject $record DataObject to save into
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    public function saveInto(DataObject $record) {
        $fieldName = $this->name;
        $saveDest = $record->$fieldName();

        if (!$saveDest) {
            user_error(
                sprintf(
                    "SilvercartTextAutoCompleteField::saveInto() Field '%s' not found on %s.%s",
                    $fieldName,
                    $record->class,
                    $record->ID
                ),
                E_USER_ERROR
            );
        }

        $items = array();
        $list = $this->value;
        $autoCompleteSource = $this->getAutoCompleteSource();
        if (empty ($autoCompleteSource)) {
            $this->generateAutoCompleteSource();
        }
        $autoCompleteSourceDataObject = $this->getAutoCompleteSourceDataObject();
        $autoCompleteSourceAttribute = $this->getAutoCompleteSourceAttribute();
        $relatedIDs = array();
        if ($list) {
            if ($list != 'undefined') {
                $items = explode($this->getEntryDelimiter(), $list);
                foreach ($items as $item) {
                    if (trim($item) == '') {
                        continue;
                    }
                    $item = trim($item);
                    if (is_array($autoCompleteSourceAttribute)) {
                        $filters        = array();
                        $splittedItems  = explode($this->getFieldDelimiter(), $item);
                        foreach ($autoCompleteSourceAttribute as $key => $fieldName) {
                            $filters[] = sprintf(
                                    "`%s` = '%s'",
                                    $fieldName,
                                    $splittedItems[$key]
                            );
                        }
                        $filter = implode(' AND ', $filters);
                    } else {
                        $filter = sprintf(
                                "`%s` = '%s'",
                                $autoCompleteSourceAttribute,
                                $item
                        );
                    }
                    $existingItem = DataObject::get_one(
                            $autoCompleteSourceDataObject,
                            $filter
                    );
                    if (!$existingItem) {
                        $existingItem = new $autoCompleteSourceDataObject();
                        if (is_array($autoCompleteSourceAttribute)) {
                            $splittedItems  = explode($this->getFieldDelimiter(), $item);
                            foreach ($autoCompleteSourceAttribute as $key => $fieldName) {
                                $existingItem->{$fieldName} = $splittedItems[$key];
                            }
                        } else {
                            $existingItem->{$autoCompleteSourceAttribute} = $item;
                        }
                        $existingItem->write();
                    }
                    $relatedIDs[] = $existingItem->ID;
                }
            }
        }

        $saveDest->setByIDList($relatedIDs);
    }
    
    /**
     * Generates the autocomplete source by the given controllers relations and
     * fieldname
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    protected function generateAutoCompleteSource() {
        $controller = $this->getController();
        $fieldName = $this->name;
        $relations = $controller->has_many();
        foreach ($relations as $relationName => $dataObject) {
            if ($relationName == $fieldName) {
                $this->setAutoCompleteSource($dataObject);
                break;
            }
        }
    }
    
    /**
     * Generates the autocomplete value by the given controllers relations and
     * fieldname
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    protected function generateAutoCompleteValue() {
        $fieldName = $this->name;
        $controller = $this->getController();
        $relations = $controller->$fieldName();
        if ($relations->Count() > 0) {
            $values = array();
            foreach ($relations as $dataObject) {
                $attribute = $this->getAutoCompleteSourceAttribute();
                if (is_array($attribute)) {
                    $valueParts = array();
                    foreach ($attribute as $key => $fieldName) {
                        $valueParts[] = $this->prepareValue($dataObject->{$fieldName});
                    }
                    $values[] = implode($this->getFieldDelimiter(), $valueParts);
                } else {
                    $values[] = $this->prepareValue($dataObject->{$attribute});
                }
            }
            $value = implode($this->getEntryDelimiter(), $values);
            $this->setAutoCompleteValue($value);
        }
    }

}