<% cached $WidgetCacheKey %>
    <% if $FrontTitle %>
        <h2>{$FrontTitle}</h2>
    <% end_if %>
    <% if $FrontContent %>
        {$FrontContent}
    <% end_if %>

    <div class="silvercart-widget-content_frame silvercart-pagelist-widget">
    <% if $getPages %>
        <div class="row-fluid">
        <% loop $getPages %>
            <div class="span6">
                <div class="silvercart-pagelist-widget_entry">
                    <div class="silvercart-pagelist-widget-title">
                        <% if $widgetTitle %>
                            <a href="{$Link}" title="{$widgetTitle}">{$widgetTitle}</a>
                        <% else_if $Title %>
                            <a href="{$Link}" title="{$Title}">{$Title}</a>
                        <% end_if %>
                    </div>
                    <div class="row-fluid">
                        <div class="span8">
                            <div class="silvercart-pagelist-widget-content">{$widgetText}</div>
                        </div>
                        <div class="span4">&nbsp;</div>
                    </div>
                    <% if $widgetImage %>
                        <a href="{$Link}" title="{$Title}" class="silvercart-pagelist-widget-image">{$widgetImage}</a>
                    <% end_if %>
                </div>
            </div>
            <% if $Even %>
        </div>
        <div class="row-fluid">
            <% end_if %>
        <% end_loop %>
        </div>
    <% end_if %>
    </div>
<% end_cached %>