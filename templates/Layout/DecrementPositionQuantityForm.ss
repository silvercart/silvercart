<% if IncludeFormTag %>
<form class="yform" $FormAttributes >
<% end_if %>
      $CustomHtmlFormMetadata
            <% control Actions %>
            <div class="type-button">
                $Field
            </div>
            <% end_control %>

<% if IncludeFormTag %>
</form>
<% end_if %>
