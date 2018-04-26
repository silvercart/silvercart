{$Controller.ContentStep6}
<% if $PaymentMethod.ErrorList %>
<div class="alert alert-danger">
    <strong><%t SilverCart\Admin\Model\Config.ERROR_TITLE 'An error occured!' %></strong><br/>
    <ul>
    <% loop $PaymentMethod.ErrorList %>
        <li>{$error}</li>
    <% end_loop %>
    </ul>
    <p><%t SilverCart\Model\Pages\Page.CHANGE_PAYMENTMETHOD_CALL 'Please choose another payment method or contact the shop owner.' %></p>
    <a class="btn btn-primary" href="{$Controller.PaymentStepLink}"><%t SilverCart\Model\Pages\Page.CHANGE_PAYMENTMETHOD_LINK 'Choose another payment method' %></a>
    <a class="btn" href="{$Controller.PageByIdentifierCode('SilvercartContactFormPage').Link}"><%t SilverCart\Model\Pages\Page.CONTACT_FORM 'Contact form' %></a>
</div>
<% end_if %>
{$CustomOutput}