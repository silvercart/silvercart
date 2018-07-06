<% cached $WidgetCacheKey %>
    <% if $ImagesToDisplay %>
    <div class="silvercart-widget-slidorion-productgroup">
        <% if $FrontTitle %>
        <div class="section-header clearfix">
            <h3>$FrontTitle</h3> 
        </div>
        <% end_if %>
        <% if $FrontContent %>
            {$FrontContent}
        <% end_if %>
        <div id="silvercart-slidorion-{$ID}" class="silvercart-widget-slidorion-productgroup-slider">
            <div class="silvercart-slidorion-slider">
            <% loop $ImagesToDisplay %>
                <div class="silvercart-slidorion-slide" style="background: url('{$resizedImage.URL}') no-repeat center;">
                    <div class="silvercart-slidorion-slide-prev"><div class="arrow"><div></div></div></div>
                <% if $Link %>
                    <a class="silvercart-slidorion-slide-click" href="{$Link}"></a>
                <% end_if %>
                    <div class="silvercart-slidorion-slide-next"><div class="arrow_outer"><div class="arrow"><div></div></div></div></div>
                    {$Content}
                </div>
            <% end_loop %>
            </div>
            <div class="silvercart-slidorion-accordeon">
            <% loop $ImagesToDisplay %>
                <div class="silvercart-slidorion-link-header"><span>{$Title}</span></div>
                <div class="silvercart-slidorion-link-content">{$Description}</div>
            <% end_loop %>
            </div>
        </div>
    </div>
    <% end_if %>
<% end_cached %>