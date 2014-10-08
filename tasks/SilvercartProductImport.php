<?php
/**
 * Copyright 2014 pixeltricks GmbH
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
 * @subpackage Tasks
 */

/**
 * Does an import for a specified file.
 *
 * This task should be called via sake on the command line:
 * "sake /SilvercartProductImport -i={FILEPATH}" -l={BULKLOADER}
 *
 * Example with parameters:
 *
 *     sake /SilvercartProductImport -i="/var/www/my_product_file.csv"
 *     sake /SilvercartProductImport -i="/var/www/my_product_file.csv" -l="MyCsvBulkLoader"
 *
 * @package Silvercart
 * @subpackage Tasks
 * @author Ramon Kupper <rkupper@pixeltricks.de>
 * @copyright 2014 pixeltricks GmbH
 * @since 08.10.2014
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductImport extends CliController {
    
    /**
     * This method gets called from sake.
     * 
     * It starts the import from a specified file.
     *
     * @return void
     *
     * @copyright 2014 pixeltricks GmbH
     * @since 08.10.2014
     */
    public function process() {
        $file = false;
        $bulkLoader = 'SilvercartProductCsvBulkLoader';

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
     * @copyright 2014 pixeltricks GmbH
     * @since 08.10.2014
     */
    protected function importFile($file, $bulkLoader) {    
        $loader = new $bulkLoader('SilvercartProduct');
        
        $result = $loader->load($file);
    }
}