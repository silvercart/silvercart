<tr<% if Last %> class="separator"<% end_if %>>
    <td><a href="$silvercartProduct.Link">$getProductNumberShop</a></td>
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
    <td><a href="$silvercartProduct.Link"><strong>$getTitle</strong></a><% if getCartDescription %><br/><small>$getCartDescription</small><% end_if %><br/>$addToTitle</td>
    <td class="right">$getPrice(true).Nice</td>
    <td class="right">{$SilvercartProduct.TaxRate}%</td>
    <td class="right borderlr">
        <% if Top.EditableShoppingCart %>
        <div class="subcolumns">
            {$DecrementPositionQuantityForm}
            <form action="/customhtmlformaction/addToCart" method="post">
                <input type="hidden" name="productID" value="{$SilvercartProductID}">
                <div class="addToCartField">
                    <input type="text" class="text" name="productQuantity" value="{$TypeSafeQuantity}" maxlength="3" size="3" id="productQuantity-{$ID}"> <label for="productQuantity-{$ID}">{$SilvercartProduct.SilvercartQuantityUnit.Abbreviation}</label>
                </div>
            </form>
            <% if isQuantityIncrementableBy %>
                {$IncrementPositionQuantityForm}
            <% end_if %>
        <% else %>
            <span class="silvercart-quantity-label">
                $getTypeSafeQuantity
            </span>
        <% end_if %>
    </td>
    <td class="right">$Price.Nice</td>

    <% if Top.EditableShoppingCart %>
        <td>$RemovePositionForm</td>
    <% end_if %>
</tr>
