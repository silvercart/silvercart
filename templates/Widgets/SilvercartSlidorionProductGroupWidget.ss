<% cached WidgetCacheKey %>
    <% if ImagesToDisplay %>
        <div class="silvercart-widget-slidorion-productgroup">
            <% if FrontTitle %>
                <strong class="h2">{$FrontTitle}</strong>
            <% end_if %>
            <% if FrontContent %>
                $FrontContent
            <% end_if %>

            <div id="silvercart-slidorion-{$ID}" class="silvercart-widget-slidorion-productgroup-slider">
                <div class="silvercart-slidorion-slider">
                    $getGroupPictureList
                </div>

                <div class="silvercart-slidorion-accordeon">
                    <% loop ImagesToDisplay %>
                        <div class="silvercart-slidorion-link-header"><span>{$Title}</span></div>
                        <div class="silvercart-slidorion-link-content">{$Description}</div>
                    <% end_loop %>
                </div>
            </div>
        </div>
    <% end_if %>
<% end_cached %>
