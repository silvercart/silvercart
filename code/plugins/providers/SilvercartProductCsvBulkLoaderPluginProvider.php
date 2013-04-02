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
 * @subpackage Plugins
 */

/**
 * Plugin-Provider for the the SilvercartProductCsvBulkLoader object.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 22.11.2011
 * @license see license file in modules root directory
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductCsvBulkLoaderPluginProvider extends SilvercartPlugin {
    
    /**
     * Initialisation for plugin providers.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.2011
     */
    public function init(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginInit', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * Overwrite the load routine here.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = $filepath: The path of the file to load
     * @param mixed &$callingObject The calling object
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.2011
     */
    public function overwriteLoad(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOverwriteLoad', $arguments, $callingObject);
        
        if (empty($result)) {
            return false;
        }
        
        return $result;
    }
    
    /**
     * Overwrite the "process all" routine here.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = $filepath: The path of the file to load
     *                              $arguments[1] = $preview: Indicates wether to simulate or do write actions
     * @param mixed &$callingObject The calling object
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.2011
     */
    public function overwriteProcessAll(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOverwriteProcessAll', $arguments, $callingObject);
        
        if (empty($result)) {
            return false;
        }
        
        return $result;
    }
    
    /**
     * Overwrite the "process record" routine here.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = $record: An associative array containing one line of the CSV-file
     *                              $arguments[1] = $columnmap: The map of columns; NOT USED
     *                              $arguments[2] = $results: Stores the results so they can be displayed for the user; NOT USED
     *                              $arguments[3] = $preview: Indicates wether to simulate or do write actions 
     * @param mixed &$callingObject The calling object
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.2011
     */
    public function overwriteProcessRecord(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOverwriteProcessRecord', $arguments, $callingObject);
        
        if (empty($result)) {
            return false;
        }
        
        return $result;
    }
}