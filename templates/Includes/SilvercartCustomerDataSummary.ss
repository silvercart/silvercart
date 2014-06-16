
<% with CurrentMember %>
    <table>
        <colgroup>
            <col width="20%"></col>
            <col width="80%"></col>
        </colgroup>
        <tbody>
            <tr>
                <td><strong>$fieldLabel(FullName):</strong></td>
                <td>$TranslatedSalutation $FirstName $Surname</td>
            </tr>
            <tr>
                <td><strong>$fieldLabel(CustomerNumberShort):</strong></td>
                <td>$CustomerNumber</td>
            </tr>
            <tr>
                <td><strong>$fieldLabel(EmailAddress): </strong></td>
                <td>$Email</td>
            </tr>
        </tbody>
    </table>
<% end_with %>