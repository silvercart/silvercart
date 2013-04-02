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
 * @subpackage Base
 */

/**
 * Provides methods for seo tasks in SilverCart.
 * 
 * @package Silvercart
 * @subpackage Base
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 05.06.2012
 * @license see license file in modules root directory
 */
class SilvercartSeoTools extends SilvercartTools {
    
    /**
     * Max legth of meta description
     *
     * @var int
     */
    public static $metaDescriptionMaxLength = 200;
    
    /**
     * Connector char for meta description parts
     *
     * @var string
     */
    public static $metaDescriptionConnector = '-';

    /**
     * List of words to extend to the keywords
     *
     * @var array
     */
    public static $metaKeywordExtensions = array();

    /**
     * List of words to remove out of keywords
     *
     * @var array
     */
    public static $metaKeywordRemovements = array(
        'und',
        'and',
        'für',
        'for',
        'mit',
        'with',
    );
    
    /**
     * Extracts a meta description out of the given string
     *
     * @param string $string String to extract meta description out of
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public static function extractMetaDescription($string) {
        $metaDescription    = $string;
        $metaDescription    = html_entity_decode($metaDescription, ENT_COMPAT, 'UTF-8');
        $metaDescription    = strip_tags($metaDescription);
        $metaDescription    = str_replace('"', '', $metaDescription);
        $lines              = explode(PHP_EOL, $metaDescription);
        $cleanedLines       = array();
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $cleanedLines[] = $line;
            }
        }
        $metaDescription    = implode(', ', $cleanedLines);
        $metaDescription    = str_replace(PHP_EOL, '', $metaDescription);
        while (strpos($metaDescription, '  ') !== false) {
            $metaDescription = str_replace('  ', ' ', $metaDescription);
        }
        $metaDescription = self::trimMetaDescription($metaDescription);
        return $metaDescription;
    }
    
    /**
     * Extracts a meta description out of the given string
     *
     * @param array $array List of strings to extract meta description out of
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public static function extractMetaDescriptionOutOfArray($array) {
        $metaDescription        = '';
        $metaDescriptionParts   = array();
        foreach ($array as $string) {
            if (mb_strlen($metaDescription) >= self::$metaDescriptionMaxLength) {
                break;
            }
            $metaDescriptionParts[] = self::extractMetaDescription($string);
        }
        $metaDescription = implode(' ' . self::$metaDescriptionConnector . ' ', $metaDescriptionParts);
        $metaDescription = self::trimMetaDescription($metaDescription);
        return $metaDescription;
    }
    
    /**
     * Trims the meta description to the max length without line breaks
     *
     * @param string $metaDescription Meta description to trim
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public static function trimMetaDescription($metaDescription) {
        if (strlen($metaDescription) >= self::$metaDescriptionMaxLength) {
            $metaDescription    = wordwrap($metaDescription, self::$metaDescriptionMaxLength);
            $metaDescription    = trim(substr($metaDescription, 0, strpos($metaDescription, "\n")));
            $metaDescriptionRev = strrev($metaDescription);
            if (strpos($metaDescriptionRev, ',') === 0) {
                $metaDescription = strrev(substr($metaDescriptionRev, 1));
            }
        }
        return $metaDescription;
    }

    /**
     * Extracts some meta key words out of the given string and returns them 
     * as a comma separated string
     *
     * @param string $string String to extract key words out of
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public static function extractMetaKeywords($string) {
        $metaKeywords = preg_replace('#[^a-z0-9]#i', ',', $string);
        foreach (self::$metaKeywordRemovements as $removement) {
            $removement = ',' . $removement . ',';
            $metaKeywords = str_replace($removement, ',', $metaKeywords);
        }
        foreach (self::$metaKeywordExtensions as $extension) {
            $extension = ',' . $extension;
            if (strpos($metaKeywords, $extension) === false) {
                $metaKeywords .= $extension;
            }
        }
        while (strpos($metaKeywords, ',,') !== false) {
            $metaKeywords = str_replace(',,', ',', $metaKeywords);
        }
        return $metaKeywords;
    }

    /**
     * Extracts some meta key words out of the given string and returns them 
     * as an array
     *
     * @param string $string String to extract key words out of
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public static function extractMetaKeywordsAsArray($string) {
        $extractedMetaKeywords = self::extractMetaKeywords($string);
        return explode(',', $extractedMetaKeywords);
    }
    
    /**
     * Adds a new meta key word extension string
     *
     * @param string $metaKeywordExtension String to extend to the key words
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public static function addMetaKeywordExtension($metaKeywordExtension) {
        if (!in_array($metaKeywordExtension, self::$metaKeywordExtensions)) {
            self::$metaKeywordExtensions[] = $metaKeywordExtension;
        }
    }
    
    /**
     * Adds a new meta key word extension list
     *
     * @param array $metaKeywordExtensions List to extend to the key words
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public static function addMetaKeywordExtensions($metaKeywordExtensions) {
        foreach ($metaKeywordExtensions as $metaKeywordExtension) {
            self::addMetaKeywordExtension($metaKeywordExtension);
        }
    }
    
    /**
     * Adds a new meta key word removement string
     *
     * @param string $metaKeywordRemovement String to remove out of key words
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public static function addMetaKeywordRemovement($metaKeywordRemovement) {
        if (!in_array($metaKeywordRemovement, self::$metaKeywordRemovements)) {
            self::$metaKeywordRemovements[] = $metaKeywordRemovement;
        }
    }
    
    /**
     * Adds a new meta key word removement list
     *
     * @param array $metaKeywordRemovements List to remove out of key words
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public static function addMetaKeywordRemovements($metaKeywordRemovements) {
        foreach ($metaKeywordRemovements as $metaKeywordRemovement) {
            self::addMetaKeywordRemovement($metaKeywordRemovement);
        }
    }
}
