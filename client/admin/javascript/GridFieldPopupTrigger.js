
function GridFieldPopupTrigger() {
    (function($){
        if ($('.sc-grid-field-popup-trigger').length) {
            var targetURL = $('.sc-grid-field-popup-trigger').attr('rel');
            window.open(targetURL);
        }
    }(jQuery));    
}