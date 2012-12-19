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
 * @subpackage Backend
 */

/**
 * Custom importer for SilvercartShippingMethod
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 17.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartShippingMethodCsvBulkLoader extends CsvBulkLoader {
    
    /**
     * Call backs for has many relations
     *
     * @var array
     */
    public $has_many_relation_callbacks = array(
        
    );
    
    /**
     * Call backs for many many relations
     *
     * @var array
     */
    public $many_many_relation_callbacks = array(
        'AttributedZoneIDs' => array(
            'relationname'  => 'SilvercartZones',
            'callback'      => 'importSilvercartZones',
        ),
    );
    
    /**
     * Overwrites the object class name if given by the request
     *
     * @param string $objectClass Name of the object class to use
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.01.2012
     */
    public function __construct($objectClass) {
        if (array_key_exists('ObjectClass', $_REQUEST)) {
            if (class_exists($_REQUEST['ObjectClass'])) {
                $objectClass = $_REQUEST['ObjectClass'];
            }
        }
        parent::__construct($objectClass);
    }
    
    /**
     * Process a record from the import file
     *
     * @param array             $record    The record to process
     * @param array             $columnMap The map of columns; NOT USED
     * @param BulkLoader_Result &$results  Stores the results so they can be displayed for the user
     * @param boolean           $preview   If set to true changes will not be written to the database
     *
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.01.2012
     */
    protected function processRecord($record, $columnMap, &$results, $preview = false) {
        if (array_key_exists('ObjectClass', $_REQUEST)) {
            if (!class_exists($_REQUEST['ObjectClass'])) {
                throw new Exception(
                        sprintf(
                            "Unknown ObjectClass '%s'",
                            $_REQUEST['ObjectClass']
                        )
                );
            }
        } else {
            throw new Exception('ObjectClass has to be passed');
        }
        $methodName         = 'processRecordFor' . $this->objectClass;
        $this->objID = parent::processRecord($record, $columnMap, $results, $preview);
        $this->onAfterProcessRecord($record);
        return $this->objID;
    }
    
    /**
     * Calls the has_many and many_many callbacks.
     *
     * @param array $record The record to process
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.01.2012
     */
    public function onAfterProcessRecord($record) {
        $obj = DataObject::get_by_id($this->objectClass, $this->objID);
        if ($obj) {
            foreach ($record as $fieldName => $val) {
                // don't bother querying of value is not set
                if ($this->isNullValue($val)) {
                    continue;
                }

                // checking for existing relations
                if (isset($this->many_many_relation_callbacks[$fieldName])) {
                    $relationName = $this->many_many_relation_callbacks[$fieldName]['relationname'];
                    if ($this->hasMethod($this->many_many_relation_callbacks[$fieldName]['callback'])) {
                        $this->{$this->many_many_relation_callbacks[$fieldName]['callback']}($obj, $val, $record);
                    } elseif ($obj->hasMethod($this->many_many_relation_callbacks[$fieldName]['callback'])) {
                        $obj->{$this->many_many_relation_callbacks[$fieldName]['callback']}($val, $record);
                    }
                } elseif (isset($this->has_many_relation_callbacks[$fieldName])) {
                    $relationName = $this->has_many_relation_callbacks[$fieldName]['relationname'];
                    if ($this->hasMethod($this->has_many_relation_callbacks[$fieldName]['callback'])) {
                        $this->{$this->has_many_relation_callbacks[$fieldName]['callback']}($obj, $val, $record);
                    } elseif ($obj->hasMethod($this->has_many_relation_callbacks[$fieldName]['callback'])) {
                        $obj->{$this->has_many_relation_callbacks[$fieldName]['callback']}($val, $record);
                    }
                }

            }
            $obj->write();
        }
    }
    
    /**
     * Callback to import related zones
     *
     * @param DataObject $obj    DataObject
     * @param string     $val    Value to set
     * @param array      $record Record data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.01.2012
     */
    protected function importSilvercartZones($obj, $val, $record) {
        $obj->SilvercartZones()->removeAll();
        $zoneIDs = explode(',', $val);
        foreach ($zoneIDs as $zoneID) {
            $zone = DataObject::get_by_id('SilvercartZone', $zoneID);
            if ($zone) {
                $obj->SilvercartZones()->add($zone);
            }
        }
    }

        /**
     * Write a log message.
     * 
     * @param string $logString string to log
     * @param string $filename  filename to log into
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.01.2012
     */
    public function Log($logString, $filename = 'importProducts') {
        SilvercartConfig::Log('CSV Import', $logString, $filename);
    }
}
