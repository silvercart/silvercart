<% cached WidgetCacheKey %>
    <div class="silvercart-widget-headline">
        <% if FrontTitle %>
            <strong class="h2">$FrontTitle</strong>
        <% end_if %>
        <% if FrontContent %>
            $FrontContent
        <% end_if %>
    </div>

    <% if Top.useSlider %>
        <% if ProductPages %>
            <ul class="silvercart-widget-slider" id="SilvercartBargainProductsWidgetSlider{$ID}">
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
            <ul class="silvercart-widget-roundabout" id="SilvercartBargainProductsWidgetSlider{$ID}">
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