<div class="row">
    <div class="col-2 text-center">
        <% if $ListImage %>
            <img class="img-fluid" src="{$ListImage.Pad(100,80).URL}" alt="{$Title}" />
        <% end_if %>
    </div>
    <div class="col-6">
        <div class="silvercart-product-title">
            <h3 class="mt-0">{$Title.HTML}</h3>
        </div>
    </div>
    <div class="col-4 text-right text-lg">
        {$PriceNice}
    </div>
</div>