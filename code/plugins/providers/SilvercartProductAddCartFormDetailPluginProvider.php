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
 * Plugin-Provider for the SilvercartProductAddCartFormDetail object.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 16.11.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductAddCartFormDetailPluginProvider extends SilvercartPlugin {

    /**
     * Initialisation for plugin providers.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function init(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginInit', $arguments, $callingObject);
        
        return $this->returnExtensionResultAsString($result);
    }
    
    /**
     * We inject our additional fields here.
     * 
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function updateFormFields(&$arguments = array(), &$callingObject) {
        $this->extend('pluginUpdateFormFields', $arguments, $callingObject);
        
        return $arguments;
    }
    
    /**
     * This method will be called after CustomHtmlForm's default submitFailure.
     * You can manipulate the relevant data here.
     * 
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return bool
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function onAfterSubmitFailure(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOnAfterSubmitFailure', $arguments, $callingObject);
        
        return $result;
    }
    
    /**
     * This method will be called after CustomHtmlForm's default submitSuccess.
     * You can manipulate the relevant data here.
     * 
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return bool
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function onAfterSubmitSuccess(&$arguments = array(), &$callingObject) {
        $result = $this->extend('plguinOnAfterSubmitSuccess', $arguments, $callingObject);
        
        return $result;
    }
    
    /**
     * This method will be called before CustomHtmlForm's default submitFailure.
     * You can manipulate the relevant data here.
     * 
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return bool
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function onBeforeSubmitFailure(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOnBeforeSubmitFailure', $arguments, $callingObject);
        
        return $result;
    }
    
    /**
     * This method will be called before CustomHtmlForm's default submitSuccess.
     * You can manipulate the relevant data here.
     * 
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return bool
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function onBeforeSubmitSuccess(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOnBeforeSubmitSuccess', $arguments, $callingObject);
        
        return $result;
    }
    
    /**
     * This method will replace CustomHtmlForm's default submitFailure. It's
     * important that this method returns sth. to ensure that the default 
     * submitFailure won't be called. The return value should be a rendered 
     * template or sth. similar.
     * You can also trigger a direct or redirect and return what ever you want
     * (perhaps boolean true?).
     * 
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return bool
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function overwriteSubmitFailure(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOverwriteSubmitFailure', $arguments, $callingObject);
        
        return $result;
    }
    
    /**
     * This method will replace CustomHtmlForm's default submitSuccess. It's
     * important that this method returns sth. to ensure that the default 
     * submitSuccess won't be called. The return value should be a rendered 
     * template or sth. similar.
     * You can also trigger a direct or redirect and return what ever you want
     * (perhaps boolean true?).
     * 
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return bool
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function overwriteSubmitSuccess(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginOverwriteSubmitSuccess', $arguments, $callingObject);
        
        return $result;
    }
    
    /**
     * Use this method to insert additional fields into the
     * SilvercartProductAddCartFormDetail form.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function AddCartFormDetailAdditionalFields(&$arguments = array(), &$callingObject) {
        $result = $this->extend('pluginAddCartFormDetailAdditionalFields', $arguments, $callingObject);
        
        if (is_array($result)) {
            return $result[0];
        } else {
            return $result;
        }
    }
}
