<% if TagsForCloud %>
<strong class="h2"><% _t('SilvercartSearchCloudWidget.TITLE') %></strong>
<div class="silvercart-widget-content_frame silvercart-widget-search silvercart-widget-search-cloud">
    <% control TagsForCloud %>
    <a class="silvercart-search-cloud-widget-{$FontSize}" href="{$Link}">$SearchQuery</a>
    <% end_control %>
</div>
<% end_if %>