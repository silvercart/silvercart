<form class="yform" $FormAttributes >
	$CustomHtmlFormMetadata

    $CustomHtmlFormSpecialFields

	<% loop Actions %>
		<div class="type-button">
			$Field
		</div>
	<% end_loop %>
</form>
