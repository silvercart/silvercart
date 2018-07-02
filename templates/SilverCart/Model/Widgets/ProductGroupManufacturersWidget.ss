<% if $ShowWidget %>
    <% if $Manufacturers %>
        <% if $FrontTitle %>
            <h2 <% if $isFilteredByManufacturer %>class="is-filtered"<% end_if %>>{$FrontTitle}</h2>
        <% end_if %>
        <div>
            <ul class="unstyled">
        <% loop $Manufacturers %>
            <% if $Title %>
                <li><a href="{$Link}" title="{$Title}"><% if $logo %>{$logo.Pad(220,100)}<% else %>{$Title}<% end_if %></a></li>
            <% end_if %>
        <% end_loop %>
            </ul>
        </div>

        <% if $isFilteredByManufacturer %>
            <a class="btn btn-small" href="{$PageLink}" title="<%t SilverCart\Model\Widgets\ProductGroupManufacturersWidget.RESETFILTER 'Show all' %>">
                <%t SilverCart\Model\Widgets\ProductGroupManufacturersWidget.RESETFILTER 'Show all' %>
            </a>
        <% end_if %>
    <% end_if %>
<% end_if %>