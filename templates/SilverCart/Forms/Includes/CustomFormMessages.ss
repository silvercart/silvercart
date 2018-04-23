<% if $Message %>
	<p id="{$FormName}_message" class="alert alert-{$MessageType} message {$MessageType}">{$Message}</p>
<% else %>
	<p id="{$FormName}_message" class="alert alert-error message" style="display: none"></p>
<% end_if %>