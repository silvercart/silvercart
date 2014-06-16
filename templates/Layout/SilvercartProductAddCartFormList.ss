<form class="yform<% if errorMessages %> error<% end_if %>" $FormAttributes>
	$CustomHtmlFormMetadata

    <% if errorMessages %>
        <% loop errorMessages %>
            <p>$message</p>
        <% end_loop %>
    <% end_if %>

    <fieldset>
        <legend></legend>
        $SilvercartPlugin(AddCartFormListAdditionalFields)
        
        $CustomHtmlFormFieldByName(productQuantity,SilvercartProductAddCartFormField)
        <% if Product.isInCart %>
            <p class="silvercart-add-cart-form-hint">$Product.QuantityInCartString</p>
        <% end_if %>
    </fieldset>
</form>
