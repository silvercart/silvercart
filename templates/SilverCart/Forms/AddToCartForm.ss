<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    {$CustomFormSpecialFields}
    <div id="{$FormName}_productQuantity_Box" class="input-append quantity">
    <% with $Fields.dataFieldByName(productQuantity) %>
        <input id="{$ID}" class="input-nano text-right" type="text" value="{$Value}" name="{$Name}">
    <% end_with %>
    <% loop $Actions %>
        <button title="{$Up.Product.Title} <%t SilverCart\Model\Product\Product.ADD_TO_CART 'add Cart' %>" class="btn btn-small btn-primary" data-placement="top" data-toggle="tooltip">
            <i class="icon-shopping-cart"></i>
        </button> 
    <% end_loop %>
    </div>
<% if $Product.isInCart %>
    <p class="silvercart-add-cart-form-hint">{$Product.QuantityInCartString}</p>
<% end_if %>
<% if $IncludeFormTag %>
</form>
<% end_if %>
