<thead>
    <tr class="mobile-hide-sm">
        <th>&nbsp;</th>
        <th class="text-left"><strong><% _t('SilvercartProduct.TITLE', 'Product') %></strong></th>
        <th class="text-right"><strong><% _t('SilvercartProduct.PRICE_SINGLE', 'price single') %></strong></th>
        <th class="text-right"><strong><% _t('SilvercartProductPage.QUANTITY') %></strong></th>
        <th class="text-right"><strong><% _t('SilvercartProduct.PRICE') %></strong></th>
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