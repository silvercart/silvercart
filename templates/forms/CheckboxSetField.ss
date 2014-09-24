<ul id="$ID" class="$extraClass">
	<% if $Options.Count %>
    <li class="selected-options-preview <% if $HasSelectedOptions %>hidden<% end_if %>"><%t SilvercartOrderSearchForm.PLEASECHOOSE %>... <span class="caret"></span></li>
		<% loop $Options %>
			<li class="$Class <% if $isChecked %>checked<% end_if %>">
				<input id="$ID" class="checkbox" name="$Name" type="checkbox" value="$Value"<% if $isChecked %> checked="checked"<% end_if %><% if $isDisabled %> disabled="disabled"<% end_if %> />
				<label for="$ID">$Title</label>
			</li> 
		<% end_loop %>
	<% else %>
		<li>No options available</li>
	<% end_if %>
</ul>