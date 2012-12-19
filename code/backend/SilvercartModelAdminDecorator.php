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
        Translatable::set_current_locale(i18n::get_locale());
        if (Director::is_ajax()) {
            return true;
        }
        
        $baseUrl                                = SilvercartTools::getBaseURLSegment();
        $preventAutoLoadingClassNames           = $this->getPreventAutoLoadForManagedModels();
        $enabledFirstEntryAutoLoadClassNames    = $this->getEnabledFirstEntryAutoLoadForManagedModels();

        RequirementsEngine::registerJsVariable('PreventAutoLoadForManagedModels',           $preventAutoLoadingClassNames);
        RequirementsEngine::registerJsVariable('EnabledFirstEntryAutoLoadForManagedModels', $enabledFirstEntryAutoLoadClassNames);
        RequirementsEngine::parse('SilvercartModelAdminDecorator.js', array('silvercart/script/SilvercartModelAdminDecorator.js'));
        
        RequirementsEngine::add_i18n_javascript('silvercart/javascript/lang');
        Requirements::javascript($baseUrl . "silvercart/script/SilvercartManyManyComplexTableField.js");
        
        Requirements::block($baseUrl . 'sapphire/thirdparty/jquery-ui/jquery.ui.core.js');
        Requirements::javascript($baseUrl . 'silvercart/script/jquery-ui/jquery.ui.core.js');
        Requirements::javascript($baseUrl . 'silvercart/script/jquery-ui/jquery.ui.position.js');
        Requirements::javascript($baseUrl . 'silvercart/script/jquery-ui/jquery.ui.widget.js');
        
        Requirements::css('silvercart/css/backend/SilvercartMain.css');
    }
    
    /**
     * Returns a string of comma separated class names for which the table list field autoload should be prevented.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.10.2012
     */
    public function getPreventAutoLoadForManagedModels() {
        $classNames     = '';
        $ownerClass     = $this->owner->class;
        $managedModels  = eval('return ' . $ownerClass . '::$managed_models;');
        $request        = $this->owner->getRequest();
        $params         = $request->allParams();
        $action         = null;
        if (array_key_exists('Action', $params)) {
            $action = $params['Action'];
        }
        
        foreach ($managedModels as $managedModel => $modelDefinitions) {
            if ((is_array($modelDefinitions) &&
                 array_key_exists('preventTableListFieldAutoLoad', $modelDefinitions) &&
                 $modelDefinitions['preventTableListFieldAutoLoad']) ||
                $action == $managedModel) {
             
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
 * Decorates the default SilvercartModelAdmin_CollectionController to inject 
 * some custom Actions.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 11.07.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartModelAdmin_CollectionController extends DataObjectDecorator {
    
    /**
     * Extended actions
     *
     * @var array
     */
    public static $allowed_actions = array(
        'doBatchAction',
    );

    /**
     * Executes a batch action (if exists)
     *
     * @param SS_HTTPRequest $request HTTP request
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.07.2012
     */
    public function doBatchAction(SS_HTTPRequest $request) {
        $orderData          = $request->postVar('SilvercartOrder');
        $selectedOrderIDs   = $orderData['selected'];
        $batchActionToCall  = $request->postVar('BatchActionToCall');
        $batchCallbackData  = $request->postVar('BatchCallbackData');
        $result             = '';
        if ($batchActionToCall) {
            $batchActionMethodName  = 'silvercartBatch_' . $batchActionToCall;
            $orderIDs               = explode(',', $selectedOrderIDs);
            $result                 = $this->owner->{$batchActionMethodName}($orderIDs, $batchCallbackData);
        }
        return $result;
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
     * List of allowed actions
     *
     * @var array
     */
    public static $allowed_actions = array(
        'printDataObject',
    );

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

    /**
     * Defaut action to trigger printing the current record
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2012
     */
    public function printDataObject() {
        $message                = _t('Silvercart.NOT_ALLOWED_TO_PRINT', "You are not allowed to print this object!");
        $status                 = 'bad';
        $success                = false;
        $additionalJavaScript   = '';
        if ($this->owner->currentRecord->canView()) {
            $message                = _t('Silvercart.LOADING_PRINT_VIEW', "Loading print view.");
            $status                 = 'good';
            $success                = true;
            $additionalJavaScript   = sprintf(
                    "window.open('%s');",
                    SilvercartPrint::getPrintURL($this->owner->currentRecord)
            );
        }
        return $this->getFormResponse($message, $status, $success, $additionalJavaScript);
    }
}