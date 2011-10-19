<?php
/**
 * Injects CustomHtmlForm objects into the security controller
 *
 * @package Silvercart
 * @subpacke Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 15.10.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartSecurityController extends DataObjectDecorator {
    
    /**
     * We register the common forms for SilvercartPages here.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.10.2011
     */
    public function onBeforeInit() {
        if (!isset($_SESSION['Silvercart'])) {
            $_SESSION['Silvercart'] = array();
        }
        if (!isset($_SESSION['Silvercart']['errors'])) {
            $_SESSION['Silvercart']['errors'] = array();
        }
        
        $this->owner->registerCustomHtmlForm('SilvercartQuickSearchForm', new SilvercartQuickSearchForm($this->owner));
        $this->owner->registerCustomHtmlForm('SilvercartQuickLoginForm',  new SilvercartQuickLoginForm($this->owner));
        
        SilvercartPlugin::call($this->owner, 'init', array($this->owner));
    }
}