// initialize namespace or use existing one
var silvercart          = silvercart            ? silvercart            : [];
    silvercart.address  = silvercart.address    ? silvercart.address    : [];

/**
 * Toggles the address add form's display state with slide animation.
 * 
 * @param Event event The triggered event
 * 
 * @return void
 * 
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.03.2013
 */
silvercart.address.toggleAddForm = function(event) {
    event.preventDefault();
    $('#silvercart-add-address-form').slideToggle('slow', function() {
        if ($('#silvercart-add-address-link').length) {
            if ($(this).is(':visible')) {
                $('#silvercart-add-address-link').fadeOut();
            } else {
                $('#silvercart-add-address-link').fadeIn();
            }
        }
        if ($('#silvercart-add-address-form-scrolltarget').length > 0) {
            document.location.hash = $('#silvercart-add-address-form-scrolltarget').attr('name');
            
        }
    });
};

// Define the document ready actions here.
$(function(){
    $('#silvercart-add-address-link, .silvercart-trigger-add-address-link').click(function(event) {
        silvercart.address.toggleAddForm(event);
    });
    $('#silvercart-add-address-form-cancel-id').click(function(event) {
        silvercart.address.toggleAddForm(event);
    });
});