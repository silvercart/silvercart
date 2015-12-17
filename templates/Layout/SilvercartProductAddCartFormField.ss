<% if Parent.Product.IsNotBuyable %>
<% else %>
<div id="{$FormName}_{$FieldName}_Box" class="type-cart<% if errorMessage %> error<% end_if %> <% if Parent.Product.IsNotBuyable %>is-not-buyable<% end_if %>" <% if Parent.Product.IsNotBuyable %>style="display:none;"<% end_if %>>

    <label for="{$FormName}_{$FieldName}">{$Label}</label>
    $FieldTag

    $CustomHtmlFormSpecialFields

    <% loop Parent.Actions %>
        $Field
    <% end_loop %>
</div>
<% end_if %>
