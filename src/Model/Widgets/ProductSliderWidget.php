<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Widgets\WidgetTools;

/**
 * Trait for a ProductSliderWidget.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait ProductSliderWidget {
    
    /**
     * Returns the slider tab input fields for this widget.
     * 
     * @param TabSet $rootTabSet The root tab set
     * 
     * @return FieldList
     */
    public function getCMSFieldsSliderTab($rootTabSet) {
        WidgetTools::getCMSFieldsSliderToggleForSliderWidget($this, $rootTabSet);
    }
    
    /**
     * Returns the slider tab input fields for this widget.
     * 
     * @param TabSet $rootTabSet The root tab set
     * 
     * @return void
     */
    public function getCMSFieldsRoundaboutTab($rootTabSet) {
        WidgetTools::getCMSFieldsRoundaboutTabForProductSliderWidget($this, $rootTabSet);
    }
    
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
    public function populateFromPostData($data) {
        WidgetTools::populateFromPostDataForProductSliderWidget($this, $data);
        parent::populateFromPostData($data);
    }
    
}