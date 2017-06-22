var silvercartVisibilityChangeCallBackListFocus = new Array();
var silvercartVisibilityChangeCallBackListBlur  = new Array();

function cutCountryList() {
    $('.country-list').each(function() {
        if ($(this).children().length > 9) {
            $(this).css({
                height: '146px',
                overflow: 'hidden',
                position: 'relative'
            });
            $(this).append('<div class="show-all-box">...<br/><a class="btn btn-small btn-link" href="javascript:;"><i class="icon icon-plus"></i> ' + ss.i18n._t('Silvercart.ShowAll') + '</a></div>');
        }
    });
    $('.country-list .show-all-box').css({
        backgroundColor: '#ffffff',
        position: 'absolute',
        bottom: '0px',
        left: '0px',
        width: '100%'
    });
    $('.odd .country-list .show-all-box').css({
        backgroundColor: '#f9f9f9'
    });
    $('.show-all-box a').on('click', function() {
        var countryList   = $(this).closest('.country-list'),
            childrenCount = 0;
        $(this).closest('.show-all-box').remove();
        childrenCount = countryList.children().length - 1;
        countryList.css({
            maxHeight: 'none'
        });
        countryList.animate({
            height: (childrenCount * 20) + 'px'
        });
    });
}
$(document).ready(function(){
    
    if ($('html').attr('lang').length) {
        ss.i18n.setLocale($('html').attr('lang').replace(/-/,'_'));
    } else if ($('body').attr('lang').length) {
        ss.i18n.setLocale($('body').attr('lang').replace(/-/,'_'));
    }
    
    cutCountryList();
    
    $('.silvercart-product-list-productvariant-popup-button').on('click', function(e) {
        e.preventDefault();
        var addToCartFormSelector = '#' + $(this).attr('data-target'),
            popupSelector         = '#popup-' + $(this).attr('data-target'),
            addToCartBtnSelector  = '#' + $(this).attr('data-target') + '_productQuantity_Box';
        $(addToCartBtnSelector).css({
            bottom: '38px'
        });
        $(popupSelector).css({
            bottom: '70px',
            borderTop: '1px solid #999999',
            boxShadow: '0px 0px 4px #999999'
        });
        $(popupSelector + ' select').css({
            maxWidth: parseInt($(addToCartFormSelector).outerWidth()) - 4
        });
        $(popupSelector + ',' + addToCartBtnSelector).css({
            backgroundColor: '#ffffff',
            position: 'absolute',
            width: $(addToCartFormSelector).outerWidth()
        }).slideToggle();
    });
    if ($(".silvercart-product-group-page-selectors")) {
        $(".silvercart-product-group-page-selectors select").on('change', function() { this.form.submit(); });
    }
    
    var hidden,
        change,
        vis = {
            hidden:         "visibilitychange",
            mozHidden:      "mozvisibilitychange",
            webkitHidden:   "webkitvisibilitychange",
            msHidden:       "msvisibilitychange",
            oHidden:        "ovisibilitychange" // not currently supported
        };             
    for (hidden in vis) {
        if (vis.hasOwnProperty(hidden) && hidden in document) {
            change = vis[hidden];
            break;
        }
    }
    if (change) {
        document.addEventListener(change, onchange);
    } else if (/*@cc_on!@*/false) { // IE 9 and lower
        document.onfocusin = document.onfocusout = onchange
    } else {
        window.onfocus = window.onblur = onchange;
    }

    function onchange (evt) {
        var body    = $('body');
        evt         = evt || window.event;
        
        if (evt.type == 'focus' || evt.type == 'focusin') {
            body.removeClass('hidden');
            body.addClass('visible');
        } else if (evt.type == 'blur' || evt.type == 'focusout') {
            body.removeClass('visible');
            body.addClass('hidden');
        } else {
            body.removeClass(this[hidden] ? 'visible' : 'hidden');
            body.addClass(this[hidden] ? 'hidden' : 'visible');
        }
        if (body.hasClass('visible')) {
            $.each(silvercartVisibilityChangeCallBackListFocus, function() {
                if (typeof this == 'function') {
                    this();
                }
            });
        } else {
            $.each(silvercartVisibilityChangeCallBackListBlur, function() {
                if (typeof this == 'function') {
                    this();
                }
            });
        }
    }
});

//(function($){
//        $.fn.extend({
//                slidorion: function(options) {
//                }
//        });
//})(jQuery);