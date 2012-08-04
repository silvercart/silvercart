<% if TagsForCloud %>
<h2><% _t('SilvercartSearchCloudWidget.TITLE') %></h2>
<div class="silvercart-widget-content_frame silvercart-widget-search">
    <% loop TagsForCloud %>
    <a class="silvercart-search-cloud-widget-{$FontSize}" href="{$Link}">$SearchQuery</a>
    <% end_loop %>
</div>
<% end_if %>