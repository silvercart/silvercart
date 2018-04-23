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

                    if(typeof(ss) === 'undefined' || typeof(ss.i18n) === 'undefined') {
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
    var countryFieldId  = parameters.data.formName + parameters.data.nameSeparator + 'Country';
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
    }
    var packstationSelector = '#' + form.formName + ' .optionset input[name="IsPackstation"], .optionset input[name="Shipping_IsPackstation"]',
        initPackstation     = function() {
            var slideIn     = '.packstation-address-data';
            var slideOut    = '.absolute-address-data';
            if ($('#' + form.formName + ' .optionset input[name="IsPackstation"]:checked').val() == 0 ||
                $('#' + form.formName + ' .optionset input[name="Shipping_IsPackstation"]:checked').val() == 0) {
                slideIn     = '.absolute-address-data';
                slideOut    = '.packstation-address-data';
            }
            $('#' + form.formName + ' ' + slideOut).slideUp('slow');
            $('#' + form.formName + ' ' + slideIn).slideDown('slow');
        };
    $('#' + form.formName + ' ' + hideAddressData).hide();
    if (typeof $(packstationSelector).live == 'function') {
        $(packstationSelector).live('change', initPackstation);
    } else if (typeof $(packstationSelector).on == 'function') {
        $(packstationSelector).on('change', initPackstation);
    }
}