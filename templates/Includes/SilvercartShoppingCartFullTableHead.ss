<colgroup>
    <col width="12%"></col>
    <col width=""></col>
    <col width="12%"></col>
    <col width="7%"></col>
    
    <% if Top.EditableShoppingCart %>
        <col width="5%"></col>
        <col width="5%"></col>
        <col width="5%"></col>
    <% else %>
        <col width="15%"></col>
    <% end_if %>
    
    <col width="12%"></col>
    
    <% if Top.EditableShoppingCart %>
        <col width="5%"></col>
    <% end_if %>
</colgroup>
<thead>
    <tr>
        <th class="left">
            <% _t('SilvercartProduct.IMAGE', 'Product image') %>
        </th>
        <th class="left">
            <% _t('SilvercartProduct.TITLE', 'Product') %>
        </th>
        <th class="right"><% _t('SilvercartProduct.PRICE_SINGLE', 'price single') %></th>
        <th class="right"><% _t('SilvercartProduct.VAT','VAT') %></th>
        
        <th<% if Top.EditableShoppingCart %> colspan="3"<% end_if %> class="right"><% _t('SilvercartProductPage.QUANTITY') %></th>
        
        <th class="right"><% _t('SilvercartProduct.PRICE') %></th>

        <% if Top.EditableShoppingCart %>
            <th>&nbsp;</th>
        <% end_if %>
    </tr>
</thead>

