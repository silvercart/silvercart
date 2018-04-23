<div class="row">
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
    <% if $CurrentRegisteredCustomer %>
        <div class="section-header clearfix">
            <h1>{$Title}</h1>
        </div>
        {$Content}
        <% if $CurrentMembersOrders %>
        <table id="silvercart-order-holder-table-id" class="table full silvercart-order-holder-table">
            <thead>
                <tr class="mobile-hide-sm">
                    <th class="text-left"><%t SilverCart\Model\Pages\Page.ORDER_DATE 'order date' %> /<br />{$CurrentMembersOrders.first.fieldLabel('OrderNumber')}</th>
                    <th class="text-left"><%t SilverCart\Model\Pages\Page.ORDERED_PRODUCTS 'ordered products' %></th>
                    <th class="text-left"><%t SilverCart\Model\Order\OrderStatus.SINGULARNAME 'Order status' %></th>
                    <th class="text-left">{$CurrentMembersOrders.first.fieldLabel('AmountTotal')}</th>
                    <th>&nbsp;</th>
                </tr>
                <tr class="mobile-show-sm">
                    <th class="text-left" colspan="5"><%t SilverCart\Model\Pages\Page.ORDERED_PRODUCTS 'ordered products' %></th>
                </tr>
            </thead>
            <tbody>
            <% loop $CurrentMembersOrders %>
                <% include SilverCart/Model/Pages/OrderHolderOrderListEntry %>
            <% end_loop %>
            </tbody>
        </table>
        <% else %>
        <div class="alert alert-error">
            <p><%t SilverCart\Model\Pages\Page.NO_ORDERS 'You do not have any orders yet' %></p>
        </div>
        <% end_if %>
<% else %>
    <% include SilverCart/Model/Pages/MyAccountLoginOrRegister %>
<% end_if %>
    </div>
    <aside class="span3">
    <% if $CurrentRegisteredCustomer %>
        {$SubNavigation}
    <% end_if %>
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>
