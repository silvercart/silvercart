<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * We use our own bulkloader because there's an unpatched bug in Silverstripe's
 * implementation with regards to relationships.
 * (see Silverstripe bugtracker "http://open.silverstripe.org/ticket/6472").
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 20.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductCsvBulkLoader extends CsvBulkLoader {
    
    /**
     * List of field names to check existing records for.
     *
     * @var array
     */
    public static $match_existing_fields = array(
        'ID',
        'EANCode',
        'ProductNumberManufacturer',
        'ProductNumberShop',
    );


    /**
     * Delimiter character
     *
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.2011
     */
    public $delimiter = ';';
    
    /**
     * Load the given file via {@link self::processAll()} and {@link self::processRecord()}.
     * Optionally truncates (clear) the table before it imports. 
     * 
     * @param string $filepath The filepath to use
     *  
     * @return BulkLoader_Result See {@link self::processAll()}
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.07.2011
     */
    public function load($filepath) {
        if (!SilvercartPlugin::call($this, 'overwriteLoad', array($filepath), false, 'DataObject')) {
            ini_set('max_execution_time', 3600);
            increase_memory_limit_to('256M');

            //get all instances of the to be imported data object 
            if ($this->deleteExistingRecords) {
                $q = singleton($this->objectClass)->buildSQL();

                if (!empty($this->objectClass)) {
                    $idSelector = $this->objectClass.'."ID"';
                } else {
                    $idSelector = '"ID"';
                }

                $q->select = array($idSelector);
                $ids = $q->execute()->column('ID');

                foreach ($ids as $id) {
                    $obj = DataObject::get_by_id($this->objectClass, $id);
                    $obj->delete();
                    $obj->destroy();
                    unset($obj);
                }
            } 
            return $this->processAll($filepath);
        }
    }
    
    /**
     * Process every record in the file
     *
     * @param string  $filepath Absolute path to the file we're importing (with UTF8 content)
     * @param boolean $preview  If true, we'll just output a summary of changes but not actually do anything
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 16.08.2011
     */
    public function processAll($filepath, $preview = false) {
        $pluginResult = SilvercartPlugin::call($this, 'overwriteProcessAll', array($filepath, $preview), false, 'DataObject');

        if ($pluginResult) {
            return $pluginResult;
        }
        
        $results                = new BulkLoader_Result();
        $result                 = 0;
        $currPointer            = 0;
        $csvParser              = new CSVParser($filepath, $this->delimiter, $this->enclosure);

        $this->Log('product import start ---------------------------------------------------------------------');

        // --------------------------------------------------------------------
        // Insert header row if configured so
        // --------------------------------------------------------------------
        if ($this->columnMap) {
            if ($this->hasHeaderRow) {
                $csvParser->mapColumns($this->columnMap);
            } else {
                $csvParser->provideHeaderRow($this->columnMap);
            }
        }
        
        // --------------------------------------------------------------------
        // Process data range
        // --------------------------------------------------------------------
        foreach ($csvParser as $row) {
            $status = $this->processRecord(
                $row,
                $this->columnMap,
                $results,
                $preview
            );
            
            if ($status) {
                $results->addCreated($status);
            }

            $currPointer++;
            usleep(1000);
        }

        $this->Log('product import end ---------------------------------------------------------------------');

        return $results;
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.08.2011
     */
    protected function processRecord($record, $columnMap, &$results, $preview = false) {
        $pluginResult = SilvercartPlugin::call($this, 'overwriteProcessRecord', array($record, $columnMap, $results, $preview), false, 'DataObject');
        
        if ($pluginResult &&
            is_array($pluginResult)) {
            
            return $pluginResult[0];
        }
        
        $silvercartProduct   = false;
        $silvercartProductID = 0;
        $action              = '';
        $updateIdentifier    = '';
        
        // ----------------------------------------------------------------
        // Check for an existing record
        // ----------------------------------------------------------------
        foreach (self::$match_existing_fields as $field) {
            if (!$silvercartProduct &&
                 array_key_exists($field, $record)) {
                $silvercartProduct = DataObject::get_one(
                    'SilvercartProduct',
                    sprintf(
                        "`SilvercartProduct`.`%s` = '%s'",
                        $field,
                        $record[$field]
                    )
                );
                $action             = 'update';
                $updateIdentifier   = $field;
            }
        }
        
        if (!$silvercartProduct) {
            // ----------------------------------------------------------------
            // Create new object:
            // We go for speed here, thus using direct DB queries.
            // ----------------------------------------------------------------
            $sqlQuery = new SQLQuery(
                'ID',
                'SilvercartProduct',
                null,
                'ID DESC',
                null,
                null,
                '1'
            );
            $insertID = $sqlQuery->execute()->value();
            $insertID = (int) $insertID + 1;
            
            DB::query(
                sprintf("
                    INSERT INTO
                        SilvercartProduct(
                            ID,
                            ClassName,
                            Created
                        ) VALUES(
                            %d,
                            'SilvercartProduct',
                            '%s'
                        )
                    ",
                    $insertID,
                    date('Y-m-d H:i:s')
                )
            );
            DB::query(
                sprintf("
                    INSERT INTO
                        SilvercartProductLanguage(
                            ID,
                            ClassName,
                            Created,
                            SilvercartProductID,
                            Locale
                        ) VALUES(
                            %d,
                            'SilvercartProductLanguage',
                            '%s',
                            %d,
                            '%s'
                        )
                    ",
                    $insertID,
                    date('Y-m-d H:i:s'),
                    $insertID,
                    Translatable::get_current_locale()
                )
            );
            
            $silvercartProduct = DataObject::get_by_id(
                'SilvercartProduct',
                $insertID
            );
            $action = 'insert';
        }
        
        // --------------------------------------------------------------------
        // Update product fields
        // --------------------------------------------------------------------
        if ($silvercartProduct) {
            if (array_key_exists('ID', $record)) {
                unset($record['ID']);
            }
            
            // ----------------------------------------------------------------
            // save data
            // ----------------------------------------------------------------
            foreach ($record as $fieldName => $val) {
                if ($this->isNullValue($val, $fieldName)) {
                    continue;
                }
                if (strpos($fieldName, '->') !== false) {
                    $funcName = substr($fieldName, 2);
                    $this->$funcName($silvercartProduct, $val, $record);
                } elseif ($silvercartProduct->hasMethod("import" . $fieldName)) {
                    $silvercartProduct->{"import{$fieldName}"}($val, $record);
                } else {
                    $silvercartProduct->update(array($fieldName => $val));
                }
            }
            // ----------------------------------------------------------------
            // Update product group mirror pages
            // ----------------------------------------------------------------
            if (array_key_exists('SilvercartProductMirrorGroupIDs', $record)) {
                $this->Log('Mirror IDs are to be set');
                
                // Delete existing relations
                if ($silvercartProduct->SilvercartProductGroupMirrorPages()) {
                    foreach ($silvercartProduct->SilvercartProductGroupMirrorPages() as $silvercartProductGroupMirrorPage) {
                        $silvercartProduct->SilvercartProductGroupMirrorPages()->remove($silvercartProductGroupMirrorPage);
                    }
                }
                
                // Set new relations
                $silvercartProductMirrorGroupIDs = explode(',', $record['SilvercartProductMirrorGroupIDs']);
                
                foreach ($silvercartProductMirrorGroupIDs as $silvercartProductMirrorGroupID) {
                    $silvercartProductGroupMirrorPage = DataObject::get_by_id('SilvercartProductGroupPage', $silvercartProductMirrorGroupID);
                    
                    if ($silvercartProductGroupMirrorPage) {
                        $silvercartProduct->SilvercartProductGroupMirrorPages()->add($silvercartProductGroupMirrorPage);
                    }
                    unset($silvercartProductGroupMirrorPage);
                }
                unset($silvercartProductMirrorGroupIDs);
                
                $this->Log('Mirror IDs set');
            }
            $silvercartProduct->write();
            
            $silvercartProductID = $silvercartProduct->ID;
            
            if ($action == 'update') {
                $this->Log("Updated Product ID: ".$silvercartProductID.", identified by ".$updateIdentifier);
            } else {
                $this->Log("Inserted Product ID: ".$silvercartProductID);
            }
        }
        
        unset($sqlQuery);
        unset($insertID);
        unset($action);
        
        return $silvercartProduct;
    }
    
    /**
     * Write a log message.
     * 
     * @param string $logString string to log
     * @param string $filename  filename to log into
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 16.08.2011
     */
    public function Log($logString, $filename = 'importProducts') {
        SilvercartConfig::Log('CSV Import', $logString, $filename);
    }
}
