
(function($) {
    
    $('.silvercart-change-language-selector li.selectable').live('click', function() {
        $('.silvercart-change-language-selector li.first').removeClass('first').addClass('selectable');
        $(this).removeClass('selectable').addClass('first');
        $('.silvercart-change-language-selector li.selectable').hide();
        $('.silvercart-change-language-form select option[value="' + $(this).attr('class') + '"]').attr('selected',true);
        $('.silvercart-change-language-form').submit();
    });
    
    $(document).ready(function() {
        var languageSelector        = $('.silvercart-change-language-form select');
        var languageSelectorOptions = $('option', languageSelector);
        var firstLanguage           = true;
        var languageCssClass        = 'first';
        var markup = '<ul class="silvercart-change-language-selector">';
        
        languageSelectorOptions.each(function() {
            var locale  = $(this).val();
            var iso2    = $(this).attr('class');
            if (firstLanguage) {
                languageCssClass    = 'first';
                firstLanguage       = false;
            } else {
                languageCssClass    = 'selectable';
            }
            var img = '<img src="/silvercart/images/icons/flags/' + iso2 + '.png" alt="" />';
            markup += '<li class="' + languageCssClass + '" class="' + locale + '">' + img + $(this).html() + '</li>';
        });
        
        markup += '</ul>';
        
        $('.silvercart-change-language-form').hide();
        $('.silvercart-change-language').append(markup);
    });
})(jQuery);