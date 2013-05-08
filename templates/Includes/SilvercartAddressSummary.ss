
<% control CurrentMember %>
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