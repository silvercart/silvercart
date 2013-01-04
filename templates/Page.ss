<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="$ContentLocale" lang="$ContentLocale">
    <head>
        <% base_tag %>
        <% with SiteConfig %>
            $GoogleWebmasterCode.Raw
        <% end_with %>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title><% if MetaTitle %>$MetaTitle<% else %>$Title<% end_if %></title>
		$MetaTags(false)
    </head>
    <body>
        <div id="silvercart-headerbar">
            <div id="silvercart-headerbar_content">
                <div class="subcolumns">
                    <div class="c40l">
                        <div class="subcolumns">
                            <div class="c25l">
                                <div class="subcl">
                                    <a class="skip" title="skip link" href="#navigation">Skip to the navigation</a><span class="hideme">.</span>
                                    <a class="skip" title="skip link" href="#content">Skip to the content</a><span class="hideme">.</span>

                                    <div class="silvercart-meta-navigation">
                                        <div class="silvercart-button">
                                            <div class="silvercart-button_content">
                                                <a id="silvercart-headerbar-home-link" href="$PageByIdentifierCode(SilvercartFrontPage).Link">
                                                    <img src="{$BaseHref}silvercart/images/icon_home.png" alt="home" />
                                                </a>
                                            </div>
                                        </div>
                                        <div class="silvercart-button">
                                            <div class="silvercart-button_content">
                                                <a id="silvercart-headerbar-contact-link" href="$PageByIdentifierCode(SilvercartContactFormPage).Link">
                                                    <img src="{$BaseHref}silvercart/images/icon_contact.png" alt="<% _t('SilvercartContactFormPage.TITLE') %>" />
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="c75r">
                                <div class="subcr">
                                    <div id="silvercart-quicksearch-form">
                                        $InsertCustomHtmlForm(SilvercartQuickSearchForm)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="c60r">
                        <div class="subcr">
                            
                            <div class="subcolumns">
                                <div class="c60l">
                                    <div class="subcl">
                                        <% if CurrentRegisteredCustomer %>
                                            <div class="silvercart-headerbar-actions right">
                                            <% if CurrentRegisteredCustomer.isAdmin %>
                                                <div class="silvercart-button">
                                                    <div class="silvercart-button_content">
                                                        <a href="{$baseHref}admin">
                                                            <% _t('SilvercartPage.ADMIN_AREA', 'Admin Access') %>
                                                        </a>
                                                    </div>
                                                </div>
                                            <% end_if %>
                                                <div class="silvercart-button">
                                                    <div class="silvercart-button_content">
                                                        <a id="silvercart-myaccount-link" href="$PageByIdentifierCode(SilvercartMyAccountHolder).Link">
                                                            <% _t('SilvercartPage.MYACCOUNT', 'my account') %>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="silvercart-button">
                                                    <div class="silvercart-button_content">
                                                        <a id="silvercart-logout-link" href="{$baseHref}Security/logout/">
                                                            <% _t('SilvercartPage.LOGOUT', 'Logout') %>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <% else %>
                                            <div class="silvercart-headerbar-actions right">

                                                <div class="silvercart-button">
                                                    <div class="silvercart-button_content">
                                                        <a id="silvercart-register-link" href="$PageByIdentifierCode(SilvercartRegistrationPage).Link">
                                                            <% _t('SilvercartPage.REGISTER', 'Register') %>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="silvercart-button">
                                                    <div class="silvercart-button_content">
                                                        <a id="silvercart-login-link" href="$PageByIdentifierCode(SilvercartMyAccountHolder).Link">
                                                            <% _t('SilvercartPage.LOGIN', 'Login') %>
                                                        </a>
                                                    </div>
                                                </div>

                                            </div>
                                        
                                            <div id="silvercart-quicklogin-form">
                                                <div id="silvercart-quicklogin-form_content">
                                                    $InsertCustomHtmlForm(SilvercartQuickLoginForm)
                                                </div>
                                            </div>
                                        <% end_if %>
                                    </div>
                                </div>
                                <div class="c40r">
                                    <div class="subcr">
                                        <div class="silvercart-headerbar-actions right">
                                            <% if SilvercartShoppingCart %>
                                                <% if SilvercartShoppingCart.isFilled %>
                                                    <div class="silvercart-button">
                                                        <div class="silvercart-button_content">
                                                            <a id="silvercart-checkout-link" href="$PageByIdentifierCode(SilvercartCheckoutStep).Link">
                                                                <% _t('SilvercartPage.CHECKOUT', 'checkout') %>
                                                            </a>
                                                        </div>
                                                    </div>
                                                <% end_if %>
                                            <% end_if %>
                                            <div class="silvercart-button">
                                                <div class="silvercart-button_content">
                                                    <a id="silvercart-shoppingcart-link" href="$PageByIdentifierCode(SilvercartCartPage).Link">
                                                        <% _t('SilvercartPage.CART', 'cart') %> (<% if CurrentMember %><% with CurrentMember %>$SilvercartShoppingCart.getQuantity<% end_with %><% else %>0<% end_if %>)
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page_margins">
            <div class="page clearfix">
                <div id="silvercart-header" class="clearfix">
                    <div class="subcolumns overflow-visible clearfix">
                        <div class="c66l">
                            <div id="silvercart-shop-claim" class="clearfix">
                                <a href="{$PageByIdentifierCode(SilvercartFrontPage).Link}">
                                    <img src="{$BaseHref}silvercart/images/logo.png" alt="site logo" />
                                </a>
                                <div id="silvercart-shop-claim-text">
                                    <h1>
                                        <a href="{$PageByIdentifierCode(SilvercartFrontPage).Link}">
                                            $SiteConfig.Title
                                        </a>
                                    </h1>
                                    <p>$SiteConfig.Tagline</p>
                                </div>
                            </div>
                            
                        </div>
                        <div class="c33r">
                            <div class="subcr">
                                <% if Translations %>
                                    $InsertCustomHtmlForm(SilvercartChangeLanguageForm)
                                <% end_if %>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="silvercart-productgroup-navigation">
                    <div id="silvercart-productgroup-navigation_content" class="clearfix">
                        <a id="navigation" name="navigation"></a>
                        <% include SilvercartNavigation %>
                    </div>
                </div>
                <div id="main" class="clearfix">
                    <a id="content" name="content"></a>
                    $Layout
                </div>
                <div class="clearfix"></div>
                <div id="silvercart-footer">
                    <div id="silvercart-footer_content">
                        <% cached 'SilvercartNavigation',List(SilvercartMetaNavigationHolder).max(LastEdited),ID %>
                            <% with PageByIdentifierCode(SilvercartMetaNavigationHolder) %>
                                <% loop Children %>
                                    <% if Last %>
                                        <a href="$Link" title="<% sprintf(_t('SilvercartPage.GOTO', 'go to %s page'),$Title.XML) %>" class="$LinkingMode levela">$MenuTitle.XML</a>
                                    <% else %>
                                        <a href="$Link" title="<% sprintf(_t('SilvercartPage.GOTO', 'go to %s page'),$Title.XML) %>">$MenuTitle.XML</a> |
                                    <% end_if %>
                                <% end_loop %>
                            <% end_with %>
                        <% end_cached %>
                        <br/>
                        <br/>
                        <a href="http://www.silvercart.org" target="_blank">SilverCart. eCommerce software. Open-source. You'll love it.</a>
                    </div>
                </div>
            </div>
        </div>
        <% with SiteConfig %>
            $GoogleAnalyticsTrackingCode.Raw
            $PiwikTrackingCode.Raw
        <% end_with %>
    </body>
</html>
