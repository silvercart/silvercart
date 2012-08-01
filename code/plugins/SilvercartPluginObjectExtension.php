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
 * Methods for objects that want to provide plugin support.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 22.09.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartPluginObjectExtension extends DataExtension {
    
    /**
     * Passes through calls to SilvercartPlugins.
     *
     * @param string $method The name of the method to call
     *
     * @return mixed
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function SilvercartPlugin($method) {
        return SilvercartPlugin::call($this->owner, $method);
    }
    
    // ------------------------------------------------------------------------
    // CustomHtmlForm related methods
    // ------------------------------------------------------------------------
    
    /**
     * This method will be called after CustomHtmlForm's default submitFailure.
     * You can manipulate the relevant data here.
     * 
     * @param SS_HTTPRequest &$data submit data
     * @param Form           &$form form object
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.11.2011
     */
    public function onAfterSubmitFailure(&$data, &$form) {
    }
    
    /**
     * This method will be called after CustomHtmlForm's default submitSuccess.
     * You can manipulate the relevant data here.
     * 
     * @param SS_HTTPRequest &$data     submit data
     * @param Form           &$form     form object
     * @param array          &$formData secured form data
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.11.2011
     */
    public function onAfterSubmitSuccess(&$data, &$form, &$formData) {
    }
    
    /**
     * This method will be called before CustomHtmlForm's default submitFailure.
     * You can manipulate the relevant data here.
     * 
     * @param SS_HTTPRequest &$data submit data
     * @param Form           &$form form object
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.11.2011
     */
    public function onBeforeSubmitFailure(&$data, &$form) {
    }
    
    /**
     * This method will be called before CustomHtmlForm's default submitSuccess.
     * You can manipulate the relevant data here.
     * 
     * @param SS_HTTPRequest &$data     submit data
     * @param Form           &$form     form object
     * @param array          &$formData secured form data
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.11.2011
     */
    public function onBeforeSubmitSuccess(&$data, &$form, &$formData) {
    }
    
    /**
     * This method will replace CustomHtmlForm's default submitFailure. It's
     * important that this method returns sth. to ensure that the default 
     * submitFailure won't be called. The return value should be a rendered 
     * template or sth. similar.
     * You can also trigger a direct or redirect and return what ever you want
     * (perhaps boolean true?).
     * 
     * @param SS_HTTPRequest &$data submit data
     * @param Form           &$form form object
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.11.2011
     */
    public function overwriteSubmitFailure(&$data, &$form) {
    }
    
    /**
     * This method will replace CustomHtmlForm's default submitSuccess. It's
     * important that this method returns sth. to ensure that the default 
     * submitSuccess won't be called. The return value should be a rendered 
     * template or sth. similar.
     * You can also trigger a direct or redirect and return what ever you want
     * (perhaps boolean true?).
     * 
     * @param SS_HTTPRequest &$data     submit data
     * @param Form           &$form     form object
     * @param array          &$formData secured form data
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.11.2011
     */
    public function overwriteSubmitSuccess(&$data, &$form, &$formData) {
    }
    
    /**
     * This method is called before CustomHtmlForm requires the form fields. You 
     * can manipulate the default form fields here.
     * 
     * @param array &$formFields Form fields to manipulate
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.11.2011
     */
    public function updateFormFields(&$formFields) {
        $formFields = SilvercartPlugin::call($this->owner, 'updateFormFields', $formFields, true, array());
        
        if ($formFields &&
            is_array($formFields) &&
            count($formFields) > 0) {
            
            $formFields = $formFields[0];
        }
    }
    
    /**
     * This method is called before CustomHtmlForm set the preferences. You 
     * can manipulate the default preferences here.
     * 
     * @param array &$preferences Preferences to manipulate
     * 
     * @return bool
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.03.2012
     */
    public function updatePreferences(&$preferences) {
        $extendedPreferences = SilvercartPlugin::call($this->owner, 'updatePreferences', $preferences, true, array());
        
        if ($extendedPreferences &&
            is_array($extendedPreferences) &&
            count($extendedPreferences) > 0) {
            $preferences = $preferences[0];
        }
    }
}
