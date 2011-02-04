<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include BreadCrumbs %>

        <h2>$Title</h2>

        $Content
        $SearchResults
        $Form
        $PageComments
        <% if CurrentMembersOrders %>
        <table>
            <tr>
                <th><% _t('Page.ORDER_DATE','order date') %></th>
                <th><%_t('Page.ORDERD_ARTICLES','ordered articles') %></th>
                <th><%_t('OrderStatus.SINGULARNAME') %></th>
                <th><% _t('Page.REMARKS') %></th>
            </tr>
            <tr>
                <% control CurrentMembersOrders %>
                <td>
                    <a href="bestelluebersicht/bestellansicht/$ID">$Created.Nice</a>
                </td>
                <td>
                    <% control orderPositions %>
                    $Title <% if Last %><% else %> | <% end_if %>
                    <% end_control %>
                </td>
                <td>
                    <% control status %>
                    $Title
                    <% end_control %>
                </td>
                <td>
                    $Note
                </td>
            </tr>
            <% end_control %>
        </table>
        <% else %>
        <p><% _t('Page.NO_ORDERS','You do not have any orders yet') %></p>
        <% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SecondLevelNavigation %>
        <% include SideBarCart %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
