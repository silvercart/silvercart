var silvercartQuickLoginBoxVisibility           = 'hidden';
var silvercartVisibilityChangeCallBackListFocus = new Array();
var silvercartVisibilityChangeCallBackListBlur  = new Array();

(function($) {jQuery(document).ready(function(){

    /* Toggling the quick login box if available */
    var quickLoginBoxLink = $('#silvercart-login-link');
    
    if (quickLoginBoxLink) {
        quickLoginBoxLink.bind(
            'click',
            SilvercartToggleQuickLoginBox
        );
    }
    
    /* Bind a click event to the cancel link of the quick login box */
    var quickLoginBoxCancelLink = $('#silvercart-quicklogin-form-cancel');
    
    if (quickLoginBoxCancelLink) {
        quickLoginBoxCancelLink.bind(
            'click',
            SilvercartToggleQuickLoginBox
        );
    }
    
    // ------------------------------------------------------------------------
    // Originally taken from
    // "http://www.sohtanaka.com/web-design/simple-tabs-w-css-jquery/" and
    // added some bugfixes.
    // ------------------------------------------------------------------------
    var tabContent = $(".tab_content");
    
    if (tabContent.length > 0) {
        tabContent.hide(); //Hide all content

        $("ul.tabs li, a.tab-trigger").click(function() {
            var lastActiveTabId = $("ul.tabs li.active a").attr('href');
            var lastActiveTab   = lastActiveTabId.substr(lastActiveTabId.indexOf('#'));
            $("ul.tabs li").removeClass("active"); //Remove any "active" class
            
            var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
            if (activeTab == undefined) {
                activeTab = $(this).attr("href");
            }
            activeTab   = activeTab.substr(activeTab.indexOf('#'));
            var tabID   = activeTab.replace('#','');
            $('ul.tabs li[rel="' + tabID + '"]').addClass("active"); //Add "active" class to selected tab

            $(activeTab).fadeIn(); //Fade in the active ID content
            
            if (activeTab != lastActiveTab) {
                $(lastActiveTab).hide();
            }

            return false;
        });
        
        var tabID = window.location.hash.replace('#','');
        if ($('ul.tabs li[rel="' + tabID + '"]').length > 0) {
            //Activate tab by url call
            $('ul.tabs li[rel="' + tabID + '"]').addClass("active").show();
            $('.tab_content#' + tabID).show();
        } else {
            //Activate first tab
            $("ul.tabs li:first").addClass("active").show();
            $(".tab_content:first").show();
        }
    }
    
    // ------------------------------------------------------------------------
    // Show loading state on search input field.
    // ------------------------------------------------------------------------
    if ($('input[name="quickSearchQuery"]').length > 0) {
        var scSearchInProgress = false;
        $('input[name="quickSearchQuery"]').closest('form').submit(function(event) {
            event.preventDefault();
            if (scSearchInProgress) {
                return;
            }
            var uri  = document.baseURI ? document.baseURI : '/',
                form = $(this);
            scSearchInProgress = true;
            $('input[name="quickSearchQuery"]').attr('readonly', 'readonly');
            $('input[name="quickSearchQuery"]').css('background-color',     '#ffffff');
            $('input[name="quickSearchQuery"]').css('background-image',     'url("' + uri + 'silvercart/images/loader-circle.gif")');
            $('input[name="quickSearchQuery"]').css('background-repeat',    'no-repeat');
            $('input[name="quickSearchQuery"]').css('background-position',  '4px center');
            $('input[name="quickSearchQuery"]').css('padding-left',         '25px');
            $('input[name="quickSearchQuery"]').addClass('loading');
            
            if ($('.silvercart-search-autocompletion-results ul').length > 0) {
                $('.silvercart-search-autocompletion-results ul').html('');
            }
            
            setTimeout(function() {
                form.unbind('submit').submit();
            }, 1);
        });
    }
    
    // ------------------------------------------------------------------------
    // Hide submit buttons for the select-fields on product group pages and
    // add onchange events to the select-fields so that the form ist submitted
    // on change.
    // ------------------------------------------------------------------------
    if ($(".silvercart-product-group-page-selectors")) {
        $(".silvercart-product-group-page-selectors .type-button").hide();
        $(".silvercart-product-group-page-selectors select").live('change', function() { this.form.submit(); });
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
})})(jQuery);
