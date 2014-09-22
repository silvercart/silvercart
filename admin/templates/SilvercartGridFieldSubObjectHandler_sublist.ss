<% if $count > 0 %>
<div class="sub-list ui-state-default" data-parent-record-id="{$ParentRecordID}" id="sub-list-{$ParentRecordID}" data-target-url="{$TargetURL}" data-action-id="{$ActionID}">
    <% loop $Items %>
    <span class="sub-list-record"><span class="sub-list-record-title">{$Title}</span> <span class="sub-list-record-remove btn-icon-chain--minus" data-record-id="{$ID}"></span></span>
    <% end_loop %>
</div>
<% end_if %>