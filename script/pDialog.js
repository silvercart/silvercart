jQuery.fn.pDialog = function() {
    var modal_height    = $(this).outerHeight();
    var modal_width     = $(this).outerWidth();
    var posTop     = ($(window).height()-modal_height)/2;
    var posLeft    = ($(window).width()-modal_width)/2;
    if($(this).css('position') == 'absolute'){
        posTop = posTop + $(window).scrollTop();
    }

    if( !$("#pDialogMask").length ){
        $('body').append('<div id="pDialogMask"></div>');
        $("#pDialogMask").click(function(){
            $('.modal').each(function(){
                $(this).pCloseDialog();
            });
        });
        $(".pDialogClose").click(function(){
            $('.modal').each(function(){
                $(this).pCloseDialog();
            });
        });
    }
    $('#pDialogMask').css({'width':$(window).width(),'height':$(document).height()});
    $('#pDialogMask').css({'display' : 'block', opacity : 0});
    $('#pDialogMask').fadeTo(500,0.8);

    $(this).css({'top' : posTop , 'left' : posLeft});
    $(this).fadeIn(500);
};
jQuery.fn.pCloseDialog = function() {
    $('#pDialogMask').fadeOut(500);
    $('.modal').fadeOut(500);
};
