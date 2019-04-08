<div class="modal fade" tabindex="-1" role="dialog" id="cart-modal-{$Quantity}-{$Product.ID}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <% if $Quantity > 0 %>
                <h4 class="modal-title"><span class="text-success fa fa-check"></span> <%t SilverCart.ProductAddedToCart 'The product was added to your shopping cart.' %></h4> 
            <% else %>
                <h4 class="modal-title"><span class="text-danger fa fa-remove"></span> <%t SilverCart.ProductRemovedFromCart 'The product was removed from your shopping cart.' %></h4>
            <% end_if %>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
            <% with $Product %>
                {$BeforeShoppingCartAjaxResponseContent}
                <% include SilverCart\Model\Order\ShoppingCart_AjaxResponse_Position %>
                {$AfterShoppingCartAjaxResponseContent}
            <% end_with %>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-gray float-left" data-dismiss="modal"><%t SilverCart\Model\Pages\Page.CONTINUESHOPPING 'Continue shopping' %> <span class="fa fa-angle-double-right"></span></button>
                <a class="btn btn-primary" href="{$PageByIdentifierCode('SilvercartCartPage').Link}"><%t SilverCart\Model\Pages\Page.GOTO_CART 'Go to cart' %> <span class="fa fa-angle-double-right"></span></a>
            </div>
        </div>
    </div>
</div>