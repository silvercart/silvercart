<% if $HasMoreProductsThan(0) %>
    <% if $ActiveProducts && $hasMoreGroupViewsThan(1) %>      
    <div class="inline pull-right">
        <div class="btn-group">
        <% loop $GroupViews %>
            <% if $isActive %>
            <a class="btn btn-small btn-primary active"  title="{$Label}">
                <i class="icon-groupview-{$Code}"></i>
            </a>
                <% else %>
            <a class="btn btn-small" href="{$CurrentPage.Link}switchGroupView/{$Code}" title="{$Label}">
                <i class="icon-groupview-{$Code}"></i>
            </a>
            <% end_if %>
        <% end_loop %>
        </div> 
    </div>
    <% else_if $Children && $hasMoreGroupHolderViewsThan(1) %>
<div class="inline pull-right">
    <div class="btn-group">
            <% loop $GroupHolderViews %>
                <% if $isActiveHolder %>
        <a class="btn btn-small btn-primary active"  title="{$Label}">
            <i class="icon-groupview-{$Code}"></i>
        </a>
                <% else %>
        <a class="btn btn-small" href="{$CurrentPage.Link}switchGroupView/{$Code}" title="{$Label}">
            <i class="icon-groupview-{$Code}"></i>
        </a>
    </div>
</div>
            <% end_if %>
        <% end_loop %>
    <% end_if %>
<% end_if %>