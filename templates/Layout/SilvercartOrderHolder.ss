<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <% if CurrentRegisteredCustomer %>
            <h1>$Title</h1>

            $Content
            $SearchResults
            $Form
            $PageComments
            <% if CurrentMembersOrders %>
            <table id="silvercart-order-holder-table-id" class="full">
                <colgroup>
                    <col width="20%"></col>
                    <col width="40%"></col>
                    <col width="15%"></col>
                    <col width="15%"></col>
                    <col width="10%"></col>
                </colgroup>
                <thead>
                    <tr>
                        <th class="left"><% _t('SilvercartPage.ORDER_DATE','order date') %> /<br /><% _t('SilvercartOrder.ORDERNUMBER') %></th>
                        <th class="left"><% _t('SilvercartPage.ORDERED_PRODUCTS','ordered products') %></th>
                        <th class="left"><% _t('SilvercartOrderStatus.SINGULARNAME') %></th>
                        <th class="left"><% _t('SilvercartOrder.AMOUNTTOTAL') %></th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <% loop CurrentMembersOrders %>
                        <% include SilvercartOrderHolderOrderListEntry %>
                    <% end_loop %>
                </tbody>
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
