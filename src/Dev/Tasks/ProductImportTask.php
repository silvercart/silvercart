<?php

namespace SilverCart\Dev\Tasks;

use SilverCart\Dev\ProductCsvBulkLoader;
use SilverCart\Model\Product\Product;
use SilverStripe\Control\CliController;

/**
 * Does an import for a specified file.
 *
 * This task should be called via sake on the command line:
 * "sake /ProductImportTask -i={FILEPATH}" -l={BULKLOADER}
 *
 * Example with parameters:
 *
 *     sake /ProductImportTask -i="/var/www/my_product_file.csv"
 *     sake /ProductImportTask -i="/var/www/my_product_file.csv" -l="MyCsvBulkLoader"
 *
 * @package SilverCart
 * @subpackage Dev_Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 12.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductImportTask extends CliController {
    
    /**
     * This method gets called from sake.
     * 
     * It starts the import from a specified file.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.10.2017
     */
    public function process() {
        $file = false;
        $bulkLoader = ProductCsvBulkLoader::class;

        foreach ($_GET as $key => $argument) {
            if ($key === '-i') {
                $file = $argument;
                continue;
            }
            if ($key === '-l') {
                $bulkLoader = $argument;
                continue;
            }
        }

        if (!file_exists($file)) {
            printf(
                "File '%s' not found.\n",
                $file
            );
            return false;
        }
        
        $this->importFile($file, $bulkLoader);
        
        return true;
    }
    
    /**
     * Imports a CSV file.
     * 
     * @param string $file       The filepath to use
     * @param string $bulkLoader given bulkloader for import
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.10.2017
     */
    protected function importFile($file, $bulkLoader) {    
        $loader = new $bulkLoader(Product::class);
        $loader->load($file);
    }
}