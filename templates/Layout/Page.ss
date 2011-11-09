<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <h2>$Title</h2>

        $Content
        $Form
        
        <div class="silvercartWidgetHolder">
            <div class="silvercartWidgetHolder_content">
                $InsertWidgetArea(Content)
            </div>
        </div>
        
        $PageComments
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $SubNavigation
        <div class="silvercartWidgetHolder">
            <div class="silvercartWidgetHolder_content">
                $InsertWidgetArea(Sidebar)
            </div>
        </div>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
