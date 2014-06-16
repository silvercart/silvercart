
<% with CurrentMember %>
    <div class="subcolumns silvercart-address-equalize">
        <div class="c50l">
            <div class="subcl">
                <% with SilvercartInvoiceAddress %>
                    <% include SilvercartAddressDetailReadOnly %>
                <% end_with %>
            </div>
        </div>
        <div class="c50r">
            <div class="subcr">
                <% with SilvercartShippingAddress %>
                    <% include SilvercartAddressDetailReadOnly %>
                <% end_with %>
            </div>
        </div>
    </div>
<% end_with %>