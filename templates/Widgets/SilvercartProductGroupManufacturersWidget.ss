<% if ShowWidget %>
    <% if SilvercartManufacturers %>
        <% if Title %>
            <h2 <% if isFilteredByManufacturer %>class="is-filtered"<% end_if %>>
                $Title
            </h2>
        <% end_if %>

        <div>
            <ul class="unstyled">
                <% loop SilvercartManufacturers %>
                    <% if Title %>
                        <li>
                            <a href="$Link" title="$Title">
                                <% if logo %>
                                    $logo.SetRatioSize(150,100)
                                <% else %>
                                    <p>$Title</p>
                                <% end_if %>
                            </a>
                        </li>
                    <% end_if %>
                <% end_loop %>
            </ul>
        </div>

        <% if isFilteredByManufacturer %>
                    <a class="btn btn-small" href="$PageLink" title="<% _t('SilvercartProductGroupManufacturersWidget.RESETFILTER') %>">
                        <% _t('SilvercartProductGroupManufacturersWidget.RESETFILTER') %>
                    </a>
        <% end_if %>
    <% end_if %>
<% end_if %>