<?php

namespace SilverCart\Dev;

use SilverCart\Dev\Tools;
use SilverCart\Admin\Model\Config;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\ProductTranslation;
use SilverStripe\Dev\BulkLoader_Result;
use SilverStripe\Dev\CsvBulkLoader;
use SilverStripe\Dev\CSVParser;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Queries\SQLSelect;

/**
 * We use our own bulkloader because there's an unpatched bug in Silverstripe's
 * implementation with regards to relationships.
 * (see Silverstripe bugtracker "http://open.silverstripe.org/ticket/6472").
 * 
 * @package SilverCart
 * @subpackage Admin_Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 * @todo Test this.
 */
class ProductCsvBulkLoader extends CsvBulkLoader {
    
    /**
     * List of field names to check existing records for.
     *
     * @var array
     */
    public static $match_existing_fields = array(
        'ProductNumberShop',
    );
    
    /**
     * Indicator to check whether to create a new product if no existing one is
     * matching
     *
     * @var bool 
     */
    protected $createNewIfNotMatched = true;


    /**
     * Load the given file via {@link self::processAll()} and {@link self::processRecord()}.
     * Optionally truncates (clear) the table before it imports. 
     * 
     * @param string $filepath The filepath to use
     *  
     * @return BulkLoader_Result See {@link self::processAll()}
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.04.2018
     */
    public function load($filepath) {
        $this->extend('onBeforeLoad', $filepath);
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
        $result = $this->processAll($filepath);
        $this->extend('onAfterLoad', $result);
        return $result;
    }
    
    /**
     * Process every record in the file
     *
     * @param string  $filepath Absolute path to the file we're importing (with UTF8 content)
     * @param boolean $preview  If true, we'll just output a summary of changes but not actually do anything
     *
     * @return int
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.04.2018
     */
    public function processAll($filepath, $preview = false) {
        $this->extend('onBeforeProcessAll', $filepath, $preview);
        
        $results     = new BulkLoader_Result();
        $currPointer = 0;
        $csvParser   = new CSVParser($filepath, $this->delimiter, $this->enclosure);

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

        $this->extend('onAfterProcessAll', $results, $filepath, $preview);
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Ramon Kupper <rkupper@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.04.2018
     */
    protected function processRecord($record, $columnMap, &$results, $preview = false) {
        $this->extend('onBeforeProcessRecord', $record, $columnMap, $results, $preview);
        
        $silvercartProduct   = false;
        $action              = '';
        $updateIdentifier    = '';
        
        // ----------------------------------------------------------------
        // Check for an existing record
        // ----------------------------------------------------------------
        foreach (self::$match_existing_fields as $field) {
            if (!$silvercartProduct &&
                 array_key_exists($field, $record)) {
                $silvercartProduct = Product::get()
                        ->filter($field, $record[$field])
                        ->first();
                $action            = 'update';
                $updateIdentifier  = $field;
            }
        }
        
        if (!$silvercartProduct &&
            $this->createNewIfNotMatched) {
            // ----------------------------------------------------------------
            // Create new object:
            // We go for speed here, thus using direct DB queries.
            // ----------------------------------------------------------------
            $table = DataObject::getSchema()->tableForField(Product::class, 'ID');
            $translationTable = DataObject::getSchema()->tableForField(ProductTranslation::class, 'ID');
            $sqlSelect = new SQLSelect(
                'ID',
                $table,
                null,
                'ID DESC',
                null,
                null,
                '1'
            );
            $insertID = $sqlSelect->execute()->value();
            $insertID = (int) $insertID + 1;
                    
            DB::query(
                sprintf("
                    INSERT INTO
                        %s(
                            ID,
                            ClassName,
                            Created,
                            PriceGrossAmount
                        ) VALUES(
                            %d,
                            '%s',
                            '%s',
                            '%s'
                        )
                    ",
                    $table,
                    $insertID,
                    Product::class,
                    date('Y-m-d H:i:s'),
                    $record['PriceGrossAmount']
                )
            );
            DB::query(
                sprintf("
                    INSERT INTO
                        %s(
                            ClassName,
                            Created,
                            ProductID,
                            Locale
                        ) VALUES(
                            '%s',
                            '%s',
                            %d,
                            '%s'
                        )
                    ",
                    $translationTable,
                    ProductTranslation::class,
                    date('Y-m-d H:i:s'),
                    $insertID,
                    Tools::current_locale()
                )
            );
            
            $silvercartProduct = Product::get()->byID($insertID);
            $action = 'insert';
        }
        
        if ($silvercartProduct) {
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
                if (array_key_exists('ProductMirrorGroupIDs', $record)) {
                    $this->Log('Mirror IDs are to be set');

                    // Delete existing relations
                    if ($silvercartProduct->ProductGroupMirrorPages()) {
                        foreach ($silvercartProduct->ProductGroupMirrorPages() as $silvercartProductGroupMirrorPage) {
                            $silvercartProduct->ProductGroupMirrorPages()->remove($silvercartProductGroupMirrorPage);
                        }
                    }

                    // Set new relations
                    $silvercartProductMirrorGroupIDs = explode(',', $record['ProductMirrorGroupIDs']);

                    foreach ($silvercartProductMirrorGroupIDs as $silvercartProductMirrorGroupID) {
                        if (!empty($silvercartProductMirrorGroupID)) {
                            $silvercartProductGroupMirrorPage = ProductGroupPage::get()->byID($silvercartProductMirrorGroupID);

                            if ($silvercartProductGroupMirrorPage) {
                                $silvercartProduct->ProductGroupMirrorPages()->add($silvercartProductGroupMirrorPage);
                            }
                            unset($silvercartProductGroupMirrorPage);
                        }
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
        }
        
        unset($sqlSelect);
        unset($insertID);
        unset($action);
        
        $this->extend('onAfterProcessRecord', $silvercartProduct, $record, $columnMap, $results, $preview);
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
     * @since 16.08.2011
     */
    public function Log($logString, $filename = 'importProducts') {
        Config::Log('CSV Import', $logString, $filename);
    }
}
