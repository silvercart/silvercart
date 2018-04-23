<?php

namespace SilverCart\Model\Plugins\Providers;

use SilverCart\Model\Plugins\Plugin;

/**
 * Plugin-Provider for the contact message object.
 *
 * @package SilverCart
 * @subpackage Model_Plugins_Providers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ContactMessagePluginProvider extends Plugin {
    
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
     * ContactMessage object, so you can alter them to your needs.
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
     * ContactMessage object, so you can alter them to your needs.
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
     * ContactMessage object; just return true for that.
     *
     * @param array &$arguments     The arguments to pass
     *                              $arguments[0] = associative array of fields
     * @param mixed &$callingObject The calling object
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.01.2012
     */
    public function send(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginSend', $callingObject);
        
        if (is_array($result) &&
            count($result) == 0) {
            $result = false;
        }
        
        return $result;
    }
}
