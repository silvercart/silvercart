<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
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
 * @subpackage Backend
 */

/**
 * Decorates the default ModelAdmin to inject some custom javascript.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 24.02.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartModelAdminDecorator extends DataObjectDecorator {
    
    /**
     * Injects some custom javascript to provide instant loading of DataObject
     * tables.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.01.2011
     */
    public function onAfterInit() {
        if (Director::is_ajax()) {
            return true;
        }
        
        $preventAutoLoadingClassNames           = $this->getPreventAutoLoadForManagedModels();
        $enabledFirstEntryAutoLoadClassNames    = $this->getEnabledFirstEntryAutoLoadForManagedModels();

        RequirementsEngine::registerJsVariable('PreventAutoLoadForManagedModels',           $preventAutoLoadingClassNames);
        RequirementsEngine::registerJsVariable('EnabledFirstEntryAutoLoadForManagedModels', $enabledFirstEntryAutoLoadClassNames);
        RequirementsEngine::parse('SilvercartModelAdminDecorator.js', array('silvercart/script/SilvercartModelAdminDecorator.js'));
        
        RequirementsEngine::add_i18n_javascript('silvercart/javascript/lang');
    }
    
    /**
     * Returns a string of comma separated class names for which the table list field autoload should be prevented.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.10.2011
     */
    public function getPreventAutoLoadForManagedModels() {
        $classNames     = '';
        $ownerClass     = $this->owner->class;
        $managedModels  = eval('return ' . $ownerClass . '::$managed_models;');
        
        foreach ($managedModels as $managedModel => $modelDefinitions) {
            if (is_array($modelDefinitions) &&
                array_key_exists('preventTableListFieldAutoLoad', $modelDefinitions) &&
                $modelDefinitions['preventTableListFieldAutoLoad']) {
             
                if (!empty($classNames)) {
                    $classNames .= ',';
                }
                $classNames .= "'".$managedModel."'";
            }
        }
        
        return $classNames;
    }
    
    /**
     * Returns a string of comma separated class names for which the autoload of
     * the first table entry should be provided.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.10.2011
     */
    public function getEnabledFirstEntryAutoLoadForManagedModels() {
        $classNames     = '';
        $ownerClass     = $this->owner->class;
        $managedModels  = eval('return ' . $ownerClass . '::$managed_models;');
        
        foreach ($managedModels as $managedModel => $modelDefinitions) {
            if (is_array($modelDefinitions) &&
                array_key_exists('enableFirstEntryAutoLoad', $modelDefinitions) &&
                $modelDefinitions['enableFirstEntryAutoLoad']) {
             
                if (!empty($classNames)) {
                    $classNames .= ',';
                }
                $classNames .= "'".$managedModel."'";
            }
        }
        
        return $classNames;
    }
}

/**
 * Decorates the default ModelAdmin_RecordController to inject some custom Actions.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 13.01.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartModelAdmin_RecordControllerDecorator extends DataObjectDecorator {

    /**
     * Liefert eine FormAction mit der CSS-Klasse customModelAdminRecordAction
     *
     * @param string $action     Method to call after clicking the button (server side)
     * @param string $title      Button label
     * @param Form   $form       Parent form. Will be set automatically when the action is set into an existing form
     * @param string $extraData  Extra Data
     * @param string $extraClass CSS class to use
     *
     * @return FormAction
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.01.2011
     */
    public function addFormAction($action, $title = "", $form = null, $extraData = null, $extraClass = '') {
        $extraClass = 'silvercartModelAdminRecordAction' . ($extraClass == '' ? '' : ' ' . $extraClass);
        return new FormAction($action, $title, $form, $extraData, $extraClass);
    }

    /**
     * Generiert und liefert eine FormResponse.
     *
     * @param string $message              Success-/Errormessage
     * @param string $status               Status 'good'/'bad'/'unknown'
     * @param bool   $success              true on success, else false
     * @param bool   $additionalJavaScript Additional JavaScipt
     *
     * @return string
     */
    public function getFormResponse($message, $status, $success, $additionalJavaScript = '') {
        FormResponse::status_message(sprintf($message), $status);
        FormResponse::add('var success=' . ($success == true ? 'true' : (is_string($success) ? $success : 'false')) . ';' . $additionalJavaScript);
        return FormResponse::respond();
    }
}