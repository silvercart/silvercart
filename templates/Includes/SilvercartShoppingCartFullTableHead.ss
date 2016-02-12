<thead>
    <tr class="mobile-hide-sm">
        <th>&nbsp;</th>
        <th><% _t('SilvercartProduct.TITLE', 'Product') %></th>
        <th><% _t('SilvercartProduct.PRICE_SINGLE', 'price single') %></th>
        <th><% _t('SilvercartProductPage.QUANTITY') %></th>
        <th><% _t('SilvercartProduct.PRICE') %></th>
    <% if $CurrentPage.EditableShoppingCart %>
        <th>&nbsp;</th>
    <% end_if %>
    </tr>
    <tr class="mobile-show-sm">
        <th colspan="5"><% _t('SilvercartProduct.TITLE', 'Product') %></th>
    <% if $CurrentPage.EditableShoppingCart %>
        <th>&nbsp;</th>
    <% end_if %>
    </tr>
</thead>