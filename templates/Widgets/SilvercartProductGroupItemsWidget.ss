<h2>$ProductGroupTitle</h2>
<% if isContentView %>
    <% if useListView %>
        <% include SilvercartProductGroupPageList %>
    <% else %>
        <% include SilvercartProductGroupPageTile %>
    <% end_if %>
<% else %>
    <% if useListView %>
        <% include SilvercartWidgetProductBoxList %>
    <% else %>
        <% include SilvercartWidgetProductBoxTile %>
    <% end_if %>
<% end_if %>