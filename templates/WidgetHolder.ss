<% if isContentView %>
    <% if Content %>
        <% if DoUseWidgetContainer %>
        <div class="silvercart-widget-content-area {$ExtraCssClasses}">
            <div class="silvercart-widget-content-area_content">
        <% end_if %>
                $Content
        <% if DoUseWidgetContainer %>
            </div>
        </div>
        <% end_if %>
    <% end_if %>
<% else %>
    <% if Content %>
        <% if DoUseWidgetContainer %>
        <div class="widget {$ExtraCssClasses}">
            <div class="widget_content">
                <div class="silvercart-widget">
                    <div class="silvercart-widget_content">
        <% end_if %>
                        $Content
        <% if DoUseWidgetContainer %>
                    </div>
                </div>
            </div>
        </div>
        <% end_if %>
    <% end_if %>
<% end_if %>