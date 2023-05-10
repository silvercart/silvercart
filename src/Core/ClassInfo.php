<?php

namespace SilverCart\Core;

use SilverStripe\Core\ClassInfo as SilverStripeClassInfo;

/**
 * Extends the default SilverStripe ClassInfo to provide a better support for 
 * namespaces models.
 * 
 * Provides introspection information about the class tree.
 *
 * It's a cached wrapper around the built-in class functions.  SilverStripe uses
 * class introspection heavily and without the caching it creates an unfortunate
 * performance hit.
 * 
 * @package SilverCart
 * @subpackage Core
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.05.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ClassInfo extends SilverStripeClassInfo
{
    /**
     * Strip namespace from class
     *
     * @param string|object $nameOrObject Name of class, or instance
     * 
     * @return string
     */
    public static function longName(string $nameOrObject) : string
    {
        $name = static::class_name($nameOrObject);
        return str_replace('\\', '_', (string) $name);
    }
}