<% if ShowWidget %>
<div class="section-header clearfix">
    <h3><% _t('SilvercartShoppingcartWidget.TITLE') %></h3>
</div>    
<div class="silvercart-widget-content_frame">
    <% if CurrentMember.SilvercartShoppingCart.isFilled %>
    <table class="table table-condensed">
        <colgroup>
            <col width="60%"></col>
            <col width="10%"></col>
            <col width="30%"></col>
        </colgroup>
        <thead>
            <tr>
                <th class="left"><% _t('SilvercartProduct.TITLE') %></th>
                <th class="right"><% _t('SilvercartProduct.QUANTITY_SHORT') %></th>
                <th class="right"><% _t('SilvercartProduct.PRICE') %></th>
            </tr>
        </thead>
        <% with CurrentMember %>
            <% with SilvercartShoppingCart %>
                <tfoot>
                    <tr>
                        <td colspan="2" class="align-right">
                           <% _t('SilvercartPage.SUM','sum') %>
                        </td>
                        <td class="align-right">$AmountTotal.Nice</td>
                    </tr>
                </tfoot>
                <tbody>
                    <% loop SilvercartShoppingCartPositions %>
                        <% include SilvercartShoppingcartWidgetPosition %>
                    <% end_loop %>
                </tbody>
            <% end_with %>
        <% end_with %>
    </table>
    <br/>
    <div class="btn-toolbar">
        <a class="btn pull-left" href="$CartLink"><i class="icon-shopping-cart"></i> <% _t('SilvercartPage.GOTO_CART_SHORT', 'Cart') %></a>
        <a class="btn btn-primary pull-right" href="$CheckOutLink"><% _t('SilvercartPage.CHECKOUT', 'checkout') %> &raquo;</a>
    </div>
    <% else %>
    <p><% _t('SilvercartCartPage.CART_EMPTY') %></p>
    <% end_if %>
</div>
<% end_if %>