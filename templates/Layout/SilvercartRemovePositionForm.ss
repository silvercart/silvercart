<form class="yform" $FormAttributes >
	$CustomHtmlFormMetadata

    $CustomHtmlFormSpecialFields

	<% loop Actions %>
            <button class="btn btn-mini btn-danger" id="{$ID}" name="{$Name}" title="{$Title}" data-placement="top" data-toggle="tooltip" ><i class="icon-trash"></i></button>
	<% end_loop %>
</form>
