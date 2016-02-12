<div id="mobile-bottom-bar">
    <div class="btn-group mobile-bottom-bar">
        <a class="btn btn-large" href="{$PageByIdentifierCode(SilvercartFrontPage).Link}"><i class="icon icon-home"></i><small><% _t('SilvercartPage.Start') %></small></a>
        <a class="btn btn-large focus-on-search" href="javascript:;"><i class="icon icon-search"></i><small><% _t('SilvercartConfig.SEARCH') %></small></a>
        <a class="btn btn-large" href="{$PageByIdentifierCode(SilvercartCartPage).Link}"><i class="icon icon-shopping-cart"></i><small><% _t('SilvercartPage.CART') %></small><% if CurrentMember %><% with CurrentMember %><span class="badge badge-important absolute">{$SilvercartShoppingCart.getQuantity}</span><% end_with %><% end_if %></a>
        <a class="btn btn-large" href="{$PageByIdentifierCode(SilvercartMyAccountHolder).Link}"><i class="icon icon-user"></i><small><% _t('SilvercartPage.MYACCOUNT') %></small></a>
    </div>
</div>