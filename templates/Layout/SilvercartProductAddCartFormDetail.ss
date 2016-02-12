<form class="product-add-cart-detail form<% if errorMessages %> error<% end_if %>" $FormAttributes>
	$CustomHtmlFormMetadata

    <% if errorMessages %>
        <% loop errorMessages %>
            <p>$message</p>
        <% end_loop %>
    <% end_if %>

        $AddCartFormDetailAdditionalFields
        
        $CustomHtmlFormFieldByName(productQuantity,SilvercartProductAddCartFormFieldDetail)
        <% if Product.isInCart %>
            <p class="silvercart-add-cart-form-hint">$Product.QuantityInCartString</p>
        <% end_if %>
</form>
