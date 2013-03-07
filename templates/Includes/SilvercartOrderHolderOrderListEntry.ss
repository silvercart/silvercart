<tr>
    <td>
        <a href="{$CurrentPage.OrderDetailLink}{$ID}">{$Created.Nice}</a><br />
        <a href="{$CurrentPage.OrderDetailLink}{$ID}">{$OrderNumber}</a>
    </td>
    <td>
        <ul>
        <% loop LimitedSilvercartOrderPositions(2) %>
            <li><a href="{$CurrentPage.OrderDetailLink}{$SilvercartOrder.ID}">{$Title.RAW}</a></li>
        <% end_loop %>
        <% if hasMoreSilvercartOrderPositionsThan(2) %>
            <li><a href="{$CurrentPage.OrderDetailLink}{$ID}">...</a></li>
        <% end_if %>
        </ul>
    </td>
    <td>
        <a href="{$CurrentPage.OrderDetailLink}{$ID}">{$SilvercartOrderStatus.Title}</a>
    </td>
    <td>
        <a href="{$CurrentPage.OrderDetailLink}{$ID}">{$AmountTotal.Nice}</a>
    </td>
    <td>
        <div class="silvercart-button">
            <div class="silvercart-button_content">
                <a href="{$CurrentPage.OrderDetailLink}{$ID}"><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
            </div>
        </div>
    </td>
</tr>
