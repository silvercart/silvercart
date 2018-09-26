<?php

namespace SilverCart\Dev;

use SilverCart\Dev\Tools;

/**
 * Provides methods for seo tasks in SilverCart.
 *
 * @package SilverCart
 * @subpackage Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 25.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SeoTools extends Tools
{
    /**
     * Max legth of meta description
     *
     * @var int
     */
    public static $metaDescriptionMaxLength = 156;
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
    public static $metaKeywordExtensions = [];
    /**
     * List of words to remove out of keywords
     *
     * @var array
     */
    public static $metaKeywordRemovements = [
        'und',
        'and',
        'für',
        'for',
        'mit',
        'with',
    ];
    
    /**
     * Returns a default meta title for the given page.
     * 
     * @param \SilverCart\Model\Pages\Page $page Page to get default meta title for
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.09.2018
     */
    public static function defaultMetaDescriptionFor($page)
    {
        return self::trimMetaDescription(self::string2html($page->Content)->Plain());
    }
    
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
    public static function extractMetaDescription($string)
    {
        $metaDescription    = $string;
        $metaDescription    = html_entity_decode($metaDescription, ENT_COMPAT, 'UTF-8');
        $metaDescription    = strip_tags($metaDescription);
        $metaDescription    = str_replace('"', '', $metaDescription);
        $lines              = explode(PHP_EOL, $metaDescription);
        $cleanedLines       = [];
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
    public static function extractMetaDescriptionOutOfArray($array)
    {
        $metaDescription      = '';
        $metaDescriptionParts = [];
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
     * @since 26.09.2018
     */
    public static function trimMetaDescription($metaDescription)
    {
        $metaDescription = preg_replace('/\s+/', ' ', trim(str_replace(PHP_EOL, " ", strip_tags(htmlspecialchars_decode($metaDescription)))));
        if (strlen($metaDescription) >= self::$metaDescriptionMaxLength) {
            $metaDescription    = wordwrap($metaDescription, self::$metaDescriptionMaxLength);
            $metaDescription    = trim(substr($metaDescription, 0, strpos($metaDescription, "\n")));
            $metaDescriptionRev = strrev($metaDescription);
            if (strpos($metaDescriptionRev, ',') === 0) {
                $metaDescription = strrev(substr($metaDescriptionRev, 1));
            }
            $metaDescription .= '...';
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
    public static function extractMetaKeywords($string)
    {
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
    public static function extractMetaKeywordsAsArray($string)
    {
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
    public static function addMetaKeywordExtension($metaKeywordExtension)
    {
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
    public static function addMetaKeywordExtensions($metaKeywordExtensions)
    {
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
    public static function addMetaKeywordRemovement($metaKeywordRemovement)
    {
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
    public static function addMetaKeywordRemovements($metaKeywordRemovements)
    {
        foreach ($metaKeywordRemovements as $metaKeywordRemovement) {
            self::addMetaKeywordRemovement($metaKeywordRemovement);
        }
    }
}