<?php
/**
 * Copyright 2012 pixeltricks GmbH
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
 * @subpackage Print
 */

/**
 * Provides some helping methods for the print environment.
 *
 * @package Silvercart
 * @subpackage Print
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 19.04.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPrint {
    
    /**
     * Returns the print URL for the given DataObject
     * (silvercart-print/$DataObjectName/$DataObjectID)
     *
     * @param DataObject $dataObject DataObject to get print URL for
     * 
     * @return string 
     */
    public static function getPrintURL($dataObject) {
        $printURL = '';
        if ($dataObject instanceof DataObject) {
            $printURL = sprintf(
                    'silvercart-print/%s/%s',
                    $dataObject->ClassName,
                    $dataObject->ID
            );
        }
        return $printURL;
    }
    
    /**
     * Returns the print URL for the given DataObject
     * (silvercart-print/$DataObjectName/$DataObjectID)
     *
     * @param DataObjectSet $dataObjectSet DataObjectSet to get print URL for
     * 
     * @return string 
     */
    public static function getPrintURLForMany($dataObjectSet) {
        $printURL = '';
        if ($dataObjectSet instanceof DataObjectSet) {
            $dataObject = $dataObjectSet->First();
            if ($dataObject instanceof DataObject) {
                $printURL = sprintf(
                        'silvercart-print-many/%s/%s',
                        $dataObject->ClassName,
                        implode('-', $dataObjectSet->map('ID','ID'))
                );
            }
        }
        return $printURL;
    }
    
    /**
     * Returns the print URL for the given DataObject
     * (silvercart-print/$DataObjectName/$DataObjectID)
     *
     * @param DataObject $dataObject DataObject to get print URL for
     * 
     * @return string 
     */
    public static function getPrintInlineURL($dataObject) {
        $printURL = '';
        if ($dataObject instanceof DataObject) {
            $printURL = sprintf(
                    'silvercart-print-inline/%s/%s',
                    $dataObject->ClassName,
                    $dataObject->ID
            );
        }
        return $printURL;
    }
    
    /**
     * Clears the already set requirements and loads the default print requirements
     * 
     * @param bool $withJavascript Should the JS be loaded?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012 
     */
    public static function loadDefaultRequirements($withJavascript = true) {
        Requirements::clear();
        Requirements::themedCSS('SilvercartPrintDefault');
        if ($withJavascript) {
            Requirements::javascript('silvercart/script/SilvercartPrintDefault.js');
        }
    }
    
    /**
     * Returns the given DataObjects default print template
     * 
     * @param DataObject $dataObject DataObject to get print output for
     * @param bool       $isInline   Is this an inline print preview?
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    public static function getPrintOutput($dataObject, $isInline = false) {
        $printResult = '';
        if ($dataObject->CanView()) {
            self::loadDefaultRequirements(!$isInline);
            Requirements::themedCSS('SilvercartPrint' . $dataObject->ClassName);
            $printResult = $dataObject->renderWith('SilvercartPrint' . $dataObject->ClassName);
        }
        return $printResult;
    }
    
    /**
     * Returns the given DataObjects default print template
     * 
     * @param DataObject $dataObject DataObject to get print output for
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    public static function getPrintInlineOutput($dataObject) {
        return self::getPrintOutput($dataObject, true);
    }
    
    /**
     * Returns the given DataObjects default print template
     * 
     * @param string $dataObjectName DataObject name
     * @param array  $dataObjectIDs  DataObject IDs to get print output for
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    public static function getPrintManyOutput($dataObjectName, $dataObjectIDs) {
        $printResult = '';
        self::loadDefaultRequirements(false);
        Requirements::themedCSS('SilvercartPrint' . $dataObjectName);
        foreach ($dataObjectIDs as $dataObjectID) {
            $dataObject = DataObject::get_by_id($dataObjectName, $dataObjectID);
            if ($dataObject &&
                $dataObject->CanView()) {
                $printResult .= $dataObject->renderWith('SilvercartPrint' . $dataObject->ClassName);
            }
        }
        return $printResult;
    }
    
}

/**
 * Default controller to get the print output for supported DataObjects.
 * The controller is called by using the URL rewrite rule
 * silvercart-print/$DataObjectName/$DataObjectID
 * and requires the methods printDataObject() and CanView() on the given
 * DataObject.
 *
 * @package Silvercart
 * @subpackage Print
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 19.04.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPrint_Controller extends SilvercartPage_Controller {

    /**
     * Executes the print controllers logic
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    public function init() {
        parent::init();
        $request        = $this->getRequest();
        $params         = $request->allParams();
        $dataObjectName = $params['DataObjectName'];
        $dataObjectID   = $params['DataObjectID'];
        if (strpos($request->getVar('url'), 'silvercart-print-many') !== false) {
            $dataObjectIDs  = explode('-', $dataObjectID);
            $output         = SilvercartPrint::getPrintManyOutput($dataObjectName, $dataObjectIDs);
            if (!empty($output)) {
                print $output;
                exit();
            }
            $this->redirect(Director::baseURL());
        } else {
            $dataObject     = DataObject::get_by_id($dataObjectName, $dataObjectID);
            if ($dataObject &&
                $dataObject->canView()) {
                if ($dataObject->hasMethod('printDataObject')) {
                    print $dataObject->printDataObject();
                } else {
                    if (strpos($request->getVar('url'), 'silvercart-print-inline') === false) {
                        $output = SilvercartPrint::getPrintOutput($dataObject);
                    } else {
                        $output = SilvercartPrint::getPrintInlineOutput($dataObject);
                    }
                    print $output;
                }
                exit();
            } else {
                $this->redirect(Director::baseURL());
            }
        }
    }
    
}