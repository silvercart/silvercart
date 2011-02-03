<form name="QuickLogin" class="yform" $FormAttributes>
    $CustomHtmlFormMetadata

  <div class="subcolumns">
     <div class="c50l">
          <div class="subcl">
              <div class="Head_line">
                  <% _t('Page.EMAIL_ADDRESS') %>
              </div>
                  $CustomHtmlFormFieldByName(emailaddress,QuickLoginFormFields)
          </div>
     </div>

    <div class="c40l">
         <div class="subcl">
             <div class="Head_line">
                  <% _t('Page.PASSWORD','password') %>:
             </div>
                  $CustomHtmlFormFieldByName(password,QuickLoginFormFields)
         </div>
    </div>

    <div class="c10r">
         <div class="subcr">
                <% control Actions %>
                    <div id="SendAction">
                        $Field
                    </div>
                <% end_control %>
         </div>
    </div>
   $CustomHtmlFormErrorMessages
  </div>
</form>