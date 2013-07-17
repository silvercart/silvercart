<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <h2>$Title</h2>

        $Content
        $Form
        $PageComments

<% if Carriers %>
    <% control Carriers %>
        <% if AllowedSilvercartShippingMethods %>
        <h3>$Title</h3>
            <% control AllowedSilvercartShippingMethods %>
                <% if isActive %>
                    <% if ShowOnShippingFeesPage %>
        <div class="subcolumns">
            <div class="c20l">
                <h4>$Title</h4>
            </div>
            <div class="c80r">
                <% if DescriptionForShippingFeesPage %>
                    {$DescriptionForShippingFeesPage}
                <% else_if Description %>
                    {$Description}
                <% end_if %>
                <table class="full silvercart-default-table">
                    <colgroup>
                        <col width="20%"></col>
                        <col width="65%"></col>
                        <col width="15%"></col>
                    </colgroup>
                    <tr>
                        <th class="align_right"><% _t('SilvercartProduct.WEIGHT') %> ({$SilvercartShippingFees.First.MaximumWeightUnitAbreviation})</th>
                        <th><% _t('SilvercartZone.SINGULARNAME') %></th>
                        <th class="align_right"><% _t('SilvercartProduct.PRICE') %></th>
                    </tr>
                    <% control SilvercartShippingFees %>
                    <tr class="$EvenOdd">
                        <td class="align_right align_top padding_right"><% if UnlimitedWeight %><% _t('SilvercartShippingFee.UNLIMITED_WEIGHT') %><% else %>$MaximumWeightNice<% end_if %></td>
                        <td>
                            <% control SilvercartZone %>
                                <b>$Title:</b><br/>
                                <% if hasAllCountries %>
                                    <strong><% _t('SilvercartZone.VALID_FOR_ALL_AVAILABLE') %></strong>
                                <% else %>
                                    <% control SilvercartCountries %>
                                        <% if Active %>
                                            $Title<br/>
                                        <% end_if %>
                                    <% end_control %>
                                <% end_if %>
                            <% end_control %>
                        </td>
                        <td class="align_right align_top">$PriceFormatted <% if PostPricing %>*<% end_if %></td>
                    </tr>
                    <% end_control %>
                </table>
                <% if hasFeeWithPostPricing %>* <% _t('SilvercartShippingFee.POST_PRICING_INFO') %><% end_if %>
            </div>
        </div>
                    <% end_if %>
                <% end_if %>
            <% end_control %>
        <% end_if %>
    <% end_control %>
<% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $SubNavigation
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
