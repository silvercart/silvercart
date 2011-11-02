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
 * @subpackage Tasks
 */

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
     * @param string $file The filepath to use
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