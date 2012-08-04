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
            <% loop Actions %>
                $Field
            <% end_loop %>
        </div>
    </div>
</form>