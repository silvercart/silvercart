 <div id="{$FormName}_{$FieldName}_Box" class="input-append quantity<% if errorMessage %> error<% end_if %>">
    <%-- <label for="{$FormName}_{$FieldName}">{$Label}</label>  --%>

<% with FieldTag %>
    <input id="{$ID}" class="input-nano align-right" type="text" value="{$Value}" name="{$Name}">
<% end_with %>   
$CustomHtmlFormSpecialFields

<% loop Parent.Actions %>
    <button title="$Product.Title <% _t('SilvercartProduct.ADD_TO_CART','add Cart') %>" class="btn btn-small btn-primary" data-title="$Owner.Title <% _t('SilvercartProduct.ADD_TO_CART','add Cart') %>" data-placement="top" data-toggle="tooltip">
        <i class="icon-shopping-cart"></i>
    </button> 
<% end_loop %>
</div>