<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        <h2>$Title</h2>
            $Content
            $Form

            <h2>
                <% _t('SilvercartNewsletterResponsePage.TITLE') %>
            </h2>

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
