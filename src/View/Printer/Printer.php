<?php

namespace SilverCart\View\Printer;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\SS_List;
use SilverStripe\ORM\Map;
use SilverStripe\View\Requirements;

/**
 * Provides some helping methods for the print environment.
 *
 * @package SilverCart
 * @subpackage Printer
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 19.04.2012
 * @license see license file in modules root directory
 */
class Printer {
    
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
                    str_replace('\\', '-', $dataObject->ClassName),
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
                if ($map instanceof Map) {
                    $map = $map->toArray();
                }
                $printURL = sprintf(
                        'silvercart-print-many/%s/%s',
                        str_replace('\\', '-', $dataObject->ClassName),
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
                    str_replace('\\', '-', $dataObject->ClassName),
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
        Requirements::themedCSS('client/css/PrinterDefault');
        if ($withJavascript) {
            Requirements::javascript('silvercart/silvercart:client/javascript/PrinterDefault.js');
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
            Requirements::themedCSS('client/css/Printer' . str_replace('\\', '', $dataObject->ClassName));
            $printResult = $dataObject->renderWith('SilverCart/Printer/' . str_replace('\\', '', $dataObject->ClassName));
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
        foreach ($dataObjectIDs as $dataObjectID) {
            $dataObject = DataObject::get_by_id($dataObjectName, $dataObjectID);
            if ($dataObject &&
                $dataObject->CanView()) {
                Requirements::themedCSS('client/css/Printer' . str_replace('\\', '', $dataObject->ClassName));
                $printResult .= $dataObject->renderWith('SilverCart/Printer/' . str_replace('\\', '', $dataObject->ClassName));
            }
        }
        return $printResult;
    }
    
}