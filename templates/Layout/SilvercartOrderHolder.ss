<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <h2>$Title</h2>

        $Content
        $SearchResults
        $Form
        $PageComments
        <% if CurrentMembersOrders %>
			<table>
				<tr>
					<th><% _t('SilvercartPage.ORDER_DATE','order date') %></th>
					<th><%_t('SilvercartPage.ORDERD_ARTICLES','ordered articles') %></th>
					<th><%_t('SilvercartOrderStatus.SINGULARNAME') %></th>
					<th><% _t('SilvercartPage.REMARKS') %></th>
				</tr>
				<tr>
					<% control CurrentMembersOrders %>
						<td>
							<a href="/my-account/my-orders/order-details/$ID">$Created.Nice</a>
						</td>
						<td>
							<% control SilvercartOrderPositions %>
								$Title <% if Last %><% else %> | <% end_if %>
							<% end_control %>
						</td>
						<td>
							<% control status %>
								$Title
							<% end_control %>
						</td>
						<td>
							$FormattedNote
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
        <% include SilvercartSecondLevelNavigation %>
        <% include SilvercartSideBarCart %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
