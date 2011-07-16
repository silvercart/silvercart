var silvercartQuickLoginBoxVisibility = 'hidden';

$(document).ready(function(){

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
    
    /* Delete the value of the qick search box field on click. If nothing was
       entered restore the old value on blur */
    var silvercartQuickSearchFieldValue = '';
    var quickSearchField = $('#silvercart-quicksearch-form .type-text input');
    
    if (quickSearchField) {
        silvercartQuickSearchFieldValue = quickSearchField.val();
        
        quickSearchField.bind(
            'click',
            function() {
                silvercartQuickSearchFieldValue = quickSearchField.val();
                
                quickSearchField.val('');
            }
        );
        quickSearchField.bind(
            'blur',
            function() {
                if (quickSearchField.val() == '') {
                    quickSearchField.val(silvercartQuickSearchFieldValue)
                }
            }
        );
    }
    
    // ------------------------------------------------------------------------
    // Originally taken from
    // "http://www.sohtanaka.com/web-design/simple-tabs-w-css-jquery/" and
    // added some bugfixes.
    // ------------------------------------------------------------------------
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content

	$("ul.tabs li").click(function() {

		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content

		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
        activeTab     = activeTab.substr(activeTab.indexOf('#'));
        
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
});
