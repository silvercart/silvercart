<div class="cms-sitename silvercart">
    <a href="{$ApplicationLink}" class="cms-sitename__link font-icon-silvercart font-icon-large" target="_blank" title="{$ApplicationName} (Version - {$SilvercartVersion} | SilverStripe {$CMSVersion})">
        <span class="sr-only">{$ApplicationName} <% if $SilvercartVersion %><abbr class="cms-sitename__version">{$SilvercartVersion}</abbr><% end_if %></span>
    </a>
    <a class="cms-sitename__title" href="{$BaseHref}" target="_blank"><% if $SiteConfig %>{$SiteConfig.Title}<% else %>{$ApplicationName}<% end_if %></a>
</div>
