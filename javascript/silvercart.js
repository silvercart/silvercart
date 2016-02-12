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
});

(function($){
        $.fn.extend({
                slidorion: function(options) {
                }
        });
})(jQuery);