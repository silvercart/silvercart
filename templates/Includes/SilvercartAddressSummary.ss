<% with CurrentMember %>
    <div class="row-fluid silvercart-address-equalize">
        <div class="span4">
        <% with SilvercartInvoiceAddress %>
            <% include SilvercartAddressDetailReadOnly %>
        <% end_with %>
            <hr class="mobile-show-sm"/>
        </div>
        <div class="span4">
        <% with SilvercartShippingAddress %>
            <% include SilvercartAddressDetailReadOnly %>
        <% end_with %>
        </div>
    </div>
<% end_with %>