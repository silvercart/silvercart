<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>
        <div class="section-header clearfix">
        <h1>$Title</h1>
        </div>
            $Content
            $Form
            $InsertCustomHtmlForm(SilvercartContactForm)
            $PageComments
    </div>
<aside class="span3">
        $SubNavigation
        $InsertWidgetArea(Sidebar)
    </aside><!--end aside-->
</div>
