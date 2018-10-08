<% with $CurrentPage.SiteConfig %>
<div style="border-top: 1px solid #ddd; margin-top: 24px; padding-top: 6px;">
    <% if $ShopName %>
    <p style="float: left;">
        <strong>{$ShopName}</strong><br/>
        {$ShopStreet} {$ShopStreetNumber}<br/>
        {$ShopPostcode} {$ShopCity}<br/>
        {$ShopCountry.Title}<br/>
        <% if $ShopPhone || $EmailLink %>
            <% if $ShopPhone %>
        <i>Tel.: {$ShopPhone}</i><br/>
            <% end_if %>
            <% if $EmailLink %>
        <i>E-Mail: <a href="mailto:{$EmailLink}">{$EmailLink}</a></i><br/>
            <% end_if %>
    </p>
        <% end_if %>
    <% end_if %>
    <% if $ShopOpeningHours %>
    <p style="float: right; text-align: right;">
        <strong>{$fieldLabel('ShopOpeningHours')}:</strong><br/>
        {$ShopOpeningHours.RAW}
    </p>
    <% end_if %>
</div>
<div style="clear: both;"></div>
<% end_with %>