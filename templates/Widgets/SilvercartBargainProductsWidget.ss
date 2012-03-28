<div class="silvercart-widget-headline">
    <% if FrontTitle %>
        <h2>$FrontTitle</h2>
    <% end_if %>
    <% if FrontContent %>
        $FrontContent
    <% end_if %>
</div>

<% if Top.useSlider %>
    <% if ProductPages %>
        <ul class="silvercart-widget-slider" id="SilvercartBargainProductsWidgetSlider{$ID}">
            <% control ProductPages %>
                <li<% if IsFirst %><% else %> style="display: none;"<% end_if %>>
                    <div>
                    <% if Top.isContentView %>
                        <% if Top.useListView %>
                            <% include SilvercartProductGroupPageList %>
                        <% else %>
                            <% include SilvercartProductGroupPageTile %>
                        <% end_if %>
                    <% else %>
                        <% if Top.useListView %>
                            <% include SilvercartWidgetProductBoxList %>
                        <% else %>
                            <% include SilvercartWidgetProductBoxTile %>
                        <% end_if %>
                    <% end_if %>
                    </div>
                </li>
            <% end_control %>
        </ul>
    <% end_if %>
<% else_if Top.useRoundabout %>
    <% if ProductPages %>
        <ul class="silvercart-widget-roundabout" id="SilvercartBargainProductsWidgetSlider{$ID}">
            <% control ProductPages %>
                <li>
                    <div>
                    <% if Top.isContentView %>
                        <% if Top.useListView %>
                            <% include SilvercartProductGroupPageList %>
                        <% else %>
                            <% include SilvercartProductGroupPageTile %>
                        <% end_if %>
                    <% else %>
                        <h2>$Top.ProductGroupTitle</h2>
                        <% if Top.useListView %>
                            <% include SilvercartWidgetProductBoxList %>
                        <% else %>
                            <% include SilvercartWidgetProductBoxTile %>
                        <% end_if %>
                    <% end_if %>
                    </div>
                </li>
            <% end_control %>
        </ul>
    <% end_if %>
<% else %>
     <% if Top.isContentView %>
        <% if Top.useListView %>
            <% include SilvercartProductGroupPageList %>
        <% else %>
            <% include SilvercartProductGroupPageTile %>
        <% end_if %>
    <% else %>
        <% if Top.useListView %>
            <% include SilvercartWidgetProductBoxList %>
        <% else %>
            <% include SilvercartWidgetProductBoxTile %>
        <% end_if %>
    <% end_if %>
<% end_if %>