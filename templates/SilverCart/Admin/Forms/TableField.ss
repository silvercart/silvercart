<table class="silvercart-table-field">
<% loop $getItems %>
    <% if $first %>
    <thead>
        <tr>
            <% loop $Columns %>
            <th>{$Title}</th>
            <% end_loop %>
        </tr>
    </thead>
    <tbody>
    <% end_if %>
        <tr>
            <% loop $Columns %>
            <td>{$Value}</td>
            <% end_loop %>
        </tr>
<% end_loop %>
    </tbody>
</table>