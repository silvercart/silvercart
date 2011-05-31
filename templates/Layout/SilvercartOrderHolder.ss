<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <% if CurrentRegisteredCustomer %>
            <h2>$Title</h2>

            $Content
            $SearchResults
            $Form
            $PageComments
            <% if CurrentMembersOrders %>
            <table id="silvercart-order-holder-table-id" class="full">
                <tr>
                    <th><% _t('SilvercartPage.ORDER_DATE','order date') %></th>
                    <th><% _t('SilvercartOrder.ORDERNUMBER','Ordernumber') %></th>
                    <th><% _t('SilvercartPage.ORDERD_PRODUCTS','ordered products') %></th>
                    <th><% _t('SilvercartOrderStatus.SINGULARNAME') %></th>
                    <th>&nbsp;</th>
                </tr>
                <% control CurrentMembersOrders %>
                <tr>
                    <td>
                        <a href="{$Top.OrderDetailLink}$ID">$Created.Nice</a>
                    </td>
                    <td>
                        <a href="{$Top.OrderDetailLink}$ID">$OrderNumber</a>
                    </td>
                    <td>
                        <a href="{$Top.OrderDetailLink}$ID">
                        <% control SilvercartOrderPositions %>
                            $Title <% if Last %><% else %> | <% end_if %>
                        <% end_control %>
                        </a>
                    </td>
                    <td>
                        <a href="{$Top.OrderDetailLink}$ID">
                            $SilvercartOrderStatus.Title
                        </a>
                    </td>
                    <td>
                        <div class="silvercart-button">
                            <div class="silvercart-button_content">
                                <a href="{$Top.OrderDetailLink}$ID"><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
                            </div>
                        </div>
                    </td>
                </tr>
                <% end_control %>
            </table>
            <% else %>
            <p><% _t('SilvercartPage.NO_ORDERS','You do not have any orders yet') %></p>
            <% end_if %>
        <% else %>
            <% include SilvercartMyAccountLoginOrRegister %>
        <% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% if CurrentRegisteredCustomer %>
            $SubNavigation
        <% end_if %>
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
