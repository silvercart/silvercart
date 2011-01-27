<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include BreadCrumbs %>

        <h2>$Title</h2>

        $Content
        $Form
        $PageComments

        <% control Carriers %>
        <h3>$Title</h3>
        <% control shippingMethods %>
        <div class="subcolumns">
            <div class="c20l">
                <h4>$Title</h4>
            </div>
            <div class="c80r">
                <table>
                    <tr>
                        <th>Gewicht (g)</th><th>Zone</th><th>Preis</th>
                    </tr>
                    <% control shippingFees %>
                    <tr>
                        <td>$MaximumWeight</td><td><% control zone %>$Title ( <% control countries %>$Title <% end_control %><% end_control %>)</td><td>$Price.Nice</td>
                    </tr>
                    <% end_control %>
                </table>
            </div>
        </div>
        <% end_control %>
        <% end_control %>
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