<div class="row">
    <div class="span9">
        <div class="section-header clearfix">
             <h1>{$Title}</h1>
        </div>
            $Content
            <% loop doConfirmation %>
                <p>$message</p>
            <% end_loop %>
            $Form
    </div><!--end span9-->
    <aside class="span3">
        $InsertWidgetArea(Sidebar)
    </aside><!--end aside-->
</div>
