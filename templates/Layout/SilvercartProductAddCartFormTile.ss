<form class="yform<% if errorMessages %> error<% end_if %>" $FormAttributes>
	$CustomHtmlFormMetadata

    <% if errorMessages %>
        <% loop errorMessages %>
            <p>$message</p>
        <% end_loop %>
    <% end_if %>

    <fieldset>
        <legend></legend>
        $SilvercartPlugin(AddCartFormTileAdditionalFields)
        
        $CustomHtmlFormFieldByName(productQuantity,SilvercartProductAddCartFormField)
    </fieldset>
</form>
