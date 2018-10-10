<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de-DE" lang="de-DE">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{$ShopEmailSubject}</title>
    </head>
    <body style="background-color: #f0f0f0; padding: 0; margin: 0;">
        <style>
            body { font-family: Verdana; font-size:70.00%; color:#666; }
            .text-right { text-align: right !important; }
            .text-left { text-align: left !important; }
            h1 { font-size: 14px; }
            h2 { font-size: 12px; }
            body, table td { font-size: 10px; }
            table { width: 100%; border-collapse:collapse; margin-bottom:0.5em; border-top:0px; border-bottom:0px; }
            table caption { font-variant:small-caps; }
            table.full { width:100%; }
            table.fixed { table-layout:fixed; }

            th,td { padding: 0.2em 0.5em; }
            thead th { font-size: 10px; border-bottom:1px #ddd solid; }
            tbody th { font-size: 10px; background:#e0e0e0; color:#666; border-bottom:1px solid #fff; text-align:left; }
            tbody th[scope="row"], tbody th.sub { background:#f0f0f0; }

            tbody th { border-bottom:1px solid #fff; text-align:left; }
            tbody td { border-bottom:1px solid #eee; }

            tfoot td { border-top: 1px #666 solid; }
        </style>
        <div style="width: 96%; max-width: 800px; min-width: 350px; margin: 0px auto; padding: 8px 0;">
            <div style="padding: 12px; background-color: #fff; box-shadow: 0 1px 3px #888;">
                <a href="{$BaseHref}" title="{$SiteConfig.Title} - {$SiteConfig.Tagline}">
                    <img src="<% if $SiteConfig.ShopLogo %>{$SiteConfig.ShopLogo.ScaleHeight(70).Link}<% else %>{$BaseHref}resources/vendor/silvercart/silvercart/client/img/logo.png<% end_if %>" alt="{$SiteConfig.Title}">
                </a>
                <hr/>
                {$Layout}
                <% include SilverCart\Email\Footer %>
            </div>
        </div>
    </body>
</html>