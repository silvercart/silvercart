<% cached WidgetCacheKey %>
    <div class="silvercart-widget-productgroupitems-headline">
        <% if FrontTitle %>
            <strong class="h2">$FrontTitle</strong>
        <% end_if %>
        <% if FrontContent %>
            $FrontContent.RAW
        <% end_if %>
    </div>

    <% if Top.useSlider %>
        <% if ProductPages %>
            <ul class="silvercart-widget-productgroupitems-slider" id="SilvercartProductGroupItemsWidgetSlider{$ID}">
                <% loop ProductPages %>
                    <li<% if IsFirst %><% else %> style="display: none;"<% end_if %>>
                        <div>
                            $Content
                        </div>
                    </li>
                <% end_loop %>
            </ul>
        <% end_if %>
    <% else_if Top.useRoundabout %>
        <% if ProductPages %>
            <ul class="silvercart-widget-productgroupitems-roundabout" id="SilvercartProductGroupItemsWidgetSlider{$ID}">
                <% loop ProductPages %>
                    <li>
                        <div>
                            $Content
                        </div>
                    </li>
                <% end_loop %>
            </ul>
        <% end_if %>
    <% else %>
        $ElementsContent
    <% end_if %>
<% end_cached %>
