<% if $Tag == 'fieldset' && $Legend %>
    <legend>{$Legend}</legend>
<% end_if %>
<% loop $FieldList %>
    {$addExtraClass('w-auto d-inline-block').Field}
<% end_loop %>
