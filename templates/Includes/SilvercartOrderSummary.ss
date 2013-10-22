
<% if CurrentPage.CurrentMembersOrders %>
    <table id="silvercart-order-holder-table-id" class="full">
        <thead>
            <tr>
                <th><% _t('SilvercartPage.ORDER_DATE','order date') %></th>
                <th><% _t('SilvercartOrder.ORDERNUMBER','Ordernumber') %></th>
                <th><% _t('SilvercartPage.ORDERED_PRODUCTS','ordered products') %></th>
                <th><% _t('SilvercartOrderStatus.SINGULARNAME') %></th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <% control CurrentPage.CurrentMembersOrders(3) %>
                <tr>
                    <td>
                        <a href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)$ID">$Created.Nice</a>
                    </td>
                    <td>
                        <a href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)$ID">$OrderNumber</a>
                    </td>
                    <td>
                        <a href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)$ID">
                        <% control SilvercartOrderPositions %>
                            $Title <% if Last %><% else %> | <% end_if %>
                        <% end_control %>
                        </a>
                    </td>
                    <td>
                        <a href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)$ID">
                            $SilvercartOrderStatus.Title
                        </a>
                    </td>
                    <td>
                        <div class="silvercart-button">
                            <div class="silvercart-button_content">
                                <a href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)$ID"><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
                            </div>
                        </div>
                    </td>
                </tr>
            <% end_control %>
        </tbody>
    </table>
<% else %>
    <p><% _t('SilvercartPage.NO_ORDERS','You do not have any orders yet') %></p>
<% end_if %>