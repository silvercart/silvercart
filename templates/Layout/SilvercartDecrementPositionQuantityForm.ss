<form class="yform" $FormAttributes >
	$CustomHtmlFormMetadata
	<% loop Actions %>
		<div class="type-button">
			$Field
		</div>
	<% end_loop %>

    $CustomHtmlFormSpecialFields
</form>
