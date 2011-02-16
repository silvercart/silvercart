<% if IncludeFormTag %>
<form class="yform" $FormAttributes >
<% end_if %>
      $CustomHtmlFormMetadata
      <div class="subcolumns">
        <div class="c33l">
            $CustomHtmlFormFieldByName(articleAmount,ArticlePreviewFormField)
        </div>
        <div class="c66r">
            <% control Actions %>
            <div class="type-button">
                $Field
            </div>
            <% end_control %>
        </div>
    </div>

<% if IncludeFormTag %>
</form>
<% end_if %>
