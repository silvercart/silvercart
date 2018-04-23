<thead>
    <tr class="mobile-hide-sm">
        <th>&nbsp;</th>
        <th class="text-left"><strong><%t SilverCart\Model\Product\Product.TITLE 'Product' %></strong></th>
        <th class="text-right"><strong><%t SilverCart\Model\Product\Product.PRICE_SINGLE 'price single' %></strong></th>
        <th class="text-right"><strong><%t SilverCart\Model\Pages\ProductPage.QUANTITY 'Quantity' %></strong></th>
        <th class="text-right"><strong><%t SilverCart\Model\Product\Product.PRICE 'Price' %></strong></th>
    <% if $CurrentPage.EditableShoppingCart %>
        <th>&nbsp;</th>
    <% end_if %>
    </tr>
    <tr class="mobile-show-sm">
        <th colspan="5"><%t SilverCart\Model\Product\Product.TITLE 'Product' %></th>
    <% if $CurrentPage.EditableShoppingCart %>
        <th>&nbsp;</th>
    <% end_if %>
    </tr>
</thead>