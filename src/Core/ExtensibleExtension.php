<?php

namespace SilverCart\Core;

use ReflectionMethod;

/**
 * 
 * 
 * @package SilverCart
 * @subpackage Core
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.03.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\View\ViewableData $owner Owner
 */
trait ExtensibleExtension
{
    /**
     * Lsit of custom callback methods.
     * 
     * @var string[]
     */
    protected $customAddCallbackMethodList = [];
    
    /**
     * Adds a callback for the given $method name. If $extensionClass is not given
     * self::class will be used as fallback.
     * 
     * @param string $method         Method name to add callback for
     * @param string $extensionClass Extension class to use (optional)
     * 
     * @return void
     */
    public function customAddCallbackMethod(string $method, string $extensionClass = null) : void
    {
        if (!$this->owner->hasMethod('addCallbackMethod')) {
            return;
        }
        $reflection = new ReflectionMethod($this->owner, 'addCallbackMethod');
        if (!$reflection->isPublic()) {
            return;
        }
        $extensionClass    = $extensionClass === null ? self::class : $extensionClass;
        $customCallbackKey = "{$extensionClass}-{$method}";
        if (array_key_exists($customCallbackKey, $this->customAddCallbackMethodList)) {
            return;
        }
        $this->customAddCallbackMethodList[$customCallbackKey] = true;
        $this->owner->addCallbackMethod($method, function ($inst, $args) use ($method, $extensionClass) {
            /** @var Extensible $inst */
            $extension = $inst->getExtensionInstance($extensionClass);
            if (!$extension) {
                return null;
            }
            try {
                $extension->setOwner($inst);
                return call_user_func_array([$extension, $method], $args);
            } finally {
                $extension->clearOwner();
            }
        });
    }
}