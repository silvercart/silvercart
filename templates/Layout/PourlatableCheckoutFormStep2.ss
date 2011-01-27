<form class="yform" $FormAttributes >

      $CustomHtmlFormMetadata
      $CustomHtmlFormErrorMessages
      <fieldset>
          <legend>Bezahlart</legend>
          <div class="subcolumns">
              $CustomHtmlFormFieldByName(PaymentMethod,CustomHtmlFormFieldSelect)
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