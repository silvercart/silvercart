<span itemscope itemtype="http://schema.org/Product">
    <meta itemprop="productID" content="{$ProductNumberShop}" />
    <meta itemprop="url" content="{$Link}" />
    <meta itemprop="name" content="{$Title.HTML}" />
    <meta itemprop="description" content="{$HtmlEncodedLongDescription.HTML}" />
<% if $ListImage %>
    <link itemprop="image" href="{$ListImage.AbsoluteURL}" />
<% end_if %>
<% if $SilvercartManufacturer %>
    <% with $SilvercartManufacturer %>
    <span itemprop="brand" itemscope itemtype="http://schema.org/Brand">
        <% if $Title %>
        <meta itemprop="name" content="{$Title}" />
        <% end_if %>
        <% if $logo %>
        <link itemprop="logo" href="{$logo.AbsoluteURL}" />
        <% end_if %>
    </span>
    <% end_with %>
<% end_if %>
<% if $PriceIsLowerThanMsr %>
    <span itemprop="offers" itemscope="itemscope" itemtype="http://schema.org/AggregateOffer">
        <meta itemprop="priceCurrency" content="{$Price.Currency}" />
        <meta itemprop="lowPrice" content="{$Price.Amount}" />
        <meta itemprop="highPrice" content="{$MSRPrice.Amount}" />
        <meta itemprop="availability" content="{$SilvercartAvailabilityStatus.Title}" />
    </span>
    <% else %>
    <span itemprop="offers" itemscope="itemscope" itemtype="http://schema.org/Offer">
        <meta itemprop="price" content="{$Price.Amount}" />
        <meta itemprop="priceCurrency" content="{$Price.Currency}" />
        <meta itemprop="availability" content="{$SilvercartAvailabilityStatus.Title}" />
    </span>
    <% end_if %>
</span>