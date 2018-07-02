<div class="main-navigation">
    <div class="container">
        <div class="navbar">
            <div class="navbar-inner">
            <% cached $MainNavigationCacheKey %>
                <ul class="nav">
                <% if $showHomeIconInNavigation %>
                    <% with $PageByIdentifierCode('SilvercartFrontPage') %>
                    <li <% if $isCurrent %> class="active"<% end_if %>>
                        <a href="{$OriginalLink}" title="{$SiteConfig.Title} {$SiteConfig.Tagline}"><i class="icon-home"></i></a>
                    </li>
                    <% end_with %>
                <% end_if %>
                <% with $MainNavigationRootPage %>
                    <% loop $Children %>
                        <% if $hasProductsOrChildren %>
                    <li class="<% if $LinkOrSection == section %>active<% else %>{$LinkingMode}<% end_if %> <% if $IsRedirectedChild %>active<% end_if %>">
                        <a href="{$OriginalLink}" title="<%t SilverCart\Model\Pages\Page.GOTO 'go to {title} page' title=$Title.XML %>">{$MenuTitle.XML}  <% if $Children %><i class="icon-caret-down"></i><% end_if %></a>
                        <% if $ClassName == 'RedirectorPage' %>
                            <% with $LinkTo %>
                                <% include SilverCart/Model/Pages/NavigationSubmenu %>
                            <% end_with %>
                        <% else %>
                            <% include SilverCart/Model/Pages/NavigationSubmenu %>
                        <% end_if %>
                    </li>
                        <% else %>
                    <li><a href="{$OriginalLink}" title="<%t SilverCart\Model\Pages\Page.GOTO 'go to {title} page' title=$Title.XML %>">{$MenuTitle.XML}</a></li>
                        <% end_if %>
                    <% end_loop %>
                <% end_with %>
                </ul>
            <% end_cached %>
                <div id="actionItems" class="row">
            <% if $ShoppingCart %>
                <% if $ShoppingCart.isFilled %>
                    <div class="btn-group pull-right">
                        <a id="silvercart-checkout-link" class="btn" href="{$PageByIdentifierCode('SilvercartCheckoutStep').Link}"><%t SilverCart\Model\Pages\Page.CHECKOUT 'checkout' %> <i class="icon icon-caret-right"></i></a>
                    </div>
                <% end_if %>
            <% end_if %>

            <% if not $EditableShoppingCart %>
                    <div class="btn-group cart-preview pull-right">
                        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="icon-shopping-cart"></i> <% if $CurrentMember %>{$CurrentMember.ShoppingCart.getQuantity}<% else %>0<% end_if %> <%t SilverCart\Model\Product\Product.PLURALNAME 'Products' %>
                            <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu cart-content pull-right">
                            <% include SilverCart/Model/Pages/ShoppingCartDropdown %>
                        </div>
                    </div>
            <% end_if %>

            <% if $AllTranslations.Count > 1 %>
                    <div class="btn-group pull-right">
                <% loop $AllTranslations %>
                    <% if $First %>
                        <a class="btn dropdown-toggle first" data-toggle="dropdown" href="#" hreflang="{$RFC1766}" title="<%t SilverCart\Model\Pages\Page.SHOWINPAGE 'set language to {language}' language=$Name %>">
                            <img alt="{$Name}" src="{$BaseHref}resources/vendor/silvercart/silvercart/client/img/icons/flags/{$Code}.png" width="19" /> <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu language">
                    <% else %>
                            <li class="{$RFC1766}">
                                <a href="{$Link}" hreflang="{$RFC1766}" title="<%t SilverCart\Model\Pages\Page.SHOWINPAGE 'set language to {language}' language=$Name %>">
                                    <img alt="{$Name}" src="{$BaseHref}resources/vendor/silvercart/silvercart/client/img/icons/flags/{$Code}.png" width="19" /> {$Name}</a>
                            </li>
                    <% end_if %>
                <% end_loop %>
                        </ul>
                    </div>
            <% end_if %>
                    <div class="btn-group pull-right sqsf clearfix">
                        {$QuickSearchForm}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mobile-show-sm category-link">
    <a class="btn btn-primary btn-block btn-large" href="{$PageByIdentifierCodeLink('SilvercartProductGroupHolder')}"><%t SilverCart\Model\Pages\Page.Categories 'Our Categories' %> <i class="icon icon-caret-right"></i></a>
</div>
