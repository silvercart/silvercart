<% if Products %>
    <h2><% _t('SilvercartProductGroupItemsWidget.TITLE') %></h2>

    <% control Products %>
        <% include SilvercartWidgetProductBox %>
    <% end_control %>
<% end_control %>