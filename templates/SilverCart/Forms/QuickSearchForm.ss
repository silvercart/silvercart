<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    <div class="input-append">
    <% with $Fields.dataFieldByName(quickSearchQuery) %>
        <input id="{$ID}" class="span2" type="text" placeholder="{$Placeholder}" value="{$Value}" name="{$Name}">
    <% end_with %>
        {$CustomFormSpecialFields}
    <% loop $Actions %>
        <button class="btn btn-primary" name="quickSearchButton" type="submit">
            <i class="icon-search"></i>
        </button>
    <% end_loop %>
    </div>
<% if $IncludeFormTag %>
</form>
<% end_if %>
