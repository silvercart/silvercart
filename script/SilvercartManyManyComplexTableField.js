
(function($) {
    $('.SilvercartManyManyComplexTableField a.mark-all').live('click', function() {
        var rel = $(this).attr('rel');
        var context = $('#' + rel);
        $('.checkbox', context).trigger('click');
    });
})(jQuery);