<% if $ShowWidget %>
<div class="section-header clearfix">
    <h3><%t SilverCart\Model\Widgets\ShoppingcartWidget.TITLE 'Shopping cart' %></h3>
</div>    
<div class="silvercart-widget-content_frame">
    <% if $CurrentMember.ShoppingCart.isFilled %>
    <table class="table table-condensed">
        <colgroup>
            <col width="60%"></col>
            <col width="10%"></col>
            <col width="30%"></col>
        </colgroup>
        <thead>
            <tr>
                <th class="text-left"><%t SilverCart\Model\Product\Product.TITLE 'Product' %></th>
                <th class="text-right"><%t SilverCart\Model\Product\Product.QUANTITY_SHORT 'Qty.' %></th>
                <th class="text-right"><%t SilverCart\Model\Product\Product.PRICE 'Price' %></th>
            </tr>
        </thead>
    <% with $CurrentMember.ShoppingCart %>
        <tbody>
        <% loop $ShoppingCartPositions %>
            <tr>
                <td class="text-left"><a href="{$Product.Link}">{$getTitleForWidget}</a><br/>{$addToTitleForWidget}</td>
                <td class="text-right">{$getTypeSafeQuantity}</td>
                <td class="text-right">{$Price.Nice}</td>
            </tr>
        <% end_loop %>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right">
                   <%t SilverCart\Model\Pages\Page.SUM 'sum' %>
                </td>
                <td class="text-right">{$AmountTotal.Nice}</td>
            </tr>
        </tfoot>
    <% end_with %>
    </table>
    <br/>
    <div class="btn-toolbar">
        <a class="btn pull-left" href="{$CartLink}"><i class="icon-shopping-cart"></i> <%t SilverCart\Model\Pages\Page.GOTO_CART_SHORT 'Cart' %></a>
        <a class="btn btn-primary pull-right" href="{$CheckOutLink}"><%t SilverCart\Model\Pages\Page.CHECKOUT 'checkout' %> &raquo;</a>
    </div>
    <% else %>
    <p><%t SilverCart\Model\Pages\CartPage.CART_EMPTY 'Your cart is empty' %></p>
    <% end_if %>
</div>
<% end_if %>