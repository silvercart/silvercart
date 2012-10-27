<% if SilvercartManufacturers %>
    <% if Title %>
        <h2>
            $Title
        </h2>
    <% end_if %>

    <div class="vlist">
        <ul>
            <% loop SilvercartManufacturers %>
                <% if Title %>
                    <li>
                        <a href="$Link" title="$Title">
                            <% if logo %>
                                $logo.SetRatioSize(200,100)
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
        <div class="silvercart-button">
            <div class="silvercart-button_content">
                <a href="$PageLink">
                    <% _t('SilvercartProductGroupManufacturersWidget.RESETFILTER') %>
                </a>
            </div>
        </div>
    <% end_if %>
<% end_if %>