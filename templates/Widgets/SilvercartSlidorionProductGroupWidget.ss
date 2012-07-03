<% cached WidgetCacheKey %>
    <% if SilvercartImages %>
        <div class="silvercart-widget-slidorion-productgroup">
            <% if FrontTitle %>
                <h2>$FrontTitle</h2>
            <% end_if %>
            <% if FrontContent %>
                $FrontContent
            <% end_if %>

            <div id="silvercart-slidorion-{$ID}" class="silvercart-widget-slidorion-productgroup-slider">
                <div class="silvercart-slidorion-slider">
                    $getGroupPictureList
                </div>

                <div class="silvercart-slidorion-accordeon">
                    <% control SilvercartImages %>
                        <div class="silvercart-slidorion-link-header"><span>$Title</span></div>
                        <div class="silvercart-slidorion-link-content">$Description</div>
                    <% end_control %>
                </div>
            </div>
        </div>
    <% end_if %>
<% end_cached %>