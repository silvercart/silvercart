<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata

    <fieldset>
        <legend><% _t('SilvercartCheckoutFormStep1.NEWCUSTOMER') %></legend>
        <p><% _t('SilvercartCheckoutFormStep1.REGISTERTEXT') %></p>

        $CustomHtmlFormErrorMessages

        $CustomHtmlFormFieldByName(AnonymousOptions,CustomHtmlFormFieldCheck)
    </fieldset>

    $CustomHtmlFormSpecialFields

    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
                $Field
            <% end_control %>
        </div>
    </div>
</form>