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
            <% include OrderDetails %>
            <% else %>
            <p>Ihre Sitzung ist abgelaufen.</p>
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
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
