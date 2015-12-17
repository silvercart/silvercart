<strong class="h2">$WidgetTitle</strong>
<% if BlogPosts %>
    <% loop BlogPosts %>
        <% if Top.isContentView %>
            
        <% else %>
            <div class="silvercart-widget-content_frame silvercart-latest-blog-post-widget">
                <p><strong>{$FormattedCreationDateWithTime} - {$Author}</strong></p>
                <p style="">$Title</p>
                <p style="color: #888888">&quot;$Content.LimitWordCount(12)&quot;</p>
                <a href="$Link" class="float_right"><% _t('SilvercartLatestBlogPostsWidget.SHOW_ENTRY') %> &rarr;</a>
                <br/>
            </div>
        <% end_if %>
    <% end_loop %>
<% end_if %>