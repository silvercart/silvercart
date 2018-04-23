<?php

namespace SilverCart\Dev;

use SilverCart\Admin\Model\Config;
use SilverStripe\Dev\CsvBulkLoader;
use SilverStripe\ORM\DataObject;

/**
 * Custom importer for ShippingMethod
 * 
 * @package SilverCart
 * @subpackage Admin_Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 * @todo Test this.
 */
class ShippingMethodCsvBulkLoader extends CsvBulkLoader {
    
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
            'relationname'  => 'Zones',
            'callback'      => 'importZones',
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
    protected function importZones($obj, $val, $record) {
        $obj->Zones()->removeAll();
        $zoneIDs = explode(',', $val);
        foreach ($zoneIDs as $zoneID) {
            $zone = Zone::get()->byID($zoneID);
            if ($zone) {
                $obj->Zones()->add($zone);
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
        Config::Log('CSV Import', $logString, $filename);
    }
}
