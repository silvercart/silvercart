<% base_tag %>
{$RequireExternalResourcesForHead}
<meta charset="utf-8">
<title><% if $MetaTitle %>{$MetaTitle}<% else %>{$Title}<% end_if %></title>
{$MetaTags(false)}
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<% require themedCSS("client/css/bootstrap.min") %>
<% require themedCSS("client/css/font-awesome") %>
<% require themedCSS("client/css/style") %>
<% require themedCSS("client/css/customize") %>
<% require themedCSS("client/css/silvercart") %>
<% require themedCSS("client/css/silvercart.desktop") %>
<script>var scmfm = false;</script>
{$RequireColorSchemeCSS}
<% require themedCSS("client/css/flexslider") %>
<% require themedCSS("client/css/jquery.fancybox") %>
<% require themedCSS("client/css/jquery-ui-1.10.1.min") %>
<% require themedCSS("client/css/slidorion.css") %>
{$RequireFullJavaScript}
<!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
        <link rel="stylesheet" href="{$BaseHref}resources/vendor/silvercart/silvercart/client/css/font-awesome-ie7.css">
<![endif]-->
<% require javascript(silvercart/silvercart:client/javascript/LanguageDropdownField.js) %>
<% require javascript(silvercart/silvercart:client/javascript/slidorion/jquery.slidorion.min.js) %>
<% if $SiteConfig.MobileTouchIcon %>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<link rel="apple-touch-icon" sizes="76x76" href="{$SiteConfig.MobileTouchIcon.Pad(76,76).Link}" />
<link rel="apple-touch-icon" sizes="120x120" href="{$SiteConfig.MobileTouchIcon.Pad(120,120).Link}" />
<link rel="apple-touch-icon" sizes="152x152" href="{$SiteConfig.MobileTouchIcon.Pad(152,152).Link}" />
<link rel="apple-touch-icon" sizes="180x180" href="{$SiteConfig.MobileTouchIcon.Pad(180,180).Link}" />
<link rel="icon" sizes="192x192" href="{$SiteConfig.MobileTouchIcon.Pad(192,192).Link}">
<link rel="apple-touch-startup-image" href="{$SiteConfig.MobileTouchIcon.Link}">
<% end_if %>
<% if $SiteConfig.Favicon %>
<link rel="shortcut icon" href="{$SiteConfig.Favicon.Link}">
<% end_if %>
<% if $SiteConfig.ShopLogo %>
<style>#main-header .siteLogo a{background-image:url('{$SiteConfig.ShopLogo.Link}')!important;}</style>
<% else %>
<style>#main-header .siteLogo a{background-image:url('/resources/vendor/silvercart/silvercart/client/img/logo.png')!important;}</style>
<% end_if %>
<% include SilverCart/Model/Pages/HeadCustomHtml %>