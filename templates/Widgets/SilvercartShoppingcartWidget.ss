<% cached WidgetCacheKey %>
    <% if ShowWidget %>
        <strong class="h2"><% _t('SilvercartShoppingcartWidget.TITLE') %></strong>

        <div class="silvercart-widget-content_frame">
            <% if CurrentMember.SilvercartShoppingCart.isFilled %>
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
                    <% control CurrentMember %>
                        <% control SilvercartShoppingCart %>
                            <tfoot>
                                <tr>
                                    <td colspan="2">
                                        <strong><% _t('SilvercartPage.SUM','sum') %></strong>
                                    </td>
                                    <td class="right">$AmountTotal.Nice</td>
                                </tr>
                            </tfoot>
                            <tbody>
                                <% control SilvercartShoppingCartPositions %>
                                    <% include SilvercartShoppingcartWidgetPosition %>
                                <% end_control %>
                            </tbody>
                        <% end_control %>
                    <% end_control %>
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