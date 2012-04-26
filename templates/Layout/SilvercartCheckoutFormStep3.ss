<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    <fieldset>
        <legend><% _t('SilvercartShippingMethod.SINGULARNAME') %></legend>
        <div class="subcolumns">
            $CustomHtmlFormFieldByName(ShippingMethod)
        </div>
    </fieldset>
    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
            $Field
            <% end_control %>
        </div>
    </div>
</form>
