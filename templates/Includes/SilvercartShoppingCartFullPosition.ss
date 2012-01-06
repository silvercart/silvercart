<tr<% if Last %> class="separator"<% end_if %>>
    <td>
        <div class="silvercart-product-group-page-box-image">
            <% if SilvercartProduct.getSilvercartImages %>
                <% control SilvercartProduct.getSilvercartImages %>
                    <% if First %>
            <a href="$silvercartProduct.Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Image.Title) %>">$Image.SetRatioSize(60,60)</a>
                    <% end_if %>
                <% end_control %>
            <% end_if %>
        </div>
    </td>
    <td>
        <a href="$silvercartProduct.Link">$SilvercartProduct.Title</a><br />
        <a href="$silvercartProduct.Link">(<% _t('SilvercartProduct.PRODUCTNUMBER', 'product number') %> $SilvercartProduct.ProductNumberShop)</a>
    </td>
    <td class="right">$getPrice(true).Nice</td>
    <td class="right">{$SilvercartProduct.TaxRate}%</td>
    <td class="right borderlr"<% if Top.EditableShoppingCart %> colspan="3"<% end_if %>>
        <% if Top.EditableShoppingCart %>
        <div class="subcolumns">
            <div class="c33l">
                $DecrementPositionQuantityForm
            </div>
        <div class="c33l">
        <% end_if %>
        <div class="silvercart-quantity-value">
            <div>$Quantity</div>
        </div>
        <% if Top.EditableShoppingCart %>
            </div>
            <div class="c33r">
                <% if isQuantityIncrementableBy %>
                    $IncrementPositionQuantityForm
                <% else %>
                    &nbsp;
                <% end_if %>
            </div>
        </div>
        <% end_if %>
    </td>
    <td class="right">$Price.Nice</td>

    <% if Top.EditableShoppingCart %>
        <td>$RemovePositionForm</td>
    <% end_if %>
</tr>
