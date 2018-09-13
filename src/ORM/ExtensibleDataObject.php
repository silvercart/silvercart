<?php

namespace SilverCart\ORM;

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
    protected function beforeRequireDefaultRecords($callback) {
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
    protected function beforeUpdateCMSActions($callback)
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
    protected function beforeUpdateFieldLabels($callback) {
        $this->beforeExtending('updateFieldLabels', $callback);
    }
}