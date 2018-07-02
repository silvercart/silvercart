<% if $IncludeFormTag %>
<form {$addExtraClass('pull-left').AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    {$CustomFormSpecialFields}
    <div id="{$FormName}_productQuantity_Box" class="input-append quantity">
    <% with $Fields.dataFieldByName('productQuantity') %>
        <input id="{$ID}" class="input-nano text-right" type="text" value="{$Value}" name="{$Name}">
    <% end_with %>
    <% loop $Actions %>
        <button title="{$Up.Product.Title} <%t SilverCart\Model\Product\Product.ADD_TO_CART 'add Cart' %>" class="btn btn-small btn-primary" data-placement="top" data-toggle="tooltip">
            <span class="icon-shopping-cart"></span> <span class="full-shopping-cart-title">{$Form.SubmitButtontitle}</span><span class="short-shopping-cart-title"><%t SilverCart\Model\Pages\Page.CART 'Cart' %></span>
        </button>
    <% end_loop %>
    </div>
<% if $Product.isInCart %>
    <p class="silvercart-add-cart-form-hint">{$Product.QuantityInCartString}</p>
<% end_if %>
<% if $IncludeFormTag %>
</form>
<% end_if %>
