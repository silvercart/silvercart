<?php
/**
 * Does an import for a specified file.
 *
 * This task should be called via sake on the command line:
 * "sake /SilvercartProductImport -i={FILEPATH}"
 *
 * Example with parameters:
 *
 *     sake /SilvercartProductImport -i="/var/www/my_product_file.csv"
 *
 *
 * @package Silvercart
 * @subpackage Tasks
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 17.08.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductImport extends ScheduledTask {
    
    /**
     * This method gets called from sake.
     * 
     * It starts the import from a specified file.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 17.08.2011
     */
    public function process() {
        $file = false;

        foreach ($_GET as $key => $argument) {
            if ($key === '-i') {
                $file = $argument;
                break;
            }
        }

        if (!file_exists($file)) {
            printf(
                "File '%s' not found.\n",
                $file
            );
            return false;
        }
        
        $this->importFile($file);
        
        return true;
    }
    
    /**
     * Imports a CSV file.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 17.08.2011
     */
    protected function importFile($file) {
        $loader = new SilvercartProductCsvBulkLoader('SilvercartProduct');
        
        $result = $loader->load($file);
    }
}