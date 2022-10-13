<?php

namespace SilverCart\Dev;

use Psr\SimpleCache\CacheInterface;
use SilverStripe\Core\Injector\Injector;

/**
 * Tools for caching purposes.
 * 
 * @package SilverCart
 * @subpackage Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.10.2022
 * @copyright 2022 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CacheTools
{
    use \SilverStripe\Core\Config\Configurable;
    /**
     * Default cache namespace
     * 
     * @var string
     */
    private static $default_namespace = 'cacheblock';
    
    /**
     * Sets the given $content with the given $cacheKey to the cache with the 
     * given $namespace.
     * @see self::config()->default_namespace
     * 
     * @param string                                  $cacheKey  Cache key
     * @param bool|int|float|array|object|string|null $content   Content to store in cache
     * @param string|null                             $namespace Cache namespace
     * 
     * @return void
     */
    public static function set(string $cacheKey, bool|int|float|array|object|string|null $content, string|null $namespace = null) : void
    {
        self::getCache($namespace)->set($cacheKey, $content);
    }
    
    /**
     * Returns the cached content with the given $cacheKey out of the cache with 
     * the given $namespace.
     * @see self::config()->default_namespace
     * 
     * @param string      $cacheKey  Cache key
     * @param string|null $namespace Cache namespace
     * 
     * @return bool|int|float|array|object|string|null
     */
    public static function get(string $cacheKey, string|null $namespace = null) : bool|int|float|array|object|string|null
    {
        return self::getCache($namespace)->get($cacheKey);
    }
    
    /**
     * Returns the cache with the given $namespace.
     * @see self::config()->default_namespace
     * 
     * @param string|null $namespace Cache namespace
     * 
     * @return CacheInterface
     */
    public static function getCache(string|null $namespace = null) : CacheInterface
    {
        if ($namespace === null) {
            $namespace = self::config()->default_namespace;
        }
        return Injector::inst()->get(CacheInterface::class . ".{$namespace}");
    }
}