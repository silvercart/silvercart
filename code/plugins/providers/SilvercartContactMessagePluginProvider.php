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
 * Plugin-Provider for the contact message object.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 21.11.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartContactMessagePluginProvider extends SilvercartPlugin {
    
    /**
     * Initialisation for plugin providers.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.11.2011
     */
    public function init(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginInit', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * This method gets called after the field labels have been defined by the
     * SilvercartContactMessage object, so you can alter them to your needs.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = associative array of fields
     * @param mixed &$callingObject The calling object
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.11.2011
     */
    public function fieldLabels(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginFieldLabels', $arguments, $callingObject);
        
        return $result;
    }
    
    /**
     * This method gets called after the summary fields have been defined by the
     * SilvercartContactMessage object, so you can alter them to your needs.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = associative array of fields
     * @param mixed &$callingObject The calling object
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.11.2011
     */
    public function summaryFields(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginSummaryFields', $arguments, $callingObject);
        
        return $result;
    }
    
    /**
     * This method can replace the original send method of the
     * SilvercartContactMessage object; just return true for that.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = associative array of fields
     * @param mixed &$callingObject The calling object
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.11.2011
     */
    public function send(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginSend', $callingObject);

        if (is_array($result) &&
            count($result) == 0) {

            return false;
        }
        
        return true;
    }
}
