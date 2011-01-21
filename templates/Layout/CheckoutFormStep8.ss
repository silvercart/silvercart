<form class="yform" $FormAttributes>

    <fieldset>
        <legend>
            <% if controller.getErrorOccured %>
                Es ist ein Fehler aufgetreten
            <% else %>
                Das gewählte Zahlungsmodul ist fehlerhaft
            <% end_if %>
        </legend>

        <% if controller.getErrorOccured %>

            <p>
                Es sind folgende Fehler aufgetreten:
            </p>

            <div class="error">
                <ul class="message">
                    <% control controller.getErrorList %>
                        <li>
                            $error
                        </li>
                    <% end_control %>
                </ul
            </div>
            <br />
        <% end_if %>

        <p>
            Bitte wählen Sie ein anderes Zahlungsmodul oder setzen Sie sich mit dem Webseitenbetreiber in Verbindung.
        </p>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    <a href="{$controller.Link}Cancel" class="button_type1">
                        <span class="button_type1_content">
                            <span class="button_type1_panel">
                                <span class="button_type1_panel_content">Ein anderes Zahlungsmodul wählen</span>
                            </span>
                        </span>
                    </a>
                </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    <a href="/kontakt" class="button_type1">
                        <span class="button_type1_content">
                            <span class="button_type1_panel">
                                <span class="button_type1_panel_content">Zur Seite "Kontakt" gehen</span>
                            </span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </fieldset>

</form>