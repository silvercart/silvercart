<% if isContentView %>
    <% if Content %>
        <div class="silvercart-widget-content-area">
            <div class="silvercart-widget-content-area_content">
                $Content
            </div>
        </div>
    <% end_if %>
<% else %>
    <% if Content %>
        <div class="widget $ExtraCssClasses">
            <div class="widget_content">
                <div class="silvercart-widget">
                    <div class="silvercart-widget_content">
                        $Content
                    </div>
                </div>
            </div>
        </div>
    <% end_if %>
<% end_if %>