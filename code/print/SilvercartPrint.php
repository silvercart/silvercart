<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @since 19.04.2012
 * @license see license file in modules root directory
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
     * @param ArrayList $dataObjectList ArrayList to get print URL for
     * 
     * @return string 
     */
    public static function getPrintURLForMany($dataObjectList) {
        $printURL = '';
        if ($dataObjectList instanceof SS_List) {
            $dataObject = $dataObjectList->first();
            if ($dataObject instanceof DataObject) {
                $map = $dataObjectList->map('ID','ID');
                if ($map instanceof SS_Map) {
                    $map = $map->toArray();
                }
                $printURL = sprintf(
                        'silvercart-print-many/%s/%s',
                        $dataObject->ClassName,
                        implode('-', $map)
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
        Requirements::themedCSS('SilvercartPrintDefault', 'silvercart');
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
        Requirements::themedCSS('SilvercartPrint' . $dataObjectName, 'silvercart');
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
 * @copyright 2013 pixeltricks GmbH
 * @since 19.04.2012
 * @license see license file in modules root directory
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