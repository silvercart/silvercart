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
 * @subpackage Tasks
 */

/**
 * Basic task functionallity to handle cli args
 *
 * @package Silvercart
 * @subpackage Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 13.07.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartXmlExportTask extends SilvercartTask {
    
    /**
     * Filter for export
     *
     * @var string
     */
    public static $export_filter = '';
    
    /**
     * Marker for export
     *
     * @var string
     */
    public static $export_marker = '';
    
    /**
     * Object name for export
     *
     * @var string
     */
    public static $export_objectName = '';

    /**
     * Relation depth for export
     *
     * @var int
     */
    public static $export_relationDepth = 1;

    /**
     * Relation depth to show detailed XML data for export
     *
     * @var int
     */
    public static $export_relationDetailDepth = 0;
    
    /**
     * Target dir for export
     *
     * @var string
     */
    public static $export_targetDir = '';

    /**
     * Init
     *
     * @return mixed
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function init() {
        $result = parent::init();
        
        $printHelp              = $this->getCliArg('--help');
        $relationDepth          = $this->getCliArg('-d');
        $relationDetailDepth    = $this->getCliArg('-e');
        $filter                 = $this->getCliArg('-f');
        $marker                 = $this->getCliArg('-m');
        $objectName             = $this->getCliArg('-o');
        $targetDir              = $this->getCliArg('-t');
        
        if (!$printHelp) {
            if (is_null($objectName)) {
                self::add_error('Caution: no object name is set! Please set an object name by using the -o=objectName option!');
                $printHelp = true;
            }

            $this->printErrors();
        }
        
        if ($printHelp) {
            $this->printHelp(true);
        } else {
            if (!is_null($filter)) {
                self::set_export_filter($filter);
            }
            if (!is_null($marker)) {
                self::set_export_marker($marker);
            }
            if (!is_null($targetDir)) {
                self::set_export_targetDir($targetDir);
            }
            if (!is_null($relationDepth)) {
                self::set_export_relationDepth($relationDepth);
            }
            if (!is_null($relationDetailDepth)) {
            self::set_export_relationDetailDepth($relationDetailDepth);
            }
            self::set_export_objectName($objectName);
            $this->doExport();
        }
        
        return $result;
    }
    
    /**
     * Executes the XML export
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    protected function doExport() {
        $this->loginSimulation();
        $filter = self::get_export_filter();
        $marker = self::get_export_marker();
        if (!empty($marker)) {
            if (!empty($filter)) {
                $filter .= ' AND ';
            }
            $filter .= sprintf(
                    "`%s`.`%s` = 1",
                    self::get_export_objectName(),
                    $marker
            );
        }
        $set = DataObject::get(
                self::get_export_objectName(),
                $filter
        );
        
        $formatter = new SilvercartXMLDataFormatter();
        $formatter->setRelationDepth(       self::get_export_relationDepth());
        $formatter->setRelationDetailDepth( self::get_export_relationDetailDepth());
        $formatter->setTotalSize($set->Count());
        $xml = $formatter->convertDataObjectSet($set);
        
        $xmlFileName = 'export_xml_' . self::get_export_objectName() . '_' . date('Y-m-d_H-i-s') . '.xml';
        $xmlFilePath = self::get_export_targetDir() . '/' . $xmlFileName;
        file_put_contents($xmlFilePath, $xml);
        print PHP_EOL;
        print "\033[42m" . " Export finished " . "\033[0m";
        print PHP_EOL;
        print PHP_EOL;
        exit();
    }

    /**
     * Prints the help
     *
     * @param bool $exit Should the programm exit after printing the help?
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function printHelp($exit = false) {
        $tab    = "\t";
        $help   = PHP_EOL;
        $help   .= 'Usage:' . PHP_EOL;
        $help   .= $tab . 'sake SilvercartXmlExportTask -o=[objectName] [options]' . PHP_EOL;
        $help   .= PHP_EOL;
        $help   .= 'Options:' . PHP_EOL;
        $help   .= $tab . '--help'  . $tab  . $tab  . $tab . 'Prints this help to the output' . PHP_EOL;
        $help   .= $tab . '-d=relationDepth'        . $tab . 'Depth of relation chain to include into the export XML file (default:' . self::get_export_relationDepth() . ')' . PHP_EOL;
        $help   .= $tab . '-e=relationDetailDepth'  . $tab . 'Depth of relation chain to show XML details (default:' . self::get_export_relationDetailDepth() . ')' . PHP_EOL;
        $help   .= $tab . '-f=filter'       . $tab  . $tab . 'Filter for the objects to export' . PHP_EOL;
        $help   .= $tab . '-m=marker'       . $tab  . $tab . 'Marker to indicate which objects should be exported.' . PHP_EOL;
        $help   .= $tab .       $tab        . $tab  . $tab . 'After writing the export file, the marker will be set to true and the object will be written.' . PHP_EOL;
        $help   .= $tab . '-o=objectName'   . $tab  . $tab . 'Name of the object to export' . PHP_EOL;
        $help   .= $tab . '-t=targetDir'    . $tab  . $tab . 'Directory to write XML export files to (default:' . self::get_export_targetDir() . ')' . PHP_EOL;
        $help   .= PHP_EOL;
        print $help;
        if ($exit) {
            exit();
        }
    }

    /**
     * Returns the export filter
     *
     * @return string
     */
    public static function get_export_filter() {
        return self::$export_filter;
    }
    
    /**
     * Sets the export filter
     *
     * @param string $export_filter Export filter
     * 
     * @return void
     */
    public static function set_export_filter($export_filter) {
        self::$export_filter = $export_filter;
    }

    /**
     * Returns the export marker
     *
     * @return string
     */
    public static function get_export_marker() {
        return self::$export_marker;
    }
    
    /**
     * Sets the export marker
     *
     * @param string $export_marker Export marker
     * 
     * @return void
     */
    public static function set_export_marker($export_marker) {
        self::$export_marker = $export_marker;
    }

    /**
     * Returns the export object name
     *
     * @return string
     */
    public static function get_export_objectName() {
        return self::$export_objectName;
    }
    
    /**
     * Sets the export object name
     *
     * @param string $export_objectName Export object name
     * 
     * @return void
     */
    public static function set_export_objectName($export_objectName) {
        self::$export_objectName = $export_objectName;
    }

    /**
     * Returns the export relation depth
     *
     * @return string
     */
    public static function get_export_relationDepth() {
        return self::$export_relationDepth;
    }
    
    /**
     * Sets the export relation depth
     *
     * @param string $export_relationDepth Export relation depth
     * 
     * @return void
     */
    public static function set_export_relationDepth($export_relationDepth) {
        self::$export_relationDepth = $export_relationDepth;
    }

    /**
     * Returns the export relation detail depth
     *
     * @return string
     */
    public static function get_export_relationDetailDepth() {
        return self::$export_relationDetailDepth;
    }
    
    /**
     * Sets the export relation detail depth
     *
     * @param string $export_relationDetailDepth Export relation detail depth
     * 
     * @return void
     */
    public static function set_export_relationDetailDepth($export_relationDetailDepth) {
        self::$export_relationDetailDepth = $export_relationDetailDepth;
    }
    
    /**
     * Returns the export target dir
     *
     * @return string
     */
    public static function get_export_targetDir() {
        if (empty(self::$export_targetDir)) {
            self::$export_targetDir = Director::baseFolder() . '/silvercart/export';
        }
        return self::$export_targetDir;
    }
    
    /**
     * Sets the export target dir
     *
     * @param string $export_targetDir Export target dir
     * 
     * @return void
     */
    public static function set_export_targetDir($export_targetDir) {
        self::$export_targetDir = $export_targetDir;
    }
    
}