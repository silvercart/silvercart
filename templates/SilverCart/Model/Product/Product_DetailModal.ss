<div class="modal fade" tabindex="-1" role="dialog" id="modal-product-{$ID}">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{$Title}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<%t SilverCart.Close 'Close' %>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body clearfix">
                <div class="row">
                    <div class="col-6">
                        <a class="fancybox d-inline-block" href="{$ListImage.Link}" data-fancybox-group="modal-product-{$ID}-image-group" title="{$Title}">
                            <img class="img-fluid float-left mr-2 mb-2" src="{$ListImage.Pad(214,145).URL}" alt="{$Title}" /></a>
                        <% if $getImages.count > 1 %>
                        <div class="my-1">
                            <% loop $getImages %>
                                <% if not $First %>
                            <a class="fancybox d-inline-block border rounded p-1" href="{$Image.Link}" data-fancybox-group="modal-product-{$Up.ID}-image-group" title="{$Product.Title}"><img src="{$Image.Pad(50,50).URL}" alt="{$Product.Title}" /></a>
                                <% end_if %>
                            <% end_loop %>
                        </div>
                        <% end_if %>
                    </div>
                    <div class="col-6">
                        <div class="mb-2 text-right">
                        <% if not $isInProductChain && $ChainedProductPriceLabel %>
                            <span class="text-lg font-weight-normal lh-15"><small>{$ChainedProductPriceLabel}</small> {$PriceNice}</span>
                        <% else_if $isInProductChain && $ChainedProductPriceLabel %>
                            <span class="text-lg font-weight-normal lh-15">{$PriceNice}</span>
                        <% else %>
                            <span class="text-lg font-weight-normal lh-15">{$PriceNice}</span>
                        <% end_if %>
                        <% if $PriceIsLowerThanMsr %>
                            <span class="text-line-through text-gray">{$MSRPrice.Nice}</span>
                        <% end_if %>
                        </div>
                    <% if $ShortDescription %>
                        <div class="text-muted mb-1">{$ShortDescription}</div>
                    <% end_if %>
                    </div>
                </div>
                <div class="text-justify">{$LongDescription}</div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-gray" data-dismiss="modal"><%t SilverCart.Close 'Close' %></button>
            </div>
        </div>
    </div>
</div>