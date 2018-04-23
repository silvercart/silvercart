<% if $ShowWidget %>
    <% if $ExtraCssClasses %>
<div class="{$ExtraCssClasses}">
    <% end_if %>
    <% if $FrontTitle %>
    <div class="section-header clearfix">
        <h3>{$FrontTitle}</h3>
    </div>
    <% end_if %>

    <% if $FrontContent %>
        {$FrontContent.RAW}
    <% end_if %>

    <div class="silvercart-product-group-page-control-top">
        <% include SilverCart/Model/Pages/ProductGroupPageControlsTop %>
    </div>
    <div class="silvercart-product-group-page">
        {$RenderProductGroupPageGroupView}
    </div>
    <div class="silvercart-product-group-page-control-top">
        <% include SilverCart/Model/Pages/ProductGroupPageControlsBottom %>
    </div>
    <% if $ExtraCssClasses %>
</div>
    <% end_if %>
<% end_if %>