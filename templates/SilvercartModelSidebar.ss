<% include ModelSidebar %>

<% if SilvercartCustomForms %>
    <% control SilvercartCustomForms %>
        {$Form}
    <% end_control %>
<% end_if %>