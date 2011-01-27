<div class="subcolumns">
    <div class="c50l">
        <div id="ShippingInfos">
        <h3>Lieferadresse:</h3>
        <% if CurrentMember.shippingAddress %>
            <% control CurrentMember.shippingAddress %>
                <% include AddressTable %>
                <a href="/meinkonto/adressuebersicht/adressansicht/{$ID}">bearbeiten</a>
            <% end_control %>
        <% else %>
            <p>
                Entschuldigung $CurrentMember.FirstName $CurrentMember.Surname, Sie haben keine Lieferadresse gepflegt.
            </p>
        <% end_if %>
        </div>
    </div>
    <div class="c50r">
        <div id="InvoiceInfos">
            <h3>Rechnungsadresse:</h3>
            <% if CurrentMember.invoiceAddress %>
                <% control CurrentMember.invoiceAddress %>
                    <% include AddressTable %>
                        <a href="/meinkonto/adressuebersicht/adressansicht/{$ID}">bearbeiten</a>
                    <% end_control %>
            <% else %>
                <p>
                    Entschuldigung $CurrentMember.FirstName $CurrentMember.Surname, Sie haben keine Rechnungsadresse gepflegt.
                </p>
            <% end_if %>
        </div>
    </div>
</div>