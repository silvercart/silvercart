<% with $CurrentMember %>
    <div class="row-fluid silvercart-address-equalize">
        <div class="span4">
        <% with $InvoiceAddress %>
            <% include SilverCart/Model/Pages/AddressDetailReadOnly %>
        <% end_with %>
            <hr class="mobile-show-sm"/>
        </div>
        <div class="span4">
        <% with $ShippingAddress %>
            <% include SilverCart/Model/Pages/AddressDetailReadOnly %>
        <% end_with %>
        </div>
    </div>
<% end_with %>