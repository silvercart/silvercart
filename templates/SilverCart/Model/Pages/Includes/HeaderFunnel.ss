<header>
    <% include SilverCart/Model/Pages/HeaderPreHeader %>
    <div id="main-header" class="clearfix">
        <div class="container">

            <div class="clearfix row">
                <div class="siteLogo pull-left span6">
                    <a class="h1" href="{$PageByIdentifierCode(SilvercartFrontPage).Link}" title="$SiteConfig.Title $SiteConfig.Tagline">
                        <span>{$SiteConfig.Title} - {$SiteConfig.Tagline}</span>
                    </a>
                </div>
                <div class="smtb span6">
                </div>
            </div>
        </div>
    </div>
    <div style="height:41px"></div>
</header>