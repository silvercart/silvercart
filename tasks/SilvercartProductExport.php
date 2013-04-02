<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Tasks
 */

/**
 * Calls all due exports.
 *
 * This task should be called via sake on the command line:
 * "sake /SilvercartProductExport"
 *
 * You can set the following parameters:
 * "getAll": generate all product exports regardless of their update interval settings
 *
 * Example with parameters:
 *
 *   Generate all exports regardless of their update interval settings:
 *      sake /SilvercartProductExport getAll
 *
 * @package Silvercart
 * @subpackage Tasks
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.07.2011
 * @license see license file in modules root directory
 */
class SilvercartProductExport extends ScheduledTask {
    
    /**
     * This method gets called from sake.
     * 
     * You can give the argument "getAll" to generate all product exports
     * regardless of their update interval settings.
     *
     * It starts the export of all due SilvercartProductExporter objects.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.07.2011
     */
    public function process() {
        $getAll = false;
        
        if (isset($_GET['args']) &&
            is_array($_GET['args'])) {
            
            foreach ($_GET['args'] as $argument) {
                if ($argument == 'getAll') {
                    $getAll = true;
                }
            }
        }
        
        $referenceCurrentTimeStamp  = time();
        $silvercartProductExporters = $this->getDueSilvercartProductExporters($referenceCurrentTimeStamp, $getAll);
        
        foreach ($silvercartProductExporters as $silvercartProductExporter) {
            $silvercartProductExporter->doExport($referenceCurrentTimeStamp);
        }
        
        return true;
    }
    
    /**
     * This method returns an array containing all SilvercartProductExporters
     * that should be run now (according to their update interval settings).
     *
     * @param Int     $referenceCurrentTimeStamp The timestamp to use as reference
     *                                           for calculating if an update is due.
     * @param Boolean $getAll                    Indicates wether all exporters should be returned or
     *                                           only those whose update intervals are due.
     * 
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2013
     */
    protected function getDueSilvercartProductExporters($referenceCurrentTimeStamp, $getAll = false) {
        $dueSilvercartProductExporters = array();
        $conversionTable               = array(
            'Minutes'           => 1,
            'Hours'             => 60,
            'Days'              => 1440,
            'Weeks'             => 10080,
            'Months'            => 312480,
            'Years'             => 3749760
        );
        
        $silvercartProductExporters = DataObject::get(
            'SilvercartProductExporter',
            "isActive = 1"
        );
        if ($silvercartProductExporters instanceof DataObjectSet) {
            foreach ($silvercartProductExporters as $silvercartProductExport) {
                if ($getAll) {
                    $dueSilvercartProductExporters[] = $silvercartProductExport;
                } else {
                    // Get difference in minutes from now to the last export
                    $lastExportTimeStamp = strtotime($silvercartProductExport->lastExportDateTime);
                    $timeStampDifference = $referenceCurrentTimeStamp - $lastExportTimeStamp;
                    $differenceInMinutes = $this->time_duration($timeStampDifference, 'm');

                    // Get update interval in minutes
                    $intervalLengthInMinutes = $silvercartProductExport->updateInterval * $conversionTable[$silvercartProductExport->updateIntervalPeriod];
                    
                    if ((int) str_replace(' minutes', '', $differenceInMinutes) >= $intervalLengthInMinutes) {
                        $dueSilvercartProductExporters[] = $silvercartProductExport;
                    }
                }
            }
        }
        
        return $dueSilvercartProductExporters;
    }
    
    /**
     * A function for making time periods readable
     * 
     * @param int    $seconds number of seconds elapsed
     * @param string $use     which time periods to display
     * @param bool   $zeros   whether to show zero time periods
     * 
     * @return string
     * 
     * @author Aidan Lister <aidan@php.net>
     * @version 2.0.1
     * @link http://aidanlister.com/2004/04/making-time-periods-readable/
     * @since 08.07.2011
     */
    protected function time_duration($seconds, $use = null, $zeros = false) {
        // Define time periods
        $periods = array (
            'years'     => 31556926,
            'Months'    => 2629743,
            'weeks'     => 604800,
            'days'      => 86400,
            'hours'     => 3600,
            'minutes'   => 60,
            'seconds'   => 1
            );

        // Break into periods
        $seconds = (float) $seconds;
        $segments = array();
        foreach ($periods as $period => $value) {
            if ($use && strpos($use, $period[0]) === false) {
                continue;
            }
            $count = floor($seconds / $value);
            if ($count == 0 && !$zeros) {
                continue;
            }
            $segments[strtolower($period)] = $count;
            $seconds = $seconds % $value;
        }

        // Build the string
        $string = array();
        foreach ($segments as $key => $value) {
            $segment_name = substr($key, 0, -1);
            $segment = $value . ' ' . $segment_name;
            if ($value != 1) {
                $segment .= 's';
            }
            $string[] = $segment;
        }

        return implode(', ', $string);
    }
}