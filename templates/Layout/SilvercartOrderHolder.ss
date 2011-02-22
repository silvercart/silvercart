<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <h2>$Title</h2>

        $Content
        $SearchResults
        $Form
        $PageComments
        <% if CurrentMembersOrders %>
        <table class="full">
            <tr>
                <th><% _t('SilvercartPage.ORDER_DATE','order date') %></th>
                <th><% _t('SilvercartPage.ORDERD_PRODUCTS','ordered products') %></th>
                <th><% _t('SilvercartOrderStatus.SINGULARNAME') %></th>
                <th><% _t('SilvercartPage.REMARKS') %></th>
                <th>&nbsp;</th>
            </tr>
            <% control CurrentMembersOrders %>
            <tr>
                <td>
                    <a href="{$Top.OrderDetailLink}$ID">$Created.Nice</a>
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
                    <% control status %>
                        $Title
                    <% end_control %>
                    </a>
                </td>
                <td>
                    $FormattedNote
                </td>
                <td>
                    <a href="{$Top.OrderDetailLink}$ID"><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
                </td>
            </tr>
            <% end_control %>
        </table>
        <% else %>
        <p><% _t('SilvercartPage.NO_ORDERS','You do not have any orders yet') %></p>
        <% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $SubNavigation
        <% include SilvercartSideBarCart %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
