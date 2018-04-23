<?php

namespace SilverCart\Model\Widgets;

use SilverStripe\ORM\ArrayList;
use SilverCart\Model\Widgets\WidgetTools;

/**
 * Interface for a ProductSliderWidget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait ProductSliderWidgetController {

    /**
     * Product elements
     *
     * @var ArrayList 
     */
    protected $elements = null;
    
    /**
     * Returns the elements
     *
     * @return ArrayList
     */
    public function getElements() {
        return $this->elements;
    }

    /**
     * Sets the elements
     *
     * @param ArrayList $elements Elements to set
     * 
     * @return void
     */
    public function setElements(ArrayList $elements) {
        $this->elements = $elements;
    }
    
    /**
     * Insert the javascript necessary for the anything slider.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function initAnythingSlider() {
        WidgetTools::initAnythingSliderForProductSliderWidget($this);
    }
    
    /**
     * Insert the javascript necessary for the roundabout slider.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    public function initRoundabout() {
        WidgetTools::initRoundaboutForProductSliderWidget($this);
    }
    
}