<% cached WidgetCacheKey %>
        <div class="section-header clearfix">
            <% if FrontTitle %>
                <h3>$FrontTitle</h3>
            <% end_if %>
            <% if useSlider %>
                <div class="pagers">
                    <div class="btn-toolbar">
                        <div class="btn-group">
                        <% if transitionEffect = horizontalSlide %>
                            <button class="btn btn-mini vNext" id="widget-{$ID}"><i class="icon-caret-left"></i></button>
                            <button class="btn btn-mini vPrev" id="widget-{$ID}"><i class="icon-caret-right"></i></button>
                        <% else %>
                            <button class="btn btn-mini vNext" id="widget-{$ID}"><i class="icon-caret-down"></i></button>
                            <button class="btn btn-mini vPrev" id="widget-{$ID}"><i class="icon-caret-up"></i></button>
                        <% end_if %>
                        </div>
                    </div>
                </div>
            <% end_if %>
        </div>
        <% if FrontContent %>
            $FrontContent.RAW
        <% end_if %>

    <% if Top.useSlider %>
        <div class="sc-products clearfix cycle-slideshow <% if transitionEffect = verticalSlide %>vertical<% end_if %>"
                data-cycle-fx="<% if transitionEffect = horizontalSlide %>scrollHorz<% else_if transitionEffect = verticalSlide %>carousel<% else %>default<% end_if %>"
                data-cycle-speed="300"
                data-cycle-pause-on-hover="true"
                data-cycle-slides="> div"
                data-cycle-next="#widget-{$ID} .vPrev"
                data-cycle-prev="#widget-{$ID} .vNext"
                data-cycle-carousel-visible="{$numberOfProductsToShow}"
                data-cycle-carousel-vertical="true"
                data-cycle-swipe="true"
                data-cycle-swipe-fx="scrollHorz"
                <% if Autoplay %>
                <% else %>
                data-cycle-timeout="0"
                <% end_if %>
                >
            {$ElementsContent}
        </div>
    <% else_if Top.useRoundabout %>
        <% if ProductPages %>
        <div id="productSlider" class="carousel slide">
            <ul class="carousel-inner silvercart-widget-productgroupitems-roundabout" id="SilvercartProductGroupItemsWidgetSlider{$ID}">
                <% loop ProductPages %>
                    <li class="item">
                        <div>
                            $Content
                        </div>
                    </li>
                <% end_loop %>
            </ul>
        </div>
        <% end_if %>
    <% else %>
        {$ElementsContent}
    <% end_if %>
    
<% end_cached %>
