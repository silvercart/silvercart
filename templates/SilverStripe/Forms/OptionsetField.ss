<div {$AttributesHTML}>
<% loop $Options %>
    <label class="radio {$Class}" for="{$ID}">
      <input id="{$ID}" name="{$Name}" value="{$Value}" type="radio"<% if $isChecked %> checked<% end_if %><% if $isDisabled %> disabled<% end_if %><% if $Up.Required %> required<% end_if %> />
      {$Title}
    </label>
<% end_loop %>
</div>