<form class="yform<% if errorMessages %> error<% end_if %>" $FormAttributes>
	$CustomHtmlFormMetadata

    <% if errorMessages %>
        <% control errorMessages %>
            <p>$message</p>
        <% end_control %>
    <% end_if %>

    <fieldset>
        <legend></legend>
        $SilvercartPlugin(AddCartFormAdditionalFields)
        
        $CustomHtmlFormFieldByName(productQuantity,SilvercartProductAddCartFormField)
    </fieldset>
</form>
