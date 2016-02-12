<form class="yform" $FormAttributes >
	$CustomHtmlFormMetadata

	<% loop Actions %>
    <button class="btn" id="{$ID}" name="{$Name}" value="{$description}" type="submit" data-toggle="tooltip" data-placement="top" title="{$description}" data-title="{$description}">
         <i class="icon-plus"></i>
    </button>
	<% end_loop %>

    $CustomHtmlFormSpecialFields
</form>
