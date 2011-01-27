<% if IncludeFormTag %>
    <form class="yform" $FormAttributes >
<% end_if %>

    $CustomHtmlFormMetadata


    <fieldset>
        <legend>Kontaktdaten</legend>

               $CustomHtmlFormFieldByName(Salutation,CustomHtmlFormFieldSelect)

        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(FirstName)
                </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Surname)
                </div>
            </div>
        </div>

        $CustomHtmlFormFieldByName(Email)

    </fieldset>


    <fieldset>
        <legend>Geburtstag:</legend>

        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(BirthdayDay,CustomHtmlFormFieldSelect)
                 </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(BirthdayMonth,CustomHtmlFormFieldSelect)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(BirthdayYear)
                </div>
            </div>
        </div>

    </fieldset>

        <fieldset>
        <legend>Passwort</legend>
        <div>
            <p>Wenn Sie dieses Feld leer lassen, wird Ihr Passwort nicht ge&auml;ndert.</p>
        </div>

        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Password)
                 </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(PasswordCheck)
                </div>
            </div>
        </div>

    </fieldset>
 <fieldset>
        <legend>Newsletter</legend>

        $CustomHtmlFormFieldByName(SubscribedToNewsletter,CustomHtmlFormFieldCheck)

    </fieldset>

    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
            $Field
            <% end_control %>
        </div>
    </div>   


    $dataFieldByName(SecurityID)

    <% if IncludeFormTag %>
</form>
<% end_if %>
