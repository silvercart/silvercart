<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <% if CurrentRegisteredCustomer %>
            <h2>$Title</h2>
        
            $Content
            
            <div class="silvercart-section">
                <div class="silvercart-section_content clearfix">
                    <h3><% _t('SilvercartMyAccountHolder.YOUR_MOST_CURRENT_ORDERS') %>:</h3>

                    <% if CurrentMembersOrders %>
                        <table id="silvercart-order-holder-table-id" class="full">
                            <thead>
                                <tr>
                                    <th><% _t('SilvercartPage.ORDER_DATE','order date') %></th>
                                    <th><% _t('SilvercartOrder.ORDERNUMBER','Ordernumber') %></th>
                                    <th><% _t('SilvercartPage.ORDERED_PRODUCTS','ordered products') %></th>
                                    <th><% _t('SilvercartOrderStatus.SINGULARNAME') %></th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <% control CurrentMembersOrders(3) %>
                                    <tr>
                                        <td>
                                            <a href="$Top.PageByIdentifierCodeLink(SilvercartOrderDetailPage)$ID">$Created.Nice</a>
                                        </td>
                                        <td>
                                            <a href="$Top.PageByIdentifierCodeLink(SilvercartOrderDetailPage)$ID">$OrderNumber</a>
                                        </td>
                                        <td>
                                            <a href="$Top.PageByIdentifierCodeLink(SilvercartOrderDetailPage)$ID">
                                            <% control SilvercartOrderPositions %>
                                                $Title <% if Last %><% else %> | <% end_if %>
                                            <% end_control %>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="$Top.PageByIdentifierCodeLink(SilvercartOrderDetailPage)$ID">
                                                $SilvercartOrderStatus.Title
                                            </a>
                                        </td>
                                        <td>
                                            <div class="silvercart-button">
                                                <div class="silvercart-button_content">
                                                    <a href="$Top.PageByIdentifierCodeLink(SilvercartOrderDetailPage)$ID"><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <% end_control %>
                            </tbody>
                        </table>
                    <% else %>
                        <p><% _t('SilvercartPage.NO_ORDERS','You do not have any orders yet') %></p>
                    <% end_if %>

                    <div class="silvercart-button right">
                        <div class="silvercart-button_content">
                            <a href="$PageByIdentifierCodeLink(SilvercartOrderHolder)"><% _t('Silvercart.MORE') %></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="silvercart-section">
                <div class="silvercart-section_content clearfix">
                    <h3><% _t('SilvercartMyAccountHolder.YOUR_CURRENT_ADDRESSES') %>:</h3>

                    <% control CurrentRegisteredCustomer %>
                        <div class="subcolumns silvercart-address-equalize">
                            <div class="c50l">
                                <div class="subcl">
                                    <% control SilvercartInvoiceAddress %>
                                        <% include SilvercartAddressDetailReadOnly %>
                                    <% end_control %>
                                </div>
                            </div>
                            <div class="c50r">
                                <div class="subcr">
                                    <% control SilvercartShippingAddress %>
                                        <% include SilvercartAddressDetailReadOnly %>
                                    <% end_control %>
                                </div>
                            </div>
                        </div>
                    <% end_control %>

                    <div class="silvercart-button right">
                        <div class="silvercart-button_content">
                            <a href="$PageByIdentifierCodeLink(SilvercartAddressHolder)"><% _t('Silvercart.MORE') %></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="silvercart-section">
                <div class="silvercart-section_content clearfix">
                    <h3><% _t('SilvercartMyAccountHolder.YOUR_PERSONAL_DATA') %>:</h3>

                    <% control CurrentRegisteredCustomer %>
                        <p>
                            $TranslatedSalutation $FirstName $Surname
                        </p>
                        <table>
                            <colgroup>
                                <col width="20%"></col>
                                <col width="80%"></col>
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td><strong><% _t('SilvercartCustomer.CUSTOMERNUMBER_SHORT') %>:</strong></td>
                                    <td>$CustomerNumber</td>
                                </tr>
                                <tr>
                                    <td><strong><% _t('SilvercartAddress.EMAIL') %>: </strong></td>
                                    <td>$Email</td>
                                </tr>
                            </tbody>
                        </table>
                    <% end_control %>

                    <div class="silvercart-button right">
                        <div class="silvercart-button_content">
                            <a href="$PageByIdentifierCodeLink(SilvercartDataPage)"><% _t('Silvercart.MORE') %></a>
                        </div>
                    </div>
                </div>
            </div>
        <% else %>
            <% include SilvercartMyAccountLoginOrRegister %>
        <% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% if CurrentRegisteredCustomer %>
            $SubNavigation
        <% end_if %>
        
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>