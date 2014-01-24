// Namensraum initialisieren und ggfs. vorhandenen verwenden
var pixeltricks         = pixeltricks       ? pixeltricks       : [];
    pixeltricks.tools   = pixeltricks.tools ? pixeltricks.tools : [];

/**
 * Checks whether an email address exists already.
 *
 * @param form pixeltricks.form.validator 
 * @param fieldName string
 */
function doesEmailExistAlready(form, fieldName) {
    var fieldValue      = $('#' + form.formName + form.nameSeparator + fieldName).val();
    var errorMessage    = '';
    var success         = false;
    var uri             = document.baseURI ? document.baseURI : '/';

    if (fieldValue) {
        // send user name to the backend
        var result = $.ajax({
            url:        uri + 'api/v1/Member.json?Email=' + fieldValue,
            dataType:   'json',
            async:      false,
            success:    function(data) {
                if (data.totalSize > 0) {
                    // email addresse exists
                    success = false;

                    if(typeof(ss) == 'undefined' || typeof(ss.i18n) == 'undefined') {
                        errorMessage = 'This email address already exists.';
                    } else {
                        errorMessage = ss.i18n._t('SilvercartRegistrationPage.EMAIL_EXISTS_ALREADY', 'This email address already exists.');
                    }
                } else {
                    success         = true;
                    errorMessage    = '';
                }
            }
        });
    }
    return {
        success:        success,
        errorMessage:   errorMessage
    };
}

/**
 * Dummy to skip JS validation
 *
 * @param pixeltricks.form.validator form
 * @param string                     fieldName
 */
function doesEmailExistAlreadyServerSideOnly(form, fieldName) {
    return {
        success:        true,
        errorMessage:   ''
    };
}

/**
 * Prueft, ob die Im Feld "Versandart" eingesetzten Werte fuer das gewaehlte
 * Land verfuegbar sind. Nicht verfuegbare Werte werden entfernt.
 */
function checkShippingMethodOptions(parameters) {
    var countryFieldId  = parameters.data.formName + parameters.data.nameSeparator + 'SilvercartCountry';
    var countryValue    = $('#' + countryFieldId).val();
    var checkParameters = parameters.data.parameters[countryValue];
    var idx;

    // Schleife ueber die Optionsfelder der Bezahlart
    $.each(
        $('#' + parameters.data.fieldId + ' option'),
        function() {

            var fieldIsValid = false;
            var optionValue  = $(this).val();

            // "Leere" Felder mit Labels / Hinweisen sollen stehen bleiben
            if (optionValue != '') {

                // Pruefen, ob das Optionsfeld fuer das Land erlaubt ist
                $.each(
                    checkParameters,
                    function(key, value) {
                        if (optionValue == value) {
                            fieldIsValid = true;
                        }
                    }
                );

                if (!fieldIsValid) {
                    $(this).remove();
                }
            }
        }
    );
}

/**
 * Toggle the check and visibility status of the shipping address fields.
 *
 * @param Event definition The jQuery event object
 *
 * @return void
 *
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 13.03.2011
 */
function toggleShippingAddressSection(definition) {
   var shippingAddressFieldContainer = $('#ShippingAddressFields');

    if (shippingAddressFieldContainer) {
        if (shippingAddressFieldContainer.css('display') != 'none') {
            deactivateShippingAddressValidation();
            shippingAddressFieldContainer.slideToggle();
        } else {
            activateShippingAddressValidation();
            shippingAddressFieldContainer.slideToggle();
        }
    }
}

/**
 * Disable validation of the shipping address fields.
 *
 * @return void
 *
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 13.03.2011
 */
function deactivateShippingAddressValidation() {
    if (typeof(SilvercartCheckoutFormStep2Anonymous_customHtmlFormSubmit_1) !== 'undefined') {
        with(SilvercartCheckoutFormStep2Anonymous_customHtmlFormSubmit_1) {
            deactivateValidationFor('Shipping_Salutation');
            deactivateValidationFor('Shipping_FirstName');
            deactivateValidationFor('Shipping_Surname');
            deactivateValidationFor('Shipping_Addition');
            deactivateValidationFor('Shipping_Street');
            deactivateValidationFor('Shipping_StreetNumber');
            deactivateValidationFor('Shipping_Postcode');
            deactivateValidationFor('Shipping_City');
            deactivateValidationFor('Shipping_PhoneAreaCode');
            deactivateValidationFor('Shipping_Phone');
            deactivateValidationFor('Shipping_Country');
            deactivateValidationFor('Shipping_PostNumber');
            deactivateValidationFor('Shipping_Packstation');
            deactivateValidationFor('Shipping_IsPackstation');
        }
    } else if (typeof(SilvercartCheckoutFormStep2Regular_customHtmlFormSubmit_1) !== 'undefined') {
        with(SilvercartCheckoutFormStep2Regular_customHtmlFormSubmit_1) {
            deactivateValidationFor('ShippingAddress');
        }
    }
}

/**
 * Enable validation of the shipping address fields.
 *
 * @return void
 *
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 13.03.2011
 */
function activateShippingAddressValidation() {
    if (typeof(SilvercartCheckoutFormStep2Anonymous_customHtmlFormSubmit_1) !== 'undefined') {
        with(SilvercartCheckoutFormStep2Anonymous_customHtmlFormSubmit_1) {
            activateValidationFor('Shipping_Salutation');
            activateValidationFor('Shipping_FirstName');
            activateValidationFor('Shipping_Surname');
            activateValidationFor('Shipping_Addition');
            activateValidationFor('Shipping_Postcode');
            activateValidationFor('Shipping_City');
            activateValidationFor('Shipping_PhoneAreaCode');
            activateValidationFor('Shipping_Phone');
            activateValidationFor('Shipping_Country');
            if ($('.optionset input[name="IsPackstation"]').length < 0) {
                activateValidationFor('Shipping_IsPackstation');
                if ($('.optionset input[name="IsPackstation"]:checked').val() == 0 ||
                    $('.optionset input[name="Shipping_IsPackstation"]:checked').val() == 0) {
                    activateValidationFor('Shipping_Street');
                    activateValidationFor('Shipping_StreetNumber');
                 } else {
                    activateValidationFor('Shipping_PostNumber');
                    activateValidationFor('Shipping_Packstation');
                 }
            } else {
                activateValidationFor('Shipping_Street');
                activateValidationFor('Shipping_StreetNumber');
            }
        }
    } else if (typeof(SilvercartCheckoutFormStep2Regular_customHtmlFormSubmit_1) !== 'undefined') {
        with(SilvercartCheckoutFormStep2Regular_customHtmlFormSubmit_1) {
            activateValidationFor('ShippingAddress');
        }
    }
}

/**
 * Toggles the visibility of the quick login box.
 *
 * @return void
 *
 * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 24.01.2013
 */
function SilvercartToggleQuickLoginBox() {
    if (typeof(window['updateSilvercartToggleQuickLoginBox']) !== 'undefined') {
        updateSilvercartToggleQuickLoginBox();
        return false;
    }
    
    $('#silvercart-quicklogin-form').slideToggle('fast');
    return false;
}

/**
 * Toggles the visibility of the packstation fields.
 *
 * @return void
 *
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 24.01.2014
 */
function initAddressForm(form) {
    var hideAddressData = '.absolute-address-data';
    if ($('#' + form.formName + ' .optionset input[name="IsPackstation"]:checked').val() == 0 ||
        $('#' + form.formName + ' .optionset input[name="Shipping_IsPackstation"]:checked').val() == 0) {
        hideAddressData = '.packstation-address-data';
        with(form) {
            deactivateValidationFor('PostNumber');
            deactivateValidationFor('Packstation');
            deactivateValidationFor('Shipping_PostNumber');
            deactivateValidationFor('Shipping_Packstation');
        }
    } else {
        with(form) {
            deactivateValidationFor('Street');
            deactivateValidationFor('StreetNumber');
            deactivateValidationFor('Shipping_Street');
            deactivateValidationFor('Shipping_StreetNumber');
        }
    }
    $('#' + form.formName + ' ' + hideAddressData).hide();
    $('#' + form.formName + ' .optionset input[name="IsPackstation"], .optionset input[name="Shipping_IsPackstation"]').live('change', function() {
        var slideIn     = '.packstation-address-data';
        var slideOut    = '.absolute-address-data';
        if ($('#' + form.formName + ' .optionset input[name="IsPackstation"]:checked').val() == 0 ||
            $('#' + form.formName + ' .optionset input[name="Shipping_IsPackstation"]:checked').val() == 0) {
            slideIn     = '.absolute-address-data';
            slideOut    = '.packstation-address-data';

            with(form) {
                deactivateValidationFor('PostNumber');
                deactivateValidationFor('Packstation');
                deactivateValidationFor('Shipping_PostNumber');
                deactivateValidationFor('Shipping_Packstation');
                activateValidationFor('Street');
                activateValidationFor('StreetNumber');
                activateValidationFor('Shipping_Street');
                activateValidationFor('Shipping_StreetNumber');
            }
        } else {
            with(form) {
                deactivateValidationFor('Street');
                deactivateValidationFor('StreetNumber');
                deactivateValidationFor('Shipping_Street');
                deactivateValidationFor('Shipping_StreetNumber');
                activateValidationFor('PostNumber');
                activateValidationFor('Packstation');
                activateValidationFor('Shipping_PostNumber');
                activateValidationFor('Shipping_Packstation');
            }
        }
        $('#' + form.formName + ' ' + slideOut).slideUp('slow');
        $('#' + form.formName + ' ' + slideIn).slideDown('slow');
    });
}