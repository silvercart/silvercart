<table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: auto;padding:0px 22px 22px 22px;">
    <tr>
        <td style="padding:18px 0px 18px 0px;line-height:22px;text-align:inherit;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #CCCDCC;"
            height="100%"
            valign="top"
            bgcolor="">
            <span style="color:#929392;">&nbsp;</span>
        </td>
        <td style="padding:18px 0px 18px 0px;line-height:22px;text-align:inherit;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #CCCDCC;"
            height="100%"
            valign="top"
            bgcolor="">
            <span style="color:#929392;">{$fieldLabel('Product')}</span>
        </td>
        <td style="padding:18px 0px 18px 12px;line-height:22px;text-align:right;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #CCCDCC;"
            height="100%"
            valign="top"
            bgcolor="">
            <span style="color:#929392;">{$ShoppingCartPositions.first.fieldLabel('Price')}</span>
        </td>
    </tr>
<% loop $ShoppingCartPositions %>
    <tr>
        <td style="padding:21px 18px 18px 0px;line-height:22px;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #CCCDCC;" valign="top">
        <% if $Product.ListImage %>
            <% if $SiteConfig.DisableProductLinkInCart %>
            <span style="display: inline-block;"><img src="{$Product.ListImage.Pad(30,30).URL}" alt="{$Product.Title}" /></span>
            <% else %>
            <a style="display: inline-block;" href="{$Product.Link}"><img src="{$Product.ListImage.Pad(30,30).URL}" alt="{$Product.Title}" /></a>
            <% end_if %>
        <% else %>
            &nbsp;
        <% end_if %>
        </td>
        <td style="padding:21px 0px 18px 0px;line-height:22px;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #CCCDCC;width:100%;" valign="top">
            <% if $SiteConfig.DisableProductLinkInCart || not $Product.exists %>
                <span>{$getTypeSafeQuantity}x {$getTitle}</span>
            <% else %>
                <a href="{$Product.Link}">{$getTypeSafeQuantity}x {$getTitle}</a>
            <% end_if %>
            <% if $addToTitle %><br><small style="color: #919191; line-height: 1.4em;">{$addToTitle}</small><% end_if %>
        </td>
        <td style="padding:18px 0px 18px 12px;line-height:22px;border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #CCCDCC;text-align:right;white-space:nowrap;" valign="top">{$PriceNice}</td>
    </tr>
<% end_loop %>
</table>
