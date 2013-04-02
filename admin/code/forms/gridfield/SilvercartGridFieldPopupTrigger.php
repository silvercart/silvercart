<?php

/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_Components
 */

/**
 * Similar to {@link GridFieldConfig}, but adds some static helper methods.
 *
 * @package Silvercart
 * @subpackage Forms_GridField_Components
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 08.03.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldPopupTrigger implements GridField_HTMLProvider {

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
     * @return SilvercartGridFieldPopupTrigger
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
        Requirements::javascript(SilvercartTools::getBaseURLSegment() . 'silvercart/admin/javascript/SilvercartGridFieldPopupTrigger.js');
        return array(
            'after' => sprintf(
                    '<div class="sc-grid-field-popup-trigger" rel="%s"></div>',
                    $this->getTargetURL()
            ),
        );
    }

}