(function($) {
    $('.silvercart-permanent-notification .btn-close').live('click', function() {
        $(this).closest('.silvercart-permanent-notification').fadeOut();
    });
})(jQuery);