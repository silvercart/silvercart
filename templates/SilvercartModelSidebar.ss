<% include ModelSidebar %>

<% if SilvercartCustomForms %>
    <% loop SilvercartCustomForms %>
        {$Form}
    <% end_loop %>
<% end_if %>