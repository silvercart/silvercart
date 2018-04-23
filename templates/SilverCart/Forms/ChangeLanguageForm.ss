<div class="silvercart-change-language">
<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    {$Fields.dataFieldByName(Language).FieldHolder}
    {$CustomFormSpecialFields}
    <span><% loop $Actions %>{$Field}<% end_loop %></span>
<% if $IncludeFormTag %>
</form>
<% end_if %>
</div>