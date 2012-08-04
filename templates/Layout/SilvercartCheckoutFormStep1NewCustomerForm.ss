<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata

    <fieldset>
        <legend><% _t('SilvercartCheckoutFormStep1.NEWCUSTOMER') %></legend>
        <p><% _t('SilvercartCheckoutFormStep1.REGISTERTEXT') %></p>

        $CustomHtmlFormErrorMessages

        $CustomHtmlFormFieldByName(AnonymousOptions,CustomHtmlFormFieldCheck)
    </fieldset>

    <div class="actionRow">
        <div class="type-button">
            <% loop Actions %>
                $Field
            <% end_loop %>
        </div>
    </div>
</form>