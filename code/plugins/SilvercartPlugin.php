<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Plugins
 */

/**
 * Base object providing general methods for all extending plugin-provider
 * objects.
 *
 * @package Silvercart
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 22.09.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartPlugin extends Object {
    
    /**
     * The object that called this plugin
     *
     * @var mixed
     */
    protected $callingObject = null;

    /**
     * Contains informations about calling objects for caching purposes.
     *
     * @var array
     */
    protected static $pluginProvidersForCallingObject = array();

    /**
     * Contains all registered plugin providers.
     *
     * @var array
     */
    public static $registeredPluginProviders = array();
    
    /**
     * Takes the calling object as argument and stores it in a class variable
     *
     * @param mixed $callingObject The calling object
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function __construct($callingObject) {
        parent::__construct();
        
        $this->callingObject = $callingObject;
    }
    
    /**
     * Registers a plugin provider for the given class.
     *
     * @param string $forObject               The class name of the object you want to provide with the plugin
     * @param string $pluginProviderClassName The class name of the plugin provider
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public static function registerPluginProvider($forObject, $pluginProviderClassName) {
        if (!array_key_exists($forObject, self::$registeredPluginProviders)) {
            self::$registeredPluginProviders[$forObject] = array();
        }
        
        self::$registeredPluginProviders[$forObject][] = array(
            'className' => $pluginProviderClassName,
            'object'    => null
        );
    }
    
    /**
     * Returns all extensions for the given class.
     *
     * @param string $className The name of the class for which you want the extensions
     *
     * @return array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public static function getExtensionsFor($className) {
        return self::get_static($className, 'extensions');
    }
    
    /**
     * Returns all extensions for the current class.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function getExtensions() {
        return self::getExtensionsFor($this->class);
    }
    
    /**
     * Returns the calling object.
     *
     * @return mixed
     */
    public function getCallingObject() {
        return $this->callingObject;
    }
    
    /**
     * The central method. Every Silvercart object calls this method to invoke
     * a plugin action.
     *
     * @param mixed   $callingObject            The object that performs the call
     * @param string  $methodName               The name of the method to call
     * @param array   $arguments                The arguments to pass
     * @param boolean $passArgumentsByReference Indicate wether the arguments should be passed by reference
     * @param mixed   $returnContainer          The container to gather the output. This can be e.g. a string if you want concatenated strings,
     *                                          an array or a ArrayList
     *
     * @return mixed
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 19.03.2014
     */
    public static function call($callingObject, $methodName, $arguments = array(), $passArgumentsByReference = false, $returnContainer = '') {
        if (!is_array($arguments)) {
            if ($passArgumentsByReference) {
                $arguments = array($arguments);
            } else {
                $arguments = array($arguments);
            }
        }

        if (array_key_exists($callingObject->class, self::$pluginProvidersForCallingObject)) {
            $pluginProviders = self::$pluginProvidersForCallingObject[$callingObject->class];
        } else {
            $pluginProviders = self::getPluginProvidersForObject($callingObject);
            self::$pluginProvidersForCallingObject[$callingObject->class] = $pluginProviders;
        }

        if ($pluginProviders) {
            foreach ($pluginProviders as $pluginProvider) {
                if (method_exists($pluginProvider, $methodName)) {
                    if ($passArgumentsByReference) {
                        if (is_array($returnContainer)) {
                            $returnContainer[] = $pluginProvider->$methodName($arguments, $callingObject);
                        } else if ($returnContainer instanceof SS_List) {
                            if ($returnContainer->count() === 0) {
                                $returnContainer = $pluginProvider->$methodName($arguments, $callingObject);
                            } else {
                                $returnContainer->merge($pluginProvider->$methodName($arguments, $callingObject));
                            }
                        } else if ($returnContainer == 'boolean') {
                            $returnContainer = $pluginProvider->$methodName($arguments,$callingObject);
                        } else if ($returnContainer == 'DataObject') {
                            $returnContainer = $pluginProvider->$methodName($arguments,$callingObject);
                        } else if ($returnContainer == 'ArrayList') {
                            $returnContainer = $pluginProvider->$methodName($arguments,$callingObject);
                        } else {
                            $result = $pluginProvider->$methodName($arguments,$callingObject);
                            if (is_string($result)) {
                                $returnContainer .= $result;
                            } else {
                                $returnContainer = $result;
                            }
                        }
                    } else {
                        if (is_array($returnContainer)) {
                            $returnContainer[] = $pluginProvider->$methodName($arguments, $callingObject);
                        } else if ($returnContainer instanceof SS_List) {
                            if ($returnContainer->count() === 0) {
                                $returnContainer = $pluginProvider->$methodName($arguments, $callingObject);
                            } else {
                                $returnContainer->merge($pluginProvider->$methodName($arguments, $callingObject));
                            }
                        } else if ($returnContainer == 'boolean') {
                            $returnContainer = $pluginProvider->$methodName($arguments, $callingObject);
                        } else if ($returnContainer == 'DataObject') {
                            $returnContainer = $pluginProvider->$methodName($arguments, $callingObject);
                        } else if ($returnContainer == 'ArrayList') {
                            $returnContainer = $pluginProvider->$methodName($arguments, $callingObject);
                        } else {
                            $result = $pluginProvider->$methodName($arguments, $callingObject);
                            if (is_string($result)) {
                                $returnContainer .= $result;
                            } else {
                                $returnContainer = $result;
                            }
                        }
                    }
                } else {
                    if ($returnContainer == 'boolean') {
                        $returnContainer = false;
                    } else if ($returnContainer == 'DataObject') {
                        $returnContainer = new DataObject();
                    } else if ($returnContainer == 'ArrayList') {
                        $returnContainer = new ArrayList();
                    }
                }
            }
        } else {
            $returnContainer = false;
        }
        
        return $returnContainer;
    }
    
    /**
     * Retrieves all plugin providers that belong to the given object.
     *
     * @param mixed $callingObject The object for which the plugin providers shall be retrieved
     *
     * @return array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public static function getPluginProvidersForObject($callingObject) {
        $pluginProviders = array();
        
        if (array_key_exists($callingObject->class, self::$registeredPluginProviders)) {
            foreach (self::$registeredPluginProviders[$callingObject->class] as $pluginProvider) {
                if (empty($pluginProvider['object'])) {
                    $pluginProviderClassName  = $pluginProvider['className'];
                    $pluginProvider['object'] = new $pluginProviderClassName($callingObject);
                }
                
                $pluginProviders[] = $pluginProvider['object'];
            }
        }
        
        return $pluginProviders;
    }
    
    // ------------------------------------------------------------------------
    // Base methods for plugin providers
    // ------------------------------------------------------------------------
    
    /**
     * Initialisation for plugin providers.
     *
     * @param array &$arguments     The arguments to pass
     * @param mixed &$callingObject The calling object
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function init(&$arguments = array(), &$callingObject) {
    }
    
    /**
     * Extension results consist of arrays. This method concatenates all array
     * entries into a string.
     *
     * @param array $extensionResultSet The result delivered by an extension
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function returnExtensionResultAsString($extensionResultSet) {
        $result = '';
        
        if (is_array($extensionResultSet)) {
            foreach ($extensionResultSet as $extensionResult) {
                $result .= $extensionResult;
            }
        }
        
        return $result;
    }
    
    /**
     * Extension results consist of arrays. This method concatenates all array
     * entries into a string, separated by <br/>.
     *
     * @param array  $extensionResultSet The result delivered by an extension
     * @param string $prefix             A prefix string to add
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    public function returnExtensionResultAsHtmlString($extensionResultSet, $prefix = '') {
        $result = '';
        if (is_array($extensionResultSet)) {
            foreach ($extensionResultSet as $key => $extensionResult) {
                if (!is_string($extensionResult) ||
                    strlen($extensionResult) == 0 ||
                    empty($extensionResult)) {
                    unset($extensionResultSet[$key]);
                }
            }
            $result = implode('<br/>' . $prefix, $extensionResultSet);
            if (!empty($result)) {
                $result = $prefix . $result;
            }
        }
        return $result;
    }
    
    /**
     * Extension results consist of arrays. This method concatenates all array
     * entries into a ArrayList.
     *
     * @param array $extensionResultSet The result delivered by an extension
     *
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.09.2012
     */
    public function returnExtensionResultAsArrayList($extensionResultSet) {
        $result = new ArrayList();
        
        if (is_array($extensionResultSet)) {
            foreach ($extensionResultSet as $extensionResult) {
                if ($extensionResult instanceof SS_List) {
                    $result->merge($extensionResult);
                } else {
                    $result->push($extensionResult);
                }
            }
        }
        return $result;
    }
    
    /**
     * This method has been renamed because ArrayList does not exist since SS 3.0.
     *
     * @param array $extensionResultSet The result delivered by an extension
     *
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 10.08.2012
     */
    public function returnExtensionResultAsDataObjectSet($extensionResultSet) {
        user_error("The method SilvercartPlugin::returnExtensionResultAsDataObjectSet() has been renamed. Please use SilvercartPlugin::returnExtensionResultAsArrayList() instead.", E_USER_ERROR);
    }
    
    /**
     * Extension results consist of potential null values. The first not null 
     * value will be returned.
     *
     * @param array $extensionResultSet The result delivered by an extension
     *
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.04.2012
     */
    public function returnFirstNotNull($extensionResultSet) {
        $result = null;
        
        if (is_array($extensionResultSet)) {
            foreach ($extensionResultSet as $extensionResult) {
                if (!is_null($extensionResult)) {
                    $result = $extensionResult;
                    break;
                }
            }
        }
        return $result;
    }
}
