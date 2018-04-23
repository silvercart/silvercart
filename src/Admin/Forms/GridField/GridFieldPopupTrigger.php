<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\View\Requirements;

/**
 * Similar to {@link GridFieldConfig}, but adds some static helper methods.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField_Components
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldPopupTrigger implements GridField_HTMLProvider {

    /**
     * Target URL
     *
     * @var string
     */
    protected $targetURL;

    /**
     * Sets the defaults.
     * 
     * @param string $targetURL The target URL to load
     * 
     * @return GridFieldPopupTrigger
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function __construct($targetURL) {
        $this->targetURL = $targetURL;
    }
    
    /**
     * Returns the target URL
     * 
     * @return string
     */
    public function getTargetURL() {
        return $this->targetURL;
    }

    /**
     * Sets the target URL
     * 
     * @param string $targetURL The target URL to set
     * 
     * @return void
     */
    public function setTargetURL($targetURL) {
        $this->targetURL = $targetURL;
    }
    
    /***************************************************************************
     * GridField_HTMLProvider
     ***************************************************************************/

    /**
     * Adds a div which holds a url in its rel tag
     * 
     * @param GridField $gridField GridField to add fragments to
     * 
     * @return array
     */
    public function getHTMLFragments($gridField) {
        Requirements::javascript('silvercart/silvercart:client/admin/javascript/GridFieldPopupTrigger.js');
        return array(
            'after' => sprintf(
                    '<div class="sc-grid-field-popup-trigger" rel="%s"></div>',
                    $this->getTargetURL()
            ),
        );
    }

}