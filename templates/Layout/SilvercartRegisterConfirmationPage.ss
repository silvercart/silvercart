<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        <h2>$Title</h2>
        $Content
        <% control doConfirmation %>
			<p>$message</p>
        <% end_control %>

        $SearchResults
        $Form
        $PageComments
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $SubNavigation
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
