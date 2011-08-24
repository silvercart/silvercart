<?php
/**
 * We use our own bulkloader because there's an unpatched bug in Silverstripe's
 * implementation with regards to relationships.
 * (see Silverstripe bugtracker "http://open.silverstripe.org/ticket/6472").
 *
 * @package Silvercart
 * @subpacke Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 20.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductCsvBulkLoader extends CsvBulkLoader {
    
    /*
	 * Load the given file via {@link self::processAll()} and {@link self::processRecord()}.
	 * Optionally truncates (clear) the table before it imports. 
	 *  
	 * @return BulkLoader_Result See {@link self::processAll()}
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.07.2011
	 */
	public function load($filepath) {
		ini_set('max_execution_time', 3600);
		increase_memory_limit_to('256M');
		
		//get all instances of the to be imported data object 
        if($this->deleteExistingRecords) {
            $q = singleton($this->objectClass)->buildSQL();
            
            if (!empty($this->objectClass)) {
                $idSelector = $this->objectClass.'."ID"';
            } else {
                $idSelector = '"ID"';
            }
            
            $q->select = array($idSelector); $ids = $q->execute()->column('ID');
            
            foreach($ids as $id) {
                $obj = DataObject::get_by_id($this->objectClass, $id); $obj->delete(); $obj->destroy(); unset($obj);
            }
        } 
		
		return $this->processAll($filepath);
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
        $results                = new BulkLoader_Result();
        $result                 = 0;
        $currPointer            = 0;
        $csvParser              = new CSVParser($filepath, $this->delimiter, $this->enclosure);
        
        $this->Log('product import start ---------------------------------------------------------------------');
        
        // --------------------------------------------------------------------
        // Insert header row if configured so
        // --------------------------------------------------------------------
		if($this->columnMap) {
			if ($this->hasHeaderRow) {
                $csv->mapColumns($this->columnMap);
            } else {
                $csv->provideHeaderRow($this->columnMap);
            }
		}
		
        // --------------------------------------------------------------------
        // Process data range
        // --------------------------------------------------------------------
		foreach($csvParser as $row) {
			$this->processRecord(
                $row,
                $this->columnMap,
                $results,
                $preview
            );
            
            $currPointer++;
            usleep(1000);
		}
        
        $this->Log('product import end ---------------------------------------------------------------------');
        
        return $result;
    }
    
    /**
     * Process a record from the import file
     *
     * @return boolean
     *
     * @param array             $record    The record to process
     * @param array             $columnMap The map of columns; NOT USED
     * @param BulkLoader_Result &$results  Stores the results so they can be displayed for the user
     * @param boolean           $preview   If set to true changes will not be written to the database
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.08.2011
     */
	protected function processRecord($record, $columnMap, &$results, $preview = false) {
        $silvercartProduct   = false;
        $silvercartProductID = 0;
        $action              = '';
        $updateIdentifier    = '';
        
        // ----------------------------------------------------------------
        // Use existing object:
        // We look for an ID, GTIN or manufacturers product number
        // ----------------------------------------------------------------
        if (!$silvercartProduct &&
             array_key_exists('ID', $record)) {
            
            $silvercartProduct = DataObject::get_by_id(
                'SilvercartProduct',
                $record['ID']
            );
            $action             = 'update';
            $updateIdentifier   = 'ID';
        }
        if (!$silvercartProduct &&
             array_key_exists('EANCode', $record)) {
            
            $silvercartProduct = DataObject::get_one(
                'SilvercartProduct',
                sprintf(
                    "`SilvercartProduct`.`EANCode` = '%s'",
                    $record['EANCode']
                )
            );
            $action             = 'update';
            $updateIdentifier   = 'EAN';
        }
        if (!$silvercartProduct &&
             array_key_exists('ProductNumberManufacturer', $record)) {
            
            $silvercartProduct = DataObject::get_one(
                'SilvercartProduct',
                sprintf(
                    "`SilvercartProduct`.`ProductNumberManufacturer` = '%s'",
                    $record['ProductNumberManufacturer']
                )
            );
            $action             = 'update';
            $updateIdentifier   = 'Manufacturer product number';
        }
        
        if (!$silvercartProduct) {
            // ----------------------------------------------------------------
            // Create new object:
            // We go for speed here, thus using direct DB queries.
            // ----------------------------------------------------------------
            $sqlQuery = new SQLQuery(
                'ID',
                'SilvercartProduct',
                NULL,
                'ID DESC',
                NULL,
                NULL,
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
            
            $silvercartProduct->castedUpdate($record);
            $this->Log('Mirror IDs are to be set');
            // ----------------------------------------------------------------
            // Update product group mirror pages
            // ----------------------------------------------------------------
            if (array_key_exists('SilvercartProductMirrorGroupIDs', $record)) {
                
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
            }
            $this->Log('Mirror IDs set');
            $silvercartProduct->write();
            
            $silvercartProductID = $silvercartProduct->ID;
            
            if ($action == 'update') {
                $this->Log("Updated Product ID: ".$silvercartProductID.", identified by ".$updateIdentifier);
            } else {
                $this->Log("Inserted Product ID: ".$silvercartProductID);
            }
        }
        
        unset($silvercartProduct);
        unset($sqlQuery);
        unset($insertID);
        unset($action);
        
        return $silvercartProductID;
    }
    
    /**
     * Write a log message.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 16.08.2011
     */
    protected function Log($logString) {
        $logDirectory = Director::baseFolder();

        $logDirectory = explode('/', $logDirectory);
        array_pop($logDirectory);
        array_pop($logDirectory);
        $logDirectory = implode('/', $logDirectory);

        if ($fp = fopen($logDirectory.'/log/importProducts.log', 'a')) {

            fwrite(
                $fp,
                "=== ".date('d.m.Y H:i:s').":\n".
                "    ".$logString."\n"
            );

            fclose($fp);
        }
    }
}
