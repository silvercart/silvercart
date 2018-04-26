<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Widgets\WidgetController;
use SilverStripe\View\Requirements;

/**
 * SlidorionProductGroupWidget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SlidorionProductGroupWidgetController extends WidgetController {
    
    /**
     * Load javascript and css files.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    protected function init() {
        parent::init();
        Requirements::customScript(
            sprintf(
                "
                (function($) {
                    jQuery(document).ready(function(){
                        var slidorionSelector = '#silvercart-slidorion-%d';
                        $(slidorionSelector).slidorion({
                            speed:      %d,
                            interval:   %d,
                            effect:     '%s',
                            hoverPause: %s,
                            autoPlay:   %s
                        });
                        $(slidorionSelector + ' .silvercart-slidorion-slide-prev').click(function() {
                            var prevObj = $(slidorionSelector + ' .silvercart-slidorion-link-header.active').prevAll('.silvercart-slidorion-link-header');
                            if (prevObj.length == 0) {
                                prevObj = $(slidorionSelector + ' .silvercart-slidorion-link-header').last();
                            }
                            prevObj.trigger('click');
                        });
                        $(slidorionSelector + ' .silvercart-slidorion-slide-next').click(function() {
                            var nextObj = $(slidorionSelector + ' .silvercart-slidorion-link-header.active').nextAll('.silvercart-slidorion-link-header');
                            if (nextObj.length == 0) {
                                nextObj = $(slidorionSelector + ' .silvercart-slidorion-link-header').first();
                            }
                            nextObj.trigger('click');
                        });
                    });
                })(jQuery);
                ",
                $this->ID,
                $this->getSpeedValue(),
                $this->getIntervalValue(),
                $this->getEffectValue(),
                $this->getHoverPauseValue(),
                $this->getAutoPlayValue()
            ),
            'silvercart-slidorion-' . $this->ID
        );
        
        $slidorionHeight        = $this->getWidgetHeightValue();
        $numberOfItems          = $this->getImagesToDisplay()->count();
        $accordeonTitleHeight   = 30;
        $correctionHeight       = 16;
        $accordeonContentHeight = $slidorionHeight - $numberOfItems * $accordeonTitleHeight - $correctionHeight;
        
        Requirements::customCSS(
            sprintf(
                "
                #silvercart-slidorion-%d.silvercart-widget-slidorion-productgroup-slider {
                    height: %dpx;
                }
                #silvercart-slidorion-%d .silvercart-slidorion-slider {
                    height: %dpx;
                }
                #silvercart-slidorion-%d .silvercart-slidorion-accordeon {
                    height: %dpx;
                }
                #silvercart-slidorion-%d .silvercart-slidorion-accordeon > .silvercart-slidorion-link-content {
                    height: %dpx;
                }
                ",
                $this->ID,
                $this->getWidgetHeightValue(),
                $this->ID,
                $this->getSliderHeight(),
                $this->ID,
                $this->getWidgetHeightValue(),
                $this->ID,
                $accordeonContentHeight
            ),
            'silvercart-slidorion-' . $this->ID . '-css'
        );
    }
}