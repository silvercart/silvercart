<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Widgets\LoginWidgetForm;
use SilverCart\Model\Widgets\WidgetController;
use SilverStripe\i18n\i18n;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

/**
 * LoginWidget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class LoginWidgetController extends WidgetController {
    
    /**
     * Returns the "My Account" page object.
     * 
     * @return MyAccountHolder
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function MyAccountPage() {
        return Tools::PageByIdentifierCode(Page::IDENTIFIER_MY_ACCOUNT_HOLDER);
    }
    
    /**
     * Creates the cache key for this widget.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 02.07.2012
     */
    public function WidgetCacheKey() {
        $key = i18n::get_locale();
        $customer = Security::getCurrentUser();
        if ($customer instanceof Member) {
            $key .= '_' . $customer->ID;
        }
        
        if ((int) $key > 0) {
            $permissions = $this->MyAccountPage()->Children()->map('ID', 'CanView');

            foreach ($permissions as $pageID => $permission) {
                $key .= '_' . $pageID . '-' . ((string) $permission);
            }
        }
        
        return $key;
    }
    
    /**
     * Returns the LoginWidgetForm.
     * 
     * @return LoginWidgetForm
     */
    public function getLoginWidgetForm() {
        $form = new LoginWidgetForm($this);
        return $form;
    }
    
}