<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>  
        <div class="section-header clearfix">
            <h1>$Title</h1>
        </div>
        $Content
        <% if Form %>
        <div class="form">
            $Form
        </div>
        <% end_if %>
        <div class="silvercartWidgetHolder">
                $InsertWidgetArea(Content)
        </div>
        $PageComments  
    </div><!--end span9-->
    <aside class="span3">
        $InsertWidgetArea(Sidebar)  
    </aside><!--end aside-->
</div>
