<% if CurrentPage.CurrentMembersOrders %>
    <table id="silvercart-order-holder-table-id" class="table full table-horizontal silvercart-order-holder-table">
        <thead class="mobile-hide-sm">
            <tr>
                <th><% _t('SilvercartPage.ORDER_DATE','order date') %></th>
                <th><% _t('SilvercartOrder.ORDERNUMBER','Ordernumber') %></th>
                <th><% _t('SilvercartPage.ORDERED_PRODUCTS','ordered products') %></th>
                <th><% _t('SilvercartOrderStatus.SINGULARNAME') %></th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <% loop CurrentPage.CurrentMembersOrders(3) %>
                <tr>
                    <td class="creationdate"><a class="highlight" href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)detail/$ID">{$Created.Nice}</a></td>
                    <td class="ordernumber"><a class="highlight" href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)detail/$ID"><span class="mobile-show-sm inline">#</span>{$OrderNumber}</a></td>
                    <td class="positions">
                        <a class="highlight" href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)detail/$ID">
                        <% loop SilvercartOrderPositions %>
                            $Title.RAW <% if Last %><% else %> | <% end_if %>
                        <% end_loop %>
                        </a>
                    </td>
                    <td class="orderstatus"><a class="highlight" href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)detail/$ID">{$SilvercartOrderStatus.Title}</a></td>
                    <td class="detailbutton"><a class="btn btn-small btn-primary" href="$CurrentPage.PageByIdentifierCodeLink(SilvercartOrderDetailPage)detail/$ID" title="<% _t('SilvercartPage.SHOW_DETAILS','show details') %>"  data-toggle="tooltip" data-placement="top" data-title="<% _t('SilvercartPage.SHOW_DETAILS','show details') %>"><i class="icon-eye-open"></i></a></td>
                </tr>
            <% end_loop %>
        </tbody>
    </table>
<% else %>
<div class="alert alert-error">
    <p><% _t('SilvercartPage.NO_ORDERS','You do not have any orders yet') %></p>
</div>
<% end_if %>