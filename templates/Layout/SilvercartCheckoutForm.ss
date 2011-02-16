<% if IncludeFormTag %>
<form class="yform" $FormAttributes >
      <% end_if %>

      $CustomHtmlFormMetadata

      <fieldset>
        <legend><% _t('PaymentMethod.SHIPPINGMETHOD') %></legend>
        
        <div class="subcolumns" >
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(PaymentMethod,CustomHtmlFormFieldSelect)
                </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    <div id="ShippingMethod">
                    $CustomHtmlFormFieldByName(ShippingMethod,CustomHtmlFormFieldSelect)
                    </div>
                  </div>
            </div>
        </div>
      
      </fieldset>

      <fieldset>
          <legend><% _t('Page.REMARKS') %></legend>
          $CustomHtmlFormFieldByName(Note)
      </fieldset>
    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
            $Field
            <% end_control %>
        </div>
    </div>

    $dataFieldByName(SecurityID)

<% if IncludeFormTag %>
</form>
<% end_if %>