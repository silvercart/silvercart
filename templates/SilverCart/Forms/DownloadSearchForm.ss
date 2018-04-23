<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    {$CustomFormSpecialFields}
    <div id="{$FormName}_SearchQuery_Box" class="input-append">
    <% with $Fields.dataFieldByName(SearchQuery) %>
        <input id="{$ID}" class="text" type="text" placeholder="{$Placeholder}" value="{$Value}" name="{$Name}">
    <% end_with %>
    <% loop $Actions %>
        <button title="{$Title}" class="btn btn-primary" data-placement="top" data-toggle="tooltip"><i class="icon-search"></i> {$Title}</button> 
    <% end_loop %>
    </div>
<% if $IncludeFormTag %>
</form>
<% end_if %>