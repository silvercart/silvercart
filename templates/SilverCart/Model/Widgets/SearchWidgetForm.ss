<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    {$Fields.dataFieldByName(quickSearchQuery).FieldHolder}
    <% loop $Actions %>
        {$Field}
    <% end_loop %>
<% if $IncludeFormTag %>
</form>
<% end_if %>
