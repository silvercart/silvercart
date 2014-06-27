<% cached WidgetCacheKey %>
    <% if ShowWidget %>
        <h2><% _t('SilvercartShoppingcartWidget.TITLE') %></h2>

        <div class="silvercart-widget-content_frame">
            <% if CurrentMember.getCart.isFilled %>
                <table class="full">
                    <colgroup>
                        <col width="60%"></col>
                        <col width="10%"></col>
                        <col width="30%"></col>
                    </colgroup>
                    <thead>
                        <tr>
                            <th><% _t('SilvercartProduct.TITLE') %></th>
                            <th class="right"><% _t('SilvercartProduct.QUANTITY_SHORT') %></th>
                            <th class="right"><% _t('SilvercartProduct.PRICE') %></th>
                        </tr>
                    </thead>
                    <% with CurrentMember %>
                        <% with getCart %>
                            <tfoot>
                                <tr>
                                    <td colspan="2">
                                        <strong><% _t('SilvercartPage.SUM','sum') %></strong>
                                    </td>
                                    <td class="right">$AmountTotal.Nice</td>
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
                <div class="subcolumns">
                    <div class="c50l">
                        <div class="silvercart-button left">
                            <div class="silvercart-button_content">
                                <a href="$CartLink"><% _t('SilvercartPage.GOTO_CART_SHORT', 'Cart') %></a>
                            </div>
                        </div>
                    </div>
                    <div class="c50r">
                        <div class="silvercart-button right">
                            <div class="silvercart-button_content">
                                <a href="$CheckOutLink"><% _t('SilvercartPage.CHECKOUT', 'checkout') %></a>
                            </div>
                        </div>
                    </div>
                </div>
            <% else %>
                <p>
                    <% _t('SilvercartCartPage.CART_EMPTY') %>
                </p>
            <% end_if %>
        </div>
    <% end_if %>
 <% end_cached %>