<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @since 24.02.2011
 * @license see license file in modules root directory
 */
class SilvercartModelAdminExtension extends DataExtension {
    
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

        #Requirements::javascript($baseUrl . "silvercart/script/SilvercartManyManyComplexTableField.js");
        
        Requirements::block($baseUrl . FRAMEWORK_DIR. '/thirdparty/jquery-ui/jquery.ui.core.js');
        //Requirements::javascript($baseUrl . 'silvercart/script/jquery-ui/jquery.ui.core.js');
        //Requirements::javascript($baseUrl . 'silvercart/script/jquery-ui/jquery.ui.position.js');
        //Requirements::javascript($baseUrl . 'silvercart/script/jquery-ui/jquery.ui.widget.js');
        
        Requirements::css('silvercart/css/backend/SilvercartMain.css');
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
        $managedModels  = $this->owner->getManagedModels();
        
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
        $managedModels  = $this->owner->getManagedModels();
        
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
