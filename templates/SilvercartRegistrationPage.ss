<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="$ContentLocale"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="$ContentLocale"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="$ContentLocale"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="$ContentLocale"> <!--<![endif]-->
<head>
    <% include SilvercartHead %>
</head>
<body lang="$ContentLocale" class="{$ClassName} <% if IsInCheckout %>SilvercartCheckoutStep<% end_if %>">
    <div id="main-container" class="clearfix">
        <% if IsInCheckout %>
            <% include SilvercartHeaderFunnel %>
        <% else %>
            <% include SilvercartHeaderFull %>
        <% end_if %>
        <div class="container main" id="main">
            $Layout
        </div>
        <% include SilvercartFooter %>
    </div>
    {$ModuleHtmlInjections}
    <% include SilvercartBottomTabBar %>
    <% if isLive %>
        <% with SiteConfig %>
            {$GoogleAnalyticsTrackingCode.Raw}
            {$PiwikTrackingCode.Raw}
        <% end_with %>
    <% end_if %>
    </body>
</html>