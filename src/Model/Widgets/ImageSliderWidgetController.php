<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Widgets\Widget;
use SilverCart\Model\Widgets\WidgetController;
use SilverStripe\View\Requirements;

/**
 * ImageSliderWidget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ImageSliderWidgetController extends WidgetController {
    
    /**
     * Create javascript for the slider.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2011
     */
    public function init() {
        parent::init();
        $autoplay           = 'false';
        $autoPlayDelayed    = 'false';
        $autoPlayLocked     = 'true';
        $stopAtEnd          = 'false';
        $buildArrows        = 'false';
        $buildStartStop     = 'false';
        $buildNavigation    = 'false';

        if ($this->Autoplay) {
            $autoplay = 'true';
        }
        if ($this->buildArrows) {
            $buildArrows = 'true';
        }
        if ($this->buildNavigation) {
            $buildNavigation = 'true';
        }
        if ($this->buildStartStop) {
            $buildStartStop = 'true';
        }
        if ($this->autoPlayDelayed) {
            $autoPlayDelayed = 'true';
        }
        if ($this->autoPlayLocked) {
            $autoPlayLocked = 'false';
        }
        if ($this->stopAtEnd) {
            $stopAtEnd = 'true';
        }

        switch ($this->transitionEffect) {
            case 'horizontalSlide':
                $vertical           = 'false';
                $animationTime      = 500;
                $delayBeforeAnimate = 0;
                $effect             = 'swing';
                break;
            case 'verticalSlide':
                $vertical           = 'true';
                $animationTime      = 500;
                $delayBeforeAnimate = 0;
                $effect             = 'swing';
                break;
            case 'fade':
            default:
                $vertical           = 'false';
                $animationTime      = 0;
                $delayBeforeAnimate = 500;
                $effect             = 'fade';
        }
           
        if (!Widget::$use_anything_slider) {
            Requirements::customScript(
                sprintf('
var imageSliderAutoPlay = %s,
    imageSliderAutoPlayDelayed = %s,
    imageSliderAutoPlayLocked = %s,
    imageSliderStopAtEnd = %s,
    imageSliderBuildArrows = %s,
    imageSliderBuildNavigation = %s,
    imageSliderBuildStartStop = %s,
    imageSliderDelay = %d,
    imageSliderAnimationTime = %s,
    imageSliderDelayBeforeAnimate = %d,
    imageSliderVertical = %s,
    imageSliderEffect = \'%s\';',
                    $this->ID,
                    $autoplay,
                    $autoPlayDelayed,
                    $autoPlayLocked,
                    $stopAtEnd,
                    $buildArrows,
                    $buildNavigation,
                    $buildStartStop,
                    $this->slideDelay,
                    $animationTime,
                    $delayBeforeAnimate,
                    $vertical,
                    $effect
                )
            );
        } else {
            Requirements::customScript(
                sprintf('
                    $(document).ready(function() {
                        $("#ImageSliderWidget%d")
                        .anythingSlider({
                            startPanel:         1,
                            autoPlay:           %s,
                            autoPlayDelayed:    %s,
                            autoPlayLocked:     %s,
                            stopAtEnd:          %s,
                            buildArrows:        %s,
                            buildNavigation:    %s,
                            buildStartStop:     %s,
                            delay:              %d,
                            animationTime:      %s,
                            delayBeforeAnimate: %d,
                            theme:              \'silvercart-default\',
                            vertical:           %s,
                            navigationFormatter: function(index, panel){
                                panel.css("display", "block");
                                return index;
                            }
                        })
                        .anythingSliderFx({
                            // base FX definitions
                            // ".selector" : [ "effect(s)", "size", "time", "easing" ]
                            // "size", "time" and "easing" are optional parameters, but must be kept in order if added
                            \'.panel\' : [ \'%s\', \'\', 500, \'easeInOutCirc\' ]
                        });
                    });
                    ',
                    $this->ID,
                    $autoplay,
                    $autoPlayDelayed,
                    $autoPlayLocked,
                    $stopAtEnd,
                    $buildArrows,
                    $buildNavigation,
                    $buildStartStop,
                    $this->slideDelay,
                    $animationTime,
                    $delayBeforeAnimate,
                    $vertical,
                    $effect
                )
            );
        } 
    }

    /**
     * This widget should always be a content view.
     *
     * @return boolean true
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2011
     */
    public function isContentView() {
        return true;
    }
}