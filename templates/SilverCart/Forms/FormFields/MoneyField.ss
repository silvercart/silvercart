<div class="form__fieldgroup <% if $extraClass %>{$extraClass}<% end_if %>" id="{$ID}" <% include SilverStripe/Forms/AriaAttributes %>>
    <% if $CurrencyIsReadonly %>
        {$CurrencyField.FieldHolder}
        {$AmountField.Field} {$CurrencyField.Value}
    <% else %>
        {$CurrencyField.SmallFieldHolder}
        {$AmountField.SmallFieldHolder}
    <% end_if %>
</div>
