<?php

namespace SilverCart\Model\Plugins;

use SilverCart\Model\Plugins\Plugin;
use SilverStripe\ORM\DataExtension;
/**
 * Methods for objects that want to provide plugin support.
 *
 * @package SilverCart
 * @subpackage Model_Plugins
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PluginObjectExtension extends DataExtension {
    
    /**
     * Passes through calls to Plugins.
     *
     * @param string $method The name of the method to call
     *
     * @return mixed
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.09.2011
     */
    public function Plugin($method) {
        return Plugin::call($this->owner, $method);
    }
}
