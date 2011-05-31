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
});
