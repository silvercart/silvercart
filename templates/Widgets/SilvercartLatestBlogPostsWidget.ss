<h2>$WidgetTitle</h2>
<% if BlogPosts %>
    <% control BlogPosts %>
        <% if Top.isContentView %>
            
        <% else %>
            <div class="silvercart-widget-content_frame silvercart-latest-blog-post-widget">
                <h3>$Created.format(d.m.Y)</h3>
                <p>$Title</p>
                
                <div class="subcolumns">
                    <div class="silvercart-button-row clearfix">
                        <div class="silvercart-button-small left">
                            <div class="silvercart-button-small_content">
                                <a href="$Link">
                                    <% _t('SilvercartLatestBlogPostsWidget.SHOW_ENTRY') %>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <% end_if %>
    <% end_control %>
<% end_if %>