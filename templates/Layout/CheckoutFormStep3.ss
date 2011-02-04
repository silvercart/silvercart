<form class="yform" $FormAttributes >

      $CustomHtmlFormMetadata
      $CustomHtmlFormErrorMessages
      <fieldset>
          <legend><% _t('PaymentMethod.SHIPPINGMETHOD') %></legend>
          <div class="subcolumns">
              $CustomHtmlFormFieldByName(ShippingMethod,CustomHtmlFormFieldSelect)
          </div>
    </fieldset>

    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
            $Field
            <% end_control %>
        </div>
    </div>

    $dataFieldByName(SecurityID)
</form>