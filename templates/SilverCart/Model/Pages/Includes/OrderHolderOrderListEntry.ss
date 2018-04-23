<tr>
    <td class="creationdate-and-ordernumber">
        <a class="highlight creationdate" href="{$CurrentPage.OrderDetailLink}{$ID}">{$Created.Nice}</a><br />
        <a class="highlight ordernumber" href="{$CurrentPage.OrderDetailLink}{$ID}"><span class="mobile-show-sm inline">#</span>{$OrderNumber}</a>
    </td>
    <td class="positions">
        <ul class="unstyled">
        <% loop $LimitedOrderPositions(2) %>
            <li><a class="highlight" href="{$CurrentPage.OrderDetailLink}{$Order.ID}">{$Title.RAW}</a></li>
        <% end_loop %>
        <% if $hasMoreOrderPositionsThan(2) %>
            <li><a class="highlight" href="{$CurrentPage.OrderDetailLink}{$ID}">...</a></li>
        <% end_if %>
        </ul>
    </td>
    <td class="orderstatus">
        <a class="highlight" href="{$CurrentPage.OrderDetailLink}{$ID}">{$OrderStatus.Title}</a>
    </td>
    <td class="total-price">
        <a class="highlight" href="{$CurrentPage.OrderDetailLink}{$ID}">{$AmountTotal.Nice}</a>
    </td>
    <td class="detailbutton">
        <a class="btn btn-small btn-primary" href="{$CurrentPage.OrderDetailLink}{$ID}" title="<%t SilverCart\Model\Pages\Page.SHOW_DETAILS 'show details' %>"  data-toggle="tooltip" data-placement="top" data-title="<%t SilverCart\Model\Pages\Page.SHOW_DETAILS 'show details' %>"><i class="icon-eye-open"></i></a>
    </td>
</tr>
