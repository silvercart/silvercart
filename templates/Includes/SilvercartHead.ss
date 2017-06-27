<% base_tag %>
{$SiteConfig.GoogleWebmasterCode.Raw}
<meta charset="utf-8">
<title><% if $MetaTitle %>{$MetaTitle}<% else %>{$Title}<% end_if %></title>
{$MetaTags(false)}
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<% require themedCSS("bootstrap.min","silvercart") %>
<% require themedCSS("font-awesome","silvercart") %>
<% require themedCSS("style","silvercart") %>
<% require themedCSS("customize","silvercart") %>
<% require themedCSS("silvercart","silvercart") %>
<% require themedCSS("silvercart.desktop","silvercart") %>
<script type="text/javascript">var scmfm = false;</script>
{$RequireColorSchemeCSS}
<% require themedCSS("flexslider","silvercart") %>
<% require themedCSS("jquery.fancybox","silvercart") %>
<% require themedCSS("jquery-ui-1.10.1.min","silvercart") %>
<!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
        <link rel="stylesheet" href="{$ThemeDir}/css/font-awesome-ie7.css">
<![endif]-->
<% if $SilvercartConfig.MobileTouchIcon %>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<link rel="apple-touch-icon" sizes="76x76" href="{$SilvercartConfig.MobileTouchIcon.SetSize(76,76).Link}" />
<link rel="apple-touch-icon" sizes="120x120" href="{$SilvercartConfig.MobileTouchIcon.SetSize(120,120).Link}" />
<link rel="apple-touch-icon" sizes="152x152" href="{$SilvercartConfig.MobileTouchIcon.SetSize(152,152).Link}" />
<link rel="apple-touch-icon" sizes="180x180" href="{$SilvercartConfig.MobileTouchIcon.SetSize(180,180).Link}" />
<link rel="icon" sizes="192x192" href="{$SilvercartConfig.MobileTouchIcon.SetSize(192,192).Link}">
<link rel="apple-touch-startup-image" href="{$SilvercartConfig.MobileTouchIcon.Link}">
<% end_if %>
<% if $SilvercartConfig.Favicon %>
<link rel="shortcut icon" href="{$SilvercartConfig.Favicon.Link}">
<% end_if %>
<% if $SilvercartConfig.SilvercartLogo %>
<style type="text/css">#main-header .siteLogo a{background-image:url('{$SilvercartConfig.SilvercartLogo.Link}')!important;}</style>
<% else %>
<style type="text/css">#main-header .siteLogo a{background-image:url('/silvercart/img/logo.png')!important;}</style>
<% end_if %>
<% include SilvercartHeadCustomHtml %>