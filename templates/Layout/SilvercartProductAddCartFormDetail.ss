<form class="yform<% if errorMessages %> error<% end_if %>" $FormAttributes>
	$CustomHtmlFormMetadata

    <% if errorMessages %>
        <% control errorMessages %>
            <p>$message</p>
        <% end_control %>
    <% end_if %>

    <fieldset>
        <legend></legend>
        $AddCartFormDetailAdditionalFields
        
        $CustomHtmlFormFieldByName(productQuantity,SilvercartProductAddCartFormField)
        <% if Product.isInCart %>
            <p class="silvercart-add-cart-form-hint">$Product.QuantityInCartString</p>
        <% end_if %>
    </fieldset>
</form>
