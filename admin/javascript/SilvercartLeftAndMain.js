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
        $('.LeftAndMain :input[name=action_add_example_data]').entwine({
            onclick: function(e) {
                this.parents('form').trigger('submit', [this]);
                e.preventDefault();
                return false;
            }
        });
        $('.LeftAndMain :input[name=action_add_example_config]').entwine({
            onclick: function(e) {
                this.parents('form').trigger('submit', [this]);
                e.preventDefault();
                return false;
            }
        });
        $('.LeftAndMain :input[name=action_do_newsletter_recipients_export]').entwine({
            onclick: function(e) {
                var suffix = '/',
                    postTargetURL = document.location.pathname + '/do_newsletter_recipients_export',
                    exportContext = $('select[name="ExportContext"]').val();
                if (document.location.pathname.substr(-suffix.length) === suffix) {
                    postTargetURL = document.location.pathname + 'do_newsletter_recipients_export';
                }
                window.open(postTargetURL + '?ExportContext=' + exportContext);
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

        $('.hover-image-preview').live('hover', function(e) {
            var imageURL = $(this).data('img-src');
            if (e.type === 'mouseenter') {
                if ($('#hover-image-preview-box').length === 0) {
                    $('body').append('<div id="hover-image-preview-box"><img/></div>');
                    $('#hover-image-preview-box').hide();
                    $('#hover-image-preview-box').css({
                        maxWidth : '1000px',
                        maxheight : '500px',
                        position: 'absolute',
                        zIndex: '100'
                    });
                    $('#hover-image-preview-box img').css({
                        width: 'auto',
                        height: 'auto',
                        maxWidth: '100%',
                        maxHeight: '100%',
                        boxShadow: '0px 0px 10px #555555',
                        padding: '20px',
                        backgroundColor: '#ffffff'
                    });
                }
                $('#hover-image-preview-box').css({
                    top: e.pageY - 10,
                    left: e.pageX + 30,
                    bottom: 'auto'
                });
                if (e.pageY > window.innerHeight / 2) {
                    $('#hover-image-preview-box').css({
                        bottom: window.innerHeight - e.pageY - 10,
                        left: e.pageX + 30,
                        top: 'auto'
                    });
                }
                $('#hover-image-preview-box img').attr('src', imageURL);
                $('#hover-image-preview-box').show();
            } else if (e.type === 'mouseleave') {
                $('#hover-image-preview-box').hide();
            }
        });
        $('.hover-image-preview').live('click', function(e) {
            $('#hover-image-preview-box').hide();
        });
    });
}(jQuery));