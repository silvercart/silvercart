<% if SCProductGroupPages %>
    <div class="silvercart-widget-slidorion-productgroup">
        <% if FrontTitle %>
            <h2>$FrontTitle</h2>
        <% end_if %>
        <% if FrontContent %>
            $FrontContent
        <% end_if %>

        <div id="silvercart-slidorion-{$ID}" class="silvercart-widget-slidorion-productgroup-slider">
            <div class="silvercart-slidorion-slider">
                <% control SCProductGroupPages %>
                    <div class="silvercart-slidorion-slide">
                        <% control GroupPicture %>
                            $SetRatioSize(426,385)
                        <% end_control %>
                    </div>
                <% end_control %>
            </div>

            <div class="silvercart-slidorion-accordeon">
                <% control SCProductGroupPages %>
                    <div class="silvercart-slidorion-link-header">$MenuTitle</div>
                    <div class="silvercart-slidorion-link-content">$Content</div>
                <% end_control %>
            </div>
        </div>
    </div>
<% end_if %>

