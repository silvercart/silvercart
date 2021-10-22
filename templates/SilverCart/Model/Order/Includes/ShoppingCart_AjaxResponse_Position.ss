<div class="row">
    <div class="col-2 text-center">
        <% if $Product.ListImage %>
            <img class="img-fluid" src="{$Product.ListImage.Pad(100,80).URL}" alt="{$Title}" />
        <% end_if %>
    </div>
    <div class="col-7">
        <div class="silvercart-product-title">
            <h3 class="mt-0">{$Quantity}x {$Title.HTML}</h3>
            <% if $addToTitle %><div class="text-break text-blue-dark-85">{$addToTitle}</div><% end_if %>
        </div>
    </div>
    <div class="col-3 text-right text-lg">
        {$PriceNice}
    </div>
</div>