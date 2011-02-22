<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <h2>$Title</h2>

        $Content
        $Form
        $PageComments

    <% control Carriers %>
        <h3>$Title</h3>
        <% control SilvercartShippingMethods %>
        <div class="subcolumns">
            <div class="c20l">
                <h4>$Title</h4>
            </div>
            <div class="c80r">
                <table>
                    <tr>
                        <th><% _t('SilvercartProduct.WEIGHT') %> (g)</th><th><% _t('SilvercartZone.SINGULARNAME') %></th><th><% _t('SilvercartProduct.PRICE') %></th>
                    </tr>
                    <% control SilvercartShippingFees %>
                    <tr>
                        <td>$MaximumWeight</td>
                        <td>
                            <% control SilvercartZone %>
                                $Title ( <% control SilvercartCountries %>$Title<% end_control %> )
                            <% end_control %>
                        </td>
                        <td>$Price.Nice</td>
                    </tr>
                    <% end_control %>
                </table>
            </div>
        </div>
        <% end_control %>
    <% end_control %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $SubNavigation
        <% include SilvercartSideBarCart %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
