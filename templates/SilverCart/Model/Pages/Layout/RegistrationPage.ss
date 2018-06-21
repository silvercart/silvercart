<div class="row">
    <div class="span9">
        <div class="section-header clearfix">
            <h1>{$Title}</h1>
        </div>
        {$Content}
        <% if $CurrentRegisteredCustomer %>
            <p><%t SilverCart\Model\Pages\Page.ALREADY_REGISTERED 'Hello {name}, You have already registered.' name=$CurrentMember.FirstName %></p>
        <% else %>
            {$RegisterRegularCustomerForm}
        <% end_if %>
    </div>
    <aside class="span3">
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>
