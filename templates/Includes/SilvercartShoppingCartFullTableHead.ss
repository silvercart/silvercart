<colgroup>
    <col width="12%"></col>
    <col width="12%"></col>
    <col width=""></col>
    <col width="12%"></col>
    <col width="7%"></col>
    <col width="15%"></col>
    <col width="12%"></col>
<% if CurrentPage.EditableShoppingCart %>
    <col width="5%"></col>
<% end_if %>
</colgroup>
<thead>
    <tr>
        <th class="left">
            <% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>
        </th>
        <th class="left">
            <% _t('SilvercartProduct.IMAGE', 'Product image') %>
        </th>
        <th class="left">
            <% _t('SilvercartProduct.TITLE', 'Product') %>
        </th>
        <th class="right"><% _t('SilvercartProduct.PRICE_SINGLE', 'price single') %></th>
        <th class="right"><% _t('SilvercartProduct.VAT','VAT') %></th>
        
        <th class="right"><% _t('SilvercartProductPage.QUANTITY') %></th>
        
        <th class="right"><% _t('SilvercartProduct.PRICE') %></th>

        <% if CurrentPage.EditableShoppingCart %>
            <th>&nbsp;</th>
        <% end_if %>
    </tr>
</thead>

