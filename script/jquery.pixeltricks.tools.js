// Namensraum initialisieren und ggfs. vorhandenen verwenden
var pixeltricks         = pixeltricks       ? pixeltricks       : [];
    pixeltricks.tools   = pixeltricks.tools ? pixeltricks.tools : [];

/**
 * Prueft, ob eine Email Adresse schon existiert und gibt dem Benutzer eine
 * entsprechende Rueckmeldung.
 *
 * @param pixeltricks.form.validator form
 * @param string fieldName
 */
function doesEmailExistAlready(form, fieldName)
{
    var fieldValue      = $('#' + form.formName + form.nameSeparator + fieldName).val();
    var errorMessage    = '';
    var success         = false;

    if (fieldValue)
    {
        // Benutzername zur Pruefung an das Backend senden und Antwort ins
        // Ergebnisfeld schreiben.
        var result = $.ajax(
            {
                url:        '/api/v1/Member.json?Email=' + fieldValue,
                dataType:   'json',
                async:      false,
                success:    function(data)
                {
                    if (data.totalSize > 0)
                    {
                        // Email Adresse existiert schon
                        success = false;

                        if(typeof(ss) == 'undefined' || typeof(ss.i18n) == 'undefined') {
                            errorMessage = 'Diese Email Adresse ist schon vergeben.';
                        } else {
                            errorMessage = ss.i18n._t('RegistrationForm.EMAIL_EXISTS_ALREADY', 'Diese Email Adresse ist schon vergeben.');
                        }
                    } else {
                        success         = true;
                        errorMessage    = '';
                    }
                }
            }
        );

        return {
            success:        success,
            errorMessage:   errorMessage
        };
    }
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
    with(SilvercartCheckoutFormStep1_customHtmlFormSubmit_1) {
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
    with(SilvercartCheckoutFormStep1_customHtmlFormSubmit_1) {
        activateValidationFor('Shipping_Salutation');
        activateValidationFor('Shipping_FirstName');
        activateValidationFor('Shipping_Surname');
        activateValidationFor('Shipping_Addition');
        activateValidationFor('Shipping_Street');
        activateValidationFor('Shipping_StreetNumber');
        activateValidationFor('Shipping_Postcode');
        activateValidationFor('Shipping_City');
        activateValidationFor('Shipping_PhoneAreaCode');
        activateValidationFor('Shipping_Phone');
        activateValidationFor('Shipping_Country');
    }
}