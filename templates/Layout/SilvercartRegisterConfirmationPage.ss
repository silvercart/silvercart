<div id="col1">
    <div id="col1_content" class="clearfix">
        <div class="text-content">
            <h1>{$Title}</h1>
            $Content
            <% with doConfirmation %>
                <p>$message</p>
            <% end_with %>
            $Form
        </div>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
