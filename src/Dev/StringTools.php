<?php

namespace SilverCart\Dev;

use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Parsers\Transliterator;

/**
 * Provides string related tools.
 *
 * @package SilverCart
 * @subpackage Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.04.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class StringTools
{
    /**
     * Takes the given string and puts it into a DBHTMLText object to render properly in a 
     * template.
     * 
     * @param string $string String to convert.
     * 
     * @return DBHTMLText
     */
    public static function string2html(string $string) : DBHTMLText
    {
        return DBHTMLText::create()->setValue($string);
    }

    /**
     * Remove chars from the given string that are not appropriate for an url
     *
     * @param string $originalString String to convert
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.04.2014
     */
    public static function string2urlSegment(string $originalString) : string
    {
        if (function_exists('mb_strtolower')) {
            $string = mb_strtolower($originalString);
        } else {
            $string = strtolower($originalString);
        }
        $transliterator = Transliterator::create();
        $string         = $transliterator->toASCII($string);
        $string         = str_replace('&amp;', '-and-', $string);
        $string         = str_replace('&', '-and-', $string);
        $string         = preg_replace('/[^A-Za-z0-9]+/', '-', $string);

        if (!$string
         || $string == '-'
         || $string == '-1'
        ) {
            if (function_exists('mb_strtolower')) {
                $string = mb_strtolower($originalString);
            } else {
                $string = strtolower($originalString);
            }
        }
        $string = trim($string, '-');
        self::replace_special_chars($string);
        self::replace_cyrillic_chars($string);
        return urlencode($string);
    }
    
    /**
     * Replaces special chars.
     * 
     * @param string &$string String reference to replace special chars for
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2014
     */
    public static function replace_special_chars(string &$string) : void
    {
        $remove  = ['ä',  'ö',  'ü',  'Ä',  'Ö',  'Ü',  '/', '?', '&', '#', '.', ',', ' ', '%', '"', "'", '<', '>'];
        $replace = ['ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', '-', '-', '-', '-', '-', '-', '-', '',  '',  '',  '',  ''];
        $string  = str_replace($remove, $replace, $string);
    }
    
    /**
     * Replaces cyrillic chars with latin chars
     * 
     * @param string &$string String reference to replace cyrillic chars for
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.03.2014
     */
    public static function replace_cyrillic_chars(string &$string) : void
    {
        $remove  = ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'];
        $replace = ['a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'shh', '-', 'y', '-', 'e-', 'yu', 'ya'];
        $string  = str_replace($remove, $replace, $string);
    }
    
    /**
     * Calls html_entity_decode on $string and then converts all numeric entities.
     * 
     * @param string $string String to decode
     * 
     * @return string
     */
    public static function htmlEntityDecode(string $string) : string
    {
        $decoded = html_entity_decode($string);
        return preg_replace_callback("/(&#[0-9]+;)/", function($match) { return mb_convert_encoding($match[1], "UTF-8", "HTML-ENTITIES"); }, $decoded);
    }
    
    /**
     * Removes all characters except A-Z, a-z, 0-9, ÄäÜüÖö, ' ' (space).
     * 
     * @param string $string String to remove characters from
     * 
     * @return string
     */
    public static function removeSpecialChars(string $string) : string
    {
        return preg_replace('/[^A-Za-z0-9ÄäÜüÖö ]/', '', $string);
    }
}