<% if $TagsForCloud %>
<div class="section-header clearfix">
<h3><%t SilverCart\Model\Widgets\SearchCloudWidget.TITLE 'Most frequent search terms' %></h3>
</div>
<div class="silvercart-widget-content_frame silvercart-widget-search silvercart-widget-search-cloud ">
    <ul class="nav nav-pills">
    <% loop $TagsForCloud %>
    <li><a  class="silvercart-search-cloud-widget-{$FontSize}" href="{$Link}"><i>{$SearchQuery}</i></a></li>
    <% end_loop %>
    </ul>
</div>
<% end_if %>