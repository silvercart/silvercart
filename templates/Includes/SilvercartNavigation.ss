<div class="main-navigation"> <!-- main-navigation -->
    <div class="container">
        <div class="navbar">
            <div class="navbar-inner">
            <% cached $MainNavigationCacheKey %>
                <ul class="nav">
                <% if $showHomeIconInNavigation %>
                    <% with $PageByIdentifierCode(SilvercartFrontPage) %>
                    <li <% if $isCurrent %> class="active"<% end_if %>>
                        <a href="{$Link}" title="{$SiteConfig.Title} {$SiteConfig.Tagline}"><i class="icon-home"></i></a>
                    </li>
                    <% end_with %>
                <% end_if %>
                <% with $MainNavigationRootPage %>
                    <% loop $Children %>
                        <% if $hasProductsOrChildren %>
                    <li class="<% if $LinkOrSection == section %>active<% else %>{$LinkingMode}<% end_if %> <% if $IsRedirectedChild %>active<% end_if %>">
                        <a href="{$Link}" title="<% sprintf(_t('SilvercartPage.GOTO'),$Title.XML) %>">{$MenuTitle.XML}  <% if $Children %><i class="icon-caret-down"></i><% end_if %></a>
                            <% if $Children %>
                        <div>
                            <ul>
                                <% loop $Children %>
                                <li class="{$LinkOrSection}">
                                    <% if $Children %>
                                    <a href="{$Link}" title="<% sprintf(_t('SilvercartPage.GOTO'),$Title.XML) %>"> <span>-</span> {$MenuTitle.XML} ({$Children.Count}) <i class="icon-caret-right pull-right"></i></a>
                                    <div>
                                       <ul>
                                        <% loop $Children %>
                                           <li class="{$LinkOrSection}"><a href="{$Link}" title="<% sprintf(_t('SilvercartPage.GOTO'),$Title.XML) %>"> <span>-</span> {$MenuTitle.XML} <% if $Children %>({$Children.Count})<% end_if %></a></li>
                                        <% end_loop %>
                                       </ul>
                                   </div>

                                    <% else %>
                                    <a href="{$Link}" title="<% sprintf(_t('SilvercartPage.GOTO'),$Title.XML) %>"><span>-</span> {$MenuTitle.XML}</a>
                                    <% end_if %>

                                </li>
                                <% end_loop %>
                            </ul>
                        </div>
                            <% end_if %>
                    </li>
                        <% else %>
                    <li><a href="{$Link}" title="<% sprintf(_t('SilvercartPage.GOTO'),$Title.XML) %>">{$MenuTitle.XML}</a></li>
                        <% end_if %>
                    <% end_loop %>
                <% end_with %>  
                </ul>
            <% end_cached %>
                <div id="actionItems" class="row">
            <% if $SilvercartShoppingCart %>
                <% if $SilvercartShoppingCart.isFilled %>
                    <div class="btn-group pull-right">
                        <a id="silvercart-checkout-link" class="btn" href="{$PageByIdentifierCode(SilvercartCheckoutStep).Link}"><% _t('SilvercartPage.CHECKOUT', 'checkout') %> <i class="icon icon-caret-right"></i></a>
                    </div>
                <% end_if %>
            <% end_if %>

            <% if not $EditableShoppingCart %>
                    <div class="btn-group cart-preview pull-right">
                        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="icon-shopping-cart"></i> <% if $CurrentMember %><% with $CurrentMember %>{$SilvercartShoppingCart.getQuantity}<% end_with %><% else %>0<% end_if %> <% _t('SilvercartProduct.PLURALNAME') %>
                            <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu cart-content pull-right">
                            <% include SilvercartShoppingCartDropdown %>
                        </div> 
                    </div>
            <% end_if %>

            <% if $AllTranslations.Count > 1 %>
                    <div class="btn-group pull-right">  
                <% loop $AllTranslations %>
                    <% if $First %>
                        <a class="btn dropdown-toggle first" data-toggle="dropdown" href="#" hreflang="{$RFC1766}" title="<% sprintf(_t('SilvercartPage.SHOWINPAGE','set language to %s'),$Name) %>">
                            <img alt="{$Name}" src="{$BaseHref}silvercart/img/icons/flags/{$Code}.png" width="19" /> <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu language">
                    <% else %>
                            <li class="{$RFC1766}">
                                <a href="{$Link}" hreflang="$RFC1766" title="<% sprintf(_t('SilvercartPage.SHOWINPAGE','set language to %s'),$Name) %>">
                                    <img alt="{$Name}" src="{$BaseHref}silvercart/img/icons/flags/{$Code}.png" width="19" /> {$Name}</a>
                            </li>
                    <% end_if %>
                <% end_loop %>
                        </ul>
                    </div> 
            <% end_if %>
                    <div class="btn-group pull-right sqsf clearfix">
                        {$InsertCustomHtmlForm(SilvercartQuickSearchForm)}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mobile-show-sm category-link">
    <a class="btn btn-primary btn-block btn-large" href="{$PageByIdentifierCodeLink(SilvercartProductGroupHolder)}"><% _t('Silvercart.OurCategories', 'Our Categories') %> <i class="icon icon-caret-right"></i></a>
</div>
