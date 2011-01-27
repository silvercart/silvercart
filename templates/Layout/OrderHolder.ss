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
                <th>Bestelldatum</th>
                <th>bestellte Artikel</th>
                <th>Status</th>
                <th>Ihre Bemerkung</th>
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
        <p>Sie haben noch keine abgeschlossenen Bestellungen.</p>
        <% end_if %>
    </div>
</div>
<% if LayoutType = 4 %>
<div id="col2">
    <div id="col2_content" class="clearfix"></div>
</div>
<% end_if %>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SecondLevelNavigation %>
        <% include SideBarCart %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
