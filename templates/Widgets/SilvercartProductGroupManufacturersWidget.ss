<% if ShowWidget %>
    <% if SilvercartManufacturers %>
        <% if Title %>
            <h2 <% if isFilteredByManufacturer %>class="is-filtered"<% end_if %>>
                $Title
            </h2>
        <% end_if %>

        <div class="vlist">
            <ul>
                <% control SilvercartManufacturers %>
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
                <% end_control %>
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
<% end_if %>