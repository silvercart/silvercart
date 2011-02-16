<div id="col1">
    <div id="col1_content" class="clearfix">
		<div id="Breadcrumbs">
			<p>$Breadcrumbs</p>
		</div>

		<h2>$Title</h2>

		$Content
		$SearchResults
		$Form
		$PageComments
		<% if CustomersOrder %>
			<% include SilvercartOrderDetails %>
		<% else %>
			<p><%_t('SilvercartPage.SESSION_EXPIRED','Your session has expired.') %></p>
		<% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SilvercartSecondLevelNavigation %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
