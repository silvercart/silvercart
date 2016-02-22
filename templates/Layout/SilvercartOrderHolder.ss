<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>

<% if CurrentRegisteredCustomer %>
        <div class="section-header clearfix">
            <h1>$Title</h1>
        </div>

        {$Content}
    <% if CurrentMembersOrders %>
        <table id="silvercart-order-holder-table-id" class="table full silvercart-order-holder-table">
            <thead>
                <tr class="mobile-hide-sm">
                    <th class="text-left"><% _t('SilvercartPage.ORDER_DATE','order date') %> /<br /><% _t('SilvercartOrder.ORDERNUMBER') %></th>
                    <th class="text-left"><% _t('SilvercartPage.ORDERED_PRODUCTS','ordered products') %></th>
                    <th class="text-left"><% _t('SilvercartOrderStatus.SINGULARNAME') %></th>
                    <th class="text-left"><% _t('SilvercartOrder.AMOUNTTOTAL') %></th>
                    <th>&nbsp;</th>
                </tr>
                <tr class="mobile-show-sm">
                    <th class="text-left" colspan="5"><% _t('SilvercartPage.ORDERED_PRODUCTS','ordered products') %></th>
                </tr>
            </thead>
            <tbody>
            <% loop CurrentMembersOrders %>
                <% include SilvercartOrderHolderOrderListEntry %>
            <% end_loop %>
            </tbody>
        </table>
    <% else %>
        <div class="alert alert-error">
            <p><% _t('SilvercartPage.NO_ORDERS','You do not have any orders yet') %></p>
        </div>
    <% end_if %>
<% else %>
    <% include SilvercartMyAccountLoginOrRegister %>
<% end_if %>
    </div>
    <aside class="span3">
    <% if CurrentRegisteredCustomer %>
        {$SubNavigation}
    <% end_if %>
    $InsertWidgetArea(Sidebar)
    </aside>
</div>
