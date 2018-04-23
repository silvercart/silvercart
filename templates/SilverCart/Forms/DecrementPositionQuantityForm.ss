<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    {$CustomFormSpecialFields}
<% loop $Actions %>
    <button class="btn" id="{$ID}" name="{$Name}" title="{$Title}" data-placement="top" data-toggle="tooltip" type="submit"><i class="icon-minus"></i></button>
<% end_loop %>
<% if $IncludeFormTag %>
</form>
<% end_if %>