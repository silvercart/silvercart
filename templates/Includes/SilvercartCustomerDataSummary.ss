
<% with CurrentMember %>
<table class="table table-condensed">
    <tbody>
        <tr>
            <td class="text-left nowrap"><strong>$fieldLabel(FullName):</strong></td>
            <td class="text-left full" style="width: 100%">$TranslatedSalutation $FirstName $Surname</td>
        </tr>
        <tr>
            <td class="text-left nowrap"><strong>$fieldLabel(CustomerNumberShort):</strong></td>
            <td class="text-left full">$CustomerNumber</td>
        </tr>
        <tr>
            <td class="text-left nowrap"><strong>$fieldLabel(EmailAddress): </strong></td>
            <td class="text-left full">$Email</td>
        </tr>
    </tbody>
</table>
<% end_with %>