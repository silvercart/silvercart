<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>
        <div class="section-header clearfix">
        <h1>{$Title}</h1>
        </div>
        $Content
        $Form
        $PageComments

<% if Carriers %>
    <% loop Carriers %>
        <% if AllowedSilvercartShippingMethods %>
        <div class="section-header clearfix special">
        <h2>$Title</h2>
        </div>
            <% loop AllowedSilvercartShippingMethods %>
                <% if isActive %>
                    <% if ShowOnShippingFeesPage %>
            <table class="silvercart-default-table table table-striped table-bordered table-padded">
                <caption>
                    <h3>{$Title}</h3>
                    <% if DescriptionForShippingFeesPage %>
                        {$DescriptionForShippingFeesPage}
                    <% else_if Description %>
                        {$Description}
                    <% end_if %>
                </caption>
                <thead>
                    <tr>
                        <th class="align-left col-20"><% _t('SilvercartProduct.WEIGHT') %> ({$SilvercartShippingFees.First.MaximumWeightUnitAbreviation})</th>
                        <th class="align-left col-65"><% _t('SilvercartZone.SINGULARNAME') %></th>
                        <th class="align-right"><% _t('SilvercartProduct.PRICE') %></th>
                    </tr>
                </thead>
                <tbody>
                <% loop SilvercartShippingFees %>
                    <tr class="$EvenOdd">
                        <td class="align-right align-top"><% if UnlimitedWeight %><% _t('SilvercartShippingFee.UNLIMITED_WEIGHT') %><% else %>$MaximumWeightNice<% end_if %></td>
                        <td class="align-left">
                            <div class="country-list">
                            <% with SilvercartZone %>
                                <b>$Title:</b><br/>
                                <% if hasAllCountries %>
                                    <strong><% _t('SilvercartZone.VALID_FOR_ALL_AVAILABLE') %></strong>
                                <% else %>
                                    <% loop SilvercartCountries %>
                                        <% if Active %>
                                            $Title<br/>
                                        <% end_if %>
                                    <% end_loop %>
                                <% end_if %>
                            <% end_with %>
                            </div>
                        </td>
                        <td class="align-right align-top">$PriceFormatted <% if PostPricing %>*<% end_if %></td>
                    </tr>
                <% end_loop %>
                <% if hasFeeWithPostPricing %>
                    <tr class="info">
                        <td class="align-left" colspan="3">* <% _t('SilvercartShippingFee.POST_PRICING_INFO') %></td>
                    </tr>
                <% end_if %>
                </tbody>
            </table>
                    <% end_if %>
                <% end_if %>
            <% end_loop %>
        <% end_if %>
    <% end_loop %>
<% end_if %>
    </div>
 <aside class="span3">
        $SubNavigation
        $InsertWidgetArea(Sidebar)
    </aside><!--end aside-->
</div>
