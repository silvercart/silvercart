<?php

namespace SilverCart\ORM;

use SilverStripe\Core\Config\Config as SilverStripeConfig;
use SilverStripe\Forms\FormField;

/**
 * Trait to add extende Extensible features to a DataObject.
 * 
 * @package SilverCart
 * @subpackage ORM
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait ExtensibleDataObject
{
    /**
     * Default field labels.
     *
     * @var array
     */
    protected $defaultFieldLabels = [];
    
    /**
     * Allows user code to hook into DataObject::requireDefaultRecords() prior 
     * to requireDefaultRecords being called on extensions.
     *
     * @param callable $callback The callback to execute
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.09.2018
     */
    protected function beforeRequireDefaultRecords($callback) : void
    {
        $this->beforeExtending('requireDefaultRecords', $callback);
    }

    /**
     * Allows user code to hook into DataObject::getCMSActions prior to
     * updateCMSActions being called on extensions.
     *
     * @param callable $callback The callback to execute
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2018
     */
    protected function beforeUpdateCMSActions($callback) : void
    {
        $this->beforeExtending('updateCMSActions', $callback);
    }
    
    /**
     * Allows user code to hook into DataObject::fieldLabels() prior to 
     * updateFieldLabels being called on extensions.
     *
     * @param callable $callback The callback to execute
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2018
     */
    protected function beforeUpdateFieldLabels($callback) : void
    {
        $this->beforeExtending('updateFieldLabels', $callback);
    }
    
    /**
     * Allows user code to hook into DataObject::updateProvidePermissions() prior 
     * to providePermissions being called on extensions.
     *
     * @param callable $callback The callback to execute
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.09.2020
     */
    protected function beforeUpdateProvidePermissions(callable $callback) : void
    {
        $this->beforeExtending('updateProvidePermissions', $callback);
    }
    
    /**
     * Returns the default field labels.
     * 
     * @param bool  $includerelations Include relations?
     * @param array $additionalLabels Additional field labels to add
     * 
     * @return array
     */
    public function defaultFieldLabels($includerelations = true, array $additionalLabels = []) : array
    {
        $cacheKey = static::class . '_' . $includerelations;
        if (!isset(self::$_cache_field_labels[$cacheKey])) {
            $this->beforeUpdateFieldLabels(function(&$labels) use ($additionalLabels) {
                $labels = array_merge(
                        $labels,
                        $this->scaffoldFieldLabels(),
                        $additionalLabels
                );
            });
        }
        return parent::fieldLabels($includerelations);
    }
    
    /**
     * Resets the field label cache.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.09.2020
     */
    public static function reset_field_labels() : void
    {
        self::$_cache_field_labels = [];
    }
    
    /**
     * Returns the default field labels for this DataObject.
     * 
     * @var string $objectName Optional context object name
     * 
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2018
     */
    protected function scaffoldFieldLabels(string $objectName = '') : array
    {
        if (empty($objectName)) {
            $objectName = self::class;
        }
        if (!array_key_exists($objectName, $this->defaultFieldLabels)
         || empty($this->defaultFieldLabels[$objectName])
        ) {
            $fieldLabels = [];
            $params      = ['db', 'casting', 'has_one', 'has_many', 'many_many', 'belongs_many_many'];
            foreach ($params as $param) {
                if (method_exists($objectName, 'config')) {
                    $source = $objectName::config()->uninherited($param);
                } else {
                    $source = SilverStripeConfig::inst()->get($objectName, $param);
                }
                if (is_array($source)) {
                    foreach (array_keys($source) as $fieldname) {
                        $fieldLabels[$fieldname]               = _t("{$objectName}.{$fieldname}", $fieldname);
                        $fieldLabels["{$fieldname}Desc"]       = _t("{$objectName}.{$fieldname}Desc", FormField::name_to_label("{$fieldname}Desc"));
                        $fieldLabels["{$fieldname}Default"]    = _t("{$objectName}.{$fieldname}Default", FormField::name_to_label("{$fieldname}Default"));
                        $fieldLabels["{$fieldname}RightTitle"] = _t("{$objectName}.{$fieldname}RightTitle", FormField::name_to_label("{$fieldname}RightTitle"));
                        if ($fieldLabels[$fieldname] === $fieldname) {
                            if ($param === 'has_one') {
                                $className = $source[$fieldname];
                                $fieldLabels[$fieldname] = $className::singleton()->singular_name();
                            } elseif ($param === 'has_many'
                                   || $param === 'many_many'
                                   || $param === 'belongs_many_many'
                            ) {
                                $className = $source[$fieldname];
                                if (strpos($className, '.') !== false) {
                                    $parts = explode('.', $className);
                                    $className = array_shift($parts);
                                }
                                $fieldLabels[$fieldname] = $className::singleton()->plural_name();
                            }
                        }
                    }
                }
            }
            $this->defaultFieldLabels[$objectName] = $fieldLabels;
        }
        return $this->defaultFieldLabels[$objectName];
    }
}