<div class="row">
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
        <div class="section-header clearfix">
           <h1><%t SilverCart\Model\Pages\NewsletterPage.THANKS_TITLE 'Newsletter Status' %></h1>
        </div>
    <% with $NewsletterForm %>
        <% if $Message %>
            <p id="{$FormName}_message" class="alert alert-{$MessageType} message {$MessageType}">{$Message}</p>
        <% end_if %>
    <% end_with %>
    </div>
    <aside class="span3">
        {$SubNavigation}
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>
