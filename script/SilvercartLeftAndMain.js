(function($) {
    $('.silvercart-permanent-notification .btn-close').live('click', function() {
        $(this).closest('.silvercart-permanent-notification').fadeOut();
    });
    
    $.entwine('ss', function($) {

        /**
         * Loads /admin/createsitetreetranslation, which will create a new 
         * record for every single page.
         */
        $('.LeftAndMain :input[name=action_createsitetreetranslation]').entwine({
            onclick: function(e) {
                this.parents('form').trigger('submit', [this]);
                e.preventDefault();
                return false;
            }
        });

        /**
         * Loads /admin/publishsitetree, which will publish all pages of the 
         * current locale.
         */
        $('.LeftAndMain :input[name=action_publishsitetree]').entwine({
            onclick: function(e) {
                this.parents('form').trigger('submit', [this]);
                e.preventDefault();
                return false;
            }
        });
        
        $('.opened .collapse li').live('click', function() {
            $('.opened.current').removeClass('current');
            $('#' + $(this).attr('rel')).addClass('current');
        });
        $('.cms-menu.collapsed .collapsed-flyout li').live('click', function() {
            $('.cms-menu li.current').removeClass('current');
            $('#' + $(this).attr('rel')).addClass('current');
        });
        
        $('li[aria-controls="Root_PrintPreviewTab"]').live('click', function() {
            $('iframe.print-preview').height($('.cms-content-fields').height() - 54);
        });
    });
}(jQuery));