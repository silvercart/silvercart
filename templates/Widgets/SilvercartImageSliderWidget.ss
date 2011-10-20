<% if slideImages %>
    <div>
        <% if FrontTitle %>
            <h2>$FrontTitle</h2>
        <% end_if %>
        <% if FrontContent %>
            $FrontContent
        <% end_if %>
    </div>

    <ul class="silvercart-widget-image-slider" id="SilvercartImageSliderWidget{$ID}">
        <% control slideImages %>
            <li<% if First %><% else %> style="display: none;"<% end_if %>>
                <div>
                    <% if LinkedSite %>
                        <a href="$LinkedSite.Link">
                    <% end_if %>
                    $Image.SetRatioSize(660,414)
                    <% if LinkedSite %>
                        </a>
                    <% end_if %>
                </div>
            </li>
        <% end_control %>
    </ul>
<% end_if %>