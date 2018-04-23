<?php

namespace SilverCart\Security\MemberAuthenticator;

use SilverCart\Checkout\Checkout;
use SilverStripe\Core\Extension;

/**
 * Extension for the LogoutHandler.
 *
 * @package SilverCart
 * @subpackage Security
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 23.11.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class LogoutHandlerExtension extends Extension {
    
    /**
     * Clears some SilverCart session data after logging out.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2017
     */
    public function afterLogout() {
        Checkout::clear_session();
    }
    
}