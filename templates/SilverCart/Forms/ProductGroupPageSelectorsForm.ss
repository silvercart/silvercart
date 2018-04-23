<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    <div class="sortBy inline pull-left">
    <% with $Fields.dataFieldByName(SortOrder) %>
        {$Title} {$RequiredFieldMarker} {$Field}
    <% end_with %>
    </div>
<% if $hasProductsPerPageOptions %>
    <div class="showItem inline pull-left">
    <% with $Fields.dataFieldByName(productsPerPage) %>
        {$Title} {$RequiredFieldMarker} {$Field}
    <% end_with %>
    </div>
<% end_if %>
    {$CustomFormSpecialFields}
    <div class="compareItem inline pull-left">
    <% loop $Actions %>
        <button class="btn btn-mini btn-primary active type-button" name="{$name}"><i class="icon-filter"></i> {$Title}</button>
    <% end_loop %>
    </div>
<% if $IncludeFormTag %>
</form>
<% end_if %>