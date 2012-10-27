var silvercartQuickLoginBoxVisibility = 'hidden';

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
    // 
    // @author Sascha Koehler <skoehler@pixeltricks.de>
    // @since 23.08.2011
    // ------------------------------------------------------------------------
    var tabContent = jQuery(".tab_content");
    
    if (tabContent.length > 0) {
        tabContent.hide(); //Hide all content

        jQuery("ul.tabs li, a.tab-trigger").click(function() {
            var lastActiveTabId = jQuery("ul.tabs li.active a").attr('href');
            var lastActiveTab   = lastActiveTabId.substr(lastActiveTabId.indexOf('#'));
            jQuery("ul.tabs li").removeClass("active"); //Remove any "active" class
            
            var activeTab = jQuery(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
            if (activeTab == undefined) {
                activeTab = jQuery(this).attr("href");
            }
            activeTab   = activeTab.substr(activeTab.indexOf('#'));
            var tabID   = activeTab.replace('#','');
            jQuery('ul.tabs li[rel="' + tabID + '"]').addClass("active"); //Add "active" class to selected tab

            jQuery(activeTab).fadeIn(); //Fade in the active ID content
            
            if (activeTab != lastActiveTab) {
                jQuery(lastActiveTab).hide();
            }
        });
        
        var tabID = window.location.hash.replace('#','');
        if ($('ul.tabs li[rel="' + tabID + '"]').length > 0) {
            //Activate tab by url call
            $('ul.tabs li[rel="' + tabID + '"]').addClass("active").show();
            $('.tab_content#' + tabID).show();
        } else {
            //Activate first tab
            jQuery("ul.tabs li:first").addClass("active").show();
            jQuery(".tab_content:first").show();
        }
    }
    
    // ------------------------------------------------------------------------
    // Hide submit buttons for the select-fields on product group pages and
    // add onchange events to the select-fields so that the form ist submitted
    // on change.
    // 
    // @author Sascha Koehler <skoehler@pixeltricks.de>
    // @since 23.08.2011
    // ------------------------------------------------------------------------
    if (jQuery(".silvercart-product-group-page-selectors")) {
        jQuery(".silvercart-product-group-page-selectors input[type=submit]").hide();
        jQuery(".silvercart-product-group-page-selectors select").live('change', function() { this.form.submit(); });
    }
})})(jQuery);
