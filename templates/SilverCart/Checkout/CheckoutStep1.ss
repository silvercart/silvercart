<% if $ShowRegistrationForm %>
    {$RegisterRegularCustomerForm}
<% else %>
{$Controller.ContentStep1}
<div class="row-fluid">
    <div class="span6">
        <div class="form-vertical">
            <h4><%t SilverCart\Model\Pages\MyAccountHolder.ALREADY_HAVE_AN_ACCOUNT 'Do you already have an account?' %></h4>
            {$CheckoutLoginForm}
        </div>
    </div>
    <div class="span6">
        <div class="form-vertical">
            <h4><%t SilverCart\Checkout\CheckoutStep1.NewCustomer 'You are a new customer?' %></h4>
            {$CheckoutNewCustomerForm}
        </div>
    </div>
</div>
{$CustomOutput}
<% end_if %>