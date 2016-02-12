<div id="{$FormName}_{$FieldName}_Box" class="control-group type-text two-fields ltr <% if errorMessage %> error<% end_if %><% if isRequiredField %> requiredField<% end_if %>">
    <label class="control-label" for="{$FieldID}">{$Label}
        <% if isRequiredField %>
        <span class="<% if errorMessage %>text-error<% end_if %>">{$RequiredFieldMarker}</span>
        <% end_if %>
    </label>
    <div class="controls">
        $Parent.CustomHtmlFormFieldByName(PhoneAreaCode, CustomHtmlFormFieldPlainWithoutLabel) <span class="spacer">/</span> {$FieldTag}
         <% if errorMessage %>
           <div class="errorList">
               <% with errorMessage %>
               <strong class="message">
                   {$message}
               </strong>
               <% end_with %>
           </div>
       <% end_if %>
    </div>
</div>       
