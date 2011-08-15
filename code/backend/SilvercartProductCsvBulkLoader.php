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
		increase_memory_limit_to('512M');
		
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
        $class       = $this->objectClass;
        $existingObj = $this->findExistingObject($record, $columnMap);
		$obj         = ($existingObj) ? $existingObj : new $class(); 
        
        foreach($record as $fieldName => $val) {
			if($this->isNullValue($val, $fieldName)) {
                continue;
            }
			if(strpos($fieldName, '->') !== FALSE) {
				$funcName = substr($fieldName, 2);
				$this->$funcName($obj, $val, $record);
			} else if($obj->hasMethod("import{$fieldName}")) {
				$obj->{"import{$fieldName}"}($val, $record);
			} else {
				$obj->update(array($fieldName => $val));
			}
            
            $id     = ($preview) ? 0 : $obj->write();
            $objID  = $obj->ID;
		
            $obj->destroy();
            
            unset($existingObj);
            unset($obj);

            return $objID;
		}
    }
}
