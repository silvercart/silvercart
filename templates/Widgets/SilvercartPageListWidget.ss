<% cached WidgetCacheKey %>
    <% if FrontTitle %>
        <strong class="h2">$FrontTitle</strong>
    <% end_if %>
    <% if FrontContent %>
        $FrontContent
    <% end_if %>

    <div class="silvercart-widget-content_frame silvercart-pagelist-widget">
        <% if getPages %>
            <div class="subcolumns">
            <% loop getPages %>
                <div class="c50<% if Odd %>l<% else %>r<% end_if %>">
                    <div class="subc<% if Odd %>l<% else %>r<% end_if %>">
                        <div class="silvercart-pagelist-widget_entry">
                            <div class="silvercart-pagelist-widget-title">
                                <% if widgetTitle %>
                                    <a href="$Link" title="$widgetTitle">$widgetTitle</a>
                                <% else_if Title %>
                                    <a href="$Link" title="$Title">$Title</a>
                                <% end_if %>
                            </div>
                            <div class="subcolumns">
                                <div class="c60l">
                                    <div class="silvercart-pagelist-widget-content">$widgetText</div>
                                </div>
                                <div class="c40r">
                                    &nbsp;
                                </div>
                            </div>
                            <% if widgetImage %>
                                <a href="$Link" title="$Title" class="silvercart-pagelist-widget-image">
                                    $widgetImage
                                </a>
                            <% end_if %>
                        </div>
                    </div>
                </div>
            <% if Even %>
                </div>
                <div class="subcolumns">
            <% end_if %>
            <% end_loop %>
            </div>
        <% end_if %>
    </div>
<% end_cached %>