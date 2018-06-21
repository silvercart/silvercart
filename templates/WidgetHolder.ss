<% if $isContentView %>
    <% if $Content %>
        <% if $DoUseWidgetContainer %>
<div class="widget clearfix silvercart-widget-content-area {$ExtraCssClasses}<% if WidgetSet.UseAsSlider %>silvercart-widget-slider-element" rel="silvercart-widget-slider-{$WidgetSet.ID}<% end_if %>" id="widget-{$ID}">
    <div class="silvercart-widget-content-area_content">
        <% end_if %>
        {$Content}
        <% if $DoUseWidgetContainer %>
    </div>
</div>
        <% end_if %>
    <% end_if %>
<% else_if $Content %>
    <% if $DoUseWidgetContainer %>
<div class="widget clearfix silvercart-widget {$ExtraCssClasses}" id="widget-{$ID}">
    <% end_if %>
    <% if $Headline %>
        <div class="section-header clearfix">
            <h3>{$Headline}</h3>
             <% if $HeadlineLink %>
            <div class="pagers"><div class="btn-toolbar"><button class="btn btn-mini"><a href="{$Link}">Know More</a></button></div></div>
            <% end_if %>
        </div>
    <% end_if %>
        {$Content}
    <% if $DoUseWidgetContainer %>
</div>
    <% end_if %>
<% end_if %>