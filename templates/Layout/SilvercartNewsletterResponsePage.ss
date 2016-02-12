<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>  
        <div class="section-header clearfix">
           <h1><% _t('SilvercartNewsletterResponsePage.TITLE') %></h1>   
        </div>
            <% if StatusMessages %>
                <% loop statusMessages %>
                    <p>$message</p>
                <% end_loop %>
            <% end_if %>

            $PageComments
    </div><!--end span9-->
    <aside class="span3">
        $SubNavigation
        $InsertWidgetArea(Sidebar)
    </aside><!--end aside-->
</div>
