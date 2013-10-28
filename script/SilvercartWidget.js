
$(document).ready(function(){
    var sliderID = '',
        sliderWidth = 0,
        sliderElementWidth = 0,
        sliderInterval = false;
        
    var startSliderAnimation = function() {
        sliderInterval = setInterval(function() {
            sliderElementWidth  = parseInt($('#' + sliderID + ' .silvercart-widget-slider-content .silvercart-widget-content-area').css('width'));
            var leftPosition    = parseInt($('#' + sliderID + ' .silvercart-widget-slider-content').css('left')),
                countOfChildren = $('#' + sliderID + ' .silvercart-widget-slider-content .silvercart-widget-content-area').length;

            leftPosition -= sliderElementWidth;
            if (leftPosition <= (sliderElementWidth * countOfChildren) * -1) {
                leftPosition = 0;
            }

            $('#' + sliderID + ' .silvercart-widget-slider-content').animate({
                left: leftPosition
            }, 300, function() {
                $('#' + sliderID + ' .silvercart-widget-slider-content').css('left');
            });
        }, 10000);
    };
        
    $.each($('.silvercart-widget-content-area.silvercart-widget-slider-element'), function() {
        sliderID = $(this).attr('rel');
        if ($('#' + sliderID).length === 0) {
            $(this).before('<div class="silvercart-widget-slider" id="' + sliderID + '"><div class="silvercart-widget-slider-content"></div></div>');
            $(this).addClass('active');
        }
        $('#' + sliderID + ' .silvercart-widget-slider-content').append('<div class="' + $(this).attr('class') + '">' + $(this).html() + '</div>');
        if (sliderElementWidth === 0) {
            sliderElementWidth = parseInt($('#' + sliderID).css('width'));
        }
        sliderWidth += sliderElementWidth;// = parseInt($('#' + sliderID + ' .silvercart-widget-slider-content').css('width')) + parseInt($(this).css('width'));
        $('#' + sliderID + ' .silvercart-widget-slider-content').css('width', sliderWidth);
        $('#' + sliderID + ' .silvercart-widget-slider-content .silvercart-widget-content-area').css('width', sliderElementWidth);
        $(this).remove();
    });
    
    startSliderAnimation();
    
    $('.silvercart-widget-slider').live({
        mouseenter: function () {
            clearInterval(sliderInterval);
        },
        mouseleave: function () {
            startSliderAnimation();
        }
    });
    
});