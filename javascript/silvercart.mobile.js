function ismobilesafari(){
	return navigator.userAgent.match(/(iPod|iPhone|iPad)/);
}
function initLoadingBarWhenLeaving() {
    
    $('.silvercart-before-leaving-mask').remove();
    $('.silvercart-before-leaving-loading-bar').remove();
    if ($('.silvercart-before-leaving-mask').length === 0) {
        $('body').append('<div class="silvercart-before-leaving-mask"></div>');
        $('body').append('<img class="silvercart-before-leaving-loading-bar" src="/silvercart/img/loader.gif" title="" />');
    }
    $('.silvercart-before-leaving-mask').css({
        width : $('body').css('width'),
        height : $('body').css('height'),
        position : 'absolute',
        display : 'none',
        top : '0px',
        background : '#fff',
        zIndex: 1000
    });
    $('.silvercart-before-leaving-loading-bar').css({
        display : 'none',
        left : (window.innerWidth / 2) - 64,
        top : (window.innerHeight / 2) - 7,
        position : 'fixed',
        width : 128,
        height : 15
    });
    $(window).bind('pageshow', function() {
        $('.silvercart-before-leaving-mask').hide();
        $('.silvercart-before-leaving-loading-bar').hide();
    });
    $('a').on('click', function(event) {
        var href = $(this).attr('href').trim();
        if (href.indexOf('javascript:') === 0 ||
            href.indexOf('#') === 0 ||
            href === window.location.href ||
            href.replace(window.location.pathname, '').indexOf('#') === 0 ||
            $(this).hasClass('fancybox') ||
            $(this).hasClass('js-link')) {
            return;
        }
        $('body').css({
            position : 'relative'
        });
        $('.silvercart-before-leaving-mask').fadeTo('slow', 0.7, function() {
            resizeLoadingBarWhenLeaving();
            $('.silvercart-before-leaving-loading-bar').show();
        });
    });
    $('.sc-products li a').on('click', function(event) {
        $(this).css({
            backgroundColor: '#cccccc',
            color: '#ffffff'
        });
    });
    $('form').on('submit', function(event) {
        var chtmlf = eval($(this).attr('id')),
            chtmlfresult = chtmlf.checkForm(event);
        if (chtmlfresult === true) {
            $('body').css({
                position : 'relative'
            });
            $('.silvercart-before-leaving-mask').fadeTo('slow', 0.7, function() {
                resizeLoadingBarWhenLeaving();
                $('.silvercart-before-leaving-loading-bar').show();
            });
        }
    });
    
    var toolbarIsVisible = false;
    $(document).scroll(function () {
        var y = $(this).scrollTop();
        if (y > 180) {
            if (!toolbarIsVisible) {
                toolbarIsVisible = true;
                $('#mobile-bottom-bar').slideDown();
            }
        } else if (toolbarIsVisible) {
            toolbarIsVisible = false;
            $('#mobile-bottom-bar').slideUp();
        }
    });
}
function resizeLoadingBarWhenLeaving() {
    $('.silvercart-before-leaving-mask').css({
        width : $('body').css('width'),
        height : $('body').css('height')
    });
}
function enableFooterAccordeon() {
    $('footer .section-header').addClass('js-link');
    $('footer .section-header a').addClass('js-link');
    $('footer .section-header').click(function(event) {
        event.preventDefault();
        var fthParent = $(this).parent('div');
        $('.footer-links', fthParent).slideToggle();
    });
}
function formatLogoAndSearchForm() {
    $('.siteLogo').hide();
    $('.sqsf').prepend('<a href="/" class="sublogo" />');

    $('.sqsf form .input-append input[type="text"]').css({
        width: '140px'
    });
    $('.sqsf .sublogo').css({
        marginLeft: '5px',
        width: '35%',
        height: '35px',
        float: 'left',
        backgroundImage: $('.siteLogo a').css('background-image'),
        backgroundRepeat: 'no-repeat',
        backgroundPosition: 'left center',
        backgroundSize: 'contain'
    });
    $('.sqsf form').css({
        marginRight: '5px',
        marginTop: '3px',
        float: 'right'
    });
    $('.sqsf form .input-append').css({
        marginBottom: '0px'
    });
}
$(document).ready(function(){
    
    $('input[name="productQuantity"]').on('mouseup', function (e) {
        e.preventDefault();
    });
    $('input[name="productQuantity"]').on('focus', function () {
        this.setSelectionRange(0, 9999);
    });
    
    initLoadingBarWhenLeaving();
    
    $('.checkout-steps span.active.step-4, .checkout-steps span.active.step-5').on('click', function() {
        if ($('button[type="submit"]').length > 0) {
            $('html, body').animate({
                scrollTop: $('button[type="submit"]').offset().top - (window.innerHeight / 1.2)
            }, 400);
        }
    });
    $('.focus-on-search').on('click', function (event) {
        event.preventDefault();
        window.scrollTo(0, $('input[name="quickSearchQuery"]').offset().top);
        $('input[name="quickSearchQuery"]').focus();
    });
    
    if ($(window).innerWidth() < 480 ||
        scmfm === true) {
        enableFooterAccordeon();
        formatLogoAndSearchForm();
        
        if (ismobilesafari() !== null) {
            $('input.date').attr('type', 'date');
            $('input.time').attr('type', 'time');
            $('input[name="Email"]').attr('type', 'email');
            $('input[name="EmailRepeat"]').attr('type', 'email');
            $('input[name="productQuantity"]').attr('type', 'number');
        }
        
        if(("standalone" in window.navigator) && window.navigator.standalone){
 
            // If you want to prevent remote links in standalone web apps opening Mobile Safari, change 'remotes' to true
            var noddy, remotes = false;

            document.addEventListener('click', function(event) {

                noddy = event.target;

                // Bubble up until we hit link or top HTML element. Warning: BODY element is not compulsory so better to stop on HTML
                while(noddy.nodeName !== "A" && noddy.nodeName !== "HTML") {
                    noddy = noddy.parentNode;
                }

                if('href' in noddy && noddy.href.indexOf('http') !== -1 && (noddy.href.indexOf(document.location.host) !== -1 || remotes))
                {
                    event.preventDefault();
                    document.location.href = noddy.href;
                }

            },false);
        }
    }
});