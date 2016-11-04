<tr>
    <td class="img">
        <% if SilvercartProduct.getSilvercartImages %>
            <% loop SilvercartProduct.getSilvercartImages %>
                <% if First %>
                    <a href="$silvercartProduct.Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Image.Title) %>">$Image.SetSize(92,92)</a>
                <% end_if %>
            <% end_loop %>
        <% end_if %>
    </td>
    <td class="desc span4">
        <h5><a class="highlight" href="$silvercartProduct.Link">$getTitle</a></h5>
        <ul class="unstyled">
            <li><a class="highlight" href="$silvercartProduct.Link"><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>: $getProductNumberShop</a></li>
            <% if getCartDescription %><li><small>$getCartDescription</small></li><% end_if %>
            <%-- @TODO SilvercartFreightgroupShoppingCartPositionPlugin.php $addToTitle begins with <p> end </div> --%>
            <li><small><%-- $addToTitle --%></small></li>
        </ul>
    </td>
    <td class="sub-price text-right">
        $getPrice(true).Nice<span class="mobile-show-sm inline">
                <br/><% _t('SilvercartProduct.PRICE_SINGLE') %></span>
        <p class="mobile-hide-sm">
            <small>{$SilvercartProduct.TaxRate}% <% _t('SilvercartProduct.VAT') %></small>
        </p>
    </td>
<% if $CurrentPage.EditableShoppingCart %>
    <td class="text-right borderlr quantity">  
        <div class="btn-group">
            <div class="pull-left input-prepend input-append form-prepend">{$DecrementPositionQuantityForm}</div>
            <div class="pull-left">
                <form action="/customhtmlformaction/addToCart" method="post">
                    <input type="hidden" name="productID" value="{$SilvercartProductID}">
                    <div class="input-prepend input-append">
                        <input type="text" class="text input-mini" name="productQuantity" value="{$TypeSafeQuantity}" id="productQuantity-{$ID}">
                    </div>
                </form>
            </div>
            <% if isQuantityIncrementableBy %>
            <div class="pull-left input-append">{$IncrementPositionQuantityForm}</div>
            <% end_if %> 
        </div>
    </td>
<% else %>
    <td class="text-right borderlr quantity">
        <span class="silvercart-quantity-label">{$getTypeSafeQuantity}<span class="mobile-show-sm inline">x</span></span>
    </td>
<% end_if %>
    <td class="total-price text-right">
        $Price.Nice<br/>
        <p class="mobile-hide-sm">
            <small> {$SilvercartProduct.TaxRate}% <% _t('SilvercartProduct.VAT') %></small>
        </p>
    </td>
<% if $CurrentPage.EditableShoppingCart %>
    <td class="remove">$RemovePositionForm</td>
<% end_if %>
</tr>



