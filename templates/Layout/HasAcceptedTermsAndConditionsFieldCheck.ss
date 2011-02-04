<div id="{$FormName}_{$FieldName}_Box" class="type-check<% if errorMessage %> error<% end_if %>">
    <% if errorMessage %>
        <div class="errorList">
            <% control errorMessage %>
            <strong class="message">
                {$message}
            </strong>
            <% end_control %>
        </div>
    <% end_if %>

    <label for="{$FormName}_{$FieldName}"><% _t('Page.I_ACCEPT','I accept the') %> <a href="/<% _t('MetaNavigationHolder.URL_SEGMENT') %>/<% _t('Page.URL_SEGMENT_TERMS') %>"><% _t('Page.TITLE_TERMS') %>:</a> </label>
    $FieldTag
</div>
