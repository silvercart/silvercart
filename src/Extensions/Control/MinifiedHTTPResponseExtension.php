<?php

namespace SilverCart\Extensions\Control;

use SilverCart\Control\MinifiedHTTPResponse;
use SilverCart\Dev\Tools;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Extension;

/**
 * Extension to use MinifiedHTTPResponse instead of HTTPResponse.
 * 
 * @package SilverCart
 * @subpackage Extensions_Control
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 20.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property Controller $owner Owner
 */
class MinifiedHTTPResponseExtension extends Extension
{
    /**
     * Initializes the controller with a response of type MinifiedHTTPResponse.
     * 
     * @return void
     */
    public function onBeforeInit() : void
    {
        if (Tools::isBackendEnvironment()) {
            return;
        }
        if (Director::isLive()) {
            if ($this->owner instanceof Controller) {
                $this->owner->setResponse(MinifiedHTTPResponse::create());
            }
        }
    }
}