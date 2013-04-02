<?php
/**
 * Copyright 2012 pixeltricks GmbH
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
 * @subpackage Widgets
 */

/**
 * Interface for a SilvercartProductSliderWidget.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.03.2012
 * @license see license file in modules root directory
 * @copyright 2012 pixeltricks GmbH
 */
interface SilvercartProductSliderWidget {
    
    /**
     * Returns the slider tab input fields for this widget.
     * 
     * @param TabSet &$rootTabSet The root tab set
     * 
     * @return FieldList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function getCMSFieldsSliderTab(&$rootTabSet);
    
    /**
     * Returns the slider tab input fields for this widget.
     * 
     * @param TabSet &$rootTabSet The root tab set
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function getCMSFieldsRoundaboutTab(&$rootTabSet);
    
    /**
     * We set checkbox field values here to false if they are not in the post
     * data array.
     *
     * @param array $data The post data array
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function populateFromPostData($data);
    
}
/**
 * Interface for a SilvercartProductSliderWidget's controller
 * 
 * @package Silvercart
 * @subpackage Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 28.03.2012
 * @license see license file in modules root directory
 */
interface SilvercartProductSliderWidget_Controller {
    
    /**
     * Returns the elements
     *
     * @return ArrayList
     */
    public function getElements();

    /**
     * Sets the elements
     *
     * @param ArrayList $elements Elements to set
     * 
     * @return void
     */
    public function setElements(ArrayList $elements);
    
    /**
     * Insert the javascript necessary for the anything slider.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function initAnythingSlider();
    
    /**
     * Insert the javascript necessary for the roundabout slider.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function initRoundabout();
    
    /**
     * Returns a number of bargain products.
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function Elements();
    
    /**
     * Returns a number of bargain products.
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function ProductPages();
}