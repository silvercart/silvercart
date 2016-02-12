<% if BlogPosts %>
<div class="blog-tab">
    <div class="tab-content">
        <div id="popular-post" class="tab-pane active">
    <% loop BlogPosts %>
        <% if Top.isContentView %>
        <% else %>
            <div class="clearfix">
                <div class="thumbImage">
                    <small>$Created.format(d.m.Y)</small>
                </div>
                <div class="sc-product-shortinfo">
                    <div class="sc-product-title">
                        <a class="highlight" href="{$Link}" title="{$Title}">{$Title}</a>
                        <br>
                        <a class="btn btn-small btn-link pull-right" href="{$Link}"><% _t('SilvercartLatestBlogPostsWidget.SHOW_ENTRY') %> <i class="icon icon-caret-right"></i></a>
                    </div>
                </div>
            </div>
        <% end_if %>
    <% end_loop %>
        </div>
    </div>
</div>
<% end_if %>