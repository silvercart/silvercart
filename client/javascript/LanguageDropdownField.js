
(function($) {

    $(document).on('click', '.silvercart-change-language-selector li.selectable', function() {
        $('.silvercart-change-language-selector li.first').removeClass('first').addClass('selectable');
        $(this).removeClass('selectable').addClass('first');
        $('.silvercart-change-language-selector li.selectable').hide();

        var classes = $(this).attr('class').split(' ');

        if (typeof classes != 'object') {
            classes= [classes];
        }
        $('.silvercart-change-language-form select option').each(function() {
            if ($(this).attr('value').substr(0, 5) === classes[0]) {
                $(this).attr('selected', true);
            }
        });

        $('.silvercart-change-language-form').submit();

    });
    
    $(document).ready(function() {
        var languageSelector        = $('.silvercart-change-language-form select');
        var languageSelectorOptions = $('option', languageSelector);
        var firstLanguage           = true;
        var languageCssClass        = 'first';
        var markup = '<ul class="silvercart-change-language-selector">';
        
        languageSelectorOptions.each(function() {
            var locale  = $(this).attr('value').split('|')[0];
            var iso2    = $(this).attr('class');
            var link    = $(this).attr('value').split('|')[1];
            var text     = $(this).html();
            var lang     = locale.split('_')[0];
            if (firstLanguage) {
                languageCssClass    = 'first';
                firstLanguage       = false;
            } else {
                languageCssClass    = 'selectable';
            }
            var img = '<img src="/resources/vendor/silvercart/silvercart/client/img/icons/flags/' + iso2 + '.png" alt="' + locale + '" />';
            markup += '<li class="' + locale + ' ' + languageCssClass + '"><a title="' + text + '" hreflang="' + locale + '" href="' + link + '">' + img + text + '</a></li>';
        });
        
        markup += '</ul>';
        
        $('.silvercart-change-language-form').hide();
        $('.silvercart-change-language').append(markup);
    });
})(jQuery);