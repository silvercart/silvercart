<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    {$CustomFormSpecialFields}
<% loop $Actions %>
    <button class="btn btn-mini btn-danger" id="{$ID}" name="{$Name}" title="{$Title}" data-placement="top" data-toggle="tooltip" ><i class="icon-trash"></i></button>
<% end_loop %>
<% if $IncludeFormTag %>
</form>
<% end_if %>