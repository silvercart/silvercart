<?php
/**
 * Copyright 2013 pixeltricks GmbH
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
 * SilvercartBridgeTextAutoCompleteField is an autocomplete form field for a DataObjects
 * bridge relation. A bridge relation is a workaroud for multiple many_many relations.
 *
 * @package Silvercart
 * @subpackage FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.03.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartBridgeTextAutoCompleteField extends SilvercartHasManyTextAutoCompleteField {
    
    /**
     * Class name of the field
     *
     * @var string
     */
    protected $className = 'SilvercartBridgeTextAutoCompleteField';
    
    /**
     * Class of the bridge object
     *
     * @var string
     */
    protected $bridgeClass = null;
    
    /**
     * Field of the bridge object
     *
     * @var string
     */
    protected $bridgeField = null;
    
    /**
     * Remote field of the bridge object
     *
     * @var string
     */
    protected $remoteBridgeField = null;
    
    /**
     * Remote field name of the bridge object
     *
     * @var string
     */
    protected $remoteBridgeFieldName = null;
    
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
        Requirements::javascript(SilvercartTools::getBaseURLSegment() . 'silvercart/script/SilvercartBridgeTextAutoCompleteField.js');
        return parent::FieldHolderScript();
    }
    
    /**
     * Generates the autocomplete source by the given controller
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    protected function generateAutoCompleteSource() {
        $this->setAutoCompleteSource($this->getController()->ClassName);
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
        $relations = $controller->{$fieldName}();
        if ($relations->Count() > 0) {
            $values = array();
            foreach ($relations as $dataObject) {
                $targetObject = $dataObject->{$this->getRemoteBridgeFieldName()}();
                $attribute = $this->getAutoCompleteSourceAttribute();
                if (is_array($attribute)) {
                    $valueParts = array();
                    foreach ($attribute as $key => $fieldName) {
                        $valueParts[] = $this->prepareValue($targetObject->{$fieldName});
                    }
                    $values[] = implode($this->getFieldDelimiter(), $valueParts);
                } else {
                    $values[] = $this->prepareValue($targetObject->{$attribute});
                }
            }
            $value = implode($this->getEntryDelimiter(), $values);
            $this->setAutoCompleteValue($value);
        }
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
        $relatedIDs         = $this->getRelatedIDs();
        $bridgeClass        = $this->getBridgeClass();
        $bridgeField        = $this->getBridgeField();
        $remoteBridgeField  = $this->getRemoteBridgeField();
        
        $deletionQuery = sprintf(
                'DELETE FROM %s WHERE %s = %d',
                $bridgeClass,
                $bridgeField,
                $record->ID
        );
        DB::query($deletionQuery);
        
        foreach ($relatedIDs as $ID) {
            $insertQuery = sprintf(
                'INSERT INTO %s (%s,%s) VALUES (%d,%d)',
                $bridgeClass,
                $bridgeField,
                $remoteBridgeField,
                $record->ID,
                $ID
            );
            DB::query($insertQuery);
        }
    }
    
    /**
     * Generates the bridge data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.03.2013
     */
    public function generateBridgeData() {
        $controller             = $this->getController();
        $candidates             = Object::uninherited_static($controller->class, 'has_many');
        $bridge                 = $candidates[$this->name];
        $bridgeParts            = explode('.', $bridge);
        $bridgeClass            = $bridgeParts[0];
        $bridgeField            = $controller->getRemoteJoinField($this->name);
        $remoteCandidates       = Object::uninherited_static($bridgeClass, 'has_one');
        $remoteBridgeField      = '';
        $remoteBridgeFieldName  = '';
        
        foreach ($remoteCandidates as $relationName => $className) {
            if ($className == $controller->class &&
                $relationName . 'ID' != $bridgeField) {
                $remoteBridgeField      = $relationName . 'ID';
                $remoteBridgeFieldName  = $relationName;
            }
        }
        
        $this->bridgeClass              = $bridgeClass;
        $this->bridgeField              = $bridgeField;
        $this->remoteBridgeField        = $remoteBridgeField;
        $this->remoteBridgeFieldName    = $remoteBridgeFieldName;
    }

    /**
     * Returns the class of the bridge object
     * 
     * @return string
     */
    public function getBridgeClass() {
        if (is_null($this->bridgeClass)) {
            $this->generateBridgeData();
        }
        return $this->bridgeClass;
    }
    
    /**
     * Returns the field of the bridge object
     * 
     * @return string
     */
    public function getBridgeField() {
        if (is_null($this->bridgeField)) {
            $this->generateBridgeData();
        }
        return $this->bridgeField;
    }
    
    /**
     * Returns the remote field of the bridge object
     * 
     * @return string
     */
    public function getRemoteBridgeField() {
        if (is_null($this->remoteBridgeField)) {
            $this->generateBridgeData();
        }
        return $this->remoteBridgeField;
    }
    
    /**
     * Returns the remote field name of the bridge object
     * 
     * @return string
     */
    public function getRemoteBridgeFieldName() {
        if (is_null($this->remoteBridgeFieldName)) {
            $this->generateBridgeData();
        }
        return $this->remoteBridgeFieldName;
    }

}


