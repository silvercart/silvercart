<tr>
    <td>
        <a href="{$Top.OrderDetailLink}$ID">$Created.Nice</a><br />
        <a href="{$Top.OrderDetailLink}$ID">$OrderNumber</a>
    </td>
    <td>
        <ul>
            <% control LimitedSilvercartOrderPositions(2) %>
                <li>
                    <a href="{$Top.OrderDetailLink}$ID">
                        $Title.RAW
                    </a>
                </li>
            <% end_control %>
            <% if hasMoreSilvercartOrderPositionsThan(2) %>
                <li>
                    <a href="{$Top.OrderDetailLink}$ID">
                        ...
                    </a>
                </li>
            <% end_if %>
        </ul>
    </td>
    <td>
        <a href="{$Top.OrderDetailLink}$ID">
            $SilvercartOrderStatus.Title
        </a>
    </td>
    <td>
        <a href="{$Top.OrderDetailLink}$ID">
            $AmountTotal.Nice
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
