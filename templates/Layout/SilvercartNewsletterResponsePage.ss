<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
            <h1><% _t('SilvercartNewsletterResponsePage.TITLE') %></h1>

            <% if StatusMessages %>
                <% control statusMessages %>
                    <p>$message</p>
                <% end_control %>
            <% end_if %>

            $PageComments
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $SubNavigation
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
