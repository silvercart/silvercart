<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata

    <fieldset>
        <legend><% _t('SilvercartCheckoutFormStep1.LOGIN') %></legend>

        $CustomHtmlFormErrorMessages

        $CustomHtmlFormFieldByName(Email)
        $CustomHtmlFormFieldByName(Password)
    </fieldset>

    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
                $Field
            <% end_control %>
        </div>
    </div>
</form>