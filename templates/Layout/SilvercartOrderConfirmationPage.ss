<div class="row">
    <div class="span9">
	 <% include SilvercartBreadCrumbs %> 
         <div class="section-header clearfix">
                    <h1>$Title</h1> 
         </div>
		$Content
		$SearchResults
		$Form
		$PageComments
		<% if CustomersOrder %>
			<% include SilvercartOrderDetails %>
		<% else %>
                <div class="alert alert-error">
			<p><% _t('SilvercartPage.SESSION_EXPIRED','Your session has expired.') %></p>
                </div>
		<% end_if %>
    </div><!--end span9-->
    <aside class="span3">
        <% include SilvercartSecondLevelNavigation %>
        $InsertWidgetArea(Sidebar)
    </aside><!--end aside-->
</div>
