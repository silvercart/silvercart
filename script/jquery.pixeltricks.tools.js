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
