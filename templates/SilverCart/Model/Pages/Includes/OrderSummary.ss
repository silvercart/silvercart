<% if $CurrentPage.CurrentMembersOrders %>
    <table id="silvercart-order-holder-table-id" class="table full table-horizontal silvercart-order-holder-table">
        <thead class="mobile-hide-sm">
            <tr>
                <th><%t SilverCart\Model\Pages\Page.ORDER_DATE 'order date' %></th>
                <th>{$CurrentPage.CurrentMembersOrders.first.fieldLabel('OrderNumber')}</th>
                <th><%t SilverCart\Model\Pages\Page.ORDERED_PRODUCTS 'ordered products' %></th>
                <th><%t SilverCart\Model\Order\OrderStatus.SINGULARNAME 'Order status' %></th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <% loop $CurrentPage.CurrentMembersOrders(3) %>
                <tr>
                    <td class="creationdate"><a class="highlight" href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)detail/$ID">{$Created.Nice}</a></td>
                    <td class="ordernumber"><a class="highlight" href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)detail/$ID"><span class="mobile-show-sm inline">#</span>{$OrderNumber}</a></td>
                    <td class="positions">
                        <a class="highlight" href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)detail/$ID">
                        <% loop $OrderPositions %>
                            $Title.RAW <% if Last %><% else %> | <% end_if %>
                        <% end_loop %>
                        </a>
                    </td>
                    <td class="orderstatus"><a class="highlight" href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)detail/$ID">{$OrderStatus.Title}</a></td>
                    <td class="detailbutton"><a class="btn btn-small btn-primary" href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)detail/$ID" title="<%t SilverCart\Model\Pages\Page.SHOW_DETAILS 'show details' %>"  data-toggle="tooltip" data-placement="top"><i class="icon-eye-open"></i></a></td>
                </tr>
            <% end_loop %>
        </tbody>
    </table>
<% else %>
<div class="alert alert-error">
    <p><%t SilverCart\Model\Pages\Page.NO_ORDERS 'You do not have any orders yet' %></p>
</div>
<% end_if %>